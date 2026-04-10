document.addEventListener('DOMContentLoaded', () => {

    /* 1. SCROLL ANIMATIONS (Fade In Up) */
    const fadeElements = document.querySelectorAll('.ff-fade-up');
    
    const fadeObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('ff-visible');
                observer.unobserve(entry.target);
            }
        });
    }, {
        root: null,
        threshold: 0.15,
        rootMargin: "0px 0px -50px 0px"
    });

    fadeElements.forEach(el => fadeObserver.observe(el));

    /* 2. DYNAMIC STICKY HEADER */
    const header = document.querySelector('.site-header');
    if (header) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 80) {
                header.classList.add('header-scrolled');
            } else {
                header.classList.remove('header-scrolled');
            }
        });

        /* Initialize header state on load */
        if (window.scrollY > 80) header.classList.add('header-scrolled');
    }

    /* 3. CUSTOM LUXURY CURSOR */
    // Only apply on non-touch (desktop) environments
    if (window.matchMedia("(pointer: fine)").matches) {
        const cursorDot = document.createElement('div');
        cursorDot.classList.add('ff-custom-cursor');
        document.body.appendChild(cursorDot);

        let mouseX = window.innerWidth / 2, mouseY = window.innerHeight / 2;
        let cursorX = mouseX, cursorY = mouseY;

        window.addEventListener('mousemove', (e) => {
            mouseX = e.clientX;
            mouseY = e.clientY;
            // Immediate display on first move if hiding by default
            cursorDot.style.opacity = '1';
        });

        const animateCursor = () => {
            // Smooth lerp mathematical formula (current = current + (target - current) * speed)
            cursorX += (mouseX - cursorX) * 0.2;
            cursorY += (mouseY - cursorY) * 0.2;
            cursorDot.style.transform = `translate3d(${cursorX}px, ${cursorY}px, 0) translate(-50%, -50%)`;
            requestAnimationFrame(animateCursor);
        };
        animateCursor();

        // Enlarge on interactive elements
        const interactables = document.querySelectorAll('a, button, input, textarea, select, .ff-product-card');
        interactables.forEach(el => {
            el.addEventListener('mouseenter', () => cursorDot.classList.add('cursor-hover'));
            el.addEventListener('mouseleave', () => cursorDot.classList.remove('cursor-hover'));
        });
    }

});
