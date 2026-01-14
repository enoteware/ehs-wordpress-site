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
      throw new Error(`HTTP ${res.status}: ${JSON.stringify(data)}`);
    }
    return data;
  } catch (error) {
    return { error: true, message: error.message };
  }
}

const taskUuid = "f5ed5417-16de-4d15-8cb7-e514922764f2"; // Task 342

console.log("🔍 Checking checklist status for Task 342: Build constructionsafety.consulting Micro-Site\n");

// Get task details
const task = await agentFetch(`/tasks/${taskUuid}`);
const taskData = task.task || task;

console.log(`📌 Task: ${taskData.title}`);
console.log(`   Status: ${taskData.status}`);
console.log(`   ID: ${taskData.id}\n`);

// Get checklists
const checklistsResponse = await agentFetch(`/tasks/${taskUuid}/checklists`);
const checklists = checklistsResponse.checklists || [];

console.log(`📋 Found ${checklists.length} checklist items\n`);

if (checklists.length === 0) {
  console.log("⚠️  No checklist items found for this task.");
} else {
  // Check for completion status
  let completed = 0;
  let pending = 0;

  console.log("=".repeat(80));
  console.log("CHECKLIST STATUS");
  console.log("=".repeat(80));

  checklists.forEach((item, index) => {
    // Check if item has a completed field or status
    const isCompleted = item.completed === true || 
                       item.completed === 1 || 
                       item.status === 'completed' ||
                       (item.title && item.title.startsWith('✅')) ||
                       (item.title && item.title.startsWith('[x]'));

    if (isCompleted) {
      completed++;
      console.log(`✅ [${index + 1}] ${item.title}`);
    } else {
      pending++;
      console.log(`⏳ [${index + 1}] ${item.title}`);
    }
  });

  console.log("\n" + "=".repeat(80));
  console.log(`📊 SUMMARY:`);
  console.log(`   Total items: ${checklists.length}`);
  console.log(`   ✅ Completed: ${completed}`);
  console.log(`   ⏳ Pending: ${pending}`);
  console.log(`   📈 Progress: ${checklists.length > 0 ? Math.round((completed / checklists.length) * 100) : 0}%`);
  console.log("=".repeat(80));

  // Show first few items in detail
  console.log("\n📝 First 10 checklist items (detailed):");
  checklists.slice(0, 10).forEach((item, index) => {
    console.log(`\n${index + 1}. ${item.title}`);
    console.log(`   ID: ${item.id}`);
    console.log(`   Position: ${item.position}`);
    console.log(`   Created: ${item.created_at}`);
    console.log(`   Updated: ${item.updated_at}`);
    console.log(`   Completed: ${item.completed !== undefined ? item.completed : 'N/A'}`);
    console.log(`   Full object keys: ${Object.keys(item).join(', ')}`);
  });

  // Show full structure of one item
  if (checklists.length > 0) {
    console.log("\n📦 Full structure of first checklist item:");
    console.log(JSON.stringify(checklists[0], null, 2));
  }
}

console.log("\n✅ Checklist check complete!");
