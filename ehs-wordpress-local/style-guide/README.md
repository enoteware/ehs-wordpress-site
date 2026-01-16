# EHS Design System

Complete design system documentation and style guide for EHS Analytical Solutions website.

## Quick Access

- **Style Guide (HTML):** [style-guide.html](style-guide.html) - Complete visual style guide
- **Quick Reference (MD):** [DESIGN_SYSTEM.md](DESIGN_SYSTEM.md) - Developer quick reference
- **Mega Menu Guide:** [../../MEGA_MENU_DESIGN_RECOMMENDATIONS.md](../../MEGA_MENU_DESIGN_RECOMMENDATIONS.md) - Complete mega menu implementation guide

## Interactive Demos

- **Mega Menu Demo:** [style-guide-assets/demos/mega-menu-demo.html](style-guide-assets/demos/mega-menu-demo.html)
- **Service Template Demo:** [style-guide-assets/demos/service-template-demo.html](style-guide-assets/demos/service-template-demo.html)

## Folder Structure

```
style-guide/
├── style-guide.html              # Main style guide (HTML)
├── DESIGN_SYSTEM.md              # Quick reference guide
├── style-guide-assets/
│   ├── js/
│   │   └── mega-menu.js          # Mega menu JavaScript
│   ├── demos/
│   │   ├── mega-menu-demo.html   # Standalone mega menu demo
│   │   └── service-template-demo.html  # Service template demo
│   └── README.md                 # Assets documentation
└── README.md                     # This file
```

## Usage

### Opening the Style Guide

Simply open `style-guide.html` in your browser:
- **Local:** `file:///path/to/style-guide/style-guide.html`
- **Or:** Navigate to the file in Finder and double-click

### Navigation

All pages include a consistent navigation bar at the top:
- **Style Guide** - Main documentation
- **Demos** - Interactive component demos
- **Quick Reference** - Markdown quick reference

### Assets

All assets (JavaScript, demos) are in the `style-guide-assets/` folder and use relative paths, so everything works when opening HTML files directly in a browser.

## Design System Principles

**Elementor = Structure | Theme CSS = Styling**

- **Elementor:** Layout, widgets, content organization, responsive breakpoints
- **Theme CSS:** All colors, typography, spacing, effects, visual styling
- **Integration:** Apply CSS classes via Elementor's "Advanced → CSS Classes" field

## Key Files

- **Theme CSS:** `../wordpress/wp-content/themes/hello-elementor-child/style.css`
- **Functions:** `../wordpress/wp-content/themes/hello-elementor-child/functions.php`
- **Header Template:** `../wordpress/wp-content/themes/hello-elementor-child/template-parts/header.php`
- **Footer Template:** `../wordpress/wp-content/themes/hello-elementor-child/template-parts/footer.php`
- **Service Template:** `../wordpress/wp-content/themes/hello-elementor-child/single-services.php`

---

**Last Updated:** January 2025
