

<?php $__env->startSection('content'); ?>
<style>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;0,900;1,400&family=Lora:ital,wght@0,400;0,600;1,400&display=swap');

* { box-sizing: border-box; margin: 0; padding: 0; }

body {
    background: linear-gradient(160deg, #f5f0ff 0%, #fdf8f0 50%, #f0f4ff 100%) !important;
    min-height: 100vh;
}

.ref-page {
    padding: 3rem 2rem;
    max-width: 1200px;
    margin: 0 auto;
}

/* ── PAGE HEADER ── */
.ref-header {
    text-align: center;
    margin-bottom: 3.5rem;
}
.ref-header h1 {
    font-family: 'Times New Roman', Times, serif
    font-size: clamp(2rem, 5vw, 3.2rem);
    font-weight: 900;
    color: #9b60b1;
    letter-spacing: 0.5px;
    margin-bottom: 0.5rem;
}
.ref-header p {
    font-family: 'Lora', Georgia, serif;
    font-style: italic;
    color: #7c6baa;
    font-size: 1.05rem;
}

/* ── BOOK GRID ── */
.books-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 2.5rem 2rem;
    justify-items: center;
}

/* ── BOOK WRAPPER ── */
.book-wrapper {
    perspective: 1200px;
    width: 240px;
    height: 320px;
    cursor: pointer;
}

.book {
    width: 100%;
    height: 100%;
    position: relative;
    transform-style: preserve-3d;
    transform: rotateY(-15deg) rotateX(3deg);
    transition: transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    filter: drop-shadow(12px 18px 30px rgba(100,70,200,0.22));
}

.book-wrapper:hover .book {
    transform: rotateY(-8deg) rotateX(2deg) translateY(-8px);
    filter: drop-shadow(20px 28px 40px rgba(100,70,200,0.32));
}

/* FRONT COVER */
.book-front {
    position: absolute;
    width: 100%;
    height: 100%;
    border-radius: 2px 10px 10px 2px;
    overflow: hidden;
    backface-visibility: hidden;
    background: #fff;
    display: flex;
    flex-direction: column;
}

.book-cover-img {
    width: 100%;
    flex: 1;
    background-size: cover;
    background-position: center;
    background-color: #e8e0ff;
    position: relative;
}

.book-cover-img::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(to bottom, transparent 55%, rgba(30,10,70,0.55) 100%);
}

