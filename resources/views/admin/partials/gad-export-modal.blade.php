<div class="modal fade" id="exportGadFormatModal" tabindex="-1" aria-labelledby="exportGadFormatModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0">
                <div>
                    <h2 class="modal-title h5 fw-bold mb-1" id="exportGadFormatModalLabel">Choose export sections</h2>
                    <p class="small text-muted mb-0">Preview the generated PDF or Excel workbook before downloading.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="exportGadReportForm" method="GET" data-export-url-template="{{ route('admin.gad-report.export', ['format' => '__FORMAT__', 'period' => '__PERIOD__']) }}">
                <div class="modal-body pt-0" id="exportGadDataStep">
                    <label class="export-section-choice mb-2"><input type="checkbox" name="include_reports" value="1" checked><span>Incident reports and suggestions</span></label>
                    <label class="export-section-choice"><input type="checkbox" name="include_feedback" value="1" checked><span>Customer feedback and surveys</span></label>
                    <div class="small text-danger mt-2 d-none" id="exportGadDataError">Select at least one section.</div>
                </div>
                <div class="modal-body pt-0 d-none" id="exportGadFormatStep" aria-hidden="true">
                    <label for="exportGadReportFormat" class="form-label fw-semibold">File format</label>
                    <select id="exportGadReportFormat" class="form-select">
                        <option value="pdf">PDF</option>
                        <option value="excel">Excel workbook (.xlsx)</option>
                    </select>
                </div>
                <div class="modal-body pt-0 d-none" id="exportGadPeriodStep" aria-hidden="true">
                    <label for="exportGadReportPeriod" class="form-label fw-semibold">Date range</label>
                    <select id="exportGadReportPeriod" class="form-select" required>
                        <option value="30days">Last 30 days</option><option value="7days">Last 7 days</option>
                        <option value="q1">Quarter 1 (Jan–Mar)</option><option value="q2">Quarter 2 (Apr–Jun)</option>
                        <option value="q3">Quarter 3 (Jul–Sep)</option><option value="q4">Quarter 4 (Oct–Dec)</option>
                        <option value="midyear">Mid-year (Jan–Jun)</option><option value="annual">Annual (Jan–Dec)</option><option value="lastyear">Last year</option>
                    </select>
                </div>
                <div class="modal-body pt-0 d-none" id="exportGadPreviewStep" aria-hidden="true">
                    <p class="small text-muted mb-2" id="exportPreviewDescription">Loading the generated file preview…</p>
                    <iframe id="exportGadFilePreview" class="export-file-preview" title="Preview of the generated GAD export file"></iframe>
                </div>
                <div class="modal-footer border-0" id="exportGadDataActions">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="continueGadData" class="btn dashboard-export-button rounded-pill px-4">Continue</button>
                </div>
                <div class="modal-footer border-0 d-none" id="exportGadFormatActions" aria-hidden="true">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="continueGadExport" class="btn dashboard-export-button rounded-pill px-4">Continue</button>
                </div>
                <div class="modal-footer border-0 d-none" id="exportGadPeriodActions" aria-hidden="true">
                    <button type="button" id="backToGadExportFormat" class="btn btn-light rounded-pill px-4">Back</button>
                    <button type="button" id="previewGadExport" class="btn dashboard-export-button rounded-pill px-4">Preview export</button>
                </div>
                <div class="modal-footer border-0 d-none" id="exportGadPreviewActions" aria-hidden="true">
                    <button type="button" id="backToGadExportPeriod" class="btn btn-light rounded-pill px-4">Back</button>
                    <button type="submit" class="btn dashboard-export-button rounded-pill px-4"><i class="bi bi-download me-1"></i>Export now</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('exportGadFormatModal');
        const form = document.getElementById('exportGadReportForm');
        if (!modal || !form) return;

        const steps = {
            data: document.getElementById('exportGadDataStep'),
            format: document.getElementById('exportGadFormatStep'),
            period: document.getElementById('exportGadPeriodStep'),
            preview: document.getElementById('exportGadPreviewStep'),
        };
        const actions = {
            data: document.getElementById('exportGadDataActions'),
            format: document.getElementById('exportGadFormatActions'),
            period: document.getElementById('exportGadPeriodActions'),
            preview: document.getElementById('exportGadPreviewActions'),
        };
        const titles = { data: 'Choose export sections', format: 'Choose file format', period: 'Choose date range', preview: 'Preview export' };
        const title = document.getElementById('exportGadFormatModalLabel');
        const previewFrame = document.getElementById('exportGadFilePreview');
        const previewDescription = document.getElementById('exportPreviewDescription');
        let currentPreviewDescription = '';

        function showStep(current) {
            Object.keys(steps).forEach(function (name) {
                const active = name === current;
                steps[name].classList.toggle('d-none', !active);
                steps[name].setAttribute('aria-hidden', String(!active));
                actions[name].classList.toggle('d-none', !active);
                actions[name].setAttribute('aria-hidden', String(!active));
            });
            title.textContent = titles[current];
        }

        showStep('data');
        document.getElementById('continueGadData').addEventListener('click', function () {
            const selectedCount = form.querySelectorAll('#exportGadDataStep input:checked').length;
            document.getElementById('exportGadDataError').classList.toggle('d-none', selectedCount > 0);
            if (selectedCount > 0) showStep('format');
        });
        document.getElementById('continueGadExport').addEventListener('click', function () { showStep('period'); });
        document.getElementById('backToGadExportFormat').addEventListener('click', function () { showStep('format'); });
        document.getElementById('backToGadExportPeriod').addEventListener('click', function () { showStep('period'); });
        document.getElementById('previewGadExport').addEventListener('click', function () {
            const selected = Array.from(form.querySelectorAll('#exportGadDataStep input:checked'))
                .map((input) => input.name === 'include_reports' ? 'Reports & suggestions' : 'Customer feedback');
            const format = document.getElementById('exportGadReportFormat');
            const period = document.getElementById('exportGadReportPeriod');
            const previewUrl = form.dataset.exportUrlTemplate
                .replace('__FORMAT__', encodeURIComponent(format.value))
                .replace('__PERIOD__', encodeURIComponent(period.value));
            const params = new URLSearchParams();
            form.querySelectorAll('#exportGadDataStep input:checked').forEach((input) => params.append(input.name, input.value));
            params.set('preview', '1');
            currentPreviewDescription = `${format.options[format.selectedIndex].text} · ${period.options[period.selectedIndex].text} · ${selected.join(', ')}`;
            previewDescription.textContent = `Generating preview: ${currentPreviewDescription}`;
            previewFrame.src = `${previewUrl}?${params.toString()}`;
            showStep('preview');
        });
        previewFrame.addEventListener('load', function () {
            if (previewFrame.src !== 'about:blank') previewDescription.textContent = `Preview ready: ${currentPreviewDescription}`;
        });
        form.addEventListener('submit', function () {
            const format = document.getElementById('exportGadReportFormat').value;
            const period = document.getElementById('exportGadReportPeriod').value;
            form.action = form.dataset.exportUrlTemplate
                .replace('__FORMAT__', encodeURIComponent(format))
                .replace('__PERIOD__', encodeURIComponent(period));
        });
        modal.addEventListener('hidden.bs.modal', function () {
            showStep('data');
            document.getElementById('exportGadDataError').classList.add('d-none');
            previewFrame.src = 'about:blank';
        });
    });
</script>
