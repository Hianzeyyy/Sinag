<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>GAD | PSU Gender and Development</title>

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,700" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        body { 
            background-color: #f8fafc; 
            font-family: 'Nunito', sans-serif; 
            overflow-x: hidden;
        }

        .brand-descriptor { max-width: none; white-space: nowrap; line-height: 1.15; font-size: .82rem; }
        @media (min-width: 992px) {
            .top-navbar.student-navbar { flex-direction: column; align-items: stretch; gap: .45rem; }
            .student-navbar > .d-flex:first-child { width: 100%; min-width: 0; justify-content: center; }
            .student-navbar > .d-flex:last-child { width: 100%; flex: 0 0 auto; justify-content: center; }
            .student-navbar .student-top-nav { flex: 0 0 auto; justify-content: center; }
            .student-navbar .brand-descriptor { font-size: .88rem; }
        }
        @media (max-width: 991.98px) {
            .top-navbar { flex-wrap: wrap; gap: .55rem; }
            .top-navbar > .d-flex:first-child { width: 100%; min-width: 0; }
            .top-navbar > .d-flex:last-child { width: 100%; justify-content: flex-start; flex-wrap: wrap; gap: .45rem !important; }
            .navbar-brand-custom { gap: .65rem !important; max-width: 100%; }
            .navbar-brand-custom .brand-descriptor { display: block !important; max-width: 230px; white-space: normal; font-size: .78rem; }
            .student-top-nav { order: 2; width: 100%; overflow-x: auto; }
        }

        .main-wrapper {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 280px;
            background: white;
            border-right: 1px solid #e2e8f0;
            position: fixed;
            height: 100vh;
            padding: 2rem 1.5rem;
            z-index: 2000;
            display: flex;
            flex-direction: column;
            left: -280px; 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar.active {
            left: 0;
            box-shadow: 10px 0 30px rgba(0,0,0,0.08);
        }

        .top-navbar {
            background: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .user-meta {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 0.5rem;
        }

        .profile-menu-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            border: 1px solid #dbeafe;
            background: #f8fbff;
            border-radius: 999px;
            padding: 0.2rem 0.55rem 0.2rem 0.2rem;
            color: #0f172a;
            text-decoration: none;
            transition: background-color 0.2s ease, box-shadow 0.2s ease;
        }

        .profile-menu-btn:hover,
        .profile-menu-btn:focus {
            background: #eef6ff;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.18);
            color: #0f172a;
        }

        .profile-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            object-fit: cover;
            background: #1877f2;
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.78rem;
            font-weight: 800;
            text-transform: uppercase;
        }

        .profile-mini-name {
            font-size: 0.75rem;
            font-weight: 700;
            max-width: 120px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .profile-dropdown {
            min-width: 260px;
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 16px 30px rgba(15, 23, 42, 0.14);
            padding: 0.75rem;
        }

        .profile-dropdown-head {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            padding-bottom: 0.6rem;
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 0.6rem;
        }

        .profile-dropdown-action {
            display: flex;
            align-items: center;
            gap: 0.55rem;
            width: 100%;
            border: 0;
            background: #f8fafc;
            border-radius: 10px;
            padding: 0.55rem 0.7rem;
            color: #1e293b;
            font-weight: 600;
            text-align: left;
        }

        .profile-dropdown-action:hover {
            background: #eef2ff;
        }

        .nav-link-custom {
            display: flex;
            align-items: center;
            padding: 0.8rem 1.2rem;
            color: #64748b;
            text-decoration: none;
            border-radius: 12px;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
            font-weight: 600;
            border: none;
            background: none;
            width: 100%;
        }

        .nav-link-custom i { font-size: 1.2rem; margin-right: 1rem; }

        .nav-link-custom.active {
            background-color: #A594F9 !important; 
            color: white !important;
            box-shadow: 0 4px 12px rgba(165, 148, 249, 0.28);
        }

        .nav-link-logout:hover {
            background-color: #fef2f2 !important;
            color: #dc2626 !important;
        }

        .main-content {
            flex: 1;
            margin-left: 0;
            width: 100%;
            transition: all 0.3s ease-in-out;
            display: flex;
            flex-direction: column;
        }

        .login-main {
            width: 100%;
            max-width: none;
            padding: 0 !important;
        }

        .auth-main {
            width: 100%;
            max-width: none;
            padding: 0 !important;
        }

        .auth-content {
            padding: 0 !important;
            width: 100%;
            max-width: none;
        }

        @media (min-width: 992px) {
            .sidebar-open .main-content {
                margin-left: 280px;
                width: calc(100% - 280px);
            }
        }

        .navbar-brand-custom {
            font-size: 1.5rem;
            font-weight: 800;
            color: #A594F9;
            text-decoration: none;
            display: flex;
            align-items: center;
            margin-bottom: 2.5rem;
        }
        .navbar-brand-custom span { color: #facc15; }

        .sidebar-toggle {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 6px 12px;
            cursor: pointer;
            color: #1e293b;
            transition: all 0.2s;
        }

        .student-top-nav {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            flex-wrap: wrap;
        }

        .student-navbar > .d-flex:last-child {
            min-width: 0;
        }

        .student-mobile-scroll-hint {
            display: none;
        }

        .student-mobile-menu-toggle,
        .student-mobile-menu {
            display: none;
        }

        @media (max-width: 991.98px) {
            .top-navbar.admin-navbar {
                transition: transform 0.24s ease, box-shadow 0.24s ease;
                will-change: transform;
            }

            .top-navbar.admin-navbar.navbar-hidden {
                transform: translateY(-100%);
                box-shadow: none;
            }

            .admin-navbar .navbar-brand-custom {
                gap: 0.55rem !important;
                margin: 0 !important;
            }

            .admin-navbar .navbar-brand-custom img {
                width: 36px !important;
                height: 36px !important;
            }

            .admin-navbar .navbar-brand-custom > span {
                font-size: 1.45rem !important;
            }

            .admin-navbar > .d-flex:last-child > a[href*="/admin/messages"] {
                display: none !important;
            }
        }

        @media (max-width: 991.98px) {
            .student-navbar {
                transition: transform 0.24s ease, box-shadow 0.24s ease;
                will-change: transform;
            }

            .student-navbar.navbar-hidden {
                transform: translateY(-100%);
                box-shadow: none;
            }
        }

        @media (max-width: 767.98px) {
            .admin-navbar { flex-direction: row !important; align-items: center !important; flex-wrap: nowrap !important; gap: .4rem !important; padding: .5rem .65rem !important; }
            .admin-navbar > .d-flex:first-child { width: auto !important; flex: 1 1 auto; min-width: 0; }
            .admin-navbar .brand-descriptor { display: none !important; }
            .admin-navbar .navbar-brand-custom { gap: .4rem !important; max-width: 100%; }
            .admin-navbar .navbar-brand-custom > div { flex-shrink: 0; }
            .admin-navbar .navbar-brand-custom img { width: 30px !important; height: 30px !important; }
            .admin-navbar .navbar-brand-custom > span { font-size: 1.35rem !important; }
            .admin-navbar > .d-flex:last-child { width: auto !important; flex: 0 0 auto !important; gap: .25rem !important; }
            .admin-navbar > .d-flex:last-child > a[href*="/admin/messages"] { display: none !important; }
            .admin-navbar .profile-menu-btn { min-height: 38px; padding: .2rem; }
            .admin-navbar .profile-mini-name { display: none; }
            .admin-navbar + .p-4.p-lg-5 { padding: .75rem !important; }
            .admin-main h1, .admin-main h2, .admin-main h3 { overflow-wrap: anywhere; line-height: 1.15; }
            .admin-main .container, .admin-main .container-fluid { max-width: 100%; padding-inline: .25rem; }
            .admin-main .card { border-radius: 14px !important; }

            .student-navbar { flex-direction: row !important; align-items: center !important; flex-wrap: nowrap !important; gap: .35rem !important; padding: .5rem .65rem !important; }
            .student-navbar > .d-flex:first-child { width: auto !important; flex: 1 1 auto; justify-content: flex-start !important; min-width: 0; }
            .student-navbar > .d-flex:first-child .navbar-brand-custom { gap: .4rem !important; max-width: 100%; }
            .student-navbar > .d-flex:first-child .navbar-brand-custom > div { flex-shrink: 0; }
            .student-navbar > .d-flex:first-child .navbar-brand-custom img { width: 30px !important; height: 30px !important; }
            .student-navbar > .d-flex:first-child .navbar-brand-custom > span:not(.brand-descriptor) { font-size: 1.35rem !important; }
            .student-navbar .brand-descriptor, .student-navbar .student-top-nav, .student-navbar .student-mobile-scroll-hint, .student-navbar > .d-flex:last-child > a[href*="/messaging"] { display: none !important; }
            .student-navbar > .d-flex:last-child { width: auto !important; flex: 0 0 auto !important; overflow: visible !important; padding: 0 !important; gap: .25rem !important; }
            .student-mobile-menu-toggle { display: inline-flex; align-items: center; justify-content: center; width: 38px; height: 38px; border: 0; border-radius: 11px; background: #A594F9; color: #fff; box-shadow: 0 5px 14px rgba(143,114,245,.24); }
            .student-navbar .profile-menu-btn { min-height: 38px; padding: .2rem; }
            .student-navbar .profile-mini-name { display: none; }
            .student-navbar > .d-flex:last-child > .text-end { display: block !important; }
            .student-mobile-menu { position: fixed; inset: 0; z-index: 2500; background: rgba(15,23,42,.38); opacity: 0; pointer-events: none; transition: opacity .2s ease; }
            .student-mobile-menu.is-open { display: block; opacity: 1; pointer-events: auto; }
            .student-mobile-menu-panel { width: min(82vw, 300px); height: 100%; padding: 1.2rem 1rem; background: #fff; box-shadow: 12px 0 28px rgba(15,23,42,.18); transform: translateX(-100%); transition: transform .24s ease; }
            .student-mobile-menu.is-open .student-mobile-menu-panel { transform: translateX(0); }
            .student-mobile-menu-link { display: flex; align-items: center; gap: .75rem; padding: .8rem .75rem; margin-bottom: .35rem; border-radius: 11px; color: #475569; font-weight: 800; text-decoration: none; }
            .student-mobile-menu-link:hover, .student-mobile-menu-link.active { background: #f3efff; color: #7658df; }
            @media (prefers-reduced-motion: reduce) { .student-mobile-menu, .student-mobile-menu-panel { transition: none; } }
        }

        .student-nav-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            border: 1px solid transparent;
            border-radius: 999px;
            padding: 0.45rem 0.9rem;
            color: #475569;
            background: transparent;
            font-size: 0.9rem;
            font-weight: 800;
            text-decoration: none;
            transition: background-color 0.2s ease, color 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        }

        .student-nav-link:hover,
        .student-nav-link:focus-visible {
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(165, 148, 249, 0.2);
        }

        .student-nav-link:active {
            transform: translateY(0) scale(0.97);
        }

        .student-nav-link:hover,
        .student-nav-link:focus {
            color: #4c1d95;
            background: #f3f4f6;
            border-color: rgba(165, 148, 249, 0.2);
        }

        .student-nav-link.active {
            color: #fff;
            background: linear-gradient(135deg, #A594F9 0%, #8f72f5 100%);
            border-color: transparent;
            box-shadow: 0 8px 20px rgba(165, 148, 249, 0.25);
        }

        .nav-notification {
            min-width: 1.25rem;
            padding: 0.15rem 0.35rem;
            font-size: 0.68rem;
            background: #facc15;
            color: #4a3500;
            border-radius: 999px;
            line-height: 1.1;
        }

        @media (max-width: 991.98px) {
            .student-nav-link[title] { position: relative; }
            .student-nav-link[title]:hover::before,
            .student-nav-link[title]:focus-visible::before {
                content: attr(title);
                position: absolute;
                z-index: 2200;
                top: calc(100% + 0.35rem);
                left: 50%;
                transform: translateX(-50%);
                white-space: nowrap;
                padding: 0.3rem 0.5rem;
                border-radius: 0.4rem;
                background: #312e81;
                color: #fff;
                font-size: 0.7rem;
                font-weight: 700;
                pointer-events: none;
            }
        }

        .student-nav-menu {
            min-width: 220px;
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 16px 30px rgba(15, 23, 42, 0.14);
            padding: 0.45rem;
        }

        .student-nav-menu .dropdown-item {
            border-radius: 10px;
            padding: 0.55rem 0.65rem;
            font-weight: 700;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 0.45rem;
        }

        .student-nav-menu .dropdown-item:hover,
        .student-nav-menu .dropdown-item:focus {
            background: #eef2ff;
            color: #4c1d95;
        }

        @media (max-width: 991.98px) {
            .top-navbar {
                padding-left: 0.9rem;
                padding-right: 0.9rem;
            }

            .student-navbar {
                flex-direction: column;
                align-items: stretch;
            }

            .student-navbar > .d-flex {
                width: 100%;
            }

            .student-top-nav {
                width: 100%;
                gap: 0.35rem;
            }

            .student-nav-link {
                font-size: 0.78rem;
                padding: 0.42rem 0.72rem;
            }

            .user-meta {
                gap: 0.35rem;
            }

            .profile-mini-name {
                display: none;
            }

            .student-navbar {
                align-items: stretch;
                gap: 0.65rem;
                padding-block: 0.65rem;
            }

            .student-navbar > .d-flex:first-child {
                justify-content: center;
            }

            .student-navbar > .d-flex:first-child .navbar-brand-custom {
                gap: 0.65rem !important;
                margin: 0 !important;
            }

            .student-navbar > .d-flex:first-child .navbar-brand-custom > div {
                filter: drop-shadow(0 0 6px rgba(159, 83, 252, 0.55)) !important;
            }

            .student-navbar > .d-flex:first-child .navbar-brand-custom img {
                width: 40px !important;
                height: 40px !important;
            }

            .student-navbar > .d-flex:first-child .navbar-brand-custom > span {
                font-size: 1.55rem !important;
            }

            .student-navbar > .d-flex:last-child {
                position: relative;
                display: flex !important;
                align-items: center;
                flex-wrap: nowrap;
                overflow-x: auto;
                overflow-y: hidden;
                scrollbar-width: none;
                -webkit-overflow-scrolling: touch;
                gap: 0.45rem;
                width: 100%;
                padding: 0.15rem 0.1rem 0.35rem;
            }

            .student-mobile-scroll-hint {
                position: absolute;
                right: 0;
                bottom: 0.35rem;
                display: inline-flex;
                align-items: center;
                justify-content: flex-end;
                width: 2.1rem;
                height: 2.25rem;
                padding-right: 0.2rem;
                color: #8f72f5;
                background: linear-gradient(90deg, rgba(255,255,255,0), #fff 42%);
                pointer-events: none;
                font-size: 0.8rem;
            }

            .student-navbar > .d-flex:last-child::-webkit-scrollbar { display: none; }

            .student-navbar .student-top-nav {
                display: flex;
                align-items: center;
                flex-wrap: nowrap;
                flex: 0 0 auto;
                overflow: visible;
                width: auto;
                margin: 0 !important;
                gap: 0.35rem;
                padding: 0;
            }

            .student-navbar .student-top-nav::-webkit-scrollbar { display: none; }

            .student-navbar .student-nav-link,
            .student-navbar .student-top-nav .dropdown {
                flex: 0 0 auto;
                min-width: 48px;
                width: auto;
            }

            .student-navbar .student-nav-link {
                min-height: 42px;
                padding: 0.55rem 0.35rem;
                font-size: 0.75rem;
                white-space: nowrap;
            }

            .student-navbar .student-nav-link span {
                display: none;
            }

            .student-navbar .student-nav-link i {
                margin: 0;
                font-size: 1rem;
            }

            .student-navbar .student-top-nav .dropdown .student-nav-link {
                width: auto;
            }

            .student-navbar .student-nav-link.dropdown-toggle::after {
                margin-left: 0.25rem;
            }

            .student-navbar .dropdown-menu {
                z-index: 2100;
                max-width: calc(100vw - 1rem);
            }

            .mobile-dropdown-portal {
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
            }

            .student-navbar > .d-flex:last-child > a[href*="/messaging"] {
                flex: 0 0 auto;
                width: auto;
                margin: 0 !important;
                justify-content: center;
                white-space: nowrap;
            }

            .student-navbar > .d-flex:last-child > a[href*="/messaging"] span {
                display: none;
            }

            .student-navbar > .d-flex:last-child > .text-end {
                flex: 0 0 auto;
            }

            .student-navbar .profile-menu-btn {
                min-height: 42px;
            }

            .student-navbar .profile-avatar {
                width: 28px;
                height: 28px;
            }
        }
    </style>
</head>
<body>
    <div id="app" class="main-wrapper">
        
        @auth
            @if(Auth::user()->role === 'admin')
            <aside class="sidebar" id="mainSidebar">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <a class="navbar-brand-custom mb-0" href="{{ url('/') }}" style="display:inline-flex; align-items:center; gap:0.7rem; background:#b9adf4; border-radius:1.2rem; padding:0.2em 1.1em 0.2em 1.1em;">
                        <span style="font-size:1.35rem; font-weight:900; letter-spacing:1px; line-height:1;">
                            <span style="color:#fdfdfd;">SI</span><span style="color:#facc15;">NAG</span>
                        </span>
                        <img src="{{ asset('images/gadlogo.png') }}" alt="GAD Logo" style="width:42px; height:42px; border-radius:50%; object-fit:cover;">
                    </a>
                    <button class="btn border-0 d-lg-none" onclick="toggleSidebar()">
                        <i class="bi bi-x-lg" style="color:#a78bfa;"></i>
                    </button>
                </div>

                <div class="nav-menu mt-4 d-flex flex-column h-100">
                    <p class="text-uppercase small fw-bold text-muted mb-3" style="font-size: 0.7rem; letter-spacing: 1px;">Main Menu</p>
                    <a href="{{ route('admin.dashboard') }}" class="nav-link-custom {{ Request::is('admin/dashboard*') ? 'active' : '' }}">
                        <i class="bi bi-bar-chart-fill"></i> Dashboard
                    </a>
                    <a href="{{ route('admin.reports') }}" class="nav-link-custom {{ Request::is('admin/reports*') ? 'active' : '' }}">
                        <i class="bi bi-inbox-fill"></i> Manage Reports @if(($adminNotifications['reports'] ?? 0) > 0)<span class="nav-notification ms-auto">{{ $adminNotifications['reports'] }}</span>@endif
                    </a>
                    <a href="{{ route('admin.users') }}" class="nav-link-custom {{ Request::is('admin/users*') ? 'active' : '' }}">
                        <i class="bi bi-people-fill"></i> User Management @if(($adminNotifications['users'] ?? 0) > 0)<span class="nav-notification ms-auto">{{ $adminNotifications['users'] }}</span>@endif
                    </a>
                    <a href="{{ route('admin.password-resets.index') }}" class="nav-link-custom {{ Request::is('admin/password-reset-requests*') ? 'active' : '' }}">
                        <i class="bi bi-key-fill"></i> Password Reset Requests @if(($adminNotifications['password_resets'] ?? 0) > 0)<span class="nav-notification ms-auto">{{ $adminNotifications['password_resets'] }}</span>@endif
                    </a>
                    <a href="{{ route('admin.suggestions') }}" class="nav-link-custom {{ Request::is('admin/suggestions*') ? 'active' : '' }}">
                        <i class="bi bi-chat-square-dots-fill"></i> Participatory Suggestions @if(($adminNotifications['suggestions'] ?? 0) > 0)<span class="nav-notification ms-auto">{{ $adminNotifications['suggestions'] }}</span>@endif
                    </a>
                    <a href="{{ route('admin.messages') }}" class="nav-link-custom {{ Request::is('admin/messages*') ? 'active' : '' }}">
                        <i class="bi bi-chat-left-dots-fill"></i> Messages @if(($adminNotifications['messages'] ?? 0) > 0)<span class="nav-notification ms-auto">{{ $adminNotifications['messages'] }}</span>@endif
                    </a>
                     <a href="{{ route('admin.urgent.calls') }}" class="nav-link-custom {{ Request::is('admin/urgent-calls*') ? 'active' : '' }}">
                        <i class="bi bi-camera-video-fill"></i> Urgent Calls
                    </a>
                    <a href="{{ route('admin.gad-schedules.index') }}" class="nav-link-custom {{ Request::is('admin/gad-schedules*') ? 'active' : '' }}">
                        <i class="bi bi-calendar2-week-fill"></i> Office Schedule
                    </a>
                    <a href="{{ route('admin.appointments') }}" class="nav-link-custom {{ Request::is('admin/appointments*') ? 'active' : '' }}">
                        <i class="bi bi-calendar-check-fill"></i> Appointments @if(($adminNotifications['appointments'] ?? 0) > 0)<span class="nav-notification ms-auto">{{ $adminNotifications['appointments'] }}</span>@endif
                    </a>
                   

                    <div class="mt-auto mb-4">
                        <hr class="text-muted opacity-25">
                        {{-- Logout moved to profile dropdown for consistency --}}
                    </div>
                </div>
            </aside>
            @endif
        @endauth

        <main class="{{ Auth::check() ? 'main-content ' . (Auth::user()->role === 'admin' ? 'admin-main' : '') : (request()->routeIs('login', 'register', 'password.*') ? 'auth-main' : 'container py-5') }}">
            @auth
                <header class="top-navbar {{ in_array(strtolower((string) Auth::user()->role), ['student', 'employee', 'security'], true) ? 'student-navbar' : 'admin-navbar' }}">
                    <div class="d-flex align-items-center gap-3">
                         @if(Auth::user()->role === 'admin')
                            <button class="sidebar-toggle" onclick="toggleSidebar()" style="background: #A594F9; color: #fff; border: none; box-shadow: 0 2px 8px rgba(165,148,249,0.15);">
                                <i class="bi bi-list fs-5" style="color:#fff;"></i>
                            </button>
                           
                        @endif
                        @if(Auth::user()->role !== 'admin')
                            <button type="button" class="student-mobile-menu-toggle" id="studentMobileMenuToggle" aria-label="Open navigation menu" aria-controls="studentMobileMenu" aria-expanded="false"><i class="bi bi-list fs-5"></i></button>
                        @endif
                        <!-- Option 1: Modern & Spaced with Soft Shadows -->
<a class="navbar-brand-custom mb-0" style="display:inline-flex; align-items:center; gap:1.2rem; text-decoration:none;">
    <!-- Container para sa Logos: May Pink Glow/Shadow lang sa logos -->
    <div style="display:flex; align-items:center; filter: drop-shadow(0px 0px 8px rgba(159, 83, 252, 0.945));">
        <!-- PSU Logo -->
        <img src="{{ asset('images/psulogo.png') }}" alt="PSU Logo" 
             style="width:48px; height:48px; object-fit:contain; position:relative; z-index:2;">
        
        <!-- GAD Logo (Eksaktong dikit, zero margin) -->
        <img src="{{ asset('images/gadlogo.png') }}" alt="GAD Logo" 
             style="width:48px; height:48px; border-radius:50%; object-fit:cover; position:relative; z-index:1; margin-left: 0;">
    </div>

    <!-- SINAG Text: Wala nang shadow, mas malaki ang font -->
    <span style="font-size:1.9rem; font-weight:900; color:#8f72f5; letter-spacing:0.5px;">
        SI<span style="color:#facc15;">NAG</span>
    </span>
    <span class="brand-descriptor small text-muted">
        Gender and Development Safe Space and Participatory Suggestion System
    </span>
</a>

                       
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        @if(Auth::user()->role === 'admin')
                            <a href="{{ route('admin.messages') }}" class="btn btn-sm" style="background: linear-gradient(135deg, #A594F9 0%, #8f72f5 100%); color: white; border: none; border-radius: 0.85rem; padding: 0.6rem 1.2rem; font-weight: 700; display: flex; align-items: center; gap: 0.5rem; text-decoration: none; transition: transform 0.15s, box-shadow 0.15s; box-shadow: 0 4px 12px rgba(165, 148, 249, 0.2);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(165, 148, 249, 0.3)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(165, 148, 249, 0.2);'">
                                <i class="bi bi-chat-dots-fill"></i>
                                <span>Messages</span>
                                @if(($adminNotifications['messages'] ?? 0) > 0)<span class="nav-notification">{{ $adminNotifications['messages'] }}</span>@endif
                            </a>
                        @else
                            <nav class="student-top-nav me-3" aria-label="Student main navigation">
                                <a href="{{ route('student.dashboard') }}" class="student-nav-link {{ Request::is('student/dashboard*') ? 'active' : '' }}" aria-label="Dashboard" title="Dashboard">
                                    <i class="bi bi-house-door-fill"></i>
                                    <span>Dashboard</span>
                                </a>

                                <div class="dropdown d-inline-block">
                                    <button type="button" class="student-nav-link dropdown-toggle {{ Request::is('student/report*') || Request::is('student/vault*') ? 'active' : '' }}" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Report" title="Report">
                                        <i class="bi bi-flag-fill"></i>
                                        <span>Report</span>
                                    </button>
                                    <ul class="dropdown-menu student-nav-menu">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('student.report') }}">
                                                <i class="bi bi-megaphone-fill"></i>
                                                <span>File a Report</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('student.vault') }}">
                                                <i class="bi bi-safe2-fill"></i>
                                                <span>Report Status</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                                <a href="{{ route('student.boses') }}" class="student-nav-link {{ Request::is('student/boses*') ? 'active' : '' }}" aria-label="Suggestions" title="Suggestions">
                                    <i class="bi bi-chat-left-quote-fill"></i>
                                    <span>Suggestions</span>
                                </a>

                                <a href="{{ route('student.appointments') }}" class="student-nav-link {{ Request::is('student/gad-schedule*') || Request::is('student/schedule-appointment*') || Request::is('student/appointments*') ? 'active' : '' }}" aria-label="Schedule" title="Schedule">
                                        <i class="bi bi-calendar2-week-fill"></i>
                                        <span>Schedule</span>
                                </a>

                                <a href="{{ route('student.wellness') }}" class="student-nav-link {{ Request::is('student/wellness*') ? 'active' : '' }}" aria-label="References" title="References">
                                    <i class="bi bi-book-half"></i>
                                    <span>References</span>
                                </a>
                            </nav>
                            <span class="student-mobile-scroll-hint" aria-hidden="true"><i class="bi bi-chevron-double-right"></i></span>

                            <a href="{{ route('student.messaging') }}" class="btn btn-sm me-2" aria-label="Messages" title="Messages" style="background: linear-gradient(135deg, #A594F9 0%, #8f72f5 100%); color: white; border: none; border-radius: 0.85rem; padding: 0.6rem 1.2rem; font-weight: 700; display: flex; align-items: center; gap: 0.5rem; text-decoration: none; transition: transform 0.15s, box-shadow 0.15s; box-shadow: 0 4px 12px rgba(165, 148, 249, 0.2);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(165, 148, 249, 0.3)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(165, 148, 249, 0.2);'">
                                <i class="bi bi-chat-dots-fill"></i>
                                <span>Message</span>
                            </a>
                        @endif
                        <div class="text-end">
                            <div class="dropdown user-meta">
                                <a class="profile-menu-btn" href="{{ route('profile.show') }}" title="View profile">
                                    @if(Auth::user()->profile_photo_path)
                                        <img src="{{ asset('storage/'.Auth::user()->profile_photo_path) }}" alt="Profile photo" class="profile-avatar">
                                    @else
                                        <span class="profile-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                                    @endif
                                    <span class="profile-mini-name">{{ Auth::user()->name }}</span>
                                </a>
                                <button class="profile-menu-btn ms-1" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Open account menu">
                                    <i class="bi bi-chevron-down"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end profile-dropdown p-2">
                                    <div class="profile-dropdown-head">
                                        @if(Auth::user()->profile_photo_path)
                                            <img src="{{ asset('storage/'.Auth::user()->profile_photo_path) }}" alt="Profile photo" class="profile-avatar">
                                        @else
                                            <span class="profile-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                                        @endif
                                        <div>
                                            <div class="fw-bold" style="line-height: 1.2;">{{ Auth::user()->name }}</div>
                                            <small class="text-muted">{{ Auth::user()->cloak_alias ?? 'Local profile' }}</small>
                                        </div>
                                    </div>
                                    <a href="{{ route('profile.show') }}" class="profile-dropdown-action text-decoration-none">
                                        <i class="bi bi-person-lines-fill"></i>
                                        View Profile
                                    </a>
                                    <a href="{{ route('personal-data.show') }}" class="profile-dropdown-action text-decoration-none">
                                        <i class="bi bi-file-earmark-person"></i>
                                        Personal Data
                                    </a>
                                    <a href="{{ route('logout') }}" class="profile-dropdown-action text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="bi bi-power"></i>
                                        Log out
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>
            @endauth

            @auth
                @if(Auth::user()->role !== 'admin')
                    <div class="student-mobile-menu" id="studentMobileMenu" aria-hidden="true">
                        <nav class="student-mobile-menu-panel" aria-label="Mobile navigation">
                            <div class="d-flex justify-content-between align-items-center mb-3"><strong class="text-dark">SINAG Menu</strong><button type="button" class="btn-close" id="studentMobileMenuClose" aria-label="Close navigation menu"></button></div>
                            <a class="student-mobile-menu-link {{ Request::is('student/dashboard*') ? 'active' : '' }}" href="{{ route('student.dashboard') }}"><i class="bi bi-house-door-fill"></i>Dashboard</a>
                            <a class="student-mobile-menu-link {{ Request::is('student/report*') ? 'active' : '' }}" href="{{ route('student.report') }}"><i class="bi bi-flag-fill"></i>Report</a>
                            <a class="student-mobile-menu-link {{ Request::is('student/boses*') ? 'active' : '' }}" href="{{ route('student.boses') }}"><i class="bi bi-chat-left-quote-fill"></i>Suggestions</a>
                            <a class="student-mobile-menu-link {{ Request::is('student/appointments*', 'student/gad-schedule*', 'student/schedule-appointment*') ? 'active' : '' }}" href="{{ route('student.appointments') }}"><i class="bi bi-calendar2-week-fill"></i>Schedule</a>
                            <a class="student-mobile-menu-link {{ Request::is('student/wellness*') ? 'active' : '' }}" href="{{ route('student.wellness') }}"><i class="bi bi-book-half"></i>References</a>
                            <a class="student-mobile-menu-link {{ Request::is('student/messaging*') ? 'active' : '' }}" href="{{ route('student.messaging') }}"><i class="bi bi-chat-dots-fill"></i>Messages</a>
                        </nav>
                    </div>
                @endif
            @endauth

            <div class="{{ Auth::check() ? 'p-4 p-lg-5' : (request()->routeIs('login', 'register', 'password.*') ? 'auth-content' : 'p-4 p-lg-5') }}">
                @yield('content')
            </div>
        </main>

    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>

    <script>
        (function () {
            const mobileStudentMenus = document.querySelectorAll('.student-navbar .dropdown, .student-navbar .user-meta');
            if (!mobileStudentMenus.length || window.matchMedia('(min-width: 992px)').matches) return;

            const portalState = new WeakMap();

            mobileStudentMenus.forEach(function (dropdown) {
                const toggles = dropdown.querySelectorAll('[data-bs-toggle="dropdown"]');
                if (!toggles.length) return;

                toggles.forEach(function (toggle) {
                    toggle.addEventListener('shown.bs.dropdown', function () {
                        const menu = dropdown.querySelector('.dropdown-menu');
                        if (!menu) return;

                        const toggleRect = toggle.getBoundingClientRect();
                        const menuWidth = Math.min(menu.offsetWidth || 220, window.innerWidth - 16);
                        const left = Math.max(8, Math.min(toggleRect.left, window.innerWidth - menuWidth - 8));

                        portalState.set(menu, { parent: menu.parentElement, nextSibling: menu.nextSibling });
                        document.body.appendChild(menu);
                        menu.classList.add('mobile-dropdown-portal');

                        menu.style.position = 'fixed';
                        menu.style.top = `${toggleRect.bottom + 8}px`;
                        menu.style.left = `${left}px`;
                        menu.style.right = 'auto';
                        menu.style.transform = 'none';
                    });

                    toggle.addEventListener('hidden.bs.dropdown', function () {
                        const menu = document.querySelector('.mobile-dropdown-portal');
                        if (menu) {
                            const state = portalState.get(menu);
                            if (state && state.parent) {
                                state.parent.insertBefore(menu, state.nextSibling);
                            }
                            menu.classList.remove('mobile-dropdown-portal');
                            menu.style.position = '';
                            menu.style.top = '';
                            menu.style.left = '';
                            menu.style.right = '';
                            menu.style.transform = '';
                        }
                    });
                });
            });
        })();

        (function () {
            const studentNavbar = document.querySelector('.student-navbar');
            const adminNavbar = document.querySelector('.admin-navbar');
            const mobileNavbar = studentNavbar || adminNavbar;
            if (!mobileNavbar || window.matchMedia('(min-width: 992px)').matches) return;

            const adminSidebar = document.getElementById('mainSidebar');

            let lastScrollY = window.scrollY;
            let ticking = false;

            function updateStudentNavbar() {
                const currentScrollY = window.scrollY;
                const scrollingDown = currentScrollY > lastScrollY && currentScrollY > 72;

                mobileNavbar.classList.toggle('navbar-hidden', scrollingDown);
                if (currentScrollY <= 16) mobileNavbar.classList.remove('navbar-hidden');

                if (adminSidebar && scrollingDown && adminSidebar.classList.contains('active')) {
                    adminSidebar.classList.remove('active');
                    document.body.classList.remove('sidebar-open');
                }

                lastScrollY = currentScrollY;
                ticking = false;
            }

            window.addEventListener('scroll', function () {
                if (!ticking) {
                    window.requestAnimationFrame(updateStudentNavbar);
                    ticking = true;
                }
            }, { passive: true });
        })();

        function toggleSidebar() {
            const sidebar = document.getElementById('mainSidebar');
            const body = document.body;
            if (sidebar) {
                sidebar.classList.toggle('active');
                body.classList.toggle('sidebar-open');
            }
        }

        (function () {
            const menu = document.getElementById('studentMobileMenu');
            const open = document.getElementById('studentMobileMenuToggle');
            const close = document.getElementById('studentMobileMenuClose');
            if (!menu || !open || !close) return;
            function setMenu(openState) {
                menu.classList.toggle('is-open', openState);
                menu.setAttribute('aria-hidden', String(!openState));
                open.setAttribute('aria-expanded', String(openState));
                document.body.classList.toggle('sidebar-open', openState);
            }
            open.addEventListener('click', function () { setMenu(true); });
            close.addEventListener('click', function () { setMenu(false); });
            menu.addEventListener('click', function (event) { if (event.target === menu) setMenu(false); });
            document.addEventListener('keydown', function (event) { if (event.key === 'Escape') setMenu(false); });
        })();

        @auth
        setInterval(function () {
            fetch('{{ route('presence.heartbeat') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });
        }, 60000);

        @if(request()->routeIs('admin.users'))
        setInterval(function () { window.location.reload(); }, 30000);
        @endif
        @endauth
    </script>
</body>
</html>