@extends('layouts.app')

@section('title', 'Ticket Detail | Issue Ticketing Log')

@section('content')
    <div data-page="ticket-detail" data-ticket-id="{{ $ticketId }}">
        <div class="content-header">
            <div>
                <p class="content-eyebrow mb-2">Ticket Detail</p>
                <h1 class="content-title mb-2" data-ticket-heading>Loading ticket</h1>
                <p class="content-subtitle mb-0" data-ticket-description>
                    Loading detail and tracking history.
                </p>
            </div>
            <a class="btn btn-outline-secondary" href="{{ route('tickets.index') }}">Back to tickets</a>
        </div>

        <div class="state-message state-message-error d-none" data-ticket-detail-error></div>

        <section class="detail-grid">
            <div class="panel-card">
                <div class="panel-card-header">
                    <div>
                        <h2 class="panel-title mb-1">Issue</h2>
                        <p class="panel-subtitle mb-0" data-ticket-code>--</p>
                    </div>
                    <span class="status-badge" data-ticket-status>--</span>
                </div>

                <dl class="detail-list">
                    <div>
                        <dt>Severity</dt>
                        <dd data-ticket-severity>--</dd>
                    </div>
                    <div>
                        <dt>Priority</dt>
                        <dd data-ticket-priority>--</dd>
                    </div>
                    <div>
                        <dt>Requester</dt>
                        <dd data-ticket-user>--</dd>
                    </div>
                    <div>
                        <dt>Staff</dt>
                        <dd data-ticket-staff>--</dd>
                    </div>
                    <div>
                        <dt>Created</dt>
                        <dd data-ticket-created>--</dd>
                    </div>
                    <div>
                        <dt>Updated</dt>
                        <dd data-ticket-updated>--</dd>
                    </div>
                </dl>
            </div>

            <div class="panel-card">
                <div class="panel-card-header">
                    <div>
                        <h2 class="panel-title mb-1">Update Status</h2>
                        <p class="panel-subtitle mb-0">Create a status tracking record.</p>
                    </div>
                </div>

                <div class="state-message state-message-error d-none" data-status-error></div>
                <form class="ticket-form compact" data-status-form novalidate>
                    <div>
                        <label class="form-label" for="detail_status">Status</label>
                        <select class="form-select" id="detail_status" name="status" required>
                            <option value="assigned">Assigned</option>
                            <option value="in_progress">In progress</option>
                            <option value="pending">Pending</option>
                            <option value="solved">Solved</option>
                            <option value="done">Done</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                        <div class="field-error" data-error-for="status"></div>
                    </div>
                    <div>
                        <label class="form-label" for="status_note">Note</label>
                        <textarea class="form-control" id="status_note" name="note" rows="4" required></textarea>
                        <div class="field-error" data-error-for="note"></div>
                    </div>
                    <button class="btn btn-primary" type="submit" data-status-submit>Update status</button>
                </form>
            </div>
        </section>

        <section class="detail-grid mt-4">
            <div class="panel-card">
                <div class="panel-card-header">
                    <div>
                        <h2 class="panel-title mb-1">Tracking History</h2>
                        <p class="panel-subtitle mb-0">Chronological progress records.</p>
                    </div>
                </div>
                <div class="tracking-list" data-tracking-list>
                    <div class="state-message">Loading tracking history...</div>
                </div>
            </div>

            <div class="panel-card">
                <div class="panel-card-header">
                    <div>
                        <h2 class="panel-title mb-1">Add Tracking</h2>
                        <p class="panel-subtitle mb-0">Add progress detail and optionally assign staff.</p>
                    </div>
                </div>

                <div class="state-message state-message-error d-none" data-tracking-error></div>
                <form class="ticket-form compact" data-tracking-form novalidate>
                    <div>
                        <label class="form-label" for="tracking_status">Status</label>
                        <select class="form-select" id="tracking_status" name="status" required>
                            <option value="open">Open</option>
                            <option value="assigned">Assigned</option>
                            <option value="in_progress">In progress</option>
                            <option value="pending">Pending</option>
                            <option value="solved">Solved</option>
                            <option value="done">Done</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                        <div class="field-error" data-error-for="status"></div>
                    </div>
                    <div>
                        <label class="form-label" for="tracking_note">Note</label>
                        <textarea class="form-control" id="tracking_note" name="note" rows="4" required></textarea>
                        <div class="field-error" data-error-for="note"></div>
                    </div>
                    <div>
                        <label class="form-label" for="handled_by">Handled by</label>
                        <input class="form-control" id="handled_by" name="handled_by" type="text" placeholder="Optional staff UUID v7">
                        <div class="field-error" data-error-for="handled_by"></div>
                    </div>
                    <button class="btn btn-primary" type="submit" data-tracking-submit>Add tracking</button>
                </form>
            </div>
        </section>
    </div>
@endsection
