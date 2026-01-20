/**
 * EHS Mega Menu JavaScript
 * 
 * Handles mega menu interactions including:
 * - Hover/click to open/close
 * - Mobile accordion functionality
 * - Keyboard navigation
 * - Click outside to close
 * 
 * @version 1.0.0
 * @date January 2025
 */

(function() {
    'use strict';

    /**
     * Initialize mega menu functionality
     */
    function initMegaMenu() {
        const menuItems = document.querySelectorAll('.ehs-header-nav .menu-item-has-children');
        const megaMenus = document.querySelectorAll('.mega-menu');
        const hideTimers = new WeakMap();
        
        if (!menuItems.length || !megaMenus.length) {
            return; // No mega menus found
        }

        function showMenu(megaMenu) {
            if (!megaMenu) return;
            const timer = hideTimers.get(megaMenu);
            if (timer) {
                clearTimeout(timer);
                hideTimers.delete(megaMenu);
            }
            megaMenu.style.display = 'block';
        }

        function scheduleHide(menuItem, megaMenu) {
            if (!megaMenu) return;
            const timer = setTimeout(function() {
                megaMenu.style.display = 'none';
            }, 150);
            hideTimers.set(megaMenu, timer);
        }

        // Mark mega menu parents for layout-specific styling
        menuItems.forEach(function(menuItem) {
            if (menuItem.querySelector('.mega-menu')) {
                menuItem.classList.add('has-mega-menu');
            }
        });

        // Desktop: Hover to open
        menuItems.forEach(function(menuItem) {
            const megaMenu = menuItem.querySelector('.mega-menu');
            if (!megaMenu) return;

            // Hover handlers - use mouseenter/mouseleave on both item and menu
            menuItem.addEventListener('mouseenter', function() {
                if (window.innerWidth > 767) {
                    showMenu(megaMenu);
                }
            });

            // Keep menu open when hovering over the mega menu itself
            megaMenu.addEventListener('mouseenter', function() {
                if (window.innerWidth > 767) {
                    showMenu(megaMenu);
                }
            });

            // Close when leaving both the item and the menu
            menuItem.addEventListener('mouseleave', function(e) {
                if (window.innerWidth > 767) {
                    // Check if we're moving to the mega menu
                    if (!megaMenu.contains(e.relatedTarget)) {
                        scheduleHide(menuItem, megaMenu);
                    }
                }
            });

            megaMenu.addEventListener('mouseleave', function(e) {
                if (window.innerWidth > 767) {
                    // Check if we're moving back to the menu item
                    if (!menuItem.contains(e.relatedTarget)) {
                        scheduleHide(menuItem, megaMenu);
                    }
                }
            });

            // Focus handlers for keyboard navigation
            // Use querySelector with :scope to get direct child, or just get first <a>
            const menuLink = menuItem.querySelector(':scope > a') || menuItem.querySelector('a');
            if (menuLink) {
                menuLink.addEventListener('focus', function() {
                    if (window.innerWidth > 767) {
                        megaMenu.style.display = 'block';
                    }
                });
            }

            // Close when focus leaves
            megaMenu.addEventListener('focusout', function(e) {
                if (window.innerWidth > 767) {
                    // Check if focus moved to another element within the menu
                    if (!menuItem.contains(e.relatedTarget)) {
                        megaMenu.style.display = 'none';
                    }
                }
            });
        });

        // Mobile: Accordion functionality
        if (window.innerWidth <= 767) {
            initMobileAccordion();
        }

        // Handle window resize
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                if (window.innerWidth <= 767) {
                    initMobileAccordion();
                } else {
                    // Reset mobile accordion
                    const columns = document.querySelectorAll('.mega-menu-column');
                    columns.forEach(function(column) {
                        column.classList.remove('active');
                    });
                }
            }, 250);
        });

        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            const clickedInside = event.target.closest('.menu-item-has-children') || 
                                 event.target.closest('.mega-menu');
            
            if (!clickedInside) {
                megaMenus.forEach(function(menu) {
                    menu.style.display = 'none';
                });
            }
        });

        // Keyboard navigation: Enter/Space to toggle on mobile
        menuItems.forEach(function(menuItem) {
            const menuLink = menuItem.querySelector(':scope > a') || menuItem.querySelector('a');
            if (menuLink) {
                menuLink.addEventListener('keydown', function(e) {
                    if ((e.key === 'Enter' || e.key === ' ') && window.innerWidth <= 767) {
                        e.preventDefault();
                        const megaMenu = menuItem.querySelector('.mega-menu');
                        if (megaMenu) {
                            megaMenu.style.display = megaMenu.style.display === 'none' ? 'block' : 'none';
                        }
                    }
                });
            }
        });
    }

    /**
     * Initialize mobile accordion for mega menu columns
     */
    function initMobileAccordion() {
        const columns = document.querySelectorAll('.mega-menu-column');
        
        columns.forEach(function(column) {
            const title = column.querySelector('.mega-menu-column-title');
            if (!title) return;

            // Remove existing listeners to prevent duplicates
            const newTitle = title.cloneNode(true);
            title.parentNode.replaceChild(newTitle, title);

            newTitle.addEventListener('click', function() {
                column.classList.toggle('active');
            });

            // Make title keyboard accessible
            newTitle.setAttribute('tabindex', '0');
            newTitle.setAttribute('role', 'button');
            newTitle.setAttribute('aria-expanded', 'false');
            
            newTitle.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    column.classList.toggle('active');
                    newTitle.setAttribute('aria-expanded', column.classList.contains('active'));
                }
            });

            // Update aria-expanded on click
            newTitle.addEventListener('click', function() {
                newTitle.setAttribute('aria-expanded', column.classList.contains('active'));
            });
        });
    }

    /**
     * Initialize on DOM ready
     */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initMegaMenu);
    } else {
        initMegaMenu();
    }

})();
