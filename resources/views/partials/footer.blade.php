<footer class="footer-fixed">
    <p class="text-center text-sm mb-0">Copyright © 2025 Nursyahmina Mosdy</p>
</footer>

<style>
.footer-fixed {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background-color: #1e40af; /* bg-blue-800 */
    color: white;
    padding: 1rem;
    z-index: 1000;
    border-top: 1px solid #1e3a8a;
    transition: all 0.3s ease;
}

/* Adjust for sidebar when present - using the same width as admin sidebar */
.has-sidebar .footer-fixed {
    left: 16rem; /* w-64 = 16rem, matches sidebar width */
    width: calc(100% - 16rem);
}

/* Add bottom padding to main content to prevent overlap */
.has-sidebar {
    padding-bottom: 70px;
}

/* For pages without sidebar */
body:not(.has-sidebar) {
    padding-bottom: 70px;
}
</style>