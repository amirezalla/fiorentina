/**
 * adblock-detect.js
 * ------------------------------------------------------------
 * On this site Google Ads are inserted as <amp-img …>.
 * If, after 7 s, **no** such element is present we assume an
 * ad-blocker is hiding them (or the request was killed) and we
 * cover the page with an overlay that asks the visitor to pause
 * / whitelist the blocker and refresh.
 */

(function () {
    /* ──────────────────────────────────────────────────────────
     * CONFIG
     * ──────────────────────────────────────────────────────── */
    const CHECK_DELAY = 7000; // ms – give the ads script time to run

    /* ──────────────────────────────────────────────────────────
     * DETECTION
     * ──────────────────────────────────────────────────────── */
    function googleAdsMissing() {
        /* You can add / tweak domains if you serve ads via others */
        const selector =
            'amp-img[src*="googleads"],' +
            'amp-img[src*="googlesyndication"],' +
            'amp-img[src*="gdoubleclick"]';

        return !document.querySelector(selector);
    }

    /* ──────────────────────────────────────────────────────────
     * OVERLAY UI
     * ──────────────────────────────────────────────────────── */
    function showOverlay() {
        const overlay = document.createElement('div');
        overlay.id = 'adblock-overlay';
        overlay.innerHTML = `
            <div id="adblock-message">
                <h2>Please disable your ad&nbsp;blocker</h2>
                <p>
                    Ads keep our content free.<br>
                    Pause your blocker and refresh the page
                    to continue reading.
                </p>
                <button id="adblock-refresh" type="button">
                    I’ve disabled it &nbsp;⟳
                </button>
            </div>`;

        /* Basic, dark overlay – tweak as desired */
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

        /* Lock the page behind the overlay */
        document.body.style.overflow = 'hidden';
        document.body.appendChild(overlay);
    }

    /* ──────────────────────────────────────────────────────────
     * BOOT
     * ──────────────────────────────────────────────────────── */
    document.addEventListener('DOMContentLoaded', () => {
        setTimeout(() => {
            if (googleAdsMissing()) showOverlay();
        }, CHECK_DELAY);
    });
})();
