<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      data-student-theme-enabled="{{ Auth::check() && (Auth::user()->role !== 'admin' || Auth::user()->isGadCoordinator()) ? 'true' : 'false' }}"
      data-student-theme="{{ Auth::check() && Auth::user()->isGadCoordinator() ? (Auth::user()->admin_theme ?? 'light') : '' }}"
      data-coordinator-theme="{{ Auth::check() && Auth::user()->isGadCoordinator() ? (Auth::user()->admin_theme ?? 'light') : '' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>GAD | PSU Gender and Development</title>

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,700" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <script>
        (function () {
            const root = document.documentElement;
            if (root.dataset.studentThemeEnabled !== 'true') return;
            if (root.dataset.coordinatorTheme) return;

            try {
                const theme = localStorage.getItem('sinag-student-theme');
                if (theme === 'light' || theme === 'dark') root.dataset.studentTheme = theme;
            } catch (error) {
                console.error('Unable to load the SINAG theme preference:', error);
            }
        })();
    </script>

    <style>
        a,
        a:hover,
        a:focus,
        a:active,
        a:visited,
        button,
        button:hover,
        button:focus,
        button:active {
            text-decoration: none !important;
        }

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

        .main-content {
            min-width: 0;
        }

        .main-content svg {
            max-width: 100%;
            height: auto;
        }

        .pagination svg {
            width: 1rem !important;
            height: 1rem !important;
            max-width: 1rem !important;
            vertical-align: middle;
        }

        .pagination {
            flex-wrap: wrap;
            gap: .25rem;
            margin-bottom: 0;
        }

        .pagination .page-link {
            min-width: 2.25rem;
            text-align: center;
        }

        .sinag-persistent-notice:not(.d-none) {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            animation: none !important;
            transition: none !important;
        }

        .sinag-persistent-notice.d-flex:not(.d-none) {
            display: flex !important;
        }

        .modal-backdrop.show {
            opacity: .35;
        }

        .modal.show .modal-dialog {
            max-height: calc(100dvh - 1.5rem);
        }

        .modal.show .modal-content {
            max-height: calc(100dvh - 1.5rem);
            overflow-y: auto;
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

        .admin-sidebar {
            width: 60px;
            flex: 0 0 60px;
            min-width: 60px;
            padding: .55rem .35rem;
            position: fixed;
            inset: 5.5rem auto 1rem .75rem;
            z-index: 1101;
            height: auto;
            max-height: calc(100dvh - 6.5rem);
            visibility: visible;
            border-right-color: #e7defc;
            border-radius: 1.25rem;
            overflow-x: hidden;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #7866a7 transparent;
            box-shadow: 8px 0 24px rgba(62, 35, 112, .06);
            transition: width .24s ease, flex-basis .24s ease, padding .24s ease, background-color .24s ease;
        }

        .admin-sidebar.active {
            width: 248px;
            flex-basis: 248px;
            padding: 1rem .85rem;
        }

        .admin-sidebar::-webkit-scrollbar { width: 5px; }
        .admin-sidebar::-webkit-scrollbar-thumb { background: #7866a7; border-radius: 999px; }
        .admin-sidebar::-webkit-scrollbar-track { background: transparent; }

        .admin-sidebar .navbar-brand-custom {
            width: 100%;
            justify-content: center;
            margin: 0;
            padding: .45rem;
            background: #f3efff;
            border-radius: 1rem;
        }

        .admin-sidebar .admin-sidebar-close {
            display: none;
        }

        .admin-sidebar.active .navbar-brand-custom {
            justify-content: flex-start;
            padding: .4rem .65rem;
        }

        .admin-sidebar:not(.active) .admin-brand-name,
        .admin-sidebar:not(.active) .admin-sidebar-heading,
        .admin-sidebar:not(.active) .admin-nav-label,
        .admin-sidebar:not(.active) .admin-nav-count,
        .admin-sidebar:not(.active) .admin-subnav {
            display: none !important;
        }

        .admin-sidebar:not(.active) .nav-link-custom {
            justify-content: center;
            padding: .68rem .25rem;
        }

        .admin-sidebar:not(.active) .nav-link-custom i {
            font-size: 1.05rem;
        }

        .admin-sidebar .nav-link-custom i {
            margin-right: .8rem;
            flex: 0 0 auto;
            transition: transform .18s ease;
        }

        .admin-sidebar .nav-link-custom:hover i,
        .admin-sidebar .nav-link-custom:focus-visible i {
            transform: scale(1.14);
        }

        .admin-sidebar:not(.active) .nav-link-custom i {
            margin-right: 0;
        }

        .admin-sidebar .admin-sidebar-footer {
            border-top: 1px solid #e8e1f5;
            padding-top: .8rem;
            margin-top: auto;
        }

        .admin-dashboard-nav-row {
            position: relative;
            margin-bottom: .5rem;
        }

        .admin-dashboard-nav-row > .nav-link-custom {
            margin-bottom: 0;
        }

        .admin-sidebar-toggle-floating {
            position: fixed;
            top: calc(5.5rem + 1.8rem);
            left: calc(.75rem + 60px);
            z-index: 1102;
            display: grid;
            align-items: center;
            justify-content: center;
            width: 2.5rem;
            height: 2.5rem;
            padding: 0;
            border: 1px solid #d9cdf5;
            border-radius: 12px;
            background: #fff;
            color: #6b4bd4;
            box-shadow: 0 8px 20px rgba(39, 23, 73, .2);
            transition: left .24s ease, background-color .18s ease, transform .18s ease;
        }

        .admin-sidebar-toggle-floating[aria-expanded="true"] {
            left: calc(.75rem + min(88vw, 248px));
        }

        .admin-sidebar-toggle-floating:hover {
            background: #eee8ff;
            transform: scale(1.08);
        }

        .sidebar-menu-button {
            overflow: hidden;
            background: #8f72f5;
            border-color: #8f72f5;
            color: #fff;
            box-shadow: 0 5px 14px rgba(143, 114, 245, .3);
            transition: left .24s ease, background-color .18s ease, color .18s ease, transform .18s ease;
        }

        .sidebar-menu-button:hover,
        .sidebar-menu-button:focus-visible {
            background: #795ce4;
            border-color: #795ce4;
            color: #fff;
        }

        .sidebar-menu-line {
            position: absolute;
            width: 24px;
            height: 3px;
            border-radius: 999px;
            background: currentColor;
            transition: transform .4s ease, width .4s ease;
        }

        .sidebar-menu-line--top,
        .sidebar-menu-line--bottom { width: 18px; }
        .sidebar-menu-line--top { transform: translateY(-7px); }
        .sidebar-menu-line--bottom { transform: translateY(7px); }
        .sidebar-menu-button.active {
            background: #fff;
            border-color: #8f72f5;
            color: #8f72f5;
        }
        .sidebar-menu-button.active .sidebar-menu-line--center { transform: translateX(-3rem); }
        .sidebar-menu-button.active .sidebar-menu-line--top { width: 24px; transform: rotate(45deg); }
        .sidebar-menu-button.active .sidebar-menu-line--bottom { width: 24px; transform: rotate(-45deg); }

        body[data-admin-theme="dark"] .admin-sidebar-toggle-floating.active {
            background: #fff;
            border-color: #8f72f5;
            color: #8f72f5;
        }

        .admin-sidebar .admin-nav-group {
            margin: 0;
        }

        .admin-sidebar .admin-subnav {
            display: block;
            margin: -.25rem 0 .45rem 2.85rem;
            padding: .38rem .55rem;
            border-radius: .65rem;
            color: #665c7d;
            text-decoration: none;
            font-size: .79rem;
            font-weight: 600;
        }

        .admin-sidebar.active .admin-subnav:hover,
        .admin-sidebar.active .admin-subnav.active {
            color: #6b4bd4;
            background: #f2edff;
        }

        body[data-admin-theme="dark"] {
            background: #100b1d;
            color: #eee8ff;
            transition: background-color .3s ease, color .3s ease;
        }

        body[data-admin-theme="dark"] .admin-main {
            background: #100b1d;
            color: #eee8ff;
            color-scheme: dark;
        }

        body[data-admin-theme="dark"] .main-content:not(.admin-main) {
            background: #100b1d;
            color: #eee8ff;
            color-scheme: dark;
        }

        body[data-admin-theme="dark"] .student-sidebar,
        body[data-admin-theme="dark"] .student-navbar {
            background: #171126;
            border-color: #302348;
            color: #eee8ff;
        }

        body[data-admin-theme="dark"] .student-sidebar .nav-link-custom,
        body[data-admin-theme="dark"] .student-navbar .student-nav-link,
        body[data-admin-theme="dark"] .student-navbar .profile-menu-btn {
            color: #ddd1ff;
        }

        body[data-admin-theme="dark"] .student-sidebar .nav-link-custom:hover,
        body[data-admin-theme="dark"] .student-sidebar .nav-link-custom:focus-visible,
        body[data-admin-theme="dark"] .student-navbar .student-nav-link:hover,
        body[data-admin-theme="dark"] .student-navbar .student-nav-link.active {
            background: #302348 !important;
            color: #facc15 !important;
        }

        body[data-admin-theme="dark"] .student-navbar .profile-menu-btn,
        body[data-admin-theme="dark"] .student-navbar .profile-dropdown {
            border-color: #3a2d50;
            background: #211936;
        }

        body[data-admin-theme="dark"] .student-navbar .profile-dropdown-action {
            background: #302348;
            color: #eee8ff;
        }

        body[data-admin-theme="dark"] .student-navbar .profile-dropdown-head {
            border-color: #3a2d50;
        }

        body[data-admin-theme="dark"] .admin-sidebar {
            background: #171126;
            border-color: #302348;
            color: #eee8ff;
            scrollbar-color: #594478 transparent;
        }

        body[data-admin-theme="dark"] .admin-sidebar .navbar-brand-custom,
        body[data-admin-theme="dark"] .admin-sidebar-footer {
            background-color: #211936;
            border-color: #302348;
        }

        body[data-admin-theme="dark"] .admin-sidebar-toggle-floating {
            background: #211936;
            border-color: #3a2d50;
            color: #d7caff;
        }

        body[data-admin-theme="dark"] .admin-sidebar-toggle-floating:hover {
            background: #302348;
        }

        body[data-admin-theme="dark"] .admin-main > .top-navbar {
            background: #171126;
            border-color: #302348;
            color: #eee8ff;
        }

        body[data-admin-theme="dark"] .admin-main > .top-navbar .brand-descriptor,
        body[data-admin-theme="dark"] .admin-main > .top-navbar .text-muted {
            color: #bdb1d1 !important;
        }

        body[data-admin-theme="dark"] .admin-main > .top-navbar .profile-menu-btn {
            background: #211936;
            border-color: #3a2d50;
            color: #eee8ff;
        }

        body[data-admin-theme="dark"] .admin-main .bg-white,
        body[data-admin-theme="dark"] .admin-main .bg-light {
            background-color: #1b1529 !important;
            color: #eee8ff !important;
        }

        body[data-admin-theme="dark"] .admin-main .card,
        body[data-admin-theme="dark"] .admin-main .modal-content {
            background-color: #1b1529 !important;
            border-color: #332846 !important;
            color: #eee8ff !important;
        }

        body[data-admin-theme="dark"] .admin-main .text-dark,
        body[data-admin-theme="dark"] .admin-main .text-secondary {
            color: #eee8ff !important;
        }

        body[data-admin-theme="dark"] .admin-main .text-muted {
            color: #bdb1d1 !important;
        }

        body[data-admin-theme="dark"] .admin-main .form-control,
        body[data-admin-theme="dark"] .admin-main .form-select,
        body[data-admin-theme="dark"] .admin-main .input-group-text {
            background-color: #211936 !important;
            border-color: #49365f !important;
            color: #eee8ff !important;
        }

        body[data-admin-theme="dark"] .admin-main .form-control::placeholder {
            color: #bdb1d1 !important;
        }

        body[data-admin-theme="dark"] .admin-main .form-select option {
            background-color: #211936;
            color: #eee8ff;
        }

        body[data-admin-theme="dark"] .admin-main .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        body[data-admin-theme="dark"] .admin-main .dropdown-menu {
            background-color: #1b1529;
            border-color: #332846;
        }

        body[data-admin-theme="dark"] .admin-main .dropdown-item {
            color: #eee8ff;
        }

        body[data-admin-theme="dark"] .admin-main .dropdown-item:hover,
        body[data-admin-theme="dark"] .admin-main .dropdown-item:focus {
            background-color: #302348;
            color: #fff;
        }

        body[data-admin-theme="dark"] .admin-main .messages-page {
            background: #100b1d !important;
            color: #eee8ff;
        }

        body[data-admin-theme="dark"] .admin-main .messages-page .left-panel,
        body[data-admin-theme="dark"] .admin-main .messages-page .right-panel,
        body[data-admin-theme="dark"] .admin-main .messages-page .messaging-header,
        body[data-admin-theme="dark"] .admin-main .messages-page .messaging-input-area {
            background: #1b1529 !important;
            border-color: #332846 !important;
            color: #eee8ff;
        }

        body[data-admin-theme="dark"] .admin-main .messages-page .messaging-thread {
            background: #100b1d !important;
        }

        body[data-admin-theme="dark"] .admin-main .messages-page .panel-title,
        body[data-admin-theme="dark"] .admin-main .messages-page .conv-name,
        body[data-admin-theme="dark"] .admin-main .messages-page .msg-name {
            color: #eee8ff;
        }

        body[data-admin-theme="dark"] .admin-main .messages-page .panel-sub,
        body[data-admin-theme="dark"] .admin-main .messages-page .conv-preview,
        body[data-admin-theme="dark"] .admin-main .messages-page .conv-time,
        body[data-admin-theme="dark"] .admin-main .messages-page .msg-email {
            color: #bdb1d1;
        }

        body[data-admin-theme="dark"] .admin-main .messages-page .conversation-item {
            border-color: #332846;
            color: #eee8ff;
        }

        body[data-admin-theme="dark"] .admin-main .messages-page .conversation-item:hover,
        body[data-admin-theme="dark"] .admin-main .messages-page .conversation-item.active {
            background: #302348 !important;
        }

        body[data-admin-theme="dark"] .admin-main .messages-page .message-group.admin .message-bubble {
            background: #211936;
            border-color: #49365f;
            color: #eee8ff;
        }

        body[data-admin-theme="dark"] .admin-main .messages-page .message-input,
        body[data-admin-theme="dark"] .admin-main .messages-page .message-edit-input {
            background: #211936;
            border-color: #49365f;
            color: #eee8ff;
        }

        body[data-admin-theme="dark"] .admin-main .modal-header,
        body[data-admin-theme="dark"] .admin-main .modal-footer {
            border-color: #332846 !important;
        }

        body[data-admin-theme="dark"] .admin-main .modal-content .border,
        body[data-admin-theme="dark"] .admin-main .modal-content .border-bottom,
        body[data-admin-theme="dark"] .admin-main .modal-content .border-top {
            border-color: #3a2d50 !important;
        }

        body[data-admin-theme="dark"] .admin-main .appointment-detail-grid > div,
        body[data-admin-theme="dark"] .admin-main .resume-grid > div,
        body[data-admin-theme="dark"] .admin-main .resume-id-image {
            background: #211936 !important;
            border-color: #3a2d50 !important;
            color: #eee8ff !important;
        }

        body[data-admin-theme="dark"] .admin-main .appointment-detail-grid span,
        body[data-admin-theme="dark"] .admin-main .resume-grid span {
            color: #bdb1d1 !important;
        }

        body[data-admin-theme="dark"] .admin-main .resume-grid strong,
        body[data-admin-theme="dark"] .admin-main .appointment-user-link {
            color: #eee8ff !important;
        }

        body[data-admin-theme="dark"] .admin-main .appointment-filters .appointment-filter {
            background: #211936;
            border-color: #49365f;
            color: #d7caff;
        }

        body[data-admin-theme="dark"] .admin-main .appointment-filters .appointment-filter span {
            background: #302348;
            color: #eee8ff;
        }

        body[data-admin-theme="dark"] .admin-main .appointment-filters .appointment-filter.active {
            background: #7658df;
            color: #fff;
        }

        body[data-admin-theme="dark"] .admin-main .messages-page .message-user-search-results,
        body[data-admin-theme="dark"] .admin-main .messages-page .message-user-search-results a,
        body[data-admin-theme="dark"] .admin-main .messages-page .message-original {
            background: #211936 !important;
            border-color: #3a2d50 !important;
            color: #ddd1ff !important;
        }

        body[data-admin-theme="dark"] .admin-main .messages-page .message-user-search-results a:hover,
        body[data-admin-theme="dark"] .admin-main .messages-page .message-user-search-results a:focus-visible {
            background: #302348 !important;
        }

        body[data-admin-theme="dark"] .admin-main .messages-page .message-user-search-results small,
        body[data-admin-theme="dark"] .admin-main .messages-page .message-user-search-empty,
        body[data-admin-theme="dark"] .admin-main .messages-page .message-time,
        body[data-admin-theme="dark"] .admin-main .messages-page .message-action,
        body[data-admin-theme="dark"] .admin-main .messages-page .message-edited-link {
            color: #c9bde8 !important;
        }

        body[data-admin-theme="dark"] .admin-main .messages-page .message-bubble.is-deleted {
            background: #302348 !important;
            border-color: #49365f !important;
            color: #d7caff !important;
        }

        body[data-admin-theme="dark"] .admin-main .call-item,
        body[data-admin-theme="dark"] .admin-main .fw-wrap,
        body[data-admin-theme="dark"] .admin-main .fw-topbar,
        body[data-admin-theme="dark"] .admin-main .fw-table-card,
        body[data-admin-theme="dark"] .admin-main .fw-card,
        body[data-admin-theme="dark"] .admin-main .fw-empty,
        body[data-admin-theme="dark"] .admin-main .announcement-hero {
            background: #1b1529 !important;
            border-color: #332846 !important;
            color: #eee8ff !important;
        }

        body[data-admin-theme="dark"] .admin-main .fw-page-title,
        body[data-admin-theme="dark"] .admin-main .fw-cat-label,
        body[data-admin-theme="dark"] .admin-main .fw-msg,
        body[data-admin-theme="dark"] .admin-main .fw-empty-title {
            color: #eee8ff !important;
        }

        body[data-admin-theme="dark"] .admin-main .fw-count-chip,
        body[data-admin-theme="dark"] .admin-main .fw-cat,
        body[data-admin-theme="dark"] .admin-main .fw-btn-del {
            background: #302348 !important;
            border-color: #49365f !important;
            color: #ddd1ff !important;
        }

        body[data-admin-theme="dark"] .admin-main .fw-count-num,
        body[data-admin-theme="dark"] .admin-main .fw-count-text,
        body[data-admin-theme="dark"] .admin-main .fw-cat-icon {
            color: #d7caff !important;
        }

        body[data-admin-theme="dark"] .admin-main .fw-card-foot {
            border-color: #332846 !important;
        }

        body[data-admin-theme="dark"] .admin-main .fw-page-sub,
        body[data-admin-theme="dark"] .admin-main .fw-time,
        body[data-admin-theme="dark"] .admin-main .fw-author-name,
        body[data-admin-theme="dark"] .admin-main .fw-empty-sub,
        body[data-admin-theme="dark"] .admin-main .call-item .text-muted {
            color: #bdb1d1 !important;
        }

        body[data-admin-theme="dark"] .admin-main .sinag-persistent-notice {
            background: #211936 !important;
            border-color: #49365f !important;
            color: #ddd1ff !important;
        }

        body[data-admin-theme="dark"] .admin-main .sinag-persistent-notice.alert-success {
            background: #173327 !important;
            border-color: #276247 !important;
            color: #b8f5d0 !important;
        }

        body[data-admin-theme="dark"] .admin-main .sinag-persistent-notice.alert-warning {
            background: #3b2b15 !important;
            border-color: #76521f !important;
            color: #ffe0a6 !important;
        }

        body[data-admin-theme="dark"] .admin-main .sinag-persistent-notice.alert-danger {
            background: #3a1c2a !important;
            border-color: #71344d !important;
            color: #ffc3d5 !important;
        }

        body[data-admin-theme="dark"] .admin-main #userTable tbody {
            background: #100b1d !important;
        }

        body[data-admin-theme="dark"] .admin-main #userTable tbody tr[data-status] {
            background: #1b1529 !important;
            border-color: #3a2d50 !important;
            color: #eee8ff !important;
        }

        body[data-admin-theme="dark"] .admin-main #userTable tbody tr[data-status] td:last-child {
            border-color: #3a2d50 !important;
        }

        body[data-admin-theme="dark"] .admin-main .appointments-page .sinag-persistent-notice {
            background: #211936 !important;
            border-color: #49365f !important;
            color: #d7caff !important;
        }

        .main-content.admin-main {
            min-height: 100vh;
            min-height: 100dvh;
        }

        .admin-main > .container,
        .admin-main > .container-fluid,
        .admin-main > .messages-page,
        .admin-main > .gad-page,
        .admin-main > .urgent-admin-page,
        .admin-main > .fw-wrap {
            min-width: 0;
            flex: 1 0 auto;
            margin-top: 0 !important;
            padding-top: .5rem !important;
            padding-bottom: .75rem !important;
        }

        .admin-main .admin-data-page {
            padding-top: .5rem !important;
            padding-bottom: .75rem !important;
        }

        body[data-admin-theme="dark"] .admin-sidebar .nav-link-custom {
            color: #c9bde8;
        }

        body[data-admin-theme="dark"] .admin-sidebar .admin-subnav {
            color: #c9bde8;
        }

        @media (min-width: 1200px) {
            .main-wrapper {
                height: auto;
                min-height: 100dvh;
                overflow: visible;
            }

            .sidebar.active {
                position: sticky;
                top: 0;
                height: 100dvh;
                max-height: 100dvh;
                overflow-y: auto;
                overscroll-behavior: contain;
                -webkit-overflow-scrolling: touch;
            }

            .admin-sidebar:not(.active) {
                position: fixed;
                inset: 5.5rem auto 1rem .75rem;
                z-index: 1101;
                width: 60px;
                flex: 0 0 60px;
                height: auto;
                max-height: calc(100dvh - 6.5rem);
                padding: .55rem .35rem;
                border-radius: 1.25rem;
                overflow-y: auto;
            }

            .admin-sidebar.active {
                position: fixed;
                inset: 5.5rem auto 1rem .75rem;
                z-index: 1101;
                width: min(88vw, 248px);
                flex: 0 0 min(88vw, 248px);
                height: auto;
                max-height: calc(100dvh - 6.5rem);
                padding: 1rem;
                border-radius: 1.25rem;
                overflow-y: auto;
                box-shadow: 12px 0 32px rgba(15, 23, 42, .16);
            }

            .admin-sidebar.active > .d-flex:first-child > .btn {
                display: inline-flex !important;
            }

            .main-content {
                flex: 1 1 auto;
                height: auto;
                min-height: 100dvh;
                overflow: visible;
            }

            .main-wrapper .admin-main {
                width: auto;
                margin-left: 88px;
                min-width: 0;
                flex: 1 1 0%;
            }
        }

        @media (max-width: 1199.98px) {
            .admin-sidebar {
                position: fixed;
                inset: 0 auto 0 0;
                z-index: 1101;
                width: min(88vw, 280px);
                flex: 0 0 min(88vw, 280px);
                min-width: 0;
                height: 100vh;
                height: 100dvh;
                max-height: 100dvh;
                padding: max(1.25rem, env(safe-area-inset-top)) 1rem max(1.25rem, env(safe-area-inset-bottom));
                border-radius: 0 1.25rem 1.25rem 0;
                overflow-y: auto;
                transform: translateX(calc(-100% - 1rem));
                visibility: hidden;
                transition: transform .24s ease, visibility .24s ease;
            }

            .main-wrapper .admin-sidebar.active {
                position: fixed;
                inset: 0 auto 0 0;
                width: min(88vw, 280px);
                flex-basis: min(88vw, 280px);
                height: 100vh;
                height: 100dvh;
                max-height: 100dvh;
                padding: max(1.25rem, env(safe-area-inset-top)) 1rem max(1.25rem, env(safe-area-inset-bottom));
                border-radius: 0 1.25rem 1.25rem 0;
                overflow-y: auto;
                transform: translateX(0);
                visibility: visible;
                box-shadow: 12px 0 32px rgba(15, 23, 42, .16);
            }

            .admin-sidebar.active .admin-sidebar-close {
                display: inline-flex !important;
            }

            .admin-sidebar-toggle-floating {
                display: none !important;
            }

            .admin-mobile-sidebar-toggle {
                display: inline-flex !important;
            }

            .main-wrapper .admin-main {
                width: auto;
                max-width: none;
                min-width: 0;
                margin-left: 0;
                flex: 1 1 0%;
            }
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

        .dashboard-logo-sunburst img {
            transform-origin: center;
            animation: dashboardLogoSunburst 1.4s ease-out 1 both;
        }

        .dashboard-logo-sunburst img:nth-child(2) { animation-delay: .08s; }

        @keyframes dashboardLogoSunburst {
            0% { filter: drop-shadow(0 0 0 rgba(250, 204, 21, 0)); transform: scale(1); }
            35% { filter: drop-shadow(0 0 4px #fff7bf) drop-shadow(0 0 13px #facc15); transform: scale(1.08); }
            100% { filter: drop-shadow(0 0 0 rgba(250, 204, 21, 0)); transform: scale(1); }
        }

        @media (prefers-reduced-motion: reduce) {
            .dashboard-logo-sunburst img { animation: none; }
        }

        @media (min-width: 1200px) {
            .top-navbar.scroll-direction-nav {
                position: sticky;
                top: 0;
                z-index: 1030;
                transform: translateY(0);
                transition: transform .22s ease, box-shadow .22s ease;
            }

            .top-navbar.scroll-direction-nav.is-hidden {
                transform: translateY(calc(-100% - 2px));
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
            transform: translateX(6px) scale(1.02);
            box-shadow: 0 10px 22px rgba(165, 148, 249, 0.18);
        }

        .nav-link-custom i { font-size: 1.2rem; margin-right: 1rem; }

        .nav-link-custom.active {
            background-color: #A594F9 !important;
            color: white !important;
            box-shadow: 0 4px 12px rgba(165, 148, 249, 0.28);
            transform: translateX(3px) scale(1.01);
        }

        .student-sidebar .nav-link-custom {
            transition: background-color .18s ease, color .18s ease, transform .18s ease, box-shadow .18s ease;
        }

        .student-sidebar .nav-link-custom:hover,
        .student-sidebar .nav-link-custom:focus-visible {
            background: #f3efff;
            color: #7658df;
            transform: translateX(6px) scale(1.02);
            box-shadow: 0 8px 20px rgba(143, 114, 245, .18);
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

        .page-load-motion > :not(.top-navbar):not(.student-navbar) {
            animation: pageContentReveal .45s ease-out both;
        }

        @keyframes pageContentReveal {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @media (prefers-reduced-motion: reduce) {
            .page-load-motion > :not(.top-navbar):not(.student-navbar) {
                animation: none;
            }
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

        .admin-mobile-sidebar-toggle {
            display: none;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            padding: 0;
            border: 0;
            border-radius: 10px;
            background: #8f72f5;
            color: #fff;
            box-shadow: 0 2px 8px rgba(165, 148, 249, .2);
        }

        .admin-mobile-sidebar-toggle,
        .student-sidebar-toggle {
            position: relative;
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

        @media (max-width: 767.98px) {
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

            .top-navbar.student-navbar > .d-flex:first-child {
                justify-content: flex-start !important;
            }

            .top-navbar.student-navbar > .d-flex:first-child > .student-sidebar-toggle {
                flex: 0 0 42px;
                margin: 0 !important;
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

        @media (min-width: 768px) {
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

        body[data-admin-theme="dark"] .admin-main .table-responsive {
            background: #1b1529 !important;
            color: #eee8ff !important;
        }

        body[data-admin-theme="dark"] .admin-main :is(table, .table) {
            --bs-table-color: #eee8ff;
            --bs-table-bg: #1b1529;
            --bs-table-border-color: #49365f;
            --bs-table-striped-color: #eee8ff;
            --bs-table-striped-bg: #211936;
            --bs-table-hover-color: #fff;
            --bs-table-hover-bg: #302348;
            background: #1b1529 !important;
            color: #eee8ff !important;
        }

        body[data-admin-theme="dark"] .admin-main :is(table, .table) :is(th, td) {
            background: #1b1529 !important;
            background-color: #1b1529 !important;
            background-image: none !important;
            box-shadow: none !important;
            color: #eee8ff !important;
            border-color: #49365f !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .profile-dropdown-action {
            background: #302348 !important;
            color: #eee8ff !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .profile-dropdown-action:hover,
        html[data-student-theme-enabled="true"][data-student-theme="dark"] .profile-dropdown-action:focus-visible {
            background: #493568 !important;
            color: #fff !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .profile-dropdown .dropdown-divider {
            border-color: #49365f !important;
        }

        body[data-admin-theme="dark"] .admin-main :is(table, .table) :is(th, td) :not(.badge):not(.btn) {
            color: #eee8ff !important;
        }

        body[data-admin-theme="dark"] .admin-main :is(table, .table) thead :is(th, td) {
            background: #2a203e !important;
            background-color: #2a203e !important;
            color: #e5dcff !important;
        }

        body[data-admin-theme="dark"] .admin-main :is(table, .table) tbody tr:nth-child(even) :is(th, td) {
            background: #211936 !important;
            background-color: #211936 !important;
        }

        body[data-admin-theme="dark"] .admin-main :is(table, .table) tbody tr:hover :is(th, td) {
            background: #302348 !important;
            background-color: #302348 !important;
            color: #fff !important;
        }

        .student-floating-enabled > .d-flex:last-child {
            position: relative !important;
            inset: auto !important;
            z-index: 1040 !important;
            width: max-content !important;
            max-width: calc(100vw - 1rem) !important;
            min-width: 0;
            padding: .55rem .7rem !important;
            margin: .35rem auto 0 !important;
            display: flex !important;
            align-items: center !important;
            flex-wrap: nowrap !important;
            gap: .45rem !important;
            overflow: visible !important;
            border: 1px solid #e7def5;
            border-radius: 1.35rem;
            background: rgba(255, 255, 255, .96);
            box-shadow: 0 14px 40px rgba(40, 27, 66, .18);
            backdrop-filter: blur(16px);
            transform: none !important;
            transition: box-shadow .2s ease, background-color .2s ease;
        }

        .student-floating-enabled > .d-flex:last-child:hover {
            box-shadow: 0 18px 44px rgba(40, 27, 66, .22);
        }

        .student-floating-enabled {
            transform: none !important;
        }

        .student-floating-enabled > .d-flex:first-child {
            justify-content: flex-start !important;
            min-width: 0;
        }

        .student-floating-enabled .navbar-brand-custom {
            justify-content: flex-start !important;
            margin: 0 !important;
        }

        .student-floating-enabled .brand-descriptor {
            text-align: left !important;
        }

        .student-floating-enabled .student-mobile-scroll-hint {
            display: none !important;
        }

        @media (min-width: 768px) {
            .student-floating-enabled > .d-flex:last-child .student-top-nav {
                display: flex !important;
                align-items: center;
                flex: 0 1 auto;
                min-width: 0;
                width: auto;
                margin: 0 !important;
                padding: 0;
                gap: .2rem;
                flex-wrap: nowrap;
                overflow: visible;
            }

            .student-floating-enabled > .d-flex:last-child .student-nav-link {
                white-space: nowrap;
            }

            .student-floating-enabled > .d-flex:last-child > a[href*="/messaging"] {
                flex: 0 0 auto;
                display: inline-flex !important;
                align-items: center;
                justify-content: center;
                margin: 0 !important;
                white-space: nowrap;
                border-radius: 1rem !important;
            }
        }

        .student-floating-enabled .profile-menu-btn {
            white-space: nowrap;
        }

        .student-floating-enabled .profile-dropdown {
            border-radius: 1rem;
            border: 1px solid #e7def5;
            box-shadow: 0 16px 36px rgba(40, 27, 66, .2);
        }

        @media (max-width: 991.98px) {
            .student-floating-enabled > .d-flex:last-child {
                grid-column: 1 / -1;
                grid-row: 2;
                justify-self: center;
                gap: .2rem !important;
                padding: .45rem !important;
                border-radius: 1.15rem;
            }

            .student-floating-enabled > .d-flex:last-child .student-top-nav {
                gap: .1rem;
            }

            .student-floating-enabled > .d-flex:last-child .student-nav-link {
                min-width: 38px;
                min-height: 40px;
                padding: .45rem .3rem;
            }

            .student-floating-enabled > .d-flex:last-child > a[href*="/messaging"] {
                min-width: 38px;
                min-height: 40px;
                padding: .45rem .5rem !important;
            }

            .student-floating-enabled > .d-flex:last-child > .text-end {
                position: static !important;
                top: auto !important;
                right: auto !important;
                transform: none !important;
            }
        }

        @media (max-width: 575.98px) {
            .student-floating-enabled > .d-flex:last-child {
                max-width: calc(100vw - .75rem) !important;
                gap: .1rem !important;
            }

            .student-floating-enabled .student-nav-link {
                min-width: 34px !important;
                padding-inline: .22rem !important;
            }

            .student-floating-enabled .profile-avatar {
                width: 30px;
                height: 30px;
                flex-basis: 30px;
            }
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] {
            color-scheme: dark;
            --bs-body-bg: #15101f;
            --bs-body-color: #eee8ff;
            --bs-heading-color: #eee8ff;
            --bs-emphasis-color: #eee8ff;
            --bs-secondary-color: #b7aacb;
            --bs-link-color: #b9a5ff;
            --surface: #15101f;
            --card: #20192d;
            --text: #eee8ff;
            --muted: #b7aacb;
            --border: rgba(196, 181, 253, .18);
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] body {
            background: #15101f !important;
            background-color: #15101f !important;
            color: #eee8ff;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) {
            background: #15101f !important;
            color: #eee8ff;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .student-floating-enabled {
            background: #1b1529;
            border-color: #352747;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .student-floating-enabled .navbar-brand-custom,
        html[data-student-theme-enabled="true"][data-student-theme="dark"] .student-floating-enabled .brand-descriptor {
            color: #d7c9f3 !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .student-floating-enabled > .d-flex:last-child {
            background: rgba(32, 25, 45, .97);
            border-color: #49365f;
            box-shadow: 0 14px 40px rgba(0, 0, 0, .38);
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .student-nav-link {
            color: #ded3f4;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .student-nav-link:hover,
        html[data-student-theme-enabled="true"][data-student-theme="dark"] .student-nav-link:focus-visible {
            background: #302348;
            color: #fff;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .profile-dropdown,
        html[data-student-theme-enabled="true"][data-student-theme="dark"] .student-nav-menu,
        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .dropdown-menu,
        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .modal-content {
            background: #211a2e !important;
            color: #eee8ff !important;
            border-color: #49365f !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .student-nav-menu .dropdown-item,
        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .dropdown-item {
            color: #eee8ff;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .student-nav-menu .dropdown-item:hover,
        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .dropdown-item:hover {
            background: #302348;
            color: #fff;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .student-sidebar {
            background: #1b1529 !important;
            border-color: #49365f !important;
            color: #eee8ff !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .student-sidebar > div > .navbar-brand-custom {
            background: #302348 !important;
            border: 1px solid #49365f;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .student-sidebar .nav-link-custom {
            color: #ded3f4 !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .student-sidebar .nav-link-custom:hover,
        html[data-student-theme-enabled="true"][data-student-theme="dark"] .student-sidebar .nav-link-custom:focus-visible {
            background: #302348 !important;
            color: #fff !important;
            box-shadow: 0 8px 20px rgba(0, 0, 0, .24);
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .student-sidebar .nav-link-custom.active,
        html[data-student-theme-enabled="true"][data-student-theme="dark"] .student-sidebar .nav-link-custom.active:hover,
        html[data-student-theme-enabled="true"][data-student-theme="dark"] .student-sidebar .nav-link-custom.active:focus-visible {
            background: #7658df !important;
            color: #fff !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .student-sidebar .text-muted,
        html[data-student-theme-enabled="true"][data-student-theme="dark"] .student-sidebar p {
            color: #b7aacb !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .student-sidebar hr {
            border-color: #594478 !important;
            opacity: .8 !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) :is(
            .card, .vault-glass, .report-glass, .msg-chat-card, .msg-info-card,
            .list-group-item, .table-responsive, .table, .alert, .customer-satisfaction-card,
            .bg-white, .bg-light, .alert-light
        ) {
            background-color: #20192d !important;
            color: #eee8ff !important;
            border-color: #49365f !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) :is(
            .form-control, .form-select, textarea, .msg-textarea, .msg-edit-input
        ) {
            background-color: #1b1529 !important;
            color: #eee8ff !important;
            border-color: #49365f !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) :is(
            .form-control, .form-select, textarea
        )::placeholder {
            color: #b7aacb !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) :is(
            .text-dark, .text-body, .modal-title, .modal-body, .card-title
        ) {
            color: #eee8ff !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .text-muted {
            color: #b7aacb !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .modal-header,
        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .modal-footer,
        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .msg-input-area,
        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .msg-identity-control {
            border-color: #49365f !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .msg-identity-control {
            background: #20192d !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .msg-identity-choice {
            color: #ded3f4;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .student-settings-page .theme-choice {
            background: #1b1529;
            border-color: #49365f;
            color: #eee8ff;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) :is(
            .msg-page, .vault-page, .report-page, .urgent-page
        ) {
            background: #15101f !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) :is(
            .pd-card, .pd-edit-card, .pd-edit-section
        ) {
            background: #20192d !important;
            background-image: none !important;
            border-color: #49365f !important;
            color: #eee8ff !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .pd-item {
            background: #211a2e !important;
            border-color: #49365f !important;
            color: #eee8ff !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) :is(
            .pd-section-title, .pd-edit-section-title, .pd-form-section, .pd-value
        ) {
            color: #eee8ff !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) :is(
            .pd-label, .pd-note, .pd-form-note
        ) {
            color: #b7aacb !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .pd-table-wrap {
            border-color: #49365f !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .pd-table th,
        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .pd-table td {
            background: #211a2e !important;
            border-color: #49365f !important;
            color: #eee8ff !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .pd-table th {
            background: #302348 !important;
            color: #d5c6ff !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .pd-pill {
            background: #302348 !important;
            border-color: #594478 !important;
            color: #ded3f4 !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .pd-edit-page {
            --pd-ink: #eee8ff;
            --pd-violet-deep: #d5c6ff;
            --pd-violet-soft: #211a2e;
            color: #eee8ff;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .pd-edit-actions {
            border-color: #49365f !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .pd-page .btn {
            background: #302348 !important;
            border-color: #594478 !important;
            color: #ded3f4 !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .urgent-page h2 {
            color: #d5c6ff !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .urgent-page p,
        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .urgent-page .instructions {
            color: #b7aacb !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .student-dashboard-page {
            --ink: #eee8ff !important;
            --muted: #b7aacb !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) :is(
            .dashboard-card, .dashboard-panel, .dashboard-news-events, .dashboard-event-card,
            .announcement-item, .daily-quote-card, .reference-card, .vision-mission-panel
        ) {
            background: #20192d !important;
            background-color: #20192d !important;
            color: #eee8ff !important;
            border-color: #49365f !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) :is(
            .dashboard-panel .card-header, .dashboard-event-card-body
        ) {
            background: #211a2e !important;
            color: #eee8ff !important;
            border-color: #49365f !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .vision-mission-grid {
            background: #20192d !important;
            border: 1px solid #49365f !important;
            box-shadow: 0 16px 38px rgba(0, 0, 0, .22) !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) :is(
            .section-title, .dashboard-news-events h2, .dashboard-event-card-title,
            .reference-card-title, .daily-quote-text, .dashboard-card h1,
            .dashboard-card h2, .dashboard-card h3, .vision-mission-panel h2
        ) {
            color: #eee8ff !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) :is(
            .section-subtitle, .dashboard-event-card-body, .reference-card-body,
            .announcement-meta, .dashboard-news-events p, .vision-mission-panel p
        ) {
            color: #b7aacb !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) :is(
            .announcement-shell, .article-shell
        ) {
            background: #15101f !important;
            color: #eee8ff !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) :is(
            .announcement-card, .article-card
        ) {
            background: #20192d !important;
            color: #eee8ff !important;
            border-color: #49365f !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) :is(
            .announcement-title, .announcement-lead, .announcement-body,
            .article-title, .article-lead, .article-body
        ) {
            color: #eee8ff !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) :is(
            .announcement-subtitle, .article-subtitle
        ) {
            color: #b7aacb !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .appointment-list-card .col-lg-4 {
            background: #211a2e !important;
            border-color: #49365f !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .schedule-appointment-page :is(
            .appointment-format-option, .appointment-format-option small
        ) {
            color: #ded3f4 !important;
            background: #20192d !important;
            border-color: #594478 !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .schedule-appointment-page .btn-check:checked + .appointment-format-option {
            color: #fff !important;
            background: #302348 !important;
            border-color: #947cf0 !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .schedule-appointment-page .alert-info {
            color: #d7ecff !important;
            background: #17283a !important;
            border-color: #34516e !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .report-page :is(
            .report-anon, .report-hotline, .report-help
        ) {
            background: #211a2e !important;
            background-color: #211a2e !important;
            border-color: #49365f !important;
            color: #eee8ff !important;
            box-shadow: 0 8px 22px rgba(0, 0, 0, .18) !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .report-page .report-hotline :is(p, span) {
            color: #ded3f4 !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .report-page .btn-light {
            color: #eee8ff !important;
            background: #302348 !important;
            border: 1px solid #594478 !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .report-page .btn-light:hover,
        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .report-page .btn-light:focus-visible {
            color: #fff !important;
            background: #493568 !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .report-page input[type="file"]::file-selector-button {
            color: #eee8ff !important;
            background: #302348 !important;
            border-color: #594478 !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .report-page input[type="file"]::file-selector-button:hover {
            background: #493568 !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .profile-page {
            background: #15101f !important;
            color: #eee8ff !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .profile-page :is(
            .profile-hero, .profile-card, .profile-stat
        ) {
            background: #20192d !important;
            border-color: #49365f !important;
            color: #eee8ff !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .profile-card.soft-panel {
            background: #211a2e !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .profile-page :is(
            .profile-name, .profile-value, .profile-section-title, .profile-detail .value
        ) {
            color: #eee8ff !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .profile-page :is(
            .profile-meta, .profile-label, .profile-detail .label, .text-muted
        ) {
            color: #b7aacb !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .profile-detail {
            border-color: #49365f !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .profile-action-btn {
            color: #ded3f4 !important;
            background: #302348 !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .profile-action-btn:hover {
            color: #fff !important;
            background: #493568 !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .profile-input {
            color: #eee8ff !important;
            background: #1b1529 !important;
            border-color: #49365f !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .gad-student .panel {
            color: #eee8ff !important;
            background: #20192d !important;
            border-color: #49365f !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .gad-student .cal-day:not(.day-empty) {
            color: #eee8ff !important;
            background: #211a2e !important;
            border-color: #49365f !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .gad-student .cal-day.day-scheduled {
            color: #ded3f4 !important;
            background: #302348 !important;
            border-color: #594478 !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .gad-student .cal-day.day-available {
            color: #b8f5d0 !important;
            background: #173327 !important;
            border-color: #276247 !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .gad-student .cal-day.day-unavailable {
            color: #ffc3d5 !important;
            background: #3a1c2a !important;
            border-color: #71344d !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .gad-student .day-schedule-time {
            color: #ded3f4 !important;
            background: #302348 !important;
            border-color: #594478 !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .gad-student .day-schedule-time.badge-available {
            color: #b8f5d0 !important;
            background: #173327 !important;
            border-color: #276247 !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .gad-student .day-schedule-time.badge-unavailable {
            color: #ffc3d5 !important;
            background: #3a1c2a !important;
            border-color: #71344d !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .boses-page {
            --ink: #eee8ff !important;
            --muted: #b7aacb !important;
            --surface: #20192d !important;
            --surface-strong: #211a2e !important;
            --border: rgba(196, 181, 253, .18) !important;
            background: #15101f !important;
            color: #eee8ff !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .boses-page :is(
            .stat-panel, .feed-panel, .side-panel, .suggestion-card, .compose-panel,
            .empty-panel, .suggestion-feed-table .suggestion-card, .suggestion-top,
            .suggestion-admin-note,
            .comment-card, .search-input, .kebab-btn, .suggestion-comment-trigger,
            .upvote-btn:not(.is-active), .action-menu
        ) {
            background: #20192d !important;
            background-color: #20192d !important;
            color: #eee8ff !important;
            border-color: #49365f !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .boses-page :is(
            .section-title, .stat-value, .suggestion-message, .comment-author,
            .comment-text, .section-subtitle, .meta-badge.time, .search-input
        ) {
            color: #eee8ff !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .boses-page :is(
            .suggestion-top, .suggestion-feed-table .suggestion-top
        ) {
            border-bottom-color: #49365f !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .boses-page :is(
            .suggestion-comment-trigger:hover, .suggestion-comment-trigger:focus-visible,
            .suggestion-comment-trigger[aria-expanded="true"], .action-menu .dropdown-item:hover
        ) {
            background: #302348 !important;
            color: #eee8ff !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) .msg-page {
            --surface: #15101f !important;
            --card: #20192d !important;
            --text: #eee8ff !important;
            --muted: #b7aacb !important;
            --border: rgba(196, 181, 253, .18) !important;
            background: #15101f !important;
            color: #eee8ff !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) :is(
            .msg-thread, .msg-input-area, .msg-info-details, .msg-info-row,
            .msg-identity-control, .msg-identity-note
        ) {
            background: #211a2e !important;
            color: #eee8ff !important;
            border-color: #49365f !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .main-content:not(.admin-main) :is(
            .msg-group.admin .msg-bubble, .msg-info-card, .msg-textarea
        ) {
            background-color: #20192d !important;
            color: #eee8ff !important;
            border-color: #49365f !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .student-floating-enabled > .d-flex:last-child .student-nav-link {
            color: #ded3f4;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .student-floating-enabled > .d-flex:last-child .student-nav-link.active,
        html[data-student-theme-enabled="true"][data-student-theme="dark"] .student-floating-enabled > .d-flex:last-child > a[href*="/messaging"] {
            background: #7658df !important;
            border-color: #947cf0 !important;
            color: #fff !important;
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .student-floating-enabled .profile-menu-btn,
        html[data-student-theme-enabled="true"][data-student-theme="dark"] .student-floating-enabled .profile-avatar {
            background: #302348 !important;
            border-color: #594478 !important;
            color: #eee8ff !important;
        }

        @media (max-width: 991.98px) {
            html[data-student-theme-enabled="true"] .student-floating-enabled > .d-flex:last-child {
                position: relative !important;
                inset: auto !important;
                transform: none !important;
                margin: .35rem auto 0 !important;
            }
        }

        @media (max-width: 767.98px) {
            .student-floating-enabled > .d-flex:last-child {
                background: transparent !important;
                border-color: transparent !important;
                box-shadow: none !important;
                backdrop-filter: none !important;
                padding: 0 !important;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .student-floating-enabled > .d-flex:last-child {
                transition: none;
            }
        }

        /* From Uiverse.io by ommanav_2060 - https://uiverse.io/ommanav_2060/odd-falcon-79
           MIT License, Copyright (c) ommanav_2060 (Om Manav) */
        .user-verification-image-trigger {
            perspective: 1000px;
        }

        .user-verification-image {
            transform-style: preserve-3d;
            transition: transform .35s cubic-bezier(.25, 1, .5, 1), box-shadow .35s ease;
            will-change: transform;
        }

        .user-verification-image:hover,
        .user-verification-image-trigger:hover .user-verification-image,
        .user-verification-image-trigger:focus-visible .user-verification-image {
            transform: perspective(1000px) rotateY(-7deg) rotateX(3deg) translateY(-4px) scale(1.02);
            box-shadow: 14px 18px 30px rgba(48, 35, 105, .28);
        }

        @media (prefers-reduced-motion: reduce) {
            .user-verification-image {
                transition: none;
                will-change: auto;
            }
        }

        @media (min-width: 768px) and (max-width: 1199.98px) {
            .student-navbar {
                display: flex !important;
                flex-direction: column !important;
                flex-wrap: nowrap !important;
                align-items: stretch !important;
                gap: .65rem !important;
                padding: .8rem 1rem !important;
            }

            .student-navbar > .d-flex:first-child {
                display: flex !important;
                flex: 0 0 auto !important;
                width: 100% !important;
                min-width: 0;
                justify-content: center !important;
                padding: 0 !important;
            }

            .student-navbar > .d-flex:first-child > .navbar-brand-custom {
                display: flex !important;
                flex-wrap: wrap;
                justify-content: center;
                align-items: center;
                width: min(100%, 900px);
                max-width: 100%;
                min-width: 0;
                margin: 0 !important;
                gap: .25rem .55rem !important;
            }

            .student-navbar > .d-flex:first-child > .navbar-brand-custom > div {
                flex: 0 0 auto;
            }

            .student-navbar > .d-flex:first-child > .navbar-brand-custom > span:not(.brand-descriptor) {
                flex: 0 0 auto;
            }

            .student-navbar > .d-flex:first-child > .navbar-brand-custom > .brand-descriptor {
                display: block !important;
                flex: 0 0 100%;
                max-width: 100%;
                min-width: 0;
                text-align: center !important;
                font-size: clamp(.68rem, 1vw, .8rem) !important;
                line-height: 1.2;
            }

            .student-navbar > .d-flex:last-child,
            .student-floating-enabled > .d-flex:last-child {
                position: relative !important;
                inset: auto !important;
                display: flex !important;
                flex: 0 0 auto !important;
                flex-wrap: nowrap !important;
                align-items: center !important;
                justify-content: center !important;
                width: 100% !important;
                max-width: 100% !important;
                min-width: 0;
                margin: 0 !important;
                padding: .35rem .5rem !important;
                gap: .35rem !important;
                overflow: visible !important;
                transform: none !important;
            }

            .student-navbar .student-top-nav,
            .student-floating-enabled > .d-flex:last-child .student-top-nav {
                display: flex !important;
                flex: 1 1 0%;
                width: auto;
                max-width: 100%;
                min-width: 0;
                flex-wrap: nowrap;
                justify-content: flex-start;
                order: 0;
                gap: .1rem;
                margin: 0 !important;
                overflow-x: auto;
                overflow-y: hidden;
                scrollbar-width: none;
                -webkit-overflow-scrolling: touch;
            }

            .student-navbar .student-top-nav::-webkit-scrollbar {
                display: none;
            }

            .student-navbar .student-top-nav:has(.dropdown-menu.show),
            .student-floating-enabled > .d-flex:last-child .student-top-nav:has(.dropdown-menu.show) {
                overflow: visible !important;
            }

            .student-navbar .student-nav-link {
                flex: 0 0 auto;
                min-width: 0;
                padding: .45rem .5rem;
                font-size: .75rem;
                white-space: nowrap;
            }

            .student-navbar .student-nav-link span {
                display: inline !important;
            }

            .student-navbar > .d-flex:last-child > a[href*="/messaging"] {
                display: inline-flex !important;
                flex: 0 0 auto;
                order: 1;
                margin: 0 !important;
                padding: .45rem .6rem !important;
            }

            .student-navbar > .d-flex:last-child > .text-end {
                position: static !important;
                top: auto !important;
                right: auto !important;
                order: 2;
                flex: 0 0 auto;
                margin: 0 0 0 auto !important;
                transform: none !important;
            }

            .student-navbar .profile-mini-name {
                display: none !important;
            }
        }

        html[data-student-theme-enabled="true"][data-student-theme="dark"] .student-floating-enabled > .d-flex:last-child {
            background: #211a2e;
            border-color: #49365f;
            box-shadow: 0 14px 40px rgba(0, 0, 0, .28);
        }

        .coordinator-navbar,
        body[data-admin-theme="dark"] .coordinator-navbar {
            background: #f8f6ff !important;
            border-bottom: 1px solid #dcd3ee !important;
            color: #34205f !important;
        }

        .top-navbar.student-navbar.coordinator-navbar {
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            align-items: center;
            gap: 1rem;
        }

        .top-navbar.student-navbar.coordinator-navbar > .d-flex:first-child > .navbar-brand-custom {
            display: flex !important;
            flex-wrap: nowrap !important;
            width: auto !important;
            min-width: 0;
            max-width: 100%;
            gap: .5rem !important;
        }

        .top-navbar.student-navbar.coordinator-navbar > .d-flex:first-child > .navbar-brand-custom > .brand-descriptor {
            display: none !important;
        }

        .coordinator-navbar > .d-flex:first-child {
            width: auto !important;
            flex: 0 0 auto !important;
            min-width: 0;
            justify-content: flex-start !important;
        }

        .coordinator-navbar > .d-flex:last-child {
            width: auto !important;
            flex: 1 1 auto !important;
            min-width: 0;
            justify-content: flex-end !important;
        }

        .coordinator-navbar .student-top-nav {
            gap: .25rem;
            min-width: 0;
            max-width: 100%;
        }

        .coordinator-navbar .student-nav-link,
        body[data-admin-theme="dark"] .coordinator-navbar .student-nav-link,
        html[data-student-theme-enabled="true"][data-student-theme="dark"] .coordinator-navbar .student-nav-link {
            border: 1px solid transparent;
            background: transparent !important;
            color: #34205f !important;
            font-weight: 800;
        }

        body[data-admin-theme="dark"] .coordinator-navbar {
            background: #171126 !important;
            border-bottom-color: #3a2d50 !important;
            color: #eee8ff !important;
        }

        body[data-admin-theme="dark"] .coordinator-navbar .brand-descriptor {
            color: #c6b7f5 !important;
        }

        body[data-admin-theme="dark"] .coordinator-navbar .student-nav-link,
        html[data-student-theme-enabled="true"][data-student-theme="dark"] .coordinator-navbar .student-nav-link:not(.active) {
            color: #e7ddfb !important;
        }

        .coordinator-navbar .student-nav-link:hover,
        .coordinator-navbar .student-nav-link:focus-visible,
        body[data-admin-theme="dark"] .coordinator-navbar .student-nav-link:hover,
        body[data-admin-theme="dark"] .coordinator-navbar .student-nav-link:focus-visible {
            background: rgba(250, 204, 21, .22) !important;
            color: #34205f !important;
        }

        .coordinator-navbar .student-nav-link.active,
        body[data-admin-theme="dark"] .coordinator-navbar .student-nav-link.active,
        html[data-student-theme-enabled="true"][data-student-theme="dark"] .coordinator-navbar .student-nav-link.active {
            background: #34205f !important;
            border-color: #34205f !important;
            color: #facc15 !important;
        }

        .coordinator-navbar .coordinator-logout-link {
            cursor: pointer;
            font: inherit;
        }

        .coordinator-navbar .profile-menu-btn,
        body[data-admin-theme="dark"] .coordinator-navbar .profile-menu-btn {
            border-color: rgba(52, 32, 95, .3);
            background: rgba(255, 255, 255, .45);
            color: #34205f;
        }

        body[data-admin-theme="dark"] .coordinator-navbar .profile-menu-btn {
            border-color: #49365f;
            background: #241b35;
            color: #eee8ff;
        }

        @media (min-width: 768px) and (max-width: 991.98px) {
            .coordinator-navbar {
                padding: .6rem .8rem !important;
                gap: .45rem !important;
            }

            .top-navbar.student-navbar.coordinator-navbar .student-top-nav {
                display: flex !important;
                flex: 1 1 auto;
                overflow-x: auto;
                scrollbar-width: none;
            }

            .top-navbar.student-navbar.coordinator-navbar .student-top-nav::-webkit-scrollbar {
                display: none;
            }

            .coordinator-navbar .student-nav-link {
                padding: .45rem .5rem;
            }

            .coordinator-navbar .student-nav-link span {
                display: none !important;
            }
        }

        @media (min-width: 992px) and (max-width: 1199.98px) {
            .coordinator-navbar {
                padding: .65rem 1rem !important;
                gap: .5rem !important;
            }

            .coordinator-navbar .student-nav-link {
                padding: .45rem .5rem;
                font-size: .76rem;
            }
        }

        @media (max-width: 767.98px) {
            .top-navbar.student-navbar.coordinator-navbar {
                flex-wrap: wrap !important;
                gap: .3rem !important;
                padding: .5rem .65rem !important;
            }

            .coordinator-navbar > .d-flex:first-child {
                width: 100% !important;
                flex: 0 0 100% !important;
                justify-content: center !important;
            }

            .coordinator-navbar > .d-flex:last-child {
                width: 100% !important;
                flex: 0 0 100% !important;
                justify-content: center !important;
                overflow: visible !important;
            }

            .coordinator-navbar .student-top-nav {
                display: flex !important;
                flex: 1 1 auto !important;
                width: auto !important;
                max-width: 100%;
                min-width: 0;
                justify-content: center;
                gap: .1rem;
                margin: 0 !important;
                overflow-x: auto;
                scrollbar-width: none;
            }

            .coordinator-navbar .student-top-nav::-webkit-scrollbar { display: none; }
            .coordinator-navbar .student-top-nav a[href*="/messaging"] { display: inline-flex !important; }
            .coordinator-navbar .student-nav-link { padding: .4rem .5rem !important; }
            .coordinator-navbar .student-nav-link span { display: none !important; }
            .coordinator-navbar > .d-flex:last-child > .text-end { flex: 0 0 auto; margin-left: .2rem; }
        }
    </style>
</head>
<body data-admin-theme="{{ Auth::check() && Auth::user()->role === 'admin' ? (Auth::user()->admin_theme ?? 'light') : 'light' }}">
    <div id="app" class="main-wrapper {{ request()->routeIs('splash') ? 'splash-wrapper' : '' }}">

        @auth
            @if(Auth::user()->role === 'admin' && !Auth::user()->isGadCoordinator())
            <aside class="sidebar admin-sidebar" id="mainSidebar" aria-label="Admin navigation">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <a class="navbar-brand-custom mb-0" href="{{ route('admin.dashboard') }}" title="SINAG admin dashboard">
                        <img src="{{ asset('images/gadlogo.png') }}" alt="GAD Logo" style="width:40px; height:40px; border-radius:50%; object-fit:cover;">
                        <span class="admin-brand-name ms-2" style="font-size:1.2rem;font-weight:900;letter-spacing:1px;line-height:1;color:#8f72f5;">SI<span style="color:#facc15;">NAG</span></span>
                    </a>
                    <button type="button" class="btn border-0 admin-sidebar-close" onclick="toggleSidebar()" aria-label="Close sidebar">
                        <i class="bi bi-x-lg" aria-hidden="true"></i>
                    </button>
                </div>

                <nav class="nav-menu mt-4 d-flex flex-column h-100" aria-label="Admin main menu">
                    <p class="admin-sidebar-heading text-uppercase small fw-bold text-muted mb-3" style="font-size:.7rem;letter-spacing:1px;">Main Menu</p>
                    <div class="admin-dashboard-nav-row">
                        <a href="{{ route('admin.dashboard') }}" title="Dashboard" class="nav-link-custom {{ Request::is('admin/dashboard*') ? 'active' : '' }}">
                            <i class="bi bi-bar-chart-fill"></i><span class="admin-nav-label">Dashboard</span>
                        </a>
                    </div>
                    <div class="admin-nav-group">
                        <a href="{{ route('admin.appointments') }}" title="Appointments" class="nav-link-custom {{ Request::is('admin/appointments*') || Request::is('admin/gad-schedules*') ? 'active' : '' }}">
                            <i class="bi bi-calendar-check-fill"></i><span class="admin-nav-label">Appointments @if(($adminNotifications['appointments'] ?? 0) > 0)<span class="admin-nav-count nav-notification ms-auto">{{ $adminNotifications['appointments'] }}</span>@endif</span>
                        </a>
                        <a href="{{ route('admin.gad-schedules.index') }}" title="Office schedule" class="admin-subnav {{ Request::is('admin/gad-schedules*') ? 'active' : '' }}">Office schedule</a>
                    </div>
                    <a href="{{ route('admin.reports') }}" title="Manage reports" class="nav-link-custom {{ Request::is('admin/reports*') ? 'active' : '' }}">
                        <i class="bi bi-inbox-fill"></i><span class="admin-nav-label">Manage Reports @if(($adminNotifications['reports'] ?? 0) > 0)<span class="admin-nav-count nav-notification ms-auto">{{ $adminNotifications['reports'] }}</span>@endif</span>
                    </a>
                    <div class="admin-nav-group">
                        <a href="{{ route('admin.users') }}" title="User management" class="nav-link-custom {{ Request::is('admin/users*') || Request::is('admin/password-reset-requests*') ? 'active' : '' }}">
                            <i class="bi bi-people-fill"></i><span class="admin-nav-label">User Management @if(($adminNotifications['users'] ?? 0) > 0)<span class="admin-nav-count nav-notification ms-auto">{{ $adminNotifications['users'] }}</span>@endif</span>
                        </a>
                        <a href="{{ route('admin.password-resets.index') }}" title="Password requests" class="admin-subnav {{ Request::is('admin/password-reset-requests*') ? 'active' : '' }}">Password requests @if(($adminNotifications['password_resets'] ?? 0) > 0)({{ $adminNotifications['password_resets'] }})@endif</a>
                    </div>
                    <a href="{{ route('admin.suggestions') }}" title="Participatory suggestions" class="nav-link-custom {{ Request::is('admin/suggestions*') ? 'active' : '' }}">
                        <i class="bi bi-rss-fill"></i><span class="admin-nav-label">Suggestions @if(($adminNotifications['suggestions'] ?? 0) > 0)<span class="admin-nav-count nav-notification ms-auto">{{ $adminNotifications['suggestions'] }}</span>@endif</span>
                    </a>
                    <div class="admin-nav-group">
                        <a href="{{ route('admin.messages') }}" title="Messages" class="nav-link-custom {{ Request::is('admin/messages*') || Request::is('admin/urgent-calls*') ? 'active' : '' }}">
                            <i class="bi bi-chat-left-dots-fill"></i><span class="admin-nav-label">Messages <span class="admin-nav-count nav-notification ms-auto" data-admin-message-badge @if(($adminNotifications['messages'] ?? 0) < 1) hidden @endif>{{ $adminNotifications['messages'] ?? 0 }}</span></span>
                        </a>
                        <a href="{{ route('admin.urgent.calls') }}" title="Urgent calls" class="admin-subnav {{ Request::is('admin/urgent-calls*') ? 'active' : '' }}">Urgent calls</a>
                    </div>
                    <div class="admin-sidebar-footer">
                        <div class="admin-nav-group">
                            <a href="{{ route('admin.settings') }}" title="Settings" class="nav-link-custom {{ Request::is('admin/settings*', 'admin/archive*', 'admin/activity-log*') ? 'active' : '' }}">
                                <i class="bi bi-gear-fill"></i><span class="admin-nav-label">Settings</span>
                            </a>
                            <a href="{{ route('admin.settings', ['section' => 'appearance']) }}" title="Appearance" class="admin-subnav {{ request()->query('section', 'appearance') === 'appearance' && Request::is('admin/settings*') ? 'active' : '' }}">Appearance</a>
                            <a href="{{ route('admin.settings', ['section' => 'archive']) }}" title="Archive" class="admin-subnav {{ request()->query('section') === 'archive' || Request::is('admin/archive*') ? 'active' : '' }}">Archive</a>
                            <a href="{{ route('admin.settings', ['section' => 'activity']) }}" title="Activity log" class="admin-subnav {{ request()->query('section') === 'activity' || Request::is('admin/activity-log*') ? 'active' : '' }}">Activity log</a>
                            <a href="{{ route('admin.settings', ['section' => 'feedback']) }}" title="Ratings and reviews" class="admin-subnav {{ request()->query('section') === 'feedback' ? 'active' : '' }}">Ratings &amp; reviews</a>
                        </div>
                        <button type="button" title="Log out" class="nav-link-custom text-danger" onclick="document.getElementById('logout-form').submit()">
                            <i class="bi bi-box-arrow-left"></i><span class="admin-nav-label">Log out</span>
                        </button>
                    </div>
                </nav>
            </aside>
            <button type="button" id="adminSidebarToggle" class="admin-sidebar-toggle-floating sidebar-menu-button" onclick="toggleSidebar()" aria-label="Expand admin sidebar" aria-controls="mainSidebar" aria-expanded="false" title="Expand sidebar">
                <span class="sidebar-menu-line sidebar-menu-line--top" aria-hidden="true"></span>
                <span class="sidebar-menu-line sidebar-menu-line--center" aria-hidden="true"></span>
                <span class="sidebar-menu-line sidebar-menu-line--bottom" aria-hidden="true"></span>
            </button>
            <button type="button" class="sidebar-backdrop d-none" id="adminSidebarBackdrop" aria-label="Close navigation menu"></button>
            @endif
        @endauth

        @auth
            @if(Auth::user()->role !== 'admin' || Auth::user()->isGadCoordinator())
                <aside class="sidebar student-sidebar" id="studentSidebar" aria-label="{{ Auth::user()->isGadCoordinator() ? 'Coordinator' : 'Student' }} navigation">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <a class="navbar-brand-custom mb-0" href="{{ Auth::user()->isGadCoordinator() ? route('admin.dashboard') : route('student.dashboard') }}" style="display:inline-flex; align-items:center; gap:.7rem; background:#f3efff; border-radius:1rem; padding:.3rem .75rem; text-decoration:none;">
                            <img src="{{ asset('images/gadlogo.png') }}" alt="GAD Logo" style="width:38px; height:38px; border-radius:50%; object-fit:cover;">
                            <span style="font-size:1.3rem; font-weight:900; letter-spacing:1px; line-height:1; color:#8f72f5;">SI<span style="color:#facc15;">NAG</span></span>
                        </a>
                        <button type="button" class="btn border-0" onclick="toggleSidebar('studentSidebar', 'studentSidebarBackdrop')" aria-label="Close navigation menu">
                            <i class="bi bi-x-lg" style="color:#8f72f5;"></i>
                        </button>
                    </div>

                    <nav class="nav-menu mt-4 d-flex flex-column" aria-label="{{ Auth::user()->isGadCoordinator() ? 'Coordinator' : 'Student' }} menu">
                        <p class="text-uppercase small fw-bold text-muted mb-3" style="font-size:.7rem; letter-spacing:1px;">Main Menu</p>
                        @if(Auth::user()->isGadCoordinator())
                        <a href="{{ route('admin.dashboard') }}" class="nav-link-custom {{ Request::is('admin/dashboard*') ? 'active' : '' }}">
                            <i class="bi bi-bar-chart-fill"></i> GAD Report Analytics
                        </a>
                        <a href="{{ route('admin.report-tracking') }}" class="nav-link-custom {{ Request::is('admin/report-tracking*') ? 'active' : '' }}">
                            <i class="bi bi-clipboard-data-fill"></i> Report Details
                        </a>
                        <a href="{{ route('student.boses') }}" class="nav-link-custom {{ Request::is('student/boses*') ? 'active' : '' }}">
                            <i class="bi bi-rss-fill"></i> Suggestions
                        </a>
                        <a href="{{ route('student.messaging') }}" class="nav-link-custom {{ Request::is('student/messaging*') ? 'active' : '' }}">
                            <i class="bi bi-chat-dots-fill"></i> Messages
                        </a>
                        <a href="{{ route('admin.settings', ['section' => 'feedback']) }}" class="nav-link-custom {{ request()->query('section') === 'feedback' ? 'active' : '' }}">
                            <i class="bi bi-star-half"></i> Ratings &amp; Reviews
                        </a>
                        @else
                        <a href="{{ route('student.dashboard') }}" class="nav-link-custom {{ Request::is('student/dashboard*') ? 'active' : '' }}">
                            <i class="bi bi-house-door-fill"></i> Dashboard
                        </a>
                        @if(Auth::user()->hasCompletedPersonalData())
                        <a href="{{ route('student.report') }}" class="nav-link-custom {{ Request::is('student/report*') ? 'active' : '' }}">
                            <i class="bi bi-flag-fill"></i> File a Report
                        </a>
                        @else
                        <a href="{{ route('personal-data.edit') }}" class="nav-link-custom text-muted" aria-disabled="true" title="Complete your personal information to unlock File a Report">
                            <i class="bi bi-lock-fill"></i> File a Report
                        </a>
                        @endif
                        <a href="{{ route('student.vault') }}" class="nav-link-custom {{ Request::is('student/vault*') ? 'active' : '' }}">
                            <i class="bi bi-clipboard-check-fill"></i> Report Status
                        </a>
                        @if(Auth::user()->hasCompletedPersonalData())
                        <a href="{{ route('student.boses') }}" class="nav-link-custom {{ Request::is('student/boses*') ? 'active' : '' }}">
                            <i class="bi bi-rss-fill"></i> Suggestions
                        </a>
                        @else
                        <a href="{{ route('personal-data.edit', ['return_to_suggestions' => 1]) }}" class="nav-link-custom text-muted" aria-disabled="true" title="Complete your personal information to unlock Suggestions">
                            <i class="bi bi-lock-fill"></i> Suggestions
                        </a>
                        @endif
                        <a href="{{ route('student.appointments') }}" class="nav-link-custom {{ Request::is('student/appointments*') || Request::is('student/schedule-appointment*') ? 'active' : '' }}">
                            <i class="bi bi-calendar2-week-fill"></i> Appointments
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
                        @endif
                    </nav>
                </aside>
                <button type="button" class="sidebar-backdrop student-sidebar-backdrop d-none" id="studentSidebarBackdrop" aria-label="Close navigation menu"></button>
            @endif
        @endauth

        <main class="{{ Auth::check() ? 'main-content ' . (Auth::user()->role === 'admin' && !Auth::user()->isGadCoordinator() ? 'admin-main' : '') . (!request()->routeIs('admin.dashboard', 'student.dashboard') ? ' page-load-motion' : '') : (request()->routeIs('login', 'register', 'password.*') ? 'auth-main' : 'container py-5') }}">
            @auth
                <header class="top-navbar {{ in_array(strtolower((string) Auth::user()->role), ['student', 'employee', 'security'], true) || Auth::user()->isGadCoordinator() ? 'student-navbar' : 'admin-navbar' }} {{ Auth::user()->role !== 'admin' ? 'student-floating-enabled' : '' }} {{ Auth::user()->isGadCoordinator() ? 'coordinator-navbar' : '' }}">
                    <div class="d-flex align-items-center gap-3">
                        @if(Auth::user()->role === 'admin' && !Auth::user()->isGadCoordinator())
                            <button type="button" class="sidebar-toggle admin-mobile-sidebar-toggle sidebar-menu-button" id="adminMobileSidebarToggle" onclick="toggleSidebar()" aria-label="Open admin menu" aria-controls="mainSidebar" aria-expanded="false">
                                <span class="sidebar-menu-line sidebar-menu-line--top" aria-hidden="true"></span>
                                <span class="sidebar-menu-line sidebar-menu-line--center" aria-hidden="true"></span>
                                <span class="sidebar-menu-line sidebar-menu-line--bottom" aria-hidden="true"></span>
                            </button>
                        @endif
                        @if(Auth::user()->role !== 'admin' || Auth::user()->isGadCoordinator())
                            <button type="button" class="student-sidebar-toggle sidebar-menu-button" onclick="toggleSidebar('studentSidebar', 'studentSidebarBackdrop')" aria-label="Open navigation menu" aria-controls="studentSidebar" aria-expanded="false">
                                <span class="sidebar-menu-line sidebar-menu-line--top" aria-hidden="true"></span>
                                <span class="sidebar-menu-line sidebar-menu-line--center" aria-hidden="true"></span>
                                <span class="sidebar-menu-line sidebar-menu-line--bottom" aria-hidden="true"></span>
                            </button>
                        @endif
                        <!-- Option 1: Modern & Spaced with Soft Shadows -->
<a class="navbar-brand-custom mb-0" style="display:inline-flex; align-items:center; gap:1.2rem; text-decoration:none;">
    <!-- Container para sa Logos: May Pink Glow/Shadow lang sa logos -->
    <div class="{{ request()->routeIs('student.dashboard') && session('dashboard_logo_sunburst') ? 'dashboard-logo-sunburst' : '' }}" style="display:flex; align-items:center; filter: drop-shadow(0px 0px 8px rgba(159, 83, 252, 0.945));">
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
        Gender and Development Safe Space and Participatory Suggestion System for Employees and Students of Pangasinan State University
    </span>
</a>


                    </div>

                    <div class="d-flex align-items-center gap-3">
                        @if(Auth::user()->isGadCoordinator())
                            <nav class="student-top-nav me-3" aria-label="Coordinator main navigation">
                                <a href="{{ route('admin.dashboard') }}" class="student-nav-link {{ Request::is('admin/dashboard*') ? 'active' : '' }}" aria-label="GAD Report Analytics" title="GAD Report Analytics">
                                    <i class="bi bi-bar-chart-fill"></i>
                                    <span>GAD Report Analytics</span>
                                </a>
                                <a href="{{ route('admin.report-tracking') }}" class="student-nav-link {{ Request::is('admin/report-tracking*') ? 'active' : '' }}" aria-label="Report Details" title="Report Details">
                                    <i class="bi bi-clipboard-data-fill"></i>
                                    <span>Report Details</span>
                                </a>
                                <a href="{{ route('student.boses') }}" class="student-nav-link {{ Request::is('student/boses*') ? 'active' : '' }}" aria-label="Suggestions" title="Suggestions">
                                    <i class="bi bi-rss-fill"></i>
                                    <span>Suggestions</span>
                                </a>
                                <a href="{{ route('student.messaging') }}" class="student-nav-link {{ Request::is('student/messaging*') ? 'active' : '' }}" aria-label="Messages" title="Messages">
                                    <i class="bi bi-chat-dots-fill"></i>
                                    <span>Messages</span>
                                </a>
                                <a href="{{ route('admin.settings') }}" class="student-nav-link {{ Request::is('admin/settings*') && request()->query('section', 'appearance') === 'appearance' ? 'active' : '' }}" aria-label="Settings" title="Settings">
                                    <i class="bi bi-gear-fill"></i>
                                    <span>Settings</span>
                                </a>
                                <button type="submit" form="logout-form" class="student-nav-link coordinator-logout-link" aria-label="Log out" title="Log out">
                                    <i class="bi bi-box-arrow-left"></i>
                                    <span>Log out</span>
                                </button>
                            </nav>
                        @elseif(Auth::user()->role !== 'admin')
                            <nav class="student-top-nav me-3" aria-label="Student main navigation">
                                <a href="{{ route('student.dashboard') }}" class="student-nav-link {{ Request::is('student/dashboard*') ? 'active' : '' }}" aria-label="Dashboard" title="Dashboard">
                                    <i class="bi bi-house-door-fill"></i>
                                    <span>Dashboard</span>
                                </a>
                                @if(Auth::user()->hasCompletedPersonalData())
                                <a href="{{ route('student.boses') }}" class="student-nav-link {{ Request::is('student/boses*') ? 'active' : '' }}" aria-label="Suggestions" title="Suggestions">
                                    <i class="bi bi-rss-fill"></i>
                                    <span>Suggestions</span>
                                </a>
                                @else
                                <a href="{{ route('personal-data.edit', ['return_to_suggestions' => 1]) }}" class="student-nav-link text-muted" aria-disabled="true" title="Complete your personal information to unlock Suggestions">
                                    <i class="bi bi-lock-fill"></i>
                                    <span>Suggestions</span>
                                </a>
                                @endif

                                <div class="dropdown d-inline-block">
                                    <button type="button" class="student-nav-link dropdown-toggle {{ Request::is('student/report*') || Request::is('student/vault*') ? 'active' : '' }}" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Report" title="Report">
                                        <i class="bi bi-flag-fill"></i>
                                        <span>Report</span>
                                    </button>
                                    <ul class="dropdown-menu student-nav-menu">
                                        <li>
                                            @if(Auth::user()->hasCompletedPersonalData())
                                            <a class="dropdown-item" href="{{ route('student.report') }}"><i class="bi bi-flag"></i><span>File a Report</span></a>
                                            @else
                                            <a class="dropdown-item text-muted" href="{{ route('personal-data.edit') }}" aria-disabled="true"><i class="bi bi-lock"></i><span>File a Report</span></a>
                                            @endif
                                        </li>
                                        <li><a class="dropdown-item" href="{{ route('student.vault') }}"><i class="bi bi-clipboard-check"></i><span>Report Status</span></a></li>
                                    </ul>
                                </div>

                                <a href="{{ route('student.appointments') }}" class="student-nav-link {{ Request::is('student/appointments*') || Request::is('student/schedule-appointment*') ? 'active' : '' }}" aria-label="Appointments" title="Appointments">
                                    <i class="bi bi-calendar2-week-fill"></i>
                                    <span>Appointments</span>
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
                        <div class="text-end">
                            <div class="dropdown user-meta">
                                <a class="profile-menu-btn" href="{{ route('profile.show') }}" title="View profile">
                                    <img src="{{ route('profile.photo', Auth::user()) }}" alt="Profile photo" class="profile-avatar" onerror="this.hidden=true;this.nextElementSibling.hidden=false">
                                    <span class="profile-avatar profile-avatar-fallback" hidden>{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                                    <span class="profile-mini-name">{{ Auth::user()->name }}</span>
                                </a>
                                <button class="profile-menu-btn ms-1" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Open account menu">
                                    <i class="bi bi-chevron-down"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end profile-dropdown p-2">
                                    <div class="profile-dropdown-head">
                                        <img src="{{ route('profile.photo', Auth::user()) }}" alt="Profile photo" class="profile-avatar" onerror="this.hidden=true;this.nextElementSibling.hidden=false">
                                        <span class="profile-avatar profile-avatar-fallback" hidden>{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                                        <div>
                                            <div class="fw-bold" style="line-height: 1.2;">{{ Auth::user()->name }}</div>
                                            <small class="text-muted">{{ Auth::user()->cloak_alias ?? 'Local profile' }}</small>
                                        </div>
                                    </div>
                                    <a href="{{ route('profile.show') }}" class="profile-dropdown-action text-decoration-none">
                                        <i class="bi bi-person-lines-fill"></i>
                                        View Profile
                                    </a>
                                    <a href="{{ Auth::user()->isGadCoordinator() ? route('admin.settings') : route('student.settings') }}" class="profile-dropdown-action text-decoration-none">
                                        <i class="bi bi-gear-fill"></i>
                                        Settings
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a href="{{ route('logout') }}" class="profile-dropdown-action text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="bi bi-power"></i>
                                        Log out
                                    </a>
                                </div>
                            </div>
                        </div>
                            @endif
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
                document.querySelectorAll(`[aria-controls="${sidebarId}"]`).forEach(function (toggle) {
                    toggle.setAttribute('aria-expanded', String(isOpen));
                    toggle.classList.toggle('active', isOpen);
                    if (toggle.id === 'adminSidebarToggle') {
                        toggle.setAttribute('aria-label', isOpen ? 'Collapse admin sidebar' : 'Expand admin sidebar');
                        toggle.setAttribute('title', isOpen ? 'Collapse sidebar' : 'Expand sidebar');
                    } else if (toggle.id === 'adminMobileSidebarToggle') {
                        toggle.setAttribute('aria-label', isOpen ? 'Close admin menu' : 'Open admin menu');
                    } else if (toggle.classList.contains('student-sidebar-toggle')) {
                        toggle.setAttribute('aria-label', isOpen ? 'Close navigation menu' : 'Open navigation menu');
                    }
                });
                if (sidebarId === 'mainSidebar') {
                    window.requestAnimationFrame(function () {
                        window.dispatchEvent(new Event('admin-sidebar-layout'));
                    });
                }
            }
        }

        document.getElementById('adminSidebarBackdrop')?.addEventListener('click', function () {
            toggleSidebar();
        });
        document.getElementById('studentSidebarBackdrop')?.addEventListener('click', function () {
            toggleSidebar('studentSidebar', 'studentSidebarBackdrop');
        });

        (function () {
            const sidebar = document.getElementById('mainSidebar');
            const toggle = document.getElementById('adminSidebarToggle');
            const header = document.querySelector('.admin-main > .top-navbar');

            function alignSidebarToggle() {
                if (!sidebar || !toggle) return;
                const headerHeight = header?.offsetHeight || 72;
                const railTop = Math.max(88, headerHeight + 16);
                sidebar.style.top = `${railTop}px`;
                sidebar.style.maxHeight = `calc(100dvh - ${railTop + 16}px)`;
                const sidebarPaddingTop = Number.parseFloat(getComputedStyle(sidebar).paddingTop) || 0;
                toggle.style.top = `${railTop + sidebarPaddingTop + 8}px`;
            }

            alignSidebarToggle();
            window.addEventListener('resize', alignSidebarToggle);
            window.addEventListener('admin-sidebar-layout', alignSidebarToggle);
        })();

        (function () {
            if (!document.querySelector('.admin-main')) return;
            if ('scrollRestoration' in history) history.scrollRestoration = 'manual';

            function resetAdminScroll() {
                const navbar = document.querySelector('.admin-main > .top-navbar');
                navbar?.classList.remove('is-hidden');
                window.scrollTo(0, 0);
                requestAnimationFrame(function () {
                    navbar?.classList.remove('is-hidden');
                    window.scrollTo(0, 0);
                });
            }

            document.addEventListener('DOMContentLoaded', resetAdminScroll, { once: true });
            window.addEventListener('load', resetAdminScroll, { once: true });
            window.addEventListener('pageshow', resetAdminScroll);
        })();

        function settlePageMotion() {
            document.querySelectorAll(
                '.page-load-motion > :not(.top-navbar):not(.student-navbar), .admin-dashboard-page, .admin-dashboard-wrap > *'
            ).forEach(function (element) {
                element.style.animation = 'none';
                element.style.opacity = '1';
            });
        }

        if (document.querySelector('.admin-main')) {
            document.addEventListener('DOMContentLoaded', settlePageMotion, { once: true });
            window.addEventListener('pageshow', function (event) {
                if (!event.persisted) settlePageMotion();
            });
        }

        document.addEventListener('animationend', function (event) {
            if (event.animationName === 'pageContentReveal' || event.animationName === 'adminDashboardReveal') {
                event.target.style.animation = 'none';
            }
        });

        document.addEventListener('show.bs.modal', settlePageMotion);

        (function () {
            const navbar = document.querySelector('.top-navbar');
            const desktop = window.matchMedia('(min-width: 1200px)');
            let previousScrollY = window.scrollY;
            let ticking = false;

            function updateNavbarVisibility() {
                if (!navbar) return;
                if (!desktop.matches) {
                    navbar.classList.remove('scroll-direction-nav', 'is-hidden');
                    previousScrollY = window.scrollY;
                    return;
                }

                navbar.classList.add('scroll-direction-nav');
                const currentScrollY = window.scrollY;
                if (currentScrollY < 48) navbar.classList.remove('is-hidden');
                else if (currentScrollY < previousScrollY - 6) navbar.classList.remove('is-hidden');
                else if (currentScrollY > previousScrollY + 6) navbar.classList.add('is-hidden');
                previousScrollY = currentScrollY;
            }

            window.addEventListener('scroll', function () {
                if (ticking) return;
                ticking = true;
                window.requestAnimationFrame(function () {
                    updateNavbarVisibility();
                    ticking = false;
                });
            }, { passive: true });
            window.addEventListener('resize', updateNavbarVisibility);
            updateNavbarVisibility();
        })();

        @auth
        @if(auth()->user()->role === 'admin' && !auth()->user()->isGadCoordinator())
        (function () {
            const notice = document.createElement('div');
            notice.setAttribute('role', 'status');
            notice.setAttribute('aria-live', 'polite');
            notice.style.cssText = 'position:fixed;top:max(1rem,env(safe-area-inset-top));right:max(1rem,env(safe-area-inset-right));z-index:1105;width:min(24rem,calc(100vw - 2rem));box-sizing:border-box;background:#35216d;color:#fff;padding:.8rem 1rem;border-radius:.75rem;box-shadow:0 8px 28px #0003;display:none;align-items:flex-start;gap:.75rem';
            const noticeContent = document.createElement('div');
            noticeContent.style.flex = '1';
            const dismissNotice = document.createElement('button');
            dismissNotice.type = 'button';
            dismissNotice.className = 'btn-close btn-close-white flex-shrink-0';
            dismissNotice.setAttribute('aria-label', 'Dismiss notification');
            dismissNotice.addEventListener('click', function () { notice.style.display = 'none'; });
            notice.append(noticeContent, dismissNotice);
            document.body.appendChild(notice);
            let unreadCount = {{ (int) ($adminNotifications['messages'] ?? 0) }};
            let currentCallIds = null;
            function notifyAdmin(message, link) {
                noticeContent.replaceChildren();
                const text = document.createElement('span');
                text.textContent = message;
                noticeContent.appendChild(text);
                if (link) {
                    const anchor = document.createElement('a');
                    anchor.href = link;
                    anchor.className = 'text-white fw-bold ms-2';
                    anchor.textContent = 'Open';
                    noticeContent.appendChild(anchor);
                }
                notice.style.display = 'flex';
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
                    const calls = (data.calls || []).filter(function (call) {
                        return call.status !== 'ended' && call.initiated_by !== 'admin';
                    });
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

        @if(in_array(strtolower((string) auth()->user()->role), ['student', 'employee', 'security'], true) || auth()->user()->isGadCoordinator())
        (function () {
            const notice = document.createElement('div');
            notice.setAttribute('role', 'status');
            notice.setAttribute('aria-live', 'polite');
            notice.style.cssText = 'position:fixed;top:max(1rem,env(safe-area-inset-top));right:max(1rem,env(safe-area-inset-right));z-index:1105;width:min(24rem,calc(100vw - 2rem));box-sizing:border-box;background:#35216d;color:#fff;padding:.8rem 1rem;border-radius:.75rem;box-shadow:0 8px 28px #0003;display:none;align-items:flex-start;gap:.75rem';
            const noticeContent = document.createElement('div');
            noticeContent.style.flex = '1';
            const dismissNotice = document.createElement('button');
            dismissNotice.type = 'button';
            dismissNotice.className = 'btn-close btn-close-white flex-shrink-0';
            dismissNotice.setAttribute('aria-label', 'Dismiss notification');
            dismissNotice.addEventListener('click', function () { notice.style.display = 'none'; });
            notice.append(noticeContent, dismissNotice);
            document.body.appendChild(notice);

            let lastMessageId = Number(localStorage.getItem('sinag.last-notified-message-id') || 0);
            let lastCallState = null;
            try {
                lastCallState = JSON.parse(localStorage.getItem('sinag.urgent-call-state') || 'null');
            } catch (error) {}

            function notifyUser(message, href) {
                noticeContent.replaceChildren();
                noticeContent.appendChild(document.createTextNode(message));
                const link = document.createElement('a');
                link.href = href;
                link.className = 'text-white fw-bold ms-2';
                link.textContent = 'Open';
                noticeContent.appendChild(link);
                notice.style.display = 'flex';
            }

            async function checkUserNotifications() {
                try {
                    const response = await fetch('{{ route('student.notifications.feed') }}', { headers: { 'Accept': 'application/json' }, cache: 'no-store' });
                    if (!response.ok) throw new Error('User notification check failed (' + response.status + ').');
                    const data = await response.json();
                    if (data.app_notification?.message) {
                        notifyUser(data.app_notification.message, data.app_notification.url || '{{ route('home') }}');
                    } else {
                        const messageId = Number(data.latest_unread_id || 0);
                        if (messageId > lastMessageId) {
                            notifyUser('New message from the GAD Office.', '{{ route('student.messaging') }}');
                            lastMessageId = messageId;
                            localStorage.setItem('sinag.last-notified-message-id', String(lastMessageId));
                        }

                        const call = data.latest_call;
                        const adminCallIsWaiting = call?.initiated_by === 'admin'
                            && !call.accepted && call.status !== 'ended';
                        if (adminCallIsWaiting
                            && (!lastCallState || lastCallState.id !== call.id || lastCallState.status === 'ended')) {
                            notifyUser('The GAD Office is calling.', '{{ route('student.urgent') }}?call_id=' + encodeURIComponent(call.id));
                        }
                        if (call && call.id && call.status === 'joined'
                            && call.initiated_by !== 'admin'
                            && lastCallState && lastCallState.id === call.id && lastCallState.status !== 'joined') {
                            notifyUser('The GAD Office joined your urgent call.', '{{ route('student.urgent') }}');
                        }
                        if (call?.id) {
                            lastCallState = { id: call.id, status: call.status };
                            localStorage.setItem('sinag.urgent-call-state', JSON.stringify(lastCallState));
                        }
                    }
                } catch (error) {
                    console.error('Unable to check user notifications:', error);
                }
            }

            checkUserNotifications();
            setInterval(checkUserNotifications, 3000);
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
