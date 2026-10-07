

<?php $__env->startSection('content'); ?>
<style>
    body {
        background: linear-gradient(135deg, #f3e8ff 0%, #f8fafc 100%) !important;
    }
    .report-glass {
        background: rgba(255,255,255,0.85);
        box-shadow: 0 8px 40px #a594f922, 0 1.5px 8px #a594f911;
        backdrop-filter: blur(8px);
        border: 1.5px solid #ede9fe;
        border-radius: 24px;
    }
    .report-title {
        font-size: 2.1rem;
        font-weight: 900;
        letter-spacing: 1.2px;
        background: linear-gradient(90deg, #A594F9 60%, #facc15 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .report-anon {
        background: linear-gradient(90deg, #facc1533 60%, #ede9fe 100%);
        border-radius: 12px;
        border-left: 6px solid #facc15;
        box-shadow: 0 2px 12px #facc1533;
    }
    .report-btn {
        background: linear-gradient(90deg, #A594F9 60%, #facc15 100%);
        color: #fff;
        font-weight: 700;
        font-size: 1.05rem;
        box-shadow: 0 2px 12px #a594f933;
        border: none;
        transition: transform 0.12s, box-shadow 0.12s;
        border-radius: 10px;
    }
    .report-btn:hover {
        background: #A594F9;
        color: #fff;
        transform: scale(1.04);
        box-shadow: 0 6px 24px #a594f944;
    }
    .report-help {
        background: rgba(255,255,255,0.95);
        border-radius: 15px;
        box-shadow: 0 2px 12px #a594f911;
        border: 1.5px solid #ede9fe;
    }
</style>
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            
            <div class="mb-4 text-center">
                <img src="<?php echo e(asset('images/gadlogo.png')); ?>" alt="GAD Logo" class="mb-3" style="width:74px; height:74px; border-radius:50%; object-fit:cover; box-shadow:0 8px 24px rgba(124,58,237,0.25);">
                <h2 class="fw-bold report-title mb-1">File a Safe Spaces Report</h2>
                <p class="text-muted small mb-1">Your safety is our priority. All reports are handled with strict confidentiality under RA 11313.</p>
                <p class="small fw-semibold mb-0" style="color:#A594F9;">This is a safe space for students and employees. We are here to listen and help.</p>
            </div>

            <?php if(session('success')): ?>
                <div id="reportSuccessAlert" class="alert border-0 shadow-sm mb-4" style="background:#ecfdf5; color:#065f46; border-radius:14px; border-left:6px solid #10b981 !important;">
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi bi-check-circle-fill mt-1"></i>
                        <div>
                            <div class="fw-bold">Report Delivered</div>
                            <div><?php echo e(session('success')); ?></div>
                            <?php if(session('report_case_id')): ?>
                                <div class="mt-3 d-flex flex-column flex-md-row align-items-md-center gap-2">
                                    <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle">Case ID: <?php echo e(session('report_case_id')); ?></span>
                                    <a href="<?php echo e(route('student.vault')); ?>" class="btn btn-sm btn-success rounded-pill px-3">
                                        <i class="bi bi-safe2 me-1"></i> Track This Case in Stealth Vault
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if($errors->any()): ?>
                <div class="alert border-0 shadow-sm mb-4" style="background:#fef2f2; color:#991b1b; border-radius:14px; border-left:6px solid #ef4444 !important;">
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi bi-exclamation-triangle-fill mt-1"></i>
                        <div>
                            <div class="fw-bold">Submission Error</div>
                            <ul class="mb-0 ps-3">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            
            <div class="card border-0 shadow-sm report-glass">
                <div class="card-body p-4 p-md-5">
                    <form action="<?php echo e(route('reports.store')); ?>" method="POST" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase" style="letter-spacing: 1px; color:#A594F9;">Type of Incident</label>
                            <select id="nature-select" name="nature" class="form-select form-control-lg border-0 bg-white shadow-sm" required style="border-radius: 14px; font-size: 1rem;">
                                <option value="" selected disabled>Select a category...</option>
                                <option value="Catcalling" <?php echo e(old('nature') === 'Catcalling' ? 'selected' : ''); ?>>Catcalling / Wolf-whistling</option>
                                <option value="Cyber-harassment" <?php echo e(old('nature') === 'Cyber-harassment' ? 'selected' : ''); ?>>Cyber-harassment</option>
                                <option value="Domestic Concern Affecting School/Work" <?php echo e(old('nature') === 'Domestic Concern Affecting School/Work' ? 'selected' : ''); ?>>Domestic Concern Affecting School/Work</option>
                                <option value="Gender-based Discrimination" <?php echo e(old('nature') === 'Gender-based Discrimination' ? 'selected' : ''); ?>>Gender-based Discrimination</option>
                                <option value="Groping" <?php echo e(old('nature') === 'Groping' ? 'selected' : ''); ?>>Groping / Unwanted Touching</option>
                                <option value="Retaliation or Intimidation" <?php echo e(old('nature') === 'Retaliation or Intimidation' ? 'selected' : ''); ?>>Retaliation or Intimidation</option>
                                <option value="Sexist Remarks or Jokes" <?php echo e(old('nature') === 'Sexist Remarks or Jokes' ? 'selected' : ''); ?>>Sexist Remarks or Jokes</option>
                                <option value="Stalking" <?php echo e(old('nature') === 'Stalking' ? 'selected' : ''); ?>>Stalking</option>
                                <option value="Unequal Opportunities" <?php echo e(old('nature') === 'Unequal Opportunities' ? 'selected' : ''); ?>>Unequal Opportunities</option>
                                <option value="Other GAD-related Concern" <?php echo e(old('nature') === 'Other GAD-related Concern' ? 'selected' : ''); ?>>Other GAD-related Concern</option>
                            </select>
                            <div id="other-nature-wrapper" class="mt-3" style="display:none;">
                                <label for="other-nature-input" class="form-label fw-bold small text-uppercase" style="letter-spacing: 1px; color:#A594F9;">Specify Incident</label>
                                <input
                                    type="text"
                                    id="other-nature-input"
                                    name="other_nature"
                                    value="<?php echo e(old('other_nature')); ?>"
                                    class="form-control border-0 bg-white shadow-sm"
                                    placeholder="Type the specific incident type"
                                    style="border-radius: 14px; font-size: 1rem;"
                                >
                                <small class="text-muted">Required when you select Other GAD-related Concern.</small>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase" style="letter-spacing: 1px; color:#A594F9;">Department / Program</label>
                                <select name="course" class="form-select border-0 bg-white shadow-sm" required style="border-radius: 14px; font-size: 1rem;">
                                    <option value="" selected disabled>Select your department or program...</option>
                                    <optgroup label="Student Programs">
                                    <option value="Bachelor Industrial Technology - Major in Electrical Technology" <?php echo e(old('course') === 'Bachelor Industrial Technology - Major in Electrical Technology' ? 'selected' : ''); ?>>Bachelor Industrial Technology - Major in Electrical Technology</option>
                                    <option value="Bachelor Industrial Technology - Major in Food Management Service" <?php echo e(old('course') === 'Bachelor Industrial Technology - Major in Food Management Service' ? 'selected' : ''); ?>>Bachelor Industrial Technology - Major in Food Management Service</option>
                                    <option value="Bachelor Industrial Technology - Major in Mechanical Technology" <?php echo e(old('course') === 'Bachelor Industrial Technology - Major in Mechanical Technology' ? 'selected' : ''); ?>>Bachelor Industrial Technology - Major in Mechanical Technology</option>
                                    <option value="Bachelor of Elementary Education (BEEd)" <?php echo e(old('course') === 'Bachelor of Elementary Education (BEEd)' ? 'selected' : ''); ?>>Bachelor of Elementary Education (BEEd)</option>
                                    <option value="Bachelor of Technology and Livelihood Education (BTLEd)" <?php echo e(old('course') === 'Bachelor of Technology and Livelihood Education (BTLEd)' ? 'selected' : ''); ?>>Bachelor of Technology and Livelihood Education (BTLEd)</option>
                                    <option value="Bachelor Secondary Education (BSEd) - Major in English" <?php echo e(old('course') === 'Bachelor Secondary Education (BSEd) - Major in English' ? 'selected' : ''); ?>>Bachelor Secondary Education (BSEd) - Major in English</option>
                                    <option value="Bachelor Secondary Education (BSEd) - Major in Math" <?php echo e(old('course') === 'Bachelor Secondary Education (BSEd) - Major in Math' ? 'selected' : ''); ?>>Bachelor Secondary Education (BSEd) - Major in Math</option>
                                    <option value="Bachelor Secondary Education (BSEd) - Major in Science" <?php echo e(old('course') === 'Bachelor Secondary Education (BSEd) - Major in Science' ? 'selected' : ''); ?>>Bachelor Secondary Education (BSEd) - Major in Science</option>
                                    <option value="BS Business Administration (BSBA)" <?php echo e(old('course') === 'BS Business Administration (BSBA)' ? 'selected' : ''); ?>>BS Business Administration (BSBA)</option>
                                    <option value="BS Information Technology (BSIT)" <?php echo e(old('course') === 'BS Information Technology (BSIT)' ? 'selected' : ''); ?>>BS Information Technology (BSIT)</option>
                                    </optgroup>
                                    <optgroup label="Employee Departments">
                                    <option value="College of Technology and Business" <?php echo e(old('course') === 'College of Technology and Business' ? 'selected' : ''); ?>>College of Technology and Business</option>
                                    <option value="College of Education" <?php echo e(old('course') === 'College of Education' ? 'selected' : ''); ?>>College of Education</option>
                                    </optgroup>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase" style="letter-spacing: 1px; color:#A594F9;">Gender</label>
                                <select name="gender" class="form-select border-0 bg-white shadow-sm" required style="border-radius: 14px; font-size: 1rem;">
                                    <option value="" selected disabled>Select gender...</option>
                                    <option value="Female" <?php echo e(old('gender') === 'Female' ? 'selected' : ''); ?>>Female</option>
                                    <option value="Male" <?php echo e(old('gender') === 'Male' ? 'selected' : ''); ?>>Male</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase" style="letter-spacing: 1px; color:#A594F9;">Incident Date</label>
                                <input
                                    type="date"
                                    name="incident_date"
                                    value="<?php echo e(old('incident_date')); ?>"
                                    max="<?php echo e(now()->toDateString()); ?>"
                                    class="form-control border-0 bg-white shadow-sm"
                                    style="border-radius: 14px; font-size: 1rem;"
                                    required
                                >
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase" style="letter-spacing: 1px; color:#A594F9;">Incident Time</label>
                                <input
                                    type="time"
                                    name="incident_time"
                                    value="<?php echo e(old('incident_time')); ?>"
                                    class="form-control border-0 bg-white shadow-sm"
                                    style="border-radius: 14px; font-size: 1rem;"
                                    required
                                >
                            </div>
                        </div>

                        
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase" style="letter-spacing: 1px; color:#A594F9;">Detailed Description</label>
                            <textarea name="description" class="form-control border-0 bg-white shadow-sm" rows="5" 
                                placeholder="Tell us what happened... (When, Where, Who)" 
                                style="border-radius: 14px; font-size: 1rem;" required></textarea>
                            <div class="form-text text-muted mt-2">
                                <i class="bi bi-info-circle me-1"></i> Be as specific as possible to help us assist you better.
                            </div>
                        </div>


                        
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase" style="letter-spacing: 1px; color:#A594F9;">Location of Incident</label>
                            <input type="text" name="location" class="form-control border-0 bg-white shadow-sm" placeholder="Where did it happen?" style="border-radius: 14px; font-size: 1rem;" required>
                        </div>

                        
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase" style="letter-spacing: 1px; color:#A594F9;">Attach Evidence (Optional)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-0" style="border-radius: 12px 0 0 12px;">
                                    <i class="bi bi-paperclip text-secondary"></i>
                                </span>
                                <input type="file" name="evidence[]" class="form-control border-0 bg-white shadow-sm" id="inputGroupFile02" multiple style="border-radius: 0 12px 12px 0;">
                            </div>
                            <small class="text-muted">Upload screenshots, photos, or documents (Max 1GB per file).</small>
                        </div>

                        <hr class="my-4 opacity-25">

                        
                        <div class="p-3 mb-4 report-anon">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_anonymous" id="anonymousSwitch" checked>
                                <label class="form-check-label fw-bold text-dark" for="anonymousSwitch"><i class="bi bi-incognito me-1"></i>Report Anonymously</label>
                            </div>
                            <small class="d-block text-muted mt-1">Your real name will be hidden. Only your cloak alias (<?php echo e(Auth::user()->cloak_alias); ?>) will be shown to the GAD office.</small>
                        </div>

                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <button type="reset" class="btn btn-light px-4 fw-bold text-muted" style="border-radius: 10px;">Clear Form</button>
                            <button type="submit" class="btn px-5 fw-bold shadow-sm report-btn">
                                <i class="bi bi-send-fill me-2"></i> Submit Report
                            </button>
                        </div>

                        <div class="mt-4 p-3 rounded-3" style="background:#fff7ed; border:1px solid #fed7aa;">
                            <p class="mb-1 fw-bold small" style="color:#9a3412;"><i class="bi bi-telephone-fill me-1"></i> Emergency Hotline</p>
                            <p class="mb-0 small text-muted">For immediate assistance, call GAD Emergency Hotline: <span class="fw-bold text-dark">(075) 123-4567</span></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if(session('success')): ?>
<script>
    window.addEventListener('load', function () {
        const successAlert = document.getElementById('reportSuccessAlert');
        if (successAlert) {
            successAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
</script>
<?php endif; ?>

<script>
    window.addEventListener('load', function () {
        const natureSelect = document.getElementById('nature-select');
        const otherWrapper = document.getElementById('other-nature-wrapper');
        const otherInput = document.getElementById('other-nature-input');
        const otherOption = 'Other GAD-related Concern';

        function toggleOtherNatureField() {
            const isOtherSelected = natureSelect && natureSelect.value === otherOption;

            if (otherWrapper) {
                otherWrapper.style.display = isOtherSelected ? 'block' : 'none';
            }

            if (otherInput) {
                otherInput.required = !!isOtherSelected;
                if (!isOtherSelected) {
                    otherInput.value = '';
                }
            }
        }

        if (natureSelect) {
            natureSelect.addEventListener('change', toggleOtherNatureField);
            toggleOtherNatureField();
        }
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ANGELINE AMBROSIO\sinag\resources\views\student\report.blade.php ENDPATH**/ ?>