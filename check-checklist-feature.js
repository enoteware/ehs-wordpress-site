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
    throw new Error(`HTTP ${res.status}: ${JSON.stringify(data)}`);
  }
  return data;
}

console.log("🔍 Checking task structure for checklist feature...\n");

// Get a task and inspect its structure
const taskUuid = "2be98d79-9940-4cbb-b3b5-2c4e72b8c629"; // Task 335
const task = await agentFetch(`/tasks/${taskUuid}`);

const taskData = task.task || task;

console.log("📋 Task Structure Keys:");
console.log(Object.keys(taskData).join(", "));

console.log("\n🔍 Looking for checklist-related fields:");
const checklistFields = Object.keys(taskData).filter(key => 
  key.toLowerCase().includes('checklist') || 
  key.toLowerCase().includes('check') ||
  key.toLowerCase().includes('todo') ||
  key.toLowerCase().includes('item')
);
console.log("Found:", checklistFields.length > 0 ? checklistFields.join(", ") : "None");

console.log("\n📊 Full task structure (relevant fields):");
console.log(JSON.stringify({
  id: taskData.id,
  uuid: taskData.uuid,
  title: taskData.title,
  status: taskData.status,
  metadata_json: taskData.metadata_json,
  // Check for any nested structures
  has_comments: !!taskData.comments,
  comment_count: taskData.comments?.length || 0,
  // Look for any other potential checklist fields
  all_keys: Object.keys(taskData),
}, null, 2));

// Check metadata_json for checklist data
if (taskData.metadata_json) {
  console.log("\n📦 metadata_json content:");
  console.log(JSON.stringify(taskData.metadata_json, null, 2));
}

// Try to see if there's a checklist endpoint
console.log("\n🔍 Testing for checklist endpoints...");
try {
  const checklistTest = await agentFetch(`/tasks/${taskUuid}/checklist`);
  console.log("✅ Found /tasks/{uuid}/checklist endpoint!");
  console.log(JSON.stringify(checklistTest, null, 2));
} catch (error) {
  console.log("❌ No /tasks/{uuid}/checklist endpoint found");
}

try {
  const checklistItemsTest = await agentFetch(`/tasks/${taskUuid}/checklist-items`);
  console.log("✅ Found /tasks/{uuid}/checklist-items endpoint!");
  console.log(JSON.stringify(checklistItemsTest, null, 2));
} catch (error) {
  console.log("❌ No /tasks/{uuid}/checklist-items endpoint found");
}

console.log("\n✅ Structure inspection complete!");
