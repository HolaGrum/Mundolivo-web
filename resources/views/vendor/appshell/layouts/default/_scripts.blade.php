<script>
    document.addEventListener('DOMContentLoaded', function () {
        var mobileMenu = document.getElementById('mobile-menu');
        var overlay = document.getElementById('mobile-sidebar-overlay');
        var hamburger = document.getElementById('hamburger');
        var closeBtn = document.getElementById('mobile-menu-close');

        function openMobileMenu() {
            if (mobileMenu) mobileMenu.classList.add('show');
            if (overlay) overlay.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeMobileMenu() {
            if (mobileMenu) mobileMenu.classList.remove('show');
            if (overlay) overlay.classList.remove('show');
            document.body.style.overflow = '';
        }

        if (hamburger) {
            hamburger.addEventListener('click', function (e) {
                e.preventDefault();
                openMobileMenu();
            });
        }

        if (closeBtn) {
            closeBtn.addEventListener('click', function (e) {
                e.preventDefault();
                closeMobileMenu();
            });
        }

        if (overlay) {
            overlay.addEventListener('click', function () {
                closeMobileMenu();
            });
        }

        if (mobileMenu) {
            mobileMenu.querySelectorAll('.nav-link').forEach(function (link) {
                if (!link.classList.contains('nav-dropdown-toggle')) {
                    link.addEventListener('click', function () {
                        closeMobileMenu();
                    });
                }
            });
        }

        document.querySelectorAll('.nav-dropdown-items').forEach(function (el) {
            if (!el.closest('.nav-dropdown').classList.contains('open')) {
                el.style.maxHeight = '0';
                el.style.overflow = 'hidden';
            }
        });

        document.querySelectorAll('.nav-dropdown-toggle').forEach(function (toggle) {
            toggle.addEventListener('click', function (e) {
                e.preventDefault();
                var parent = this.closest('.nav-dropdown');
                var targetId = this.getAttribute('href');
                var target = targetId ? document.querySelector(targetId) : null;
                if (parent) {
                    var isOpen = parent.classList.contains('open');
                    parent.classList.toggle('open');
                    if (target) {
                        if (isOpen) {
                            target.style.maxHeight = '0';
                            target.style.overflow = 'hidden';
                        } else {
                            target.style.maxHeight = '1000px';
                            target.style.overflow = 'visible';
                        }
                    }
                    this.setAttribute('aria-expanded', !isOpen);
                }
            });
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        @stack('onload-scripts')
    });
</script>
