</div>
</main>

<!-- <footer class="text-center py-4">
    <p>&copy; 2025 IT Forum. All rights reserved.</p>
</footer> -->

<script>
    window.addEventListener('load', function () {
        var navbarHeight = document.querySelector('.top-nav').offsetHeight;
        document.querySelector('.sidebar').style.top = navbarHeight + 'px';
        document.querySelector('.sidebar').style.height = 'calc(100vh - ' + navbarHeight + 'px)';
    });
</script>


</body>

</html>