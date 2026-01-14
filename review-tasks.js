import crypto from "node:crypto";
import { readFileSync } from "node:fs";

// Load environment variables from .env file
function loadEnv() {
  try {
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
  } catch (error) {
    console.error("Error loading .env file:", error.message);
    process.exit(1);
  }
}

const env = loadEnv();
const BASE_URL = env.AGENT_BASE_URL || "https://app.noteware.dev/api/agent";
const HEADERS = {
  "x-agent-key": env.AGENT_API_KEY,
  "x-user-id": env.AGENT_USER_ID,
};

async function agentFetch(
  path,
  { method = "GET", body, idempotent = false, timeoutMs = 25_000 } = {},
) {
  const controller = new AbortController();
  const timeout = setTimeout(() => controller.abort(), timeoutMs);

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
      signal: controller.signal,
    });

    const data = await res.json().catch(() => ({}));
    if (!res.ok) {
      console.error(`HTTP ${res.status}:`, JSON.stringify(data, null, 2));
      throw new Error(`HTTP ${res.status}: ${JSON.stringify(data)}`);
    }
    return data;
  } finally {
    clearTimeout(timeout);
  }
}

// Get all tasks for project 6
console.log("📋 Fetching all tasks for project 6...");
const tasksResponse = await agentFetch("/projects/6/tasks");
const tasks = tasksResponse.tasks || [];

console.log(`\n✅ Found ${tasks.length} tasks\n`);

// Get detailed info for each task including comments
const detailedTasks = [];
for (const task of tasks) {
  console.log(`📥 Fetching details for: ${task.title}...`);
  try {
    const details = await agentFetch(`/tasks/${task.uuid}`);
    detailedTasks.push(details.task || details);
  } catch (error) {
    console.error(`  ⚠️  Error fetching details: ${error.message}`);
    detailedTasks.push(task); // Fallback to basic task info
  }
}

// Output analysis
console.log("\n" + "=".repeat(80));
console.log("📊 TASK ANALYSIS REPORT");
console.log("=".repeat(80));

detailedTasks.forEach((task, index) => {
  console.log(`\n${index + 1}. [${task.status.toUpperCase()}] ${task.title}`);
  console.log(`   UUID: ${task.uuid}`);
  console.log(`   ID: ${task.id}`);
  console.log(`   Priority: ${task.priority || "not set"}`);
  
  if (task.description) {
    console.log(`   Description: ${task.description.substring(0, 150)}...`);
  }
  
  // Check for destination/URL info
  const desc = (task.description || "").toLowerCase();
  const hasUrl = desc.includes("http") || desc.includes("url") || desc.includes("ehsanalytical.com");
  const hasPostId = desc.includes("post id") || desc.includes("post_id");
  const hasDomain = desc.includes("domain") || desc.includes("constructionsafety");
  
  console.log(`   Destination indicators:`);
  console.log(`     - Has URL/domain info: ${hasUrl || hasDomain ? "✅" : "❌"}`);
  console.log(`     - Has Post ID: ${hasPostId ? "✅" : "❌"}`);
  
  if (task.comments && task.comments.length > 0) {
    console.log(`   Comments: ${task.comments.length}`);
  }
  
  if (task.awaiting_response_from) {
    console.log(`   ⏳ Awaiting: ${task.awaiting_response_from}`);
  }
});

// Check for duplicates
console.log("\n" + "=".repeat(80));
console.log("🔍 DUPLICATE DETECTION");
console.log("=".repeat(80));

const titleGroups = {};
detailedTasks.forEach(task => {
  const key = task.title.toLowerCase().trim();
  if (!titleGroups[key]) {
    titleGroups[key] = [];
  }
  titleGroups[key].push(task);
});

const duplicates = Object.entries(titleGroups).filter(([_, tasks]) => tasks.length > 1);
if (duplicates.length > 0) {
  console.log("\n⚠️  Found potential duplicate tasks:");
  duplicates.forEach(([title, tasks]) => {
    console.log(`\n   "${title}" (${tasks.length} instances):`);
    tasks.forEach(task => {
      console.log(`     - ID ${task.id} (${task.uuid}) - Status: ${task.status}`);
    });
  });
} else {
  console.log("\n✅ No exact title duplicates found");
}

// Check for similar tasks (SSHO, Construction Safety, etc.)
console.log("\n" + "=".repeat(80));
console.log("🔍 SIMILAR TASK ANALYSIS");
console.log("=".repeat(80));

const sshoTasks = detailedTasks.filter(t => 
  t.title.toLowerCase().includes("ssho") || 
  (t.description || "").toLowerCase().includes("ssho")
);
const constructionTasks = detailedTasks.filter(t => 
  t.title.toLowerCase().includes("construction") || 
  (t.description || "").toLowerCase().includes("construction")
);

if (sshoTasks.length > 1) {
  console.log(`\n⚠️  Found ${sshoTasks.length} SSHO-related tasks:`);
  sshoTasks.forEach(task => {
    console.log(`   - ${task.title} (ID: ${task.id}, Status: ${task.status})`);
  });
}

if (constructionTasks.length > 1) {
  console.log(`\n⚠️  Found ${constructionTasks.length} Construction-related tasks:`);
  constructionTasks.forEach(task => {
    console.log(`   - ${task.title} (ID: ${task.id}, Status: ${task.status})`);
  });
}

// Save full details to file for review
import { writeFileSync } from "node:fs";
writeFileSync("project-6-tasks-detailed.json", JSON.stringify(detailedTasks, null, 2));
console.log("\n💾 Full task details saved to: project-6-tasks-detailed.json");
