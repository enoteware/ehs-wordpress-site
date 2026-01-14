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

async function getTask(uuid) {
  const res = await fetch(`${BASE_URL}/tasks/${uuid}`, {
    headers: {
      ...HEADERS,
      "x-request-id": crypto.randomUUID(),
    },
  });
  const data = await res.json();
  return data.task || data;
}

const tasks = [
  { id: 335, uuid: "2be98d79-9940-4cbb-b3b5-2c4e72b8c629" },
  { id: 341, uuid: "5e5d5bb9-9445-479f-b71b-638fcab66021" },
  { id: 342, uuid: "f5ed5417-16de-4d15-8cb7-e514922764f2" },
];

console.log("🔍 Verifying status comments on tasks 335, 341, and 342...\n");

for (const task of tasks) {
  const details = await getTask(task.uuid);
  const statusComment = details.comments?.find((c) =>
    c.comment_text.includes("WHERE WE ARE")
  );
  console.log(
    `Task ${task.id}: ${statusComment ? "✅ HAS status comment (ID: " + statusComment.id + ")" : "❌ MISSING status comment"}`
  );
  if (statusComment) {
    console.log(`   Posted: ${statusComment.created_at}`);
  }
}

console.log("\n✅ Verification complete!");
