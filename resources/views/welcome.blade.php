@extends('layouts.app')

@section('title', 'Dashboard | Issue Ticketing Log')

@section('content')
    <div class="content-header">
        <div>
            <p class="content-eyebrow mb-2">Issue Ticketing Log</p>
            <h1 class="content-title mb-2">Dashboard</h1>
            <p class="content-subtitle mb-0">
                Monitor ticket status, priority, and recent issue activities.
            </p>
        </div>
    </div>

    <section class="row g-3 g-xl-4" aria-label="Dashboard placeholder cards">
        <div class="col-12 col-md-6 col-xl-4">
            <x-dashboard.summary-card title="Open Tickets">
                Tickets waiting for assignment or review.
            </x-dashboard.summary-card>
        </div>

        <div class="col-12 col-md-6 col-xl-4">
            <x-dashboard.summary-card title="In Progress">
                Active issues currently being handled.
            </x-dashboard.summary-card>
        </div>

        <div class="col-12 col-md-6 col-xl-4">
            <x-dashboard.summary-card title="Resolved">
                Completed tickets ready for reporting.
            </x-dashboard.summary-card>
        </div>
    </section>

    <section class="panel-card mt-4">
        <div class="panel-card-header">
            <div>
                <h2 class="panel-title mb-1">Recent Tickets</h2>
                <p class="panel-subtitle mb-0">Static placeholder area for future ticket activity.</p>
            </div>
        </div>

        <div class="placeholder-stack">
            <div class="placeholder-item">
                <span class="placeholder-dot"></span>
                Ticket list preview will be rendered here.
            </div>
            <div class="placeholder-item">
                <span class="placeholder-dot"></span>
                Filters, priority labels, and status badges can be added later.
            </div>
            <div class="placeholder-item">
                <span class="placeholder-dot"></span>
                Real ticket records are not connected yet.
            </div>
        </div>
    </section>
@endsection
