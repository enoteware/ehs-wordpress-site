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

// Task updates with clear goals, destinations, and content sources
const taskUpdates = {
  // Task 335: SSHO Services Page (Main Site)
  335: {
    title: "Create SSHO Services Page (Main Site - ehsanalytical.com)",
    description: `**PROJECT:** Main Site Updates (ehsanalytical.com)
**SITE:** WordPress - ehsanalytical.com
**TYPE:** New Page Creation

**DESTINATION:**
- **URL:** https://ehsanalytical.com/ssho-services-california/
- **WordPress Post ID:** 3256
- **Status:** Draft created, in progress
- **Review URL:** https://ehsanalytical.com/ssho-services-california/

**GOAL:**
Create a new service page targeting federal contractors needing SSHO (Site Safety and Health Officer) services for military construction projects.

**CONTENT SOURCE:**
- **Primary:** \`project-organization/01-main-site-wordpress/content-source/updated-docs/1_Web_Designer_Instructions_Part1_SSHO.json\`
- **Implementation Guide:** \`project-organization/01-main-site-wordpress/implementation-guides/PAGE_IMPLEMENTATION_SSHO.md\`
- **Original Content:** \`project-organization/01-main-site-wordpress/content-source/original-files/1_Web_Designer_Instructions_Part1_SSHO.docx\`

**TECHNICAL REQUIREMENTS:**
- WordPress + Elementor page builder
- Match existing service page template (two-column layout with sidebar menu)
- Add to main navigation menu (after Construction Safety)
- Update sidebar service menu on all service pages
- Mobile responsive design
- SEO optimized (Yoast SEO)

**TARGET AUDIENCE:**
Federal contractors needing SSHO for military construction projects (USACE, NAVFAC, etc.)

**PRIORITY:** HIGH (Week 1 priority page)`,
  },

  // Task 336: Lead Compliance Plan Services Page (Main Site)
  336: {
    title: "Create Lead Compliance Plan Services Page (Main Site - ehsanalytical.com)",
    description: `**PROJECT:** Main Site Updates (ehsanalytical.com)
**SITE:** WordPress - ehsanalytical.com
**TYPE:** New Page Creation

**DESTINATION:**
- **URL:** https://ehsanalytical.com/lead-compliance-plan-services/
- **WordPress Post ID:** 3257
- **Status:** Draft created, in progress

**GOAL:**
Create a new service page for Lead Compliance Plan services targeting Caltrans bidders needing Lead Compliance Plans for California districts.

**CONTENT SOURCE:**
- **Primary:** \`project-organization/01-main-site-wordpress/content-source/updated-docs/2_Web_Designer_Instructions_Part2_LeadCompliance.json\`
- **Original Content:** \`project-organization/01-main-site-wordpress/content-source/original-files/2_Web_Designer_Instructions_Part2_LeadCompliance.docx\`

**TECHNICAL REQUIREMENTS:**
- WordPress + Elementor page builder
- Match existing service page template
- Add cross-link to/from Caltrans Construction Safety page
- Update navigation and sidebar menus
- Mobile responsive design
- SEO optimized

**TARGET AUDIENCE:**
Caltrans bidders needing Lead Compliance Plans for California districts, DVBE certification benefits

**PRIORITY:** HIGH (Week 2 priority page)`,
  },

  // Task 337: Caltrans Construction Safety Services Page (Main Site)
  337: {
    title: "Create Caltrans Construction Safety Services Page (Main Site - ehsanalytical.com)",
    description: `**PROJECT:** Main Site Updates (ehsanalytical.com)
**SITE:** WordPress - ehsanalytical.com
**TYPE:** New Page Creation

**DESTINATION:**
- **URL:** https://ehsanalytical.com/caltrans-construction-safety-services/
- **WordPress Post ID:** 3258
- **Status:** Draft created, in progress

**GOAL:**
Create a new service page focused on Caltrans-specific safety roles (Safety Rep, SQCM, WAM) and statewide coverage.

**CONTENT SOURCE:**
- **Primary:** \`project-organization/01-main-site-wordpress/content-source/updated-docs/3_Part3_Caltrans_Construction_Safety_NEW.json\`
- **Original Content:** \`project-organization/01-main-site-wordpress/content-source/original-files/3_Part3_Caltrans_Construction_Safety_NEW.txt\`

**TECHNICAL REQUIREMENTS:**
- WordPress + Elementor page builder
- Match existing service page template
- Add cross-link to/from Lead Compliance Plan page
- Update navigation and sidebar menus
- Mobile responsive design
- SEO optimized

**TARGET AUDIENCE:**
Caltrans contractors needing Safety Representatives, SQCM, and WAM services statewide

**PRIORITY:** HIGH (Week 3 priority page)`,
  },

  // Task 338: Federal Contracting Services Page (Main Site)
  338: {
    title: "Create Federal Contracting Services Page (Main Site - ehsanalytical.com)",
    description: `**PROJECT:** Main Site Updates (ehsanalytical.com)
**SITE:** WordPress - ehsanalytical.com
**TYPE:** New Page Creation

**DESTINATION:**
- **URL:** https://ehsanalytical.com/federal-contracting-sdvosb/
- **WordPress Post ID:** 3259
- **Status:** Draft created, in progress

**GOAL:**
Create a new service page targeting USACE, NAVFAC, and federal prime contractors looking for SDVOSB (Service-Disabled Veteran-Owned Small Business) partners.

**CONTENT SOURCE:**
- **Primary:** \`project-organization/01-main-site-wordpress/content-source/updated-docs/4_Web_Designer_Instructions_Part4_Federal_Contracting.json\`
- **Original Content:** \`project-organization/01-main-site-wordpress/content-source/original-files/4_Web_Designer_Instructions_Part4_Federal_Contracting.docx\`

**TECHNICAL REQUIREMENTS:**
- WordPress + Elementor page builder
- Match existing service page template
- Update navigation and sidebar menus
- Mobile responsive design
- SEO optimized

**TARGET AUDIENCE:**
USACE, NAVFAC, and federal prime contractors looking for SDVOSB partners

**PRIORITY:** MEDIUM (Week 3 priority page)`,
  },

  // Task 339: Update Construction Safety Consulting Page (Main Site)
  339: {
    title: "Update Construction Safety Consulting Page (Main Site - ehsanalytical.com)",
    description: `**PROJECT:** Main Site Updates (ehsanalytical.com)
**SITE:** WordPress - ehsanalytical.com
**TYPE:** Update Existing Page

**DESTINATION:**
- **URL:** https://ehsanalytical.com/construction-safety-consulting/ (existing page)
- **WordPress Post ID:** 3260
- **Status:** Preview draft created, in progress

**GOAL:**
Update the existing Construction Safety Consulting page by adding new sections for Markets Served, compliance expertise, and geographic coverage.

**CONTENT SOURCE:**
- **Primary:** \`project-organization/01-main-site-wordpress/content-source/updated-docs/3_Part3_Caltrans_Construction_Safety_NEW.json\`
- **Original Content:** \`project-organization/01-main-site-wordpress/content-source/original-files/3_Part3_Caltrans_Construction_Safety_NEW.txt\`

**TECHNICAL REQUIREMENTS:**
- Update existing WordPress + Elementor page
- Add 4 new sections:
  1. Markets Served
  2. Compliance Expertise
  3. Geographic Coverage
  4. Additional sections as specified in content source
- Maintain existing page structure and design
- Mobile responsive design
- SEO optimized

**TARGET AUDIENCE:**
Existing audience of Construction Safety Consulting page

**PRIORITY:** MEDIUM (Week 3 update)`,
  },

  // Task 341: General Backend Updates & Maintenance (Main Site)
  341: {
    title: "General Backend Updates & Maintenance (Main Site - ehsanalytical.com)",
    description: `**PROJECT:** Main Site Updates (ehsanalytical.com)
**SITE:** WordPress - ehsanalytical.com
**TYPE:** Maintenance Task

**DESTINATION:**
- **Site:** https://ehsanalytical.com (entire WordPress installation)
- **WordPress Path:** \`/home/a96c427e/832f87585d.nxcli.net/html\`
- **SSH:** 832f87585d.nxcli.net
- **Theme:** Hello Elementor (active)

**GOAL:**
Perform routine WordPress backend maintenance to keep the site secure, optimized, and up-to-date.

**MAINTENANCE TASKS:**
1. **WordPress Core Updates**
   - Update to latest stable version
   - Verify compatibility with theme and plugins

2. **Plugin Updates**
   - Elementor
   - Elementor Pro
   - ACF Pro (Advanced Custom Fields)
   - Yoast SEO
   - Other active plugins

3. **Theme Updates**
   - Hello Elementor theme updates

4. **Plugin-Specific Maintenance:**
   - Run Elementor database updater: \`wp elementor update db\`
   - Flush Elementor CSS cache: \`wp elementor flush_css\`
   - Rebuild Yoast SEO index: \`wp yoast index\`

5. **Cache Management:**
   - Clear WordPress cache
   - Clear any CDN cache if applicable

6. **Verification:**
   - Verify all updates completed successfully
   - Test site functionality
   - Check for any errors or warnings

**FREQUENCY:**
This task should be run periodically (monthly or as needed) to keep the site secure and optimized.

**TECHNICAL REQUIREMENTS:**
- SSH access to Nexcess server
- WordPress CLI access
- Backup before updates (recommended)

**PRIORITY:** MEDIUM (Ongoing maintenance)`,
  },

  // Task 342: Build constructionsafety.consulting Micro-Site
  342: {
    title: "Build constructionsafety.consulting Micro-Site (Separate Project)",
    description: `**PROJECT:** Micro-Site (Separate from main site)
**SITE:** constructionsafety.consulting (NEW domain)
**TYPE:** New 3-Page Static Site

**DESTINATION:**
- **Domain:** constructionsafety.consulting
- **Hosting:** Vercel (static hosting)
- **Deployment Location:** \`project-organization/02-micro-site-vercel/deployed-site/constructionsafety-microsite/\`

**PAGES TO CREATE:**
1. **Homepage**
   - URL: https://constructionsafety.consulting/
   - File: \`index.html\`

2. **SSHO Services Page**
   - URL: https://constructionsafety.consulting/ssho-services/
   - File: \`ssho-services.html\`

3. **Safety Representatives Page**
   - URL: https://constructionsafety.consulting/safety-representatives/
   - File: \`safety-representatives.html\`

**GOAL:**
Create a standalone 3-page micro-site focused on construction safety consulting. This is a SEPARATE project from the main ehsanalytical.com updates.

**CONTENT SOURCE:**
- **Location:** \`project-organization/02-micro-site-vercel/source-content/constructionsafety_consultingwebsite/\`
- **Files:**
  - \`MASTER_IMPLEMENTATION_GUIDE.txt\` - Complete implementation guide
  - \`Page1_Homepage_Content.txt\` - Homepage content
  - \`Page2_SSHO_Services_Content.txt\` - SSHO Services content
  - \`Page3_Safety_Representatives_Content.txt\` - Safety Representatives content
  - \`Technical_SEO_Specifications.txt\` - SEO requirements
  - \`Internal_Linking_Strategy.txt\` - Internal linking strategy
  - \`Design_and_Branding_Guide.txt\` - Design specifications

**TECHNICAL REQUIREMENTS:**
- Static HTML/CSS/JavaScript (no WordPress)
- Deploy to Vercel
- SSL certificate (automatic with Vercel)
- Mobile responsive
- Page speed target: < 3 seconds
- Branding: Navy blue + Safety orange (separate from main site)
- Positioning: "A Division of EHS Analytical Solutions, Inc."

**DOMAIN SETUP:**
- Purchase domain: constructionsafety.consulting
- Add domain to Vercel project
- Configure DNS records
- Set up 301 redirect: onsitesafety.consulting → constructionsafety.consulting

**DEPLOYMENT:**
\`\`\`bash
cd project-organization/02-micro-site-vercel/deployed-site/constructionsafety-microsite
vercel
\`\`\`

**TIMELINE:** 1-2 weeks
**ESTIMATED TIME:** 10-25 hours

**PRIORITY:** MEDIUM (Separate from main site priority)`,
  },
};

// Get all tasks for project 6
console.log("📋 Fetching all tasks for project 6...");
const tasksResponse = await agentFetch("/projects/6/tasks");
const tasks = tasksResponse.tasks || [];

console.log(`\n✅ Found ${tasks.length} tasks\n`);

// Update each task
for (const task of tasks) {
  const update = taskUpdates[task.id];
  if (!update) {
    console.log(`⚠️  No update defined for task ${task.id}: ${task.title}`);
    continue;
  }

  console.log(`\n📝 Updating task ${task.id}: ${task.title}`);
  console.log(`   Current title: ${task.title}`);
  console.log(`   New title: ${update.title}`);

  try {
    const result = await agentFetch(`/tasks/${task.uuid}`, {
      method: "PATCH",
      idempotent: true,
      body: {
        title: update.title,
        description: update.description,
      },
    });

    if (result.success || result.task) {
      console.log(`   ✅ Successfully updated task ${task.id}`);
    } else {
      console.log(`   ⚠️  Update response:`, JSON.stringify(result, null, 2));
    }
  } catch (error) {
    console.error(`   ❌ Error updating task ${task.id}:`, error.message);
  }
}

console.log("\n" + "=".repeat(80));
console.log("✅ Task update process complete!");
console.log("=".repeat(80));
