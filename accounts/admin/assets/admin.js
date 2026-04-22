/* ══════════════════════════════════
   ADMIN JS — admin.js
   ══════════════════════════════════ */

(function () {
    'use strict';

    const sidebar      = document.getElementById('sidebar');
    const sidebarToggle= document.getElementById('sidebarToggle');
    const overlay      = document.getElementById('sidebarOverlay');
    const mainWrapper  = document.getElementById('mainWrapper');

    // ── Sidebar toggle ──────────────
    function openSidebar() {
        sidebar.classList.add('open');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function () {
            if (sidebar.classList.contains('open')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });
    }

    if (overlay) {
        overlay.addEventListener('click', closeSidebar);
    }

    // Close sidebar on large screen resize
    window.addEventListener('resize', function () {
        if (window.innerWidth >= 992) {
            closeSidebar();
        }
    });

    // ── Active nav highlight ────────
    const currentPage = new URLSearchParams(window.location.search).get('page') || 'dashboard';
    document.querySelectorAll('.sidebar-nav .nav-link').forEach(function (link) {
        const href  = link.getAttribute('href') || '';
        const match = href.includes('page=' + currentPage);
        if (match) {
            link.classList.add('active');
        } else {
            link.classList.remove('active');
        }
    });

    // ── Tooltip init (Bootstrap) ────
    if (typeof bootstrap !== 'undefined') {
        document.querySelectorAll('[title]').forEach(function (el) {
            try {
                new bootstrap.Tooltip(el, { trigger: 'hover' });
            } catch (e) {
                console.warn('Tooltip init failed on:', el, e);
            }
        });
    }
})();
