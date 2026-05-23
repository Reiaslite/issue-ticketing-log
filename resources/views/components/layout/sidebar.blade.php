<aside class="app-sidebar" id="app-sidebar" data-sidebar>
    <div class="sidebar-header">
        <span class="sidebar-brand-mark">IT</span>
        <div>
            <p class="sidebar-eyebrow mb-0">Workspace</p>
            <h2 class="sidebar-title mb-0">Service Desk</h2>
        </div>
    </div>

    <nav class="sidebar-nav" aria-label="Primary navigation">
        <a class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}" @if (request()->routeIs('dashboard')) aria-current="page" @endif>
            <span class="sidebar-link-icon" aria-hidden="true"></span>
            <span>Overview</span>
        </a>
        <a class="sidebar-link {{ request()->routeIs('tickets.index') || request()->routeIs('tickets.show') ? 'active' : '' }}" href="{{ route('tickets.index') }}" @if (request()->routeIs('tickets.index') || request()->routeIs('tickets.show')) aria-current="page" @endif>
            <span class="sidebar-link-icon" aria-hidden="true"></span>
            <span>Tickets</span>
        </a>
        <a class="sidebar-link {{ request()->routeIs('tickets.create') ? 'active' : '' }}" href="{{ route('tickets.create') }}" @if (request()->routeIs('tickets.create')) aria-current="page" @endif>
            <span class="sidebar-link-icon" aria-hidden="true"></span>
            <span>Submit Request</span>
        </a>
        <a class="sidebar-link {{ request()->routeIs('tickets.show') ? 'active' : '' }}" href="{{ route('tickets.index') }}">
            <span class="sidebar-link-icon" aria-hidden="true"></span>
            <span>Tracking History</span>
        </a>
        <a class="sidebar-link {{ request()->routeIs('employees.index') || request()->routeIs('employees.show') ? 'active' : '' }}" href="{{ route('employees.index') }}" @if (request()->routeIs('employees.index') || request()->routeIs('employees.show')) aria-current="page" @endif>
            <span class="sidebar-link-icon" aria-hidden="true"></span>
            <span>Employees List</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <p class="mb-1 fw-semibold">Service flow</p>
        <p class="sidebar-footer-text mb-0">Create, assign, track, solve, and confirm tickets from one queue.</p>
    </div>
</aside>
