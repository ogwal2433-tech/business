<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="manifest" href="{{ asset('manifest.json') }}">

<title>{{ __('SmartBiz | Powering Businesses') }}</title>

  <!-- Favicon -->
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('bus.png') }}">
  <link rel="shortcut icon" href="{{ asset('bus.png') }}">

  <!-- SEO Meta Tags -->
  <meta name="author" content="SmartBiz">
  <meta name="description" content="{{ __('SmartBiz revolutionizes how small businesses manage sales, inventory, and analytics with our intelligent, user-friendly platform designed for modern entrepreneurs.') }}">

  <!-- Open Graph (Social Sharing) -->
  <meta property="og:title" content="{{ __('SmartBiz | Transform Your Business Operations') }}">
  <meta property="og:description" content="{{ __('SmartBiz revolutionizes how small businesses manage sales, inventory, and analytics with our intelligent, user-friendly platform designed for modern entrepreneurs.') }}">
  <meta property="og:type" content="website">
  <meta property="og:url" content="{{ url()->current() }}">
  <meta property="og:image" content="{{ asset('bus.png') }}">

  <!-- Twitter Card -->
  <meta name="twitter:card" content="summary_large_image">

  <!-- PWA Meta Tags -->
  <meta name="theme-color" content="#2A6E8C">
  <link rel="manifest" href="{{ asset('manifest.json') }}">
  <link rel="apple-touch-icon" href="{{ asset('images/icon-192x192.png') }}">

  <!-- Preload Fonts and CSS -->
  <link rel="preload" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" as="style">
  <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" as="style">
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
  <link rel="dns-prefetch" href="https://unpkg.com">

  <!-- CSS Libraries -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

  <!-- JS Libraries -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

  <!-- Blade Head Section -->
  @yield('head')

  <!-- Custom CSS -->
    <style>
    html {
  font-size: 13px;
}

