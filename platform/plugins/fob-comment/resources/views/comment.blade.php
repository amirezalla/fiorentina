@php
    Theme::asset()->add('fob-comment-css', asset('vendor/core/plugins/fob-comment/css/comment.css'));
    Theme::asset()->container('footer')->add('fob-comment-js', asset('vendor/core/plugins/fob-comment/js/comment.js'));

    Theme::registerToastNotification();

    use FriendsOfBotble\Comment\Forms\Fronts\CommentForm;
@endphp

<script>
    window.fobComment = {};

    window.fobComment = {
        listUrl: {{ Js::from(route('fob-comment.public.comments.index', isset($model) ? ['reference_type' => $model::class, 'reference_id' => $model->id] : url()->current())) }},
    };
</script>
<div class="fob-comment-list-section" style="display: none">
    <div class="d-flex justify-content-between align-items-center border-bottom text-dark mb-3">
        <div class="d-flex align-items-left">
            <h6 class="fob-comment-title fob-comment-list-title mb-2"></h6>
        </div>

        <div class="d-flex align-items-center">
            <button class="btn mb-0 btn-must-reaction">
                <i class="fa fa-bolt" aria-hidden="true"></i>
            </button>
            <button class="btn mb-0 btn-must-replies">
                <i class="fa fa-fire" aria-hidden="true"></i>
            </button>
            <div class="btn-group">
                <button class="btn btn-sm dropdown-toggle mb-0" type="button" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                    {{ collect(trans('plugins/fob-comment::comment.sort_options'))->firstWhere('key', 'latest')['title'] }}
                </button>
                <div class="dropdown-menu sort-dropdown">
                    @foreach (trans('plugins/fob-comment::comment.sort_options') as $item)
                        <a class="dropdown-item" href="#" data-key="{{ $item['key'] }}">{{ $item['title'] }}</a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="fob-comment-list-wrapper"></div>
</div>

<div class="fob-comment-form-section">
    <h4 class="fob-comment-title fob-comment-form-title">
        {{ trans('plugins/fob-comment::comment.front.form.title') }}
    </h4>
    <p class="fob-comment-form-note">{{ trans('plugins/fob-comment::comment.front.form.description') }}</p>
    <div id="quill-editor"></div>

    {!! CommentForm::createWithReference($model)->renderForm() !!}
</div>

<!-- Quill CSS -->
<link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet">

<!-- Quill JS (no jQuery required) -->
<script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // 1. Initialize Quill
        var quill = new Quill('#quill-editor', {
            theme: 'snow',
            placeholder: '', // No placeholder
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline', 'strike'],
                    [{
                        'list': 'ordered'
                    }, {
                        'list': 'bullet'
                    }],
                    ['link']
                ]
            }
        });

        // 2. On form submit, copy Quill's HTML to the hidden textarea
        var form = document.querySelector('form.fob-comment-form'); // Adjust selector if needed
        if (form) {
            form.addEventListener('submit', function() {
                var hiddenTextarea = document.getElementById('comment-content');
                hiddenTextarea.value = quill.root.innerHTML; // Quill's content as HTML
            });
        }
    });
</script>
