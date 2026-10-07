

<?php $__env->startSection('content'); ?>
<div class="container-fluid p-0 overflow-hidden" 
    style="background: linear-gradient(135deg, #A594F9 0%, #c4b5fd 55%, #8f72f5 100%); height: 100vh; display: flex; align-items: center; justify-content: center; position: fixed; top: 0; left: 0;">
    
    <div class="bg-glow top-right"></div>
    <div class="bg-glow bottom-left"></div>

    <div class="particles">
        <?php for($i = 1; $i <= 10; $i++): ?>
            <div class="particle"></div>
        <?php endfor; ?>
    </div>

    <div class="row justify-content-center text-center w-100" style="position: relative; z-index: 10;">
        <div class="col-md-10 col-lg-7 animate-all">
            
            <div class="glass-plate p-5 shadow-2xl">
                <div class="mb-5">
                    <img src="<?php echo e(asset('images/gadlogo.png')); ?>" alt="GAD Logo" 
                         class="main-logo">
                </div>

                <div class="brand-container mb-3">
                    <h1 class="display-1 fw-bold main-title">
                        <span class="text-violet">SI</span>NAG
                    </h1>
                </div>
                
                <p class="lead text-white-50 mb-5 text-uppercase fw-bold tracking-widest animate-text">
                    Gender and Development Safe Space and Participatory Suggestion System
                </p>

                <div class="d-flex flex-column flex-sm-row justify-content-center gap-4 mt-4">
                    <?php if(auth()->guard()->guest()): ?>
                        <a href="<?php echo e(route('login')); ?>" class="btn btn-enter shadow-lg">
                            <i class="bi bi-shield-lock-fill me-2"></i> ENTER SYSTEM
                        </a>
                    <?php else: ?>
                        <a href="<?php echo e(url('/home')); ?>" class="btn btn-dashboard shadow-lg">
                            <i class="bi bi-grid-1x2-fill me-2"></i> GO TO DASHBOARD
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mt-5 footer-reveal">
                <div class="badge rounded-pill badge-custom">
                    <div class="d-flex align-items-center">
                        <span class="pulse-dot me-2"></span>
                        <i class="bi bi-shield-check text-warning me-2"></i> 
                        PSU Gender and Development Office Secured
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* --- THEME CONSTANTS --- */
    :root {
        --violet-primary: #A594F9;
        --gold-primary: #facc15;
        --glass-bg: rgba(255, 255, 255, 0.03);
    }

    /* Ambient Glow Effects */
    .bg-glow {
        position: absolute;
        width: 40vw;
        height: 40vw;
        filter: blur(120px);
        border-radius: 50%;
        pointer-events: none;
        opacity: 0.6;
    }
    .top-right { top: -15%; right: -10%; background: rgba(255, 255, 255, 0.22); }
    .bottom-left { bottom: -15%; left: -10%; background: rgba(255, 255, 255, 0.16); }

    /* Glassmorphism Plate */
    .glass-plate {
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 40px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    }

    /* Typography */
    .main-title {
        letter-spacing: 15px;
        color: var(--gold-primary);
        text-shadow: 0 0 40px rgba(250, 204, 21, 0.5);
        font-family: 'Inter', system-ui, -apple-system;
    }
    .text-violet { color: #A594F9; }
    .tracking-widest { letter-spacing: 8px; font-size: 0.8rem; opacity: 0.7; }

    /* Interactive Logo */
    .main-logo {
        height: clamp(120px, 15vw, 180px);
        filter: drop-shadow(0 0 30px rgba(250, 204, 21, 0.3));
        animation: floatAndPulse 6s infinite ease-in-out;
    }

    /* Action Components */
    .btn-enter, .btn-dashboard {
        padding: 1.2rem 3.5rem;
        border-radius: 100px;
        font-weight: 800;
        color: white;
        border: none;
        transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        letter-spacing: 2px;
        text-transform: uppercase;
    }
    .btn-enter { background: linear-gradient(45deg, #A594F9, #c4b5fd); }
    .btn-dashboard { background: linear-gradient(45deg, #0d9488, #14b8a6); }

    .btn:hover {
        transform: translateY(-10px) scale(1.05);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        filter: brightness(1.2);
        color: white;
    }

    /* Particle Logic */
    .particles .particle {
        position: absolute;
        background: white;
        border-radius: 50%;
        opacity: 0.2;
        pointer-events: none;
        animation: floatUp 25s infinite linear;
    }
    .particle:nth-child(odd) { background: var(--gold-primary); width: 4px; height: 4px; }
    .particle:nth-child(even) { background: var(--violet-primary); width: 6px; height: 6px; }
    /* Particles randomized via JS below (Sass not supported in blade-rendered CSS) */
    .particles .particle { bottom: -20px; }

    /* Badge & Micro-animations */
    .badge-custom {
        background: rgba(0, 0, 0, 0.6) !important;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        padding: 1rem 2rem;
    }
    .pulse-dot {
        width: 8px; height: 8px;
        background: #22c55e;
        border-radius: 50%;
        display: inline-block;
        box-shadow: 0 0 0 rgba(34, 197, 94, 0.4);
        animation: pulseGreen 2s infinite;
    }

    @keyframes floatAndPulse {
        0%, 100% { transform: translateY(0) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(2deg); filter: drop-shadow(0 0 50px rgba(250, 204, 21, 0.6)); }
    }

    @keyframes floatUp {
        from { transform: translateY(0) rotate(0deg); opacity: 0; }
        10% { opacity: 0.5; }
        90% { opacity: 0.5; }
        to { transform: translateY(-110vh) rotate(360deg); opacity: 0; }
    }

    @keyframes pulseGreen {
        0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(34, 197, 94, 0); }
        100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const particles = document.querySelectorAll('.particles .particle');
        particles.forEach(p => {
            const left = Math.floor(Math.random() * 100);
            const delay = Math.floor(Math.random() * 20);
            const opacity = (Math.floor(Math.random() * 5) + 1) / 10;
            p.style.left = left + '%';
            p.style.animationDelay = delay + 's';
            p.style.opacity = opacity;
        });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ANGELINE AMBROSIO\sinag\resources\views\home.blade.php ENDPATH**/ ?>