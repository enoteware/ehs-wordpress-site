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
  { id: 335, uuid: "2be98d79-9940-4cbb-b3b5-2c4e72b8c629", title: "SSHO Services Page" },
  { id: 336, uuid: "9034f254-4680-4eb6-89e7-8272401d15c5", title: "Lead Compliance Plan" },
  { id: 337, uuid: "bae150d1-0477-4099-91af-02043570bc79", title: "Caltrans Construction Safety" },
  { id: 338, uuid: "60fc3faa-69d6-467c-a5a0-68083ef27378", title: "Federal Contracting" },
  { id: 339, uuid: "ec686aac-7374-41a9-8c9c-8c8bbf9559a8", title: "Update Construction Safety" },
  { id: 341, uuid: "5e5d5bb9-9445-479f-b71b-638fcab66021", title: "Backend Maintenance" },
  { id: 342, uuid: "f5ed5417-16de-4d15-8cb7-e514922764f2", title: "Micro-Site" },
];

console.log("🔍 Verifying checklists on all tasks...\n");

for (const task of tasks) {
  const details = await getTask(task.uuid);
  const checklistComment = details.comments?.find((c) =>
    c.comment_text.includes("Implementation Checklist")
  );
  console.log(
    `Task ${task.id} (${task.title}): ${checklistComment ? "✅ HAS checklist (ID: " + checklistComment.id + ")" : "❌ MISSING checklist"}`
  );
}

console.log("\n✅ Verification complete!");
