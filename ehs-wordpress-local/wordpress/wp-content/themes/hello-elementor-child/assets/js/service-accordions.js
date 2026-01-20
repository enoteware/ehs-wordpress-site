/**
 * Service Accordions
 *
 * Handle expand/collapse functionality for service page accordions
 */

(function() {
    'use strict';

    /**
     * Initialize accordions
     */
    function initAccordions() {
        const headers = document.querySelectorAll('.accordion-header');

        if (headers.length === 0) {
            return; // No accordions on this page
        }

        headers.forEach(header => {
            header.addEventListener('click', toggleAccordion);
            header.addEventListener('keydown', handleKeydown);
        });
    }

    /**
     * Toggle accordion open/closed
     */
    function toggleAccordion(event) {
        const header = event.currentTarget;
        const isOpen = header.getAttribute('aria-expanded') === 'true';
        const contentId = header.getAttribute('aria-controls');
        const content = document.getElementById(contentId);

        if (!content) {
            return;
        }

        if (isOpen) {
            closeAccordion(header, content);
        } else {
            openAccordion(header, content);
        }
    }

    /**
     * Open accordion
     */
    function openAccordion(header, content) {
        header.setAttribute('aria-expanded', 'true');
        header.classList.add('is-open');
        content.classList.add('is-open');
        content.style.maxHeight = content.scrollHeight + 'px';
    }

    /**
     * Close accordion
     */
    function closeAccordion(header, content) {
        header.setAttribute('aria-expanded', 'false');
        header.classList.remove('is-open');
        content.classList.remove('is-open');
        content.style.maxHeight = '0px';
    }

    /**
     * Handle keyboard navigation
     * - Enter/Space: Toggle accordion
     * - Up/Down: Navigate between accordions
     */
    function handleKeydown(event) {
        const header = event.currentTarget;

        if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            toggleAccordion({ currentTarget: header });
        }

        // Optional: Up/Down arrow navigation between accordions
        if (event.key === 'ArrowDown') {
            const nextHeader = header.parentElement.nextElementSibling?.querySelector('.accordion-header');
            if (nextHeader) {
                nextHeader.focus();
            }
        }

        if (event.key === 'ArrowUp') {
            const prevHeader = header.parentElement.previousElementSibling?.querySelector('.accordion-header');
            if (prevHeader) {
                prevHeader.focus();
            }
        }
    }

    /**
     * Handle window resize - adjust open accordions if content height changes
     */
    function handleResize() {
        const openContents = document.querySelectorAll('.accordion-content.is-open');

        openContents.forEach(content => {
            content.style.maxHeight = content.scrollHeight + 'px';
        });
    }

    /**
     * Initialize on DOM ready
     */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAccordions);
    } else {
        initAccordions();
    }

    // Re-adjust accordion heights on window resize
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(handleResize, 250);
    });

    // Re-initialize accordions if content is dynamically loaded
    // (e.g., after AJAX requests)
    if (typeof MutationObserver !== 'undefined') {
        const observer = new MutationObserver(initAccordions);
        observer.observe(document.body, { childList: true, subtree: true });
    }
})();
