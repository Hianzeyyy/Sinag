@extends('layouts.app')

@section('content')
<style>
    nav, .navbar { display: none !important; }
    body { overflow-x: clip; }

    .login-page {
        position: relative;
        min-height: 100dvh;
        align-items: center !important;
        width: 100vw;
        margin-left: calc(50% - 50vw);
        box-sizing: border-box;
        overflow-x: clip;
        overflow-y: visible;
        padding: 1rem 1rem 2rem;
        background:
            radial-gradient(ellipse at 50% 4%, rgba(255,255,255,.95), transparent 55%),
            linear-gradient(180deg, #f6f4ff 0%, #eeeaff 58%, #dcd5ff 100%);
    }
    .login-page::before {
        content: '';
        position: absolute;
        z-index: 0;
        left: -10%;
        right: -10%;
        bottom: 0;
        height: 10rem;
        pointer-events: none;
        opacity: .96;
        background:
            radial-gradient(ellipse 25% 60% at 10% 100%, #fff 0 55%, transparent 57%),
            radial-gradient(ellipse 30% 76% at 31% 100%, #fff 0 55%, transparent 57%),
            radial-gradient(ellipse 31% 66% at 56% 100%, #fff 0 56%, transparent 58%),
            radial-gradient(ellipse 26% 78% at 78% 100%, #fff 0 56%, transparent 58%),
            radial-gradient(ellipse 24% 61% at 96% 100%, #fff 0 56%, transparent 58%),
            linear-gradient(to bottom, transparent 0 40%, #fff 72%);
        filter: drop-shadow(0 -10px 18px rgba(255,255,255,.58));
        animation: cloudDrift 42s ease-in-out infinite alternate;
    }

    .login-page::after {
        content: '';
        position: absolute;
        z-index: 0;
        left: -15%;
        right: -15%;
        bottom: 0;
        height: 8rem;
        pointer-events: none;
        opacity: .72;
        background:
            radial-gradient(ellipse 27% 66% at 7% 100%, #fff 0 55%, transparent 57%),
            radial-gradient(ellipse 34% 82% at 35% 100%, #fff 0 55%, transparent 57%),
            radial-gradient(ellipse 30% 75% at 66% 100%, #fff 0 55%, transparent 57%),
            radial-gradient(ellipse 29% 74% at 93% 100%, #fff 0 55%, transparent 57%),
            linear-gradient(to bottom, transparent 0 45%, #fff 76%);
        filter: blur(2px);
        animation: cloudDrift 58s ease-in-out infinite alternate-reverse;
    }

    .login-shell {
        position: relative;
        z-index: 2;
        width: min(100%, 1040px);
        margin: 0 auto;
        animation: fadeInUp 0.8s ease-out;
    }

    .login-card {
        background: #fff;
        border: 1px solid rgba(111, 91, 190, 0.14) !important;
        box-shadow: 0 24px 80px rgba(75, 58, 139, .18) !important;
    }

    .login-logo { width: min(140px, 38vw); }
    .login-logo-pair { display: flex; align-items: center; justify-content: center; gap: 0.35rem; }
    .login-logo-pair img { width: min(96px, 20vw); aspect-ratio: 1; object-fit: contain; }
    .login-logo-pair .login-gad-logo { border-radius: 50%; clip-path: circle(50%); mix-blend-mode: multiply; }
    .login-brand-name { font-size: clamp(2.5rem, 5vw, 3.5rem); }
    .login-form-panel { min-width: 0; width: 100%; padding: 1.5rem !important; }
    .password-lockout-modal {
        --bs-modal-bg: transparent;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        background: rgba(224, 219, 249, .9);
    }
    .password-lockout-modal .modal-dialog {
        max-width: 25rem;
        width: min(100%, 25rem);
        margin: 0 auto;
    }
    .password-lockout-modal .modal-content {
        background: #f5f2ff;
        border-radius: 1.25rem !important;
        box-shadow: 0 1.5rem 4rem rgba(74, 61, 143, .24) !important;
    }
    .password-lockout-modal .modal-body {
        position: relative;
        padding: 2.8rem 2rem 2.25rem !important;
    }
    .password-lockout-modal .lockout-close {
        position: absolute;
        top: 1.15rem;
        right: 1.15rem;
        width: 2.7rem;
        height: 2.7rem;
        border: 1px solid #e3e8fb;
        border-radius: 50%;
        color: #242424;
        background: #fff;
        font-size: 1.8rem;
        font-weight: 300;
        line-height: 1;
    }
    .password-lockout-modal .lockout-icon {
        width: 5.9rem;
        height: 5.9rem;
        margin: 0 auto 1.65rem;
        border-radius: 50%;
        color: #7259c9;
        background: #e8e1ff;
        font-size: 2.4rem;
    }
    .password-lockout-modal .lockout-copy {
        max-width: 18rem;
        margin-inline: auto;
        color: #262626;
        font-size: .96rem;
        line-height: 1.55;
    }
    .password-lockout-modal .lockout-actions {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        margin-top: 1.8rem;
    }
    .password-lockout-modal .lockout-action {
        min-height: 3.2rem;
        border: 1.5px solid #9583e6;
        border-radius: 999px;
        color: #6851c4;
        background: #faf9ff;
        font-size: .84rem;
        font-weight: 800;
        text-decoration: none;
    }
    .password-lockout-modal .lockout-action:hover,
    .password-lockout-modal .lockout-action:focus-visible {
        color: #5139ad;
        background: #eeeaff;
        border-color: #7259c9;
    }
    .login-input-group {
        position: relative;
        border-radius: 12px;
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .login-input-group:focus-within {
        transform: translateY(-2px);
        box-shadow: 0 10px 24px rgba(53, 38, 126, 0.24), 0 0 0 3px rgba(250, 204, 21, 0.22) !important;
    }

    .login-input-group:focus-within .login-input-icon {
        background: #3038a8 !important;
        color: #fff !important;
    }
    .login-input-icon { padding-inline: clamp(0.85rem, 3vw, 1.5rem); }
    .login-input { min-width: 0; font-size: 1rem !important; }
    .login-password-hidden { -webkit-text-security: disc; }
    .login-password-eye {
        width: clamp(3.25rem, 12vw, 4.25rem);
        min-width: clamp(3.25rem, 12vw, 4.25rem);
        justify-content: center;
        border: 0 !important;
        border-radius: 0 12px 12px 0 !important;
        color: #6b7280 !important;
        background: #fff !important;
        cursor: pointer;
        transition: color .18s ease, background .18s ease;
    }
    .login-password-eye:hover,
    .login-password-eye:focus-visible {
        color: #5b43b2 !important;
        background: #f8f7ff !important;
    }
    .login-password-eye:focus-visible {
        outline: 3px solid rgba(143, 114, 245, .28);
        outline-offset: -3px;
    }
    .login-password-input {
        border-radius: 0 !important;
        padding-right: .75rem !important;
    }
    .login-password-toggle {
        display: inline-flex;
    }
    .login-actions { min-width: 0; }
    .login-actions > * { min-height: 52px; min-width: 0; }

    /* From Uiverse.io by AqFox - https://uiverse.io/AqFox/pretty-lionfish-11
       MIT License, Copyright (c) AqFox (Aquilix) */
    .login-submit {
        justify-self: center;
        width: 130px;
        height: 40px;
        min-height: 40px;
        padding: 10px 25px !important;
        border: 0 !important;
        border-radius: 5px !important;
        color: #fff !important;
        background: linear-gradient(0deg, #7658df 0%, #7658df 100%) !important;
        box-shadow: inset 2px 2px 2px 0 rgba(255,255,255,.5),
            7px 7px 20px 0 rgba(0,0,0,.1), 4px 4px 5px 0 rgba(0,0,0,.1) !important;
        font-size: 15px !important;
        font-weight: 500 !important;
        letter-spacing: normal !important;
        transition: all .3s ease;
        touch-action: manipulation;
        -webkit-tap-highlight-color: transparent;
    }

    .login-submit:hover,
    .login-submit:focus-visible {
        box-shadow: 4px 4px 6px 0 rgba(255,255,255,.5),
            -4px -4px 6px 0 rgba(116,125,136,.5),
            inset -4px -4px 6px 0 rgba(255,255,255,.2),
            inset 4px 4px 6px 0 rgba(0,0,0,.4) !important;
    }

    .login-submit:active,
    .login-submit.is-pressed {
        transform: translateY(2px);
    }

    .login-submit:focus-visible { outline: 3px solid #c4b5fd; outline-offset: 4px; }

    .login-alert { background: #f3efff; color: #5b4b9b; border-left: 4px solid #8f72f5; }

    .login-forgot {
        color: #5b43b2;
        transition: color 0.2s ease, text-shadow 0.2s ease;
    }

    .login-forgot:hover,
    .login-forgot:focus-visible {
        color: #35216d;
    }

    .reset-modal-card { background: linear-gradient(145deg, #f8f7ff, #ede9fe); border: 1px solid rgba(143, 114, 245, 0.25); }

    .login-form-panel { display: flex; flex-direction: column; }
    .auth-stage {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
        align-items: stretch;
        position: relative;
        min-height: 0;
        flex: 0 0 auto;
        overflow: visible;
        border-radius: 1rem;
        background: #fff;
        box-shadow: 0 10px 36px rgba(64, 48, 119, .12);
    }
    .auth-pane {
        position: relative;
        width: 100%;
        height: auto;
        min-height: 0;
        padding: clamp(1.5rem, 3vw, 2.75rem);
        color: #25213e;
        background: #fff;
        transition: transform .72s cubic-bezier(.2,.75,.25,1), opacity .38s ease, visibility .38s ease, filter .38s ease;
        overflow: visible;
        will-change: transform, opacity;
    }
    .auth-pane .text-white { color: #302b4c !important; }
    .auth-pane .text-white-50 { color: #6f6a83 !important; }
    .auth-pane .form-control,
    .auth-pane .form-select {
        color: #25213e;
        background-color: #f5f4fa;
        border: 1px solid #e2dfed;
        border-radius: .65rem;
    }
    .auth-pane .form-control:focus,
    .auth-pane .form-select:focus {
        border-color: #8f72f5;
        box-shadow: 0 0 0 .2rem rgba(143, 114, 245, .16);
    }
    .sign-in-pane { grid-column: 1; grid-row: 1; z-index: 2; transform: translateX(0) scale(1); }
    .sign-up-pane { grid-column: 2; grid-row: 1; display: none; z-index: 1; transform: translateX(3%) scale(.985); opacity: 0; filter: blur(5px); }
    .auth-stage:not(.right-panel-active) .sign-in-pane { animation: authSignInEnter .72s cubic-bezier(.2,.75,.25,1) both; }
    @keyframes authSignInEnter {
        0% { opacity: 0; transform: translateX(-7%) scale(.97); filter: blur(7px); }
        55% { opacity: 1; transform: translateX(1%) scale(1.012); filter: blur(0); }
        100% { opacity: 1; transform: translateX(0) scale(1); filter: blur(0); }
    }
    .auth-stage.right-panel-active .sign-in-pane { display: none; z-index: 1; transform: translateX(-8%) scale(.94); opacity: 0; visibility: hidden; filter: blur(5px); pointer-events: none; }
    .auth-stage.right-panel-active .sign-up-pane { display: block; z-index: 3; transform: translateX(0) scale(1); opacity: 1; filter: blur(0); animation: authSignUpEnter .72s cubic-bezier(.2,.75,.25,1) both; }
    @keyframes authSignUpEnter {
        0% { opacity: 0; transform: translateX(7%) scale(.97); filter: blur(7px); }
        55% { opacity: 1; transform: translateX(-1%) scale(1.012); filter: blur(0); }
        100% { opacity: 1; transform: translateX(0) scale(1); filter: blur(0); }
    }
    .auth-overlay {
        position: absolute;
        inset: 0 0 0 auto;
        z-index: 5;
        width: 50%;
        padding: 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: #302653;
        background: linear-gradient(145deg, #f1edff, #e6e0ff 58%, #d6cdff);
        transition: transform .72s cubic-bezier(.2,.75,.25,1), border-radius .72s ease, box-shadow .72s ease;
        box-shadow: -14px 0 35px rgba(75,58,139,.06);
    }
    .auth-overlay-copy { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.5rem; transition: opacity .25s ease, transform .6s ease; }
    .auth-overlay-left { opacity: 0; visibility: hidden; transform: translateX(-15%); }
    .auth-stage.right-panel-active .auth-overlay { transform: translateX(-100%); }
    .auth-stage.right-panel-active .auth-overlay-right { opacity: 0; visibility: hidden; transform: translateX(15%); }
    .auth-stage.right-panel-active .auth-overlay-left { opacity: 1; visibility: visible; transform: translateX(0); }
    .auth-overlay-copy p { max-width: 24rem; color: #625b7f; }
    .auth-brand-logo { display: flex; align-items: center; justify-content: center; gap: .75rem; margin-bottom: 1rem; }
    .auth-brand-logo img { width: clamp(3rem, 7vw, 5rem); height: clamp(3rem, 7vw, 5rem); object-fit: contain; }
    .auth-brand-logo img:nth-child(2) { border-radius: 50%; clip-path: circle(49%); mix-blend-mode: multiply; }
    .auth-brand-title { margin: 0 0 .5rem; color: #5b43b2; font-weight: 900; letter-spacing: .24em; font-size: clamp(2rem, 4vw, 3rem); }
    .auth-mobile-brand, .mobile-signup-prompt, .auth-mobile-signin-dock { display: none; }
    .auth-overlay-account { margin: .35rem 0 .7rem; color: #625b7f; font-weight: 600; }
    .auth-pane .form-control, .auth-pane .form-select { min-height: 44px; font-size: .9rem; }
    .auth-pane .form-label { color: #4d4862 !important; letter-spacing: .04em; }
    .auth-pane .login-input-icon { background: #eeeaf9 !important; color: #6a55bd !important; }
    .auth-pane .login-input { background: #f5f4fa !important; color: #25213e !important; }
    .auth-pane .form-check-label { color: #5d5870 !important; }
    .auth-pane .login-forgot { color: #5b43b2; }
    .auth-pane .login-actions { justify-content: center; }
    .auth-switch-signup {
        min-height: 44px;
        border: 0;
        border-radius: .65rem;
        color: #5b43b2;
        background: #f3f0ff;
        font-size: .92rem;
        transition: background .2s ease, color .2s ease;
    }
    .auth-switch-signup:hover,
    .auth-switch-signup:focus-visible { color: #fff; background: #6b55c4; }
    .google-auth-divider {
        display: flex;
        align-items: center;
        gap: .75rem;
        margin: .85rem 0;
        color: #817b96;
        font-size: .82rem;
    }
    .google-auth-divider::before,
    .google-auth-divider::after { content: ''; height: 1px; flex: 1; background: #e7e3f0; }
    .google-auth-button {
        display: flex;
        min-height: 46px;
        align-items: center;
        justify-content: center;
        gap: .65rem;
        border: 1px solid #dedbe7;
        border-radius: .7rem;
        color: #302b4c;
        background: #fff;
        font-weight: 700;
        transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease;
    }
    .google-auth-button:hover,
    .google-auth-button:focus-visible {
        border-color: #9a87dc;
        color: #302b4c;
        box-shadow: 0 .35rem 1rem rgba(75, 58, 139, .12);
        transform: translateY(-1px);
    }
    .auth-toggle {
        border: 1px solid #735bd0;
        border-radius: 999px;
        padding: .65rem 1.4rem;
        color: #5b43b2;
        background: #fff;
        font-weight: 700;
        letter-spacing: .08em;
        transition: color .2s ease, background .2s ease, transform .2s ease;
    }
    .auth-toggle:hover, .auth-toggle:focus-visible { background: #5b43b2; color: #fff; transform: translateY(-2px); }
    .register-fields { display: grid; grid-template-columns: repeat(6, minmax(0, 1fr)); }
    .register-name-row {
        grid-column: 1 / -1 !important;
        display: grid;
        grid-template-columns: 1fr;
        gap: .72rem;
    }
    .register-name-row > div {
        min-width: 0;
    }
    .register-name-row .form-label {
        min-height: 0;
        height: auto;
        margin-bottom: .38rem;
    }
    .register-name-row .form-control {
        min-height: 46px;
        width: 100%;
    }
    .register-name-field .form-label {
        display: block;
        height: 3rem;
        line-height: 1.25;
    }
    .register-submit {
        display: inline-flex;
        width: auto !important;
        max-width: 100%;
        min-height: 0;
        align-items: center;
        justify-content: center;
        padding: .65rem 1.35rem !important;
        font-size: .9rem;
        line-height: 1.25;
        white-space: nowrap;
    }
    .register-fields > .col-12.register-submit-wrap {
        grid-column: 1 / -1;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: .55rem;
    }
    .register-links { display: flex; justify-content: center; align-items: center; flex-wrap: wrap; gap: .35rem; text-align: center; }
    .register-submit {
        width: min(100%, 15rem) !important;
        min-height: 3.2rem;
        padding: .8rem 1.25rem !important;
        font-size: 1rem;
        position: relative;
        overflow: hidden;
        border: 0;
        border-radius: .75rem;
        color: #fff;
        color: #4a3500 !important;
        background: linear-gradient(135deg, #fde047, #facc15 65%, #f59e0b);
        box-shadow: 0 6px 14px rgba(217, 160, 0, .22);
        transition: padding .25s ease, transform .2s ease, box-shadow .2s ease, background .2s ease;
    }
    .register-submit:hover,
    .register-submit:focus-visible { padding-right: 4.25rem !important; transform: translateY(-2px); background: linear-gradient(135deg, #facc15, #f59e0b); box-shadow: 0 10px 20px rgba(217, 160, 0, .3); }
    .register-submit:active { transform: translateY(0); box-shadow: 0 3px 8px rgba(107, 85, 196, .2); }
    .register-submit-label { position: relative; z-index: 1; }
    .register-submit-icon { position: absolute; inset: 0 0 0 auto; display: flex; width: 3.2rem; align-items: center; justify-content: center; border-left: 1px solid rgba(74,53,0,.25); color: #4a3500; transform: translateX(101%); transition: transform .25s ease; }
    .register-submit:hover .register-submit-icon,
    .register-submit:focus-visible .register-submit-icon,
    .register-submit.loading .register-submit-icon { transform: translateX(0); }
    .register-submit.loading { padding-right: 4.25rem !important; }
    .register-submit-arrow,
    .register-submit-check { position: absolute; font-size: 1.1rem; transition: transform .2s ease, opacity .2s ease; }
    .register-submit-check { opacity: 0; transform: scale(.4); }
    .register-submit-loader { width: 1.15rem; height: 1.15rem; border: 2px solid rgba(255,255,255,.45); border-top-color: #fde047; border-radius: 50%; opacity: 0; animation: registerSpin .7s linear infinite; }
    .register-submit.loading .register-submit-arrow { opacity: 0; transform: scale(.5); }
    .register-submit.loading .register-submit-loader { opacity: 1; }
    @keyframes registerSpin { to { transform: rotate(360deg); } }
    .register-fields > .col-12 { width: auto; max-width: none; flex: none; }
    .register-fields > .col-6 { width: auto; max-width: none; flex: none; grid-column: span 3; }
    .register-fields > .col-12:not(.register-name-field) { grid-column: span 3; }
    .register-fields > .register-name-field { grid-column: span 2; }
    .register-fields > .col-12.d-none { display: none !important; }
    .register-fields > .col-12.register-submit-wrap { grid-column: 1 / -1; }
    .auth-overlay-copy p { font-size: .9rem; line-height: 1.5; }
    .register-password { padding-right: 2.75rem; }
    .register-password-row {
        grid-column: 1 / -1 !important;
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .5rem;
    }
    .register-detail-row {
        grid-column: 1 / -1 !important;
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .5rem;
        align-items: end;
    }
    .register-account-row {
        grid-column: 1 / -1 !important;
        display: grid;
        grid-template-columns: minmax(0, 1.35fr) minmax(7.5rem, .65fr) minmax(8.5rem, .9fr);
        gap: .5rem;
        align-items: end;
    }
    .register-account-row > div { min-width: 0; }
    .register-detail-row > div {
        min-width: 0;
    }
    .register-upload-field {
        display: flex;
        min-height: 8.75rem;
        flex-direction: column;
        align-self: stretch;
    }
    .register-upload-row {
        grid-column: 1 / -1 !important;
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .5rem;
    }
    .register-upload-row .register-upload-field {
        min-width: 0;
        height: 100%;
    }
    .register-upload-field .form-label {
        min-height: 2.8rem;
        margin-bottom: .25rem;
        line-height: 1.25;
    }
    .register-upload-field .form-control {
        min-height: 44px;
    }
    .register-upload-field .form-text {
        margin-top: .35rem;
    }
    .register-password-toggle {
        position: absolute;
        top: 50%;
        right: .45rem;
        z-index: 3;
        padding: .35rem;
        border: 0;
        color: #6b55c4;
        background: transparent;
        transform: translateY(-50%);
    }
    .register-password::-ms-reveal { display: none; }

    @media (max-width: 1199.98px) {
        .login-page { align-items: flex-start !important; padding-block: 1rem 2rem; }
        .login-form-panel { padding: .75rem !important; }
        .auth-mobile-brand { display: block; padding: .75rem 1rem 0; text-align: center; }
        .auth-mobile-brand .auth-brand-logo { margin-bottom: .25rem; }
        .auth-mobile-brand .auth-brand-logo img { width: 3rem; height: 3rem; }
        .auth-mobile-brand .auth-brand-title { margin-bottom: .65rem; font-size: 2rem; }
        .mobile-signup-prompt { display: flex; align-items: center; justify-content: center; gap: .5rem; margin: 1rem 0 0; color: #625b7f; font-size: .85rem; }
        .mobile-signup-prompt button { padding: .4rem .9rem; font-size: .78rem; }
        .login-form-panel.signup-active .mobile-signup-prompt { display: none; }
        .auth-stage { display: block; min-height: 0; overflow: visible; background: transparent; box-shadow: none; }
        .auth-pane {
            position: relative;
            inset: auto;
            width: 100%;
            height: auto;
            max-height: none;
            padding: 1.5rem;
            overflow: visible;
            transition: opacity .32s ease, transform .38s cubic-bezier(.2,.75,.25,1);
        }
        .auth-stage:not(.right-panel-active) .sign-in-pane { animation: authMobileSignInEnter .38s ease both; }
        .auth-stage:not(.right-panel-active) .sign-up-pane { transform: translateY(.7rem) scale(.99); }
        .auth-stage.right-panel-active .sign-up-pane {
            transform: translateY(0) scale(1);
            animation: authMobileSignUpEnter .42s cubic-bezier(.2,.75,.25,1) both;
        }
        @keyframes authMobileSignInEnter {
            from { opacity: 0; transform: translateY(-.45rem) scale(.99); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        @keyframes authMobileSignUpEnter {
            from { opacity: 0; transform: translateY(.7rem) scale(.99); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .auth-pane .login-actions { flex-direction: column; }
        .register-fields { grid-template-columns: repeat(6, minmax(0, 1fr)); }
        .register-fields > .col-12:not(.register-name-field) { grid-column: span 3; }
    }

    @media (min-width: 1200px) {
        .login-page { padding-block: 1.5rem; }
        .login-page.signup-active { align-items: flex-start !important; padding-block: 1rem 2rem; }
        .auth-stage.right-panel-active .sign-up-pane {
            grid-column: 2;
            grid-row: 1;
        }
    }

    .login-page.auth-card-centered { align-items: center !important; }

    @media (max-width: 575.98px) {
        .register-fields > .col-6,
        .register-fields > .col-12:not(.register-name-field),
        .register-fields > .register-name-field { grid-column: span 6; }
        .register-password-row { grid-template-columns: 1fr; }
        .register-upload-row { grid-template-columns: 1fr; }
        .register-detail-row { grid-template-columns: 1fr; }
        .register-account-row { grid-template-columns: 1fr 1fr; }
        .register-account-row > div:last-child { grid-column: 1 / -1; }
        .register-upload-field { min-height: auto; }
        .register-upload-field .form-label { min-height: 0; }
    }

    @media (max-width: 1199.98px) {
        .auth-overlay { display: none; }
        .sign-in-pane { display: block; }
        .sign-up-pane { display: none; }
        .auth-stage.right-panel-active .sign-in-pane { display: none; }
        .auth-stage.right-panel-active .sign-up-pane { display: block; }
        .auth-pane [data-auth-toggle="signup"],
        .auth-pane [data-auth-toggle="signin"] { min-height: 44px; }
        .auth-mobile-signin-dock {
            position: static;
            width: 100%;
            display: none;
            align-items: center;
            justify-content: center;
            gap: .35rem;
            margin-top: 1rem;
            padding: .65rem .75rem;
            border: 1px solid rgba(111, 91, 190, .15);
            border-radius: .75rem;
            color: #514675;
            background: rgba(255, 255, 255, .96);
        }
        .login-page.signup-active .auth-mobile-signin-dock { display: flex; }
        .auth-mobile-signin-dock button { padding: 0; border: 0; color: #5b43b2; background: transparent; font-weight: 800; }
    }

    @media (max-width: 420px) {
        .login-page { padding-inline: 0.65rem; }
        .login-card { border-radius: 1rem !important; }
        .login-form-panel { padding: 1rem !important; }
        .auth-stage { min-height: 0; }
        .auth-pane { padding: 1rem; }
        .login-page::before, .login-page::after { height: 12rem; }
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes cloudDrift {
        from { transform: translate3d(-2%, 0, 0); }
        to { transform: translate3d(3%, 0, 0); }
    }

    @media (prefers-reduced-motion: reduce) {
        .login-shell,
        .login-page::before,
        .login-page::after,
        .auth-pane,
        .auth-overlay,
        .login-submit,
        .register-submit-loader { animation: none; transition: none; }
    }
</style>

<style>
    /* From Uiverse.io by micaelgomestavares - https://uiverse.io/micaelgomestavares/dull-walrus-66
       MIT License, Copyright (c) JohnnyCSilva, JohnnyCSilva (João Silva), micaelgomestavares (Micael G. Tavares) */
    .sign-in-pane {
        padding:clamp(1.5rem,3vw,2.4rem);
        border-radius:1.25rem;
        color:#25213e;
        background:#fff;
    }

    .sign-in-pane .form-label {
        color:#25213e !important;
        font-weight:700;
    }

    .sign-in-pane .login-input-group {
        height:50px;
        overflow:hidden;
        border:1.5px solid #ecedec;
        border-radius:10px;
        background:#fff;
        box-shadow:none !important;
    }

    .sign-in-pane .login-input-group:focus-within {
        border-color:#7658df;
        box-shadow:0 0 0 3px rgba(118,88,223,.15) !important;
        transform:none;
    }

    .sign-in-pane .login-input-icon {
        padding-inline:10px;
        color:#6b55c4 !important;
        background:#fff !important;
    }

    .sign-in-pane .login-input {
        width:85%;
        height:100%;
        margin-left:6px;
        padding:0 .6rem !important;
        border:0 !important;
        border-radius:10px;
        color:#25213e !important;
        background:#fff !important;
        font-size:.95rem !important;
        box-shadow:none !important;
    }

    .sign-in-pane .login-password-eye {
        width:2.8rem;
        min-width:2.8rem;
        border-radius:0 10px 10px 0 !important;
        color:#6b55c4 !important;
        background:#fff !important;
    }

    .sign-in-pane .form-check-label { color:#514b63 !important; }

</style>

<div class="login-page d-flex align-items-center justify-content-center">

    <div class="login-shell">
        @if(session('status'))
            <div class="sinag-persistent-notice login-alert alert border-0 mb-3">
                <i class="bi bi-check-circle-fill me-1"></i> {{ session('status') }}
            </div>
        @endif
        @if(session('error'))
            <div class="sinag-persistent-notice login-alert alert border-0 mb-3">
                <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('error') }}
            </div>
        @endif
        <div class="card login-card border-0 rounded-4 overflow-hidden">
                        <div class="login-form-panel">
                            @if($errors->any() && !session('password_reset_url'))
                                <div class="sinag-persistent-notice alert alert-warning py-2 mb-3" role="alert">
                                    <ul class="mb-0 small">
                                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="auth-mobile-brand" aria-label="SINAG, Pangasinan State University Gender and Development">
                                <div class="auth-brand-logo">
                                    <img src="{{ asset('images/psulogo.png') }}" alt="PSU Logo">
                                    <img src="{{ asset('images/gadlogo.png') }}" alt="GAD Logo">
                                </div>
                                <h1 class="auth-brand-title">SINAG</h1>
                            </div>
                            <div class="auth-stage {{ ($showRegisterPanel ?? request()->routeIs('register')) ? 'right-panel-active' : '' }}" id="authStage">
                            <form method="POST" action="{{ route('login') }}" id="loginForm" class="auth-pane sign-in-pane" autocomplete="off">
                                @csrf

                                <div class="mb-4">
                                    <label class="form-label small fw-bold text-uppercase" style="letter-spacing: 1px;">Email</label>
                                    <div class="login-input-group input-group shadow-sm">
                                        <span class="login-input-icon input-group-text border-0">
                                            <i class="bi bi-envelope-at fs-5"></i>
                                        </span>
                                        <input id="email" type="email" class="login-input form-control form-control-lg border-0 py-3"
                                                 name="email" value="{{ old('email', request()->cookie('remembered_email', '')) }}" required autocomplete="off" readonly onfocus="this.removeAttribute('readonly')"
                                               placeholder="username@psu.edu.ph" style="font-size: 1rem;">
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold text-uppercase mb-0" style="letter-spacing: 1px;">Password</label>
                                    </div>

                                    <div>
                                        <div class="login-input-group input-group shadow-sm">
                                            <span class="login-input-icon input-group-text border-0">
                                                <i class="bi bi-key fs-5"></i>
                                            </span>
                                              <input id="password" type="password" class="login-input login-password-input form-control form-control-lg border-0 py-3"
                                                  name="login_password" required autocomplete="off" readonly onfocus="this.removeAttribute('readonly')" data-lpignore="true" data-1p-ignore="true" style="font-size: 1rem;">
                                              <input type="hidden" name="password" id="passwordValue" autocomplete="off">
                                            <button class="login-password-eye input-group-text login-password-toggle" type="button" id="togglePassword"
                                                    title="Show password" aria-label="Show password" aria-pressed="false">
                                                <i class="bi bi-eye-slash fs-5" id="eyeIcon" style="opacity: 0.9; transition: opacity 0.2s ease, transform 0.2s ease;"></i>
                                            </button>
                                        </div>
                                        <div class="form-text" id="passwordHint" hidden>Your password must be at least 8 characters.</div>
                                    </div>
                                </div>

                                <div class="mb-4 px-1">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1" {{ old('remember') || request()->hasCookie('remembered_email') ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="remember">Keep session active</label>
                                    </div>
                                </div>

                                <div class="login-actions d-grid gap-2">
                                    <button type="submit" class="login-submit btn btn-lg shadow-lg fw-bold text-dark py-3 fs-5 flex-fill"
                                            style="min-width: 0;">
                                        LOG IN
                                    </button>
                                </div>
                                <div class="google-auth-divider"><span>or</span></div>
                                <a class="google-auth-button" href="{{ route('auth.google.redirect', ['intent' => 'login']) }}">
                                    <i class="bi bi-google" aria-hidden="true"></i> Sign in with Google
                                </a>

                                <div class="text-center mt-3">
                                    <a class="login-forgot text-decoration-none small fw-bold" href="{{ route('password.code.form') }}">
                                        Forgot Password?
                                    </a>
                                    <span class="text-white-50 mx-2">|</span>
                                    <a class="login-forgot text-decoration-none small fw-bold" href="{{ route('password.status.form') }}">
                                        Check Request Status
                                    </a>
                                </div>
                                <div class="mobile-signup-prompt">
                                    <span>Don't have an account?</span>
                                    <button type="button" class="auth-toggle" data-auth-toggle="signup">SIGN UP</button>
                                </div>
                            </form>

                            <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" id="registerForm" class="auth-pane sign-up-pane" autocomplete="off">
                                @csrf
                                @php($googleSignup = session('google_signup', []))
                                <h3 class="text-white fw-bold mb-1">Create Account</h3>
                                <p class="text-white-50 small mb-3">Join the SINAG network today.</p>
                                <div class="google-auth-divider"><span>or prefill your details with</span></div>
                                <a class="google-auth-button mb-3" href="{{ route('auth.google.redirect', ['intent' => 'signup']) }}">
                                    <i class="bi bi-google" aria-hidden="true"></i> Continue with Google
                                </a>
                                <div class="row g-2 register-fields">
                                    <div class="col-12 register-name-row">
                                        <div>
                                            <label class="form-label small fw-bold text-uppercase">First Name</label>
                                            <input type="text" class="form-control" name="first_name" value="{{ old('first_name', $googleSignup['first_name'] ?? '') }}" maxlength="255" required autocomplete="given-name" placeholder="Juan">
                                        </div>
                                        <div>
                                            <label class="form-label small fw-bold text-uppercase">Middle Name <span class="text-muted">(optional)</span></label>
                                            <input type="text" class="form-control" name="middle_name" value="{{ old('middle_name', $googleSignup['middle_name'] ?? '') }}" maxlength="255" autocomplete="additional-name" placeholder="(Optional)">
                                        </div>
                                        <div>
                                            <label class="form-label small fw-bold text-uppercase">Last Name</label>
                                            <input type="text" class="form-control" name="last_name" value="{{ old('last_name', $googleSignup['last_name'] ?? '') }}" maxlength="255" required autocomplete="family-name" placeholder="Dela Cruz">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-bold text-uppercase">Institutional Email</label>
                                        <input type="email" class="form-control" name="email" id="registerEmail" value="{{ old('email', $googleSignup['email'] ?? '') }}" required placeholder="username@psu.edu.ph">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-bold text-uppercase">Contact Number</label>
                                        <input type="tel" class="form-control" name="phone_number" value="{{ old('phone_number') }}" inputmode="numeric" pattern="09[0-9]{9}" minlength="11" maxlength="11" required placeholder="e.g. 09123456789" title="Enter an 11-digit Philippine mobile number starting with 09">
                                    </div>
                                    <div class="col-12 register-account-row">
                                        <div>
                                            <label class="form-label small fw-bold text-uppercase">Account Type</label>
                                            <select class="form-select" name="account_type" id="accountType" required>
                                                <option value="" disabled {{ old('account_type') ? '' : 'selected' }}>Choose type</option>
                                                <option value="student" {{ old('account_type') === 'student' ? 'selected' : '' }}>Student</option>
                                                <option value="employee" {{ old('account_type') === 'employee' ? 'selected' : '' }}>Employee</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="form-label small fw-bold text-uppercase">Age</label>
                                            <input type="number" class="form-control" name="age" value="{{ old('age') }}" min="1" max="120" required>
                                        </div>
                                        <div>
                                            <label class="form-label small fw-bold text-uppercase">Gender</label>
                                            <select class="form-select" name="gender" required>
                                                <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Choose</option>
                                                @foreach(['Male', 'Female', 'Prefer not to say'] as $option)
                                                    <option value="{{ $option }}" {{ old('gender') === $option ? 'selected' : '' }}>{{ $option }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12 register-detail-row">
                                        <div>
                                            <label class="form-label small fw-bold text-uppercase">Department / Course</label>
                                            <select class="form-select" name="department" id="department" required data-old-department="{{ old('department') }}">
                                                <option value="" disabled {{ old('department') ? '' : 'selected' }}>Choose course or department</option>
                                            </select>
                                        </div>
                                        <div class="d-none" id="employeeCategoryField">
                                            <label class="form-label small fw-bold text-uppercase">Employee Category</label>
                                            <select class="form-select" name="employee_category" id="employeeCategory">
                                                <option value="" disabled {{ old('employee_category') ? '' : 'selected' }}>Choose employee category</option>
                                                <option value="teaching" {{ old('employee_category') === 'teaching' ? 'selected' : '' }}>Teaching</option>
                                                <option value="non_teaching" {{ old('employee_category') === 'non_teaching' ? 'selected' : '' }}>Non-teaching</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12 register-upload-row">
                                        <div class="register-upload-field">
                                            <label class="form-label small fw-bold text-uppercase">Profile Picture</label>
                                            <input type="file" class="form-control" name="profile_photo" accept="image/jpeg,image/png,image/webp" required>
                                            <div class="form-text">JPG, PNG, or WEBP up to 10 MB.</div>
                                        </div>
                                        <div class="register-upload-field">
                                            <label class="form-label small fw-bold text-uppercase">Photo of your Student ID</label>
                                            <input type="file" class="form-control" name="selfie_image" accept="image/jpeg,image/png,image/webp" required>
                                            <div class="form-text">JPG, PNG, or WEBP up to 10 MB.</div>
                                        </div>
                                    </div>
                                    <div class="col-12 register-password-row">
                                        <div>
                                            <label class="form-label small fw-bold text-uppercase">Password</label>
                                            <div class="position-relative">
                                                <input type="password" class="form-control register-password" name="password" id="registerPassword" autocomplete="off" readonly onfocus="this.removeAttribute('readonly')" data-lpignore="true" data-1p-ignore="true" required>
                                                <button type="button" class="register-password-toggle" data-password-target="registerPassword" aria-label="Show password" aria-pressed="false"><i class="bi bi-eye-slash"></i></button>
                                            </div>
                                            <div class="form-text" id="registerPasswordHint" hidden>Use at least 8 characters.</div>
                                        </div>
                                        <div>
                                            <label class="form-label small fw-bold text-uppercase">Confirm Password</label>
                                            <div class="position-relative">
                                                <input type="password" class="form-control register-password" name="password_confirmation" id="registerPasswordConfirmation" autocomplete="off" readonly onfocus="this.removeAttribute('readonly')" data-lpignore="true" data-1p-ignore="true" required>
                                                <button type="button" class="register-password-toggle" data-password-target="registerPasswordConfirmation" aria-label="Show password" aria-pressed="false"><i class="bi bi-eye-slash"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 register-submit-wrap mt-2">
                                        <button type="submit" class="btn fw-bold register-submit">
                                            <span class="register-submit-label">REGISTER ACCOUNT</span>
                                            <span class="register-submit-icon" aria-hidden="true"><i class="bi bi-arrow-right register-submit-arrow"></i><span class="register-submit-loader"></span></span>
                                        </button>
                                        <div class="register-links small">
                                            <a class="login-forgot text-decoration-none fw-bold" href="{{ route('password.code.form') }}">Forgot Password?</a>
                                            <span class="text-muted" aria-hidden="true">|</span>
                                            <a class="login-forgot text-decoration-none fw-bold" href="{{ route('password.status.form') }}">Check Request Status</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="auth-mobile-signin-dock">
                                    Already have an account? <button type="button" data-auth-toggle="signin">Log in</button>
                                </div>
                            </form>

                            <div class="auth-overlay">
                                <div class="auth-overlay-copy auth-overlay-right">
                                    <div class="auth-brand-logo" aria-label="Pangasinan State University Gender and Development">
                                        <img src="{{ asset('images/psulogo.png') }}" alt="PSU Logo">
                                        <img src="{{ asset('images/gadlogo.png') }}" alt="GAD Logo">
                                    </div>
                                    <h1 class="auth-brand-title">SINAG</h1>
                                    <p>Gender and Development Safe Space and Participatory Suggestion System for Employees and Students of Pangasinan State University</p>
                                    <div class="auth-overlay-account">Don't have an account?</div>
                                    <button class="auth-toggle" type="button" data-auth-toggle="signup">SIGN UP</button>
                                </div>
                                <div class="auth-overlay-copy auth-overlay-left">
                                    <div class="auth-brand-logo" aria-label="Pangasinan State University Gender and Development">
                                        <img src="{{ asset('images/psulogo.png') }}" alt="PSU Logo">
                                        <img src="{{ asset('images/gadlogo.png') }}" alt="GAD Logo">
                                    </div>
                                    <h1 class="auth-brand-title">SINAG</h1>
                                    <h2 class="fw-bold">Welcome Back!</h2>
                                    <p>Gender and Development Safe Space and Participatory Suggestion System for Employees and Students of Pangasinan State University</p>
                                    <div class="auth-overlay-account">Already have an account?</div>
                                    <button class="auth-toggle" type="button" data-auth-toggle="signin">SIGN IN</button>
                                </div>
                            </div>
                            </div>
                        </div>

        </div>
    </div>
</div>

<div class="modal fade" id="institutionalEmailModal" tabindex="-1" aria-labelledby="institutionalEmailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-body p-4 text-center">
                <div class="mb-3 text-warning fs-1"><i class="bi bi-envelope-exclamation" aria-hidden="true"></i></div>
                <h2 class="h5 fw-bold" id="institutionalEmailModalLabel">Use your institutional email</h2>
                <p class="text-muted mb-0">Students and employees need a Pangasinan State University email address ending in <strong>@psu.edu.ph</strong> to register.</p>
            </div>
            <div class="modal-footer border-0 justify-content-center pt-0">
                <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-dismiss="modal">Got it</button>
            </div>
        </div>
    </div>
</div>

@if(session('password_reset_url'))
    <div class="modal fade password-lockout-modal" id="passwordLockoutModal" tabindex="-1"
         aria-labelledby="passwordLockoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 rounded-4 shadow-lg">
                <div class="modal-body text-center">
                    <button type="button" class="lockout-close d-flex align-items-center justify-content-center"
                            data-bs-dismiss="modal" aria-label="Close notification">&times;</button>
                    <div class="lockout-icon d-flex align-items-center justify-content-center">
                        <i class="bi bi-shield-lock-fill"></i>
                    </div>
                    <h2 class="h5 fw-bold mb-2" id="passwordLockoutModalLabel">Password reset required</h2>
                    <p class="lockout-copy mb-0">
                        Your account is locked after three failed password attempts. Reset your password to continue.
                    </p>
                    <div class="lockout-actions">
                        <a href="{{ session('password_reset_url') }}" class="lockout-action d-flex align-items-center justify-content-center">Reset password</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<script>
    const authStage = document.getElementById('authStage');
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    const loginSubmit = document.querySelector('.login-submit');
    let touchPressTimeout;

    loginSubmit?.addEventListener('pointerdown', function (event) {
        if (event.pointerType !== 'touch') return;
        clearTimeout(touchPressTimeout);
        loginSubmit.classList.add('is-pressed');
    });
    ['pointerup', 'pointercancel', 'pointerleave'].forEach(function (eventName) {
        loginSubmit?.addEventListener(eventName, function () {
            clearTimeout(touchPressTimeout);
            touchPressTimeout = setTimeout(function () {
                loginSubmit.classList.remove('is-pressed');
            }, 220);
        });
    });

    const loginPage = document.querySelector('.login-page');
    const loginShell = document.querySelector('.login-shell');
    function updateAuthCardAlignment() {
        const pageStyle = getComputedStyle(loginPage);
        const verticalPadding = parseFloat(pageStyle.paddingTop) + parseFloat(pageStyle.paddingBottom);
        const availableHeight = window.innerHeight - verticalPadding;
        const canCenter = window.matchMedia('(min-width: 768px)').matches
            && loginShell.getBoundingClientRect().height <= availableHeight;
        loginPage.classList.toggle('auth-card-centered', canCenter);
    }

    function setAuthPanel(showSignup) {
        authStage.classList.toggle('right-panel-active', showSignup);
        document.querySelector('.login-form-panel').classList.toggle('signup-active', showSignup);
        loginPage.classList.toggle('signup-active', showSignup);
        loginForm.querySelectorAll('input, button, select, textarea').forEach(function (field) {
            field.disabled = showSignup;
        });
        registerForm.querySelectorAll('input, button, select, textarea').forEach(function (field) {
            field.disabled = !showSignup;
        });
        if (showSignup && document.activeElement instanceof HTMLElement) document.activeElement.blur();
        requestAnimationFrame(updateAuthCardAlignment);
    }
    document.querySelectorAll('[data-auth-toggle]').forEach(function (button) {
        button.addEventListener('click', function () {
            const showSignup = button.dataset.authToggle === 'signup';
            setAuthPanel(showSignup);
            if (!showSignup) document.getElementById('email')?.focus({ preventScroll: true });
        });
    });

    setAuthPanel(authStage.classList.contains('right-panel-active'));
    window.addEventListener('resize', updateAuthCardAlignment);
    if ('ResizeObserver' in window) new ResizeObserver(updateAuthCardAlignment).observe(loginShell);

    const loginPassword = document.querySelector('#password');
    const passwordValue = document.querySelector('#passwordValue');

    window.addEventListener('pageshow', function () {
        // Clear a restored password only when the login page is loaded again, never before submit.
        loginPassword.value = '';
        passwordValue.value = '';
    });

    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');
    const eyeIcon = document.querySelector('#eyeIcon');
    const passwordHint = document.querySelector('#passwordHint');
    const registerPassword = document.querySelector('#registerPassword');
    const registerPasswordHint = document.querySelector('#registerPasswordHint');

    function updatePasswordHint() {
        const length = loginPassword.value.length;
        passwordHint.hidden = length === 0 || length >= 8;
    }

    togglePassword.addEventListener('click', function () {
        const isHidden = password.type === 'password';
        password.type = isHidden ? 'text' : 'password';
        eyeIcon.classList.toggle('bi-eye', isHidden);
        eyeIcon.classList.toggle('bi-eye-slash', !isHidden);
        togglePassword.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
        togglePassword.setAttribute('aria-pressed', String(isHidden));
        togglePassword.setAttribute('title', isHidden ? 'Hide password' : 'Show password');
    });

    loginPassword.addEventListener('input', updatePasswordHint);
    registerPassword.addEventListener('input', function () {
        const length = registerPassword.value.length;
        registerPasswordHint.hidden = length === 0 || length >= 8;
    });
    loginForm.addEventListener('submit', function () {
        passwordValue.value = loginPassword.value;
    });

    document.querySelectorAll('.register-password-toggle').forEach(function (button) {
        button.addEventListener('click', function () {
            const input = document.getElementById(button.dataset.passwordTarget);
            const icon = button.querySelector('i');
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            icon.classList.toggle('bi-eye', isHidden);
            icon.classList.toggle('bi-eye-slash', !isHidden);
            button.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
            button.setAttribute('aria-pressed', String(isHidden));
        });
    });

    registerForm.querySelectorAll('input[name="phone_number"]').forEach(function (phoneInput) {
        phoneInput.addEventListener('input', function () {
            phoneInput.value = phoneInput.value.replace(/[^0-9]/g, '').slice(0, 11);
        });
    });

    registerForm.addEventListener('submit', function () {
        const submitButton = registerForm.querySelector('.register-submit');
        if (submitButton) {
            submitButton.classList.add('loading');
            submitButton.disabled = true;
        }
        const email = registerForm.querySelector('[name="email"]').value.trim().toLowerCase();
        const accounts = JSON.parse(localStorage.getItem('sinag.login.accounts') || '[]');
        if (email && email.includes('@')) {
            localStorage.setItem('sinag.login.accounts', JSON.stringify([email].concat(accounts.filter(account => account !== email)).slice(0, 10)));
        }
    });

    const accountType = document.getElementById('accountType');
    const department = document.getElementById('department');
    const employeeCategoryField = document.getElementById('employeeCategoryField');
    const employeeCategory = document.getElementById('employeeCategory');
    const registerEmail = document.getElementById('registerEmail');
    const institutionalEmailModal = document.getElementById('institutionalEmailModal');
    const oldDepartment = department.dataset.oldDepartment;
    const departmentOptions = {
        student: [
            ['Bachelor Industrial Technology - Major in Electrical Technology', 'Bachelor Industrial Technology - Major in Electrical Technology'],
            ['Bachelor Industrial Technology - Major in Food Management Service', 'Bachelor Industrial Technology - Major in Food Management Service'],
            ['Bachelor Industrial Technology - Major in Mechanical Technology', 'Bachelor Industrial Technology - Major in Mechanical Technology'],
            ['Bachelor of Elementary Education (BEEd)', 'Bachelor of Elementary Education (BEEd)'],
            ['Bachelor of Technology and Livelihood Education (BTLEd)', 'Bachelor of Technology and Livelihood Education (BTLEd)'],
            ['Bachelor Secondary Education (BSEd) - Major in English', 'Bachelor Secondary Education (BSEd) - Major in English'],
            ['Bachelor Secondary Education (BSEd) - Major in Math', 'Bachelor Secondary Education (BSEd) - Major in Math'],
            ['Bachelor Secondary Education (BSEd) - Major in Science', 'Bachelor Secondary Education (BSEd) - Major in Science'],
            ['BS Business Administration (BSBA)', 'BS Business Administration (BSBA)'],
            ['BS Information Technology (BSIT)', 'BS Information Technology (BSIT)']
        ],
        employee: [
            ['College of Technology and Business', 'College of Technology and Business'],
            ['College of Education', 'College of Education']
        ]
        ,
        non_teaching: [
            ['Non-Teaching', 'Non-Teaching'],
            ['Office of the Registrar', 'Office of the Registrar'],
            ['Human Resource Office', 'Human Resource Office'],
            ['Finance Office', 'Finance Office'],
            ['Library', 'Library'],
            ['IT Services', 'IT Services'],
            ['Student Affairs Office', 'Student Affairs Office']
        ]
    };
    function updateDepartmentOptions() {
        department.innerHTML = '<option value="" disabled selected>Choose course or department</option>';
        let departmentKey = accountType.value;
        if (departmentKey === 'employee') {
            departmentKey = employeeCategory.value === 'non_teaching' ? 'non_teaching' : 'employee';
        }
        (departmentOptions[departmentKey] || []).forEach(function ([value, label]) {
            department.add(new Option(label, value, false, value === oldDepartment));
        });
        const isEmployee = accountType.value === 'employee';
        employeeCategoryField.classList.toggle('d-none', !isEmployee);
        employeeCategory.required = isEmployee;
        if (!isEmployee) employeeCategory.value = '';
    }
    accountType.addEventListener('change', updateDepartmentOptions);
    employeeCategory.addEventListener('change', updateDepartmentOptions);
    updateDepartmentOptions();

    registerForm.addEventListener('submit', function (event) {
        if (!registerEmail.value.trim().toLowerCase().endsWith('@psu.edu.ph')) {
            event.preventDefault();
            window.bootstrap.Modal.getOrCreateInstance(institutionalEmailModal).show();
        }
    });
    institutionalEmailModal.addEventListener('hidden.bs.modal', function () {
        registerEmail.focus({ preventScroll: true });
    });

    const passwordLockoutModal = document.getElementById('passwordLockoutModal');
    if (passwordLockoutModal) {
        const passwordLockoutBackdrop = document.createElement('div');
        passwordLockoutBackdrop.className = 'modal-backdrop fade show';
        document.body.appendChild(passwordLockoutBackdrop);
        document.body.classList.add('modal-open');
        document.body.style.overflow = 'hidden';
        passwordLockoutModal.style.display = 'flex';
        passwordLockoutModal.classList.add('show');
        passwordLockoutModal.setAttribute('aria-hidden', 'false');
        passwordLockoutModal.setAttribute('aria-modal', 'true');

        const closePasswordLockoutModal = function () {
            passwordLockoutModal.classList.remove('show');
            passwordLockoutModal.style.display = 'none';
            passwordLockoutModal.setAttribute('aria-hidden', 'true');
            passwordLockoutModal.removeAttribute('aria-modal');
            passwordLockoutBackdrop.remove();
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
        };

        passwordLockoutModal.querySelectorAll('[data-bs-dismiss="modal"]').forEach(function (button) {
            button.addEventListener('click', closePasswordLockoutModal);
        });
    }

</script>
@endsection
