#!/bin/bash
# Visual screenshot comparison for local vs production pages
# Requires: playwright (will auto-install if needed)

set -e

echo "📸 Visual Page Comparison Tool"
echo "=============================="
echo ""

LOCAL_URL="${1:-https://ehs-local.ddev.site/about-us/}"
PROD_URL="${2:-https://ehsanalytical.com/about-us/}"
OUTPUT_DIR="./screenshots-comparison"

# Create output directory
mkdir -p "$OUTPUT_DIR"

echo "Comparing:"
echo "  Local:      $LOCAL_URL"
echo "  Production: $PROD_URL"
echo ""

# Check if Node.js is available
if ! command -v node &> /dev/null; then
    echo "❌ Node.js is required but not installed"
    exit 1
fi

# Create comparison script
cat > "$OUTPUT_DIR/compare.js" << 'ENDJS'
const { chromium } = require('playwright');
const fs = require('fs');

(async () => {
  const localUrl = process.argv[2];
  const prodUrl = process.argv[3];
  const outputDir = process.argv[4];

  console.log('🚀 Launching browser...');
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext({
    viewport: { width: 1920, height: 1080 },
    ignoreHTTPSErrors: true
  });

  const page = await context.newPage();

  // Screenshot local
  console.log('📸 Capturing local page...');
  await page.goto(localUrl, { waitUntil: 'networkidle', timeout: 30000 });
  await page.waitForTimeout(2000); // Wait for animations
  await page.screenshot({
    path: `${outputDir}/local-full.png`,
    fullPage: true
  });
  await page.screenshot({
    path: `${outputDir}/local-viewport.png`,
    fullPage: false
  });

  // Get computed styles for headings
  const localHeadings = await page.evaluate(() => {
    const headings = [];
    ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'].forEach(tag => {
      const elements = document.querySelectorAll(tag);
      elements.forEach((el, i) => {
        const style = window.getComputedStyle(el);
        headings.push({
          tag,
          index: i,
          text: el.textContent.trim().substring(0, 50),
          fontFamily: style.fontFamily,
          fontWeight: style.fontWeight,
          fontSize: style.fontSize,
          color: style.color
        });
      });
    });
    return headings;
  });

  // Screenshot production
  console.log('📸 Capturing production page...');
  await page.goto(prodUrl, { waitUntil: 'networkidle', timeout: 30000 });
  await page.waitForTimeout(2000);
  await page.screenshot({
    path: `${outputDir}/prod-full.png`,
    fullPage: true
  });
  await page.screenshot({
    path: `${outputDir}/prod-viewport.png`,
    fullPage: false
  });

  // Get computed styles for headings
  const prodHeadings = await page.evaluate(() => {
    const headings = [];
    ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'].forEach(tag => {
      const elements = document.querySelectorAll(tag);
      elements.forEach((el, i) => {
        const style = window.getComputedStyle(el);
        headings.push({
          tag,
          index: i,
          text: el.textContent.trim().substring(0, 50),
          fontFamily: style.fontFamily,
          fontWeight: style.fontWeight,
          fontSize: style.fontSize,
          color: style.color
        });
      });
    });
    return headings;
  });

  await browser.close();

  // Compare headings
  console.log('\n📊 Typography Comparison:');
  console.log('========================\n');

  const differences = [];
  localHeadings.forEach((local, i) => {
    const prod = prodHeadings[i];
    if (!prod) {
      differences.push(`Missing in production: ${local.tag} "${local.text}"`);
      return;
    }

    if (local.fontFamily !== prod.fontFamily) {
      differences.push(`${local.tag}[${i}]: Font family differs\n  Local: ${local.fontFamily}\n  Prod:  ${prod.fontFamily}`);
    }
    if (local.fontWeight !== prod.fontWeight) {
      differences.push(`${local.tag}[${i}] "${local.text}": Font weight differs\n  Local: ${local.fontWeight}\n  Prod:  ${prod.fontWeight}`);
    }
    if (local.fontSize !== prod.fontSize) {
      differences.push(`${local.tag}[${i}]: Font size differs\n  Local: ${local.fontSize}\n  Prod:  ${prod.fontSize}`);
    }
  });

  if (differences.length > 0) {
    console.log('❌ Differences found:\n');
    differences.forEach(diff => console.log(diff + '\n'));
  } else {
    console.log('✅ All typography matches!\n');
  }

  // Save detailed comparison
  const report = {
    timestamp: new Date().toISOString(),
    localUrl,
    prodUrl,
    local: localHeadings,
    production: prodHeadings,
    differences
  };

  fs.writeFileSync(
    `${outputDir}/comparison-report.json`,
    JSON.stringify(report, null, 2)
  );

  console.log(`\n✅ Screenshots saved to ${outputDir}/`);
  console.log('   - local-full.png (full page)');
  console.log('   - local-viewport.png (above fold)');
  console.log('   - prod-full.png (full page)');
  console.log('   - prod-viewport.png (above fold)');
  console.log('   - comparison-report.json (detailed analysis)\n');
})();
ENDJS

# Check if playwright is installed
echo "🔍 Checking for Playwright..."
if ! npm list playwright &> /dev/null; then
    echo "📦 Installing Playwright..."
    npm install playwright
    npx playwright install chromium
fi

# Run comparison
echo ""
echo "🔄 Running comparison..."
node "$OUTPUT_DIR/compare.js" "$LOCAL_URL" "$PROD_URL" "$OUTPUT_DIR"

echo ""
echo "🎯 Next steps:"
echo "  1. Open $OUTPUT_DIR/ to view screenshots"
echo "  2. Review comparison-report.json for detailed differences"
echo "  3. Compare screenshots side-by-side"
echo ""
