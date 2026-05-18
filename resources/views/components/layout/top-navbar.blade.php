<nav class="top-navbar navbar navbar-expand bg-white border-bottom">
    <div class="container-fluid px-3 px-md-4">
        <button
            class="btn btn-outline-primary navbar-toggle"
            type="button"
            data-sidebar-toggle
            aria-label="Toggle navigation menu"
            aria-controls="app-sidebar"
            aria-expanded="true"
        >
            <span class="navbar-toggle-line"></span>
            <span class="navbar-toggle-line"></span>
            <span class="navbar-toggle-line"></span>
        </button>

        <a class="navbar-brand ms-3 fw-semibold" href="{{ url('/') }}">
            Issue Ticketing Log
        </a>

        <div class="ms-auto d-flex align-items-center gap-3">
            <span class="d-none d-sm-inline text-muted small">Internal IT Portal</span>
            <button class="btn btn-light profile-button" type="button" aria-label="User profile">
                <span class="profile-avatar" aria-hidden="true">IT</span>
                <span class="d-none d-md-inline">Support Admin</span>
            </button>
        </div>
    </div>
</nav>
