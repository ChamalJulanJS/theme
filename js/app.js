document.addEventListener("DOMContentLoaded", function () {

    /* ============================================================
       GSAP Animations (existing — unchanged)
       ============================================================ */
    if (typeof gsap !== 'undefined') {
        if (typeof ScrollTrigger !== 'undefined') {
            gsap.registerPlugin(ScrollTrigger);
        }
        gsap.utils.toArray('.gsap-fade-up').forEach(function (el) {
            gsap.from(el, {
                scrollTrigger: { trigger: el, start: "top 85%", once: true },
                y: 40, opacity: 0, duration: 0.8, ease: "power3.out"
            });
        });
        gsap.utils.toArray('.gsap-stagger-container, ul.products').forEach(function (container) {
            var items = container.querySelectorAll('.gsap-stagger-item, li.product');
            if (items.length > 0) {
                gsap.from(items, {
                    scrollTrigger: { trigger: container, start: "top 85%", once: true },
                    y: 40, opacity: 0, duration: 0.8, stagger: 0.15, ease: "power3.out"
                });
            }
        });
        gsap.utils.toArray('.gsap-stagger-item').forEach(function (el) {
            if (!el.closest('.gsap-stagger-container') && !el.closest('ul.products')) {
                gsap.from(el, {
                    scrollTrigger: { trigger: el, start: "top 85%", once: true },
                    y: 40, opacity: 0, duration: 0.8, ease: "power3.out"
                });
            }
        });
        console.log("GSAP Animations Loaded.");
    }

    /* ============================================================
       SHOP FILTER SIDEBAR — Nike Style
       ============================================================ */

    // ----- Filter Accordion (open/close sections) -----
    var sectionHeaders = document.querySelectorAll('.ff-filter-section-header');
    sectionHeaders.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var section = btn.closest('.ff-filter-section');
            var isOpen  = section.classList.contains('ff-section-open');
            section.classList.toggle('ff-section-open', !isOpen);
            btn.setAttribute('aria-expanded', String(!isOpen));
        });
    });

    // ----- Mobile Filter Drawer -----
    var openBtn    = document.getElementById('ff-filter-open-btn');
    var closeBtn   = document.getElementById('ff-filter-close-btn');
    var sidebar    = document.getElementById('ff-filter-sidebar');
    var overlay    = document.getElementById('ff-filter-overlay');

    function openFilterDrawer() {
        if (!sidebar || !overlay) return;
        sidebar.classList.add('ff-sidebar-open');
        overlay.classList.add('ff-overlay-active');
        document.body.style.overflow = 'hidden';
    }

    function closeFilterDrawer() {
        if (!sidebar || !overlay) return;
        sidebar.classList.remove('ff-sidebar-open');
        overlay.classList.remove('ff-overlay-active');
        document.body.style.overflow = '';
    }

    if (openBtn)  openBtn.addEventListener('click', openFilterDrawer);
    if (closeBtn) closeBtn.addEventListener('click', closeFilterDrawer);
    if (overlay)  overlay.addEventListener('click', closeFilterDrawer);

    // Close drawer on Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeFilterDrawer();
    });

    // ----- Size Chips Multi-Select -----
    var sizeChips  = document.querySelectorAll('.ff-size-chip');
    var sizeHidden = document.getElementById('ff-size-hidden');

    sizeChips.forEach(function (chip) {
        chip.addEventListener('click', function () {
            chip.classList.toggle('ff-size-active');
            // Rebuild hidden input value from all active chips
            if (sizeHidden) {
                var selected = [];
                document.querySelectorAll('.ff-size-chip.ff-size-active').forEach(function (c) {
                    selected.push(c.getAttribute('data-value'));
                });
                sizeHidden.value = selected.join(',');
            }
        });
    });

    // Auto-submit size form on chip click if NOT inside mobile drawer
    // (desktop: submit immediately; mobile uses Apply button)
    sizeChips.forEach(function (chip) {
        chip.addEventListener('dblclick', function () {
            var form = document.getElementById('ff-filter-form');
            if (form && window.innerWidth >= 1024) form.submit();
        });
    });

    // ----- Header scrolled class (existing behaviour for home) -----
    var header = document.querySelector('.site-header');
    if (header) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > 50) {
                header.classList.add('header-scrolled');
            } else {
                header.classList.remove('header-scrolled');
            }
        }, { passive: true });
    }
});

/* ============================================================
   Sort Filter — called globally from onchange attributes
   ============================================================ */
function ffApplySortFilter(value) {
    var url   = new URL(window.location.href);
    url.searchParams.set('orderby', value);
    url.searchParams.delete('paged'); // reset to page 1
    window.location.href = url.toString();
}
