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
        <span class="gad-faq-launcher-icon" aria-hidden="true"><i class="bi bi-chat-heart-fill"></i></span>
        <span class="gad-faq-launcher-label">GAD Guide</span>
        <span class="gad-faq-launcher-grip" aria-hidden="true"><i class="bi bi-arrows-move"></i></span>
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
        gap: .45rem;
        padding: .4rem .65rem .4rem .4rem;
        border: 1px solid rgba(255,255,255,.75);
        border-radius: 999px;
        color: #fff;
        background: linear-gradient(135deg, #8f72f5, #b99cff 65%, #ed8aba);
        box-shadow: 0 12px 30px rgba(76, 29, 149, .28);
        font-weight: 900;
        cursor: grab;
        touch-action: none;
        transition: transform .22s ease, box-shadow .22s ease, filter .22s ease;
    }

    .gad-faq-launcher:hover,
    .gad-faq-launcher:focus-visible {
        transform: translateY(-4px) scale(1.04);
        box-shadow: 0 16px 34px rgba(76, 29, 149, .36);
        filter: saturate(1.1);
    }

    .gad-faq-launcher:active { cursor: grabbing; }
    .gad-faq-launcher-icon { display: grid; width: 2.25rem; height: 2.25rem; place-items: center; border-radius: 50%; background: rgba(255,255,255,.22); font-size: 1.05rem; }
    .gad-faq-launcher-label { white-space: nowrap; }
    .gad-faq-launcher-grip { display: grid; width: 1.25rem; height: 1.25rem; place-items: center; border-radius: 50%; background: rgba(255,255,255,.17); font-size: .68rem; }

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
    .gad-faq-answer-links a:hover, .gad-faq-answer-links a:focus-visible { border-color: #a594f9; background: #ebe5ff; text-decoration: underline; }
    .gad-faq-quick-questions { display: flex; flex-wrap: wrap; gap: .4rem; }
    .gad-faq-quick-questions button { padding: .38rem .58rem; border: 1px solid #d9ceff; border-radius: 999px; color: #6148b1; background: #f6f3ff; font-size: .72rem; font-weight: 800; transition: transform .18s ease, background .18s ease; }
    .gad-faq-quick-questions button:hover { transform: translateY(-2px); background: #ebe5ff; }
    .gad-faq-input { display: flex; gap: .5rem; padding: .7rem; border-top: 1px solid #f0ebff; }
    .gad-faq-input input { min-width: 0; flex: 1; padding: .65rem .75rem; border: 1px solid #e5def7; border-radius: .7rem; outline: none; }
    .gad-faq-input input:focus { border-color: #a594f9; box-shadow: 0 0 0 3px rgba(165,148,249,.18); }
    .gad-faq-input button { width: 2.65rem; border: 0; border-radius: .7rem; color: #fff; background: #8f72f5; }
    .gad-faq-footnote { flex: 0 0 auto; padding: 0 .8rem .7rem; color: #817b91; text-align: center; font-size: .67rem; }

    @media (max-width: 575.98px) {
        .gad-faq-widget { right: .65rem; bottom: max(.65rem, env(safe-area-inset-bottom)); }
        .gad-faq-launcher { gap: .35rem; padding: .32rem .5rem .32rem .32rem; font-size: .82rem; }
        .gad-faq-launcher-icon { width: 1.95rem; height: 1.95rem; font-size: .95rem; }
        .gad-faq-launcher-grip { width: 1.1rem; height: 1.1rem; font-size: .62rem; }
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
            schedule: @json(route('student.schedule-appointment')),
            appointments: @json(route('student.appointments')),
            messages: @json(route('student.messaging')),
            wellness: @json(route('student.wellness')),
            ra9262: @json(route('student.reference', 'ra9262')),
            ra8353: @json(route('student.reference', 'ra8353')),
            ra9208: @json(route('student.reference', 'ra9208')),
            ra9710: @json(route('student.reference', 'ra9710')),
            hivaids: @json(route('student.reference', 'hivaids')),
            antiHarassment: @json(route('student.reference', 'antisexualharassmentpolicy')),
            dashboard: @json(route('student.dashboard'))
        };
        const faqs = [
            { question: 'What is GAD and what does the office do?', terms: ['what is gad', 'gad office', 'gender and development', 'gad services'], answer: 'Gender and Development (GAD) works to promote gender equality, dignity, inclusion, and safe spaces. The campus GAD Office can receive concerns, guide students and employees to support, and share awareness and policy information. This FAQ is general information, not legal advice.', links: [{ label: 'Open GAD references', href: pageLinks.wellness }] },
            { question: 'How do I file a report?', terms: ['report', 'complaint', 'file', 'harassment report'], answer: 'To submit a concern, open File a Report, complete the incident details, and submit. Keep your report description factual and include only information relevant to the incident. After submission, open Report Status to review updates. If you are in immediate danger, contact campus security or local emergency services first.', links: [{ label: 'Go to File a Report', href: pageLinks.report }, { label: 'Check Report Status', href: pageLinks.reportStatus }] },
            { question: 'How do I book an appointment?', terms: ['appointment', 'schedule', 'book', 'face to face', 'online meeting'], answer: 'Open Schedule, choose a date and time, select Face-to-Face or Online, then submit your concern. The request stays pending until reviewed by the GAD Office. You can see its status, edit a pending request, or cancel it under My Appointments.', links: [{ label: 'Schedule an appointment', href: pageLinks.schedule }, { label: 'My Appointments', href: pageLinks.appointments }] },
            { question: 'How can I contact the GAD Office?', terms: ['contact', 'message', 'talk', 'support', 'help'], answer: 'Use Messages to send a private message to the GAD Office. Include your appointment or report reference if your question is about an existing submission. Messages are not an emergency service; for immediate danger, contact campus security or local emergency services.', links: [{ label: 'Message the GAD Office', href: pageLinks.messages }] },
            { question: 'What is Republic Act 9262?', terms: ['ra 9262', 'ra9262', 'vawc', 'violence against women', 'violence against women and children'], answer: 'Republic Act 9262, or the Anti-Violence Against Women and Their Children Act, provides protections and remedies for covered acts of physical, sexual, psychological, and economic abuse in specified intimate or family relationships. A qualified support provider or lawyer can explain how the law applies to an individual situation.', links: [{ label: 'Read the RA 9262 reference', href: pageLinks.ra9262 }] },
            { question: 'What is Republic Act 8353?', terms: ['ra 8353', 'ra8353', 'anti rape law', 'rape law'], answer: 'Republic Act 8353, the Anti-Rape Law of 1997, amended the legal treatment and definition of rape in the Philippines. This is a brief educational summary and not legal advice. Survivors may choose to seek confidential support from the GAD Office or an appropriate professional.', links: [{ label: 'Read the RA 8353 reference', href: pageLinks.ra8353 }] },
            { question: 'What is Republic Act 9208?', terms: ['ra 9208', 'ra9208', 'anti trafficking', 'human trafficking'], answer: 'Republic Act 9208, as amended, is the Anti-Trafficking in Persons law. It addresses trafficking offenses and provides a framework for prevention, victim protection, and prosecution. Contact qualified authorities or support services for help with a specific situation.', links: [{ label: 'Read the RA 9208 reference', href: pageLinks.ra9208 }] },
            { question: 'What is Republic Act 9710?', terms: ['ra 9710', 'ra9710', 'magna carta of women'], answer: 'Republic Act 9710, the Magna Carta of Women, affirms women’s human rights and promotes substantive equality and non-discrimination. It guides institutions in supporting women’s rights and access to services.', links: [{ label: 'Read the RA 9710 reference', href: pageLinks.ra9710 }] },
            { question: 'What is the Safe Spaces Act (RA 11313)?', terms: ['ra 11313', 'ra11313', 'safe spaces act', 'bawal bastos'], answer: 'Republic Act 11313, the Safe Spaces Act, addresses gender-based sexual harassment in public spaces, online spaces, workplaces, and educational or training institutions. The SINAG References section has campus-accessible GAD information; use File a Report if you need to submit a concern.', links: [{ label: 'Browse GAD references', href: pageLinks.wellness }, { label: 'File a report', href: pageLinks.report }] },
            { question: 'Where can I read about sexual harassment policy?', terms: ['sexual harassment policy', 'anti sexual harassment', 'harassment policy'], answer: 'The SINAG reference page summarizes the institutional anti-sexual-harassment policy, expected respectful conduct, reporting routes, and support options. Consult the official university policy for authoritative and current rules.', links: [{ label: 'Read the policy reference', href: pageLinks.antiHarassment }] },
            { question: 'Where can I learn about HIV/AIDS?', terms: ['hiv', 'aids', 'hiv/aids'], answer: 'The SINAG reference shares basic, stigma-free information about prevention, care, and support. For personal medical guidance, consult a qualified healthcare provider or appropriate campus health service.', links: [{ label: 'Read the HIV/AIDS reference', href: pageLinks.hivaids }] },
            { question: 'How is my information handled?', terms: ['privacy', 'private', 'confidential', 'data'], answer: 'Share only details needed to request assistance or submit a report. SINAG limits access to signed-in account areas, but this chatbot is a general FAQ and cannot assess confidentiality for a specific case. Contact the GAD Office directly for case-specific privacy questions.', links: [{ label: 'Message the GAD Office', href: pageLinks.messages }] },
            { question: 'Can I edit or cancel an appointment?', terms: ['edit appointment', 'cancel appointment', 'change appointment'], answer: 'A pending appointment can be edited or cancelled from My Appointments. After you cancel, the record is marked as cancelled; cancelled or rejected records can be deleted from your list. For approved or completed requests, contact the GAD Office.', links: [{ label: 'Open My Appointments', href: pageLinks.appointments }] },
            { question: 'What can I do in SINAG?', terms: ['sinag', 'system', 'platform', 'how to use'], answer: 'SINAG brings GAD resources and campus workflows together: submit a report, review report status, request an appointment, message the GAD Office, read announcements, and send suggestions. Choose a page below to get started.', links: [{ label: 'File a Report', href: pageLinks.report }, { label: 'Schedule', href: pageLinks.schedule }, { label: 'GAD References', href: pageLinks.wellness }, { label: 'Dashboard', href: pageLinks.dashboard }] }
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
            const normalized = text.toLocaleLowerCase().trim();
            if (!normalized) return;
            addMessage(text, 'question');
            const match = faqs.find(function (faq) {
                return normalized === faq.question.toLocaleLowerCase()
                    || faq.terms.some(function (term) { return normalized.includes(term); });
            });
            addMessage(match ? match.answer : 'I can answer FAQs only about GAD and SINAG. Ask about filing a report, appointments, messages, privacy, or a reference such as RA 9262, RA 8353, RA 9208, RA 9710, or RA 11313.', 'answer', match ? match.links : null);
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
