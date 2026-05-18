@extends('layouts.app')

@section('title', 'Dashboard | Issue Ticketing Log')

@section('content')
    <div class="content-header">
        <div>
            <p class="content-eyebrow mb-2">Dashboard foundation</p>
            <h1 class="content-title mb-2">Issue Ticketing Log</h1>
            <p class="content-subtitle mb-0">
                A clean layout base for managing internal IT issues, ticket activity, and request tracking.
            </p>
        </div>
    </div>

    <section class="row g-3 g-xl-4 mb-4" aria-label="Dashboard summary placeholders">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="summary-card">
                <p class="summary-label">Open Tickets</p>
                <p class="summary-value">24</p>
                <span class="summary-note">Placeholder metric</span>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="summary-card">
                <p class="summary-label">In Progress</p>
                <p class="summary-value">12</p>
                <span class="summary-note">Placeholder metric</span>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="summary-card">
                <p class="summary-label">Resolved Today</p>
                <p class="summary-value">8</p>
                <span class="summary-note">Placeholder metric</span>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="summary-card">
                <p class="summary-label">SLA Watch</p>
                <p class="summary-value">3</p>
                <span class="summary-note">Placeholder metric</span>
            </div>
        </div>
    </section>

    <section class="row g-3 g-xl-4">
        <div class="col-12 col-xl-8">
            <div class="panel-card">
                <div class="panel-card-header">
                    <div>
                        <h2 class="panel-title mb-1">Recent Ticket Activity</h2>
                        <p class="panel-subtitle mb-0">Static sample content for layout demonstration.</p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col">Ticket</th>
                                <th scope="col">Requester</th>
                                <th scope="col">Priority</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Network access review</td>
                                <td>Finance Team</td>
                                <td><span class="badge text-bg-warning">Medium</span></td>
                                <td><span class="badge text-bg-primary">In Progress</span></td>
                            </tr>
                            <tr>
                                <td>Laptop replacement request</td>
                                <td>Operations</td>
                                <td><span class="badge text-bg-danger">High</span></td>
                                <td><span class="badge text-bg-secondary">Queued</span></td>
                            </tr>
                            <tr>
                                <td>Email setup assistance</td>
                                <td>New Employee</td>
                                <td><span class="badge text-bg-success">Low</span></td>
                                <td><span class="badge text-bg-success">Resolved</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="panel-card h-100">
                <h2 class="panel-title mb-3">Layout Notes</h2>
                <div class="placeholder-stack">
                    <div class="placeholder-item">
                        <span class="placeholder-dot"></span>
                        Sidebar supports desktop collapse and mobile slide-in.
                    </div>
                    <div class="placeholder-item">
                        <span class="placeholder-dot"></span>
                        Main content expands automatically with the sidebar state.
                    </div>
                    <div class="placeholder-item">
                        <span class="placeholder-dot"></span>
                        Cards and panels are ready for future ticket modules.
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
