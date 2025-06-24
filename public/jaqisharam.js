/**
 * adblock-detect.js  –  controllo AdSense a livello di rete
 * ------------------------------------------------------------
 * 1. Inietta <script src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js">
 * 2.  - onload  →  gli annunci sono consentiti   →  non fare nulla
 *    - onerror →  richiesta bloccata            →  mostra overlay
 * 3. Se entro 7 s non arriva né onload né onerror, considera bloccato
 */

(function () {
    /* ——————————————————————————————— CONFIGURAZIONE */
    const TEST_URL = 'https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js';
    const MAX_WAIT = 7000;                    // 7 secondi
    let resolved = false;                   // il test è terminato?

    /* ——————————————————————————————— OVERLAY */
    function showOverlay() {
        if (resolved) return;                 // evita race tra timer e onerror
        resolved = true;

        const overlay = document.createElement('div');
        overlay.id = 'adblock-overlay';
        overlay.innerHTML = `
            <div id="adblock-message">
                <h2>Disattiva il tuo ad-blocker</h2>
                <p>
                    La pubblicità ci permette di offrirti i contenuti gratuitamente.<br>
                    Metti in pausa o aggiungi il sito alla whitelist e ricarica la pagina
                    per continuare a leggere.
                </p>
                <button id="adblock-refresh" type="button">
                    L’ho disattivato &nbsp;⟳
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

    /* ——————————————————————————————— TEST DI Rete */
    function runTest() {
        const s = document.createElement('script');
        s.src = TEST_URL;
        s.async = true;

        /* Successo: script AdSense caricato */
        s.onload = () => { resolved = true; /* niente overlay */ };

        /* Bloccato: onerror scatterà quasi sempre con un ad-block */
        s.onerror = showOverlay;

        document.head.appendChild(s);

        /* Timer di fallback */
        setTimeout(() => {
            if (!resolved) showOverlay();
        }, MAX_WAIT);
    }

    /* ——————————————————————————————— AVVIO */
    document.addEventListener('DOMContentLoaded', runTest);
})();
