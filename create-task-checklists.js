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

// Checklist items for each task (each item becomes a separate checklist)
const taskChecklists = {
  335: [
    "Review content source: `1_Web_Designer_Instructions_Part1_SSHO.json`",
    "Review implementation guide: `PAGE_IMPLEMENTATION_SSHO.md`",
    "Check existing service page template structure",
    "Verify WordPress Post ID: 3256 exists",
    "Open WordPress post ID 3256 in Elementor",
    "Match existing service page template (two-column layout with sidebar)",
    "Create hero section with full-width background image",
    "Add page title: \"SSHO Services California\"",
    "Implement all content sections from JSON file",
    "Ensure mobile responsive design",
    "Match color scheme: Navy blue (#003366) + Gold/Yellow accents",
    "Add \"What is an SSHO?\" section",
    "Add comprehensive SSHO services list",
    "Add federal agencies supported section",
    "Add California military installations section",
    "Add SSHO qualifications section",
    "Add \"Why Choose Us\" section",
    "Add call-to-action sections",
    "Add FAQ section (if included in content)",
    "Add page to main navigation menu (after Construction Safety)",
    "Update sidebar service menu on all service pages",
    "Add cross-links to/from related pages",
    "Verify all internal links work correctly",
    "Set meta title (Yoast SEO)",
    "Set meta description (Yoast SEO)",
    "Add focus keyword: \"SSHO Services California\"",
    "Optimize images with alt text",
    "Ensure page speed < 3 seconds",
    "Run Yoast SEO analysis",
    "Preview page on desktop",
    "Preview page on mobile",
    "Test all links and buttons",
    "Check spelling and grammar",
    "Get client review/approval",
    "Publish page (change status from draft to published)",
    "Verify live URL: https://ehsanalytical.com/ssho-services-california/",
  ],
  336: [
    "Review content source: `2_Web_Designer_Instructions_Part2_LeadCompliance.json`",
    "Check existing service page template structure",
    "Verify WordPress Post ID: 3257 exists",
    "Open WordPress post ID 3257 in Elementor",
    "Match existing service page template (two-column layout with sidebar)",
    "Create hero section with full-width background image",
    "Add page title: \"Lead Compliance Plan Services\"",
    "Implement all content sections from JSON file",
    "Ensure mobile responsive design",
    "Match color scheme: Navy blue (#003366) + Gold/Yellow accents",
    "Add introduction section",
    "Add Caltrans requirements section",
    "Add Lead Compliance Plan services list",
    "Add California districts coverage section",
    "Add DVBE certification benefits section",
    "Add \"Why Choose Us\" section",
    "Add call-to-action sections",
    "Add page to main navigation menu",
    "Update sidebar service menu on all service pages",
    "Add cross-link to/from Caltrans Construction Safety page",
    "Verify all internal links work correctly",
    "Set meta title (Yoast SEO)",
    "Set meta description (Yoast SEO)",
    "Add focus keyword: \"Lead Compliance Plan Services\"",
    "Optimize images with alt text",
    "Ensure page speed < 3 seconds",
    "Run Yoast SEO analysis",
    "Preview page on desktop",
    "Preview page on mobile",
    "Test all links and buttons",
    "Check spelling and grammar",
    "Get client review/approval",
    "Publish page (change status from draft to published)",
    "Verify live URL: https://ehsanalytical.com/lead-compliance-plan-services/",
  ],
  337: [
    "Review content source: `3_Part3_Caltrans_Construction_Safety_NEW.json`",
    "Check existing service page template structure",
    "Verify WordPress Post ID: 3258 exists",
    "Open WordPress post ID 3258 in Elementor",
    "Match existing service page template (two-column layout with sidebar)",
    "Create hero section with full-width background image",
    "Add page title: \"Caltrans Construction Safety Services\"",
    "Implement all content sections from JSON file",
    "Ensure mobile responsive design",
    "Match color scheme: Navy blue (#003366) + Gold/Yellow accents",
    "Add introduction section",
    "Add Caltrans-specific safety roles section (Safety Rep, SQCM, WAM)",
    "Add statewide coverage section",
    "Add services list for Caltrans projects",
    "Add qualifications section",
    "Add \"Why Choose Us\" section",
    "Add call-to-action sections",
    "Add page to main navigation menu",
    "Update sidebar service menu on all service pages",
    "Add cross-link to/from Lead Compliance Plan page",
    "Verify all internal links work correctly",
    "Set meta title (Yoast SEO)",
    "Set meta description (Yoast SEO)",
    "Add focus keyword: \"Caltrans Construction Safety Services\"",
    "Optimize images with alt text",
    "Ensure page speed < 3 seconds",
    "Run Yoast SEO analysis",
    "Preview page on desktop",
    "Preview page on mobile",
    "Test all links and buttons",
    "Check spelling and grammar",
    "Get client review/approval",
    "Publish page (change status from draft to published)",
    "Verify live URL: https://ehsanalytical.com/caltrans-construction-safety-services/",
  ],
  338: [
    "Review content source: `4_Web_Designer_Instructions_Part4_Federal_Contracting.json`",
    "Check existing service page template structure",
    "Verify WordPress Post ID: 3259 exists",
    "Open WordPress post ID 3259 in Elementor",
    "Match existing service page template (two-column layout with sidebar)",
    "Create hero section with full-width background image",
    "Add page title: \"Federal Contracting Services\"",
    "Implement all content sections from JSON file",
    "Ensure mobile responsive design",
    "Match color scheme: Navy blue (#003366) + Gold/Yellow accents",
    "Add introduction section (targeting USACE, NAVFAC, federal prime contractors)",
    "Add SDVOSB (Service-Disabled Veteran-Owned Small Business) section",
    "Add federal agencies served section (USACE, NAVFAC)",
    "Add services for federal contractors section",
    "Add partnership benefits section",
    "Add \"Why Choose Us\" section",
    "Add call-to-action sections",
    "Add page to main navigation menu",
    "Update sidebar service menu on all service pages",
    "Verify all internal links work correctly",
    "Set meta title (Yoast SEO)",
    "Set meta description (Yoast SEO)",
    "Add focus keyword: \"Federal Contracting SDVOSB\"",
    "Optimize images with alt text",
    "Ensure page speed < 3 seconds",
    "Run Yoast SEO analysis",
    "Preview page on desktop",
    "Preview page on mobile",
    "Test all links and buttons",
    "Check spelling and grammar",
    "Get client review/approval",
    "Publish page (change status from draft to published)",
    "Verify live URL: https://ehsanalytical.com/federal-contracting-sdvosb/",
  ],
  339: [
    "Review content source: `3_Part3_Caltrans_Construction_Safety_NEW.json`",
    "Check existing page: https://ehsanalytical.com/construction-safety-consulting/",
    "Verify WordPress Post ID: 3260 exists",
    "Review preview draft that was created",
    "Open WordPress post ID 3260 in Elementor",
    "Review existing page structure",
    "Add new section: Markets Served",
    "Add new section: Compliance Expertise",
    "Add new section: Geographic Coverage",
    "Add any additional sections from content source",
    "Ensure new sections match existing design style",
    "Ensure mobile responsive design",
    "Implement Markets Served section content",
    "Implement Compliance Expertise section content",
    "Implement Geographic Coverage section content",
    "Ensure content flows naturally with existing sections",
    "Update any outdated information if needed",
    "Add cross-links to new related pages (if applicable)",
    "Verify all internal links work correctly",
    "Update sidebar if needed",
    "Update meta description if needed (Yoast SEO)",
    "Review and update focus keywords if needed",
    "Optimize any new images with alt text",
    "Ensure page speed < 3 seconds",
    "Run Yoast SEO analysis",
    "Preview updated page on desktop",
    "Preview updated page on mobile",
    "Compare before/after to ensure quality",
    "Test all links and buttons",
    "Check spelling and grammar",
    "Get client review/approval",
    "Publish updates (if still in draft, change to published)",
    "Verify live URL: https://ehsanalytical.com/construction-safety-consulting/",
  ],
  341: [
    "Create full site backup (database + files)",
    "Schedule maintenance window (if needed)",
    "Notify client of maintenance (if site will be affected)",
    "Verify SSH access to server: 832f87585d.nxcli.net",
    "Check current WordPress version",
    "Review latest WordPress version available",
    "Check compatibility with active theme and plugins",
    "Update WordPress core (if update available)",
    "Verify site functionality after core update",
    "Check for plugin updates in WordPress admin",
    "Update Elementor (if update available)",
    "Update Elementor Pro (if update available)",
    "Update ACF Pro (Advanced Custom Fields Pro) (if update available)",
    "Update Yoast SEO (if update available)",
    "Update all other active plugins",
    "Update inactive plugins (optional but recommended)",
    "Verify site functionality after plugin updates",
    "Check for theme updates",
    "Update Hello Elementor theme (if update available)",
    "Update other installed themes (optional)",
    "Verify site functionality after theme update",
    "Run Elementor database updater: `wp elementor update db`",
    "Flush Elementor CSS cache: `wp elementor flush_css`",
    "Rebuild Yoast SEO index: `wp yoast index`",
    "Clear any other plugin caches",
    "Clear WordPress cache",
    "Clear CDN cache (if applicable)",
    "Clear browser cache (for testing)",
    "Test homepage loads correctly",
    "Test key pages load correctly",
    "Test navigation menus work",
    "Test forms work (if applicable)",
    "Check for any error messages",
    "Verify no broken functionality",
    "Check site speed/performance",
    "Review error logs for issues",
    "Document all updates performed",
    "Note any issues encountered",
    "Update maintenance log",
    "Notify client of completion",
  ],
  342: [
    "Review all content files in: `02-micro-site-vercel/source-content/constructionsafety_consultingwebsite/`",
    "Review MASTER_IMPLEMENTATION_GUIDE.txt",
    "Review Technical_SEO_Specifications.txt",
    "Review Design_and_Branding_Guide.txt",
    "Check existing HTML files in: `02-micro-site-vercel/deployed-site/constructionsafety-microsite/`",
    "Purchase domain: constructionsafety.consulting",
    "Set up DNS records",
    "Configure domain in Vercel project settings",
    "Verify SSL certificate is active (automatic with Vercel)",
    "Set up 301 redirect: onsitesafety.consulting → constructionsafety.consulting",
    "Review content: `Page1_Homepage_Content.txt`",
    "Create/index.html with all homepage content",
    "Implement hero section",
    "Add services overview (4 service boxes)",
    "Add \"Why Choose Us\" section",
    "Add Markets We Serve section",
    "Add Geographic Coverage section",
    "Add Credentials & Certifications section",
    "Add About Us section",
    "Add call-to-action sections",
    "Ensure mobile responsive design",
    "Apply branding: Navy blue (#003366) + Safety orange (#FF6600)",
    "Review content: `Page2_SSHO_Services_Content.txt`",
    "Create `ssho-services.html` with all SSHO content",
    "Add \"What is an SSHO?\" section",
    "Add comprehensive SSHO services (8 service boxes)",
    "Add Federal agencies supported section",
    "Add California military installations section",
    "Add SSHO qualifications section",
    "Add \"Why Choose Us\" section",
    "Add Mobilization info section",
    "Add FAQ section (6 questions)",
    "Ensure mobile responsive design for SSHO page",
    "Apply branding consistently",
    "Review content: `Page3_Safety_Representatives_Content.txt`",
    "Create `safety-representatives.html` with all content",
    "Add \"What does a Safety Rep do?\" section",
    "Add comprehensive services (10 service boxes)",
    "Add Project types supported section",
    "Add Cal-OSHA & Caltrans expertise section",
    "Add Qualifications section",
    "Add Engagement options section",
    "Add \"Why Choose Us\" section",
    "Add FAQ section (6 questions)",
    "Ensure mobile responsive design for Safety Rep page",
    "Apply branding consistently",
    "Apply Navy blue (#003366) + Safety orange (#FF6600) color scheme",
    "Use Montserrat font for headings",
    "Use Open Sans font for body text",
    "Ensure consistent navigation across all pages",
    "Add footer with company info and link to ehsanalytical.com",
    "Add \"A Division of EHS Analytical Solutions, Inc.\" positioning",
    "Ensure professional, modern design",
    "Review Internal_Linking_Strategy.txt",
    "Add navigation links between all pages",
    "Add cross-links within content",
    "Verify all links work correctly",
    "Source or create hero images for each page",
    "Optimize all images (WebP format, < 200KB each)",
    "Add proper alt text for all images",
    "Ensure images are mobile-responsive",
    "Review Technical_SEO_Specifications.txt",
    "Add meta titles for all pages",
    "Add meta descriptions for all pages",
    "Add proper heading structure (H1, H2, H3)",
    "Optimize page speed (< 3 seconds target)",
    "Create sitemap.xml",
    "Create robots.txt",
    "Add structured data (Schema.org) if applicable",
    "Test all pages locally",
    "Verify all links work",
    "Check mobile responsiveness",
    "Deploy to Vercel: `cd 02-micro-site-vercel/deployed-site/constructionsafety-microsite && vercel`",
    "Configure domain in Vercel",
    "Verify SSL certificate is active",
    "Test live site: https://constructionsafety.consulting",
    "Test all pages on live site",
    "Test on mobile devices",
    "Verify 301 redirect works: onsitesafety.consulting → constructionsafety.consulting",
    "Submit sitemap to Google Search Console",
    "Set up Google Analytics (if applicable)",
    "Test page speed on live site",
    "Verify all forms work (if applicable)",
    "Get client review/approval",
  ],
};

