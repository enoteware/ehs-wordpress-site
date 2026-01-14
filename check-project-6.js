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

// Health check first
console.log("🔍 Checking API health...");
try {
  const health = await agentFetch("/health");
  console.log("✅ Health check:", JSON.stringify(health, null, 2));
} catch (error) {
  console.error("❌ Health check failed:", error.message);
  process.exit(1);
}

// Try projects summary first
console.log("\n📋 Fetching projects summary...");
try {
  const summary = await agentFetch("/projects/summary");
  console.log("\n📊 Projects Summary:");
  console.log(JSON.stringify(summary, null, 2));
  
  // Find project 6
  if (summary.data) {
    const project6 = summary.data.find(p => p.id === 6 || p.project_id === 6);
    if (project6) {
      console.log("\n✅ Found project 6:", JSON.stringify(project6, null, 2));
    } else {
      console.log("\n⚠️  Project 6 not found in summary");
    }
  }
} catch (error) {
  console.error("❌ Failed to fetch projects summary:", error.message);
}

// Get project 6 tasks
console.log("\n📋 Fetching tasks for project 6 (EHS)...");
try {
  const tasks = await agentFetch("/projects/6/tasks");
  
  if (tasks.tasks && tasks.tasks.length > 0) {
    console.log(`\n✅ Found ${tasks.tasks.length} task(s) for project 6`);
    console.log("\n📊 Task Summary:");
    tasks.tasks.forEach((task, index) => {
      console.log(`\n${index + 1}. [${task.status.toUpperCase()}] ${task.title}`);
      if (task.description) {
        const desc = task.description.substring(0, 100);
        console.log(`   ${desc}${task.description.length > 100 ? '...' : ''}`);
      }
      if (task.awaiting_response_from) {
        console.log(`   ⏳ Awaiting response from: ${task.awaiting_response_from}`);
      }
    });
    
    // Show status breakdown
    const statusCounts = {};
    tasks.tasks.forEach(task => {
      statusCounts[task.status] = (statusCounts[task.status] || 0) + 1;
    });
    console.log("\n📈 Status Breakdown:");
    Object.entries(statusCounts).forEach(([status, count]) => {
      console.log(`   ${status}: ${count}`);
    });
  } else {
    console.log("\n⚠️  No tasks found for project 6");
  }
  
  // Full JSON output
  console.log("\n📄 Full Task Data:");
  console.log(JSON.stringify(tasks, null, 2));
} catch (error) {
  console.error("❌ Failed to fetch tasks:", error.message);
}
