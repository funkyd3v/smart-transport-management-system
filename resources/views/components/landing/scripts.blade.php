<div>
    <!-- Very little is needed to make a happy life. - Marcus Aurelius -->
</div>
<script>
    const navbar = document.getElementById('navbar');

    if (navbar) {
        window.addEventListener('scroll', () => navbar.classList.toggle('scrolled', window.scrollY > 50), {
            passive: true,
        });
    }

    const menuToggle = document.getElementById('menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');

    if (menuToggle && mobileMenu) {
        menuToggle.addEventListener('click', () => {
            mobileMenu.classList.toggle('open');
        });

        document.querySelectorAll('#mobile-menu a').forEach((link) => {
            link.addEventListener('click', () => mobileMenu.classList.remove('open'));
        });
    }

    document.querySelectorAll('a[href^="#"]').forEach((link) => {
        link.addEventListener('click', (event) => {
            const target = document.querySelector(link.getAttribute('href'));

            if (target) {
                event.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                const delay = Number.parseInt(entry.target.dataset.delay || 0, 10);

                setTimeout(() => entry.target.classList.add('visible'), delay);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

    document.querySelectorAll('.reveal').forEach((element) => observer.observe(element));
</script>
