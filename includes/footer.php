</div>
<div class="footer">
    <footer class="text-center py-4">
        <p>&copy; 2025 IT Forum. All rights reserved.</p>
        <p>
            <a href="impressum.php">Impressum</a> |
            <a href="datenschutz.php">Datenschutzerklärung</a> |
            <a href="nutzungsbedingungen.php">Nutzungsbedingungen</a>
        </p>
        <!-- <address>
        Kontakt: Max Mustermann<br>
        Telefon: +49 123 456789<br>
        E-Mail: <a href="mailto:kontakt@itforum.de">kontakt@itforum.de</a>
    </address> -->

    </footer>
</div>
</main>


</body>

<script>
    window.addEventListener('load', function () {
        var navbarHeight = document.querySelector('.top-nav').offsetHeight;
        document.querySelector('.sidebar').style.top = navbarHeight + 'px';
        document.querySelector('.sidebar').style.height = 'calc(100vh - ' + navbarHeight + 'px)';
    });
</script>

</html>