.book-cover-label {
    background: linear-gradient(135deg, #5b3fcf 0%, #a594f9 100%);
    color: #fff;
    padding: 0.8rem 1rem;
    font-family: 'Playfair Display', Georgia, serif;
}

.book-cover-label h3 {
    font-size: 0.88rem;
    font-weight: 700;
    letter-spacing: 0.3px;
    margin-bottom: 0.2rem;
    line-height: 1.3;
}

.book-cover-label p {
    font-size: 0.72rem;
    opacity: 0.85;
    font-style: italic;
    font-family: 'Lora', Georgia, serif;
    line-height: 1.3;
}

/* SPINE */
.book-spine {
    position: absolute;
    left: 0;
    top: 0;
    width: 22px;
    height: 100%;
    background: linear-gradient(180deg, #7c5ecf 0%, #4a2fa0 100%);
    transform: rotateY(-90deg) translateX(-11px) translateZ(11px);
    transform-origin: left center;
    border-radius: 2px 0 0 2px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.book-spine span {
    writing-mode: vertical-rl;
    transform: rotate(180deg);
    font-family: 'Playfair Display', serif;
    font-size: 0.6rem;
    font-weight: 700;
    color: rgba(255,255,255,0.75);
    letter-spacing: 1.5px;
    text-transform: uppercase;
    white-space: nowrap;
    overflow: hidden;
    max-height: 90%;
    text-overflow: ellipsis;
}

/* BOOK THICKNESS (pages side) */
.book-pages {
    position: absolute;
    right: 0;
    top: 3px;
    bottom: 3px;
    width: 18px;
    background: repeating-linear-gradient(
        to right,
        #f5f0e8 0px,
        #f0ebe2 1px,
        #f8f4ee 2px,
        #ece6dc 3px
    );
    transform: rotateY(90deg) translateZ(222px);
    transform-origin: right center;
    border-radius: 0;
}

/* ── OPEN-BOOK MODAL OVERLAY ── */
.book-overlay {
    position: fixed;
    inset: 0;
    background: rgba(20, 10, 50, 0.72);
    z-index: 9000;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.35s ease;
    backdrop-filter: blur(4px);
    padding: 1rem;
}

.book-overlay.active {
    opacity: 1;
    pointer-events: all;
}

/* OPEN BOOK CONTAINER */
.open-book {
    display: flex;
    width: min(900px, 96vw);
    height: min(620px, 90vh);
    transform-origin: center center;
    transform: scale(0.6) rotateY(-30deg);
    transition: transform 0.65s cubic-bezier(0.2, 0.9, 0.3, 1), opacity 0.45s ease;
    opacity: 0;
    filter: drop-shadow(0 40px 80px rgba(30,10,80,0.55));
}

.book-overlay.active .open-book {
    transform: scale(1) rotateY(0deg);
    opacity: 1;
}

/* LEFT PAGE (cover side) */
.page-left {
    flex: 1;
    background: linear-gradient(135deg, #5b3fcf 0%, #a594f9 60%, #facc15 130%);
    border-radius: 10px 0 0 10px;
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    color: #fff;
}

.page-left-bg {
    position: absolute;
    inset: 0;
    background-size: cover;
    background-position: center;
    opacity: 0.22;
}

.page-left-content {
    position: relative;
    z-index: 2;
    text-align: center;
}

.page-left-badge {
    display: inline-block;
    background: rgba(255,255,255,0.18);
    border: 1px solid rgba(255,255,255,0.35);
    border-radius: 100px;
    padding: 0.25rem 1rem;
    font-size: 0.7rem;
    letter-spacing: 2px;
    text-transform: uppercase;
    font-family: 'Lora', serif;
    margin-bottom: 1.2rem;
}

.page-left-title {
    font-family: 'Playfair Display', Georgia, serif;
    font-size: clamp(1.3rem, 3.5vw, 2rem);
    font-weight: 900;
    line-height: 1.2;
    margin-bottom: 0.9rem;
    text-shadow: 0 2px 12px rgba(0,0,0,0.2);
}

.page-left-subtitle {
    font-family: 'Lora', Georgia, serif;
    font-style: italic;
    font-size: 0.9rem;
    opacity: 0.88;
    line-height: 1.5;
}

/* PAGE DIVIDER (spine shadow) */
.page-divider {
    width: 14px;
    background: linear-gradient(to right, #3a2080, #7c5ecf, #a594f9, #f0e8ff, #fff);
    flex-shrink: 0;
    position: relative;
}
.page-divider::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(to bottom,
        transparent 0%,
        rgba(100,60,200,0.08) 30%,
        rgba(100,60,200,0.15) 50%,
        rgba(100,60,200,0.08) 70%,
        transparent 100%);
}

/* RIGHT PAGE (content) */
.page-right {
    flex: 1.15;
    background: #fffdf8;
    border-radius: 0 10px 10px 0;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    position: relative;
}

/* subtle page lines */
.page-right::before {
    content: '';
    position: absolute;
    inset: 0;
    background: repeating-linear-gradient(
        to bottom,
        transparent 0px,
        transparent 27px,
        rgba(165, 148, 249, 0.08) 28px
    );
    pointer-events: none;
}

.page-right-header {
    padding: 1.4rem 1.8rem 1rem;
    border-bottom: 1.5px solid #e9e0ff;
    position: relative;
}

.page-right-header h2 {
    font-family: 'Playfair Display', Georgia, serif;
    font-size: 1.15rem;
    font-weight: 700;
    color: #2d1b6b;
    margin-bottom: 0.2rem;
}

.page-right-header p {
    font-family: 'Lora', serif;
    font-style: italic;
    font-size: 0.8rem;
    color: #7c6baa;
}

.page-right-body {
    flex: 1;
    overflow-y: auto;
    padding: 1.2rem 1.8rem 2rem;
    font-family: 'Lora', Georgia, serif;
    font-size: 0.88rem;
    line-height: 1.8;
    color: #3d3050;
    position: relative;
    scroll-behavior: smooth;
}

/* scrollbar */
.page-right-body::-webkit-scrollbar { width: 5px; }
.page-right-body::-webkit-scrollbar-track { background: transparent; }
.page-right-body::-webkit-scrollbar-thumb { background: #c4b5fd; border-radius: 10px; }

.page-right-body h6 {
    font-family: 'Playfair Display', Georgia, serif;
    font-weight: 700;
    color: #2d1b6b;
    font-size: 0.92rem;
    margin-top: 1.3rem;
    margin-bottom: 0.4rem;
}

.page-right-body p { margin-bottom: 0.6rem; }
.page-right-body ul, .page-right-body ol {
    padding-left: 1.3rem;
    margin-bottom: 0.6rem;
}
.page-right-body li { margin-bottom: 0.3rem; }
.page-right-body strong { color: #3d1f9e; }

.page-right-footer {
    padding: 0.75rem 1.8rem;
    border-top: 1px solid #e9e0ff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: rgba(245, 240, 255, 0.6);
}

.page-num {
    font-family: 'Playfair Display', serif;
    font-size: 0.78rem;
    color: #a594f9;
    font-style: italic;
}

.close-book-btn {
    font-family: 'Lora', serif;
    font-size: 0.8rem;
    background: linear-gradient(135deg, #5b3fcf, #a594f9);
    color: #fff;
    border: none;
    border-radius: 100px;
    padding: 0.4rem 1.2rem;
    cursor: pointer;
    letter-spacing: 0.3px;
    transition: transform 0.15s, box-shadow 0.15s;
}
.close-book-btn:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 16px rgba(90,63,207,0.3);
}

/* PAGE TURN ANIMATION overlay */
.page-turn-anim {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, #f0e8ff 0%, #fff 60%);
    border-radius: 0 10px 10px 0;
    transform-origin: left center;
    animation: pageTurn 0.55s cubic-bezier(0.4, 0.0, 0.2, 1) forwards;
    pointer-events: none;
}

@keyframes pageTurn {
    0% { transform: rotateY(0deg); opacity: 1; }
    100% { transform: rotateY(-90deg); opacity: 0; }
}

/* ── RESPONSIVE ── */
@media (max-width: 680px) {
    .open-book { flex-direction: column; height: 88vh; }
    .page-left { border-radius: 10px 10px 0 0; flex: 0 0 160px; padding: 1.2rem; }
    .page-divider { width: 100%; height: 10px; background: linear-gradient(to bottom, #3a2080, #a594f9, #fff); }
    .page-right { border-radius: 0 0 10px 10px; }
    .page-left-title { font-size: 1.1rem; }
}

@media (max-width: 500px) {
    .books-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1.5rem 0.75rem; }
    .book-wrapper { width: min(140px, 100%); height: 190px; }
    .book-spine { width: 14px; }
    .book-pages { width: 12px; }
}
</style>

<div class="ref-page">
    <div class="ref-header">
        <h1>References</h1>
       
    </div>

    <div class="books-grid">

        
        <div class="book-wrapper" onclick="openBook('ra9262')">
            <div class="book">
                <div class="book-spine"><span>RA 9262 · VAWC Act</span></div>
                <div class="book-front">
                    <div class="book-cover-img" style="background-image: url('<?php echo e(asset('images/ra9262.jpg')); ?>');"></div>
                    <div class="book-cover-label">
                        <h3>Republic Act 9262</h3>
                        <p>Anti-VAWC Act of 2004</p>
                    </div>
                </div>
                <div class="book-pages"></div>
            </div>
        </div>

        
        <div class="book-wrapper" onclick="openBook('ra8353')">
            <div class="book">
                <div class="book-spine"><span>RA 8353 · Anti-Rape Law</span></div>
                <div class="book-front">
                    <div class="book-cover-img" style="background-image: url('<?php echo e(asset('images/ra8353.jpg')); ?>');"></div>
                    <div class="book-cover-label">
                        <h3>Republic Act 8353</h3>
                        <p>Anti-Rape Law of 1997</p>
                    </div>
                </div>
                <div class="book-pages"></div>
            </div>
        </div>

        
        <div class="book-wrapper" onclick="openBook('ra9208')">
            <div class="book">
                <div class="book-spine"><span>RA 9208 · Anti-Trafficking</span></div>
                <div class="book-front">
                    <div class="book-cover-img" style="background-image: url('<?php echo e(asset('images/ra9208.jpg')); ?>');"></div>
                    <div class="book-cover-label">
                        <h3>Republic Act 9208</h3>
                        <p>Anti-Trafficking in Persons Act</p>
                    </div>
                </div>
                <div class="book-pages"></div>
            </div>
        </div>

        
        <div class="book-wrapper" onclick="openBook('ra9710')">
            <div class="book">
                <div class="book-spine"><span>RA 9710 · Magna Carta</span></div>
                <div class="book-front">
                    <div class="book-cover-img" style="background-image: url('<?php echo e(asset('images/ra9710.jpg')); ?>');"></div>
                    <div class="book-cover-label">
                        <h3>Republic Act 9710</h3>
                        <p>Magna Carta of Women</p>
                    </div>
                </div>
                <div class="book-pages"></div>
            </div>
        </div>

        
        <div class="book-wrapper" onclick="openBook('hivaids')">
            <div class="book">
                <div class="book-spine"><span>HIV/AIDS · Awareness</span></div>
                <div class="book-front">
                    <div class="book-cover-img" style="background-image: url('<?php echo e(asset('images/hivaids.jpg')); ?>');"></div>
                    <div class="book-cover-label">
                        <h3>HIV/AIDS Awareness</h3>
                        <p>Towards a Safe Tomorrow</p>
                    </div>
                </div>
                <div class="book-pages"></div>
            </div>
        </div>

        
        <div class="book-wrapper" onclick="openBook('harassment')">
            <div class="book">
                <div class="book-spine"><span>Anti-Sexual Harassment Policy</span></div>
                <div class="book-front">
                    <div class="book-cover-img" style="background-image: url('<?php echo e(asset('images/antisexualharassmentpolicy.jpg')); ?>');"></div>
                    <div class="book-cover-label">
                        <h3>Anti-Sexual Harassment</h3>
                        <p>Institutional Policy</p>
                    </div>
                </div>
                <div class="book-pages"></div>
            </div>
        </div>

        
        <div class="book-wrapper" onclick="openBook('codi')">
            <div class="book">
                <div class="book-spine"><span>CODI · Decorum &amp; Investigation</span></div>
                <div class="book-front">
                    <div class="book-cover-img" style="background-image: url('<?php echo e(asset('images/codi.jpg')); ?>');"></div>
                    <div class="book-cover-label">
                        <h3>CODI</h3>
                        <p>Committee on Decorum and Investigation</p>
                    </div>
                </div>
                <div class="book-pages"></div>
            </div>
        </div>

        
        <div class="book-wrapper" onclick="openBook('nonexpulsion')">
            <div class="book">
                <div class="book-spine"><span>Student Pregnancy Policy</span></div>
                <div class="book-front">
                    <div class="book-cover-img" style="background-image: url('<?php echo e(asset('images/nonexpulsion.jpg')); ?>');"></div>
                    <div class="book-cover-label">
                        <h3>Student Pregnancy Policy</h3>
                        <p>Non-Expulsion Due to Pregnancy</p>
                    </div>
                </div>
                <div class="book-pages"></div>
            </div>
        </div>

    </div>
</div>


<div class="book-overlay" id="bookOverlay" onclick="handleOverlayClick(event)">
    <div class="open-book" id="openBook">

        
        <div class="page-left" id="pageLeft">
            <div class="page-left-bg" id="pageLeftBg"></div>
            <div class="page-left-content">
                <div class="page-left-badge">Legal Reference</div>
                <div class="page-left-title" id="pageLeftTitle">Republic Act 9262</div>
                <div class="page-left-subtitle" id="pageLeftSubtitle">Anti-Violence Against Women and Children Act of 2004</div>
            </div>
        </div>

        
        <div class="page-divider"></div>

        
        <div class="page-right">
            <div class="page-right-header">
                <h2 id="pageRightTitle">Republic Act 9262</h2>
                <p id="pageRightSubtitle">Anti-Violence Against Women and Children Act</p>
            </div>
            <div class="page-right-body" id="pageRightBody"></div>
            <div class="page-right-footer">
                <span class="page-num" id="pageNum">Page 1</span>
                <button class="close-book-btn" onclick="closeBook()">Close Book ✕</button>
            </div>
        </div>

    </div>
</div>

<script>
const BOOKS = {
    ra9262: {
        title: 'Republic Act 9262',
        subtitle: 'Anti-Violence Against Women and Children (VAWC) Act of 2004',
        image: '<?php echo e(asset("images/ra9262.jpg")); ?>',
        pageLabel: 'Anti-VAWC Act · RA 9262',
        content: `
            <h6>What is RA 9262?</h6>
            <p>RA 9262 is a Philippine law enacted to address and combat violence against women and their children (VAWC) by intimate partners. It includes protections for women abused by current or former husbands, live-in partners, boyfriends, and dating partners.</p>

            <h6>Defining Violence Against Women and Their Children (VAWC)</h6>
            <p>VAWC under RA 9262 includes any action by a partner that causes physical, sexual, psychological, or economic harm to a woman or her child. This law applies to relationships where:</p>
            <ul>
                <li>The victim is the wife, former wife, or a partner in a sexual or dating relationship.</li>
                <li>The victim shares a child with the abuser.</li>
                <li>The child may be legitimate or illegitimate, and the abuse can occur inside or outside the family home.</li>
            </ul>

            <h6>Acts of Violence Covered</h6>
            <ul>
                <li><strong>Physical Violence:</strong> Physical harm, threats, and actions that cause fear of harm.</li>
                <li><strong>Sexual Violence:</strong> Acts of sexual assault, harassment, or coercion.</li>
                <li><strong>Psychological Violence:</strong> Controlling movement, threats, public humiliation, verbal abuse, stalking, or harassment.</li>
                <li><strong>Economic Abuse:</strong> Preventing work, controlling finances, or destruction of property.</li>
            </ul>

            <h6>Important Definitions</h6>
            <ul>
                <li><strong>Children:</strong> Individuals under 18 or those unable to care for themselves.</li>
                <li><strong>Dating Relationship:</strong> A long-term, romantic partnership.</li>
                <li><strong>Sexual Relations:</strong> A single sexual act, with or without resulting in a child.</li>
                <li><strong>Battered Woman Syndrome (BWS):</strong> A pattern of psychological effects due to prolonged abuse.</li>
            </ul>

            <h6>Actions Available</h6>
            <p>Women and children can file a criminal action or apply for a Protection Order — Barangay Protection Order (BPO), Temporary Protection Order (TPO), or Permanent Protection Order (PPO).</p>

            <h6>Filing Complaints</h6>
            <ul>
                <li><strong>Who May File:</strong> The victim, family members, social workers, barangay officials, or any concerned citizen.</li>
                <li><strong>Where to File:</strong> Family Courts or Municipal Courts in the victim's area.</li>
                <li><strong>Penalties:</strong> Imprisonment from 1 month to 20 years, fines of PHP 100,000–300,000, and mandatory counseling.</li>
            </ul>

            <h6>Helplines</h6>
            <p><strong>DSWD:</strong> 733-0014 to 18 local 116</p>
            <p><strong>PNP Women and Children Protection Center:</strong> 410-3213 / 532-6690</p>
            <p><strong>Aleng Pulis Text Hot-line:</strong> 0919-777-7377</p>
            <p><strong>NBI — VAWCD:</strong> (02) 8523-8231 to 38</p>
            <p><strong>DOJ-PAO:</strong> (02) 8929-9436 local 106, 107 or 159</p>
        `
    },
    ra8353: {
        title: 'Republic Act 8353',
        subtitle: 'The Anti-Rape Law of 1997',
        image: '<?php echo e(asset("images/ra8353.jpg")); ?>',
        pageLabel: 'Anti-Rape Law · RA 8353',
        content: `
            <h6>Republic Act 8353</h6>
            <p>An Act Expanding the Definition of the Crime of Rape and Reclassifying the same as a Crime Against Persons.</p>

            <h6>Rape Redefined as:</h6>
            <ol>
                <li><strong>A crime against persons</strong> — Rape violates a person's well-being and not just one's virginity or purity. Any person may be victimized by rape.</li>
                <li><strong>A public offense</strong> — Anyone with knowledge of the crime may file a case, and prosecution can continue even if the victim pardons the offender.</li>
            </ol>

            <h6>What Constitutes Rape?</h6>
            <p>Rape is committed when sexual intercourse occurs under any of the following:</p>
            <ul>
                <li>Through force, threat, or intimidation.</li>
                <li>When the victim is deprived of reason or unconscious.</li>
                <li>Through fraudulent machination or grave abuse of authority.</li>
                <li>When the victim is under twelve (12) years of age or is demented.</li>
            </ul>
            <p>Rape is also committed by sexual assault by inserting a penis into another person's mouth or anal orifice, or any instrument into the genital or oral orifice of another person.</p>

            <h6>Who Can Be Raped?</h6>
            <p>Anyone can be a rape victim, although incidence is more common among women and girls.</p>

            <h6>Who Can Commit Rape?</h6>
            <p>Any man or woman may be held liable for rape. The law recognizes marital rape, where a husband may be held liable for raping his wife.</p>

            <h6>Penalties</h6>
            <ul>
                <li><strong>Reclusion Perpetua (20–40 years):</strong> Rape through sexual intercourse.</li>
                <li><strong>Prision Mayor (6–12 years):</strong> Rape through oral/anal sex or use of an object.</li>
                <li>Penalty may be elevated to <strong>Reclusion Temporal</strong> depending on circumstances.</li>
            </ul>

            <h6>What to Do if Someone is Raped</h6>
            <ol>
                <li>Advise the victim to seek help from a counselor/therapist.</li>
                <li>Assist in securing safe and temporary shelter.</li>
                <li>Ensure evidence is safe and intact.</li>
                <li>Secure a medico-legal certificate.</li>
                <li>Support the victim if she decides to file a case.</li>
                <li>Help her prepare emotionally and legally.</li>
            </ol>

            <h6>Where to Get Help</h6>
            <p><strong>NBI:</strong> (02) 8525-6028</p>
            <p><strong>PNP Hotline:</strong> 117 / 911</p>
            <p><strong>DOJ-PAO:</strong> (02) 8929-9436 local 106, 107 or 159</p>
            <p><strong>DSWD Ugnayan Pag-asa:</strong> (02) 8734-8639</p>
            <p><strong>Philippine Commission on Women:</strong> (+632) 8735-1654</p>
        `
    },
    ra9208: {
        title: 'Republic Act 9208',
        subtitle: 'Anti-Trafficking in Persons Act of 2003, as amended by RA 10364',
        image: '<?php echo e(asset("images/ra9208.jpg")); ?>',
        pageLabel: 'Anti-Trafficking · RA 9208',
        content: `
            <h6>What is Trafficking in Persons (TIP)?</h6>
            <p>Trafficking in persons is an illegal act and a violation of human rights. Three elements must be present:</p>
            <ul>
                <li><strong>ACTS:</strong> Recruitment, transportation, transfer, harboring, or receipt of persons.</li>
                <li><strong>MEANS:</strong> Use of threat, force, coercion, fraud, deception, or abuse of power.</li>
                <li><strong>PURPOSE:</strong> Exploitation — prostitution, forced labor, slavery, or organ removal/sale.</li>
            </ul>

            <h6>Punishable Acts</h6>
            <ol>
                <li>
                    <strong>Acts of TIP</strong> — All acts where all three elements of TIP are present.<br>
                    <strong>Penalty:</strong> 20 years imprisonment and PHP 1–2 million fine.
                </li>
                <li>
                    <strong>Acts that Promote TIP</strong> — Knowingly facilitating TIP (fake documents, propaganda, etc.)<br>
                    <strong>Penalty:</strong> 15 years and PHP 500,000–1 million fine.
                </li>
                <li>
                    <strong>Use of Trafficked Persons</strong> — Buying or engaging services of trafficked persons.<br>
                    <strong>Penalty:</strong> 6–40 years and PHP 50,000–5 million fine.
                </li>
            </ol>

            <h6>Qualified TIP (Life Imprisonment)</h6>
            <p>The act is qualified when the trafficked person is a child, when committed by a syndicate, when the offender is a public official, or when the victim died or suffered serious injuries.</p>

            <h6>Protections for Trafficked Persons</h6>
            <ul>
                <li>Legal Protection</li>
                <li>Free Legal Assistance</li>
                <li>Right to Privacy and Confidentiality</li>
                <li>Witness Protection Program</li>
                <li>Victim Compensation Program</li>
            </ul>

            <h6>Who May File a Complaint?</h6>
            <ul>
                <li>The trafficked person or offended party</li>
                <li>Spouse, parents, legal guardians, siblings, children</li>
                <li>Any person with personal knowledge of the offense</li>
            </ul>

            <h6>Where to Call for Help</h6>
            <p><strong>IACAT Hotline:</strong> 1343</p>
            <p><strong>DOJ-IACAT:</strong> (02) 8525-2131</p>
            <p><strong>PNP Hotline:</strong> 117 / 911</p>
            <p><strong>NBI Anti-Human Trafficking:</strong> (02) 8521-9208</p>
            <p><strong>Bureau of Immigration:</strong> (02) 8524-3769</p>
            <p><strong>DSWD:</strong> (02) 8734-8639</p>
        `
    },
    ra9710: {
        title: 'Republic Act 9710',
        subtitle: 'Magna Carta of Women',
        image: '<?php echo e(asset("images/ra9710.jpg")); ?>',
        pageLabel: 'Magna Carta of Women · RA 9710',
        content: `
            <h6>What is RA 9710?</h6>
            <p>The Magna Carta of Women (MCW) of 2009 is a comprehensive women's human rights law that seeks to eliminate discrimination against women by recognizing, respecting, protecting, fulfilling and promoting the rights of Filipino women, especially those in the marginalized sectors.</p>

            <h6>What is Discrimination Against Women?</h6>
            <ul>
                <li>Any gender-based distinction, exclusion, or restriction which impairs or nullifies the recognition or exercise of human rights and fundamental freedoms by women.</li>
                <li>Any act or omission, including by law or policy, that directly or indirectly restricts women in recognition of their rights.</li>
                <li>A measure of general application that fails to provide mechanisms to offset sex or gender-based disadvantages.</li>
            </ul>

            <h6>Rights Guaranteed Under the MCW</h6>
            <ul>
                <li>Protection from all forms of violence, including those committed by the State.</li>
                <li>Protection and security in times of disaster and crisis.</li>
                <li>Participation and representation.</li>
                <li>Equal treatment before the law.</li>
                <li>Equal access in education, scholarships, and training.</li>
                <li>Non-discrimination in employment in the military, police, and similar services.</li>
                <li>Comprehensive health services and health information and education.</li>
                <li>Special leave of two (2) months with full pay for gynecological surgery.</li>
                <li>Equal rights in matters relating to marriage and family relations.</li>
            </ul>

            <h6>Rights of Women in Marginalized Sectors</h6>
            <ul>
                <li>Food security and resources for food production.</li>
                <li>Localized, accessible, secure, and affordable housing.</li>
                <li>Decent work standards and employment opportunities.</li>
                <li>Skills training and livelihood programs.</li>
                <li>Social protection to reduce poverty and vulnerability.</li>
                <li>Protection of Girl-Children against all forms of discrimination.</li>
            </ul>

            <h6>Penalties for Violations</h6>
            <p>Government agencies found in violation: the directly responsible person and agency head may be held liable under MCW and applicable laws. Private entities or individuals may be liable for damages.</p>

            <h6>Oversight Bodies</h6>
            <p><strong>Philippine Commission on Women (PCW)</strong> — overall monitoring body.</p>
            <p><strong>Commission on Human Rights (CHR)</strong> — acts as GAD Ombud.</p>
            <p><strong>Commission on Audit (COA)</strong> — conducts annual audits on GAD budgets.</p>
        `
    },
    hivaids: {
        title: 'HIV/AIDS Awareness',
        subtitle: 'Towards a Safe Tomorrow',
        image: '<?php echo e(asset("images/hivaids.jpg")); ?>',
        pageLabel: 'HIV/AIDS Awareness Guide',
        content: `
            <h6>What is HIV?</h6>
            <p>HIV (Human Immunodeficiency Virus) is a virus that attacks the immune system, weakening the body's ability to fight off infections and diseases. It primarily affects CD4 cells (T cells).</p>

            <h6>What is AIDS?</h6>
            <p>AIDS (Acquired Immunodeficiency Syndrome) is the final stage of HIV infection, where the immune system is severely damaged. Not everyone with HIV will develop AIDS if treated.</p>

            <h6>How is HIV Transmitted?</h6>
            <p>HIV is spread through blood, semen, vaginal fluids, rectal fluids, and breast milk — via unprotected sexual contact, sharing needles, blood transfusions, or from mother to child.</p>

            <h6>Can HIV Spread Through Casual Contact?</h6>
            <p>No. HIV cannot be spread through hugging, shaking hands, sharing food or drinks, air, water, or insect bites.</p>

            <h6>How to Prevent HIV</h6>
            <ul>
                <li>Use condoms consistently and correctly.</li>
                <li>Get tested and know your partner's HIV status.</li>
                <li>Take pre-exposure prophylaxis (PrEP) if at high risk.</li>
                <li>Use clean needles if injecting drugs.</li>
                <li>Ensure safe blood transfusions.</li>
            </ul>

            <h6>Treatment</h6>
            <p>The main treatment is antiretroviral therapy (ART), which reduces the viral load to undetectable levels — meaning the virus is not transmissible (U=U: Undetectable = Untransmittable).</p>

            <h6>Myths and Facts</h6>
            <ul>
                <li><strong>Myth:</strong> Mosquitoes can transmit HIV. <strong>Fact:</strong> They cannot.</li>
                <li><strong>Myth:</strong> HIV is a death sentence. <strong>Fact:</strong> Early treatment allows long, healthy lives.</li>
                <li><strong>Myth:</strong> You can tell if someone has HIV by looking at them. <strong>Fact:</strong> Testing is needed.</li>
                <li><strong>Myth:</strong> Only people with multiple partners are at risk. <strong>Fact:</strong> Anyone sexually active is at risk without precautions.</li>
            </ul>

            <h6>Situation in the Philippines</h6>
            <p>As of 2024, the Philippines reports a notable increase in HIV/AIDS cases — 3,410 new cases in the first quarter alone, with males having sex with males comprising 81% of cases. Almost half of new cases involve individuals aged 15–24.</p>

            <h6>Where to Get Help</h6>
            <p><strong>Department of Health (DOH)</strong> — National HIV, AIDS &amp; STI Prevention and Control Program</p>
            <p><strong>Research Institute for Tropical Medicine (RITM)</strong> — HIV reference and treatment support</p>
            <p><strong>Local hospitals and treatment hubs</strong> — Ask for HIV testing, counseling, and ART services</p>
        `
    },
    harassment: {
        title: 'Anti-Sexual Harassment Policy',
        subtitle: 'BOR Resolution No. 12, s. 2016 — Pangasinan State University',
        image: '<?php echo e(asset("images/antisexualharassmentpolicy.jpg")); ?>',
        pageLabel: 'Anti-Sexual Harassment Policy · PSU',
        content: `
            <h6>Legal Basis</h6>
            <p>Based on Section 4 of Republic Act No. 7877, An Act Declaring Sexual Harassment Unlawful in the Employment, Education, or Training Environment, PSU formulates and implements Anti-Sexual Harassment policies.</p>

            <h6>Affirmation of Policy</h6>
            <p>The University is committed to providing a secure and conducive learning and working environment free from sexual harassment, intimidation, and exploitation. All forms of sexual harassment are declared unlawful.</p>

            <h6>Sexual Harassment Defined</h6>
            <p>Sexual harassment is committed by an officer, faculty member, or employee who having authority demands, requests, or requires any sexual favor from another — regardless of whether such is accepted.</p>

            <h6>Forms of Sexual Harassment</h6>
            <ul>
                <li>Overt sexual advances</li>
                <li>Unwelcome or improper gestures of affection</li>
                <li>Request or demand for sexual favors</li>
                <li>Sexually explicit emails or text messages</li>
                <li>Stalking, cyber stalking, voyeurism, or recording of sexual images</li>
                <li>Unwanted touching, patting, or other physical contact</li>
                <li>Recurring comments about sexual prowess or behavior</li>
            </ul>

            <h6>Where Sexual Harassment Can Be Committed</h6>
            <ul>
                <li>In or outside office buildings or training sites</li>
                <li>At work or training-related social functions</li>
                <li>During work-related travel or conferences</li>
                <li>Over the telephone or electronic communication</li>
            </ul>

            <h6>Reporting Procedures</h6>
            <ol>
                <li>Report to immediate superior, Unit Head, CODI, or any University Official.</li>
                <li>Action/investigation commences within 15 days and concludes within 90 days.</li>
                <li>Both complainant and respondent receive written notice and may present information.</li>
                <li>All records and proceedings shall be confidential.</li>
            </ol>

            <h6>Penalties</h6>
            <p>Any official, faculty member, employee, or student found to have committed sexual harassment shall be subjected to disciplinary action up to discharge or dismissal.</p>

            <h6>Where to Get Help</h6>
            <p><strong>PSU:</strong> Lingayen, Pangasinan</p>
            <p><strong>DSWD Dagupan:</strong> (075) 523-3844</p>
            <p><strong>NBI VAWDC Dagupan:</strong> (075) 515-5208</p>
            <p><strong>Civil Service Commission:</strong> (075) 542-6641</p>
            <p><strong>Region 1 Medical Center:</strong> (075) 515-3815</p>
        `
    },
    codi: {
        title: 'Committee on Decorum and Investigation',
        subtitle: 'CODI — BOR Resolution No. 12, s. 2016',
        image: '<?php echo e(asset("images/codi.jpg")); ?>',
        pageLabel: 'CODI · Decorum &amp; Investigation',
        content: `
            <h6>Legal Basis</h6>
            <p>CODI is created in compliance with the Anti-Sexual Harassment Act of 1995 (RA 7877), CSC Memo Circular No. 17, and Anti-Bullying of 2013 (RA 10627).</p>

            <h6>Affirmation of Policy</h6>
            <p>The University shall ensure policies and mechanisms to prevent and punish sexual harassment, bullying, and discrimination.</p>

            <h6>Functions of CODI</h6>
            <ul>
                <li>Receive and investigate complaints of sexual harassment, bullying, and discrimination against women.</li>
                <li>Submit findings and recommendations to the disciplining authority.</li>
                <li>Lead discussions to increase awareness and prevent incidents.</li>
                <li>Conduct activities that promote a safe environment in school campuses.</li>
            </ul>

            <h6>Composition of CODI</h6>
            <p><strong>University Level:</strong> Representative from University administration (Chair), Federated Faculty Representative, Federated Student Representative, HR Representative, University Guidance Counselor, Campus Executive Director, Legal Officer, GAD Executive Director.</p>
            <p><strong>Campus Level:</strong> Representative from Campus administration (Chair), Campus Faculty Club President, SSC President, Campus Administrative Officer, Campus Guidance Counselor, GAD Coordinator.</p>

            <h6>Procedural Requirements</h6>
            <p>The complaint must be written, signed, and sworn. It should contain:</p>
            <ol>
                <li>Full name and address of the complainant</li>
                <li>Full name, address, and position of the respondent</li>
                <li>Brief statement of relevant facts</li>
                <li>Supporting evidence, if any</li>
                <li>Certificate of non-forum shopping</li>
            </ol>

            <h6>Timeline</h6>
            <ul>
                <li>Preliminary investigation starts within 5 days and concludes within 15 working days.</li>
                <li>If prima facie case exists, formal charge issued within 3 working days.</li>
                <li>Respondent answers under oath within 72 hours.</li>
                <li>Formal investigation held within 5–10 days from receipt of answer.</li>
                <li>CODI submits report within 15 days; decision rendered within 30 days.</li>
            </ul>

            <h6>Appeals</h6>
            <p>One motion for reconsideration may be filed within fifteen (15) days. Appeals may be filed before the Civil Service Commission (CSC).</p>
        `
    },
    nonexpulsion: {
        title: 'Student Pregnancy Policy',
        subtitle: 'Non-Expulsion of Student and Faculty Due to Pregnancy — BOR Resolution No. 12, s. 2016',
        image: '<?php echo e(asset("images/nonexpulsion.jpg")); ?>',
        pageLabel: 'Student Pregnancy Policy · PSU',
        content: `
            <h6>Legal Basis</h6>
            <p>Pursuant to Republic Act 9710 (Magna Carta of Women), PSU formulated policies to prevent expulsion of women students and faculty due to pregnancy and to eliminate discrimination in education.</p>

            <h6>Affirmation of Policy</h6>
            <p>PSU shall maintain a fair, humane, and safe learning and working environment for all. Expulsion, non-readmission, prohibition of enrollment, and related discrimination due to pregnancy are condemned and considered illegal.</p>

            <h6>Key Definitions</h6>
            <ol>
                <li><strong>Non-Expulsion:</strong> Allowing a female student to continue her degree despite pregnancy.</li>
                <li><strong>Non-Readmission:</strong> Not allowing a female student to re-enter school due to pregnancy.</li>
            </ol>

            <h6>Declaration of University Policies</h6>
            <ol>
                <li>Equal access and elimination of discrimination in education and scholarships.</li>
                <li>Expulsion, non-readmission, and related discrimination due to pregnancy are outlawed.</li>
                <li>Increasing women in key University positions to achieve a 50-50 gender balance.</li>
                <li>Provision of two (2) months special leave with full pay for gynecological surgery.</li>
            </ol>

            <h6>Policy Guidelines</h6>
            <ul>
                <li>No woman student or faculty shall be discriminated against due to pregnancy, parenting, or ability to become pregnant.</li>
                <li>Non-refusal to hire women applicants due to pregnancy-related conditions.</li>
                <li>Non-demotion, denial of promotion, or dismissal due to pregnancy.</li>
                <li>Pregnancy-related benefits shall not be limited to married employees.</li>
            </ul>

            <h6>Procedures for Pregnancy Discrimination</h6>
            <ol>
                <li>Document each incident as soon as reported/witnessed.</li>
                <li>Discuss the issue with HR personnel or the equal employment officer.</li>
                <li>File a formal complaint with the Grievance Committee or CODI.</li>
            </ol>

            <h6>Where to Get Help</h6>
            <p><strong>PSU:</strong> (075) 206-0802</p>
            <p><strong>DSWD Dagupan:</strong> (075) 523-3844</p>
            <p><strong>Civil Service Commission:</strong> (075) 542-6641</p>
            <p><strong>NBI VAWDC Dagupan:</strong> (075) 515-5208</p>
            <p><strong>Region 1 Medical Center:</strong> (075) 515-3815</p>
        `
    }
};

let currentBook = null;

function openBook(key) {
    const book = BOOKS[key];
    if (!book) return;
    currentBook = key;

    document.getElementById('pageLeftTitle').textContent = book.title;
    document.getElementById('pageLeftSubtitle').textContent = book.subtitle;
    document.getElementById('pageLeftBg').style.backgroundImage = `url('${book.image}')`;
    document.getElementById('pageRightTitle').textContent = book.title;
    document.getElementById('pageRightSubtitle').textContent = book.subtitle;
    document.getElementById('pageRightBody').innerHTML = book.content;
    document.getElementById('pageNum').textContent = book.pageLabel;

    const overlay = document.getElementById('bookOverlay');
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';

    // page turn animation
    const pageRight = document.querySelector('.page-right');
    const anim = document.createElement('div');
    anim.className = 'page-turn-anim';
    pageRight.appendChild(anim);
    setTimeout(() => { if (anim.parentNode) anim.parentNode.removeChild(anim); }, 600);
}

function closeBook() {
    const overlay = document.getElementById('bookOverlay');
    overlay.classList.remove('active');
    document.body.style.overflow = '';
    currentBook = null;
}

function handleOverlayClick(e) {
    if (e.target === document.getElementById('bookOverlay')) {
        closeBook();
    }
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeBook();
});
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ANGELINE AMBROSIO\sinag\resources\views\student\wellness.blade.php ENDPATH**/ ?>