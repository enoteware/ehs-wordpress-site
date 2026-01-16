import crypto from "node:crypto";
import { readFileSync, writeFileSync, readFileSync as readFile } from "node:fs";
import { readFile as readFileAsync } from "node:fs/promises";
import { basename } from "node:path";
import { execSync } from "node:child_process";

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
    return {
      AGENT_API_KEY: '8e938f1e329a3f27702476ce48cbfb5e04eb18c57286d78760806c8a5f14b925',
      AGENT_USER_ID: '63edd5da-d2bf-47f2-934d-8416104ad129',
      AGENT_BASE_URL: 'https://app.noteware.dev/api/agent',
    };
  }
}

const env = loadEnv();
const BASE_URL = env.AGENT_BASE_URL || "https://app.noteware.dev/api/agent";
const HEADERS = {
  "x-agent-key": env.AGENT_API_KEY,
  "x-user-id": env.AGENT_USER_ID,
};

async function agentFetch(path, { method = 'GET', body, idempotent = false, timeoutMs = 30000 } = {}) {
  const controller = new AbortController();
  const timeout = setTimeout(() => controller.abort(), timeoutMs);
  
  const headers = {
    ...HEADERS,
    'x-request-id': crypto.randomUUID(),
  };
  
  if (idempotent) {
    headers['x-idempotency-key'] = crypto.randomUUID();
  }
  
  try {
    const res = await fetch(`${BASE_URL}${path}`, {
      method,
      headers: {
        ...headers,
        ...(body ? { 'content-type': 'application/json' } : {}),
      },
      body: body ? JSON.stringify(body) : undefined,
      signal: controller.signal,
    });
    
    const data = await res.json().catch(() => ({}));
    if (!res.ok) throw new Error(`HTTP ${res.status}: ${JSON.stringify(data)}`);
    return data;
  } finally {
    clearTimeout(timeout);
  }
}

const DEV_DOMAIN = 'https://dev.ehsanalytical.com';

// Task ID -> URL slug mapping
const taskUrls = {
  335: '/ssho-services-california/', // SSHO Services Page
  336: '/lead-compliance-plan-services/', // Lead Compliance Plan Services Page
  337: '/caltrans-construction-safety-services/', // Caltrans Construction Safety Services Page
  338: '/federal-contracting-services/', // Federal Contracting Services Page
  339: '/construction-safety-consulting/', // Update Construction Safety Consulting Page
};

// Check if Playwright is available
function checkPlaywright() {
  try {
    execSync('npm list playwright 2>/dev/null', { stdio: 'ignore' });
    return true;
  } catch {
    return false;
  }
}

// Install Playwright if needed
function ensurePlaywright() {
  if (!checkPlaywright()) {
    console.log('📦 Installing Playwright...');
    execSync('npm install playwright', { stdio: 'inherit' });
    console.log('📦 Installing Chromium browser...');
    execSync('npx playwright install chromium', { stdio: 'inherit' });
  }
}

// Take screenshot using Playwright
async function takeScreenshot(url, outputPath) {
  const { chromium } = await import('playwright');
  
  console.log(`   🚀 Launching browser...`);
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext({
    viewport: { width: 1920, height: 1080 },
    ignoreHTTPSErrors: true
  });
  
  const page = await context.newPage();
  
  try {
    console.log(`   📸 Navigating to ${url}...`);
    await page.goto(url, { waitUntil: 'networkidle', timeout: 30000 });
    await page.waitForTimeout(2000); // Wait for animations/loads
    
    console.log(`   📸 Capturing full page screenshot...`);
    await page.screenshot({
      path: outputPath,
      fullPage: true
    });
    
    await browser.close();
    return true;
  } catch (error) {
    await browser.close();
    throw error;
  }
}

