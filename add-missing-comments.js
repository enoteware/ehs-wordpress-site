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

// Status comments for the missing tasks
const statusComments = {
  335: `📍 **WHERE WE ARE:**

**Current Status:** In Progress - Draft page created
- ✅ WordPress post created (Post ID: 3256)
- ✅ Draft page exists at: https://ehsanalytical.com/ssho-services-california/
- ⏳ Awaiting client review/approval
- 📝 Content source ready: \`1_Web_Designer_Instructions_Part1_SSHO.json\`
- 🎯 Next Steps: Client review → Finalize content → Publish

**Priority:** HIGH (Week 1 priority page)`,

  341: `📍 **WHERE WE ARE:**

**Current Status:** Pending - Not yet started
- ⏳ Maintenance task scheduled but not yet executed
- 📋 Task includes: WordPress core updates, plugin updates, theme updates, cache clearing
- 🎯 Next Steps: Schedule maintenance window → Run updates → Verify site functionality

**Frequency:** Monthly or as needed
**Priority:** MEDIUM (Ongoing maintenance)`,

  342: `📍 **WHERE WE ARE:**

**Current Status:** Pending - Ready to begin
- ✅ All content files ready in: \`02-micro-site-vercel/source-content/constructionsafety_consultingwebsite/\`
- ✅ Static HTML files ready in: \`02-micro-site-vercel/deployed-site/constructionsafety-microsite/\`
- ⏳ Domain needs to be purchased: constructionsafety.consulting
- ⏳ Site needs to be deployed to Vercel
- ⏳ Domain needs to be configured on Vercel
- 🎯 Next Steps: Purchase domain → Deploy to Vercel → Configure domain → Set up 301 redirect

**Timeline:** 1-2 weeks
**Priority:** MEDIUM (Separate project from main site)`,
};

// Get all tasks for project 6
console.log("📋 Fetching tasks 335, 341, and 342...");
const tasksResponse = await agentFetch("/projects/6/tasks");
const tasks = tasksResponse.tasks || [];
const targetTasks = tasks.filter(t => [335, 341, 342].includes(t.id));

console.log(`\n✅ Found ${targetTasks.length} target tasks\n`);

// Add status comments to each task
for (const task of targetTasks) {
  const comment = statusComments[task.id];
  if (!comment) {
    console.log(`⚠️  No status comment defined for task ${task.id}: ${task.title}`);
    continue;
  }

  console.log(`\n💬 Adding status comment to task ${task.id}: ${task.title}`);

  try {
    const result = await agentFetch(`/tasks/${task.uuid}/comments`, {
      method: "POST",
      idempotent: true,
      body: {
        comment_text: comment,
      },
    });

    if (result.success || result.comment) {
      console.log(`   ✅ Successfully added status comment to task ${task.id}`);
      if (result.comment) {
        console.log(`   Comment ID: ${result.comment.id}`);
      }
    } else {
      console.log(`   ⚠️  Comment response:`, JSON.stringify(result, null, 2));
    }
  } catch (error) {
    console.error(`   ❌ Error adding comment to task ${task.id}:`, error.message);
  }
}

console.log("\n" + "=".repeat(80));
console.log("✅ Status comments added to missing tasks!");
console.log("=".repeat(80));
