/**
 * adblock-detect.js  –  Google-Ads guard
 * ------------------------------------------------------------
 * Google AdSense creatives are injected on this site as <amp-img …>.
 * If, 7 seconds after window.onload, the DOM contains **zero**
 * <amp-img> nodes we assume an ad-blocker (or tracking-protection)
 * removed them and we lock the page behind an overlay.
 */

(function () {
    /* ——————————————————————————————————————————————————— CONFIG */
    const CHECK_DELAY = 7000;          // ms – wait 7 s after window.onload

    /* ———————————————————————————————————————————— DETECTION LOGIC */
    function adsAreMissing() {
        return document.getElementsByTagName('amp-img').length === 0;
    }

    /* —————————————————————————————————————————————— OVERLAY UI */
    function showOverlay() {
        const overlay = document.createElement('div');
        overlay.id = 'adblock-overlay';
        overlay.innerHTML = `
            <div id="adblock-message">
                <h2>Please disable your ad&nbsp;blocker</h2>
                <p>
                    Ads keep our content free.<br>
                    Pause or whitelist the blocker and refresh
                    the page to continue reading.
                </p>
                <button id="adblock-refresh" type="button">
                    I’ve disabled it &nbsp;⟳
                </button>
            </div>`;

        /* minimal styling – customise as you wish */
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

        /* stop scrolling and show the overlay */
        document.body.style.overflow = 'hidden';
        document.body.appendChild(overlay);
    }

    /* —————————————————————————————————————————————— BOOTSTRAP */
    window.addEventListener('load', () => {
        setTimeout(() => {
            if (adsAreMissing()) showOverlay();
        }, CHECK_DELAY);
    });
})();
