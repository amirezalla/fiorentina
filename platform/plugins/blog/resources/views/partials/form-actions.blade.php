<x-core::card>
    <x-core::card.header>
        <x-core::card.title>
            @if (!empty($icon))
                <i class="{{ $icon }}"></i>
            @endif
            {{ $title ?? apply_filters(BASE_ACTION_FORM_ACTIONS_TITLE, trans('core/base::forms.publish')) }}
        </x-core::card.title>
    </x-core::card.header>
    <x-core::card.body>
        @include('plugins/blog::partials.form-buttons')
    </x-core::card.body>
</x-core::card>

<div
    data-bb-waypoint
    data-bb-target="#form-actions"
></div>

<header
    @class(['top-0 w-100 position-fixed end-0 z-1000', 'vertical-wrapper' => AdminHelper::isInAdmin(true) && AdminAppearance::isVerticalLayout()])
    id="form-actions"
    @style(['display: none'])
>
<div class="navbar">
    <div class="{{ AdminAppearance::getContainerWidth() }}">
        <div class="row g-2 align-items-center w-100">
            @if(is_in_admin(true))
                <div class="col">
                    <div class="page-pretitle">
                        {!! Breadcrumbs::render('main', PageTitle::getTitle(false)) !!}
                    </div>
                </div>
            @endif
            <div class="col-auto ms-auto d-print-none">
                @include('plugins/blog::partials.form-buttons')
            </div>
        </div>
    </div>
</div>
</header>
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg d-flex flex-column" role="document">
        <div class="modal-content flex-grow-1">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">{{ trans('plugins/blog::forms.preview') }}</h5>
                <button type="button" class="close btn btn-close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body d-flex flex-column">
                <iframe src="" class="w-100 flex-grow-1"></iframe>
                <div class="preview-test"></div>
                <div id="previewContent"></div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function () {
        const previewModal = $('#previewModal');
        const iframe = previewModal.find('iframe');
        const previewContent = $('#previewContent');
        $('button[name="preview"]').click(function (e) {
            const element = $('small.form-hint');
            if (!element) {
                const url = element.find('a').text();
                previewModal.modal('show');
                previewModal.find('iframe').attr('src',url)
            }else {
                $.ajax({
                    url : "https://laviola.collaudo.biz/humanoid-robots-in-everyday-life-ai-companions-and-assistants",
                    success : function(response){
                        iframe.attr("srcdoc", response);
                        previewModal.modal('show');
                    }
                });
                console.log(iframe.contents()[0])
            }
        });
        previewModal.find('button.close').click( function () {
            previewModal.modal('hide');
        });
        previewModal.on('hidden.bs.modal', function () {
            previewModal.find('iframe').attr('src',"");
        });
    });
</script>
