/**
 * InnoLearn Eye-Tracking Navigation
 * Uses WebGazer.js to allow gaze-based clicking and scrolling.
 */
let eyeNavEnabled = false;
let dwellTimer = null;
let lastTarget = null;
const DWELL_TIME = 1000; // 1 second to click
const SCROLL_SPEED = 18;
const SCROLL_THRESHOLD_V = 150;
const SCROLL_THRESHOLD_H = 80;

// Inject CSS to ensure WebGazer doesn't block clicks and to style the gaze cursor
const style = document.createElement('style');
style.innerHTML = `
    #webgazerVideoFeed, #webgazerVideoCanvas, #webgazerFaceOverlay, #webgazerFaceFeedbackBox {
        pointer-events: none !important;
        z-index: 9999 !important;
    }
    #gaze-cursor {
        position: fixed;
        width: 30px;
        height: 30px;
        border: 2px solid rgba(79, 70, 229, 0.4);
        border-radius: 50%;
        background: rgba(79, 70, 229, 0.05);
        pointer-events: none !important; /* Critical: don't block elementFromPoint */
        z-index: 10000;
        transition: transform 0.1s ease;
        display: none;
    }
    #gaze-cursor-inner {
        position: absolute;
        top: 50%; left: 50%;
        width: 0%; height: 0%;
        background: rgba(79, 70, 229, 0.5);
        border-radius: 50%;
        transform: translate(-50%, -50%);
        transition: width 0.1s linear, height 0.1s linear;
    }
    .calib-help {
        position: fixed;
        top: 100px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(30, 27, 75, 0.95);
        color: white;
        padding: 1.25rem 2.5rem;
        border-radius: 16px;
        z-index: 10002;
        text-align: center;
        box-shadow: 0 15px 40px rgba(0,0,0,0.4);
        display: none;
        border: 1px solid rgba(255,255,255,0.1);
        font-family: 'Inter', sans-serif;
        font-weight: 500;
    }
    .calib-point {
        position: fixed;
        width: 35px;
        height: 35px;
        background: #f43f5e;
        border-radius: 50%;
        z-index: 2147483647 !important; /* Maximum possible z-index */
        cursor: pointer;
        border: 4px solid white;
        box-shadow: 0 0 20px rgba(244, 63, 94, 0.8);
        display: none;
        transition: all 0.3s ease;
        animation: pulse-calib 1.5s infinite;
    }
    @keyframes pulse-calib {
        0% { transform: scale(1); box-shadow: 0 0 10px rgba(244, 63, 94, 0.8); }
        50% { transform: scale(1.2); box-shadow: 0 0 25px rgba(244, 63, 94, 1); }
        100% { transform: scale(1); box-shadow: 0 0 10px rgba(244, 63, 94, 0.8); }
    }
    #scroll-indicator-top, #scroll-indicator-bottom {
        position: fixed;
        left: 0;
        width: 100%;
        height: 15px;
        background: rgba(99, 102, 241, 0.15);
        z-index: 20000;
        display: none;
        pointer-events: none;
        transition: background 0.3s;
    }
    #scroll-indicator-top { top: 0; border-bottom: 1px solid rgba(99, 102, 241, 0.2); }
    #scroll-indicator-bottom { bottom: 0; border-top: 1px solid rgba(99, 102, 241, 0.2); }
`;
document.head.appendChild(style);

// Create Indicators
const iTop = document.createElement('div'); iTop.id = 'scroll-indicator-top'; document.body.appendChild(iTop);
const iBottom = document.createElement('div'); iBottom.id = 'scroll-indicator-bottom'; document.body.appendChild(iBottom);

// Create message element
const calibHelp = document.createElement('div');
calibHelp.className = 'calib-help';
calibHelp.innerHTML = '🎯 <strong>Calibration :</strong> Cliquez sur les 9 points rouges pour entraîner l\'IA. <button id="skip-calib" style="margin-left:15px; padding:5px 12px; border-radius:8px; border:none; background:#4F46E5; color:white; cursor:pointer; font-weight:bold; font-size:0.8rem;">Passer la calibration</button>';
document.body.appendChild(calibHelp);

// Handle Skip button
document.addEventListener('click', function (e) {
    if (e.target.id === 'skip-calib') {
        document.querySelectorAll('.calib-point').forEach(p => p.style.display = 'none');
        calibHelp.innerHTML = '✅ <strong>Prêt !</strong> Navigation activée.';
        setTimeout(() => calibHelp.style.display = 'none', 3000);
    }
});

// Create gaze cursor element
const gazeCursor = document.createElement('div');
gazeCursor.id = 'gaze-cursor';
gazeCursor.innerHTML = '<div id="gaze-cursor-inner"></div>';
document.body.appendChild(gazeCursor);

// Create calibration points
const pointsArr = [
    { t: '20%', l: '20%' }, { t: '20%', l: '50%' }, { t: '20%', l: '80%' },
    { t: '50%', l: '20%' }, { t: '50%', l: '50%' }, { t: '50%', l: '80%' },
    { t: '80%', l: '20%' }, { t: '80%', l: '50%' }, { t: '80%', l: '80%' }
];

function createPoints() {
    // Clear existing points if any
    document.querySelectorAll('.calib-point').forEach(p => p.remove());

    pointsArr.forEach((p, i) => {
        const div = document.createElement('div');
        div.className = 'calib-point';
        div.style.top = p.t;
        div.style.left = p.l;
        div.addEventListener('click', function (e) {
            e.stopPropagation();
            this.style.background = '#10b981';
            this.style.transform = 'scale(0.1)';
            setTimeout(() => {
                this.style.display = 'none';
                checkCalibrationDone();
            }, 200);
        });
        document.body.appendChild(div);
    });
}
createPoints();

