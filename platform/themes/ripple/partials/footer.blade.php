</div>
<style>
    .payin-nav {
        display: flow;
        text-align: center;
    }

    .payin-nav a {
        color: white;
        text-decoration: none;
        font-size: .95rem;
        transition: color .15s;
        margin-right: 10px;
    }

    .payin-nav a:hover {
        color: white;
    }
</style>
<footer class="page-footer">
    <div class="container" style="padding-top: 20px;">
        <div class="row">
            <div class="col-12 col-md-3 mx-auto mb-3">
                <a href="{{ BaseHelper::getHomepageUrl() }}" class="page-logo">
                    {{ Theme::getLogoImage(['height' => 70]) }}
                </a>
            </div>
            <ul class="footer__options">
                {{-- Facebook --}}
                <li class="footer__option">
                    <a href="https://www.facebook.com/laviola.it" class="flex" aria-label="Facebook" target="_blank"
                        rel="noopener">
                        {!! BaseHelper::renderIcon('ti ti-brand-facebook') !!}
                    </a>
                </li>

                {{-- X / Twitter --}}
                <li class="footer__option">
                    <a href="https://twitter.com/laviola_it" class="flex" aria-label="X (Twitter)" target="_blank"
                        rel="noopener">
                        {{-- Su Tabler Icons ≥ v2 il glifo è “brand-x”; se usi una versione precedente, cambia in “brand-twitter” --}}
                        {!! BaseHelper::renderIcon('ti ti-brand-x') !!}
                    </a>
                </li>

                {{-- Instagram --}}
                <li class="footer__option">
                    <a href="https://www.instagram.com/laviola_it" class="flex" aria-label="Instagram" target="_blank"
                        rel="noopener">
                        {!! BaseHelper::renderIcon('ti ti-brand-instagram') !!}
                    </a>
                </li>

                {{-- YouTube --}}
                <li class="footer__option">
                    <a href="https://www.youtube.com/channel/UC0LrzClScAKjQHizcA1IWkw" class="flex"
                        aria-label="YouTube" target="_blank" rel="noopener">
                        {!! BaseHelper::renderIcon('ti ti-brand-youtube') !!}
                    </a>
                </li>
            </ul>

            <nav class="payin-nav my-3">
                <a style="margin-right: 10px;" href="/redazione">Redazione</a>
                <a style="margin-right: 10px;" href="/contatti">Contatti</a>
                <a style="margin-right: 10px;" href="/cookie-policy">Cookie Policy</a>
            </nav>

            <div class="col-12 footer__text">
                <p class="mb-1 text-center">Pubblicazione iscritta nel registro della stampa del Tribunale di Firenze
                    con il n. 5050/01 del 27 apr 2001. Partita IVA 06783020966.</p>
                <p class="mb-1 text-center">Direttore responsabile: Niccolò Misul.</p>
                <p class="text-center">Service redazionale a cura di C&C Media Srl</p>
            </div>
        </div>
    </div>
</footer>
<div id="back2top">
    {!! BaseHelper::renderIcon('ti ti-arrow-narrow-up') !!}
</div>

{!! Theme::footer() !!}


<div class="container">

</div>


<!--<script>
    $(document).ready(async function() {
        // Define the images with Bootstrap classes and custom styling
        var leftImage =
            '<div class="col-6 d-flex flex-row"><img src="https://laviola.collaudo.biz/storage/16462360066530278727.gif" class="float-left d-none d-sm-block" alt="Left Image"></div>';
        var rightImage =
            '<div class="col-6 d-flex flex-row-reverse"><img src="https://laviola.collaudo.biz/storage/6357840656918928791.gif" class="float-right d-none d-sm-block" alt="Right Image"></div>';

        var row = '<div class="container mt-20"><div class="row">' + leftImage + rightImage +
            '</div></div>';

        var hero =
            '<div class="col-12 d-flex justify-content-center"><img src="https://laviola.collaudo.biz/storage/728x200-la-viola-ecobonus.gif" class="float-right d-none d-sm-block" alt="Right Image"></div>';
        var row1 = '<div class="container"><div class="row">' + hero + '</div></div>';

        $('.recent-posts').before(row1);
        $('.page-header').after(row);
    });
</script>-->
</body>

</html>
