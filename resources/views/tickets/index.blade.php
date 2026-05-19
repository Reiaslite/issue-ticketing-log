@extends('layouts.app')

@section('title', 'Tickets | Issue Ticketing Log')

@section('content')
    <div data-page="tickets-index">
        <div class="content-header">
            <div>
                <p class="content-eyebrow mb-2">Ticket Queue</p>
                <h1 class="content-title mb-2">Tickets</h1>
                <p class="content-subtitle mb-0">
                    Search, filter, and review issue tickets across the support workflow.
                </p>
            </div>
            <a class="btn btn-primary action-button" href="{{ route('tickets.create') }}">
                <span aria-hidden="true">+</span>
                New Ticket
            </a>
        </div>

        <section class="panel-card">
            <form class="filter-grid" data-ticket-filters>
                <div>
                    <label class="form-label" for="search">Search</label>
                    <input class="form-control" id="search" name="search" type="search" placeholder="Ticket code or issue">
                </div>
                <div>
                    <label class="form-label" for="status">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All status</option>
                        <option value="open">Open</option>
                        <option value="assigned">Assigned</option>
                        <option value="in_progress">In progress</option>
                        <option value="pending">Pending</option>
                        <option value="solved">Solved</option>
                        <option value="done">Done</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div>
                    <label class="form-label" for="priority_level">Priority</label>
                    <select class="form-select" id="priority_level" name="priority_level">
                        <option value="">All priority</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                    </select>
                </div>
                <div>
                    <label class="form-label" for="severity_level">Severity</label>
                    <select class="form-select" id="severity_level" name="severity_level">
                        <option value="">All severity</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                        <option value="critical">Critical</option>
                    </select>
                </div>
                <div class="filter-actions">
                    <button class="btn btn-primary" type="submit">Apply</button>
                    <button class="btn btn-light" type="button" data-reset-filters>Reset</button>
                </div>
            </form>
        </section>

        <section class="panel-card mt-4">
            <div class="panel-card-header">
                <div>
                    <h2 class="panel-title mb-1">Ticket List</h2>
                    <p class="panel-subtitle mb-0" data-ticket-meta>Loading tickets...</p>
                </div>
            </div>

            <div class="state-message state-message-error d-none" data-ticket-list-error></div>
            <div class="table-responsive">
                <table class="table align-middle app-table mb-0">
                    <thead>
                        <tr>
                            <th>Ticket</th>
                            <th>Issue</th>
                            <th>Owner</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Created</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody data-ticket-list></tbody>
                </table>
            </div>

            <div class="pagination-bar">
                <button class="btn btn-light btn-sm" type="button" data-page-prev>Previous</button>
                <span class="pagination-copy" data-page-copy>Page 1</span>
                <button class="btn btn-light btn-sm" type="button" data-page-next>Next</button>
            </div>
        </section>
    </div>
@endsection
