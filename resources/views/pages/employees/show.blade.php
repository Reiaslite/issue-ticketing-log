@extends('layouts.app')

@section('title', 'Employee Detail | Issue Ticketing Log')

@section('content')
	<div data-page="employee-detail" data-employee-id="{{ $employeeId ?? '' }}">
		<div class="content-header">
			<div>
				<p class="content-eyebrow mb-2">Employee Detail</p>
				<h1 class="content-title mb-2" data-employee-heading>Loading employee</h1>
				<p class="content-subtitle mb-0">
					Review account information and assigned role.
				</p>
			</div>
			<a class="btn btn-outline-secondary" href="{{ route('employees.index') }}">Back to employees</a>
		</div>

		<div class="state-message state-message-error d-none" data-employee-detail-error></div>

		<section class="detail-grid">
			<div class="panel-card">
				<div class="panel-card-header">
					<div>
						<h2 class="panel-title mb-1">Profile</h2>
						<p class="panel-subtitle mb-0" data-employee-username>--</p>
					</div>
					<span class="status-badge" data-employee-role-badge>--</span>
				</div>

				<dl class="detail-list">
					<div>
						<dt>Name</dt>
						<dd data-employee-name>--</dd>
					</div>
					<div>
						<dt>Email</dt>
						<dd data-employee-email>--</dd>
					</div>
					<div>
						<dt>Role</dt>
						<dd data-employee-role>--</dd>
					</div>
					<div>
						<dt>Created</dt>
						<dd data-employee-created>--</dd>
					</div>
					<div>
						<dt>Updated</dt>
						<dd data-employee-updated>--</dd>
					</div>
				</dl>
			</div>
		</section>
	</div>
@endsection
