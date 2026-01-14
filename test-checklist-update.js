import crypto from "node:crypto";
import { readFileSync } from "node:fs";

function loadEnv() {
  const envContent = readFileSync(".env", "utf-8");
  const env = {};
  envContent.split("\n").forEach((line) => {
    const trimmed = line.trim();
    if (trimmed && !trimmed.startsWith("#")) {
      const [key, ...valueParts] = trimmed.split("=");
      if (key && valueParts.length > 0) {
        env[key.trim()] = valueParts.join("=").replace(/^["']|["']$/g, "");
      }
    }
  });
  return env;
}

const env = loadEnv();
const BASE_URL = env.AGENT_BASE_URL;
const HEADERS = {
  "x-agent-key": env.AGENT_API_KEY,
  "x-user-id": env.AGENT_USER_ID,
};

async function agentFetch(path, { method = "GET", body, idempotent = false } = {}) {
  const headers = {
    ...HEADERS,
    "x-request-id": crypto.randomUUID(),
  };

  if (idempotent) {
    headers["x-idempotency-key"] = crypto.randomUUID();
  }

  try {
    const res = await fetch(`${BASE_URL}${path}`, {
      method,
      headers: {
        ...headers,
        ...(body ? { "content-type": "application/json" } : {}),
      },
      body: body ? JSON.stringify(body) : undefined,
    });

    const data = await res.json().catch(() => ({}));
    if (!res.ok) {
      return { error: true, status: res.status, data };
    }
    return data;
  } catch (error) {
    return { error: true, message: error.message };
  }
}

const taskUuid = "5e5d5bb9-9445-479f-b71b-638fcab66021";
const checklistId = 5; // First checklist

console.log("🔍 Testing checklist update methods...\n");

// Try PATCH to update checklist
console.log("1. Testing PATCH to update checklist:");
const patchResult = await agentFetch(`/tasks/${taskUuid}/checklists/${checklistId}`, {
  method: "PATCH",
  idempotent: true,
  body: {
    title: "Updated item 1",
    completed: true,
  },
});

if (!patchResult.error) {
  console.log("✅ PATCH successful!");
  console.log(JSON.stringify(patchResult, null, 2));
} else {
  console.log("❌ PATCH failed:", patchResult.status, JSON.stringify(patchResult.data, null, 2));
}

// Try PUT
console.log("\n2. Testing PUT to update checklist:");
const putResult = await agentFetch(`/tasks/${taskUuid}/checklists/${checklistId}`, {
  method: "PUT",
  idempotent: true,
  body: {
    title: "Updated item 1",
    completed: true,
  },
});

if (!putResult.error) {
  console.log("✅ PUT successful!");
  console.log(JSON.stringify(putResult, null, 2));
} else {
  console.log("❌ PUT failed:", putResult.status, JSON.stringify(putResult.data, null, 2));
}

// Get checklists again to see current state
console.log("\n3. Current checklist state:");
const checklists = await agentFetch(`/tasks/${taskUuid}/checklists`);
console.log(JSON.stringify(checklists, null, 2));

console.log("\n✅ Testing complete!");
