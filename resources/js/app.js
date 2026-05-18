import 'bootstrap';

const layoutShell = document.querySelector('[data-layout-shell]');
const sidebarToggle = document.querySelector('[data-sidebar-toggle]');
const sidebarOverlay = document.querySelector('[data-sidebar-overlay]');
const desktopBreakpoint = window.matchMedia('(min-width: 992px)');

const syncSidebarState = () => {
    if (!layoutShell || !sidebarToggle) {
        return;
    }

    const isExpanded = desktopBreakpoint.matches
        ? !layoutShell.classList.contains('is-sidebar-collapsed')
        : layoutShell.classList.contains('is-sidebar-open');

    sidebarToggle.setAttribute('aria-expanded', String(isExpanded));
};

const closeMobileSidebar = () => {
    if (!layoutShell || desktopBreakpoint.matches) {
        return;
    }

    layoutShell.classList.remove('is-sidebar-open');
    syncSidebarState();
};

if (layoutShell && sidebarToggle) {
    sidebarToggle.addEventListener('click', () => {
        if (desktopBreakpoint.matches) {
            layoutShell.classList.toggle('is-sidebar-collapsed');
        } else {
            layoutShell.classList.toggle('is-sidebar-open');
        }

        syncSidebarState();
    });

    sidebarOverlay?.addEventListener('click', closeMobileSidebar);

    desktopBreakpoint.addEventListener('change', () => {
        layoutShell.classList.remove('is-sidebar-open');
        syncSidebarState();
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeMobileSidebar();
        }
    });

    syncSidebarState();
}
