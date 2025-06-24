/**
 * adblock-detect.js  –  network-level Google-Ads check
 * ------------------------------------------------------------
 * 1. Inject <script src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js">
 * 2.  - onload  →  ads allowed   →  do nothing
 *    - onerror →  request blocked → show overlay
 * 3. Fallback: after 7 s, if neither event fired, treat as blocked
 */

(function () {
    /* ——————————————————————————————— CONFIG */
    const TEST_URL = 'https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js';
    const MAX_WAIT = 7000;                     // 7 seconds
    let resolved = false;                    // has the test finished?

    /* ——————————————————————————————— OVERLAY */
    function showOverlay() {
        if (resolved) return;                    // in case both timer & onerror race
        resolved = true;

        const overlay = document.createElement('div');
        overlay.id = 'adblock-overlay';
        overlay.innerHTML = `
            <div id="adblock-message">
                <h2>Please disable your ad&nbsp;blocker</h2>
                <p>
                    Ads keep our content free.<br>
                    Pause / whitelist the blocker and refresh
                    the page to continue reading.
                </p>
                <button id="adblock-refresh" type="button">
                    I’ve disabled it &nbsp;⟳
                </button>
            </div>`;
        Object.assign(overlay.style, {
            position: 'fixed',
            inset: 0,
            background: 'rgba(0,0,0,.85)',
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center',
            zIndex: 99999,
            color: '#fff',
            textAlign: 'center',
            padding: '1rem',
            fontFamily: 'system-ui, sans-serif',
        });
        const msg = overlay.querySelector('#adblock-message');
        Object.assign(msg.style, {
            maxWidth: '420px',
            background: '#222',
            borderRadius: '8px',
            padding: '2rem',
            boxShadow: '0 0 20px rgba(0,0,0,.4)',
        });
        const btn = overlay.querySelector('#adblock-refresh');
        Object.assign(btn.style, {
            marginTop: '1.5rem',
            padding: '.5rem 1rem',
            fontSize: '1rem',
            border: 0,
            borderRadius: '4px',
            cursor: 'pointer',
        });
        btn.addEventListener('click', () => location.reload());

        document.body.style.overflow = 'hidden';
        document.body.appendChild(overlay);
    }

    /* ——————————————————————————————— DETECTION */
    function runTest() {
        const s = document.createElement('script');
        s.src = TEST_URL;
        s.async = true;

        /* success — AdSense script loaded */
        s.onload = () => { resolved = true;  /* nothing to do */ };

        /* blocked — most ad-blockers abort the request or rewrite the URL */
        s.onerror = showOverlay;

        document.head.appendChild(s);

        /* fallback timer — catches cases where the request is stalled */
        setTimeout(() => {
            if (!resolved) showOverlay();
        }, MAX_WAIT);
    }

    /* ——————————————————————————————— BOOT */
    // Wait for DOM; network can start earlier but this guarantees <head> exists.
    document.addEventListener('DOMContentLoaded', runTest);
})();