// Upload screenshot as attachment using the attachments endpoint
async function uploadScreenshotToTask(taskUuid, screenshotPath, url, tag = 'reference') {
  try {
    // Read file as buffer
    const fileBuffer = await readFileAsync(screenshotPath);
    const fileName = basename(screenshotPath);
    
    // Use native FormData (Node.js 18+) with Blob
    const formData = new FormData();
    formData.append('file', new Blob([fileBuffer], { type: 'image/png' }), fileName);
    formData.append('tag', tag);
    
    // Only set required headers - let fetch handle Content-Type automatically
    const response = await fetch(`${BASE_URL}/tasks/${taskUuid}/attachments`, {
      method: 'POST',
      headers: {
        'x-agent-key': HEADERS['x-agent-key'],
        'x-user-id': HEADERS['x-user-id'],
        'x-request-id': crypto.randomUUID(),
        'x-idempotency-key': crypto.randomUUID(),
      },
      body: formData, // fetch will automatically set Content-Type with boundary
    });
    
    const data = await response.json().catch(() => ({}));
    if (!response.ok) {
      throw new Error(`HTTP ${response.status}: ${JSON.stringify(data)}`);
    }
    
    // Add a comment referencing the uploaded screenshot
    await agentFetch(`/tasks/${taskUuid}/comments`, {
      method: 'POST',
      idempotent: true,
      body: {
        comment_text: `📸 **Page Screenshot Uploaded**\n\nScreenshot captured from: ${url}\n\nScreenshot has been attached to this task.`,
      },
    });
    
    return data;
  } catch (error) {
    // If upload fails, add a comment with the error
    await agentFetch(`/tasks/${taskUuid}/comments`, {
      method: 'POST',
      idempotent: true,
      body: {
        comment_text: `⚠️ **Screenshot Upload Error**\n\nFailed to upload screenshot: ${error.message}\n\nPage URL: ${url}\n\nScreenshot saved locally at: ${screenshotPath}`,
      },
    });
    throw error;
  }
}

(async () => {
  try {
    const PROJECT_ID = 6;
    const SCREENSHOT_DIR = './screenshots';
    
    // Create screenshot directory
    execSync(`mkdir -p "${SCREENSHOT_DIR}"`, { stdio: 'ignore' });
    
    console.log('📸 Screenshot Capture Tool');
    console.log('='.repeat(80));
    console.log('');
    
    // Ensure Playwright is installed
    ensurePlaywright();
    console.log('');
    
    console.log(`Fetching tasks for project ${PROJECT_ID}...\n`);
    const tasksResponse = await agentFetch(`/projects/${PROJECT_ID}/tasks`);
    const tasks = tasksResponse.tasks || tasksResponse.data || [];
    
    const tasksToScreenshot = tasks.filter(t => taskUrls[t.id]);
    
    console.log(`Found ${tasksToScreenshot.length} task(s) to capture screenshots for.\n`);
    console.log('='.repeat(80));
    
    for (const task of tasksToScreenshot) {
      const url = taskUrls[task.id];
      const fullUrl = `${DEV_DOMAIN}${url}`;
      const screenshotPath = `${SCREENSHOT_DIR}/task-${task.id}-${Date.now()}.png`;
      
      console.log(`\n📋 Task #${task.id}: ${task.title}`);
      console.log(`   URL: ${fullUrl}`);
      
      try {
        await takeScreenshot(fullUrl, screenshotPath);
        console.log(`   ✓ Screenshot saved: ${screenshotPath}`);
        
        // Upload screenshot as attachment
        console.log(`   📤 Uploading to task...`);
        try {
          await uploadScreenshotToTask(task.uuid, screenshotPath, fullUrl);
          console.log(`   ✓ Screenshot uploaded to task`);
        } catch (uploadError) {
          console.error(`   ✗ Upload error:`, uploadError.message);
        }
        
      } catch (error) {
        console.error(`   ✗ Error capturing screenshot:`, error.message);
        
        // Add comment about the error
        await agentFetch(`/tasks/${task.uuid}/comments`, {
          method: 'POST',
          idempotent: true,
          body: {
            comment_text: `⚠️ **Screenshot Error**\n\nFailed to capture screenshot: ${error.message}\n\nPage URL: ${fullUrl}\n\nPlease capture screenshot manually.`,
          },
        });
      }
    }
    
    console.log('\n' + '='.repeat(80));
    console.log('\n✓ Screenshot capture complete!');
    console.log(`\nScreenshots saved to: ${SCREENSHOT_DIR}/`);
    console.log('You can manually upload them to tasks if needed.\n');
    
  } catch (error) {
    console.error('Error:', error.message);
    if (error.stack) console.error(error.stack);
    process.exit(1);
  }
})();
