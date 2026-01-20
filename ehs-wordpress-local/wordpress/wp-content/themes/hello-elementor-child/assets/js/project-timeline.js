/**
 * Project Timeline Scroll Animation
 *
 * - Progress line fills as user scrolls down
 * - Timeline items activate when they enter viewport
 * - Calendar icons highlight when active
 */
(function() {
    'use strict';

    function initProjectTimeline() {
        const timelines = document.querySelectorAll('.project-timeline');

        if (!timelines.length) return;

        function normalizeProjectTitles(timeline) {
            const cards = timeline.querySelectorAll('.project-timeline__card');
            if (!cards.length) return;

            cards.forEach(function(card) {
                const yearEl = card.querySelector('.project-timeline__card-year');
                const titleEl = card.querySelector('.project-timeline__card-title');
                if (!titleEl) return;

                const titleText = (titleEl.textContent || '').trim();
                if (!titleText) return;

                // Match "2023 – Project Name" (hyphen/en-dash/em-dash)
                const match = titleText.match(/^(\d{4})\s*[-–—]\s*(.+)$/);
                if (!match) return;

                const parsedYear = match[1];
                const parsedTitle = (match[2] || '').trim();
                if (!parsedTitle) return;

                // Prefer the year embedded in the title to avoid mismatches/duplication.
                if (yearEl) {
                    yearEl.textContent = parsedYear;
                }
                titleEl.textContent = parsedTitle;
            });
        }

        timelines.forEach(function(timeline) {
            normalizeProjectTitles(timeline);

            const progressLine = timeline.querySelector('.project-timeline__progress');
            const items = timeline.querySelectorAll('.project-timeline__item');
            const line = timeline.querySelector('.project-timeline__line');

            if (!progressLine || !items.length || !line) return;

            // Set initial state - first item active
            if (items[0]) {
                items[0].classList.add('is-active');
            }

            function updateTimeline() {
                const timelineRect = timeline.getBoundingClientRect();
                const lineRect = line.getBoundingClientRect();
                const viewportHeight = window.innerHeight;
                const triggerPoint = viewportHeight * 0.6; // Trigger when 60% up from bottom

                // Calculate progress based on scroll position
                const timelineTop = timelineRect.top;
                const timelineHeight = timelineRect.height;
                const scrollProgress = Math.max(0, Math.min(1,
                    (triggerPoint - timelineTop) / timelineHeight
                ));

                // Update progress line height
                progressLine.style.height = (scrollProgress * 100) + '%';

                // Update active states for items
                items.forEach(function(item, index) {
                    const itemRect = item.getBoundingClientRect();
                    const itemCenter = itemRect.top + (itemRect.height / 2);

                    if (itemCenter < triggerPoint) {
                        item.classList.add('is-active');
                    } else {
                        // Keep first item always active
                        if (index > 0) {
                            item.classList.remove('is-active');
                        }
                    }
                });
            }

            // Throttled scroll handler
            let ticking = false;
            function onScroll() {
                if (!ticking) {
                    window.requestAnimationFrame(function() {
                        updateTimeline();
                        ticking = false;
                    });
                    ticking = true;
                }
            }

            // Initialize
            window.addEventListener('scroll', onScroll, { passive: true });
            window.addEventListener('resize', updateTimeline, { passive: true });

            // Initial update
            updateTimeline();
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initProjectTimeline);
    } else {
        initProjectTimeline();
    }

    // Re-initialize if content is dynamically loaded
    if (typeof MutationObserver !== 'undefined') {
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes.length) {
                    initProjectTimeline();
                }
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }
})();