// Get all tasks for project 6
console.log("📋 Fetching all tasks for project 6...");
const tasksResponse = await agentFetch("/projects/6/tasks");
const tasks = tasksResponse.tasks || [];

console.log(`\n✅ Found ${tasks.length} tasks\n`);

// Create checklists for each task
for (const task of tasks) {
  const checklistItems = taskChecklists[task.id];
  if (!checklistItems) {
    console.log(`⚠️  No checklist items defined for task ${task.id}: ${task.title}`);
    continue;
  }

  // Check if checklists already exist
  const existingChecklists = await agentFetch(`/tasks/${task.uuid}/checklists`);
  if (existingChecklists.checklists && existingChecklists.checklists.length > 0) {
    console.log(`\n⏭️  Task ${task.id} already has ${existingChecklists.checklists.length} checklist(s), skipping...`);
    continue;
  }

  console.log(`\n📝 Creating ${checklistItems.length} checklist items for task ${task.id}: ${task.title}`);

  let position = 1;
  let created = 0;
  let errors = 0;

  for (const itemText of checklistItems) {
    try {
      const result = await agentFetch(`/tasks/${task.uuid}/checklists`, {
        method: "POST",
        idempotent: true,
        body: {
          title: itemText,
        },
      });

      if (result.checklist) {
        created++;
        position++;
      } else {
        errors++;
        console.log(`   ⚠️  Failed to create item: ${itemText.substring(0, 50)}...`);
      }
    } catch (error) {
      errors++;
      console.log(`   ❌ Error creating item: ${error.message}`);
    }
  }

  console.log(`   ✅ Created ${created} checklist items`);
  if (errors > 0) {
    console.log(`   ⚠️  ${errors} errors`);
  }
}

console.log("\n" + "=".repeat(80));
console.log("✅ Checklist creation complete!");
console.log("=".repeat(80));
