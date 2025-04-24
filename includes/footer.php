<footer class="text-center py-4">
    <p>&copy; 2025 IT Forum. All rights reserved.</p>
</footer>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const toggle = document.getElementById('darkModeToggle');
        const body = document.body;

        // Prüfe, ob Darkmode aktiviert ist
        if (localStorage.getItem('darkMode') === 'enabled') {
            body.classList.add('dark-mode');
        }

        // Toggle-Button klickbar machen
        if (toggle) {
            toggle.addEventListener('click', () => {
                body.classList.toggle('dark-mode');
                if (body.classList.contains('dark-mode')) {
                    localStorage.setItem('darkMode', 'enabled');
                } else {
                    localStorage.setItem('darkMode', 'disabled');
                }
            });
        }
    });
</script>


</body>

</html>