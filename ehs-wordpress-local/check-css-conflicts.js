// CSS Conflict Analysis Script
// Run this in browser console to check for CSS conflicts

(function() {
  const results = {
    stylesheets: [],
    conflicts: [],
    elementorOverrides: [],
    importantDeclarations: [],
    duplicateSelectors: {}
  };

  // 1. Analyze all stylesheets
  const stylesheets = Array.from(document.styleSheets);
  stylesheets.forEach((sheet, index) => {
    try {
      const href = sheet.href || 'inline';
      const rules = Array.from(sheet.cssRules || []);
      
      results.stylesheets.push({
        index,
        href: href.substring(href.lastIndexOf('/') + 1),
        fullHref: href,
        ruleCount: rules.length,
        isElementor: href.includes('elementor'),
        isTheme: href.includes('hello-elementor-child'),
        isPlugin: href.includes('plugins'),
        loadOrder: index
      });

      // 2. Check for !important declarations that might conflict
      rules.forEach(rule => {
        if (rule.style) {
          const importantProps = [];
          for (let i = 0; i < rule.style.length; i++) {
            const prop = rule.style[i];
            const value = rule.style.getPropertyValue(prop);
            const priority = rule.style.getPropertyPriority(prop);
            
            if (priority === 'important') {
              importantProps.push({
                property: prop,
                value: value,
                selector: rule.selectorText
              });
            }
          }
          
          if (importantProps.length > 0) {
            results.importantDeclarations.push({
              stylesheet: href.substring(href.lastIndexOf('/') + 1),
              selector: rule.selectorText,
              properties: importantProps
            });
          }
        }
      });

      // 3. Check for Elementor CSS overriding theme CSS
      if (href.includes('elementor') && !href.includes('hello-elementor-child')) {
        rules.forEach(rule => {
          if (rule.selectorText) {
            // Check if this selector might conflict with theme CSS
            const selectors = [
              'a', 'a:hover', 'a:visited', 'a:focus',
              '.btn', '.button', 'button',
              'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
              '.ehs-', '.service-', '.article-',
              'body', 'p', 'li'
            ];
            
            selectors.forEach(themeSelector => {
              if (rule.selectorText.includes(themeSelector) || 
                  rule.selectorText === themeSelector) {
                results.elementorOverrides.push({
                  stylesheet: href.substring(href.lastIndexOf('/') + 1),
                  selector: rule.selectorText,
                  rule: rule.cssText.substring(0, 200)
                });
              }
            });
          }
        });
      }

      // 4. Track duplicate selectors
      rules.forEach(rule => {
        if (rule.selectorText) {
          const selector = rule.selectorText;
          if (!results.duplicateSelectors[selector]) {
            results.duplicateSelectors[selector] = [];
          }
          results.duplicateSelectors[selector].push({
            stylesheet: href.substring(href.lastIndexOf('/') + 1),
            rule: rule.cssText.substring(0, 150)
          });
        }
      });

    } catch (e) {
      // Cross-origin stylesheets can't be accessed
      if (sheet.href) {
        results.stylesheets.push({
          index,
          href: sheet.href.substring(sheet.href.lastIndexOf('/') + 1),
          fullHref: sheet.href,
          ruleCount: 'CORS blocked',
          error: e.message
        });
      }
    }
  });

  // 5. Check for post-2363.css specifically
  const hasPost2363 = results.stylesheets.some(s => s.href.includes('post-2363'));
  
  // 6. Check actual computed styles on key elements
  const testElements = {
    links: Array.from(document.querySelectorAll('a')).slice(0, 10).map(link => ({
      text: link.textContent.trim().substring(0, 30),
      color: window.getComputedStyle(link).color,
      classes: link.className,
      href: link.href
    })),
    buttons: Array.from(document.querySelectorAll('button, .btn, .button, .elementor-button')).slice(0, 10).map(btn => ({
      text: btn.textContent.trim().substring(0, 30),
      backgroundColor: window.getComputedStyle(btn).backgroundColor,
      color: window.getComputedStyle(btn).color,
      classes: btn.className
    })),
    headings: Array.from(document.querySelectorAll('h1, h2, h3')).slice(0, 5).map(h => ({
      tag: h.tagName,
      text: h.textContent.trim().substring(0, 40),
      color: window.getComputedStyle(h).color,
      fontFamily: window.getComputedStyle(h).fontFamily,
      classes: h.className
    }))
  };

  // 7. Identify potential conflicts
  const potentialConflicts = [];
  
  // Check if Elementor CSS loads after theme CSS
  const themeIndex = results.stylesheets.findIndex(s => s.isTheme);
  const elementorIndices = results.stylesheets
    .map((s, i) => s.isElementor ? i : -1)
    .filter(i => i !== -1);
  
  if (themeIndex !== -1 && elementorIndices.length > 0) {
    const elementorAfterTheme = elementorIndices.some(i => i > themeIndex);
    if (elementorAfterTheme) {
      potentialConflicts.push({
        type: 'load-order',
        issue: 'Elementor CSS loads after theme CSS, which can cause overrides',
        themeIndex,
        elementorIndices
      });
    }
  }

  return {
    summary: {
      totalStylesheets: results.stylesheets.length,
      elementorStylesheets: results.stylesheets.filter(s => s.isElementor).length,
      themeStylesheets: results.stylesheets.filter(s => s.isTheme).length,
      hasPost2363: hasPost2363,
      importantDeclarations: results.importantDeclarations.length,
      elementorOverrides: results.elementorOverrides.length,
      potentialConflicts: potentialConflicts.length
    },
    stylesheets: results.stylesheets,
    importantDeclarations: results.importantDeclarations.slice(0, 20),
    elementorOverrides: results.elementorOverrides.slice(0, 20),
    duplicateSelectors: Object.entries(results.duplicateSelectors)
      .filter(([selector, rules]) => rules.length > 1)
      .slice(0, 10)
      .reduce((obj, [selector, rules]) => {
        obj[selector] = rules;
        return obj;
      }, {}),
    testElements,
    potentialConflicts
  };
})();
