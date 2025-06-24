/**
 * adblock-detect.js
 * ------------------------------------------------------------
 * A blocker on this site usually strips or hides every Google-Ad
 * element (<amp-ad>, <ins class="adsbygoogle">, the iframes they
 * spawn, etc.).  
 *
 * ➜ Strategy
 *    1.  After DOMContentLoaded we look for any **visible**
 *        Google-ad node that already exists.
 *    2.  If not found, we attach a MutationObserver that watches
 *        the whole DOM for nodes of the same kinds.
 *    3.  We stop observing as soon as we see one **visible** ad,
 *        or after 10 s (MAX_WAIT) – whichever happens first.
 *    4.  If the timer fires with zero ads detected, we display a
 *        full-page overlay asking the visitor to disable the
 *        blocker and refresh.
 */

/* ───────────────────────────────────────────────────── CONFIG */
const MAX_WAIT = 10_000;          // ms – total time budget
const GOOGLE_AD_SELECTORS = `amp-img`;

/* ───────────────────────────────────────────── HELPERS */
function isVisible(el) {
    if (!el) return false;
    const style = getComputedStyle(el);
    return style && style.display !== 'none' &&
        style.visibility !== 'hidden' &&
        el.offsetHeight > 0 && el.offsetWidth > 0;
}

function hasVisibleAd() {
    const ads = document.querySelectorAll(GOOGLE_AD_SELECTORS);
    return Array.from(ads).some(isVisible);
}

function nodeIsAd(node) {
    if (node.nodeType !== 1) return false;
    if (node.matches(GOOGLE_AD_SELECTORS) && isVisible(node)) return true;
    /* <iframe> inside other ad containers */
    if (node.tagName === 'IFRAME' && /google_ads_iframe/.test(node.src)) return true;
    return false;
}

/* ──────────────────────────────────────────── OVERLAY UI */
function showOverlay() {
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

/* ──────────────────────────────────────────── BOOTSTRAP */
(function () {
    let observer = null;
    let timer = null;

    function stopWatching() {
        if (observer) observer.disconnect();
        if (timer) clearTimeout(timer);
    }

    function startDetection() {
        /* Step 1 – instant check */
        if (hasVisibleAd()) return;

        /* Step 2 – live watch */
        observer = new MutationObserver(mutations => {
            for (const m of mutations) {
                for (const node of m.addedNodes) {
                    if (nodeIsAd(node)) {
                        stopWatching();          // ads are here → exit
                        return;
                    }
                }
            }
        });
        observer.observe(document.documentElement,
            { childList: true, subtree: true });

        /* Step 3 – timeout */
        timer = setTimeout(() => {
            stopWatching();
            if (!hasVisibleAd()) showOverlay();  // after 10 s still none
        }, MAX_WAIT);
    }

    document.addEventListener('DOMContentLoaded', startDetection);
})();