function checkCalibrationDone() {
    const remaining = Array.from(document.querySelectorAll('.calib-point')).filter(p => p.style.display === 'block').length;
    if (remaining === 0) {
        calibHelp.innerHTML = '✅ <strong>Prêt !</strong> Navigation activée.';
        setTimeout(() => calibHelp.style.display = 'none', 3000);
    }
}

// Use delegation to handle buttons loaded dynamically or via sub-templates
document.addEventListener('click', function (e) {
    const btn = e.target.closest('#eye-nav-toggle');
    if (!btn) return;

    eyeNavEnabled = !eyeNavEnabled;

    if (eyeNavEnabled) {
        btn.classList.add('active');
        btn.innerHTML = '<span class="status-dot"></span><i class="fas fa-spinner fa-spin"></i> Initialisation...';
        gazeCursor.style.display = 'block';
        calibHelp.style.display = 'block';
        iTop.style.display = 'block';
        iBottom.style.display = 'block';

        // Reset and show calibration points
        createPoints(); // Always recreate to be sure
        document.querySelectorAll('.calib-point').forEach(p => p.style.display = 'block');

        startEyeTracking();

        setTimeout(() => {
            if (eyeNavEnabled) {
                btn.innerHTML = '<span class="status-dot"></span><i class="fas fa-eye"></i> Nav ON';
            }
        }, 2000);
    } else {
        btn.classList.remove('active');
        btn.innerHTML = '<span class="status-dot"></span><i class="fas fa-eye"></i> Eye Nav';
        gazeCursor.style.display = 'none';
        calibHelp.style.display = 'none';
        iTop.style.display = 'none';
        iBottom.style.display = 'none';
        document.querySelectorAll('.calib-point').forEach(p => p.style.display = 'none');

        stopEyeTracking();
    }
});

function startEyeTracking() {
    if (typeof webgazer === 'undefined') {
        alert("WebGazer is not loaded! Please check your connection.");
        return;
    }

    console.log("WebGazer starting...");

    webgazer.setGazeListener(function (data, elapsedTime) {
        if (data == null || !eyeNavEnabled) return;

        const x = data.x;
        const y = data.y;

        // Visual cursor
        gazeCursor.style.left = (x - 15) + 'px';
        gazeCursor.style.top = (y - 15) + 'px';

        handleDwellClick(x, y);
        handleScrolling(x, y);
    }).begin();

    webgazer.showVideoPreview(true)
        .showPredictionPoints(true) // Re-enable for debugging visibility
        .applyKalmanFilter(true);
}

function stopEyeTracking() {
    if (typeof webgazer !== 'undefined') {
        webgazer.pause();
    }
    const elementsToHide = ['webgazerVideoFeed', 'webgazerVideoCanvas', 'webgazerFaceOverlay', 'webgazerFaceFeedbackBox'];
    elementsToHide.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.style.display = 'none';
    });
}

function handleDwellClick(x, y) {
    // Offset slightly to avoid the cursor itself
    const element = document.elementFromPoint(x, y);
    if (!element) return;

    const clickable = element.closest('a, button, [onclick], .btn, .project-card, .nav-item');
    const inner = document.getElementById('gaze-cursor-inner');

    if (clickable && clickable === lastTarget) {
        if (!dwellTimer) {
            console.log("Dwell started on:", clickable);
            let start = Date.now();
            dwellTimer = setInterval(() => {
                let elapsed = Date.now() - start;
                let percent = Math.min((elapsed / DWELL_TIME) * 100, 100);
                inner.style.width = percent + '%';
                inner.style.height = percent + '%';

                if (elapsed >= DWELL_TIME) {
                    console.log("Dwell CLICK triggered!");
                    clickable.click();
                    clearInterval(dwellTimer);
                    dwellTimer = null;
                    inner.style.width = '0%'; inner.style.height = '0%';
                    clickable.style.outline = "5px solid #4F46E5";
                    setTimeout(() => clickable.style.outline = "none", 500);
                }
            }, 50);
        }
    } else {
        if (dwellTimer) {
            clearInterval(dwellTimer);
            dwellTimer = null;
        }
        inner.style.width = '0%'; inner.style.height = '0%';
        lastTarget = clickable;
    }
}

function handleScrolling(x, y) {
    const vh = window.innerHeight;
    const threshold = 180; // Larger zone for easier detection

    // Vertical
    if (y < threshold) {
        window.scrollBy(0, -SCROLL_SPEED);
        iTop.style.background = 'rgba(99, 102, 241, 0.6)';
        console.log("Scrolling UP");
    } else if (y > vh - threshold) {
        window.scrollBy(0, SCROLL_SPEED);
        iBottom.style.background = 'rgba(99, 102, 241, 0.6)';
        console.log("Scrolling DOWN");
    } else {
        iTop.style.background = 'rgba(99, 102, 241, 0.15)';
        iBottom.style.background = 'rgba(99, 102, 241, 0.15)';
    }

    // Horizontal
    if (x < SCROLL_THRESHOLD_H) {
        window.scrollBy(-SCROLL_SPEED, 0);
    } else if (x > vw - SCROLL_THRESHOLD_H) {
        window.scrollBy(SCROLL_SPEED, 0);
    }
}

let backTimer = null;
function handleHorizontalActions(x) {
    if (x < 30) {
        if (!backTimer) {
            backTimer = setTimeout(() => {
                window.history.back();
            }, 2500);
        }
    } else {
        clearTimeout(backTimer);
        backTimer = null;
    }
}
