<script src="/js/jquery-4.0.0.min.js"></script>
<script>
  // Sidebar toggle functionality
  (() => {
    const sidebar = document.getElementById('sidebar');
    const toggle = document.getElementById('sidebar-toggle');
    const logo = document.getElementById('logo');

    const updateLogo = (isCollapsed) => {
      if (logo) {
        logo.src = isCollapsed ? '/images/isologo_consorcio.png' : '/images/logo_consorcio.png';
      }
    };

    if (toggle && sidebar) {
      toggle.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
        const isCollapsed = sidebar.classList.contains('collapsed');
        localStorage.setItem('sidebar-collapsed', isCollapsed);
        updateLogo(isCollapsed);
      });

      // Restore sidebar state from localStorage
      const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
      if (isCollapsed) {
        sidebar.classList.add('collapsed');
      }
      updateLogo(isCollapsed);
    }
  })();
</script>
@yield('extra_scripts')
