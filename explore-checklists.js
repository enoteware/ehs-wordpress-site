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

console.log("🔍 Exploring checklist API functionality...\n");

const taskUuid = "2be98d79-9940-4cbb-b3b5-2c4e72b8c629";

// Get existing checklists
console.log("1. Getting existing checklists:");
const getChecklists = await agentFetch(`/tasks/${taskUuid}/checklists`);
console.log(JSON.stringify(getChecklists, null, 2));

// Try to create a checklist
console.log("\n2. Testing checklist creation:");
const createChecklist = await agentFetch(`/tasks/${taskUuid}/checklists`, {
  method: "POST",
  idempotent: true,
  body: {
    title: "Implementation Checklist",
    items: [
      { text: "Review content source", completed: false },
      { text: "Create page in WordPress", completed: false },
    ],
  },
});

if (!createChecklist.error) {
  console.log("✅ Checklist created successfully!");
  console.log(JSON.stringify(createChecklist, null, 2));
} else {
  console.log("❌ Error creating checklist:");
  console.log("Status:", createChecklist.status);
  console.log("Response:", JSON.stringify(createChecklist.data, null, 2));
  
  // Try different formats
  console.log("\n3. Trying alternative format (just title):");
  const createSimple = await agentFetch(`/tasks/${taskUuid}/checklists`, {
    method: "POST",
    idempotent: true,
    body: {
      title: "Implementation Checklist",
    },
  });
  
  if (!createSimple.error) {
    console.log("✅ Simple checklist created!");
    console.log(JSON.stringify(createSimple, null, 2));
  } else {
    console.log("❌ Still failed:", createSimple.status, JSON.stringify(createSimple.data, null, 2));
  }
}

// Get checklists again to see if anything was created
console.log("\n4. Getting checklists again:");
const getChecklistsAgain = await agentFetch(`/tasks/${taskUuid}/checklists`);
console.log(JSON.stringify(getChecklistsAgain, null, 2));

console.log("\n✅ Checklist exploration complete!");
