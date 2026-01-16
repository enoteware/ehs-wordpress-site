/**
 * Service Table of Contents - Scroll Spy and Smooth Scroll
 *
 * Uses Intersection Observer API for scroll tracking
 * No jQuery dependency
 */
(function() {
    'use strict';

    // Configuration
    const CONFIG = {
        rootMargin: '-100px 0px -70% 0px',
        threshold: 0,
        smoothScrollBehavior: 'smooth',
        activeClass: 'service-toc__link--active',
        mobileBreakpoint: 992,
        scrollOffset: 100
    };

    // State
    let tocLinks = [];
    let headings = [];
    let observer = null;
    let isMobile = false;

    /**
     * Initialize ToC functionality
     */
    function init() {
        tocLinks = document.querySelectorAll('.service-toc__link');

        if (tocLinks.length === 0) {
            return;
        }

        // Get all heading IDs from ToC links
        headings = Array.from(tocLinks).map(link => {
            const id = link.getAttribute('data-toc-id');
            return document.getElementById(id);
        }).filter(Boolean);

        if (headings.length === 0) {
            return;
        }

        // Check viewport size
        checkMobile();
        window.addEventListener('resize', debounce(checkMobile, 150));

        // Setup Intersection Observer
        setupObserver();

        // Setup click handlers for smooth scroll
        setupClickHandlers();

        // Setup keyboard navigation
        setupKeyboardNav();

        // Set initial active state
        setInitialActive();
    }

    /**
     * Setup Intersection Observer for scroll spy
     */
    function setupObserver() {
        if (observer) {
            observer.disconnect();
        }

        observer = new IntersectionObserver(handleIntersection, {
            rootMargin: CONFIG.rootMargin,
            threshold: CONFIG.threshold
        });

        headings.forEach(heading => observer.observe(heading));
    }

    /**
     * Handle intersection changes
     */
    function handleIntersection(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                setActiveLink(entry.target.id);
            }
        });
    }

    /**
     * Set active ToC link
     */
    function setActiveLink(id) {
        tocLinks.forEach(link => {
            const linkId = link.getAttribute('data-toc-id');
            if (linkId === id) {
                link.classList.add(CONFIG.activeClass);
                link.setAttribute('aria-current', 'true');
            } else {
                link.classList.remove(CONFIG.activeClass);
                link.removeAttribute('aria-current');
            }
        });
    }

    /**
     * Set initial active state based on scroll position
     */
    function setInitialActive() {
        const scrollPos = window.scrollY + CONFIG.scrollOffset + 50;
        let activeHeading = headings[0];

        for (const heading of headings) {
            if (heading.offsetTop <= scrollPos) {
                activeHeading = heading;
            }
        }

        if (activeHeading) {
            setActiveLink(activeHeading.id);
        }
    }

    /**
     * Setup click handlers for smooth scroll
     */
    function setupClickHandlers() {
        tocLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('data-toc-id');
                const target = document.getElementById(targetId);

                if (target) {
                    const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - CONFIG.scrollOffset;

                    window.scrollTo({
                        top: targetPosition,
                        behavior: CONFIG.smoothScrollBehavior
                    });

                    // Update URL hash without jumping
                    history.pushState(null, null, '#' + targetId);

                    // Set focus to heading for accessibility
                    target.setAttribute('tabindex', '-1');
                    target.focus({ preventScroll: true });

                    // Manually set active link
                    setActiveLink(targetId);
                }

                // Close mobile ToC if open
                if (isMobile) {
                    closeMobileToC();
                }
            });
        });
    }

    /**
     * Setup keyboard navigation
     */
    function setupKeyboardNav() {
        const tocNav = document.querySelector('.service-toc');
        if (!tocNav) return;

        tocNav.addEventListener('keydown', function(e) {
            const links = Array.from(tocLinks);
            const currentIndex = links.indexOf(document.activeElement);

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                const nextIndex = (currentIndex + 1) % links.length;
                links[nextIndex].focus();
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                const prevIndex = currentIndex <= 0 ? links.length - 1 : currentIndex - 1;
                links[prevIndex].focus();
            } else if (e.key === 'Home') {
                e.preventDefault();
                links[0].focus();
            } else if (e.key === 'End') {
                e.preventDefault();
                links[links.length - 1].focus();
            }
        });
    }

    /**
     * Check if viewport is mobile
     */
    function checkMobile() {
        const wasMobile = isMobile;
        isMobile = window.innerWidth < CONFIG.mobileBreakpoint;

        if (wasMobile !== isMobile) {
            setupMobileToC();
        }
    }

    /**
     * Setup mobile ToC toggle behavior
     */
    function setupMobileToC() {
        const tocNav = document.querySelector('.service-toc');
        if (!tocNav) return;

        if (isMobile) {
            tocNav.classList.add('service-toc--mobile');

            // Add toggle button if not exists
            if (!tocNav.querySelector('.service-toc__toggle')) {
                const toggle = document.createElement('button');
                toggle.className = 'service-toc__toggle';
                toggle.setAttribute('aria-expanded', 'false');
                toggle.setAttribute('aria-controls', 'service-toc-list');
                toggle.innerHTML = '<span>On This Page</span><svg class="service-toc__toggle-icon" viewBox="0 0 24 24" width="20" height="20"><path fill="currentColor" d="M7 10l5 5 5-5z"/></svg>';

                toggle.addEventListener('click', toggleMobileToC);

                const header = tocNav.querySelector('.service-toc__header');
                if (header) {
                    header.style.display = 'none';
                }
                tocNav.insertBefore(toggle, tocNav.firstChild);
            }
        } else {
            tocNav.classList.remove('service-toc--mobile');
            tocNav.classList.remove('service-toc--expanded');

            // Remove toggle and restore header
            const toggle = tocNav.querySelector('.service-toc__toggle');
            if (toggle) {
                toggle.remove();
            }
            const header = tocNav.querySelector('.service-toc__header');
            if (header) {
                header.style.display = '';
            }
        }
    }

    /**
     * Toggle mobile ToC visibility
     */
    function toggleMobileToC() {
        const tocNav = document.querySelector('.service-toc');
        const toggle = tocNav.querySelector('.service-toc__toggle');
        const isExpanded = tocNav.classList.contains('service-toc--expanded');

        tocNav.classList.toggle('service-toc--expanded');
        toggle.setAttribute('aria-expanded', !isExpanded);
    }

    /**
     * Close mobile ToC
     */
    function closeMobileToC() {
        const tocNav = document.querySelector('.service-toc');
        const toggle = tocNav.querySelector('.service-toc__toggle');

        if (tocNav && toggle) {
            tocNav.classList.remove('service-toc--expanded');
            toggle.setAttribute('aria-expanded', 'false');
        }
    }

    /**
     * Debounce utility
     */
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
