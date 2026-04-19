document.addEventListener('DOMContentLoaded', function() {
    const burgerBtn = document.getElementById('burgerBtn');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.querySelector('.main-content');

    const overlay = document.createElement('div');
    overlay.className = 'sidebar-overlay';
    document.body.appendChild(overlay);

    if (burgerBtn && sidebar) {
        burgerBtn.addEventListener('click', function() {
            sidebar.classList.toggle('mobile-open');
            overlay.classList.toggle('show');

            const icon = burgerBtn.querySelector('i');
            if (sidebar.classList.contains('mobile-open')) {
                icon.classList.remove('bi-list');
                icon.classList.add('bi-x-lg');
                document.body.style.overflow = 'hidden';
            } else {
                icon.classList.remove('bi-x-lg');
                icon.classList.add('bi-list');
                document.body.style.overflow = '';
            }
        });

        overlay.addEventListener('click', function() {
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('show');
            const icon = burgerBtn.querySelector('i');
            icon.classList.remove('bi-x-lg');
            icon.classList.add('bi-list');
            document.body.style.overflow = '';
        });

        const navLinks = sidebar.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    sidebar.classList.remove('mobile-open');
                    overlay.classList.remove('show');
                    const icon = burgerBtn.querySelector('i');
                    icon.classList.remove('bi-x-lg');
                    icon.classList.add('bi-list');
                    document.body.style.overflow = '';
                }
            });
        });
    }

    window.addEventListener('resize', function() {
        if (window.innerWidth > 768 && sidebar) {
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('show');
            document.body.style.overflow = '';
            if (burgerBtn) {
                const icon = burgerBtn.querySelector('i');
                icon.classList.remove('bi-x-lg');
                icon.classList.add('bi-list');
            }
        }
    });
});
