@extends('layouts.app')

@section('title', 'Dashboard | Issue Ticketing Log')

@section('content')
    <div data-page="dashboard">
        <div class="content-header">
            <div>
                <p class="content-eyebrow mb-2">Issue Ticketing Log</p>
                <h1 class="content-title mb-2">Dashboard</h1>
                <p class="content-subtitle mb-0">
                    Monitor ticket volume, active work, and the latest issue activity.
                </p>
            </div>
            <a class="btn btn-primary action-button" href="{{ route('tickets.create') }}">
                <span aria-hidden="true">+</span>
                New Ticket
            </a>
        </div>

        <section class="row g-3 g-xl-4" aria-label="Ticket summary">
            <div class="col-12 col-md-6 col-xl-3">
                <x-dashboard.summary-card title="Open" value="--">
                    Tickets waiting for assignment.
                </x-dashboard.summary-card>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
                <x-dashboard.summary-card title="In Progress" value="--">
                    Active issues currently being handled.
                </x-dashboard.summary-card>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
                <x-dashboard.summary-card title="Solved" value="--">
                    Resolved tickets awaiting confirmation.
                </x-dashboard.summary-card>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
                <x-dashboard.summary-card title="Done" value="--">
                    Confirmed completed tickets.
                </x-dashboard.summary-card>
            </div>
        </section>

        <section class="panel-card mt-4">
            <div class="panel-card-header">
                <div>
                    <h2 class="panel-title mb-1">Recent Tickets</h2>
                    <p class="panel-subtitle mb-0">Latest records from the backend ticket queue.</p>
                </div>
                <a class="btn btn-outline-primary btn-sm" href="{{ route('tickets.index') }}">View all</a>
            </div>

            <div class="state-message d-none" data-dashboard-error></div>
            <div class="table-responsive">
                <table class="table align-middle app-table mb-0">
                    <thead>
                        <tr>
                            <th>Ticket</th>
                            <th>Issue</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody data-dashboard-recent>
                        <tr>
                            <td colspan="5">
                                <div class="state-message">Loading recent tickets...</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
