<aside class="gad-faq-widget" id="gadFaqWidget" aria-label="GAD and SINAG FAQ assistant">
    <section class="gad-faq-panel" id="gadFaqPanel" aria-labelledby="gadFaqTitle" hidden>
        <header class="gad-faq-header">
            <div class="gad-faq-brand">
                <span class="gad-faq-mascot" aria-hidden="true"><i class="bi bi-heart-fill"></i><i class="bi bi-stars"></i></span>
                <span><strong id="gadFaqTitle">GAD Guide</strong><small>SINAG FAQs, with care</small></span>
            </div>
            <div class="d-flex align-items-center gap-1">
                <button type="button" class="gad-faq-control gad-faq-drag-handle" aria-label="Move chatbot" title="Drag to move"><i class="bi bi-arrows-move"></i></button>
                <button type="button" class="gad-faq-control" id="gadFaqClose" aria-label="Close FAQ assistant"><i class="bi bi-x-lg"></i></button>
            </div>
        </header>
        <div class="gad-faq-messages" id="gadFaqMessages" aria-live="polite">
            <div class="gad-faq-message gad-faq-answer">Hi! I can help with FAQs about GAD and the SINAG system. Pick a question or type a related keyword.</div>
            <div class="gad-faq-quick-questions" id="gadFaqQuestions"></div>
        </div>
        <form class="gad-faq-input" id="gadFaqForm">
            <label class="visually-hidden" for="gadFaqInput">Ask a GAD or SINAG FAQ</label>
            <input type="text" id="gadFaqInput" maxlength="120" placeholder="Ask about GAD or SINAG..." autocomplete="off">
            <button type="submit" aria-label="Send question"><i class="bi bi-send-fill"></i></button>
        </form>
        <div class="gad-faq-footnote"><i class="bi bi-shield-heart"></i> FAQ guide only · For personal support, message the GAD Office.</div>
    </section>
    <button type="button" class="gad-faq-launcher" id="gadFaqLauncher" aria-label="Open GAD FAQ assistant" aria-expanded="false" title="GAD Guide — drag to move">
        <span class="gad-faq-launcher-tooltip" aria-hidden="true">GAD Guide</span>
        <svg class="gad-faq-launcher-badge" viewBox="0 0 100 112" aria-hidden="true">
            <defs>
                <filter id="gadFaqBadgeShadow" x="-30%" y="-30%" width="160%" height="160%">
                    <feDropShadow dx="0" dy="3" stdDeviation="3" flood-color="#5b43b2" flood-opacity=".28"></feDropShadow>
                </filter>
                <linearGradient id="gadFaqHeartFill" x1="0" y1="0" x2="1" y2="1">
                    <stop offset="0%" stop-color="#d9d0ff"></stop>
                    <stop offset="55%" stop-color="#967af3"></stop>
                    <stop offset="100%" stop-color="#6648c5"></stop>
                </linearGradient>
            </defs>
            <path d="M18 23 C18 15 24 10 32 10 H68 C76 10 82 16 82 24 V49 C82 57 76 63 68 63 H43 L30 73 L33 63 H32 C24 63 18 57 18 49 Z" fill="#ffffff" filter="url(#gadFaqBadgeShadow)"></path>
            <path class="gad-faq-badge-heart" d="M50 54 C44 49 34 42 34 34 C34 28 38 24 44 24 C47 24 49 26 50 29 C51 26 53 24 56 24 C62 24 66 28 66 34 C66 42 56 49 50 54 Z" fill="url(#gadFaqHeartFill)"></path>
            <path class="gad-faq-badge-star" d="M70 16 L72 21 L77 23 L72 25 L70 30 L68 25 L63 23 L68 21 Z" fill="#ffdf6c"></path>
            <path class="gad-faq-badge-star gad-faq-badge-star-small" d="M24 25 L25.5 28 L29 29.5 L25.5 31 L24 34 L22.5 31 L19 29.5 L22.5 28 Z" fill="#c8b9ff"></path>
        </svg>
    </button>
</aside>

