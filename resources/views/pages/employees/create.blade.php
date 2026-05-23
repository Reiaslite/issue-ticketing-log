@extends('layouts.app')

@section('title', 'Create Employee | Issue Ticketing Log')

@section('content')
	<div data-page="employee-create">
		<div class="content-header">
			<div>
				<p class="content-eyebrow mb-2">Employee Access</p>
				<h1 class="content-title mb-2">Create Employee</h1>
				<p class="content-subtitle mb-0">
					Add a new employee profile and assign the appropriate role.
				</p>
			</div>
		</div>

		<section class="panel-card form-panel">
			<div class="state-message state-message-error d-none" data-employee-form-error></div>
			<div class="state-message state-message-success d-none" data-employee-form-success></div>

			<form class="ticket-form" data-employee-create-form novalidate>
				<div class="form-grid-2">
					<div>
						<label class="form-label" for="name">Full name</label>
						<input class="form-control" id="name" name="name" type="text" minlength="2" required>
						<div class="field-error" data-error-for="name"></div>
					</div>
					<div>
						<label class="form-label" for="username">Username</label>
						<input class="form-control" id="username" name="username" type="text" minlength="3" required>
						<div class="field-error" data-error-for="username"></div>
					</div>
				</div>

				<div class="form-grid-2">
					<div>
						<label class="form-label" for="email">Email</label>
						<input class="form-control" id="email" name="email" type="email" placeholder="Optional">
						<div class="field-error" data-error-for="email"></div>
					</div>
					<div>
						<label class="form-label" for="role">Role</label>
						<select class="form-select" id="role" name="role" required>
							<option value="">Select role</option>
							<option value="user">User</option>
							<option value="staff">Staff</option>
							<option value="superadmin">Superadmin</option>
						</select>
						<div class="field-error" data-error-for="role"></div>
					</div>
				</div>

				<div>
					<label class="form-label" for="password">Password</label>
					<input class="form-control" id="password" name="password" type="password" minlength="8" required>
					<div class="field-error" data-error-for="password"></div>
				</div>

				<div class="form-actions">
					<a class="btn btn-light" href="{{ route('employees.index') }}">Cancel</a>
					<button class="btn btn-primary" type="submit" data-employee-create-submit>Create employee</button>
				</div>
			</form>
		</section>
	</div>
@endsection
