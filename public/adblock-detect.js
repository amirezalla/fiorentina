/**
 * adblock-detect.js
 * ------------------------------------------------------------
 * Detects ad-blockers and, if one is active, covers the page
 * with an overlay that asks the visitor to disable / whitelist
 * and then refresh.
 *
 * How detection works
 * -------------------
 * 1. Inject a “bait” element whose class names look like ads.
 * 2. Give it a real size (1 px × 1 px) so its offsetHeight
 *    should be > 0 when not blocked.
 * 3. If a blocker hides it (display:none / visibility:hidden /
 *    removed from the flow / height=0) we consider ads blocked.
 */

(function () {
    /**
     * Returns `true` when an ad-blocker (or custom CSS) hides
     * our bait element.
     */
    function isAdBlocked() {
        const bait = document.createElement('div');
        bait.className = 'adsbox ad-banner ad-unit'; // common keywords
        bait.style.cssText = `
            position:absolute;
            left:-9999px;
            width:1px;
            height:1px;
            pointer-events:none;`;
        bait.innerHTML = '&nbsp;';  // ensures measurable height
        document.body.appendChild(bait);

        const style = window.getComputedStyle(bait);
        const blocked =
            style.display === 'none' ||
            style.visibility === 'hidden' ||
            bait.offsetParent === null || // ancestor hidden
            bait.offsetHeight === 0;

        document.body.removeChild(bait);
        return blocked;
    }

    /**
     * Builds and shows the full-page overlay.
     */
    function showOverlay() {
        const overlay = document.createElement('div');
        overlay.id = 'adblock-overlay';
        overlay.innerHTML = `
            <div id="adblock-message">
                <h2>Please disable your ad&nbsp;blocker</h2>
                <p>
                    Ads keep our content free.<br>
                    Pause your blocker and refresh the page to continue reading.
                </p>
                <button id="adblock-refresh" type="button">
                    I’ve disabled it &nbsp;⟳
                </button>
            </div>`;
        /* Basic styling — tweak as needed */
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

        /* Lock scrolling and show */
        document.body.style.overflow = 'hidden';
        document.body.appendChild(overlay);
    }

    /* Run the check once the DOM is ready */
    document.addEventListener('DOMContentLoaded', () => {
        if (isAdBlocked()) showOverlay();
    });
})();
