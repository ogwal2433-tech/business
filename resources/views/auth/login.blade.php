<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ __('Login') }} - SmartBiz</title>
    <link rel="stylesheet" href="styles.css" />
    <style>
        /* Reset and core layout */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #cfe8ff;
            background: linear-gradient(to top right, #cfe8ff, #ffffff, #cbdff7);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            -webkit-text-size-adjust: 100%;
        }

        .login-wrapper {
            width: 100%;
            max-width: 400px;
            margin: auto;
        }

        .login-container {
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            padding: 2.5rem;
            width: 100%;
            animation: fadeInDown 0.5s ease-out;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h2 {
            color: #1e40af;
            font-size: 1.75rem;
            font-weight: 800;
            text-align: center;
            margin-bottom: 0.5rem;
        }

        .subtitle {
            color: #6b7280;
            font-size: 0.9rem;
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.2rem;
            position: relative;
        }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.4rem;
            color: #374151;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            transition: border-color 0.3s, box-shadow 0.3s;
            font-size: 16px;
        }

        input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
        }

        .toggle-password {
            position: absolute;
            top: 38px;
            right: 15px;
            cursor: pointer;
            user-select: none;
            font-size: 1rem;
            color: #6b7280;
        }

        .error-message {
            color: #dc2626;
            font-size: 0.85rem;
            margin-top: 0.3rem;
        }

        .alert {
            background-color: #fee2e2;
            color: #b91c1c;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            margin-bottom: 1.25rem;
            font-size: 0.9rem;
            border: 1px solid #fca5a5;
        }

        button {
            width: 100%;
            background-color: #2563eb;
            color: white;
            padding: 0.9rem;
            font-weight: 600;
            font-size: 1rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s, box-shadow 0.3s;
        }

        button:hover {
            background-color: #1d4ed8;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .register-info {
            margin-top: 1.5rem;
            font-size: 0.85rem;
            color: #6b7280;
            text-align: center;
        }

        .register-info a {
            color: #2563eb;
            text-decoration: none;
            font-weight: 500;
        }

        .register-info a:hover {
            text-decoration: underline;
        }

        .back-to-home {
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .back-to-home a {
            color: #6b7280;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            transition: color 0.3s;
        }

        .back-to-home a:hover {
            color: #2563eb;
        }

        /* Footer styles */
        footer {
            width: 100%;
            text-align: center;
            padding: 1.5rem 1rem 1rem;
            font-size: 0.85rem;
            color: #6b7280;
        }

        @media (max-width: 480px) {
            body { padding: 1rem; }
            .login-container { padding: 1.5rem; }
            h2 { font-size: 1.35rem; }
            .subtitle { font-size: 0.8rem; margin-bottom: 1rem; }
        }

        @media (max-width: 360px) {
            body { padding: 0.5rem; }
            .login-container { padding: 1rem; border-radius: 12px; }
            h2 { font-size: 1.15rem; }
            .subtitle { font-size: 0.75rem; }
            input[type="text"],
            input[type="password"] { font-size: 16px; padding: 0.6rem 0.7rem; }
            button { padding: 0.7rem; font-size: 0.85rem; }
            .toggle-password { top: 32px; right: 12px; }
            footer { padding: 1rem 0.5rem 0.5rem; font-size: 0.75rem; }
        }
    </style>
</head>
<body>

    <div class="login-wrapper">
        <div class="login-container">
        <div class="back-to-home">
            <a href="/">← {{ __('Back to Home') }}</a>
        </div>

        <h2>{{ __('Welcome Back to') }} <span style="color: #3b82f6;">SmartBiz</span></h2>
        <p class="subtitle">{{ __('Please enter your credentials to continue') }}</p>

        <!-- Laravel session error (optional) -->
        @if(session('error'))
            <div class="alert">
                {{ session('error') }}
            </div>
        @endif
@if(session('message'))
    <div class="alert alert-success">
        {{ session('message') }}
    </div>
@endif
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.alert, .alert-success').forEach(function(el) {
            setTimeout(function() {
                el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                el.style.opacity = '0';
                el.style.transform = 'translateY(-10px)';
                setTimeout(function() { el.remove(); }, 500);
            }, 3000);
        });
    });
</script>


        <form method="POST" action="{{ route('login.submit') }}">
            @csrf

            <!-- Username -->
            <div class="form-group">
                <label for="username">{{ __('Username') }}</label>
                <input
                    type="text"
                    name="username"
                    id="username"
                    required
                    autofocus
                    value="{{ old('username') }}"
                    placeholder="{{ __('Enter your username') }}"
                />
                @error('username')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password with Eye Toggle -->
            <div class="form-group">
                <label for="password">{{ __('Password') }}</label>
                <input
                    type="password"
                    name="password"
                    id="password"
                    required
                    placeholder="{{ __('Enter your password') }}"
                />
                <span class="toggle-password" onclick="togglePassword()">👁️</span>
                @error('password')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit">{{ __('Login') }}</button>
        </form>

        <!-- Register link -->
        @auth
            @if(auth()->user()->role === 'admin')
                <div class="register-info">
                    {{ __('Need a new account?') }}
                    <a href="{{ route('register') }}">{{ __('Register') }}</a>
                </div>
            @endif
        @else
            <div class="register-info">
                {{ __("Don't have an account?") }} <a href="/register">{{ __('Sign up') }}</a>
            </div>
        @endauth
    </div>

    <footer>
        &copy; 2025 SmartBiz Admin. {{ __('All rights reserved.') }}
    </footer>
    </div>

    <!-- JavaScript for password toggle -->
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById("password");
            const toggleIcon = document.querySelector(".toggle-password");
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                toggleIcon.textContent = "🙈";
            } else {
                passwordInput.type = "password";
                toggleIcon.textContent = "👁️";
            }
        }

        // Keep session alive every 5 minutes
        setInterval(() => {
            fetch("/keep-alive", {
                method: "GET",
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
        }, 5 * 60 * 1000);
    </script>

</body>
</html>
