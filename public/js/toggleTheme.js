document.addEventListener('DOMContentLoaded', function () {
    const themeToggle = document.getElementById('themeToggle');
    if (themeToggle) {
        const isLight = localStorage.getItem('theme') === 'light';
        if (isLight) {
            document.body.classList.add('light');
            themeToggle.innerHTML = '<i class="bi bi-sun"></i>';
        }

        themeToggle.addEventListener('click', function () {
            document.body.classList.toggle('light');
            const isLightMode = document.body.classList.contains('light');
            localStorage.setItem('theme', isLightMode ? 'light' : 'dark');
            this.innerHTML = isLightMode ? '<i class="bi bi-sun"></i>' : '<i class="bi bi-moon"></i>';
        });
    }
})
