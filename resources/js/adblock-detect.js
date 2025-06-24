/**
 * Very small ad-block detector
 * ------------------------------------------------------------
 * 1. Inserts a fake ad element (`<div class="adsbox">`)
 * 2. In most blockers that element gets hidden (height → 0)
 * 3. If hidden, show a full-page overlay that forces the user
 *    to pause / whitelist ad-block and refresh.
 */
(function () {
    // Helper that returns true when the fake ad is blocked
    function isAdBlocked() {
        const bait = document.createElement('div');
        bait.className = 'adsbox';
        bait.style.position = 'absolute';
        bait.style.left = '-9999px';
        document.body.appendChild(bait);

        const blocked = bait.offsetHeight === 0;
        document.body.removeChild(bait);
        return blocked;
    }

    // Builds the overlay HTML & CSS on the fly
    function showOverlay() {
        const overlay = document.createElement('div');
        overlay.id = 'adblock-overlay';
        overlay.innerHTML = `
            <div id="adblock-message">
                <h2>Please disable your ad&nbsp;blocker</h2>
                <p>Ads keep our content free.  
                   Pause your blocker and refresh the page
                   to continue reading.</p>
                <button id="adblock-refresh" type="button">I’ve disabled it &nbsp;⟳</button>
            </div>
        `;
        // Simple styling – tweak as you like
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

        document.body.style.overflow = 'hidden';   // stop scrolling
        document.body.appendChild(overlay);
    }

    // Wait for DOM, then test
    document.addEventListener('DOMContentLoaded', () => {
        if (isAdBlocked()) showOverlay();
    });
})();
