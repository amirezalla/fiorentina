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
    <h4 class="fob-comment-title fob-comment-list-title mb-2"></h4>
    <div class="border-bottom text-dark mb-3">aaa</div>
    <div class="fob-comment-list-wrapper"></div>
</div>

<div class="fob-comment-form-section">
    <h4 class="fob-comment-title fob-comment-form-title">aaaa{{ trans('plugins/fob-comment::comment.front.form.title') }}
    </h4>
    <p class="fob-comment-form-note">{{ trans('plugins/fob-comment::comment.front.form.description') }}</p>

    {!! CommentForm::createWithReference($model)->renderForm() !!}
</div>
