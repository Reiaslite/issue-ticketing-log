<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Issue Ticketing Log</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-page">
    <main class="auth-shell" data-page="login">
        <section class="auth-panel" aria-labelledby="login-title">
            <div class="auth-brand">
                <span class="sidebar-brand-mark">IT</span>
                <div>
                    <p class="content-eyebrow mb-1">Issue Ticketing Log</p>
                    <h1 class="auth-title mb-0" id="login-title">Sign in</h1>
                </div>
            </div>

            <p class="auth-copy">
                Access the internal service desk queue with your assigned username and password.
            </p>

            <div class="state-message state-message-error d-none" data-login-error></div>

            <form class="auth-form" data-login-form novalidate>
                <div>
                    <label class="form-label" for="username">Username</label>
                    <input class="form-control form-control-lg" id="username" name="username" type="text" autocomplete="username" required autofocus>
                    <div class="field-error" data-error-for="username"></div>
                </div>

                <div>
                    <label class="form-label" for="password">Password</label>
                    <input class="form-control form-control-lg" id="password" name="password" type="password" autocomplete="current-password" required>
                    <div class="field-error" data-error-for="password"></div>
                </div>

                <button class="btn btn-primary btn-lg auth-submit" type="submit" data-login-submit>
                    Sign in
                </button>
            </form>
        </section>
    </main>
</body>
</html>
