@extends('layouts.app')

@section('title', 'Employees | Issue Ticketing Log')

@section('content')
  <div data-page="employees-index">
    <div class="content-header">
      <div>
        <p class="content-eyebrow mb-2">Staff Directory</p>
        <h1 class="content-title mb-2">Employees</h1>
        <p class="content-subtitle mb-0">
          Manage user accounts and roles across the service desk.
        </p>
      </div>
      <a class="btn btn-primary action-button" href="{{ route('employees.create') }}">
        <span aria-hidden="true">+</span>
        New Employee
      </a>
    </div>

    <section class="panel-card">
      <form class="filter-grid" data-employee-filters>
        <div>
          <label class="form-label" for="search">Search</label>
          <input class="form-control" id="search" name="search" type="search" placeholder="Name, username, or email">
        </div>
        <div>
          <label class="form-label" for="role">Role</label>
          <select class="form-select" id="role" name="role">
            <option value="">All roles</option>
            <option value="user">User</option>
            <option value="staff">Staff</option>
            <option value="superadmin">Superadmin</option>
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
          <h2 class="panel-title mb-1">Employee List</h2>
          <p class="panel-subtitle mb-0" data-employee-meta>Loading employees...</p>
        </div>
      </div>

      <div class="state-message state-message-error d-none" data-employee-list-error></div>
      <div class="table-responsive">
        <table class="table align-middle app-table mb-0">
          <thead>
            <tr>
              <th>Name</th>
              <th>Username</th>
              <th>Email</th>
              <th>Role</th>
              <th>Created</th>
              <th></th>
            </tr>
          </thead>
          <tbody data-employee-list></tbody>
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