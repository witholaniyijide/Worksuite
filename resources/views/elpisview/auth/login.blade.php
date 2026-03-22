<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login &mdash; Elpis View Educational Services</title>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{ asset('vendor/css/all.min.css') }}">

    <!-- Template CSS -->
    <link type="text/css" rel="stylesheet" media="all" href="{{ asset('css/main.css') }}">

    <style>
        :root {
            --ev-primary: #4e73df;
            --ev-primary-dark: #3a5bc7;
            --ev-bg: #f0f2f5;
            --ev-card: #ffffff;
            --ev-text: #344767;
            --ev-text-light: #7b809a;
            --ev-border: #e9ecef;
            --ev-danger: #e74a3b;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: var(--ev-bg);
            color: var(--ev-text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .ev-login-header {
            background: var(--ev-card);
            padding: 14px 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 1px solid var(--ev-border);
            box-shadow: 0 1px 3px rgba(0,0,0,.04);
        }

        .ev-login-header .ev-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: var(--ev-text);
        }

        .ev-login-header .ev-brand-icon {
            width: 36px;
            height: 36px;
            background: var(--ev-primary);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 18px;
        }

        .ev-login-header .ev-brand-name {
            font-size: 18px;
            font-weight: 600;
        }

        .ev-login-section {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 16px;
        }

        .ev-login-card {
            background: var(--ev-card);
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,.06);
            padding: 40px 36px;
            width: 100%;
            max-width: 420px;
        }

        .ev-login-card h3 {
            font-size: 22px;
            font-weight: 600;
            margin: 0 0 6px;
            text-align: center;
        }

        .ev-login-card .ev-subtitle {
            color: var(--ev-text-light);
            text-align: center;
            margin-bottom: 28px;
            font-size: 14px;
        }

        .ev-form-group {
            margin-bottom: 20px;
        }

        .ev-form-group label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 6px;
            color: var(--ev-text);
        }

        .ev-form-control {
            width: 100%;
            height: 46px;
            padding: 0 14px;
            font-size: 14px;
            border: 1px solid var(--ev-border);
            border-radius: 8px;
            background: #fafbfc;
            color: var(--ev-text);
            transition: border-color .2s, box-shadow .2s;
            outline: none;
        }

        .ev-form-control:focus {
            border-color: var(--ev-primary);
            box-shadow: 0 0 0 3px rgba(78, 115, 223, .15);
            background: #fff;
        }

        .ev-form-control.is-invalid {
            border-color: var(--ev-danger);
        }

        .ev-invalid-feedback {
            color: var(--ev-danger);
            font-size: 12px;
            margin-top: 4px;
        }

        .ev-password-wrapper {
            position: relative;
        }

        .ev-password-wrapper .ev-form-control {
            padding-right: 44px;
        }

        .ev-toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--ev-text-light);
            cursor: pointer;
            padding: 4px;
            font-size: 14px;
        }

        .ev-toggle-password:hover {
            color: var(--ev-text);
        }

        .ev-form-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .ev-remember {
            display: flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            font-size: 13px;
            color: var(--ev-text-light);
        }

        .ev-remember input {
            cursor: pointer;
        }

        .ev-forgot-link {
            font-size: 13px;
            color: var(--ev-primary);
            text-decoration: none;
        }

        .ev-forgot-link:hover {
            text-decoration: underline;
        }

        .ev-btn-login {
            width: 100%;
            height: 46px;
            background: var(--ev-primary);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background .2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .ev-btn-login:hover {
            background: var(--ev-primary-dark);
        }

        .ev-btn-login:disabled {
            opacity: .65;
            cursor: not-allowed;
        }

        .ev-login-footer {
            text-align: center;
            padding: 16px;
            color: var(--ev-text-light);
            font-size: 12px;
        }

        @media (max-width: 480px) {
            .ev-login-card {
                padding: 28px 20px;
            }
        }
    </style>
</head>

<body>

<header class="ev-login-header">
    <span class="ev-brand">
        <span class="ev-brand-icon"><i class="fa fa-graduation-cap"></i></span>
        <span class="ev-brand-name">Elpis View</span>
    </span>
</header>

<section class="ev-login-section">
    <div class="ev-login-card">
        <h3>Welcome Back</h3>
        <p class="ev-subtitle">Sign in to your Elpis View portal</p>

        <form method="POST" action="{{ route('elpisview.login.submit') }}" id="ev-login-form">
            @csrf

            <div class="ev-form-group">
                <label for="email">Email Address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="ev-form-control @error('email') is-invalid @enderror"
                    value="{{ old('email') }}"
                    placeholder="you@example.com"
                    autofocus
                    required
                >
                @error('email')
                    <div class="ev-invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="ev-form-group">
                <label for="password">Password</label>
                <div class="ev-password-wrapper">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="ev-form-control @error('password') is-invalid @enderror"
                        placeholder="Enter your password"
                        required
                    >
                    <button type="button" class="ev-toggle-password" title="Show password">
                        <i class="fa fa-eye"></i>
                    </button>
                </div>
                @error('password')
                    <div class="ev-invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="ev-form-row">
                <label class="ev-remember">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    Remember me
                </label>
                <a href="{{ url('forgot-password') }}" class="ev-forgot-link">Forgot password?</a>
            </div>

            <button type="submit" class="ev-btn-login" id="ev-submit-btn">
                Sign In <i class="fa fa-arrow-right"></i>
            </button>
        </form>
    </div>
</section>

<footer class="ev-login-footer">
    &copy; {{ date('Y') }} Elpis View Educational Services. All rights reserved.
</footer>

<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script>
    $(document).ready(function () {
        // Toggle password visibility
        $('.ev-toggle-password').on('click', function () {
            const input = $(this).siblings('input');
            const icon = $(this).find('i');
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                input.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        // Disable button on submit to prevent double-submit
        $('#ev-login-form').on('submit', function () {
            var btn = $('#ev-submit-btn');
            btn.prop('disabled', true);
            btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Signing in...');
        });
    });
</script>

</body>
</html>
