@extends('layouts.app')

@section('content')
<style>
    .gad-page * { box-sizing: border-box; }
    .gad-page { padding: clamp(0.5rem, 2vw, 1rem) 0; }

    .panel {
        background: #fff;
        border: 1px solid rgba(165, 148, 249, 0.15);
        border-radius: 1.35rem;
        box-shadow: 0 16px 38px rgba(165, 148, 249, 0.08);
        overflow: hidden;
    }

    .cal-header {
        background: linear-gradient(135deg, #8f72f5 0%, #A594F9 100%);
        padding: clamp(1rem, 3vw, 1.35rem) clamp(1rem, 4vw, 1.5rem);
        color: #fff;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 0.75rem;
    }

    .cal-label {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        background: rgba(255,255,255,0.18);
        display: inline-block;
        padding: 2px 10px;
        border-radius: 999px;
        margin-bottom: 6px;
        color: #fff;
    }

    .cal-title { font-size: 1.45rem; font-weight: 700; color: #fff; margin-bottom: 2px; }
    .cal-sub   { font-size: 0.82rem; opacity: 0.82; color: #fff; }

    .cal-nav-btn {
        background: rgba(255,255,255,0.18);
        border: none;
        color: #fff;
        width: 36px;
        height: 36px;
        border-radius: 0.65rem;
        cursor: pointer;
        font-size: 1.1rem;
        transition: background 0.15s;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .cal-nav-btn:hover { background: rgba(255,255,255,0.32); }

    .cal-body { padding: clamp(0.7rem, 2.5vw, 1rem) clamp(0.65rem, 3vw, 1.25rem) clamp(0.85rem, 3vw, 1.25rem); }

    .cal-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: clamp(3px, 0.7vw, 7px);
    }

    .day-name {
        text-align: center;
        font-size: 0.7rem;
        font-weight: 700;
        color: #94a3b8;
        padding: 0.38rem 0;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .cal-day {
        aspect-ratio: 1 / 1;
        border-radius: 11px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
        font-size: 0.84rem;
        font-weight: 700;
        border: 1px solid #f1f5f9;
        background: #fff;
        color: #111827;
        transition: transform 0.12s, box-shadow 0.12s;
        user-select: none;
    }

    .cal-day.clickable          { cursor: pointer; }
    .cal-day.clickable:hover    { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(0,0,0,0.08); }
    .cal-day.day-empty          { visibility: hidden; }
    .cal-day.day-scheduled      { background: rgba(165,148,249,0.10); color: #5b4b9b; border-color: rgba(165,148,249,0.30); }
    .cal-day.day-modified       { outline: 2px solid #8f72f5; outline-offset: 1px; }

    .day-schedule-time {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 2px 8px;
        border-radius: 999px;
        font-size: 0.68rem;
        font-weight: 700;
        background: rgba(255,255,255,0.78);
        color: #6b21a8;
        max-width: 92%;
        text-align: center;
        line-height: 1.1;
    }

    .save-bar {
        padding: 0.75rem 1.25rem 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        border-top: 1px solid rgba(229,231,235,0.7);
    }

    .save-bar-note { font-size: 0.8rem; color: #64748b; }

    .save-cal-btn {
        background: linear-gradient(135deg, #8f72f5 0%, #A594F9 100%);
        color: #fff;
        border: none;
        padding: 0.55rem 1.5rem;
        border-radius: 999px;
        font-size: 0.84rem;
        font-weight: 700;
        cursor: pointer;
        transition: opacity 0.18s;
    }
    .save-cal-btn:hover    { opacity: 0.88; }
    .save-cal-btn:disabled { opacity: 0.45; cursor: default; }

    .alert { padding: 0.85rem 1rem; border-radius: 1rem; margin-bottom: 1rem; font-size: 0.88rem; }
    .alert-success { background: #f3efff; color: #5b4b9b; border: 1px solid #d8cdfc; }

    .schedule-modal {
        position: fixed;
        inset: 0;
        z-index: 1055;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        background: rgba(15, 23, 42, 0.52);
        backdrop-filter: blur(5px);
    }

    .schedule-modal.is-open { display: flex; }

    .schedule-modal-card {
        width: min(100%, 430px);
        background: #fff;
        border-radius: 1.25rem;
        box-shadow: 0 24px 70px rgba(15, 23, 42, 0.25);
        overflow: hidden;
        animation: scheduleModalIn 0.18s ease-out;
    }

    .schedule-modal-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        padding: 1.25rem 1.35rem;
        color: #fff;
        background: linear-gradient(135deg, #7658df 0%, #a594f9 100%);
    }

    .schedule-modal-kicker {
        margin-bottom: 0.25rem;
        font-size: 0.7rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        opacity: 0.82;
    }

    .schedule-modal-title { margin: 0; font-size: 1.25rem; font-weight: 800; }

    .schedule-modal-close {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2rem;
        height: 2rem;
        border: 0;
        border-radius: 50%;
        color: #fff;
        background: rgba(255, 255, 255, 0.16);
        font-size: 1.35rem;
        line-height: 1;
        cursor: pointer;
    }

    .schedule-modal-body { padding: 1.35rem; }

    .schedule-time-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.85rem;
    }

    .schedule-time-label {
        display: block;
        margin-bottom: 0.4rem;
        color: #475569;
        font-size: 0.78rem;
        font-weight: 800;
    }

    .schedule-time-input {
        width: 100%;
        padding: 0.7rem 0.75rem;
        border: 1px solid #dbe2ea;
        border-radius: 0.7rem;
        color: #1e293b;
        background: #f8fafc;
        font-size: 1rem;
    }

    .schedule-time-input:focus {
        outline: none;
        border-color: #8f72f5;
        box-shadow: 0 0 0 0.2rem rgba(165, 148, 249, 0.2);
        background: #fff;
    }

    .schedule-modal-error {
        min-height: 1.25rem;
        margin-top: 0.75rem;
        color: #7658df;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .schedule-modal-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        margin-top: 1.15rem;
    }

    .schedule-remove-btn,
    .schedule-modal-action {
        border: 0;
        border-radius: 0.7rem;
        padding: 0.65rem 0.95rem;
        font-size: 0.84rem;
        font-weight: 800;
        cursor: pointer;
    }

    .schedule-remove-btn { color: #5b4b9b; background: #f3efff; }
    .schedule-modal-action.cancel { color: #475569; background: #f1f5f9; }
    .schedule-modal-action.save { color: #fff; background: #8f72f5; }
    .schedule-modal-action.save:hover { background: #7658df; }

    @keyframes scheduleModalIn {
        from { opacity: 0; transform: translateY(8px) scale(0.98); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    @media (max-width: 480px) {
        .cal-header { align-items: stretch; flex-direction: column; }
        .cal-nav-btn { width: 42px; height: 38px; }
        .cal-header > div:last-child { justify-content: space-between; margin-top: 0 !important; }
        .cal-title { font-size: 1.25rem; }
        .cal-sub { max-width: 25rem; }
        .day-name { font-size: 0.58rem; letter-spacing: 0.04em; }
        .cal-day { min-width: 0; border-radius: 8px; font-size: 0.75rem; gap: 0.2rem; }
        .day-schedule-time { padding: 2px 3px; font-size: clamp(0.45rem, 1.8vw, 0.62rem); white-space: normal; overflow-wrap: anywhere; }
        .save-bar { align-items: stretch; flex-direction: column; padding-inline: 0.85rem; }
        .save-bar-note { line-height: 1.4; }
        .save-cal-btn { width: 100%; }
        .schedule-time-grid { grid-template-columns: 1fr; }
        .schedule-modal-actions { align-items: stretch; flex-direction: column-reverse; }
        .schedule-remove-btn,
        .schedule-modal-action { width: 100%; }
    }
</style>

<div class="gad-page">

    @if(session('success'))
        <div class="alert alert-success">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        </div>
    @endif

    <div class="panel">

        {{-- Header --}}
        <div class="cal-header">
            <div>
                <div class="cal-label">Admin calendar</div>
                <div class="cal-title" id="adminMonthLabel">Loading…</div>
                <div class="cal-sub">Click any day to set office hours for that date.</div>
            </div>
            <div style="display:flex;gap:6px;margin-top:4px;">
                <button class="cal-nav-btn" onclick="adminNav(-1)">&#8249;</button>
                <button class="cal-nav-btn" onclick="adminNav(1)">&#8250;</button>
            </div>
        </div>

        {{-- Calendar grid --}}
        <div class="cal-body">
            <div class="cal-grid" id="adminCalGrid"></div>
        </div>

        {{-- Save bar --}}
        <div class="save-bar">
            <div class="save-bar-note" id="adminChangeNote">No changes yet. Click a day to set office hours.</div>
            <button class="save-cal-btn" id="saveCalBtn" onclick="saveCalendar()" disabled>Save changes</button>
        </div>

    </div>
</div>

<div class="schedule-modal" id="scheduleModal" role="dialog" aria-modal="true" aria-labelledby="scheduleModalTitle" aria-hidden="true">
    <div class="schedule-modal-card">
        <div class="schedule-modal-head">
            <div>
                <div class="schedule-modal-kicker">Office hours</div>
                <h2 class="schedule-modal-title" id="scheduleModalTitle">Set schedule</h2>
            </div>
            <button type="button" class="schedule-modal-close" id="scheduleModalClose" aria-label="Close">&times;</button>
        </div>
        <div class="schedule-modal-body">
            <div class="schedule-time-grid">
                <div>
                    <label class="schedule-time-label" for="scheduleStartTime">Start time</label>
                    <input class="schedule-time-input" id="scheduleStartTime" type="time" step="900">
                </div>
                <div>
                    <label class="schedule-time-label" for="scheduleEndTime">End time</label>
                    <input class="schedule-time-input" id="scheduleEndTime" type="time" step="900">
                </div>
            </div>
            <div class="schedule-modal-error" id="scheduleModalError" role="alert"></div>
            <div class="schedule-modal-actions">
                <button type="button" class="schedule-remove-btn" id="scheduleRemoveBtn"><i class="bi bi-trash3 me-1"></i>Remove hours</button>
                <div class="d-flex gap-2">
                    <button type="button" class="schedule-modal-action cancel" id="scheduleCancelBtn">Cancel</button>
                    <button type="button" class="schedule-modal-action save" id="scheduleSaveBtn"><i class="bi bi-check2 me-1"></i>Save day</button>
                </div>
            </div>
        </div>
    </div>
</div>

@php
    $adminPayload = $schedules->map(fn($s) => [
        'id'              => $s->id,
        'date'            => optional($s->available_from)->format('Y-m-d'),
        'start_time'      => optional($s->available_from)->format('H:i'),
        'end_date'        => $s->available_until ? $s->available_until->format('Y-m-d') : null,
        'end_time'        => $s->available_until ? $s->available_until->format('H:i') : null,
        'status'          => $s->status,
    ])->values();
@endphp

<script>
const scheduleData = @json($adminPayload);
const scheduleMap = {};

let adminMonth = new Date().getMonth();
let adminYear  = new Date().getFullYear();
const modifiedDays = {};

function dateKey(y, m, d) {
    return `${y}-${String(m+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
}

function dateFromKey(dk) {
    const [year, month, day] = dk.split('-').map(Number);
    return new Date(year, month - 1, day);
}

function addDays(date, amount) {
    const next = new Date(date);
    next.setDate(next.getDate() + amount);
    return next;
}

function formatTimeLabel(timeValue) {
    if (!timeValue) return '';

    const [hours, minutes] = timeValue.split(':').map(Number);
    const suffix = hours >= 12 ? 'PM' : 'AM';
    const displayHours = hours % 12 || 12;
    return `${displayHours}:${String(minutes).padStart(2, '0')} ${suffix}`;
}

function buildScheduleMap() {
    Object.keys(scheduleMap).forEach(key => delete scheduleMap[key]);

    scheduleData.forEach(schedule => {
        if (schedule.status !== 'active' || !schedule.date || !schedule.start_time) return;

        const startDate = dateFromKey(schedule.date);
        const endDate = schedule.end_date ? dateFromKey(schedule.end_date) : startDate;

        for (let cursor = startDate; cursor <= endDate; cursor = addDays(cursor, 1)) {
            const dk = dateKey(cursor.getFullYear(), cursor.getMonth(), cursor.getDate());
            scheduleMap[dk] = {
                start_time: schedule.start_time,
                end_time: schedule.end_time,
            };
        }
    });
}

function getEffectiveSchedule(dk) {
    if (dk in modifiedDays) {
        return modifiedDays[dk].remove ? null : modifiedDays[dk];
    }

    return scheduleMap[dk] || null;
}

let activeScheduleDate = null;

function closeScheduleModal() {
    const modal = document.getElementById('scheduleModal');
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
    activeScheduleDate = null;
}

function openScheduleModal(dk) {
    const current = getEffectiveSchedule(dk);
    activeScheduleDate = dk;
    document.getElementById('scheduleModalTitle').textContent = `Office hours for ${formatDateLabel(dk)}`;
    document.getElementById('scheduleStartTime').value = current ? current.start_time : '09:00';
    document.getElementById('scheduleEndTime').value = current ? current.end_time : '17:00';
    document.getElementById('scheduleModalError').textContent = '';
    const modal = document.getElementById('scheduleModal');
    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
    document.getElementById('scheduleStartTime').focus();
}

function formatDateLabel(dk) {
    return dateFromKey(dk).toLocaleDateString(undefined, { month: 'long', day: 'numeric', year: 'numeric' });
}

function saveScheduleModal() {
    const startTime = document.getElementById('scheduleStartTime').value;
    const endTime = document.getElementById('scheduleEndTime').value;
    const error = document.getElementById('scheduleModalError');

    if (!startTime || !endTime) {
        error.textContent = 'Please select both a start and end time.';
        return;
    }

    if (startTime >= endTime) {
        error.textContent = 'End time must be later than the start time.';
        return;
    }

    modifiedDays[activeScheduleDate] = { start_time: startTime, end_time: endTime, remove: false };
    closeScheduleModal();
    updateChangeNote();
    renderAdminCalendar();
}

function removeScheduleModal() {
    modifiedDays[activeScheduleDate] = { remove: true };
    closeScheduleModal();
    updateChangeNote();
    renderAdminCalendar();
}

function renderAdminCalendar() {
    const grid  = document.getElementById('adminCalGrid');
    const label = document.getElementById('adminMonthLabel');
    grid.innerHTML = '';

    const months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    label.textContent = `${months[adminMonth]} ${adminYear}`;

    ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'].forEach(n => {
        const el = document.createElement('div');
        el.className = 'day-name';
        el.textContent = n;
        grid.appendChild(el);
    });

    const firstDay    = new Date(adminYear, adminMonth, 1).getDay();
    const daysInMonth = new Date(adminYear, adminMonth + 1, 0).getDate();

    for (let i = 0; i < firstDay; i++) {
        const e = document.createElement('div');
        e.className = 'cal-day day-empty';
        grid.appendChild(e);
    }

    for (let d = 1; d <= daysInMonth; d++) {
        const dk    = dateKey(adminYear, adminMonth, d);
        const schedule = getEffectiveSchedule(dk);

        const el = document.createElement('div');
        el.className = 'cal-day';
        el.classList.add('clickable');
        if (dk in modifiedDays) el.classList.add('day-modified');
        if (schedule) el.classList.add('day-scheduled');

        el.innerHTML = `<span>${d}</span>`;

        if (schedule) {
            const timeText = `${formatTimeLabel(schedule.start_time)} - ${formatTimeLabel(schedule.end_time)}`;
            const timeEl = document.createElement('small');
            timeEl.className = 'day-schedule-time';
            timeEl.textContent = timeText;
            el.appendChild(timeEl);
        }

        el.addEventListener('click', () => {
            openScheduleModal(dk);
        });

        grid.appendChild(el);
    }
}

function updateChangeNote() {
    const count = Object.keys(modifiedDays).length;
    const note  = document.getElementById('adminChangeNote');
    const btn   = document.getElementById('saveCalBtn');
    if (count === 0) {
        note.textContent = 'No changes yet. Click a day to set office hours.';
        btn.disabled = true;
    } else {
        note.textContent = `${count} day${count > 1 ? 's' : ''} modified. Save to apply.`;
        btn.disabled = false;
    }
}

function adminNav(diff) {
    adminMonth += diff;
    if (adminMonth > 11) { adminMonth = 0; adminYear++; }
    else if (adminMonth < 0) { adminMonth = 11; adminYear--; }
    renderAdminCalendar();
}

function saveCalendar() {
    const updates = Object.entries(modifiedDays).map(([date, entry]) => ({
        date,
        start_time: entry.remove ? null : entry.start_time,
        end_time: entry.remove ? null : entry.end_time,
        remove: !!entry.remove,
    }));
    if (!updates.length) return;

    const btn = document.getElementById('saveCalBtn');
    btn.disabled = true;
    btn.textContent = 'Saving…';

    fetch('{{ route('admin.gad-schedules.sync') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ updates })
    })
    .then(res => {
        if (res.ok) {
            window.location.reload();
        } else {
            alert('Save failed. Please try again.');
            btn.disabled = false;
            btn.textContent = 'Save changes';
        }
    })
    .catch(() => {
        alert('Network error. Please try again.');
        btn.disabled = false;
        btn.textContent = 'Save changes';
    });
}

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('scheduleModalClose').addEventListener('click', closeScheduleModal);
    document.getElementById('scheduleCancelBtn').addEventListener('click', closeScheduleModal);
    document.getElementById('scheduleSaveBtn').addEventListener('click', saveScheduleModal);
    document.getElementById('scheduleRemoveBtn').addEventListener('click', removeScheduleModal);
    document.getElementById('scheduleModal').addEventListener('click', event => {
        if (event.target.id === 'scheduleModal') closeScheduleModal();
    });
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape') closeScheduleModal();
    });
    buildScheduleMap();
    renderAdminCalendar();
    updateChangeNote();
});
</script>
@endsection