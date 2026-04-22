
function applyTheme(theme) {
    if (theme === 'light') {
        document.documentElement.classList.add('light');
        document.documentElement.classList.remove('dark');
        document.body.classList.add('light');
        document.body.classList.remove('dark');
    } else {
        document.documentElement.classList.remove('light');
        document.documentElement.classList.add('dark');
        document.body.classList.remove('light');
        document.body.classList.add('dark');
    }
}


(function() {
    try {
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'light') {
            applyTheme('light');
        } else if (savedTheme === 'dark') {
            applyTheme('dark');
        } else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches) {
            applyTheme('light');
        } else {
            applyTheme('dark');
        }
    } catch(e) {
        console.warn('Theme error:', e);
        applyTheme('dark');
    }
})();


document.addEventListener('DOMContentLoaded', function () {
    const themeToggle = document.getElementById('themeToggle');
    if (!themeToggle) return;

    const isLight = document.body.classList.contains('light') || document.documentElement.classList.contains('light');
    themeToggle.innerHTML = isLight ? '<i class="bi bi-sun"></i>' : '<i class="bi bi-moon"></i>';

    themeToggle.addEventListener('click', function () {
        const isLightMode = document.body.classList.contains('light');

        if (isLightMode) {
            applyTheme('dark');
            localStorage.setItem('theme', 'dark');
            this.innerHTML = '<i class="bi bi-moon"></i>';
        } else {
            applyTheme('light');
            localStorage.setItem('theme', 'light');
            this.innerHTML = '<i class="bi bi-sun"></i>';
        }
    });
});
