const html = document.documentElement;

// Apply saved theme on load
if (localStorage.getItem('darkMode') === 'enabled') {
    html.setAttribute('data-bs-theme', 'dark');
} else {
    html.setAttribute('data-bs-theme', 'light');
}

// Toggle theme on button click
document.querySelectorAll('#darkModeToggle').forEach(btn => {
    btn.addEventListener('click', () => {
        const currentTheme = html.getAttribute('data-bs-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        html.setAttribute('data-bs-theme', newTheme);

        localStorage.setItem('darkMode', newTheme === 'dark' ? 'enabled' : 'disabled');
    });
});

