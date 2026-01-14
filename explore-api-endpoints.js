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

console.log("🔍 Exploring API for checklist endpoints...\n");

const taskUuid = "2be98d79-9940-4cbb-b3b5-2c4e72b8c629";

// Try various potential checklist endpoints
const endpointsToTry = [
  `/tasks/${taskUuid}/checklist`,
  `/tasks/${taskUuid}/checklists`,
  `/tasks/${taskUuid}/checklist-items`,
  `/tasks/${taskUuid}/items`,
  `/checklist/tasks/${taskUuid}`,
  `/checklists/tasks/${taskUuid}`,
];

console.log("Testing potential checklist endpoints:\n");
for (const endpoint of endpointsToTry) {
  const result = await agentFetch(endpoint);
  if (!result.error) {
    console.log(`✅ ${endpoint} - EXISTS!`);
    console.log(JSON.stringify(result, null, 2));
  } else {
    console.log(`❌ ${endpoint} - ${result.status || result.message}`);
  }
}

// Check if there's a general API discovery endpoint
console.log("\n🔍 Checking for API discovery/documentation...");
const discoveryResult = await agentFetch("/");
if (!discoveryResult.error) {
  console.log("✅ Root endpoint exists:");
  console.log(JSON.stringify(discoveryResult, null, 2));
} else {
  console.log("❌ No root endpoint");
}

// Check projects summary for available tools
console.log("\n🔍 Checking projects summary for available endpoints...");
const summaryResult = await agentFetch("/projects/summary");
if (!summaryResult.error) {
  console.log("Projects summary structure:");
  console.log("Keys:", Object.keys(summaryResult).join(", "));
}

// Check if metadata_json can store checklist data
console.log("\n🔍 Testing if we can store checklist in metadata_json...");
const testTask = await agentFetch(`/tasks/${taskUuid}`);
const taskData = testTask.task || testTask;

if (taskData.metadata_json === null) {
  console.log("metadata_json is null - could potentially store checklist data here");
  console.log("Would need to test PATCH with metadata_json containing checklist structure");
}

console.log("\n✅ API exploration complete!");
