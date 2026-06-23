<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ __('SmartBiz') }} | {{ __('Inventory') }} & {{ __('Sales') }} {{ __('Management System') }}</title>

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('bus.png') }}">
    <link rel="shortcut icon" href="{{ asset('bus.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(to bottom right, #cfe8ff, #ffffff, #cbdff7);
            min-height: 100vh;
            color: #1e293b;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }

        /* ── Scroll progress bar ── */
        #scroll-progress {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 9999;
            width: 0%;
            height: 3px;
            background: linear-gradient(90deg, #2563eb, #60a5fa);
            transition: width 0.1s linear;
        }

        /* ── Glass nav on scroll ── */
        nav {
            max-width: 1200px;
            margin: 0 auto 24px;
            padding: 14px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 16px;
            transition: all 0.4s ease;
        }

        nav.nav-scrolled {
            background: rgba(255,255,255,0.75);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.9);
            box-shadow: 0 8px 32px rgba(0,0,0,0.06);
            margin-bottom: 0;
            position: fixed;
            top: 40px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 150;
            width: calc(100% - 40px);
            max-width: 1200px;
        }

        body.nav-fixed-pad { padding-top: 80px; }

        /* ── Back to top button ── */
        #back-to-top {
            position: fixed;
            bottom: 32px;
            right: 32px;
            z-index: 999;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #2563eb;
            color: white;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            box-shadow: 0 4px 16px rgba(37,99,235,0.3);
            opacity: 0;
            transform: translateY(20px) scale(0.8);
            transition: all 0.35s ease;
            pointer-events: none;
        }

        #back-to-top.visible {
            opacity: 1;
            transform: translateY(0) scale(1);
            pointer-events: auto;
        }

        #back-to-top:hover {
            background: #1d4ed8;
            box-shadow: 0 6px 24px rgba(37,99,235,0.4);
            transform: translateY(-3px) scale(1.05);
        }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(37,99,235,0.2); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(37,99,235,0.35); }

        /* ── Language bar ── */
        .lang-bar {
            position: sticky;
            top: 0;
            z-index: 200;
            background: #1e3a5f;
            padding: 5px 0;
        }

        .lang-bar-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 32px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 4px;
        }

        .lang-link {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 12px;
            border-radius: 6px;
            font-size: 0.78rem;
            font-weight: 500;
            color: rgba(255,255,255,0.65);
            text-decoration: none;
            transition: all 0.2s;
        }

        .lang-link:hover {
            color: #fff;
            background: rgba(255,255,255,0.1);
        }

        .lang-link.active {
            color: #fff;
            background: rgba(255,255,255,0.15);
        }

        .lang-divider {
            width: 1px;
            height: 14px;
            background: rgba(255,255,255,0.15);
        }

        /* ── Navigation ── */
        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            font-size: 1.2rem;
            color: #0f172a;
            text-decoration: none;
        }

        .logo img { width: 28px; height: 28px; }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .nav-links a {
            padding: 8px 18px;
            border-radius: 10px;
            font-weight: 500;
            font-size: 0.88rem;
            text-decoration: none;
            color: #64748b;
            transition: all 0.25s;
        }

        .nav-links a:not(.btn-primary):hover {
            color: #2563eb;
            background: rgba(255,255,255,0.7);
        }

        .nav-links .btn-primary {
            background: #2563eb;
            color: white;
            box-shadow: 0 4px 14px rgba(37,99,235,0.25);
        }

        .nav-links .btn-primary:hover {
            background: #1d4ed8;
            box-shadow: 0 6px 20px rgba(37,99,235,0.35);
            transform: translateY(-1px);
        }

        .nav-links .btn-desktop-nav {
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 0.82rem;
            background: rgba(37,99,235,0.08);
            color: #2563eb;
            border: 1px solid rgba(37,99,235,0.15);
        }

        .nav-links .btn-desktop-nav:hover {
            background: rgba(37,99,235,0.15);
            color: #1d4ed8;
        }

        body.is-electron .btn-desktop-nav { display: none; }

        /* ── Hero ── */
        .hero {
            max-width: 1200px;
            margin: 0 auto;
            padding: 100px 32px 0;
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 60px;
            align-items: center;
        }

        .hero-left h1 {
            font-size: clamp(2.5rem, 5vw, 4rem);
            font-weight: 900;
            line-height: 1.08;
            letter-spacing: -0.04em;
            color: #0f172a;
            margin-bottom: 24px;
        }

        .hero-left h1 .highlight {
            color: #2563eb;
        }

        .hero-left p {
            font-size: 1.1rem;
            color: #64748b;
            line-height: 1.75;
            margin-bottom: 40px;
            max-width: 500px;
        }

        .hero-buttons { display: flex; gap: 14px; flex-wrap: wrap; }

        .hero-buttons a {
            padding: 16px 32px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.95rem;
            text-decoration: none;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .hero-buttons .btn-primary {
            background: #2563eb;
            color: white;
            box-shadow: 0 8px 25px rgba(37,99,235,0.25);
        }

        .hero-buttons .btn-primary:hover {
            background: #1d4ed8;
            box-shadow: 0 12px 35px rgba(37,99,235,0.35);
            transform: translateY(-2px);
        }

        .hero-buttons .btn-outline {
            background: rgba(255,255,255,0.8);
            backdrop-filter: blur(8px);
            color: #1e293b;
            border: 1px solid rgba(255,255,255,0.9);
        }

        .hero-buttons .btn-outline:hover {
            border-color: #2563eb;
            color: #2563eb;
            background: rgba(255,255,255,0.95);
            transform: translateY(-2px);
        }

        /* ── Dashboard mockup ── */
        .hero-right {}

        .dashboard-mockup {
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255,255,255,0.95);
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.08);
            transition: transform 0.5s ease, box-shadow 0.5s ease;
            animation: mockupFloat 6s ease-in-out infinite;
        }

        .dashboard-mockup:hover {
            transform: translateY(-4px);
            box-shadow: 0 30px 80px rgba(37,99,235,0.12);
        }

        @keyframes mockupFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        .mockup-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        .mockup-header .dots { display: flex; gap: 6px; }
        .mockup-header .dots span {
            width: 10px; height: 10px; border-radius: 50%;
        }
        .mockup-header .dots span:nth-child(1) { background: #ef4444; }
        .mockup-header .dots span:nth-child(2) { background: #f59e0b; }
        .mockup-header .dots span:nth-child(3) { background: #22c55e; }

        .mockup-header .badge {
            font-size: 0.7rem;
            padding: 4px 10px;
            border-radius: 6px;
            background: rgba(37,99,235,0.08);
            color: #2563eb;
            font-weight: 600;
        }

        .mockup-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 16px;
        }

        .mockup-stat {
            background: rgba(0,0,0,0.02);
            border-radius: 12px;
            padding: 14px;
            border: 1px solid rgba(0,0,0,0.04);
        }

        .mockup-stat .label { font-size: 0.7rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px; }
        .mockup-stat .value { font-size: 1.3rem; font-weight: 700; color: #0f172a; }
        .mockup-stat .value .trend { font-size: 0.7rem; font-weight: 600; margin-left: 6px; }
        .mockup-stat .value .trend.up { color: #22c55e; }
        .mockup-stat .value .trend.down { color: #ef4444; }

        .mockup-chart {
            background: rgba(0,0,0,0.02);
            border-radius: 12px;
            padding: 16px;
            border: 1px solid rgba(0,0,0,0.04);
        }

        .mockup-chart .chart-label { font-size: 0.75rem; color: #94a3b8; margin-bottom: 12px; font-weight: 500; }

        .mockup-chart .bars {
            display: flex;
            align-items: flex-end;
            gap: 6px;
            height: 80px;
        }

        .mockup-chart .bars .bar {
            flex: 1;
            border-radius: 4px 4px 0 0;
            background: linear-gradient(to top, #2563eb, #60a5fa);
            min-height: 12px;
            transition: height 0.6s ease;
            opacity: 0.7;
        }

        .mockup-chart .bars .bar:nth-child(1) { height: 40%; }
        .mockup-chart .bars .bar:nth-child(2) { height: 65%; }
        .mockup-chart .bars .bar:nth-child(3) { height: 30%; }
        .mockup-chart .bars .bar:nth-child(4) { height: 75%; }
        .mockup-chart .bars .bar:nth-child(5) { height: 50%; }
        .mockup-chart .bars .bar:nth-child(6) { height: 85%; }
        .mockup-chart .bars .bar:nth-child(7) { height: 55%; }
        .mockup-chart .bars .bar:nth-child(8) { height: 70%; }
        .mockup-chart .bars .bar:nth-child(9) { height: 45%; }
        .mockup-chart .bars .bar:nth-child(10) { height: 90%; }

        .mockup-chart .bars .bar.highlight {
            opacity: 1;
            background: linear-gradient(to top, #2563eb, #3b82f6);
        }

        .mockup-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 16px;
            padding-top: 14px;
            border-top: 1px solid rgba(0,0,0,0.05);
        }

        .mockup-footer .user {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .mockup-footer .user .avatar {
            width: 28px; height: 28px; border-radius: 50%;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 0.7rem; color: white;
        }

        .mockup-footer .user .name { font-size: 0.75rem; color: #64748b; }
        .mockup-footer .status { font-size: 0.7rem; color: #22c55e; display: flex; align-items: center; gap: 4px; }

        .mockup-footer .status::before {
            content: '';
            width: 6px; height: 6px; border-radius: 50%;
            background: #22c55e;
            animation: statusPulse 2s ease-in-out infinite;
        }

        @keyframes statusPulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }

        /* ── Hero cards grid ── */
        .hero-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .hero-card {
            background: rgba(255,255,255,0.85);
            backdrop-filter: blur(4px);
            border: 1px solid rgba(255,255,255,0.9);
            border-radius: 12px;
            padding: 16px;
            transition: all 0.25s;
        }

        .hero-card:hover {
            border-color: #bfdbfe;
            background: rgba(255,255,255,0.95);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(37,99,235,0.06);
        }

        .hero-card .icon {
            width: 36px; height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
            font-size: 1rem;
        }

        .hero-card h3 { font-size: 0.9rem; font-weight: 600; color: #0f172a; margin-bottom: 2px; }
        .hero-card p { font-size: 0.78rem; color: #64748b; line-height: 1.4; }

        /* ── Sections ── */
        .section { max-width: 1200px; margin: 0 auto; padding: 120px 32px; }
        .section-header { text-align: center; max-width: 640px; margin: 0 auto 60px; }

        .section-header h2 {
            font-size: clamp(1.8rem, 3.5vw, 2.5rem);
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 14px;
            letter-spacing: -0.02em;
        }

        .section-header h2 .highlight {
            color: #2563eb;
        }

        .section-header p {
            font-size: 1rem;
            color: #64748b;
            line-height: 1.7;
        }

        /* ── Feature cards ── */
        .grid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .feature-card {
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.95);
            border-radius: 16px;
            padding: 32px;
            transition: all 0.35s ease;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.04);
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, #2563eb, transparent);
            opacity: 0;
            transition: opacity 0.4s;
        }

        .feature-card:hover::before { opacity: 1; }

        .feature-card:hover {
            transform: translateY(-6px);
            background: rgba(255,255,255,0.98);
            border-color: #bfdbfe;
            box-shadow: 0 12px 32px rgba(37,99,235,0.08);
        }

        .feature-card .icon {
            width: 48px; height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 18px;
            font-size: 1.2rem;
            transition: all 0.35s ease;
        }

        .feature-card:hover .icon {
            transform: scale(1.1) rotate(-4deg);
        }

        .feature-card h3 { font-size: 1.1rem; font-weight: 700; color: #0f172a; margin-bottom: 10px; }
        .feature-card p { font-size: 0.9rem; color: #64748b; line-height: 1.6; }

        /* ── Industries ── */
        .industries {
            max-width: 1200px;
            margin: 0 auto;
            padding: 60px 32px 30px;
        }

        .industry-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 12px;
            max-width: 960px;
            margin: 0 auto;
        }

        .industry-item {
            background: rgba(255,255,255,0.85);
            backdrop-filter: blur(4px);
            border: 1px solid rgba(255,255,255,0.9);
            border-radius: 12px;
            padding: 20px 12px;
            text-align: center;
            font-size: 0.83rem;
            font-weight: 500;
            color: #475569;
            transition: all 0.3s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        }

        .industry-item:hover {
            border-color: #bfdbfe;
            background: rgba(255,255,255,0.95);
            color: #2563eb;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(37,99,235,0.08);
        }

        .industry-item i {
            display: block;
            font-size: 1.4rem;
            margin-bottom: 8px;
            color: #2563eb;
        }

        /* ── Stats ── */
        .stats {
            max-width: 1200px;
            margin: 0 auto;
            padding: 80px 32px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
        }

        .stat {
            text-align: center;
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.95);
            border-radius: 16px;
            padding: 36px 24px;
            transition: all 0.3s;
            box-shadow: 0 4px 12px rgba(0,0,0,0.04);
        }

        .stat:hover {
            transform: translateY(-4px);
            border-color: #bfdbfe;
            box-shadow: 0 12px 32px rgba(37,99,235,0.08);
        }

        .stat .num {
            font-size: 2.5rem;
            font-weight: 900;
            color: #2563eb;
            margin-bottom: 6px;
        }

        .stat p { font-size: 0.9rem; color: #64748b; font-weight: 500; }

        /* ── Testimonials ── */
        .testimonials {
            max-width: 1200px;
            margin: 0 auto;
            padding: 80px 32px;
        }

        .testimonial-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .testimonial-card {
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.95);
            border-radius: 16px;
            padding: 28px;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            box-shadow: 0 4px 12px rgba(0,0,0,0.04);
        }

        .testimonial-card:hover {
            transform: translateY(-6px);
            border-color: #bfdbfe;
            background: rgba(255,255,255,0.98);
            box-shadow: 0 12px 32px rgba(37,99,235,0.08);
        }

        .testimonial-card .quote {
            font-size: 0.9rem;
            color: #475569;
            line-height: 1.7;
            margin-bottom: 18px;
            font-style: italic;
        }

        .testimonial-card .quote i {
            color: #2563eb;
            opacity: 0.2;
            font-size: 1.2rem;
            margin-right: 4px;
        }

        .testimonial-card .author {
            display: flex;
            align-items: center;
            gap: 12px;
            border-top: 1px solid rgba(0,0,0,0.05);
            padding-top: 16px;
        }

        .testimonial-card .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            overflow: hidden;
            flex-shrink: 0;
            border: 2px solid rgba(37,99,235,0.1);
            transition: all 0.3s;
        }

        .testimonial-card:hover .avatar {
            border-color: rgba(37,99,235,0.3);
        }

        .testimonial-card .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .testimonial-card .info h4 {
            font-size: 0.9rem;
            font-weight: 600;
            color: #0f172a;
        }

        .testimonial-card .info p {
            font-size: 0.78rem;
            color: #94a3b8;
        }

        .testimonial-card .location {
            position: absolute;
            top: 16px;
            right: 16px;
            font-size: 0.72rem;
            background: rgba(37,99,235,0.08);
            color: #2563eb;
            padding: 3px 10px;
            border-radius: 20px;
            font-weight: 500;
        }

        /* ── Testimonial Modal ── */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.4);
            backdrop-filter: blur(4px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-overlay.active { display: flex; }

        .modal-content {
            background: white;
            border-radius: 24px;
            max-width: 520px;
            width: 100%;
            padding: 40px;
            position: relative;
            animation: modalIn 0.35s ease-out;
            box-shadow: 0 25px 80px rgba(0,0,0,0.15);
        }

        @keyframes modalIn {
            from { opacity: 0; transform: scale(0.92) translateY(16px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }

        .modal-close {
            position: absolute;
            top: 16px;
            right: 16px;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: none;
            background: #f1f5f9;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            transition: all 0.2s;
        }

        .modal-close:hover { background: #e2e8f0; color: #0f172a; }

        .modal-body { text-align: center; }

        .modal-body .big-avatar {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            margin: 0 auto 18px;
            overflow: hidden;
            border: 3px solid rgba(37,99,235,0.1);
        }

        .modal-body .big-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .modal-body blockquote {
            font-size: 1.05rem;
            color: #334155;
            line-height: 1.8;
            font-style: italic;
            margin-bottom: 24px;
        }

        .modal-body blockquote i {
            color: #2563eb;
            opacity: 0.15;
            font-size: 2rem;
            display: block;
            margin-bottom: 10px;
        }

        .modal-body .name { font-weight: 700; font-size: 1.05rem; color: #0f172a; }
        .modal-body .title { font-size: 0.85rem; color: #94a3b8; margin-top: 2px; }

        .modal-body .detail-location {
            display: inline-block;
            margin-top: 14px;
            font-size: 0.8rem;
            background: rgba(37,99,235,0.08);
            color: #2563eb;
            padding: 4px 16px;
            border-radius: 20px;
        }

        .modal-body .rating {
            margin-top: 16px;
            color: #f59e0b;
            font-size: 1.1rem;
        }

        /* ── Privacy ── */
        .privacy-section {
            padding: 100px 32px;
            background: rgba(255,255,255,0.4);
            backdrop-filter: blur(4px);
            border-top: 1px solid rgba(255,255,255,0.8);
            border-bottom: 1px solid rgba(255,255,255,0.8);
        }

        .privacy-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 80px;
            align-items: center;
        }

        .privacy-inner h2 {
            font-size: 2rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 16px;
        }

        .privacy-inner > div p {
            color: #64748b;
            line-height: 1.7;
            margin-bottom: 12px;
            font-size: 0.95rem;
        }

        .privacy-badges {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .privacy-badge {
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.95);
            border-radius: 12px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 14px;
            transition: all 0.3s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }

        .privacy-badge:hover {
            background: rgba(255,255,255,0.98);
            border-color: #bfdbfe;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(37,99,235,0.06);
        }

        .privacy-badge i { font-size: 1.3rem; color: #2563eb; width: 32px; text-align: center; }
        .privacy-badge h4 { font-size: 0.9rem; font-weight: 600; color: #0f172a; margin-bottom: 2px; }
        .privacy-badge p { font-size: 0.8rem; color: #94a3b8; }

        /* ── CTA ── */
        .cta-section {
            text-align: center;
            padding: 120px 32px;
        }

        .cta-section h2 {
            font-size: clamp(1.8rem, 3.5vw, 2.5rem);
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 14px;
            letter-spacing: -0.02em;
        }

        .cta-section p {
            color: #64748b;
            margin-bottom: 36px;
            font-size: 1rem;
        }

        .cta-section a {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 18px 40px;
            background: #2563eb;
            color: white;
            border-radius: 14px;
            font-weight: 600;
            text-decoration: none;
            font-size: 1rem;
            transition: all 0.3s;
            box-shadow: 0 8px 25px rgba(37,99,235,0.25);
        }

        .cta-section a:hover {
            background: #1d4ed8;
            box-shadow: 0 12px 40px rgba(37,99,235,0.35);
            transform: translateY(-2px);
        }

        /* ── Footer ── */
        footer {
            width: 100%;
            text-align: center;
            padding: 2rem 1rem;
            font-size: 0.85rem;
            color: #64748b;
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(10px);
            border-top: 1px solid rgba(0,0,0,0.06);
        }

        /* ── Scroll reveal animations ── */
        .reveal {
            opacity: 0;
            transform: translateY(40px);
            transition: opacity 0.8s ease, transform 0.8s ease;
        }

        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .reveal-delay-1 { transition-delay: 0.1s; }
        .reveal-delay-2 { transition-delay: 0.2s; }
        .reveal-delay-3 { transition-delay: 0.3s; }
        .reveal-delay-4 { transition-delay: 0.4s; }
        .reveal-delay-5 { transition-delay: 0.5s; }
        .reveal-delay-6 { transition-delay: 0.6s; }

        .fade-in {
            animation: fadeInUp 0.7s ease-out forwards;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ── Responsive ── */
        @media (max-width: 1024px) {
            .hero { grid-template-columns: 1fr; gap: 60px; padding-top: 60px; }
            .hero-left p { max-width: 100%; }
            .grid-3 { grid-template-columns: 1fr 1fr; }
            .testimonial-grid { grid-template-columns: 1fr 1fr; }
            .industry-grid { grid-template-columns: repeat(4, 1fr); }
            .stats { grid-template-columns: repeat(2, 1fr); }
            .privacy-inner { grid-template-columns: 1fr; gap: 40px; }
            .dashboard-mockup { max-width: 520px; margin: 0 auto; }
        }

        @media (max-width: 768px) {
            .grid-3 { grid-template-columns: 1fr; }
            .testimonial-grid { grid-template-columns: 1fr; }
            .industry-grid { grid-template-columns: repeat(3, 1fr); }
            .stats { grid-template-columns: 1fr; gap: 16px; }
            .privacy-badges { grid-template-columns: 1fr; }
            nav { flex-direction: column; gap: 14px; padding: 14px 20px; }
            .modal-content { padding: 28px 20px; }
            .section { padding: 80px 20px; }
            .hero { padding: 40px 20px 0; }
            .testimonials { padding: 60px 20px; }
            .stats { padding: 60px 20px; }
            .privacy-section { padding: 60px 20px; }
            .cta-section { padding: 80px 20px; }
            .industries { padding: 40px 20px 10px; }
            .hero-grid { grid-template-columns: 1fr 1fr; }
        }

        @media (max-width: 480px) {
            .hero-left h1 { font-size: 2rem; }
            .hero-left p { font-size: 0.95rem; margin-bottom: 24px; }
            .hero-buttons { flex-direction: column; }
            .hero-buttons a { width: 100%; text-align: center; justify-content: center; }
            .hero-buttons a.btn-primary,
            .hero-buttons a.btn-outline,
            .hero-buttons a.btn-desktop { padding: 14px 24px; }
            .section-header h2 { font-size: 1.5rem; }
            .section-header p { font-size: 0.9rem; }
            .feature-card { padding: 24px; }
            .cta-section h2 { font-size: 1.5rem; }
            .cta-section p { font-size: 0.9rem; }
            .cta-section a { width: 100%; text-align: center; justify-content: center; }
            .lang-bar-inner { flex-wrap: wrap; justify-content: center; gap: 2px; }
            .nav-links { flex-wrap: wrap; justify-content: center; }
            .nav-links a { padding: 10px 14px; font-size: 0.82rem; }
            footer { padding: 1rem 0.75rem; font-size: 0.75rem; }
            .industry-grid { grid-template-columns: repeat(2, 1fr); }
            .hero-grid { grid-template-columns: 1fr; }
            .mockup-stats { grid-template-columns: 1fr; }
            .stat { padding: 28px 16px; }
            .stat .num { font-size: 2rem; }
        }

        @media (max-width: 360px) {
            .hero-left h1 { font-size: 1.6rem; }
            .hero-left p { font-size: 0.85rem; }
            .hero-buttons a.btn-primary,
            .hero-buttons a.btn-outline { padding: 12px 20px; font-size: 0.85rem; }
            .cta-section a { padding: 14px 24px; font-size: 0.9rem; }
            .nav-links a { padding: 8px 12px; font-size: 0.78rem; }
        }
    </style>
</head>
<body>
    <div id="scroll-progress"></div>

    <!-- ── Language bar ── -->
    @php $currentLocale = session('locale', 'en'); @endphp
    <div class="lang-bar">
        <div class="lang-bar-inner">
            <a href="{{ route('language.switch', 'en') }}" class="lang-link {{ $currentLocale === 'en' ? 'active' : '' }}"><span>🇬🇧</span> English</a>
            <span class="lang-divider"></span>
            <a href="{{ route('language.switch', 'sw') }}" class="lang-link {{ $currentLocale === 'sw' ? 'active' : '' }}"><span>🇹🇿</span> Kiswahili</a>
            <span class="lang-divider"></span>
            <a href="{{ route('language.switch', 'ar') }}" class="lang-link {{ $currentLocale === 'ar' ? 'active' : '' }}"><span>🇸🇦</span> العربية</a>
        </div>
    </div>

    <!-- ── Navigation ── -->
    <nav>
        <a href="/" class="logo">
            <img src="{{ asset('bus.png') }}" alt="SmartBiz">
            SmartBiz
        </a>
        <div class="nav-links">
            <a href="#features">{{ __('Features') }}</a>
            <a href="#testimonials">{{ __('Testimonials') }}</a>
            <a href="{{ route('login') }}">{{ __('Sign In') }}</a>
            <a href="{{ route('register') }}" class="btn-primary">{{ __('Get Started') }} <i class="fas fa-arrow-right" style="font-size:0.75rem;"></i></a>
            <a href="{{ route('app.download', ['platform' => 'windows']) }}" class="btn-desktop-nav" id="desktop-download-btn"><i class="fas fa-download"></i> {{ __('Desktop App') }}</a>
        </div>
    </nav>

    <!-- ── Hero ── -->
    <div class="hero">
        <div class="hero-left fade-in">
            <h1>{{ __('Business Management') }}<br><span class="highlight">{{ __('Made Simple') }}</span></h1>
            <p>
                {{ __('One platform to manage inventory, sales, credit clients, expenses, and financial reporting. Built for businesses that need clarity and control.') }}
            </p>
            <div class="hero-buttons">
                <a href="{{ route('register') }}" class="btn-primary">{{ __('Create Free Account') }} <i class="fas fa-arrow-right" style="font-size:0.85rem;"></i></a>
                <a href="{{ route('login') }}" class="btn-outline">{{ __('Sign In') }}</a>
            </div>
        </div>
        <div class="hero-right fade-in" style="animation-delay:0.2s;">
            <div class="dashboard-mockup">
                <div class="mockup-header">
                    <div class="dots">
                        <span></span><span></span><span></span>
                    </div>
                    <span class="badge">{{ __('Live') }}</span>
                </div>
                <div class="mockup-stats">
                    <div class="mockup-stat">
                        <div class="label">{{ __('Today Revenue') }}</div>
                        <div class="value">UGX 845K <span class="trend up">+12.5%</span></div>
                    </div>
                    <div class="mockup-stat">
                        <div class="label">{{ __('Orders') }}</div>
                        <div class="value">48 <span class="trend up">+8.2%</span></div>
                    </div>
                </div>
                <div class="mockup-chart">
                    <div class="chart-label">{{ __('Weekly Sales') }}</div>
                    <div class="bars">
                        <div class="bar"></div>
                        <div class="bar"></div>
                        <div class="bar"></div>
                        <div class="bar"></div>
                        <div class="bar"></div>
                        <div class="bar highlight"></div>
                        <div class="bar"></div>
                        <div class="bar"></div>
                        <div class="bar"></div>
                        <div class="bar highlight"></div>
                    </div>
                </div>
                <div class="mockup-footer">
                    <div class="user">
                        <div class="avatar">SM</div>
                        <span class="name">Sarah's Boutique</span>
                    </div>
                    <span class="status">{{ __('System Online') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Features ── -->
    <div class="section" id="features">
        <div class="section-header reveal">
            <h2>{{ __('Everything your') }} <span class="highlight">{{ __('business needs') }}</span></h2>
            <p>{{ __('A complete platform for managing your day-to-day operations and finances.') }}</p>
        </div>
        <div class="grid-3">
            <div class="feature-card reveal reveal-delay-1">
                <div class="icon" style="background:rgba(37,99,235,0.1);color:#2563eb;"><i class="fas fa-warehouse"></i></div>
                <h3>{{ __('Inventory Control') }}</h3>
                <p>{{ __('Real-time stock tracking, bulk uploads, purchase management, low-stock alerts, and adjustment history.') }}</p>
            </div>
            <div class="feature-card reveal reveal-delay-2">
                <div class="icon" style="background:rgba(34,197,94,0.1);color:#16a34a;"><i class="fas fa-shopping-cart"></i></div>
                <h3>{{ __('Sales Recording') }}</h3>
                <p>{{ __('Cash and credit sales with itemized entries, employee attribution, and daily reporting.') }}</p>
            </div>
            <div class="feature-card reveal reveal-delay-3">
                <div class="icon" style="background:rgba(234,179,8,0.1);color:#ca8a04;"><i class="fas fa-hand-holding-usd"></i></div>
                <h3>{{ __('Credit & Repayments') }}</h3>
                <p>{{ __('Client accounts with flexible repayment plans, balance tracking, and payment history.') }}</p>
            </div>
            <div class="feature-card reveal reveal-delay-4">
                <div class="icon" style="background:rgba(236,72,153,0.1);color:#db2777;"><i class="fas fa-file-invoice-dollar"></i></div>
                <h3>{{ __('Expenses') }}</h3>
                <p>{{ __('Operational and employee expense tracking with full categorization and reporting.') }}</p>
            </div>
            <div class="feature-card reveal reveal-delay-5">
                <div class="icon" style="background:rgba(6,182,212,0.1);color:#0891b2;"><i class="fas fa-calculator"></i></div>
                <h3>{{ __('Accounting') }}</h3>
                <p>{{ __('Journal entries, chart of accounts, profit & loss, and balance sheet reports.') }}</p>
            </div>
            <div class="feature-card reveal reveal-delay-6">
                <div class="icon" style="background:rgba(139,92,246,0.1);color:#7c3aed;"><i class="fas fa-users-cog"></i></div>
                <h3>{{ __('Team Management') }}</h3>
                <p>{{ __('Role-based access for admin and employees with performance tracking and messaging.') }}</p>
            </div>
        </div>
    </div>

    <!-- ── Industries ── -->
    <div class="industries">
        <div class="section-header reveal">
            <h2>{{ __('Built for') }} <span class="highlight">{{ __('every type') }}</span> {{ __('of business') }}</h2>
            <p>{{ __('From small shops to large enterprises, SmartBiz adapts to your industry.') }}</p>
        </div>
        <div class="industry-grid">
            <div class="industry-item reveal reveal-delay-1"><i class="fas fa-spa"></i> {{ __('Perfumery') }}</div>
            <div class="industry-item reveal reveal-delay-2"><i class="fas fa-store"></i> {{ __('Retail Shops') }}</div>
            <div class="industry-item reveal reveal-delay-3"><i class="fas fa-shopping-bag"></i> {{ __('Boutiques') }}</div>
            <div class="industry-item reveal reveal-delay-4"><i class="fas fa-superpowers"></i> {{ __('Supermarkets') }}</div>
            <div class="industry-item reveal reveal-delay-5"><i class="fas fa-capsules"></i> {{ __('Pharmacy') }}</div>
            <div class="industry-item reveal reveal-delay-6"><i class="fas fa-tv"></i> {{ __('Electronics') }}</div>
            <div class="industry-item reveal reveal-delay-1"><i class="fas fa-tools"></i> {{ __('Hardware') }}</div>
            <div class="industry-item reveal reveal-delay-2"><i class="fas fa-warehouse"></i> {{ __('Wholesale') }}</div>
            <div class="industry-item reveal reveal-delay-3"><i class="fas fa-tshirt"></i> {{ __('Clothing') }}</div>
            <div class="industry-item reveal reveal-delay-4"><i class="fas fa-wine-bottle"></i> {{ __('Liquor Store') }}</div>
            <div class="industry-item reveal reveal-delay-5"><i class="fas fa-seedling"></i> {{ __('Agribusiness') }}</div>
            <div class="industry-item reveal reveal-delay-6"><i class="fas fa-concierge-bell"></i> {{ __('Hospitality') }}</div>
        </div>
    </div>

    <!-- ── Stats ── -->
    <div class="stats">
        <div class="stat reveal reveal-delay-1">
            <div class="num" data-target="15">0</div>
            <p>{{ __('Active businesses') }}</p>
        </div>
        <div class="stat reveal reveal-delay-2">
            <div class="num" data-target="50">0</div>
            <p>{{ __('Daily transactions') }}</p>
        </div>
        <div class="stat reveal reveal-delay-3">
            <div class="num" data-target="999">0</div>
            <p>{{ __('Uptime SLA') }}</p>
        </div>
        <div class="stat reveal reveal-delay-4">
            <div class="num" data-target="247">0</div>
            <p>{{ __('Support') }}</p>
        </div>
    </div>

    <!-- ── Testimonials ── -->
    <div class="testimonials" id="testimonials">
        <div class="section-header reveal">
            <h2>{{ __('Trusted by') }} <span class="highlight">{{ __('businesses across Uganda') }}</span></h2>
            <p>{{ __('Hear from real business owners using SmartBiz to manage their operations every day.') }}</p>
        </div>
        <div class="testimonial-grid">
            <div class="testimonial-card reveal reveal-delay-1" onclick="openTestimonial(0)">
                <span class="location">📍 Kampala</span>
                <div class="quote"><i class="fas fa-quote-left"></i>{{ __('SmartBiz transformed how I track my inventory. I used to lose stock weekly, now I know exactly what I have and what I need to order.') }}</div>
                <div class="author">
                    <div class="avatar"><img src="https://images.pexels.com/photos/3727474/pexels-photo-3727474.jpeg?auto=compress&cs=tinysrgb&w=200&h=200&fit=crop" alt="Nantongo Sarah" loading="lazy"></div>
                    <div class="info">
                        <h4>Nantongo Sarah</h4>
                        <p>{{ __('Retail Shop Owner') }} — Wandegeya</p>
                    </div>
                </div>
            </div>
            <div class="testimonial-card reveal reveal-delay-2" onclick="openTestimonial(1)">
                <span class="location">📍 Gulu</span>
                <div class="quote"><i class="fas fa-quote-left"></i>{{ __('The credit sales feature is a lifesaver. I can now track who owes me and when payments are due. My cash flow has improved dramatically.') }}</div>
                <div class="author">
                    <div class="avatar"><img src="https://images.pexels.com/photos/5647575/pexels-photo-5647575.jpeg?auto=compress&cs=tinysrgb&w=200&h=200&fit=crop" alt="Okot Denis" loading="lazy"></div>
                    <div class="info">
                        <h4>Okot Denis</h4>
                        <p>{{ __('Hardware Store') }} — Gulu Town</p>
                    </div>
                </div>
            </div>
            <div class="testimonial-card reveal reveal-delay-3" onclick="openTestimonial(2)">
                <span class="location">📍 Mbarara</span>
                <div class="quote"><i class="fas fa-quote-left"></i>{{ __('I manage three employees and the reporting helps me see who is performing. The AI assistant answers my questions instantly. Worth every shilling!') }}</div>
                <div class="author">
                    <div class="avatar"><img src="https://images.pexels.com/photos/5717310/pexels-photo-5717310.jpeg?auto=compress&cs=tinysrgb&w=200&h=200&fit=crop" alt="Aisha Namubiru" loading="lazy"></div>
                    <div class="info">
                        <h4>Aisha Namubiru</h4>
                        <p>{{ __('Pharmacy Owner') }} — Mbarara City</p>
                    </div>
                </div>
            </div>
            <div class="testimonial-card reveal reveal-delay-4" onclick="openTestimonial(3)">
                <span class="location">📍 Jinja</span>
                <div class="quote"><i class="fas fa-quote-left"></i>{{ __('Setting up was so easy. I uploaded my products in minutes and started selling the same day. The dashboard gives me everything I need at a glance.') }}</div>
                <div class="author">
                    <div class="avatar"><img src="https://images.pexels.com/photos/6578432/pexels-photo-6578432.jpeg?auto=compress&cs=tinysrgb&w=200&h=200&fit=crop" alt="Wasswa Charles" loading="lazy"></div>
                    <div class="info">
                        <h4>Wasswa Charles</h4>
                        <p>{{ __('Supermarket') }} — Jinja Main Street</p>
                    </div>
                </div>
            </div>
            <div class="testimonial-card reveal reveal-delay-5" onclick="openTestimonial(4)">
                <span class="location">📍 Arua</span>
                <div class="quote"><i class="fas fa-quote-left"></i>{{ __('As a wholesaler, I deal with bulk stock and dozens of products daily. SmartBiz makes inventory management smooth and my profit tracking is now accurate.') }}</div>
                <div class="author">
                    <div class="avatar"><img src="https://images.pexels.com/photos/9775676/pexels-photo-9775676.jpeg?auto=compress&cs=tinysrgb&w=200&h=200&fit=crop" alt="Dralega Peter" loading="lazy"></div>
                    <div class="info">
                        <h4>Dralega Peter</h4>
                        <p>{{ __('Wholesale Distributor') }} — Arua Park</p>
                    </div>
                </div>
            </div>
            <div class="testimonial-card reveal reveal-delay-6" onclick="openTestimonial(5)">
                <span class="location">📍 Mbale</span>
                <div class="quote"><i class="fas fa-quote-left"></i>{{ __('I run a boutique and a small salon. Having both inventory and expense tracking in one system saves me hours every week. Highly recommend!') }}</div>
                <div class="author">
                    <div class="avatar"><img src="https://images.pexels.com/photos/7876449/pexels-photo-7876449.jpeg?auto=compress&cs=tinysrgb&w=200&h=200&fit=crop" alt="Florence Nabirye" loading="lazy"></div>
                    <div class="info">
                        <h4>Florence Nabirye</h4>
                        <p>{{ __('Boutique & Salon Owner') }} — Mbale Town</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Testimonial Popup Modal ── -->
    <div class="modal-overlay" id="testimonialModal" onclick="if(event.target===this)closeTestimonial()">
        <div class="modal-content">
            <button class="modal-close" onclick="closeTestimonial()"><i class="fas fa-times"></i></button>
            <div class="modal-body" id="modalBody">
            </div>
        </div>
    </div>

    <script>
    var testimonials = [
        {
            image: 'https://images.pexels.com/photos/3727474/pexels-photo-3727474.jpeg?auto=compress&cs=tinysrgb&w=400&h=400&fit=crop', color: '#2563eb', name: 'Nantongo Sarah',
            title: '{{ __("Retail Shop Owner") }} — Wandegeya',
            location: '📍 Kampala',
            quote: '{{ __("SmartBiz transformed how I track my inventory. I used to lose stock weekly, now I know exactly what I have and what I need to order. The real-time stock updates alone have saved me from overselling multiple times.") }}',
            rating: 5
        },
        {
            image: 'https://images.pexels.com/photos/5647575/pexels-photo-5647575.jpeg?auto=compress&cs=tinysrgb&w=400&h=400&fit=crop', color: '#16a34a', name: 'Okot Denis',
            title: '{{ __("Hardware Store") }} — Gulu Town',
            location: '📍 Gulu',
            quote: '{{ __("The credit sales feature is a lifesaver. I can now track who owes me and when payments are due. My cash flow has improved dramatically since I started using SmartBiz. The repayment reminders are a game changer.") }}',
            rating: 5
        },
        {
            image: 'https://images.pexels.com/photos/5717310/pexels-photo-5717310.jpeg?auto=compress&cs=tinysrgb&w=400&h=400&fit=crop', color: '#db2777', name: 'Aisha Namubiru',
            title: '{{ __("Pharmacy Owner") }} — Mbarara City',
            location: '📍 Mbarara',
            quote: '{{ __("I manage three employees and the reporting helps me see who is performing. The AI assistant answers my questions instantly. Worth every shilling! The financial position report gives me a clear picture of my business health.") }}',
            rating: 5
        },
        {
            image: 'https://images.pexels.com/photos/6578432/pexels-photo-6578432.jpeg?auto=compress&cs=tinysrgb&w=400&h=400&fit=crop', color: '#f59e0b', name: 'Wasswa Charles',
            title: '{{ __("Supermarket") }} — Jinja Main Street',
            location: '📍 Jinja',
            quote: '{{ __("Setting up was so easy. I uploaded my products in minutes and started selling the same day. The dashboard gives me everything I need at a glance — daily sales, expenses, and profit all in one place.") }}',
            rating: 5
        },
        {
            image: 'https://images.pexels.com/photos/9775676/pexels-photo-9775676.jpeg?auto=compress&cs=tinysrgb&w=400&h=400&fit=crop', color: '#0891b2', name: 'Dralega Peter',
            title: '{{ __("Wholesale Distributor") }} — Arua Park',
            location: '📍 Arua',
            quote: '{{ __("As a wholesaler, I deal with bulk stock and dozens of products daily. SmartBiz makes inventory management smooth and my profit tracking is now accurate. The bulk upload feature saves me hours every week.") }}',
            rating: 5
        },
        {
            image: 'https://images.pexels.com/photos/7876449/pexels-photo-7876449.jpeg?auto=compress&cs=tinysrgb&w=400&h=400&fit=crop', color: '#7c3aed', name: 'Florence Nabirye',
            title: '{{ __("Boutique & Salon Owner") }} — Mbale Town',
            location: '📍 Mbale',
            quote: "{{ __('I run a boutique and a small salon. Having both inventory and expense tracking in one system saves me hours every week. Highly recommend! The employee management feature helps me track my team\'s performance too.') }}",
            rating: 5
        }
    ];

    function openTestimonial(index) {
        var t = testimonials[index];
        if (!t) return;
        var stars = '';
        for (var i = 0; i < (t.rating || 5); i++) stars += '<i class="fas fa-star"></i>';
        document.getElementById('modalBody').innerHTML =
            '<div class="big-avatar"><img src="' + t.image + '" alt="' + t.name + '"></div>' +
            '<blockquote><i class="fas fa-quote-left"></i>' + t.quote + '</blockquote>' +
            '<div class="name">' + t.name + '</div>' +
            '<div class="title">' + t.title + '</div>' +
            '<div class="detail-location">' + t.location + '</div>' +
            '<div class="rating">' + stars + '</div>';
        document.getElementById('testimonialModal').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeTestimonial() {
        document.getElementById('testimonialModal').classList.remove('active');
        document.body.style.overflow = '';
    }

    // ── IntersectionObserver scroll reveals ──
    document.addEventListener('DOMContentLoaded', function() {
        var revealEls = document.querySelectorAll('.reveal');
        if (revealEls.length) {
            var observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
            revealEls.forEach(function(el) { observer.observe(el); });
        }

        // ── Animated counters ──
        var statNums = document.querySelectorAll('.stat .num');
        if (statNums.length) {
            var counterObserver = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        var el = entry.target;
                        var target = parseInt(el.getAttribute('data-target'));
                        if (el._counting) return;
                        el._counting = true;
                        animateCounter(el, target);
                        counterObserver.unobserve(el);
                    }
                });
            }, { threshold: 0.5 });
            statNums.forEach(function(el) { counterObserver.observe(el); });
        }

        function animateCounter(el, target) {
            var duration = 2000;
            var start = performance.now();
            function step(now) {
                var elapsed = now - start;
                var progress = Math.min(elapsed / duration, 1);
                var eased = 1 - Math.pow(1 - progress, 3);
                var current = Math.round(eased * target);
                if (target === 999) {
                    el.textContent = (current / 10).toFixed(1) + '%';
                } else if (target === 247) {
                    el.textContent = '24/7';
                } else {
                    el.textContent = current + 'K+';
                }
                if (progress < 1) {
                    requestAnimationFrame(step);
                }
            }
            requestAnimationFrame(step);
        }

        // ── Scroll effects ──
        var progressBar = document.getElementById('scroll-progress');
        var navEl = document.querySelector('nav');
        var backToTop = document.getElementById('back-to-top');
        var ticking = false;

        function onScroll() {
            var scrollY = window.scrollY;
            var docHeight = document.documentElement.scrollHeight - window.innerHeight;
            var progress = docHeight > 0 ? (scrollY / docHeight) * 100 : 0;
            if (progressBar) progressBar.style.width = progress + '%';

            if (navEl) {
                if (scrollY > 80) {
                    navEl.classList.add('nav-scrolled');
                    document.body.classList.add('nav-fixed-pad');
                } else {
                    navEl.classList.remove('nav-scrolled');
                    document.body.classList.remove('nav-fixed-pad');
                }
            }

            if (backToTop) {
                if (scrollY > 500) {
                    backToTop.classList.add('visible');
                } else {
                    backToTop.classList.remove('visible');
                }
            }

            ticking = false;
        }

        window.addEventListener('scroll', function() {
            if (!ticking) {
                requestAnimationFrame(onScroll);
                ticking = true;
            }
        }, { passive: true });

        onScroll();

        // ── Parallax hero on mouse move ──
        var mockup = document.querySelector('.dashboard-mockup');
        if (mockup) {
            document.querySelector('.hero-right').addEventListener('mousemove', function(e) {
                var rect = this.getBoundingClientRect();
                var x = e.clientX - rect.left;
                var y = e.clientY - rect.top;
                var centerX = rect.width / 2;
                var centerY = rect.height / 2;
                var rotateX = (y - centerY) / 20;
                var rotateY = (centerX - x) / 20;
                mockup.style.transform = 'perspective(800px) rotateX(' + rotateX + 'deg) rotateY(' + rotateY + 'deg) translateY(-4px)';
            });

            document.querySelector('.hero-right').addEventListener('mouseleave', function() {
                mockup.style.transform = '';
            });
        }
    });
    </script>

    <!-- ── Privacy ── -->
    <div class="privacy-section" id="privacy">
        <div class="privacy-inner">
            <div class="reveal">
                <h2>{{ __('Your data is safe with us') }}</h2>
                <p>
                    {{ __('We take data protection seriously. Your business information is encrypted, backed up daily, and never shared with third parties.') }}
                </p>
                <p>
                    {{ __('SmartBiz uses industry-standard security practices to ensure your data remains confidential and available when you need it.') }}
                </p>
                <p>
                    <a href="{{ route('privacy') }}" style="color:#2563eb;text-decoration:none;font-weight:600;font-size:0.9rem;transition:opacity 0.2s;">
                        {{ __('Learn more about our privacy practices') }} <i class="fas fa-arrow-right" style="font-size:0.75rem;"></i>
                    </a>
                </p>
            </div>
            <div class="privacy-badges reveal reveal-delay-2">
                <div class="privacy-badge">
                    <i class="fas fa-lock"></i>
                    <div>
                        <h4>{{ __('Encryption') }}</h4>
                        <p>{{ __('256-bit SSL/TLS encryption') }}</p>
                    </div>
                </div>
                <div class="privacy-badge">
                    <i class="fas fa-user-shield"></i>
                    <div>
                        <h4>{{ __('Access Control') }}</h4>
                        <p>{{ __('Role-based permissions') }}</p>
                    </div>
                </div>
                <div class="privacy-badge">
                    <i class="fas fa-database"></i>
                    <div>
                        <h4>{{ __('Backups') }}</h4>
                        <p>{{ __('Automated daily backups') }}</p>
                    </div>
                </div>
                <div class="privacy-badge">
                    <i class="fas fa-eye-slash"></i>
                    <div>
                        <h4>{{ __('Privacy') }}</h4>
                        <p>{{ __('We never share your data') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── CTA ── -->
    <div class="cta-section reveal">
        <h2>{{ __('Start managing your business better') }}</h2>
        <p>{{ __('Join thousands of businesses already using SmartBiz.') }}</p>
        <a href="{{ route('register') }}">{{ __('Create Your Free Account') }} <i class="fas fa-arrow-right" style="font-size:0.85rem;"></i></a>
    </div>

    <!-- ── Footer ── -->
    <footer>
        &copy; {{ date('Y') }} SmartBiz. {{ __('All rights reserved') }}.
    </footer>

    <button id="back-to-top" onclick="window.scrollTo({top:0,behavior:'smooth'})" aria-label="{{ __('Back to top') }}">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script>
        if (navigator.userAgent.includes('Electron') || window.electronAPI) {
            document.body.classList.add('is-electron');
        }
    </script>
</body>
</html>
