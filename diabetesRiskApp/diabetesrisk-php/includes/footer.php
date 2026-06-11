    </main><!-- /.page-content -->
</div><!-- /.main-wrapper -->

<!-- App JS -->
<script src="/diabetesrisk-php/assets/js/main.js"></script>
<script>
    // Init feather icons
    feather.replace();

    // Live clock in topbar
    function updateClock() {
        const el = document.getElementById('currentTime');
        if (!el) return;
        const now = new Date();
        el.textContent = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
    }
    updateClock();
    setInterval(updateClock, 1000);

    // Sidebar toggle for mobile
    document.getElementById('sidebarToggle')?.addEventListener('click', () => {
        document.getElementById('sidebar').classList.toggle('open');
    });
</script>
</body>
</html>
