</div>
<footer class="page-footer pt-50">
    <div class="container">
        <div class="row">
            <div class="col-12 col-md-3 mx-auto mb-3">
                <a href="{{ BaseHelper::getHomepageUrl() }}" class="page-logo">
                    {{ Theme::getLogoImage(['height' => 70]) }}
                </a>
            </div>
            <ul class="footer__options">
                <li class="footer__option">
                    <a href="" class="flex">
                        {!! BaseHelper::renderIcon('ti ti-home') !!}
                    </a>
                </li>
                <li class="footer__option">
                    <a href="" class="flex">
                        {!! BaseHelper::renderIcon('ti ti-home') !!}
                    </a>
                </li>
            </ul>
            <div class="col-12">
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
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
</body>

</html>
