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

// Get all tasks and show their latest comments
console.log("📋 Fetching all tasks and their comments...\n");
const tasksResponse = await agentFetch("/projects/6/tasks");
const tasks = tasksResponse.tasks || [];

for (const task of tasks) {
  console.log(`\n${"=".repeat(80)}`);
  console.log(`📌 Task ${task.id}: ${task.title}`);
  console.log(`   Status: ${task.status}`);
  console.log(`   UUID: ${task.uuid}`);
  
  try {
    const details = await agentFetch(`/tasks/${task.uuid}`);
    const taskData = details.task || details;
    
    if (taskData.comments && taskData.comments.length > 0) {
      // Get the most recent comment
      const latestComment = taskData.comments[taskData.comments.length - 1];
      console.log(`\n   💬 Latest Comment (ID: ${latestComment.id}):`);
      console.log(`   ${latestComment.comment_text.split('\n').join('\n   ')}`);
      console.log(`   Posted: ${latestComment.created_at} by ${latestComment.user_name || 'Unknown'}`);
    } else {
      console.log(`   ⚠️  No comments found`);
    }
  } catch (error) {
    console.error(`   ❌ Error fetching task details:`, error.message);
  }
}

console.log(`\n${"=".repeat(80)}`);
console.log("✅ Comment verification complete!");
