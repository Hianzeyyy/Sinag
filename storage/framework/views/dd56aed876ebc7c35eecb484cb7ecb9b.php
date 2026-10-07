<?php $__env->startSection('content'); ?>
<div class="sinag-splash">
    
    <div style="position: absolute; top: -10%; right: -5%; width: 500px; height: 500px; background: rgba(165, 148, 249, 0.28); filter: blur(120px); border-radius: 50%;"></div>
    <div style="position: absolute; bottom: -10%; left: -5%; width: 500px; height: 500px; background: rgba(194, 181, 255, 0.22); filter: blur(120px); border-radius: 50%;"></div>

    <div class="row justify-content-center text-center w-100">
        <div class="col-md-8">
            <div class="mb-4 d-flex justify-content-center align-items-center gap-4" style="animation: logoEntrance 1.5s ease-out;">
              <img src="<?php echo e(asset('images/gadlogo.png')); ?>" alt="GAD Logo" style="width: 90px; height: 90px; border-radius: 50%; object-fit: cover; filter: drop-shadow(0 0 18px rgba(250, 204, 21, 0.4));">
                <h1 class="display-1 fw-bold mb-0" style="letter-spacing: -2px; text-shadow: 0 4px 15px rgba(0,0,0,0.3); animation: fadeInDown 1s ease-out; line-height:1;">
                <span style="color: #ede9fe;">SI</span><span style="color: #facc15;">NAG</span>
                </h1>
            </div>
            
            <p class="lead text-white mb-2 text-uppercase fw-bold" style="letter-spacing: 5px; opacity: 0.9; animation: fadeInUp 1.2s ease-out;">
                Gender and Development Safe Space and Participatory Suggestion System
            </p>
            <p class="text-white mb-5" style="font-size:1.1rem; font-style:italic; opacity:0.85; animation: fadeInUp 1.3s ease-out;">“Every voice matters. Every story is safe.”</p>

            <div class="mt-4" style="animation: fadeInUp 1.4s ease-out;">
                <!-- Animated loading dots -->
                <div style="font-size:2.2rem; color:#fff7c2; letter-spacing:0.2em; font-weight:700;">
                    <span class="loading-dot">.</span><span class="loading-dot">.</span><span class="loading-dot">.</span>
                </div>
                <p class="text-white mt-3 small text-uppercase" style="letter-spacing: 2px;">Establishing Secure Connection</p>
            </div>
            <!-- About Button (bottom right) -->
            <button type="button" class="btn btn-link position-fixed" style="bottom: 24px; right: 32px; color: #ede9fe; font-size: 1.2rem; opacity: 0.7; z-index: 10001; text-decoration: none;" data-bs-toggle="modal" data-bs-target="#aboutModal">
                <i class="bi bi-info-circle-fill"></i> <span class="d-none d-md-inline">About</span>
            </button>
            <!-- About Modal -->
            <div class="modal fade" id="aboutModal" tabindex="-1" aria-labelledby="aboutModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content bg-dark text-white" style="background: rgba(30,27,75,0.97); border-radius: 18px;">
                  <div class="modal-header border-0">
                    <h5 class="modal-title" id="aboutModalLabel">About SINAG</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body text-center">
                    <p class="mb-3">This system was developed with care by:</p>
                    <ul class="list-unstyled mb-2">
                      <li>Angeline D. Ambrosio</li>
                      <li>Charlie T. Angco</li>
                      <li>Ivan Rey L. Ariap</li>
                      <li>Emilhene S. Obrero</li>
                      <li>Ellaiza May C. Ortiz</li>
                    </ul>
                    <small class="text-secondary">Pangasinan State University &mdash; 2026</small>
                  </div>
                </div>
              </div>
            </div>

            <div class="mt-5 pt-4" style="animation: fadeInUp 2.2s ease-out;">
               
            </div>
        </div>
    </div>
</div>

<style>
    nav { display: none !important; }
    body { overflow: hidden !important; padding: 0 !important; }
    .sinag-splash {
      background: linear-gradient(135deg, #A594F9 0%, #c4b5fd 55%, #8f72f5 100%);
      min-height: 100dvh;
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      position: fixed;
      inset: 0;
      z-index: 9999;
      overflow: hidden;
    }

    @keyframes logoEntrance {
        from { opacity: 0; transform: scale(0.5) translateY(-20px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }
    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes livelyGradient {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }
    .splash-lively-bg {
      background: linear-gradient(120deg, #A594F9, #c4b5fd, #e9d5ff, #facc15, #c4b5fd, #8f72f5);
      background-size: 300% 300%;
      animation: livelyGradient 12s ease-in-out infinite;
      height: 100vh;
      width: 100vw;
      display: flex;
      align-items: center;
      justify-content: center;
      position: fixed;
      top: 0; left: 0;
      z-index: 9999;
      overflow: hidden;
    }
    .loading-dot {
      animation: blink 1.2s infinite alternate;
      opacity: 0.5;
    }
    .loading-dot:nth-child(2) { animation-delay: 0.3s; }
    .loading-dot:nth-child(3) { animation-delay: 0.6s; }
    @media (max-width: 767px) {
      .sinag-splash .lead { font-size: 0.8rem; letter-spacing: 2px !important; }
      .sinag-splash .gap-4 { gap: 1rem !important; }
      .sinag-splash h1 { font-size: 3.5rem; }
    }
    @keyframes blink {
      0% { opacity: 0.2; }
      100% { opacity: 1; }
    }
</style>

<script>
    setTimeout(function() {
        window.location.href = "<?php echo e(route('login')); ?>";
    }, 3000);
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sinag\resources\views/splash.blade.php ENDPATH**/ ?>