<style>
    .gad-faq-widget {
        position: fixed;
        z-index: 1085;
        right: 1.25rem;
        bottom: 1.25rem;
        width: auto;
        font-family: 'Nunito', sans-serif;
        pointer-events: none;
    }

    .gad-faq-launcher {
        pointer-events: auto;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 5.5rem;
        height: 5.8rem;
        padding: 0;
        border: 0;
        border-radius: 50%;
        color: #5b43b2;
        background: transparent;
        box-shadow: none;
        cursor: grab;
        touch-action: none;
        transition: transform .22s ease, box-shadow .22s ease, filter .22s ease;
    }

    .gad-faq-launcher:hover,
    .gad-faq-launcher:focus-visible {
        transform: translateY(-4px) scale(1.04);
        box-shadow: none;
        filter: saturate(1.1);
    }

    .gad-faq-launcher:active { cursor: grabbing; }
    .gad-faq-launcher-badge {
        width: 4.65rem;
        height: 5.2rem;
        overflow: visible;
        filter: drop-shadow(0 7px 12px rgba(76, 29, 149, .20));
    }
    /* From Uiverse.io by vinodjangid07 - https://uiverse.io/vinodjangid07/funny-panther-79
       MIT License, Copyright (c) vinodjangid07 (Vinod Jangid) */
    .gad-faq-launcher-tooltip {
        position: absolute;
        top: -.25rem;
        left: 50%;
        z-index: 2;
        padding:.35rem .7rem;
        border-radius:.4rem;
        color:#fff;
        background:linear-gradient(120deg,#7658df 0%,#a594f9 58%,#db8db8 100%);
        font-size:.75rem;
        font-weight:800;
        letter-spacing:.04em;
        opacity:0;
        pointer-events:none;
        transform:translate(-50%,0);
        transition:top .3s ease,opacity .3s ease;
        white-space: nowrap;
    }
    .gad-faq-launcher-tooltip::before {
        position:absolute;
        bottom:-.2rem;
        left:calc(50% - .25rem);
        width:.5rem;
        height:.5rem;
        content:"";
        background:#db8db8;
        transform:rotate(45deg);
    }
    .gad-faq-launcher:hover .gad-faq-launcher-tooltip,
    .gad-faq-launcher:focus-visible .gad-faq-launcher-tooltip {
        top:-2.3rem;
        opacity:1;
    }
    .gad-faq-launcher:hover .gad-faq-launcher-badge { animation:gadFaqJelloVertical .7s both; }
    @keyframes gadFaqJelloVertical {
        0%,100% { transform:scale3d(1,1,1); }
        30% { transform:scale3d(.75,1.25,1); }
        40% { transform:scale3d(1.25,.75,1); }
        50% { transform:scale3d(.85,1.15,1); }
        65% { transform:scale3d(1.05,.95,1); }
        75% { transform:scale3d(.95,1.05,1); }
    }
    .gad-faq-badge-heart {
        filter: drop-shadow(0 3px 2px rgba(67, 43, 143, .38));
        transform-origin: 50px 50px;
        animation: gadFaqHeart30s 15s ease-in-out infinite;
    }
    .gad-faq-badge-star {
        filter: drop-shadow(0 1.5px 1px rgba(92,55,171,.28));
        transform-origin: center;
        animation: gadFaqStar30s 15s ease-in-out infinite;
    }
    .gad-faq-badge-star-small {
        opacity: .75;
    }
    @keyframes gadFaqHeart30s {
        0%, 88%, 100% { transform: scale(1); }
        91% { transform: scale(1.38); }
        94% { transform: scale(.94); }
        97% { transform: scale(1.28); }
    }
    @keyframes gadFaqStar30s {
        0%, 88%, 100% { transform: scale(1) rotate(0deg); opacity: 1; }
        91% { transform: scale(1.55) rotate(12deg); opacity: 1; }
        94% { transform: scale(.85) rotate(-8deg); opacity: .75; }
        97% { transform: scale(1.35) rotate(6deg); opacity: 1; }
    }

    .gad-faq-widget.gad-faq-wiggle .gad-faq-launcher {
        animation: gadFaqNudge .75s ease-in-out;
    }

    @keyframes gadFaqNudge {
        0%, 100% { transform: rotate(0) scale(1); }
        20% { transform: rotate(-5deg) scale(1.05); }
        40% { transform: rotate(5deg) scale(1.05); }
        60% { transform: rotate(-3deg) scale(1.03); }
        80% { transform: rotate(3deg) scale(1.02); }
    }

    .gad-faq-panel {
        position: absolute;
        left: 0;
        bottom: calc(100% + .65rem);
        display: flex;
        flex-direction: column;
        width: min(22rem, calc(100vw - 1rem));
        height: min(34rem, calc(100dvh - 2rem));
        min-height: min(17rem, calc(100dvh - 2rem));
        overflow: hidden;
        border: 1px solid #e9ddff;
        border-radius: 1.2rem;
        background: #fff;
        box-shadow: 0 18px 50px rgba(48, 30, 91, .22);
        transform-origin: bottom right;
        animation: gadFaqPop .2s ease-out;
        pointer-events: auto;
    }

    .gad-faq-panel[hidden] { display: none; }
    .gad-faq-open-below .gad-faq-panel { top: calc(100% + .65rem); bottom: auto; }
    @keyframes gadFaqPop { from { opacity: 0; transform: translateY(8px) scale(.97); } to { opacity: 1; transform: translateY(0) scale(1); } }
    .gad-faq-header { display: flex; align-items: center; justify-content: space-between; padding: .85rem 1rem; color: #fff; background: linear-gradient(120deg, #7757d9, #a594f9 70%, #db8db8); }
    .gad-faq-brand { display: flex; align-items: center; gap: .65rem; }
    .gad-faq-brand strong, .gad-faq-brand small { display: block; }
    .gad-faq-brand small { opacity: .88; font-size: .72rem; }
    .gad-faq-mascot { position: relative; display: grid; width: 2.4rem; height: 2.4rem; place-items: center; border-radius: 50%; color: #8f72f5; background: #fff; }
    .gad-faq-mascot .bi-stars { position: absolute; top: -.3rem; right: -.35rem; color: #ffdf6c; font-size: .8rem; }
    .gad-faq-control { display: grid; width: 2rem; height: 2rem; place-items: center; border: 0; border-radius: .55rem; color: #fff; background: rgba(255,255,255,.17); }
    .gad-faq-control:hover { background: rgba(255,255,255,.3); }
    .gad-faq-drag-handle { cursor: move; touch-action: none; }
    .gad-faq-messages { display: flex; flex: 1 1 auto; flex-direction: column; gap: .75rem; min-height: 0; padding: 1rem; overflow-y: auto; overscroll-behavior: contain; background: #fcfbff; }
    .gad-faq-message { max-width: 92%; padding: .7rem .85rem; border-radius: .9rem; font-size: .86rem; line-height: 1.45; white-space: pre-line; }
    .gad-faq-answer { align-self: flex-start; border: 1px solid #eee8ff; border-bottom-left-radius: .25rem; color: #40365c; background: #fff; }
    .gad-faq-question { align-self: flex-end; border-bottom-right-radius: .25rem; color: #fff; background: #8f72f5; }
    .gad-faq-answer-links { display: flex; flex-wrap: wrap; gap: .4rem; margin-top: .65rem; }
    .gad-faq-answer-links a { display: inline-flex; align-items: center; gap: .3rem; padding: .38rem .58rem; border: 1px solid #d9ceff; border-radius: 999px; color: #6148b1; background: #f6f3ff; font-size: .76rem; font-weight: 800; text-decoration: none; }
    .gad-faq-answer-links a::after { content: '\2197'; font-size: .72rem; }
    .gad-faq-answer-links a:hover, .gad-faq-answer-links a:focus-visible { border-color: #a594f9; background: #ebe5ff; }
    .gad-faq-quick-questions { display: flex; flex-wrap: wrap; gap: .4rem; }
    .gad-faq-quick-questions button { padding: .38rem .58rem; border: 1px solid #d9ceff; border-radius: 999px; color: #6148b1; background: #f6f3ff; font-size: .72rem; font-weight: 800; transition: transform .18s ease, background .18s ease; }
    .gad-faq-quick-questions button:hover { transform: translateY(-2px); background: #ebe5ff; }
    .gad-faq-input { display: flex; gap: .5rem; padding: .7rem; border-top: 1px solid #f0ebff; }
    .gad-faq-input input { min-width: 0; flex: 1; padding: .65rem .75rem; border: 1px solid #e5def7; border-radius: .7rem; outline: none; }
    .gad-faq-input input:focus { border-color: #a594f9; box-shadow: 0 0 0 3px rgba(165,148,249,.18); }
    .gad-faq-input button { width: 2.65rem; border: 0; border-radius: .7rem; color: #fff; background: #8f72f5; }
    .gad-faq-footnote { flex: 0 0 auto; padding: 0 .8rem .7rem; color: #817b91; text-align: center; font-size: .67rem; }

    html[data-student-theme-enabled="true"][data-student-theme="dark"] .gad-faq-panel {
        border-color: #49365f;
        background: #20192d;
        color: #eee8ff;
        box-shadow: 0 18px 50px rgba(0, 0, 0, .48);
    }
    html[data-student-theme-enabled="true"][data-student-theme="dark"] .gad-faq-header {
        background: linear-gradient(120deg, #5b3fa8, #7658c7 70%, #8f72f5);
    }
    html[data-student-theme-enabled="true"][data-student-theme="dark"] .gad-faq-messages {
        background: #15101f;
    }
    html[data-student-theme-enabled="true"][data-student-theme="dark"] .gad-faq-answer {
        border-color: #49365f;
        color: #eee8ff;
        background: #211a2e;
    }
    html[data-student-theme-enabled="true"][data-student-theme="dark"] .gad-faq-quick-questions button,
    html[data-student-theme-enabled="true"][data-student-theme="dark"] .gad-faq-answer-links a {
        border-color: #594478;
        color: #ded3f4;
        background: #211a2e;
    }
    html[data-student-theme-enabled="true"][data-student-theme="dark"] .gad-faq-quick-questions button:hover,
    html[data-student-theme-enabled="true"][data-student-theme="dark"] .gad-faq-answer-links a:hover,
    html[data-student-theme-enabled="true"][data-student-theme="dark"] .gad-faq-answer-links a:focus-visible {
        border-color: #947cf0;
        color: #fff;
        background: #302348;
    }
    html[data-student-theme-enabled="true"][data-student-theme="dark"] .gad-faq-input {
        border-color: #49365f;
        background: #20192d;
    }
    html[data-student-theme-enabled="true"][data-student-theme="dark"] .gad-faq-input input {
        border-color: #594478;
        color: #eee8ff;
        background: #15101f;
    }
    html[data-student-theme-enabled="true"][data-student-theme="dark"] .gad-faq-input input::placeholder,
    html[data-student-theme-enabled="true"][data-student-theme="dark"] .gad-faq-footnote {
        color: #b7aacb;
    }
    html[data-student-theme-enabled="true"][data-student-theme="dark"] .gad-faq-input button {
        background: #7658df;
    }
    @media (max-width: 575.98px) {
        .gad-faq-widget { right: .65rem; bottom: max(.65rem, env(safe-area-inset-bottom)); }
        .gad-faq-launcher { width: 5rem; height: 5.15rem; }
        .gad-faq-panel { width: min(21rem, calc(100vw - 1rem)); height: min(68dvh, 31rem); min-height: min(15rem, calc(100dvh - 2rem)); border-radius: 1rem; }
        .gad-faq-open-below .gad-faq-panel { transform-origin: top right; }
        .gad-faq-messages { padding: .75rem; }
    }

    @media (max-height: 560px) {
        .gad-faq-panel { height: calc(100dvh - 1rem); min-height: 10rem; }
    }

    @media (max-width: 360px) {
        .gad-faq-launcher-label { display: none; }
        .gad-faq-launcher { padding: .3rem; }
    }

    @media (prefers-reduced-motion: reduce) {
        .gad-faq-widget *, .gad-faq-widget *::before, .gad-faq-widget *::after { animation-duration: .01ms !important; animation-iteration-count: 1 !important; transition-duration: .01ms !important; }
    }
</style>

<script>
    (function () {
        const widget = document.getElementById('gadFaqWidget');
        const launcher = document.getElementById('gadFaqLauncher');
        const panel = document.getElementById('gadFaqPanel');
        const close = document.getElementById('gadFaqClose');
        const form = document.getElementById('gadFaqForm');
        const input = document.getElementById('gadFaqInput');
        const messages = document.getElementById('gadFaqMessages');
        const questions = document.getElementById('gadFaqQuestions');
        if (!widget || !launcher || !panel || !close || !form || !input || !messages || !questions) return;

        const pageLinks = {
            report: @json(route('student.report')),
            reportStatus: @json(route('student.vault')),
            suggestions: @json(route('student.boses')),
            schedule: @json(route('student.schedule-appointment')),
            appointments: @json(route('student.appointments')),
            messages: @json(route('student.messaging')),
            wellness: @json(route('student.wellness')),
            dashboard: @json(route('student.dashboard'))
        };
        const faqs = [
            { question: 'What is GAD and what does the office do?', terms: ['what is gad', 'gad office', 'gender and development', 'gad services'], answer: 'Gender and Development (GAD) works to promote gender equality, dignity, inclusion, and safe spaces. The campus GAD Office can receive concerns, guide students and employees to support, and share awareness and policy information. This FAQ is general information, not legal advice.', links: [{ label: 'Open GAD references', href: pageLinks.wellness }] },
            { question: 'How do I file a report?', terms: ['file a report', 'submit a report', 'report an incident', 'report a concern'], answer: 'Open File a Report, complete the incident details, and submit. Keep the description factual and include only information relevant to the incident. You can review updates in your Evidence Vault. If you are in immediate danger, contact campus security or local emergency services first.', links: [{ label: 'Go to File a Report', href: pageLinks.report }, { label: 'Open Evidence Vault', href: pageLinks.reportStatus }] },
            { question: 'How can I check my report status?', terms: ['report status', 'check my report', 'track report', 'report update'], answer: 'Open your Evidence Vault to review the current status and updates for your submitted report. Include the incident ID if you message the GAD Office about it.', links: [{ label: 'Open Evidence Vault', href: pageLinks.reportStatus }, { label: 'Message the GAD Office', href: pageLinks.messages }] },
            { question: 'How do I book an appointment?', terms: ['book an appointment', 'schedule an appointment', 'request an appointment', 'appointment schedule'], answer: 'Open Schedule, choose an available date and time, select Face-to-Face or Online, and submit your concern. The request remains pending until the GAD Office reviews it. You can follow updates under My Appointments.', links: [{ label: 'Schedule an appointment', href: pageLinks.schedule }, { label: 'My Appointments', href: pageLinks.appointments }] },
            { question: 'How can I cancel or edit an appointment?', terms: ['cancel an appointment', 'cancel appointment', 'cancel my appointment', 'edit appointment', 'change appointment', 'reschedule appointment'], answer: 'Open My Appointments. You can edit or cancel a pending request there. For an approved or completed request, message the GAD Office so staff can help with the change.', links: [{ label: 'Open My Appointments', href: pageLinks.appointments }, { label: 'Message the GAD Office', href: pageLinks.messages }] },
            { question: 'Paano ko ba kanselahin o baguhin ang appointment?', terms: ['paano cancel', 'paano kanselahin', 'kanselahin ang appointment', 'cancel ko', 'mag cancel', 'magkansela', 'pwedeng i-cancel', 'i-cancel', 'baguhin ang appointment', 'reschedule'], answer: 'Buksan ang My Appointments at piliin ang pending na appointment na gusto mong baguhin o kanselahin. I-click ang edit o cancel, pagkatapos ay isumite ang pag-update. Kung approved o completed na ang appointment, makipag-ugnayan sa GAD Office sa Messages upang matulungan ka.', links: [{ label: 'Buksan ang My Appointments', href: pageLinks.appointments }, { label: 'Makipag-ugnayan sa GAD Office', href: pageLinks.messages }] },
            { question: 'Paano ako mag-file ng report?', terms: ['paano mag report', 'mag file ng report', 'i-report', 'ireport', 'paano magsumbong', 'ano ang gagawin sa harassment'], answer: 'Pumunta sa File a Report, piliin ang uri ng insidente, ilagay ang petsa, lugar, at malinaw na kuwento ng nangyari, at mag-upload ng evidence kung mayroon. Pagkatapos isumite, buksan ang Evidence Vault/Report Status para makita ang update. Para sa agarang panganib, tawagan muna ang campus security o emergency services.', links: [{ label: 'Buksan ang File a Report', href: pageLinks.report }, { label: 'Tingnan ang Report Status', href: pageLinks.reportStatus }] },
            { question: 'Paano mag-book ng appointment?', terms: ['paano mag appointment', 'paano magpa-appointment', 'mag book', 'schedule appointment', 'petsa at oras'], answer: 'Buksan ang Schedule Appointment, pumili ng available na petsa at oras, piliin ang Face-to-Face o Online, at isumite ang iyong concern. Magiging pending ang request hanggang ma-review ito ng GAD Office. Makikita mo ang resulta sa My Appointments.', links: [{ label: 'Mag-book ng appointment', href: pageLinks.schedule }, { label: 'Tingnan ang My Appointments', href: pageLinks.appointments }] },
            { question: 'Paano makipag-usap sa GAD Office?', terms: ['makipag usap', 'makipag-usap', 'message sa gad', 'magmessage', 'kontakin', 'kontak', 'tawagan'], answer: 'Buksan ang Messages at magpadala ng private message sa GAD Office. Kung tungkol ito sa report o appointment, isama ang incident ID o appointment date upang mas mabilis matukoy ang iyong concern.', links: [{ label: 'Buksan ang Messages', href: pageLinks.messages }] },
            { question: 'How do I message the GAD Office?', terms: ['message the gad office', 'contact the gad office', 'talk to someone', 'personal support', 'send a message'], answer: 'Open Messages to contact the GAD Office directly. Include an appointment date or report ID when your message concerns an existing request. Messages are not an emergency service; if you are in immediate danger, contact campus security or local emergency services.', links: [{ label: 'Open Messages', href: pageLinks.messages }] },
            { question: 'How do I submit a suggestion?', terms: ['submit a suggestion', 'post a suggestion', 'share an idea', 'suggestion'], answer: 'Open Suggestions, choose a category, write your suggestion, and submit it for review. You can choose whether to post anonymously. Approved suggestions appear in the community feed.', links: [{ label: 'Open Suggestions', href: pageLinks.suggestions }] },
            { question: 'How is my information handled?', terms: ['privacy', 'confidential', 'personal information', 'my data'], answer: 'Share only the information needed for your request. Report anonymity is controlled when you submit the report, and case-specific privacy questions should be discussed directly with the GAD Office.', links: [{ label: 'Message the GAD Office', href: pageLinks.messages }] },
            { question: 'What can I do in SINAG?', terms: ['sinag features', 'what can i do', 'how to use sinag', 'system features'], answer: 'SINAG connects GAD resources with campus workflows: submit reports and suggestions, review request status, schedule appointments, and message the GAD Office. Choose a page below to get started.', links: [{ label: 'File a Report', href: pageLinks.report }, { label: 'Schedule', href: pageLinks.schedule }, { label: 'GAD References', href: pageLinks.wellness }, { label: 'Messages', href: pageLinks.messages }] }
        ];

        faqs.forEach(function (faq) {
            const button = document.createElement('button');
            button.type = 'button';
            button.textContent = faq.question;
            button.addEventListener('click', function () { ask(faq.question); });
            questions.appendChild(button);
        });

        function addMessage(text, kind, links) {
            const bubble = document.createElement('div');
            bubble.className = 'gad-faq-message gad-faq-' + kind;
            bubble.textContent = text;
            if (links && links.length) {
                const linkList = document.createElement('div');
                linkList.className = 'gad-faq-answer-links';
                links.forEach(function (link) {
                    const anchor = document.createElement('a');
                    anchor.href = link.href;
                    anchor.textContent = link.label;
                    linkList.appendChild(anchor);
                });
                bubble.appendChild(linkList);
            }
            messages.appendChild(bubble);
            messages.scrollTop = messages.scrollHeight;
        }

        function ask(text) {
            const normalizeFaqText = function (value) {
                return value.toLocaleLowerCase()
                    .normalize('NFKC')
                    .replace(/[^a-z0-9\s-]/g, ' ')
                    .replace(/\s+/g, ' ')
                    .trim();
            };
            const normalized = normalizeFaqText(text);
            if (!normalized) return;
            addMessage(text, 'question');
            const match = faqs
                .map(function (faq) {
                    const terms = [normalizeFaqText(faq.question)].concat(faq.terms.map(normalizeFaqText));
                    let score = terms.reduce(function (best, term) {
                        return normalized.includes(term) ? Math.max(best, term.length) : best;
                    }, 0);

                    if (/(cancel|kansela|kanselahin|cancel ko|i-cancel|reschedule|baguhin)/.test(normalized)
                        && /(appointment|schedule|petsa|oras)/.test(normalized)
                        && (faq.question === 'How can I cancel or edit an appointment?'
                            || faq.question === 'Paano ko ba kanselahin o baguhin ang appointment?')) {
                        score = 100;
                    }
                    if (/paano.*(report|magsumbong)|file.*report|i-report|ireport/.test(normalized)
                        && (faq.question === 'How do I file a report?' || faq.question === 'Paano ako mag-file ng report?')) {
                        score = 100;
                    }
                    if (/paano.*(appointment|schedule)|magpa-appointment|mag book/.test(normalized)
                        && (faq.question === 'How do I book an appointment?' || faq.question === 'Paano mag-book ng appointment?')) {
                        score = 100;
                    }
                    if (/((makipag.*usap)|(magmessage)|(contact)|(kontak)|(message)).*(gad|office)|(gad|office).*(message|contact|office)/.test(normalized)
                        && (faq.question === 'How do I message the GAD Office?' || faq.question === 'Paano makipag-usap sa GAD Office?')) {
                        score = 100;
                    }
                    return {
                        faq: faq,
                        score: score
                    };
                })
                .filter(function (candidate) { return candidate.score > 0; })
                .sort(function (first, second) { return second.score - first.score; })[0]?.faq;
            addMessage(match ? match.answer : 'I could not match that to a SINAG FAQ. For a person-to-person answer, open Messages to contact the GAD Office.', 'answer', match ? match.links : [{ label: 'Message the GAD Office', href: pageLinks.messages }]);
            input.value = '';
        }

        function positionPanel() {
            if (panel.hidden) return;
            const rect = launcher.getBoundingClientRect();
            const maxPanelHeight = Math.min(window.innerHeight - 16, window.innerHeight * (window.innerWidth <= 575 ? .68 : .8));
            const roomAbove = rect.top - 12;
            const roomBelow = window.innerHeight - rect.bottom - 12;
            const availableHeight = Math.max(120, Math.min(maxPanelHeight, Math.max(roomAbove, roomBelow) - 8));
            panel.style.height = availableHeight + 'px';
            panel.style.minHeight = Math.min(272, availableHeight) + 'px';
            const panelHeight = panel.getBoundingClientRect().height;
            widget.classList.toggle('gad-faq-open-below', roomAbove < panelHeight && roomBelow > roomAbove);
            const panelWidth = panel.getBoundingClientRect().width;
            const panelLeft = Math.max(8, Math.min(rect.left, window.innerWidth - panelWidth - 8));
            panel.style.left = (panelLeft - rect.left) + 'px';
        }

        form.addEventListener('submit', function (event) {
            event.preventDefault();
            ask(input.value);
        });
        launcher.addEventListener('click', function () {
            if (launcher.dataset.moved === 'true') {
                delete launcher.dataset.moved;
                return;
            }
            panel.hidden = !panel.hidden;
            launcher.setAttribute('aria-expanded', String(!panel.hidden));
            if (!panel.hidden) {
                positionPanel();
                input.focus();
            } else {
                widget.classList.remove('gad-faq-open-below');
            }
        });
        close.addEventListener('click', function () {
            panel.hidden = true;
            launcher.setAttribute('aria-expanded', 'false');
            launcher.focus();
        });

        const storageKey = 'sinag-gad-faq-position';
        try {
            const saved = JSON.parse(localStorage.getItem(storageKey) || 'null');
            if (saved && Number.isFinite(saved.left) && Number.isFinite(saved.top)) {
                widget.style.left = Math.max(8, Math.min(saved.left, window.innerWidth - widget.offsetWidth - 8)) + 'px';
                widget.style.top = Math.max(8, Math.min(saved.top, window.innerHeight - widget.offsetHeight - 8)) + 'px';
                widget.style.right = 'auto';
                widget.style.bottom = 'auto';
            }
        } catch (error) {
            console.warn('Could not restore the FAQ assistant position.', error);
        }

        function clampWidgetPosition(left, top) {
            const launcherRect = launcher.getBoundingClientRect();
            const minLeft = 8;
            const maxLeft = Math.max(minLeft, window.innerWidth - launcherRect.width - 8);
            const minTop = 8;
            const maxTop = Math.max(minTop, window.innerHeight - launcherRect.height - 8);
            return {
                left: Math.max(minLeft, Math.min(left, maxLeft)),
                top: Math.max(minTop, Math.min(top, maxTop))
            };
        }

        function enableDragging(handle, markMoved) {
            let startX;
            let startY;
            let startLeft;
            let startTop;
            let moved = false;
            handle.addEventListener('pointerdown', function (event) {
                if (event.button !== 0) return;
                const rect = widget.getBoundingClientRect();
                startX = event.clientX;
                startY = event.clientY;
                startLeft = rect.left;
                startTop = rect.top;
                moved = false;
                handle.setPointerCapture(event.pointerId);
            });
            handle.addEventListener('pointermove', function (event) {
                if (!handle.hasPointerCapture(event.pointerId)) return;
                const dx = event.clientX - startX;
                const dy = event.clientY - startY;
                if (Math.abs(dx) + Math.abs(dy) > 5) moved = true;
                if (!moved) return;
                const position = clampWidgetPosition(startLeft + dx, startTop + dy);
                widget.style.left = position.left + 'px';
                widget.style.top = position.top + 'px';
                widget.style.right = 'auto';
                widget.style.bottom = 'auto';
                positionPanel();
                if (markMoved) launcher.dataset.moved = 'true';
            });
            handle.addEventListener('pointerup', function (event) {
                if (handle.hasPointerCapture(event.pointerId)) handle.releasePointerCapture(event.pointerId);
                if (!moved) return;
                try {
                    const rect = widget.getBoundingClientRect();
                    localStorage.setItem(storageKey, JSON.stringify({ left: rect.left, top: rect.top }));
                } catch (error) {
                    console.warn('Could not save the FAQ assistant position.', error);
                }
            });
        }

        enableDragging(launcher, true);
        enableDragging(widget.querySelector('.gad-faq-drag-handle'), false);
        window.addEventListener('resize', function () {
            if (!widget.style.left || !widget.style.top) return;
            const position = clampWidgetPosition(parseFloat(widget.style.left), parseFloat(widget.style.top));
            widget.style.left = position.left + 'px';
            widget.style.top = position.top + 'px';
            positionPanel();
        });
        window.visualViewport?.addEventListener('resize', positionPanel);

        window.setInterval(function () {
            widget.classList.remove('gad-faq-wiggle');
            window.requestAnimationFrame(function () { widget.classList.add('gad-faq-wiggle'); });
        }, 30000);
    })();
</script>
