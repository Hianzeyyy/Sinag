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
        html {
            min-height: 100%;
            height: auto;
            overflow-x: clip;
            overflow-y: auto;
        }

        body {
            background-color: #f8fafc; 
            font-family: 'Nunito', sans-serif; 
            min-height: 100vh;
            min-height: 100dvh;
            height: auto;
            overflow-x: clip;
            overflow-y: visible;
        }

        body.page-scroll-locked {
            position: fixed;
            width: 100%;
            overflow: hidden;
        }

        .brand-descriptor { max-width: 100%; white-space: normal; overflow-wrap: normal; word-break: normal; line-height: 1.15; font-size: .82rem; }
        @media (min-width: 1200px) {
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
            .student-navbar .navbar-brand-custom { width: 100%; flex-wrap: wrap; justify-content: center; }
            .student-navbar .brand-descriptor {
                display: block !important;
                flex: 0 0 100%;
                max-width: 100%;
                white-space: normal;
                text-align: center;
                font-size: .72rem;
                line-height: 1.2;
            }
            .admin-navbar .navbar-brand-custom > .brand-descriptor {
                display: block !important;
                flex: 0 0 100%;
                max-width: 100%;
                min-width: 0;
                white-space: normal !important;
                overflow-wrap: normal;
                word-break: normal;
                text-align: center;
                font-size: clamp(.72rem, 1.2vw, .82rem) !important;
                line-height: 1.2;
            }
            .student-top-nav { order: 2; width: 100%; overflow-x: auto; }
        }

        /* Keep the tablet header compact instead of stacking the full student
           navigation into a tall, wrapping bar. */
        @media (min-width: 992px) and (max-width: 1199.98px) {
            .student-navbar {
                flex-wrap: wrap;
                gap: .75rem;
                padding-inline: 1rem;
            }

            .student-navbar > .d-flex:first-child {
                width: 100%;
                flex: 0 0 100%;
                min-width: 0;
                justify-content: center;
            }

            .student-navbar > .d-flex:last-child {
                width: 100%;
                flex: 0 0 100%;
                min-width: 0;
                justify-content: center;
            }

            .student-navbar .student-top-nav {
                flex-wrap: nowrap;
                overflow-x: auto;
                min-width: 0;
                scrollbar-width: thin;
            }

            .student-navbar .brand-descriptor {
                flex: 0 0 100%;
                max-width: none;
                white-space: normal;
                text-align: center;
            }

            .admin-navbar {
                flex-wrap: wrap;
                gap: .75rem;
            }

            .admin-navbar > .d-flex:first-child,
            .admin-navbar > .d-flex:last-child {
                width: 100%;
                flex: 0 0 100%;
                justify-content: center;
            }

            .admin-navbar .navbar-brand-custom {
                flex-wrap: wrap;
                justify-content: center;
            }

            .admin-navbar .navbar-brand-custom > .brand-descriptor {
                flex: 0 0 100%;
                max-width: 100%;
                min-width: 0;
                white-space: normal !important;
                overflow-wrap: normal;
                word-break: normal;
                text-align: center;
                font-size: clamp(.72rem, 1.2vw, .82rem) !important;
                line-height: 1.2;
            }

            .student-navbar .student-nav-link {
                padding-inline: .55rem;
                font-size: .78rem;
                white-space: nowrap;
            }

            .admin-navbar {
                padding-inline: 1rem;
                flex-wrap: wrap;
                gap: .75rem;
            }

            .admin-navbar > .d-flex:first-child,
            .admin-navbar > .d-flex:last-child {
                width: 100%;
                flex: 0 0 100%;
                justify-content: center;
            }

            .admin-navbar .navbar-brand-custom {
                flex-wrap: wrap;
                justify-content: center;
            }

            .admin-navbar .brand-descriptor {
                flex: 0 0 100%;
                max-width: 100%;
                white-space: normal;
                text-align: center;
                font-size: .72rem !important;
                line-height: 1.2;
            }

            .admin-main > .p-4,
            .admin-main > .p-4.p-lg-5 {
                padding: 1rem !important;
            }
        }

        .main-wrapper {
            display: flex;
            min-height: 100vh;
            min-height: 100dvh;
            height: auto;
            overflow: visible;
        }

        .sidebar {
            width: 0;
            flex: 0 0 0;
            min-width: 0;
            background: white;
            border-right: 1px solid #e2e8f0;
            position: static;
            height: auto;
            max-height: none;
            padding: 0;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            visibility: hidden;
            transition: none;
        }

        .sidebar.active {
            width: 280px;
            flex-basis: 280px;
            padding: 2rem 1.5rem;
            overflow: visible;
            visibility: visible;
        }

        .sidebar .nav-menu {
            height: auto !important;
            min-height: 0;
            flex: 1 0 auto;
        }

        .top-navbar {
            background: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            flex: 0 0 auto;
        }

        .student-navbar {
            z-index: 30;
        }

        @media (min-width: 1200px) {
            .top-navbar {
                position: sticky;
                top: 0;
                z-index: 1030;
            }
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
            flex: 0 0 30px;
            border-radius: 50%;
            object-fit: cover;
            object-position: center;
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
            z-index: 31;
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
            transition: all 0.22s ease;
            font-weight: 600;
            border: none;
            background: none;
            width: 100%;
            transform: translateX(0);
        }

        .nav-link-custom:hover,
        .nav-link-custom:focus-visible {
            background: linear-gradient(135deg, rgba(165, 148, 249, 0.12), rgba(250, 204, 21, 0.12));
            color: #4c1d95;
            transform: translateX(4px);
            box-shadow: 0 8px 18px rgba(165, 148, 249, 0.12);
        }

        .nav-link-custom i { font-size: 1.2rem; margin-right: 1rem; }

        .nav-link-custom.active {
            background-color: #A594F9 !important; 
            color: white !important;
            box-shadow: 0 4px 12px rgba(165, 148, 249, 0.28);
            transform: translateX(2px);
        }

        .student-sidebar .nav-link-custom {
            transition: background-color .18s ease, color .18s ease, transform .18s ease, box-shadow .18s ease;
        }

        .student-sidebar .nav-link-custom:hover,
        .student-sidebar .nav-link-custom:focus-visible {
            background: #f3efff;
            color: #7658df;
            transform: translateX(5px);
            box-shadow: 0 6px 16px rgba(143, 114, 245, .14);
        }

        .student-sidebar .nav-link-custom.active:hover,
        .student-sidebar .nav-link-custom.active:focus-visible {
            background: #8f72f5 !important;
            color: #fff !important;
        }

        .nav-link-logout:hover {
            background-color: #fef2f2 !important;
            color: #dc2626 !important;
        }

        .main-content {
            flex: 1;
            margin-left: 0;
            width: 100%;
            min-width: 0;
            min-height: 0;
            height: auto;
            overflow: visible;
            transition: all 0.3s ease-in-out;
            display: flex;
            flex-direction: column;
            align-items: stretch;
        }

        .main-content > .p-4,
        .main-content > .p-4.p-lg-5 {
            min-width: 0;
            flex: 0 0 auto;
        }

        .main-content > .p-4 {
            flex: 0 0 auto;
        }

        .login-main {
            width: 100%;
            max-width: none;
            padding: 0 !important;
        }

        .auth-main {
            width: 100%;
            max-width: none;
            min-height: 0;
            padding: 0 !important;
        }

        .auth-content {
            padding: 0 !important;
            width: 100%;
            max-width: none;
        }

        .splash-wrapper > main {
            display: flex;
            flex: 0 0 auto;
            width: 100%;
            min-width: 0;
            max-width: none;
            min-height: 0;
            padding: 0 !important;
            box-sizing: border-box;
        }

        .splash-wrapper > main > .p-4,
        .splash-wrapper > main > .p-4.p-lg-5 {
            display: flex;
            flex: 0 0 auto;
            width: 100%;
            min-width: 0;
            max-width: 100%;
            min-height: 0;
            padding: 0 !important;
            box-sizing: border-box;
        }

        @media (max-width: 991.98px) {
            .main-wrapper {
                flex-direction: column;
            }

            .sidebar.active {
                width: 100%;
                flex-basis: auto;
                position: static;
                height: auto;
                max-height: min(70dvh, 40rem);
                overflow-y: auto;
                overscroll-behavior-y: contain;
                -webkit-overflow-scrolling: touch;
                touch-action: pan-y;
            }

            .sidebar.active .nav-menu {
                flex: 0 0 auto;
            }

            .main-content {
                width: 100%;
                flex: 0 0 auto;
            }

            .main-content > .p-4,
            .main-content > .p-4.p-lg-5 {
                padding: 1rem 1rem max(1rem, env(safe-area-inset-bottom)) !important;
            }
        }

        @media (max-width: 767.98px) {
            .main-content > .p-4,
            .main-content > .p-4.p-lg-5 {
                padding: .75rem .75rem max(.75rem, env(safe-area-inset-bottom)) !important;
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

        .student-mobile-messaging-link {
            display: none !important;
        }

        .student-sidebar-toggle {
            display: none;
        }

        .student-navbar > .d-flex:last-child {
            min-width: 0;
        }

        .student-mobile-scroll-hint {
            display: none;
        }

        @media (max-width: 991.98px) {
            .admin-navbar {
                flex-wrap: wrap;
                gap: .4rem;
            }

            .admin-navbar > .d-flex:first-child {
                display: grid !important;
                grid-template-columns: 40px minmax(0, 1fr);
                align-items: center;
                column-gap: .45rem;
                row-gap: .2rem;
                width: 100%;
                flex: 0 0 100%;
                min-width: 0;
            }

            .admin-navbar > .d-flex:first-child > .sidebar-toggle {
                grid-column: 1;
                grid-row: 1;
            }

            .admin-navbar .navbar-brand-custom {
                display: grid !important;
                grid-template-columns: auto minmax(0, 1fr);
                align-items: center;
                column-gap: .4rem !important;
                row-gap: .2rem;
                grid-column: 2;
                grid-row: 1;
                width: 100%;
                min-width: 0;
                max-width: 100%;
                margin: 0 !important;
            }

            .admin-navbar .navbar-brand-custom > div {
                grid-column: 1;
                grid-row: 1;
                flex-shrink: 0;
            }

            .admin-navbar .navbar-brand-custom img {
                width: 34px !important;
                height: 34px !important;
                flex: 0 0 34px;
            }

            .admin-navbar .navbar-brand-custom > span:not(.brand-descriptor) {
                grid-column: 2;
                grid-row: 1;
                font-size: 1.4rem !important;
            }

            .admin-navbar .navbar-brand-custom > .brand-descriptor {
                display: block !important;
                grid-column: 1 / -1;
                grid-row: 2;
                width: 100%;
                max-width: 100%;
                min-width: 0;
                white-space: normal !important;
                overflow-wrap: normal;
                word-break: normal;
                text-align: center;
                font-size: clamp(.72rem, 1.2vw, .82rem) !important;
                line-height: 1.2;
            }

            .admin-navbar > .d-flex:last-child {
                width: 100%;
                flex: 0 0 100%;
                justify-content: center;
                gap: .25rem !important;
            }

            .admin-navbar > .d-flex:last-child > a[href*="/admin/messages"] {
                display: none !important;
            }

            .admin-navbar .profile-menu-btn {
                min-height: 38px;
                padding: .2rem;
            }

            .admin-navbar .profile-avatar {
                width: 34px !important;
                height: 34px !important;
                flex: 0 0 34px;
                border-radius: 50%;
                object-fit: cover;
                object-position: center;
            }

            .admin-navbar img.profile-avatar {
                display: block;
            }

            .admin-navbar .profile-mini-name {
                display: none;
            }

            .admin-main .card-header {
                flex-wrap: wrap;
                gap: .75rem;
            }

            .admin-main .card-header .input-group {
                width: min(100%, 250px) !important;
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

        @media (max-width: 767.98px) {
            .admin-navbar { flex-direction: row !important; padding: .5rem .65rem !important; }
            .admin-navbar > .d-flex:first-child { padding-right: 2.8rem; }
            .admin-navbar > .d-flex:last-child {
                position: absolute;
                top: .5rem;
                right: .65rem;
                width: auto;
                flex: 0 0 auto;
                justify-content: flex-end;
            }
            .admin-navbar .profile-avatar {
                width: 34px !important;
                height: 34px !important;
                flex: 0 0 34px;
                border-radius: 50%;
                object-fit: cover;
                object-position: center;
            }
            .admin-navbar img.profile-avatar { display: block; }
            .admin-navbar .profile-mini-name { display: none; }
            .admin-navbar + .p-4.p-lg-5 { padding: .75rem !important; }
            .admin-main h1, .admin-main h2, .admin-main h3 { overflow-wrap: anywhere; line-height: 1.15; }
            .admin-main .container, .admin-main .container-fluid { max-width: 100%; padding-inline: .25rem; }
            .admin-main .card { border-radius: 14px !important; }
            .admin-main .card-header { flex-wrap: wrap; gap: .75rem; }
            .admin-main .card-header > * { min-width: 0; }
            .admin-main .card-header .input-group { width: min(100%, 20rem) !important; }
            .admin-main .table-responsive { max-width: 100%; }

            .student-navbar { flex-direction: row !important; align-items: center !important; flex-wrap: nowrap !important; gap: .35rem !important; padding: .5rem .65rem !important; }
            .student-navbar > .d-flex:first-child { width: auto !important; flex: 1 1 auto; justify-content: flex-start !important; min-width: 0; }
            .student-navbar > .d-flex:first-child .navbar-brand-custom {
                display: grid !important;
                width: 100%;
                grid-template-columns: auto minmax(0, 1fr);
                grid-template-rows: auto auto;
                align-items: center;
                column-gap: .4rem !important;
                row-gap: 0 !important;
                max-width: 100%;
            }
            .student-navbar > .d-flex:first-child .navbar-brand-custom > div {
                grid-column: 1;
                grid-row: 1 / span 2;
                flex-shrink: 0;
            }
            .student-navbar > .d-flex:first-child .navbar-brand-custom img { width: 30px !important; height: 30px !important; }
            .student-navbar > .d-flex:first-child .navbar-brand-custom > span:not(.brand-descriptor) {
                grid-column: 2;
                grid-row: 1;
                align-self: end;
                font-size: 1.35rem !important;
            }
            .student-navbar .brand-descriptor {
                display: block !important;
                grid-column: 1 / -1;
                grid-row: 2;
                align-self: start;
                max-width: 100%;
                min-width: 0;
                white-space: normal;
                font-size: .55rem !important;
                line-height: 1.05;
                text-align: center;
            }
            .student-navbar .student-top-nav, .student-navbar .student-mobile-scroll-hint, .student-navbar > .d-flex:last-child > a[href*="/messaging"] { display: none !important; }
            .student-navbar > .d-flex:last-child { width: auto !important; flex: 0 0 auto !important; overflow: visible !important; padding: 0 !important; gap: .25rem !important; }
            .student-navbar .profile-menu-btn { min-height: 38px; padding: .2rem; }
            .student-navbar .profile-mini-name { display: none; }
            .student-navbar > .d-flex:last-child > .text-end { display: block !important; }
        }

        @media (min-width: 768px) and (max-width: 991.98px) {
            .top-navbar {
                padding: .65rem 1rem;
            }

            .top-navbar > .d-flex:first-child,
            .top-navbar > .d-flex:last-child {
                min-width: 0;
            }

            .top-navbar > .d-flex:last-child {
                gap: .35rem !important;
            }

            .navbar-brand-custom {
                min-width: 0;
            }

            .navbar-brand-custom .brand-descriptor {
                max-width: 100%;
                overflow-wrap: anywhere;
            }

            .student-navbar {
                flex-wrap: wrap !important;
                align-items: center;
                gap: .65rem;
            }

            .student-navbar > .d-flex:first-child {
                width: 100% !important;
                flex: 0 0 100%;
                justify-content: center !important;
            }

            .student-navbar > .d-flex:last-child {
                width: 100% !important;
                flex: 0 0 100%;
                justify-content: center !important;
                flex-wrap: nowrap;
            }

            .student-navbar .student-top-nav {
                display: flex;
                flex-wrap: nowrap;
                width: auto;
                max-width: 100%;
                overflow-x: auto;
                scrollbar-width: thin;
            }

            .student-navbar .student-nav-link {
                flex: 0 0 auto;
                padding: .4rem .55rem;
                font-size: .76rem;
            }

            .student-navbar .profile-mini-name {
                max-width: 90px;
            }

            .main-content > .p-4,
            .main-content > .p-4.p-lg-5 {
                padding: 1rem !important;
            }

            .admin-main .row > [class*="col-"] {
                min-width: 0;
            }

            .student-navbar > .d-flex:first-child {
                flex: 0 0 100%;
            }

            .student-navbar .brand-descriptor,
            .student-navbar .student-top-nav,
            .student-navbar .student-mobile-scroll-hint {
                display: none !important;
            }

            .student-navbar .brand-descriptor {
                display: block !important;
                flex: 0 0 100%;
                max-width: 100%;
                white-space: normal;
                font-size: .62rem !important;
                line-height: 1.1;
                text-align: center;
            }

            .student-navbar > .d-flex:last-child {
                flex: 0 0 100%;
                justify-content: center !important;
            }

            .student-navbar .profile-menu-btn {
                min-height: 40px;
            }

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

        .profile-avatar-fallback[hidden] {
            display: none !important;
        }

        .profile-avatar-fallback {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #A594F9;
            color: #fff;
            font-weight: 800;
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
                width: 34px;
                height: 34px;
                flex: 0 0 34px;
            }
        }

        @media (max-width: 991.98px) {
            .student-navbar {
                display: grid !important;
                grid-template-columns: 40px minmax(0, 1fr) auto;
                align-items: start !important;
                gap: .35rem !important;
                width: 100%;
                min-width: 0;
                padding: .55rem .65rem !important;
            }

            .student-navbar > .d-flex:first-child {
                display: contents !important;
            }

            .student-navbar .navbar-brand-custom {
                display: grid !important;
                grid-column: 2;
                grid-row: 1;
                grid-template-columns: auto minmax(0, 1fr);
                grid-template-rows: auto auto;
                align-items: center;
                column-gap: .35rem !important;
                row-gap: .2rem !important;
                width: 100%;
                min-width: 0;
                max-width: 100%;
                margin: 0 !important;
            }

            .student-navbar > .d-flex:first-child .navbar-brand-custom > div {
                grid-column: 1;
                grid-row: 1;
                min-width: 0;
                flex-shrink: 0;
            }

            .student-navbar > .d-flex:first-child .navbar-brand-custom img {
                width: 30px !important;
                height: 30px !important;
            }

            .student-navbar > .d-flex:first-child .navbar-brand-custom > span:not(.brand-descriptor) {
                grid-column: 2;
                grid-row: 1;
                min-width: 0;
                align-self: center;
                font-size: 1.35rem !important;
                line-height: 1;
            }

            .student-navbar > .d-flex:first-child .navbar-brand-custom > .brand-descriptor {
                display: block !important;
                grid-column: 1 / -1;
                grid-row: 2;
                width: 100%;
                max-width: 100%;
                min-width: 0;
                margin: 0;
                white-space: normal !important;
                overflow-wrap: normal;
                word-break: normal;
                text-align: center;
                font-size: clamp(.65rem, 2.1vw, .8rem) !important;
                line-height: 1.2;
            }

            .student-navbar > .d-flex:last-child {
                display: flex !important;
                grid-column: 3;
                grid-row: 1;
                align-items: center;
                justify-content: flex-end !important;
                width: auto !important;
                min-width: 0;
                max-width: 100%;
                margin: 0;
                padding: 0 !important;
                overflow: visible !important;
                gap: .2rem !important;
            }

            .student-navbar .student-top-nav,
            .student-navbar .student-mobile-scroll-hint,
            .student-navbar > .d-flex:last-child > a[href*="/messaging"] {
                display: none !important;
            }

            .student-navbar .user-meta {
                display: flex;
                align-items: center;
                gap: .15rem;
            }

            .student-navbar .profile-menu-btn {
                min-height: 38px;
                padding: .15rem;
            }

            .student-navbar .profile-avatar {
                width: 34px !important;
                height: 34px !important;
                flex: 0 0 34px;
                border-radius: 50%;
                object-fit: cover;
                object-position: center;
            }

            .student-navbar .profile-mini-name {
                display: none;
            }
        }

        @media (min-width: 992px) and (max-width: 1199.98px) {
            .student-navbar {
                flex-direction: row !important;
                flex-wrap: wrap !important;
                align-items: center !important;
                justify-content: center;
                gap: .6rem !important;
                min-width: 0;
            }

            .student-navbar > .d-flex:first-child,
            .student-navbar > .d-flex:last-child {
                width: 100% !important;
                min-width: 0;
                flex: 0 0 100% !important;
                justify-content: center !important;
            }

            .student-navbar .navbar-brand-custom {
                display: grid !important;
                grid-template-columns: auto auto minmax(0, 1fr);
                align-items: center;
                column-gap: .5rem !important;
                width: min(100%, 900px);
                min-width: 0;
                margin: 0 !important;
            }

            .student-navbar > .d-flex:first-child > .navbar-brand-custom > div {
                grid-column: 1;
                grid-row: 1;
            }

            .student-navbar > .d-flex:first-child > .navbar-brand-custom > span:not(.brand-descriptor) {
                grid-column: 2;
                grid-row: 1;
                white-space: nowrap;
            }

            .student-navbar > .d-flex:first-child > .navbar-brand-custom > .brand-descriptor {
                display: block !important;
                grid-column: 3;
                grid-row: 1;
                min-width: 0;
                max-width: 100%;
                white-space: normal;
                text-align: left;
                font-size: .78rem !important;
                line-height: 1.2;
            }

            .student-navbar .student-top-nav {
                display: flex !important;
                flex-wrap: nowrap;
                min-width: 0;
                overflow-x: auto;
                scrollbar-width: thin;
            }

            .student-navbar .student-mobile-scroll-hint {
                display: none !important;
            }

            .student-navbar > .d-flex:last-child > a[href*="/messaging"] {
                display: flex !important;
            }

            .student-navbar .profile-avatar {
                width: 34px;
                height: 34px;
                flex: 0 0 34px;
                border-radius: 50%;
                object-fit: cover;
                object-position: center;
            }
        }

        @media (min-width: 768px) and (max-width: 991.98px) {
            .student-navbar {
                display: grid !important;
                grid-template-columns: 42px minmax(0, 1fr) auto !important;
                align-items: start !important;
                gap: .5rem !important;
                width: 100% !important;
                min-width: 0;
                box-sizing: border-box;
                padding: .65rem 1rem !important;
            }

            .student-navbar > .d-flex:first-child {
                display: contents !important;
            }

            .student-navbar > .d-flex:first-child > .navbar-brand-custom {
                display: grid !important;
                grid-column: 2;
                grid-row: 1;
                grid-template-columns: auto minmax(0, 1fr);
                grid-template-rows: auto auto;
                align-items: center;
                column-gap: .5rem !important;
                row-gap: .35rem !important;
                width: 100%;
                max-width: 100%;
                min-width: 0;
                margin: 0 !important;
            }

            .student-navbar > .d-flex:first-child > .navbar-brand-custom > div {
                grid-column: 1;
                grid-row: 1;
                min-width: 0;
            }

            .student-navbar > .d-flex:first-child > .navbar-brand-custom > div img {
                width: 34px !important;
                height: 34px !important;
            }

            .student-navbar > .d-flex:first-child > .navbar-brand-custom > span:not(.brand-descriptor) {
                grid-column: 2;
                grid-row: 1;
                min-width: 0;
                align-self: center;
                font-size: 1.5rem !important;
                line-height: 1;
                white-space: nowrap;
            }

            .student-navbar > .d-flex:first-child > .navbar-brand-custom > .brand-descriptor {
                display: block !important;
                grid-column: 1 / -1;
                grid-row: 2;
                width: 100%;
                max-width: 100%;
                min-width: 0;
                margin: 0;
                white-space: normal !important;
                overflow-wrap: normal;
                word-break: normal;
                text-align: center;
                font-size: clamp(.75rem, 1.65vw, .9rem) !important;
                line-height: 1.25;
            }

            .student-navbar > .d-flex:last-child {
                display: flex !important;
                grid-column: 3;
                grid-row: 1;
                align-items: center;
                justify-content: flex-end !important;
                width: auto !important;
                min-width: 0;
                max-width: 100%;
                margin: 0;
                padding: 0 !important;
                overflow: visible !important;
                gap: .2rem !important;
            }

            .student-navbar .student-top-nav,
            .student-navbar .student-mobile-scroll-hint,
            .student-navbar > .d-flex:last-child > a[href*="/messaging"] {
                display: none !important;
            }

            .student-navbar .user-meta {
                display: flex;
                align-items: center;
                gap: .15rem;
            }

            .student-navbar .profile-menu-btn {
                min-height: 42px;
                padding: .2rem;
            }

            .student-navbar .profile-avatar {
                width: 38px !important;
                height: 38px !important;
                flex: 0 0 38px;
                object-fit: cover;
                object-position: center;
            }

            .student-navbar .profile-mini-name {
                display: none;
            }
        }

        @media (max-width: 1199.98px) {
            .student-navbar {
                position: relative;
            }

            .student-navbar > .d-flex:last-child {
                position: static !important;
                overflow: visible !important;
            }

            .student-navbar > .d-flex:last-child > .text-end {
                position: absolute !important;
                top: 50%;
                right: .65rem;
                z-index: 2;
                margin: 0 !important;
                transform: translateY(-50%);
            }

            .main-content {
                min-height: 0;
            }

            .sidebar-toggle {
                display: inline-flex !important;
            }

            .sidebar.active {
                position: fixed;
                inset: 0 auto 0 0;
                z-index: 1101;
                width: min(88vw, 280px);
                flex: 0 0 min(88vw, 280px);
                height: 100vh;
                height: 100dvh;
                max-height: 100dvh;
                padding: max(1.25rem, env(safe-area-inset-top)) 1rem max(1.25rem, env(safe-area-inset-bottom));
                overflow-y: auto;
                overscroll-behavior-y: contain;
                -webkit-overflow-scrolling: touch;
                box-shadow: 12px 0 32px rgba(15, 23, 42, .16);
            }

            .sidebar.active > .d-flex:first-child > .btn {
                display: inline-flex !important;
            }

            .sidebar.active .nav-menu {
                height: auto !important;
                min-height: 0;
                flex: 0 0 auto;
            }

            .sidebar-backdrop.is-visible {
                position: fixed;
                inset: 0;
                z-index: 1100;
                display: block !important;
                width: 100%;
                height: 100vh;
                height: 100dvh;
                border: 0;
                background: rgba(15, 23, 42, .48);
            }

            .student-navbar {
                display: flex !important;
                flex-direction: row !important;
                flex-wrap: wrap !important;
                align-items: center !important;
                justify-content: space-between;
                gap: .45rem .75rem !important;
                width: 100%;
                min-width: 0;
            }

            .student-navbar > .d-flex:first-child {
                display: flex !important;
                flex: 1 1 auto !important;
                width: auto !important;
                min-width: 0;
                justify-content: flex-start !important;
            }

            .student-navbar > .d-flex:first-child > .navbar-brand-custom {
                display: grid !important;
                grid-template-columns: auto auto;
                grid-template-rows: auto auto;
                align-items: center;
                gap: .25rem .5rem !important;
                width: auto;
                max-width: 100%;
                min-width: 0;
                margin: 0 !important;
            }

            .student-navbar > .d-flex:first-child > .navbar-brand-custom > div {
                grid-column: 1;
                grid-row: 1;
                min-width: 0;
            }

            .student-navbar .navbar-brand-custom img {
                width: 34px !important;
                height: 34px !important;
            }

            .student-navbar > .d-flex:first-child > .navbar-brand-custom > span:not(.brand-descriptor) {
                grid-column: 2;
                grid-row: 1;
                min-width: 0;
                font-size: 1.5rem !important;
                white-space: nowrap;
            }

            .student-navbar > .d-flex:first-child > .navbar-brand-custom > .brand-descriptor {
                display: block !important;
                grid-column: 1 / -1;
                grid-row: 2;
                width: 100%;
                max-width: 100%;
                min-width: 0;
                text-align: left;
                white-space: normal !important;
                font-size: clamp(.62rem, 1vw, .78rem) !important;
                line-height: 1.2;
            }

            .student-navbar > .d-flex:last-child {
                display: flex !important;
                flex: 1 0 100% !important;
                order: 2;
                align-items: center;
                justify-content: flex-end !important;
                flex-wrap: wrap;
                width: 100% !important;
                min-width: 0;
                gap: .35rem !important;
                padding: 0 !important;
                overflow: visible !important;
            }

            .student-navbar .student-top-nav {
                display: flex !important;
                flex: 0 0 100%;
                order: 3;
                width: 100%;
                min-width: 0;
                margin: 0 !important;
                overflow-x: auto;
                overflow-y: hidden;
                flex-wrap: nowrap;
                justify-content: flex-start;
                scrollbar-width: thin;
                -webkit-overflow-scrolling: touch;
            }

            .student-navbar .student-nav-link {
                flex: 0 0 auto;
                white-space: nowrap;
            }

            .student-navbar > .d-flex:last-child > a[href*="/messaging"] {
                display: flex !important;
            }

            .student-navbar .student-mobile-scroll-hint {
                display: none !important;
            }

            .student-navbar > .d-flex:last-child > .text-end {
                order: 1;
                margin-left: auto;
            }

            .student-navbar .profile-menu-btn {
                min-height: 38px;
                padding: .2rem;
            }

            .student-navbar .profile-mini-name {
                display: none;
            }
        }

        @media (max-width: 1199.98px) {
            .student-navbar .brand-descriptor {
                display: block !important;
            }

            .student-navbar .student-top-nav,
            .student-navbar .student-mobile-scroll-hint,
            .student-navbar > .d-flex:last-child > a[href*="/messaging"] {
                display: none !important;
            }

            .student-sidebar {
                display: none !important;
            }

            .student-sidebar.active {
                display: flex !important;
                position: fixed;
                inset: 0 auto 0 0;
                z-index: 1101;
                width: min(88vw, 280px);
                flex: 0 0 min(88vw, 280px);
                height: 100vh;
                height: 100dvh;
                max-height: 100dvh;
                padding: max(1.25rem, env(safe-area-inset-top)) 1rem max(1.25rem, env(safe-area-inset-bottom));
                overflow-x: hidden;
                overflow-y: auto;
                overscroll-behavior-y: contain;
                -webkit-overflow-scrolling: touch;
                touch-action: pan-y;
                background: #fff;
                visibility: visible;
                box-shadow: 12px 0 32px rgba(15, 23, 42, .16);
            }

            .student-sidebar.active .nav-menu {
                height: auto !important;
                min-height: 0;
                flex: 0 0 auto;
            }

            .student-sidebar-backdrop.is-visible {
                position: fixed;
                inset: 0;
                z-index: 1100;
                display: block !important;
                width: 100%;
                height: 100vh;
                height: 100dvh;
                border: 0;
                background: rgba(15, 23, 42, .48);
            }

            .student-sidebar-toggle {
                display: inline-flex !important;
                align-items: center;
                justify-content: center;
                flex: 0 0 42px;
                width: 42px;
                height: 42px;
                border: 0;
                border-radius: 11px;
                background: #A594F9;
                color: #fff;
                box-shadow: 0 5px 14px rgba(143, 114, 245, .24);
            }
        }

        @media (max-width: 991.98px) {
            .student-navbar {
                display: flex !important;
                flex-direction: row !important;
                flex-wrap: wrap !important;
                align-items: center !important;
                gap: .45rem !important;
                padding: .55rem .65rem !important;
            }

            .student-navbar > .d-flex:first-child {
                display: flex !important;
                flex: 0 0 100% !important;
                width: 100% !important;
                min-width: 0;
                padding-right: 5.5rem;
            }

            .student-navbar > .d-flex:first-child > .navbar-brand-custom {
                display: grid !important;
                grid-template-columns: auto minmax(0, 1fr);
                grid-template-rows: auto auto;
                width: 100%;
                min-width: 0;
                max-width: 100%;
                margin: 0 !important;
            }

            .student-navbar > .d-flex:first-child > .navbar-brand-custom > div {
                grid-column: 1;
                grid-row: 1;
            }

            .student-navbar > .d-flex:first-child > .navbar-brand-custom > span:not(.brand-descriptor) {
                grid-column: 2;
                grid-row: 1;
            }

            .student-navbar > .d-flex:last-child {
                display: flex !important;
                flex: 0 0 100% !important;
                flex-wrap: wrap;
                width: 100% !important;
                min-width: 0;
                padding: 0 !important;
                overflow: visible !important;
            }

            .student-navbar .student-top-nav {
                order: 1;
            }

            .student-navbar > .d-flex:last-child > .text-end {
                display: block !important;
                order: 0;
            }
        }

        @media (min-width: 1200px) {
            .student-navbar .student-top-nav {
                display: flex !important;
            }

            .student-sidebar,
            .student-sidebar-backdrop,
            .student-sidebar-toggle {
                display: none !important;
            }

            .student-navbar .student-mobile-messaging-link {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div id="app" class="main-wrapper {{ request()->routeIs('splash') ? 'splash-wrapper' : '' }}">
        
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
                    <a href="{{ route('admin.appointments') }}" class="nav-link-custom {{ Request::is('admin/appointments*') ? 'active' : '' }}">
                        <i class="bi bi-calendar-check-fill"></i> Appointments @if(($adminNotifications['appointments'] ?? 0) > 0)<span class="nav-notification ms-auto">{{ $adminNotifications['appointments'] }}</span>@endif
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
                        <i class="bi bi-chat-left-dots-fill"></i> Messages <span class="nav-notification ms-auto" data-admin-message-badge @if(($adminNotifications['messages'] ?? 0) < 1) hidden @endif>{{ $adminNotifications['messages'] ?? 0 }}</span>
                    </a>
                     <a href="{{ route('admin.urgent.calls') }}" class="nav-link-custom {{ Request::is('admin/urgent-calls*') ? 'active' : '' }}">
                        <i class="bi bi-camera-video-fill"></i> Urgent Calls
                    </a>
                    <a href="{{ route('admin.gad-schedules.index') }}" class="nav-link-custom {{ Request::is('admin/gad-schedules*') ? 'active' : '' }}">
                        <i class="bi bi-calendar2-week-fill"></i> Office Schedule
                    </a>
                   

                    <div class="mt-auto mb-4">
                        <hr class="text-muted opacity-25">
                        {{-- Logout moved to profile dropdown for consistency --}}
                    </div>
                </div>
            </aside>
            <button type="button" class="sidebar-backdrop d-none" id="adminSidebarBackdrop" aria-label="Close navigation menu"></button>
            @endif
        @endauth

        @auth
            @if(Auth::user()->role !== 'admin')
                <aside class="sidebar student-sidebar" id="studentSidebar" aria-label="Student navigation">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <a class="navbar-brand-custom mb-0" href="{{ route('student.dashboard') }}" style="display:inline-flex; align-items:center; gap:.7rem; background:#f3efff; border-radius:1rem; padding:.3rem .75rem; text-decoration:none;">
                            <img src="{{ asset('images/gadlogo.png') }}" alt="GAD Logo" style="width:38px; height:38px; border-radius:50%; object-fit:cover;">
                            <span style="font-size:1.3rem; font-weight:900; letter-spacing:1px; line-height:1; color:#8f72f5;">SI<span style="color:#facc15;">NAG</span></span>
                        </a>
                        <button type="button" class="btn border-0" onclick="toggleSidebar('studentSidebar', 'studentSidebarBackdrop')" aria-label="Close navigation menu">
                            <i class="bi bi-x-lg" style="color:#8f72f5;"></i>
                        </button>
                    </div>

                    <nav class="nav-menu mt-4 d-flex flex-column" aria-label="Student menu">
                        <p class="text-uppercase small fw-bold text-muted mb-3" style="font-size:.7rem; letter-spacing:1px;">Main Menu</p>
                        <a href="{{ route('student.dashboard') }}" class="nav-link-custom {{ Request::is('student/dashboard*') ? 'active' : '' }}">
                            <i class="bi bi-house-door-fill"></i> Dashboard
                        </a>
                        <a href="{{ route('student.report') }}" class="nav-link-custom {{ Request::is('student/report*') ? 'active' : '' }}">
                            <i class="bi bi-flag-fill"></i> File a Report
                        </a>
                        <a href="{{ route('student.vault') }}" class="nav-link-custom {{ Request::is('student/vault*') ? 'active' : '' }}">
                            <i class="bi bi-clipboard-check-fill"></i> Report Status
                        </a>
                        <a href="{{ route('student.boses') }}" class="nav-link-custom {{ Request::is('student/boses*') ? 'active' : '' }}">
                            <i class="bi bi-chat-left-quote-fill"></i> Suggestions
                        </a>
                        <a href="{{ route('student.appointments') }}" class="nav-link-custom {{ Request::is('student/appointments*') || Request::is('student/schedule-appointment*') ? 'active' : '' }}">
                            <i class="bi bi-calendar2-week-fill"></i> Schedule
                        </a>
                        <a href="{{ route('student.wellness') }}" class="nav-link-custom {{ Request::is('student/wellness*') || Request::is('student/reference/*') ? 'active' : '' }}">
                            <i class="bi bi-book-half"></i> References
                        </a>
                        <a href="{{ route('student.messaging') }}" class="nav-link-custom {{ Request::is('student/messaging*') ? 'active' : '' }}">
                            <i class="bi bi-chat-dots-fill"></i> Messages
                        </a>
                        <a href="{{ route('student.urgent') }}" class="nav-link-custom {{ Request::is('student/urgent*') ? 'active' : '' }}">
                            <i class="bi bi-camera-video-fill"></i> Urgent Call
                        </a>
                        <hr class="text-muted opacity-25">
                    </nav>
                </aside>
                <button type="button" class="sidebar-backdrop student-sidebar-backdrop d-none" id="studentSidebarBackdrop" aria-label="Close navigation menu"></button>
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
                            <button type="button" class="student-sidebar-toggle" onclick="toggleSidebar('studentSidebar', 'studentSidebarBackdrop')" aria-label="Open navigation menu" aria-controls="studentSidebar" aria-expanded="false">
                                <i class="bi bi-list fs-5"></i>
                            </button>
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
    @if(Auth::user()->role === 'admin')
        <span class="brand-descriptor small text-muted">
            Gender and Development Safe Space and Participatory Suggestion System for Employees and Students of Pangasinan State University Asingan Campus
        </span>
    @endif
</a>

                       
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        @if(Auth::user()->role === 'admin')
                            <a href="{{ route('admin.messages') }}" class="btn btn-sm" style="background: linear-gradient(135deg, #A594F9 0%, #8f72f5 100%); color: white; border: none; border-radius: 0.85rem; padding: 0.6rem 1.2rem; font-weight: 700; display: flex; align-items: center; gap: 0.5rem; text-decoration: none; transition: transform 0.15s, box-shadow 0.15s; box-shadow: 0 4px 12px rgba(165, 148, 249, 0.2);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(165, 148, 249, 0.3)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(165, 148, 249, 0.2);'">
                                <i class="bi bi-chat-dots-fill"></i>
                                <span>Messages</span>
                                <span class="nav-notification" data-admin-message-badge @if(($adminNotifications['messages'] ?? 0) < 1) hidden @endif>{{ $adminNotifications['messages'] ?? 0 }}</span>
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
                                        <li><a class="dropdown-item" href="{{ route('student.report') }}"><i class="bi bi-flag"></i><span>File a Report</span></a></li>
                                        <li><a class="dropdown-item" href="{{ route('student.vault') }}"><i class="bi bi-clipboard-check"></i><span>Report Status</span></a></li>
                                    </ul>
                                </div>

                                <a href="{{ route('student.boses') }}" class="student-nav-link {{ Request::is('student/boses*') ? 'active' : '' }}" aria-label="Suggestions" title="Suggestions">
                                    <i class="bi bi-chat-left-quote-fill"></i>
                                    <span>Suggestions</span>
                                </a>

                                <a href="{{ route('student.appointments') }}" class="student-nav-link {{ Request::is('student/appointments*') || Request::is('student/schedule-appointment*') ? 'active' : '' }}" aria-label="Schedule" title="Schedule">
                                    <i class="bi bi-calendar2-week-fill"></i>
                                    <span>Schedule</span>
                                </a>

                                <a href="{{ route('student.wellness') }}" class="student-nav-link {{ Request::is('student/wellness*') ? 'active' : '' }}" aria-label="References" title="References">
                                    <i class="bi bi-book-half"></i>
                                    <span>References</span>
                                </a>
                                <a href="{{ route('student.messaging') }}" class="student-nav-link student-mobile-messaging-link {{ Request::is('student/messaging*') ? 'active' : '' }}" aria-label="Messages" title="Messages">
                                    <i class="bi bi-chat-dots-fill"></i>
                                    <span>Messages</span>
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
                                        <img src="{{ asset('storage/'.Auth::user()->profile_photo_path) }}" alt="Profile photo" class="profile-avatar" onerror="this.hidden=true;this.nextElementSibling.hidden=false">
                                        <span class="profile-avatar profile-avatar-fallback" hidden>{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
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
                                            <img src="{{ asset('storage/'.Auth::user()->profile_photo_path) }}" alt="Profile photo" class="profile-avatar" onerror="this.hidden=true;this.nextElementSibling.hidden=false">
                                            <span class="profile-avatar profile-avatar-fallback" hidden>{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
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

                <div class="{{ Auth::check() ? 'p-4 p-lg-5' : (request()->routeIs('login', 'register', 'password.*') ? 'auth-content' : 'p-4 p-lg-5') }}">
                @yield('content')
            </div>
        </main>

    </div>

    @auth
        @if(in_array(strtolower((string) Auth::user()->role), ['student', 'employee', 'security'], true))
            @include('components.gad-faq-chatbot')
        @endif
    @endauth

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>

    <script>
        let lockedPageScrollY = 0;

        function lockPageScroll() {
            if (document.body.classList.contains('page-scroll-locked')) return;
            lockedPageScrollY = window.scrollY;
            document.body.classList.add('page-scroll-locked');
            document.body.style.top = `-${lockedPageScrollY}px`;
        }

        function unlockPageScroll() {
            if (!document.body.classList.contains('page-scroll-locked')) return;
            document.body.classList.remove('page-scroll-locked');
            document.body.style.top = '';
            const scrollBehavior = document.documentElement.style.scrollBehavior;
            document.documentElement.style.scrollBehavior = 'auto';
            window.scrollTo(0, lockedPageScrollY);
            document.documentElement.style.scrollBehavior = scrollBehavior;
        }

        function toggleSidebar(sidebarId = 'mainSidebar', backdropId = 'adminSidebarBackdrop') {
            const sidebar = document.getElementById(sidebarId);
            if (sidebar) {
                sidebar.classList.toggle('active');
                const isOpen = sidebar.classList.contains('active');
                const isMobile = window.matchMedia('(max-width: 1199.98px)').matches;
                const backdrop = document.getElementById(backdropId);
                if (backdrop && isMobile) {
                    backdrop.classList.toggle('is-visible', isOpen);
                    backdrop.classList.toggle('d-none', !isOpen);
                }
                document.querySelector(`[aria-controls="${sidebarId}"]`)?.setAttribute('aria-expanded', String(isOpen));
                if (isMobile && isOpen) lockPageScroll();
                else unlockPageScroll();
            }
        }

        document.getElementById('adminSidebarBackdrop')?.addEventListener('click', function () {
            toggleSidebar();
        });
        document.getElementById('studentSidebarBackdrop')?.addEventListener('click', function () {
            toggleSidebar('studentSidebar', 'studentSidebarBackdrop');
        });

        @auth
        @if(auth()->user()->role === 'admin')
        (function () {
            const notice = document.createElement('div');
            notice.setAttribute('role', 'status');
            notice.setAttribute('aria-live', 'polite');
            notice.style.cssText = 'position:static;margin:1rem 1rem 0 auto;z-index:1080;background:#35216d;color:#fff;padding:.8rem 1rem;border-radius:.75rem;box-shadow:0 8px 28px #0003;display:none;max-width:min(90vw,24rem)';
            document.body.appendChild(notice);
            let hideNotice;
            let unreadCount = {{ (int) ($adminNotifications['messages'] ?? 0) }};
            let currentCallIds = null;
            function notifyAdmin(message, link) {
                notice.textContent = '';
                const text = document.createElement('span');
                text.textContent = message;
                notice.appendChild(text);
                if (link) {
                    const anchor = document.createElement('a');
                    anchor.href = link;
                    anchor.className = 'text-white fw-bold ms-2';
                    anchor.textContent = 'Open';
                    notice.appendChild(anchor);
                }
                notice.style.display = 'block';
                clearTimeout(hideNotice);
                hideNotice = setTimeout(function () { notice.style.display = 'none'; }, 7000);
            }
            async function checkMessages() {
                try {
                    const response = await fetch('{{ route('admin.messages.feed') }}', { headers: { 'Accept': 'application/json' } });
                    if (!response.ok) throw new Error('Message notification check failed (' + response.status + ').');
                    const data = await response.json();
                    document.querySelectorAll('[data-admin-message-badge]').forEach(function (badge) {
                        badge.textContent = data.unread_count;
                        badge.hidden = data.unread_count < 1;
                    });
                    if (data.unread_count > unreadCount) {
                        const href = data.latest_unread_student_id
                            ? '{{ route('admin.messages') }}?student_id=' + encodeURIComponent(data.latest_unread_student_id)
                            : '{{ route('admin.messages') }}';
                        notifyAdmin('New message received.', href);
                    }
                    unreadCount = data.unread_count;
                } catch (error) {
                    console.error(error);
                }
            }
            async function checkCalls() {
                try {
                    const response = await fetch('{{ route('admin.urgent.calls.feed') }}', { headers: { 'Accept': 'application/json' } });
                    if (!response.ok) throw new Error('Urgent-call notification check failed (' + response.status + ').');
                    const data = await response.json();
                    const calls = (data.calls || []).filter(function (call) { return call.status !== 'ended'; });
                    const ids = new Set(calls.map(function (call) { return call.id; }));
                    const recent = calls.some(function (call) { return Date.now() - Date.parse(call.created_at) < 5 * 60 * 1000; });
                    if ((currentCallIds && calls.some(function (call) { return !currentCallIds.has(call.id); })) || (currentCallIds === null && recent)) {
                        notifyAdmin('A user is requesting an urgent call.', '{{ route('admin.urgent.calls') }}');
                    }
                    currentCallIds = ids;
                } catch (error) {
                    console.error(error);
                }
            }
            checkMessages();
            checkCalls();
            setInterval(checkMessages, 3000);
            setInterval(checkCalls, 5000);
        })();
        @endif

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