@media (max-width: 640px) {
    html { font-size: 14px; }
}

      /* ===== DARK THEME OVERRIDES ===== */
      [data-theme="dark"] body { background: #111827; color: #e5e7eb; }

      /* Layout chrome */
      [data-theme="dark"] .main-content-wrapper header {
        background: #1f2937 !important;
        border-color: #374151 !important;
      }

      [data-theme="dark"] .main-content-wrapper header .text-gray-700,
      [data-theme="dark"] .main-content-wrapper header .text-gray-600 {
        color: #d1d5db !important;
      }

      [data-theme="dark"] .main-content-wrapper header button {
        color: #d1d5db !important;
      }
      [data-theme="dark"] .main-content-wrapper header button:hover {
        background: #374151 !important;
        color: #f9fafb !important;
      }

      [data-theme="dark"] #mainContent {
        background: #111827 !important;
        color: #e5e7eb !important;
      }

      [data-theme="dark"] .main-content-wrapper footer {
        background: #1f2937 !important;
        border-color: #374151 !important;
        color: #9ca3af !important;
      }

      [data-theme="dark"] #sidebar {
        background: #0f172a !important;
      }

      [data-theme="dark"] #sidebar .border-b,
      [data-theme="dark"] #sidebar .border-t {
        border-color: #1e293b !important;
      }

      [data-theme="dark"] #sidebar .menu-item:hover {
        background: rgba(255, 255, 255, 0.08) !important;
      }

      [data-theme="dark"] #sidebar .menu-item.active {
        background: rgba(255, 255, 255, 0.12) !important;
      }

      [data-theme="dark"] #sidebar .sidebar-label { color: rgba(255, 255, 255, 0.35); }

      [data-theme="dark"] .sidebar-sub::before { background: rgba(255, 255, 255, 0.08); }

      /* ===== BOOTSTRAP OVERRIDES ===== */
      [data-theme="dark"] .card {
        background: #1f2937 !important;
        border-color: #374151 !important;
      }
      [data-theme="dark"] .card-header {
        background: #374151 !important;
        color: #f3f4f6 !important;
        border-color: #4b5563 !important;
      }
      [data-theme="dark"] .card-header.bg-primary {
        background: #1d4ed8 !important;
      }
      [data-theme="dark"] .card-header.bg-light {
        background: #374151 !important;
      }
      [data-theme="dark"] .card-body { color: #d1d5db; }
      [data-theme="dark"] .card.border-0 { border-color: transparent !important; }

      /* Tables */
      [data-theme="dark"] .table { color: #d1d5db; }
      [data-theme="dark"] .table thead th {
        background: #374151 !important;
        color: #f3f4f6 !important;
        border-color: #4b5563 !important;
      }
      [data-theme="dark"] .table tbody td,
      [data-theme="dark"] .table tbody th {
        border-color: #374151 !important;
      }
      [data-theme="dark"] .table tbody tr { background: transparent; }
      [data-theme="dark"] .table tbody tr:hover { background: rgba(255,255,255,0.03) !important; }
      [data-theme="dark"] .table-striped tbody tr:nth-of-type(odd) { background: rgba(255,255,255,0.02); }
      [data-theme="dark"] .table-hover tbody tr:hover { background: rgba(255,255,255,0.04) !important; }
      [data-theme="dark"] .table-light { background: #374151 !important; }
      [data-theme="dark"] .table-primary { background: #1e3a5f !important; color: #dbeafe !important; }
      [data-theme="dark"] .table-bordered { border-color: #374151 !important; }
      [data-theme="dark"] .table-responsive { border-color: #374151; }

      /* Forms */
      [data-theme="dark"] .form-control,
      [data-theme="dark"] .form-select,
      [data-theme="dark"] textarea.form-control {
        background: #111827 !important;
        border-color: #4b5563 !important;
        color: #e5e7eb !important;
      }
      [data-theme="dark"] .form-control:focus,
      [data-theme="dark"] .form-select:focus {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 0.2rem rgba(59,130,246,0.25) !important;
      }
      [data-theme="dark"] .form-control::placeholder { color: #6b7280 !important; }
      [data-theme="dark"] .form-control.bg-light { background: #111827 !important; }
      [data-theme="dark"] .form-label { color: #d1d5db; }
      [data-theme="dark"] .input-group-text {
        background: #374151 !important;
        border-color: #4b5563 !important;
        color: #d1d5db !important;
      }

      /* Buttons */
      [data-theme="dark"] .btn-light {
        background: #374151 !important;
        border-color: #4b5563 !important;
        color: #e5e7eb !important;
      }
      [data-theme="dark"] .btn-light:hover {
        background: #4b5563 !important;
        border-color: #6b7280 !important;
      }
      [data-theme="dark"] .btn-secondary {
        background: #4b5563 !important;
        border-color: #6b7280 !important;
      }
      [data-theme="dark"] .btn-outline-primary {
        border-color: #3b82f6 !important;
        color: #93c5fd !important;
      }
      [data-theme="dark"] .btn-outline-primary:hover {
        background: #1d4ed8 !important;
        color: #fff !important;
      }
      [data-theme="dark"] .btn-outline-success {
        border-color: #22c55e !important;
        color: #86efac !important;
      }
      [data-theme="dark"] .btn-outline-success:hover {
        background: #16a34a !important;
        color: #fff !important;
      }
      [data-theme="dark"] .btn-outline-danger {
        border-color: #ef4444 !important;
        color: #fca5a5 !important;
      }
      [data-theme="dark"] .btn-outline-danger:hover {
        background: #dc2626 !important;
        color: #fff !important;
      }
      [data-theme="dark"] .btn-outline-secondary {
        border-color: #6b7280 !important;
        color: #9ca3af !important;
      }
      [data-theme="dark"] .btn-outline-secondary:hover {
        background: #4b5563 !important;
        color: #fff !important;
      }

      /* Alerts */
      [data-theme="dark"] .alert-success {
        background: #064e3b !important;
        border-color: #065f46 !important;
        color: #a7f3d0 !important;
      }
      [data-theme="dark"] .alert-danger {
        background: #450a0a !important;
        border-color: #7f1d1d !important;
        color: #fecaca !important;
      }
      [data-theme="dark"] .alert-warning {
        background: #451a03 !important;
        border-color: #78350f !important;
        color: #fdba74 !important;
      }
      [data-theme="dark"] .alert-info {
        background: #0c4a6e !important;
        border-color: #0e7490 !important;
        color: #bae6fd !important;
      }

      /* Modals */
      [data-theme="dark"] .modal-content {
        background: #1f2937 !important;
        border-color: #374151 !important;
      }
      [data-theme="dark"] .modal-header {
        border-color: #374151 !important;
      }
      [data-theme="dark"] .modal-header .modal-title { color: #f3f4f6; }
      [data-theme="dark"] .modal-body { color: #d1d5db; }
      [data-theme="dark"] .modal-footer {
        border-color: #374151 !important;
      }
      [data-theme="dark"] .btn-close { filter: invert(0.8); }

      /* Badges */
      [data-theme="dark"] .badge.bg-primary { background: #1d4ed8 !important; }
      [data-theme="dark"] .badge.bg-success { background: #16a34a !important; }
      [data-theme="dark"] .badge.bg-danger { background: #dc2626 !important; }
      [data-theme="dark"] .badge.bg-warning { background: #d97706 !important; color: #111827; }
      [data-theme="dark"] .badge.bg-info { background: #0891b2 !important; }
      [data-theme="dark"] .badge.bg-secondary { background: #4b5563 !important; }

      /* Bootstrap utility overrides */
      [data-theme="dark"] .bg-white { background: #1f2937 !important; }
      [data-theme="dark"] .bg-light { background: #374151 !important; }
      [data-theme="dark"] .text-dark { color: #e5e7eb !important; }
      [data-theme="dark"] .text-danger { color: #fca5a5 !important; }
      [data-theme="dark"] .text-muted { color: #9ca3af !important; }
      [data-theme="dark"] .border { border-color: #374151 !important; }
      [data-theme="dark"] .border-top { border-top-color: #374151 !important; }
      [data-theme="dark"] .border-bottom { border-bottom-color: #374151 !important; }
      [data-theme="dark"] .shadow-sm { box-shadow: 0 1px 3px rgba(0,0,0,0.3) !important; }
      [data-theme="dark"] .shadow { box-shadow: 0 4px 6px rgba(0,0,0,0.3) !important; }
      [data-theme="dark"] .shadow-lg { box-shadow: 0 10px 15px rgba(0,0,0,0.4) !important; }
      [data-theme="dark"] hr { border-color: #374151; }

      /* Dropdowns */
      [data-theme="dark"] .dropdown-menu {
        background: #1f2937 !important;
        border-color: #374151 !important;
      }
      [data-theme="dark"] .dropdown-item { color: #d1d5db !important; }
      [data-theme="dark"] .dropdown-item:hover {
        background: #374151 !important;
        color: #f3f4f6 !important;
      }
      [data-theme="dark"] .dropdown-item.text-danger { color: #fca5a5 !important; }
      [data-theme="dark"] .dropdown-item.text-danger:hover { color: #fecaca !important; }
      [data-theme="dark"] .dropdown-divider { border-color: #374151 !important; }
      [data-theme="dark"] .dropdown-menu .text-gray-500 { color: #9ca3af !important; }

      /* ===== TAILWIND OVERRIDES ===== */
      [data-theme="dark"] .bg-white { background: #1f2937 !important; }
      [data-theme="dark"] .bg-gray-50 { background: #1f2937 !important; }
      [data-theme="dark"] .text-gray-900 { color: #f3f4f6 !important; }
      [data-theme="dark"] .text-gray-700 { color: #d1d5db !important; }
      [data-theme="dark"] .text-gray-600 { color: #9ca3af !important; }
      [data-theme="dark"] .text-gray-500 { color: #9ca3af !important; }
      [data-theme="dark"] .border-gray-200 { border-color: #374151 !important; }
      [data-theme="dark"] .divide-gray-200 > * { border-color: #374151 !important; }
      [data-theme="dark"] .hover\:bg-gray-50:hover { background: rgba(255,255,255,0.03) !important; }

      /* ===== GLOBAL TEXT READABILITY ===== */
      .text-gray-400 { color: #6b7280 !important; }
      .text-gray-500 { color: #4b5563 !important; }
      .text-gray-600 { color: #374151 !important; }
      [data-theme="dark"] .text-gray-400 { color: #d1d5db !important; }
      [data-theme="dark"] .text-gray-500 { color: #d1d5db !important; }
      [data-theme="dark"] .text-gray-600 { color: #e5e7eb !important; }

    /* CSS Variables for consistent theming */
    :root {
      --sidebar-width: 16rem;
      --sidebar-width-collapsed: 4rem;
      --transition-speed: 0.3s;
      --header-height: 64px;
      --footer-height: 48px;
    }

    /* Smooth transitions for all interactive elements */
    * {
      transition: background-color var(--transition-speed) ease,
                  color var(--transition-speed) ease,
                  border-color var(--transition-speed) ease;
    }

    /* Sidebar transitions */
    #sidebar {
      transition: all var(--transition-speed) cubic-bezier(0.4, 0, 0.2, 1);
      width: var(--sidebar-width);
      min-width: var(--sidebar-width);
      flex-shrink: 0;
      overflow-y: auto;
      overflow-x: hidden;
      height: 100vh;
      position: fixed;
      left: 0;
      top: 0;
      z-index: 50;
      box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
    }

    /* Collapsed sidebar state */
    #sidebar.collapsed {
      width: var(--sidebar-width-collapsed);
      min-width: var(--sidebar-width-collapsed);
    }

    /* Sidebar mobile behavior */
    @media (max-width: 767px) {
      #sidebar {
        transform: translateX(-100%);
      }
      #sidebar.open {
        transform: translateX(0);
      }
      #sidebar.collapsed {
        transform: translateX(-100%);
        width: var(--sidebar-width);
      }
    }

    /* Sidebar desktop behavior */
    @media (min-width: 768px) {
      #sidebar {
        transform: none !important;
        position: fixed !important;
      }
      #sidebar.collapsed {
        width: var(--sidebar-width-collapsed);
      }
    }

    /* Overlay for mobile sidebar */
    #sidebarOverlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: rgba(0, 0, 0, 0.5);
      z-index: 40;
      opacity: 0;
      transition: opacity var(--transition-speed) ease;
    }

    #sidebarOverlay.show {
      display: block;
      opacity: 1;
    }

    /* Main content adjustments */
    .main-content-wrapper {
      transition: margin-left var(--transition-speed) cubic-bezier(0.4, 0, 0.2, 1);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    @media (min-width: 768px) {
      .main-content-wrapper {
        margin-left: var(--sidebar-width);
      }

      .main-content-wrapper.sidebar-collapsed {
        margin-left: var(--sidebar-width-collapsed);
      }
    }

    /* Scrollable main content */
    #mainContent {
      overflow-y: auto;
      overflow-x: hidden;
      flex: 1;
      max-height: calc(100vh - var(--header-height) - var(--footer-height));
    }

    /* Sidebar content transitions */
    .sidebar-content {
      transition: opacity var(--transition-speed) ease,
                  visibility var(--transition-speed) ease;
    }

    #sidebar.collapsed .sidebar-content {
      opacity: 0;
      visibility: hidden;
    }

    /* Collapse/expand icon animation */
    .collapse-icon {
      transition: transform var(--transition-speed) ease;
    }

    #sidebar.collapsed .collapse-icon {
      transform: rotate(180deg);
    }

    /* Menu item icons in collapsed state */
    #sidebar.collapsed .menu-icon {
      justify-content: center;
      padding-left: 0;
      padding-right: 0;
    }

    /* ===== MODERN SIDEBAR MENU ===== */
    .menu-item {
      position: relative;
      border-radius: 10px;
      transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
      overflow: hidden;
    }

    .menu-item::before {
      content: '';
      position: absolute;
      left: 0;
      top: 0;
      bottom: 0;
      width: 3px;
      background: #60a5fa;
      border-radius: 0 3px 3px 0;
      transform: scaleY(0);
      transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .menu-item:hover::before,
    .menu-item.active::before {
      transform: scaleY(1);
    }

    .menu-item:hover {
      background: rgba(255, 255, 255, 0.1) !important;
      transform: translateX(3px);
    }

    .menu-item.active {
      background: rgba(255, 255, 255, 0.15) !important;
      box-shadow: inset 3px 0 0 #60a5fa;
    }

    .menu-item i {
      transition: transform 0.2s ease;
    }

    .menu-item:hover i {
      transform: scale(1.15);
    }

    .menu-item .sidebar-content {
      transition: opacity 0.2s ease, transform 0.2s ease;
    }

    .menu-item:hover .sidebar-content {
      transform: translateX(2px);
    }

    /* Sub-menu styling */
    .sidebar-sub {
      position: relative;
    }

    .sidebar-sub::before {
      content: '';
      position: absolute;
      left: 8px;
      top: 0;
      bottom: 0;
      width: 1.5px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 2px;
    }

    .sidebar-sub .menu-item {
      padding-left: 20px !important;
      font-size: 0.82rem;
    }

    .sidebar-sub .menu-item:hover {
      transform: translateX(2px);
    }

    /* Section label styling */
    .sidebar-label {
      font-size: 0.6rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: rgba(255, 255, 255, 0.4);
      padding: 16px 12px 6px;
      position: relative;
    }

    .sidebar-label::after {
      content: '';
      position: absolute;
      left: 12px;
      bottom: 2px;
      width: 20px;
      height: 1.5px;
      background: rgba(255, 255, 255, 0.15);
      border-radius: 2px;
    }

    /* Tooltip for collapsed sidebar items */
    .sidebar-tooltip {
      position: relative;
    }

    .sidebar-tooltip::after {
      content: attr(data-tooltip);
      position: absolute;
      left: 100%;
      top: 50%;
      transform: translateY(-50%);
      background: #1e293b;
      color: #f1f5f9;
      padding: 0.5rem 0.85rem;
      border-radius: 8px;
      font-size: 0.75rem;
      font-weight: 500;
      white-space: nowrap;
      opacity: 0;
      visibility: hidden;
      transition: all 0.2s ease;
      margin-left: 0.75rem;
      z-index: 60;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
      letter-spacing: 0.01em;
    }

    #sidebar.collapsed .sidebar-tooltip:hover::after {
      opacity: 1;
      visibility: visible;
      transform: translateY(-50%) translateX(3px);
    }

    /* Collapsed state indicator */
    #sidebar.collapsed .menu-item.active {
      box-shadow: inset 3px 0 0 #60a5fa;
      background: rgba(255, 255, 255, 0.12) !important;
    }

    /* Sidebar header with glass effect */
    #sidebar .sidebar-header {
      background: rgba(0, 0, 0, 0.15);
      backdrop-filter: blur(4px);
    }

    /* ===== SETTINGS DROPDOWN ===== */
    .settings-btn { transition: all 0.2s; }
    .settings-btn:hover { transform: rotate(30deg); }

    .settings-dropdown {
      min-width: 240px;
      border-radius: 12px !important;
      padding: 8px !important;
      background: #ffffff !important;
      box-shadow: 0 10px 40px rgba(0,0,0,0.12) !important;
    }

    .settings-header {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 12px 12px 16px;
      border-bottom: 1px solid #f1f5f9;
      margin-bottom: 4px;
    }

    .settings-avatar {
      width: 38px;
      height: 38px;
      border-radius: 10px;
      background: linear-gradient(135deg, #2563eb, #1d4ed8);
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 0.85rem;
      flex-shrink: 0;
    }

    .settings-user-info { line-height: 1.3; }
    .settings-user-info strong { display: block; font-size: 0.85rem; color: #0f172a; }
    .settings-user-info span { font-size: 0.75rem; color: #94a3b8; }

    .settings-section-label {
      font-size: 0.65rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: #94a3b8;
      padding: 6px 12px 4px;
    }

    .settings-lang-list { display: flex; flex-direction: column; gap: 2px; padding: 0 4px; }

    .settings-lang-item {
      display: flex;
      align-items: center;
      gap: 10px;
      width: 100%;
      border: none;
      background: transparent;
      padding: 8px 10px;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.15s;
      font-family: inherit;
      font-size: 0.85rem;
      color: #334155;
      text-align: left;
    }

    .settings-lang-item:hover { background: #f1f5f9; }
    .settings-lang-item.current { background: #eff6ff; color: #2563eb; font-weight: 600; }

    .settings-lang-flag { font-size: 1.1rem; line-height: 1; }
    .settings-lang-name { flex-grow: 1; }
    .settings-lang-check { color: #2563eb; font-size: 0.7rem; }

    .settings-divider {
      height: 1px;
      background: #f1f5f9;
      margin: 4px 0;
    }

    .settings-item {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 10px 12px;
      border-radius: 8px;
      text-decoration: none;
      color: #334155;
      font-size: 0.85rem;
      transition: all 0.15s;
      width: 100%;
      border: none;
      background: transparent;
      font-family: inherit;
      cursor: pointer;
    }

    .settings-item:hover { background: #f1f5f9; color: #0f172a; }
    .settings-item-icon { width: 18px; text-align: center; font-size: 0.95rem; color: #64748b; }
    .settings-item:hover .settings-item-icon { color: #2563eb; }

    .settings-logout { color: #dc2626 !important; }
    .settings-logout .settings-item-icon { color: #fca5a5 !important; }
    .settings-logout:hover { background: #fef2f2 !important; color: #b91c1c !important; }
    .settings-logout:hover .settings-item-icon { color: #ef4444 !important; }

    /* Dark mode dropdown */
    [data-theme="dark"] .settings-dropdown { background: #1f2937 !important; }
    [data-theme="dark"] .settings-header { border-color: #374151; }
    [data-theme="dark"] .settings-user-info strong { color: #f3f4f6; }
    [data-theme="dark"] .settings-user-info span { color: #6b7280; }
    [data-theme="dark"] .settings-section-label { color: #6b7280; }
    [data-theme="dark"] .settings-lang-item { color: #d1d5db; }
    [data-theme="dark"] .settings-lang-item:hover { background: #374151; }
    [data-theme="dark"] .settings-lang-item.current { background: #1e3a5f; color: #93c5fd; }
    [data-theme="dark"] .settings-lang-check { color: #93c5fd; }
    [data-theme="dark"] .settings-divider { background: #374151; }
    [data-theme="dark"] .settings-item { color: #d1d5db; }
    [data-theme="dark"] .settings-item:hover { background: #374151; color: #f3f4f6; }
    [data-theme="dark"] .settings-item-icon { color: #6b7280; }
    [data-theme="dark"] .settings-item:hover .settings-item-icon { color: #60a5fa; }
    [data-theme="dark"] .settings-logout { color: #fca5a5 !important; }
    [data-theme="dark"] .settings-logout .settings-item-icon { color: #ef4444 !important; }
    [data-theme="dark"] .settings-logout:hover { background: #450a0a !important; color: #fecaca !important; }
    [data-theme="dark"] .settings-logout:hover .settings-item-icon { color: #f87171 !important; }

    /* ===== RESPONSIVE BUTTONS ===== */
    @media (max-width: 640px) {
      .btn, button:not(.btn-close):not(.chat-btn):not([class*="ts"]):not(.ts-wrapper *) {
        font-size: 0.875rem;
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
      }
      .btn:not(.btn-sm):not(.btn-xs):not(.btn-lg) {
        min-height: 40px;
      }
      .btn-group {
        flex-wrap: wrap;
      }
      .btn-group .btn {
        flex: 1 1 auto;
      }
      .btn-group-vertical .btn {
        min-height: 40px;
      }
      .modal-footer .btn {
        flex: 1 1 auto;
        min-width: 0;
      }
      .modal-footer .btn + .btn {
        margin-left: 0.5rem;
      }
      .input-group .btn {
        min-height: 40px;
      }
    }

    @media (max-width: 480px) {
      .btn, button:not(.btn-close):not(.chat-btn):not([class*="ts"]):not(.ts-wrapper *) {
        font-size: 0.8125rem;
      }
      .btn:not(.btn-sm):not(.btn-xs):not(.btn-lg) {
        padding-left: 0.75rem;
        padding-right: 0.75rem;
      }
      .modal-footer {
        flex-direction: column;
        gap: 0.5rem;
      }
      .modal-footer .btn {
        width: 100%;
        margin-left: 0 !important;
      }
      .btn-group {
        flex-direction: column;
        width: 100%;
      }
      .btn-group .btn {
        border-radius: 0.375rem !important;
        margin-bottom: 0.25rem;
      }
      .btn-group .btn:not(:first-child) {
        border-left-width: 1px;
      }
      .input-group {
        flex-wrap: wrap;
      }
      .input-group .btn {
        width: 100%;
        border-radius: 0.375rem !important;
        margin-top: 0.25rem;
      }
      .input-group .form-control,
      .input-group .form-select {
        width: 100% !important;
      }
      .d-flex.gap-2.flex-wrap.justify-content-end .btn,
      .d-flex.gap-2 .btn,
      .flex-wrap .btn {
        flex: 1 1 auto;
        min-width: 0;
      }
      .btn-group-vertical .btn {
        min-height: 44px;
      }
      .card-body .d-flex.gap-2 {
        flex-direction: column;
      }
      .card-body .d-flex.gap-2 .btn {
        width: 100%;
      }
    }

    @media (max-width: 360px) {
      .btn, button:not(.btn-close):not(.chat-btn) {
        font-size: 0.75rem;
        padding-left: 0.5rem;
        padding-right: 0.5rem;
      }
      .btn:not(.btn-sm):not(.btn-xs):not(.btn-lg) {
        min-height: 38px;
      }
    }
    /* ===== DESKTOP APP (ELECTRON) ===== */
    body.electron-app {
      padding-top: 0;
    }
    body.electron-app header {
      -webkit-app-region: drag;
    }
    body.electron-app header button,
    body.electron-app header a,
    body.electron-app .no-drag {
      -webkit-app-region: no-drag;
    }
    body.electron-app .desktop-badge {
      display: inline-flex;
    }
    .desktop-badge {
      display: none;
      align-items: center;
      gap: 4px;
      font-size: 0.65rem;
      color: #6b7280;
      background: rgba(255,255,255,0.5);
      padding: 2px 8px;
      border-radius: 4px;
      margin-left: 6px;
    }
    [data-theme="dark"] .desktop-badge {
      background: rgba(255,255,255,0.08);
      color: #9ca3af;
    }
  </style>

  <script>
    // Detect Electron environment
    if (navigator.userAgent.toLowerCase().indexOf('electron') !== -1 || window.electronAPI) {
      document.addEventListener('DOMContentLoaded', function() {
        document.body.classList.add('electron-app');
      });
    }
  </script>

</head>


<body class="bg-white text-gray-800">
  <!-- Overlay for mobile sidebar -->
  <div id="sidebarOverlay" class="md:hidden"></div>

  <!-- Sidebar -->
  <nav id="sidebar"
       class="fixed top-0 left-0 h-screen bg-blue-900 text-white z-50 flex flex-col">

    <!-- Sidebar Header with Toggle -->
    <div class="sidebar-header p-4 border-b border-blue-800 flex items-center justify-between">
      <!-- Branding (shown when expanded) -->
      <div class="sidebar-content text-xl font-bold tracking-wide whitespace-nowrap overflow-hidden">
        <h2>{{ __('SmartBiz') }}</h2>
      </div>

      <!-- Collapse Toggle Button -->
      <button id="sidebarCollapse"
              class="collapse-icon text-white hover:text-blue-200 focus:outline-none p-2 rounded-lg hover:bg-blue-800"
               aria-label="{{ __('Toggle Sidebar') }}">
        <i class="fas fa-chevron-left"></i>
      </button>
    </div>

    <!-- Menu Content -->
    <div class="flex-1 overflow-y-auto px-3 py-4">
      <ul class="space-y-2">
        @auth
        @php $role = Auth::user()->role; @endphp

        @if($role === 'super_admin')
        <li>
            <h4 class="sidebar-label sidebar-content">{{ __('System Admin') }}</h4>
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('system-admin.dashboard') }}"
                       data-tooltip="{{ __('System Dashboard') }}"
                       class="sidebar-tooltip menu-item flex items-center gap-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('system-admin.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt w-5 text-center"></i>
                        <span class="sidebar-content whitespace-nowrap">{{ __('Dashboard') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('system-admin.businesses') }}"
                       data-tooltip="{{ __('Businesses') }}"
                       class="sidebar-tooltip menu-item flex items-center gap-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('system-admin.businesses*') ? 'active' : '' }}">
                        <i class="fas fa-store w-5 text-center"></i>
                        <span class="sidebar-content whitespace-nowrap">{{ __('Businesses') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('system-admin.subscriptions') }}"
                       data-tooltip="{{ __('Subscriptions') }}"
                       class="sidebar-tooltip menu-item flex items-center gap-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('system-admin.subscriptions*') ? 'active' : '' }}">
                        <i class="fas fa-tag w-5 text-center"></i>
                        <span class="sidebar-content whitespace-nowrap">{{ __('Subscriptions') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('system-admin.plans') }}"
                       data-tooltip="{{ __('Plans') }}"
                       class="sidebar-tooltip menu-item flex items-center gap-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('system-admin.plans*') ? 'active' : '' }}">
                        <i class="fas fa-box w-5 text-center"></i>
                        <span class="sidebar-content whitespace-nowrap">{{ __('Plans') }}</span>
                    </a>
                </li>
            </ul>
        </li>

        @elseif($role === 'admin')
        <li>
          <h4 class="sidebar-label sidebar-content">{{ __('Menu for :name', ['name' => Auth::user()->name]) }}</h4>
          <ul class="space-y-1">

            <!-- Dashboard -->
            <li>
              <a href="{{ route('admin.dashboard') }}"
                 data-tooltip="{{ __('Dashboard') }}"
                 class="sidebar-tooltip menu-item flex items-center gap-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-line w-5 text-center"></i>
                <span class="sidebar-content whitespace-nowrap">{{ __('Dashboard') }}</span>
              </a>
            </li>

            <!-- Inventory -->
            <li>
              <div class="sidebar-label sidebar-content">{{ __('Inventory') }}</div>
              <ul class="sidebar-sub space-y-1">
                <li>
                  <a href="{{ route('inventory.upload.form') }}"
                     data-tooltip="{{ __('Upload Goods/Products') }}"
                     class="sidebar-tooltip menu-item flex items-center gap-3 py-2 rounded-lg {{ request()->routeIs('inventory.upload.form') ? 'active' : '' }}">
                    <i class="fas fa-file-upload w-5 text-center"></i>
                    <span class="sidebar-content whitespace-nowrap">{{ __('Upload Goods/Products') }}</span>
                  </a>
                </li>
                <li>
                  <a href="/inventory/listings"
                     data-tooltip="{{ __('View Products/Goods') }}"
                     class="sidebar-tooltip menu-item flex items-center gap-3 py-2 rounded-lg {{ request()->routeIs('inventory.list') ? 'active' : '' }}">
                    <i class="fas fa-boxes w-5 text-center"></i>
                    <span class="sidebar-content whitespace-nowrap">{{ __('View Products/Goods') }}</span>
                  </a>
                </li>
                <li>
                  <a href="{{ route('inventory.adjust.form') }}"
                     data-tooltip="{{ __('Adjust Quantity Instock') }}"
                     class="sidebar-tooltip menu-item flex items-center gap-3 py-2 rounded-lg {{ request()->routeIs('inventory.adjust.form') ? 'active' : '' }}">
                    <i class="fas fa-edit w-5 text-center"></i>
                    <span class="sidebar-content whitespace-nowrap">{{ __('Adjust Quantity Instock') }}</span>
                  </a>
                </li>
                <li>
                  <a href="{{ route('inventory.history') }}"
                     data-tooltip="{{ __('View Stock Movement') }}"
                     class="sidebar-tooltip menu-item flex items-center gap-3 py-2 rounded-lg {{ request()->routeIs('inventory.history') ? 'active' : '' }}">
                    <i class="fas fa-history w-5 text-center"></i>
                    <span class="sidebar-content whitespace-nowrap">{{ __('View Stock Movement') }}</span>
                  </a>
                </li>
              </ul>
            </li>

            <!-- Employees -->
            <li>
              <a href="{{ route('admin.employees.create') }}"
                 data-tooltip="{{ __('Grant Employee Access') }}"
                 class="sidebar-tooltip menu-item flex items-center gap-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('admin.employees.create') ? 'active' : '' }}">
                <i class="fas fa-users w-5 text-center"></i>
                <span class="sidebar-content whitespace-nowrap">{{ __('Grant Employee Access') }}</span>
              </a>
            </li>

            <!-- Subscription -->
            <li>
              <a href="{{ route('admin.subscription.my') }}"
                 data-tooltip="{{ __('My Subscription') }}"
                 class="sidebar-tooltip menu-item flex items-center gap-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('admin.subscription.my') ? 'active' : '' }}">
                <i class="fas fa-tag w-5 text-center"></i>
                <span class="sidebar-content whitespace-nowrap">{{ __('My Subscription') }}</span>
              </a>
            </li>

            <!-- Record Sale -->
            <li>
              <a href="{{ route('admin.sales.create') }}"
                 data-tooltip="{{ __('Record Sale') }}"
                 class="sidebar-tooltip menu-item flex items-center gap-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('admin.sales.create') ? 'active' : '' }}">
                <i class="fas fa-plus-circle w-5 text-center"></i>
                <span class="sidebar-content whitespace-nowrap">{{ __('Record Sale') }}</span>
              </a>
       <li>
  <a href="/expenses"
     data-tooltip="{{ __('Employee Expense') }}"
     class="sidebar-tooltip menu-item flex items-center gap-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('expenses.index') ? 'active' : '' }}">
    <i class="fas fa-receipt w-5 text-center"></i>
    <span class="sidebar-content whitespace-nowrap">{{ __('Employee Expense') }}</span>
  </a>
</li>

            <!-- My Sales -->
            {{-- <li>
              <a href="/reports/adminRecord"
                 data-tooltip="My Sales"
                 class="sidebar-tooltip menu-item flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-blue-800 {{ request()->routeIs('admin.sales.*') ? 'bg-blue-800 active' : '' }}">
                <i class="fas fa-shopping-cart w-5 text-center"></i>
                <span class="sidebar-content whitespace-nowrap">My Sales</span>
              </a>
            </li> --}}



            <!-- Record Business Cost -->
            <li>
              <a href="/admin/operational-costs"
                 data-tooltip="{{ __('Record Business Cost') }}"
                 class="sidebar-tooltip menu-item flex items-center gap-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('admin.sales') ? 'active' : '' }}">
                <i class="fas fa-receipt w-5 text-center"></i>
                <span class="sidebar-content whitespace-nowrap">{{ __('Record Business Cost') }}</span>
              </a>
            </li>

            <!-- Business Reports -->
            <li>
              <div class="sidebar-label sidebar-content">{{ __('Business Reports') }}</div>
              <ul class="sidebar-sub space-y-1">
                @if(Auth::user()->planHasFeature('advanced_analytics'))
                <li>
                  <a href="{{ route('admin.analytics') }}"
                     data-tooltip="{{ __('Analytics') }}"
                     class="sidebar-tooltip menu-item flex items-center gap-3 py-2 rounded-lg {{ request()->routeIs('admin.analytics') ? 'active' : '' }}">
                    <i class="fas fa-chart-pie w-5 text-center"></i>
                    <span class="sidebar-content whitespace-nowrap">{{ __('Analytics') }}</span>
                  </a>
                </li>
                @endif
                <li>
                  <a href="/reports/adminsale"
                     data-tooltip="{{ __('Sales Report') }}"
                     class="sidebar-tooltip menu-item flex items-center gap-3 py-2 rounded-lg {{ request()->is('reports/adminsale') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar w-5 text-center"></i>
                    <span class="sidebar-content whitespace-nowrap">{{ __('Sales Report') }}</span>
                  </a>
                </li>
                <li>
                  <a href="/reports/EmpRecord"
                     data-tooltip="{{ __('Employee Sales') }}"
                     class="sidebar-tooltip menu-item flex items-center gap-3 py-2 rounded-lg {{ request()->is('reports/EmpRecord') ? 'active' : '' }}">
                    <i class="fas fa-users w-5 text-center"></i>
                    <span class="sidebar-content whitespace-nowrap">{{ __('Employee Sales') }}</span>
                  </a>
                </li>
                <li>
                  <a href="/inventory"
                     data-tooltip="{{ __('Inventory Report') }}"
                     class="sidebar-tooltip menu-item flex items-center gap-3 py-2 rounded-lg {{ request()->is('inventory') || request()->is('inventory/listings') ? 'active' : '' }}">
                    <i class="fas fa-boxes w-5 text-center"></i>
                    <span class="sidebar-content whitespace-nowrap">{{ __('Inventory Report') }}</span>
                  </a>
                </li>
                @if(Auth::user()->planHasFeature('credit_sales'))
                <li>
                  <a href="/credit-sales"
                     data-tooltip="{{ __('Credit Sales') }}"
                     class="sidebar-tooltip menu-item flex items-center gap-3 py-2 rounded-lg {{ request()->is('credit-sales') ? 'active' : '' }}">
                    <i class="fas fa-credit-card w-5 text-center"></i>
                    <span class="sidebar-content whitespace-nowrap">{{ __('Credit Sales') }}</span>
                  </a>
                </li>
                @endif
              </ul>
            </li>

            <!-- View Messages -->
            {{-- <li>
              <a href="{{ route('admin.messages.index') }}"
                 data-tooltip="{{ __('Messages') }}"
                 class="sidebar-tooltip menu-item flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-blue-800 {{ request()->routeIs('employee.messages.create') ? 'bg-blue-800 active' : '' }}">
                <i class="fas fa-envelope w-5 text-center"></i>
                <span class="sidebar-content whitespace-nowrap">{{ __('Messages') }}</span>
              </a>
            </li> --}}
          </ul>
        </li>

        @elseif($role === 'employee')
        <li>
          <h4 class="sidebar-label sidebar-content">{{ __('Employee Menu') }}</h4>
          <ul class="space-y-1">
            <li>
              <a href="{{ route('employee.dashboard') }}"
                 data-tooltip="{{ __('Dashboard') }}"
                 class="sidebar-tooltip menu-item flex items-center gap-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('employee.dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-pie w-5 text-center"></i>
                <span class="sidebar-content whitespace-nowrap">{{ __('Dashboard') }}</span>
              </a>
            </li>
            <li>
              <a href="{{ route('employee.sales.create') }}"
                 data-tooltip="{{ __('Record Sale') }}"
                 class="sidebar-tooltip menu-item flex items-center gap-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('employee.sales.create') ? 'active' : '' }}">
                <i class="fas fa-plus-circle w-5 text-center"></i>
                <span class="sidebar-content whitespace-nowrap">{{ __('Record Sale') }}</span>
              </a>
            </li>
            <li>
              <a href="{{ route('employee.sales.history') }}"
                 data-tooltip="{{ __('Sales History') }}"
                 class="sidebar-tooltip menu-item flex items-center gap-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('employee.sales.history') ? 'active' : '' }}">
                <i class="fas fa-history w-5 text-center"></i>
                <span class="sidebar-content whitespace-nowrap">{{ __('Sales History') }}</span>
              </a>
            </li>
            <li>
              <a href="{{ route('employee.prices') }}"
                 data-tooltip="{{ __('Product Prices') }}"
                 class="sidebar-tooltip menu-item flex items-center gap-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('employee.prices') ? 'active' : '' }}">
                <i class="fas fa-tags w-5 text-center"></i>
                <span class="sidebar-content whitespace-nowrap">{{ __('Product Prices') }}</span>
              </a>
            </li>
            {{-- <li>
              <a href="{{ route('chat.index') }}"
                 data-tooltip="{{ __('Messages') }}"
                 class="sidebar-tooltip menu-item flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-blue-800 {{ request()->routeIs('employee.messages.create') ? 'bg-blue-800 active' : '' }}">
                <i class="fas fa-envelope w-5 text-center"></i>
                <span class="sidebar-content whitespace-nowrap">{{ __('Messages') }}</span>
              </a>
            </li> --}}
          </ul>
        </li>
        @endif

        @else
        <!-- Guest -->
        <li>
          <h4 class="sidebar-label sidebar-content">{{ __('Guest') }}</h4>
          <a href="{{ route('login') }}"
             data-tooltip="{{ __('Login') }}"
             class="sidebar-tooltip menu-item flex items-center gap-3 px-4 py-2.5 rounded-lg">
            <i class="fas fa-sign-in-alt w-5 text-center"></i>
            <span class="sidebar-content whitespace-nowrap">{{ __('Login') }}</span>
          </a>
        </li>
        @endauth
      </ul>
    </div>

   <!-- Sidebar Footer -->
<div class="p-4 border-t border-blue-800 sidebar-content">
  <div class="text-xs text-blue-300 text-center">
    {{ __('SmartBiz') }} &copy; <span id="currentYear"></span>
  </div>
</div>

<script>
  // Set current year
  document.getElementById('currentYear').textContent = new Date().getFullYear();

  // Function to format date as "Mon Feb 15 4:21:35 AM"
  function formatDateTime(date) {
    const options = {
      weekday: 'short',
      month: 'short',
      day: 'numeric',
      hour: 'numeric',
      minute: 'numeric',
      second: 'numeric',
      hour12: true
    };
    return date.toLocaleString('en-US', options);
  }

  // Function to update date/time every second
  function updateDateTime() {
    const now = new Date();
    document.getElementById('currentDateTime').textContent = formatDateTime(now);
  }

  // Initial call
  updateDateTime();

  // Update every second
  setInterval(updateDateTime, 1000);
</script>

  </nav>

  <!-- Main content area -->
  <div id="mainContentWrapper" class="main-content-wrapper">
    <!-- Header -->
    <header class="flex items-center justify-between px-6 py-4 border-b bg-white shadow-sm sticky top-0 z-40">
      <div class="flex items-center gap-4">
        <!-- Hamburger menu for mobile -->
        <button id="sidebarToggle"
                class="md:hidden text-gray-700 hover:text-gray-900 focus:outline-none p-2 rounded-lg hover:bg-gray-100"
                aria-label="Toggle Sidebar">
          <i class="fas fa-bars text-xl"></i>
        </button>

        <!-- Expand button for desktop (only visible when sidebar is collapsed) -->
        <button id="sidebarExpand"
                class="hidden md:flex items-center gap-2 text-gray-700 hover:text-gray-900 focus:outline-none p-2 rounded-lg hover:bg-gray-100"
                aria-label="{{ __('Expand Sidebar') }}">
          <i class="fas fa-chevron-right"></i>
          <span class="text-sm font-medium">{{ __('Menu') }}</span>
        </button>

        <div class="text-xl font-semibold">{{ __('SmartBiz Dashboard') }}</div>
      </div>

      <div class="flex items-center justify-end gap-4">
        <!-- Dark mode toggle button - KEEPING ORIGINAL TOGGLE LOGIC -->
        <button id="darkModeToggle"
                title="{{ __('Toggle Dark Mode') }}"
                class="text-xl text-gray-600 hover:text-gray-900 focus:outline-none p-2 rounded-lg hover:bg-gray-100">
          <i class="fas fa-moon"></i>
        </button>

        @auth
        <div class="dropdown">
          <button
            id="settingsDropdown"
            data-bs-toggle="dropdown"
            aria-expanded="false"
            title="{{ __('Settings') }}"
            class="settings-btn text-xl text-gray-600 hover:text-gray-900 focus:outline-none p-2 rounded-lg hover:bg-gray-100">
            <i class="fas fa-cog"></i>
          </button>
          <ul class="settings-dropdown dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2" aria-labelledby="settingsDropdown">
            <!-- User Header -->
            <li class="settings-header">
              <div class="settings-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
              <div class="settings-user-info">
                <strong>{{ Auth::user()->name }}</strong>
                <span>{{ Auth::user()->email ?? Auth::user()->username }}</span>
              </div>
            </li>

            <!-- Language -->
            <li class="settings-section-label">{{ __('System Language') }}</li>
            @php $currentLang = app()->getLocale(); @endphp
            <li class="settings-lang-list">
              @foreach(['en' => ['🇬🇧', 'English'], 'sw' => ['🇹🇿', 'Kiswahili'], 'ar' => ['🇸🇦', 'العربية']] as $code => $lang)
              <form action="{{ route('admin.settings.language.update') }}" method="POST" class="m-0">
                @csrf
                <input type="hidden" name="language" value="{{ $code }}">
                <button type="submit" class="settings-lang-item {{ $currentLang === $code ? 'current' : '' }}">
                  <span class="settings-lang-flag">{{ $lang[0] }}</span>
                  <span class="settings-lang-name">{{ __($lang[1]) }}</span>
                  @if($currentLang === $code)
                  <span class="settings-lang-check"><i class="fas fa-check"></i></span>
                  @endif
                </button>
              </form>
              @endforeach
            </li>

            <li class="settings-divider"></li>

            <!-- User Settings -->
            <li><a class="settings-item" href="{{ route('profile.index') }}"><i class="fas fa-user-cog settings-item-icon"></i> {{ __('User Settings') }}</a></li>

            <li class="settings-divider"></li>

            <!-- Logout -->
            <li>
              <form action="{{ route('logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="settings-item settings-logout"><i class="fas fa-sign-out-alt settings-item-icon"></i> {{ __('Logout') }}</button>
              </form>
            </li>
          </ul>
        </div>
        @else
        <a href="{{ route('login') }}"
           class="btn bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm transition-colors duration-200">
          {{ __('Login') }}
        </a>
        @endauth
      </div>
    </header>

    <!-- Main Content -->
    <main id="mainContent" class="flex-1 p-6 bg-gray-50 text-gray-900 transition-colors duration-300">
      @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 text-center text-sm text-gray-600 py-4">
      <div class="container mx-auto px-4">
<strong>&copy; {{ date('Y') }} {{ __('SmartBiz') }}. {{ __('All rights reserved') }} | <em>{{ __('powering your business') }}</em></strong>

      </div>
    </footer>
  </div>

  <!-- Scripts -->
  <script>
    // DOM Elements
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarCollapse = document.getElementById('sidebarCollapse');
    const sidebarExpand = document.getElementById('sidebarExpand');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const mainContentWrapper = document.getElementById('mainContentWrapper');
    const mainContent = document.getElementById('mainContent');
    const darkModeToggle = document.getElementById('darkModeToggle');

    // State
    const SIDEBAR_STATE_KEY = 'smartbiz_sidebar_collapsed';

    // Initialize from localStorage
    function initializeFromStorage() {
      // Sidebar state
      const isCollapsed = localStorage.getItem(SIDEBAR_STATE_KEY) === 'true';
      if (isCollapsed) {
        collapseSidebar();
      }

      // Dark mode initialization
      const savedTheme = localStorage.getItem('smartbiz_theme');
      if (savedTheme) {
        setTheme(savedTheme);
      } else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        setTheme('dark');
      } else {
        setTheme('light');
      }
    }

    function setTheme(theme) {
      document.documentElement.setAttribute('data-theme', theme);
      localStorage.setItem('smartbiz_theme', theme);
      // Update main content classes for Bootstrap compatibility
      if (theme === 'dark') {
        mainContent.classList.remove('bg-gray-50', 'text-gray-900');
        mainContent.classList.add('bg-gray-900', 'text-white');
      } else {
        mainContent.classList.remove('bg-gray-900', 'text-white');
        mainContent.classList.add('bg-gray-50', 'text-gray-900');
      }
    }

    // Toggle sidebar open/close on mobile
    function toggleMobileSidebar() {
      sidebar.classList.toggle('open');
      sidebarOverlay.classList.toggle('show');
    }

    // Collapse/Expand sidebar on desktop
    function toggleSidebar() {
      if (window.innerWidth >= 768) {
        if (sidebar.classList.contains('collapsed')) {
          expandSidebar();
        } else {
          collapseSidebar();
        }
        // Save state
        localStorage.setItem(SIDEBAR_STATE_KEY, sidebar.classList.contains('collapsed'));
      } else {
        toggleMobileSidebar();
      }
    }

    // Collapse sidebar
    function collapseSidebar() {
      sidebar.classList.add('collapsed');
      mainContentWrapper.classList.add('sidebar-collapsed');
      sidebarExpand.classList.remove('hidden');
    }

    // Expand sidebar
    function expandSidebar() {
      sidebar.classList.remove('collapsed');
      mainContentWrapper.classList.remove('sidebar-collapsed');
      sidebarExpand.classList.add('hidden');
    }

    // Close sidebar on mobile
    function closeMobileSidebar() {
      sidebar.classList.remove('open');
      sidebarOverlay.classList.remove('show');
    }

    // Dark mode toggle
    darkModeToggle.addEventListener('click', () => {
      const current = document.documentElement.getAttribute('data-theme');
      setTheme(current === 'dark' ? 'light' : 'dark');
    });

    // Event Listeners
    sidebarToggle.addEventListener('click', (e) => {
      e.stopPropagation();
      toggleMobileSidebar();
    });

    sidebarCollapse.addEventListener('click', (e) => {
      e.stopPropagation();
      toggleSidebar();
    });

    sidebarExpand.addEventListener('click', (e) => {
      e.stopPropagation();
      expandSidebar();
      localStorage.setItem(SIDEBAR_STATE_KEY, 'false');
    });

    sidebarOverlay.addEventListener('click', closeMobileSidebar);

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', (e) => {
      if (window.innerWidth < 768 &&
          sidebar.classList.contains('open') &&
          !sidebar.contains(e.target) &&
          !sidebarToggle.contains(e.target)) {
        closeMobileSidebar();
      }
    });

    // Close mobile sidebar when clicking on a link
    document.querySelectorAll('#sidebar a').forEach(link => {
      link.addEventListener('click', () => {
        if (window.innerWidth < 768) {
          closeMobileSidebar();
        }
      });
    });

    // Handle window resize
    function handleResize() {
      if (window.innerWidth >= 768) {
        // On desktop, ensure sidebar is visible and overlay is hidden
        sidebar.classList.remove('open');
        sidebarOverlay.classList.remove('show');

        // Show/hide expand button based on sidebar state
        if (sidebar.classList.contains('collapsed')) {
          sidebarExpand.classList.remove('hidden');
        } else {
          sidebarExpand.classList.add('hidden');
        }
      } else {
        // On mobile, hide expand button
        sidebarExpand.classList.add('hidden');
      }
    }

    // Initialize
    initializeFromStorage();
    handleResize();

    // Listen for resize events
    window.addEventListener('resize', handleResize);

    // Add keyboard shortcuts
    document.addEventListener('keydown', (e) => {
      // Ctrl + B or Cmd + B to toggle sidebar
      if ((e.ctrlKey || e.metaKey) && e.key === 'b') {
        e.preventDefault();
        toggleSidebar();
      }
    });

    // Global AJAX 401 handler (single-session enforcement)
    (function() {
      let redirecting = false;
      document.addEventListener('click', function(e) {
        var link = e.target.closest('a[href]');
        if (link && link.href.includes('/logout')) return;
      });

      var origOpen = XMLHttpRequest.prototype.open;
      XMLHttpRequest.prototype.open = function() {
        this.addEventListener('load', function() {
          if (this.status === 401 && !redirecting) {
            try {
              var resp = JSON.parse(this.responseText);
              if (resp.logout) {
                redirecting = true;
                if (window.electronAPI) {
                  window.electronAPI.notification('Logged out from another device');
                }
                window.location.href = '/login?logout=other_device';
              }
            } catch(e) {}
          }
        });
        origOpen.apply(this, arguments);
      };

      var origFetch = window.fetch;
      if (origFetch) {
        window.fetch = function() {
          return origFetch.apply(this, arguments).then(function(response) {
            if (response.status === 401 && !redirecting) {
              response.clone().json().then(function(data) {
                if (data && data.logout) {
                  redirecting = true;
                  if (window.electronAPI) {
                    window.electronAPI.notification('Logged out from another device');
                  }
                  window.location.href = '/login?logout=other_device';
                }
              }).catch(function(){});
            }
            return response;
          });
        };
      }
    })();
  </script>
  @yield('scripts')

  @include('ai.chat-bubble')

  <style>
    .flash-fade {
      transition: opacity 0.5s ease, transform 0.5s ease;
    }
    .flash-fade-out {
      opacity: 0;
      transform: translateY(-10px);
    }
  </style>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      var selectors = [
        '.bg-green-50', '.bg-red-50', '.bg-yellow-50',
        '.alert-success', '.alert-danger', '.alert-warning', '.alert-info',
        '[data-flash]'
      ];
      document.querySelectorAll(selectors.join(',')).forEach(function(el) {
        if (el.hasAttribute('data-no-dismiss')) return;
        el.classList.add('flash-fade');
        setTimeout(function() {
          el.classList.add('flash-fade-out');
          setTimeout(function() { el.remove(); }, 500);
        }, 3000);
      });
    });
  </script>
</body>
</html>
