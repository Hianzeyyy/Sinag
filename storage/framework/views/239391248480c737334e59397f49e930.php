

<?php $__env->startSection('content'); ?>
<style>
    .gad-student * { box-sizing: border-box; }
    .gad-student {
        width: min(100%, 1180px);
        margin-inline: auto;
        padding: clamp(0.5rem, 2vw, 1rem) 0;
    }

    .panel {
        background: #fff;
        border: 1px solid rgba(165, 148, 249, 0.15);
        border-radius: 1.35rem;
        box-shadow: 0 16px 38px rgba(165, 148, 249, 0.08);
        overflow: hidden;
    }

    .cal-header {
        background: linear-gradient(135deg, #8f72f5 0%, #A594F9 100%);
        padding: 1.35rem 1.5rem;
        color: #fff;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        border-radius: 1.35rem 1.35rem 0 0;
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
        margin-bottom: 8px;
        color: #fff;
    }

    .cal-title { font-size: 1.55rem; font-weight: 700; color: #fff; margin-bottom: 3px; }
    .cal-sub   { font-size: 0.83rem; opacity: 0.82; color: #fff; }

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
        flex-shrink: 0;
    }
    .cal-nav-btn:hover { background: rgba(255,255,255,0.32); }

    .cal-body { padding: 1rem 1.25rem 1.35rem; }

    @media (max-width: 767.98px) {
        .cal-header {
            flex-direction: column;
            align-items: stretch;
            padding: 1rem;
        }

        .cal-header > div:last-child {
            justify-content: space-between;
            margin-top: 0 !important;
        }

        .cal-body { padding: 0.65rem; }
        .cal-grid { gap: 3px; }
        .day-name { font-size: 0.58rem; letter-spacing: 0.04em; }
        .cal-day { border-radius: 8px; font-size: 0.72rem; }
        .day-schedule-time { padding: 2px 3px; font-size: clamp(0.46rem, 1.8vw, 0.62rem); }
    }

    .cal-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 7px;
    }

    .day-name {
        text-align: center;
        font-size: 0.72rem;
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
        cursor: default;
    }

    .cal-day:not(.day-empty):hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.07);
    }

    .day-empty       { visibility: hidden; }
    .day-scheduled   { background: rgba(165,148,249,0.10); color: #5b4b9b; border-color: rgba(165,148,249,0.30); }
    .day-available   { background: rgba(16, 185, 129, 0.08); color: #065f46; border-color: rgba(16, 185, 129, 0.32); }
    .day-unavailable { background: rgba(244, 63, 94, 0.08); color: #9f1239; border-color: rgba(244, 63, 94, 0.28); }

    .day-number {
        font-size: 0.95rem;
        line-height: 1;
    }

    .day-schedule-time {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 2px 8px;
        border-radius: 999px;
        font-size: 0.70rem;
        font-weight: 700;
        background: rgba(255,255,255,0.78);
        color: #6b21a8;
        max-width: 92%;
        text-align: center;
        line-height: 1.1;
    }

    .day-schedule-time.badge-available {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }

    .day-schedule-time.badge-unavailable {
        background: #fff1f2;
        color: #be123c;
        border: 1px solid #fecdd3;
    }

    .day-schedule-time.badge-hours {
        background: rgba(255,255,255,0.85);
        color: #6b21a8;
        border: 1px solid rgba(165,148,249,0.35);
    }

    .day-schedule-label {
        font-size: 0.62rem;
        font-weight: 800;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: #8b5cf6;
    }

    .day-schedule-label.label-available {
        color: #059669;
    }

    .day-schedule-label.label-unavailable {
        color: #e11d48;
    }
</style>

<?php
    $schedulePayload = $schedules->map(function($s) {
        $type = 'hours';
        if ($s->status === 'unavailable' || $s->details === 'unavailable') {
            $type = 'unavailable';
        } elseif ($s->details === 'all_day') {
            $type = 'available';
        }

        return [
            'date'              => optional($s->available_from)->format('Y-m-d'),
            'start_time'        => optional($s->available_from)->format('H:i'),
            'end_date'          => $s->available_until ? $s->available_until->format('Y-m-d') : null,
            'end_time'          => $s->available_until ? $s->available_until->format('H:i') : null,
            'status'            => $s->status,
            'availability_type' => $type,
            'details'           => $s->details,
        ];
    })->values();
?>

<div class="gad-student">
    <div class="panel">

        
        <div class="cal-header">
            <div>
                <div class="cal-label">GAD Office availability</div>
                <div class="cal-title" id="monthLabel">Loading…</div>
                <div class="cal-sub">Check the office hours and availability set by the admin for each date.</div>
            </div>
            <div style="display:flex;gap:6px;margin-top:6px;">
                <button class="cal-nav-btn" onclick="changeMonth(-1)" aria-label="Previous month">&#8249;</button>
                <button class="cal-nav-btn" onclick="changeMonth(1)"  aria-label="Next month">&#8250;</button>
            </div>
        </div>

        
        <div class="cal-body">
            <div class="cal-grid" id="calGrid"></div>
        </div>

    </div>
</div>

<script>
const scheduleData = <?php echo json_encode($schedulePayload, 15, 512) ?>;
const scheduleMap = {};

let currentMonth = new Date().getMonth();
let currentYear  = new Date().getFullYear();

function toDateKey(date) {
    return `${date.getFullYear()}-${String(date.getMonth()+1).padStart(2,'0')}-${String(date.getDate()).padStart(2,'0')}`;
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
        if (!schedule.date) return;

        const startDate = dateFromKey(schedule.date);
        const endDate = schedule.end_date ? dateFromKey(schedule.end_date) : startDate;

        for (let cursor = startDate; cursor <= endDate; cursor = addDays(cursor, 1)) {
            const dk = toDateKey(cursor);
            scheduleMap[dk] = {
                type: schedule.availability_type || (schedule.status === 'unavailable' ? 'unavailable' : 'hours'),
                start_time: schedule.start_time || '09:00',
                end_time: schedule.end_time || '17:00',
                status: schedule.status
            };
        }
    });
}

function renderCalendar(month, year) {
    const grid  = document.getElementById('calGrid');
    const label = document.getElementById('monthLabel');
    grid.innerHTML = '';

    const months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    label.textContent = `${months[month]} ${year}`;

    ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'].forEach(n => {
        const el = document.createElement('div');
        el.className = 'day-name';
        el.textContent = n;
        grid.appendChild(el);
    });

    const firstDay    = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    for (let i = 0; i < firstDay; i++) {
        const e = document.createElement('div');
        e.className = 'cal-day day-empty';
        grid.appendChild(e);
    }

    for (let d = 1; d <= daysInMonth; d++) {
        const date  = new Date(year, month, d);
        const dk    = toDateKey(date);
        const schedule = scheduleMap[dk] || null;

        const el = document.createElement('div');
        el.className = 'cal-day';
        el.innerHTML = `<span class="day-number">${d}</span>`;

        if (schedule) {
            const schedType = schedule.type || (schedule.status === 'unavailable' ? 'unavailable' : 'hours');

            if (schedType === 'available') {
                el.classList.add('day-available');
                el.title = 'Office is available all day (8:00 AM – 5:00 PM)';

                const labelEl = document.createElement('small');
                labelEl.className = 'day-schedule-label label-available';
                labelEl.textContent = 'Available';
                el.appendChild(labelEl);

                const timeEl = document.createElement('small');
                timeEl.className = 'day-schedule-time badge-available';
                timeEl.textContent = 'All Day (8 AM - 5 PM)';
                el.appendChild(timeEl);
            } else if (schedType === 'unavailable') {
                el.classList.add('day-unavailable');
                el.title = 'Office is unavailable / closed';

                const labelEl = document.createElement('small');
                labelEl.className = 'day-schedule-label label-unavailable';
                labelEl.textContent = 'Closed';
                el.appendChild(labelEl);

                const timeEl = document.createElement('small');
                timeEl.className = 'day-schedule-time badge-unavailable';
                timeEl.textContent = 'Unavailable';
                el.appendChild(timeEl);
            } else {
                el.classList.add('day-scheduled');
                el.title = `Available ${formatTimeLabel(schedule.start_time)} to ${formatTimeLabel(schedule.end_time)}`;

                const labelEl = document.createElement('small');
                labelEl.className = 'day-schedule-label';
                labelEl.textContent = 'Available';
                el.appendChild(labelEl);

                const timeEl = document.createElement('small');
                timeEl.className = 'day-schedule-time badge-hours';
                timeEl.textContent = `${formatTimeLabel(schedule.start_time)} - ${formatTimeLabel(schedule.end_time)}`;
                el.appendChild(timeEl);
            }
        }

        grid.appendChild(el);
    }
}

function changeMonth(diff) {
    currentMonth += diff;
    if (currentMonth > 11) { currentMonth = 0; currentYear++; }
    else if (currentMonth < 0) { currentMonth = 11; currentYear--; }
    renderCalendar(currentMonth, currentYear);
}

document.addEventListener('DOMContentLoaded', () => {
    buildScheduleMap();
    renderCalendar(currentMonth, currentYear);
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sinag\resources\views/student/gad_schedule.blade.php ENDPATH**/ ?>