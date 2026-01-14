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

const taskUuid = "5e5d5bb9-9445-479f-b71b-638fcab66021"; // Task 341

console.log("🔍 Checking checklist structure for task 341...\n");

const checklists = await agentFetch(`/tasks/${taskUuid}/checklists`);

console.log("📋 Checklists found:");
console.log(JSON.stringify(checklists, null, 2));

if (checklists.checklists && checklists.checklists.length > 0) {
  const checklist = checklists.checklists[0];
  console.log("\n📝 Checklist details:");
  console.log("ID:", checklist.id);
  console.log("Title:", checklist.title);
  console.log("Items count:", checklist.items?.length || 0);
  
  if (checklist.items && checklist.items.length > 0) {
    console.log("\n✅ Items structure:");
    checklist.items.forEach((item, index) => {
      console.log(`\nItem ${index + 1}:`);
      console.log(JSON.stringify(item, null, 2));
    });
  } else {
    console.log("\n⚠️  No items found in checklist");
  }
} else {
  console.log("\n⚠️  No checklists found");
}
