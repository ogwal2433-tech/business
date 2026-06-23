<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Privacy Policy — SmartBiz | Inventory & Sales Management System</title>

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
            padding-bottom: 80px;
        }

        nav {
            max-width: 900px;
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
            max-width: 900px;
            margin: 0 auto;
            padding: 40px 32px 60px;
        }

        .privacy-card {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.06);
            padding: 48px;
            animation: fadeInDown 0.5s ease-out;
        }

        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .privacy-card h1 {
            font-size: 2rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 6px;
        }

        .privacy-card .last-updated {
            font-size: 0.85rem;
            color: #94a3b8;
            margin-bottom: 32px;
        }

        .privacy-card h2 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #0f172a;
            margin: 28px 0 10px;
        }

        .privacy-card h2:first-of-type { margin-top: 0; }

        .privacy-card p {
            font-size: 0.95rem;
            color: #475569;
            line-height: 1.7;
            margin-bottom: 12px;
        }

        .privacy-card ul {
            margin: 8px 0 16px 20px;
            color: #475569;
            font-size: 0.95rem;
            line-height: 1.7;
        }

        .privacy-card ul li { margin-bottom: 6px; }

        .privacy-card ul li strong { color: #0f172a; }

        .privacy-card .badge-list {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin: 24px 0;
        }

        .privacy-card .badge-item {
            background: rgba(255,255,255,0.8);
            border: 1px solid rgba(255,255,255,0.95);
            border-radius: 12px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 14px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }

        .privacy-card .badge-item i {
            font-size: 1.4rem;
            color: #2563eb;
            width: 36px;
            text-align: center;
        }

        .privacy-card .badge-item h4 {
            font-size: 0.95rem;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 2px;
        }

        .privacy-card .badge-item p {
            font-size: 0.82rem;
            color: #94a3b8;
            margin-bottom: 0;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #64748b;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 24px;
            transition: color 0.2s;
        }

        .back-link:hover { color: #2563eb; }

        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-top: 1px solid rgba(0, 0, 0, 0.08);
            padding: 1rem 0;
            text-align: center;
            font-size: 0.85rem;
            color: #64748b;
            z-index: 10;
        }

        @media (max-width: 640px) {
            .privacy-card { padding: 32px 24px; }
            .privacy-card .badge-list { grid-template-columns: 1fr; }
            nav { padding: 16px 20px; }
            .main { padding: 20px 16px 60px; }
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
            <a href="/">Home</a>
        </div>
    </nav>

    <div class="main">
        <a href="/" class="back-link"><i class="fas fa-arrow-left"></i> Back to Home</a>

        <div class="privacy-card">
            <h1>Privacy Policy</h1>
            <p class="last-updated">Last updated: June 2025</p>

            <h2>Information We Collect</h2>
            <p>
                SmartBiz collects only the information necessary to provide our business
                management services. This includes:
            </p>
            <ul>
                <li><strong>Account Information</strong> — username, email address, and role (admin/employee) when you register.</li>
                <li><strong>Business Data</strong> — inventory records, sales transactions, client credit information, expenses, and journal entries that you enter into the system.</li>
                <li><strong>Usage Data</strong> — basic analytics about how you interact with the platform to improve performance and usability.</li>
            </ul>

            <h2>How We Use Your Information</h2>
            <p>
                Your data is used exclusively to operate and improve the SmartBiz platform:
            </p>
            <ul>
                <li>To provide inventory, sales, credit, expense, and accounting features</li>
                <li>To generate reports and dashboards for your business</li>
                <li>To communicate important updates about the service</li>
                <li>To ensure platform security and prevent abuse</li>
            </ul>

            <h2>Data Protection Measures</h2>
            <p>
                We employ industry-standard security practices to protect your data:
            </p>

            <div class="badge-list">
                <div class="badge-item">
                    <i class="fas fa-lock"></i>
                    <div>
                        <h4>256-bit Encryption</h4>
                        <p>All data transmitted via SSL/TLS</p>
                    </div>
                </div>
                <div class="badge-item">
                    <i class="fas fa-user-shield"></i>
                    <div>
                        <h4>Role-Based Access</h4>
                        <p>Granular admin & employee permissions</p>
                    </div>
                </div>
                <div class="badge-item">
                    <i class="fas fa-database"></i>
                    <div>
                        <h4>Daily Backups</h4>
                        <p>Automated encrypted backups</p>
                    </div>
                </div>
                <div class="badge-item">
                    <i class="fas fa-eye-slash"></i>
                    <div>
                        <h4>Zero Third-Party Sharing</h4>
                        <p>Your data stays yours</p>
                    </div>
                </div>
            </div>

            <h2>Data Retention</h2>
            <p>
                We retain your business data for as long as your account is active. If you
                choose to delete your account, all associated data is permanently erased
                within 30 days of the deletion request.
            </p>

            <h2>Your Rights</h2>
            <p>
                As a SmartBiz user, you have the right to:
            </p>
            <ul>
                <li>Access and export your data at any time</li>
                <li>Correct inaccurate information</li>
                <li>Request deletion of your account and associated data</li>
                <li>Opt out of non-essential communications</li>
            </ul>

            <h2>Third-Party Services</h2>
            <p>
                SmartBiz does not sell, trade, or share your personal or business data with
                third parties. We may use trusted service providers (e.g., cloud hosting) that
                are contractually bound to maintain confidentiality and security.
            </p>

            <h2>Contact Us</h2>
            <p>
                If you have questions about this privacy policy or how your data is handled,
                please contact your system administrator or reach out to our support team.
            </p>
        </div>
    </div>

    <footer>
        &copy; {{ date('Y') }} SmartBiz Admin. All rights reserved.
    </footer>
</body>
</html>
