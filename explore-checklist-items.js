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

console.log("🔍 Exploring checklist items API...\n");

const taskUuid = "2be98d79-9940-4cbb-b3b5-2c4e72b8c629";
const checklistId = 3; // From previous test

// Get the checklist to see its structure
console.log("1. Getting checklist details:");
const checklist = await agentFetch(`/tasks/${taskUuid}/checklists/${checklistId}`);
console.log(JSON.stringify(checklist, null, 2));

// Try to add items via PATCH
console.log("\n2. Testing adding items via PATCH:");
const patchChecklist = await agentFetch(`/tasks/${taskUuid}/checklists/${checklistId}`, {
  method: "PATCH",
  idempotent: true,
  body: {
    items: [
      { text: "Review content source", completed: false },
      { text: "Create page in WordPress", completed: false },
    ],
  },
});

if (!patchChecklist.error) {
  console.log("✅ Items added via PATCH!");
  console.log(JSON.stringify(patchChecklist, null, 2));
} else {
  console.log("❌ PATCH failed:", patchChecklist.status, JSON.stringify(patchChecklist.data, null, 2));
}

// Try items endpoint
console.log("\n3. Testing checklist items endpoint:");
const itemsResult = await agentFetch(`/tasks/${taskUuid}/checklists/${checklistId}/items`, {
  method: "POST",
  idempotent: true,
  body: {
    text: "Review content source",
    completed: false,
  },
});

if (!itemsResult.error) {
  console.log("✅ Item added via POST to /items!");
  console.log(JSON.stringify(itemsResult, null, 2));
} else {
  console.log("❌ POST to /items failed:", itemsResult.status, JSON.stringify(itemsResult.data, null, 2));
}

// Try creating checklist with items in one go
console.log("\n4. Testing creating checklist with items:");
const createWithItems = await agentFetch(`/tasks/${taskUuid}/checklists`, {
  method: "POST",
  idempotent: true,
  body: {
    title: "Test Checklist with Items",
    items: [
      { text: "Item 1", completed: false },
      { text: "Item 2", completed: false },
    ],
  },
});

if (!createWithItems.error) {
  console.log("✅ Checklist with items created!");
  console.log(JSON.stringify(createWithItems, null, 2));
} else {
  console.log("❌ Failed:", createWithItems.status, JSON.stringify(createWithItems.data, null, 2));
}

// Get all checklists to see final state
console.log("\n5. Final checklist state:");
const allChecklists = await agentFetch(`/tasks/${taskUuid}/checklists`);
console.log(JSON.stringify(allChecklists, null, 2));

console.log("\n✅ Checklist items exploration complete!");
