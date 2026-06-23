<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Create Account') }} — SmartBiz | {{ __('Inventory') }} & {{ __('Sales') }} {{ __('Management System') }}</title>

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('bus.png') }}">
    <link rel="shortcut icon" href="{{ asset('bus.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background: linear-gradient(to bottom right, #cfe8ff, #ffffff, #cbdff7);
            min-height: 100vh;
            color: #1e293b;
            -webkit-font-smoothing: antialiased;
        }

        nav {
            max-width: 1280px;
            margin: 0 auto;
            padding: 24px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            font-size: 1.25rem;
            color: #0f172a;
            text-decoration: none;
        }

        .logo img { width: 32px; height: 32px; }

        .nav-links a {
            padding: 8px 18px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.9rem;
            text-decoration: none;
            transition: all 0.2s;
            color: #64748b;
        }

        .nav-links a:hover { color: #2563eb; background: rgba(255,255,255,0.7); }

        .main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 32px 60px;
        }

        .register-card {
            width: 100%;
            max-width: 520px;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.06);
            padding: 40px;
            animation: fadeInDown 0.5s ease-out;
        }

        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .register-card h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 4px;
        }

        .register-card .subtitle {
            font-size: 0.9rem;
            color: #64748b;
            margin-bottom: 28px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .form-group { margin-bottom: 18px; position: relative; }

        label {
            display: block;
            font-weight: 500;
            font-size: 0.875rem;
            margin-bottom: 6px;
            color: #374151;
        }

        .input-wrapper { position: relative; }

        input {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-family: 'Inter', sans-serif;
            font-size: 16px;
            transition: border-color 0.2s, box-shadow 0.2s;
            background: #ffffff;
            color: #1e293b;
        }

        input::placeholder { color: #94a3b8; }

        input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
        }

        .input-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            cursor: pointer;
            transition: color 0.2s;
            z-index: 2;
        }

        .input-icon:hover { color: #2563eb; }

        .password-strength {
            margin-top: 8px;
            height: 3px;
            background: #f1f5f9;
            border-radius: 2px;
            overflow: hidden;
        }

        .password-strength-bar {
            height: 100%;
            width: 0;
            transition: all 0.3s;
            border-radius: 2px;
        }

        .strength-0 { width: 0; }
        .strength-1 { width: 25%; background: #ef4444; }
        .strength-2 { width: 50%; background: #f59e0b; }
        .strength-3 { width: 75%; background: #2563eb; }
        .strength-4 { width: 100%; background: #10b981; }

        .password-requirements {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
            font-size: 0.78rem;
        }

        .requirement {
            display: flex;
            align-items: center;
            gap: 4px;
            color: #94a3b8;
            padding: 3px 10px;
            background: #f8fafc;
            border-radius: 20px;
            transition: all 0.2s;
            font-size: 0.75rem;
        }

        .requirement.met { background: #d1fae5; color: #065f46; }
        .requirement i { font-size: 0.65rem; }

        .password-match-indicator {
            margin-top: 6px;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            gap: 6px;
            opacity: 0;
            transform: translateY(-8px);
            transition: all 0.3s;
        }

        .password-match-indicator.show { opacity: 1; transform: translateY(0); }
        .password-match-indicator.match { background: #d1fae5; color: #065f46; }
        .password-match-indicator.no-match { background: #fef2f2; color: #991b1b; }

        .error-message {
            color: #dc2626;
            font-size: 0.8rem;
            margin-top: 4px;
        }

        .alert {
            background: #fef2f2;
            color: #991b1b;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.85rem;
            border: 1px solid #fecaca;
        }

        .submit-btn {
            width: 100%;
            background: #2563eb;
            color: white;
            padding: 12px;
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            font-size: 0.9rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.2s, box-shadow 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 4px;
        }

        .submit-btn:hover:not(:disabled) {
            background: #1d4ed8;
            box-shadow: 0 4px 12px rgba(37,99,235,0.2);
        }

        .submit-btn:disabled { opacity: 0.6; cursor: not-allowed; }

        .footer-links {
            margin-top: 20px;
            text-align: center;
            font-size: 0.85rem;
            color: #94a3b8;
        }

        .footer-links a {
            color: #2563eb;
            text-decoration: none;
            font-weight: 500;
        }

        .footer-links a:hover { text-decoration: underline; }

        .spinner { animation: spin 1s linear infinite; }
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

        footer {
            width: 100%;
            text-align: center;
            padding: 1.5rem 1rem;
            font-size: 0.85rem;
            color: #64748b;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-top: 1px solid rgba(0, 0, 0, 0.08);
        }

        @media (max-width: 480px) {
            .register-card { padding: 24px 16px; }
        }

        @media (max-width: 360px) {
            .register-card { padding: 16px 12px; border-radius: 12px; }
            .register-card h1 { font-size: 1.2rem; }
            .register-card .subtitle { font-size: 0.8rem; margin-bottom: 20px; }
            input { font-size: 16px; padding: 8px 10px; }
            .submit-btn { padding: 10px; font-size: 0.85rem; }
            .password-requirements { gap: 4px; }
            .requirement { font-size: 0.7rem; padding: 2px 8px; }
            footer { padding: 1rem 0.5rem; font-size: 0.75rem; }
        }

        @media (max-width: 640px) {
            .register-card { padding: 32px 24px; }
            .form-row { grid-template-columns: 1fr; }
            .main { padding: 20px 16px 60px; }
            nav { padding: 16px 20px; }
            .password-requirements { flex-direction: column; }
        }
    </style>
</head>
<body>
    <nav>
        <a href="/" class="logo">
            <img src="{{ asset('bus.png') }}" alt="SmartBiz">
            SmartBiz
        </a>
        <div class="nav-links">
            <a href="/">{{ __('Home') }}</a>
            <a href="{{ route('login') }}">{{ __('Sign In') }}</a>
        </div>
    </nav>

    <div class="main">
        <div class="register-card">
            <h1>{{ __('Create your account') }}</h1>
            <p class="subtitle">{{ __('Start managing your business better with SmartBiz.') }}</p>

            @if($errors->any())
                <div class="alert">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.store') }}" id="registerForm">
                @csrf

                <div class="form-group">
                    <label for="business_name">{{ __('Business Name') }}</label>
                    <div class="input-wrapper">
                        <input type="text" name="business_name" id="business_name" required placeholder="{{ __('ABC Enterprises Ltd.') }}" value="{{ old('business_name') }}">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="name">{{ __('Full Name') }}</label>
                        <div class="input-wrapper">
                            <input type="text" name="name" id="name" required placeholder="{{ __('John Doe') }}" value="{{ old('name') }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="username">{{ __('Username') }}</label>
                        <div class="input-wrapper">
                            <input type="text" name="username" id="username" required placeholder="johndoe" value="{{ old('username') }}">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">{{ __('Email Address') }}</label>
                    <div class="input-wrapper">
                        <input type="email" name="email" id="email" required placeholder="john@example.com" value="{{ old('email') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">{{ __('Password') }}</label>
                    <div class="input-wrapper">
                        <input type="password" name="password" id="password" required placeholder="{{ __('Create a strong password') }}">
                        <i class="fas fa-eye input-icon" id="togglePassword" onclick="togglePassword('password', this)"></i>
                    </div>
                    <div class="password-strength">
                        <div class="password-strength-bar" id="passwordStrength"></div>
                    </div>
                    <div class="password-requirements" id="passwordRequirements">
                        <span class="requirement" id="req-length"><i class="far fa-circle"></i> {{ __('6+ chars') }}</span>
                        <span class="requirement" id="req-lower"><i class="far fa-circle"></i> {{ __('a-z') }}</span>
                        <span class="requirement" id="req-upper"><i class="far fa-circle"></i> {{ __('A-Z') }}</span>
                        <span class="requirement" id="req-number"><i class="far fa-circle"></i> {{ __('0-9') }}</span>
                        <span class="requirement" id="req-special"><i class="far fa-circle"></i> {{ __('!@#') }}</span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password_confirmation">{{ __('Confirm Password') }}</label>
                    <div class="input-wrapper">
                        <input type="password" name="password_confirmation" id="password_confirmation" required placeholder="{{ __('Confirm your password') }}">
                        <i class="fas fa-eye input-icon" id="toggleConfirmPassword" onclick="togglePassword('password_confirmation', this)"></i>
                    </div>
                    <div id="passwordMatchIndicator" class="password-match-indicator">
                        <i class="fas fa-info-circle"></i><span id="passwordMatchMessage"></span>
                    </div>
                </div>

                <button type="submit" class="submit-btn" id="submitBtn">
                    <span>{{ __('Create My Account') }}</span>
                    <i class="fas fa-arrow-right"></i>
                </button>

                <div class="footer-links">
                    {{ __('Already have an account?') }} <a href="{{ route('login') }}">{{ __('Sign In') }}</a>
                    <span style="margin:0 6px;color:#e2e8f0;">·</span>
                    <a href="/">{{ __('Back to home') }}</a>
                </div>
            </form>
        </div>
    </div>

    <footer>
        &copy; {{ date('Y') }} SmartBiz. {{ __('All rights reserved.') }}
    </footer>

    <script>
        function togglePassword(fieldId, icon) {
            const input = document.getElementById(fieldId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fas fa-eye-slash input-icon';
            } else {
                input.type = 'password';
                icon.className = 'fas fa-eye input-icon';
            }
        }

        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('password_confirmation');
        const strengthBar = document.getElementById('passwordStrength');
        const requirements = {
            length: document.getElementById('req-length'),
            lower: document.getElementById('req-lower'),
            upper: document.getElementById('req-upper'),
            number: document.getElementById('req-number'),
            special: document.getElementById('req-special')
        };
        const passwordMatchIndicator = document.getElementById('passwordMatchIndicator');
        const passwordMatchMessage = document.getElementById('passwordMatchMessage');
        const submitBtn = document.getElementById('submitBtn');
        const form = document.getElementById('registerForm');

        password.addEventListener('input', checkPasswordStrength);
        confirmPassword.addEventListener('input', checkPasswordMatch);

        function checkPasswordStrength() {
            const val = password.value;
            let strength = 0;
            const hasLength = val.length >= 6;
            const hasLower = /[a-z]/.test(val);
            const hasUpper = /[A-Z]/.test(val);
            const hasNumber = /[0-9]/.test(val);
            const hasSpecial = /[^A-Za-z0-9]/.test(val);

            updateRequirement('length', hasLength);
            updateRequirement('lower', hasLower);
            updateRequirement('upper', hasUpper);
            updateRequirement('number', hasNumber);
            updateRequirement('special', hasSpecial);

            if (hasLength) strength++;
            if (hasLower) strength++;
            if (hasUpper) strength++;
            if (hasNumber) strength++;
            if (hasSpecial) strength++;

            strengthBar.className = 'password-strength-bar';
            strengthBar.classList.add('strength-' + strength);
            checkPasswordMatch();
        }

        function updateRequirement(req, met) {
            const el = requirements[req];
            if (met) {
                el.classList.add('met');
                el.querySelector('i').className = 'fas fa-check-circle';
            } else {
                el.classList.remove('met');
                el.querySelector('i').className = 'far fa-circle';
            }
        }

        function checkPasswordMatch() {
            const pass = password.value;
            const confirm = confirmPassword.value;
            if (confirm.length > 0) {
                if (pass === confirm) {
                    confirmPassword.style.borderColor = '#10b981';
                    passwordMatchIndicator.classList.add('show', 'match');
                    passwordMatchIndicator.classList.remove('no-match');
                    passwordMatchMessage.innerHTML = '<i class="fas fa-check-circle"></i> {{ __("Passwords match") }}';
                } else {
                    confirmPassword.style.borderColor = '#dc2626';
                    passwordMatchIndicator.classList.add('show', 'no-match');
                    passwordMatchIndicator.classList.remove('match');
                    passwordMatchMessage.innerHTML = '<i class="fas fa-exclamation-circle"></i> {{ __("Passwords do not match") }}';
                }
            } else {
                passwordMatchIndicator.classList.remove('show', 'match', 'no-match');
            }
        }

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const passwordValue = password.value;
            const confirmValue = confirmPassword.value;

            if (passwordValue !== confirmValue) {
                confirmPassword.style.borderColor = '#dc2626';
                return;
            }
            if (passwordValue.length < 6) {
                return;
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner spinner"></i> {{ __('Creating Account...') }}';

            const formData = new FormData(form);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch("{{ route('admin.store') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    "Accept": "application/json",
                    "X-Requested-With": "XMLHttpRequest"
                },
                body: formData
            })
            .then(async response => {
                const data = await response.json();
                if (!response.ok) {
                    if (data.errors) {
                        Object.values(data.errors).forEach(errorArray => { });
                    }
                    throw new Error(data.message || 'Request failed');
                }
                return data;
            })
            .then(data => {
                form.reset();
                setTimeout(() => {
                    window.location.href = "{{ route('login') }}";
                }, 1500);
            })
            .catch(error => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<span>{{ __('Create My Account') }}</span><i class="fas fa-arrow-right"></i>';
            });
        });

        if (password.value) {
            checkPasswordStrength();
        }
    </script>
</body>
</html>
