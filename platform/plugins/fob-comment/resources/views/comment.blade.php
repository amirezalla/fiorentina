@php

    Theme::registerToastNotification();
    use FriendsOfBotble\Comment\Forms\Fronts\CommentForm;
@endphp


<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

<style>
    .fob-comment-title {
        font-size: 30px;
        font-weight: 600;
        margin-bottom: 1.5rem
    }

    .fob-comment-item-inner {
        display: flex;
        margin-bottom: 2.5rem
    }

    .fob-comment-item-avatar {
        margin-inline-end: 20px
    }

    .fob-comment-item-avatar img {
        border-radius: 50%;
        width: 60px
    }

    .fob-comment-item-content {
        width: 100%
    }

    .fob-comment-item-pending {
        color: #5e5e5e;
        margin-bottom: 10px;
        display: block;
        font-style: normal !important;
        background: lightyellow;
        font-size: smaller !important;
        padding: 10px !important;
    }

    .fob-comment-item-body p {
        font-size: 16px;
        margin-bottom: .75rem
    }

    .fob-comment-item-footer {
        align-items: center;
        display: flex;
        justify-content: space-between
    }

    .fob-comment-item-author {
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 0
    }

    .fob-comment-item-info {
        align-items: center;
        display: flex;
        gap: .75rem
    }

    .fob-comment-item-admin-badge {
        background-color: var(--primary-color);
        border-radius: 5px;
        color: #fff;
        font-size: 12px;
        padding: .2rem .5rem
    }

    .fob-comment-item-date {
        color: #999;
        font-size: 14px;
        margin-bottom: 0
    }

    .fob-comment-item-reply {
        background-color: #f0f0f0;
        border: 1px solid #ccc;
        border-radius: 20px;
        color: #333;
        padding: 5px 10px;
        font-size: 12px;
        text-decoration: none;
        display: inline-flex;
        align-items: center
    }

    .fob-comment-item .fob-comment-list {
        margin-inline-start: 2.5rem
    }

    .fob-comment-form-section {
        margin: 3rem 0
    }

    .fob-comment-form-note {
        color: #888;
        font-size: 14px;
        margin-bottom: 1.5rem
    }

    .fob-comment-form label.required:after {
        color: red;
        content: " *"
    }

    .cancel-comment-reply-link {
        font-size: 14px;
        margin-inline-start: 1rem;
        text-decoration: underline
    }

    .cancel-comment-reply-link:hover {
        text-decoration: none
    }

    .fob-comment-item-dislike-btn,
    .fob-comment-item-like-btn {
        font-size: 12px;
        text-decoration: none;
        display: -webkit-inline-box;
        display: -ms-inline-flexbox;
        display: inline-flex;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        gap: 4px;
        background-color: #fff;
        border: 1px solid #ccc;
        border-radius: 20px;
        color: #888;
        padding: 5px 10px;
        position: absolute;
        bottom: 0;
        -webkit-transform: translateY(50%);
        -ms-transform: translateY(50%);
        transform: translateY(50%)
    }

    .fob-comment-item .fob-comment-item-content .fob-comment-item-content-inside .fob-comment-item-body .fob-comment-item-reply {
        left: 172px !important
    }

    .fob-comment-item-like-btn {
        left: 42px
    }

    .fob-comment-item-dislike-btn {
        left: 100px
    }
</style>

<script>
    $(function() {
        let t = !1;
        $(document).on("click", ".js-fob-comment-item-like-dislike-btn", function(e) {
            if (!t) {
                let o = $(this);
                $.ajax({
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                    },
                    url: $(this).data("action"),
                    type: "POST",
                    dataType: "json",
                    beforeSend: function() {
                        t = !0,
                            o.prop("disabled", !0)
                    },
                    success: function(e) {
                        t = !1,
                            o.prop("disabled", !1),
                            o.find("span").text(e.count)
                    },
                    error: function(e, n, i) {
                        t = !1,
                            o.prop("disabled", !1)
                    }
                })
            }
        });
        let e = $(".btn-must-replies"),
            o = $(".btn-must-reaction"),
            n = "",
            i = "latest";
        e.click(function() {
                o.removeClass("text-warning"),
                    $(this).addClass("text-danger"),
                    m(`${fobComment.listUrl}&sort=${n = "must-replies"}&sort2=${i}`)
            }),
            o.click(function() {
                e.removeClass("text-danger"),
                    $(this).addClass("text-warning"),
                    m(`${fobComment.listUrl}&sort=${n = "must-reaction"}&sort2=${i}`)
            }),
            $(".dropdown-item").click(function(t) {
                t.preventDefault();
                let e = $(t.target).text().trim(),
                    o = $(t.target).data("key");
                o !== i && (i = o,
                    $(t.target).closest(".btn-group").find("button").text(e),
                    m(`${fobComment.listUrl}&sort=${n}&sort2=${i}`))
            });
        var r = !1,
            c = "",
            a = function(t, e, o) {
                var n = new Date;
                n.setDate(n.getDate() + o),
                    e = encodeURIComponent(e) + (null == o ? "" : "; expires=" + n.toUTCString()),
                    document.cookie = "fob-comment-".concat(t, "=").concat(e, "; path=/")
            },
            s = function(t) {
                var e = document.cookie.match(RegExp("(^| )fob-comment-".concat(t, "=([^;]*)(;|$)")));
                return null != e ? decodeURIComponent(e[2]) : null
            },
            l = function(t) {
                document.cookie = "fob-comment-".concat(t, "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/")
            };
        $(document).find(".fob-comment-form input").each(function(t, e) {
            var o = $(e).prop("name");
            s(o) && ("cookie_consent" === o ? $(e).prop("checked", !0) : $(e).val($(e).val() || s(o)))
        });
        var m = function() {
            var t = arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : fobComment.listUrl;
            $.ajax({
                url: t,
                type: "GET",
                dataType: "json",
                success: function(t) {
                    var e, o = t.error,
                        n = t.data,
                        i = t.message;
                    if (void 0 !== (null === (e = window) || void 0 === e ? void 0 : e.Theme) &&
                        o)
                        Theme.showError(i);
                    else {
                        var r = n.title,
                            c = n.html,
                            a = n.comments,
                            s = $(document).find(".fob-comment-list-section");
                        a.total < 1 ? s.hide() : (s.show(),
                            $(document).find(".fob-comment-list-title").text(r),
                            $(document).find(".fob-comment-list-wrapper").html(c))
                    }
                }
            })
        };
        $(document).on("submit", ".fob-comment-form", function(t) {
                if (t.stopPropagation(),
                    t.preventDefault(),
                    void 0 === $.fn.validate || $(".fob-comment-form").valid()) {
                    var e = $(t.currentTarget),
                        o = new FormData(e[0]),
                        n = e.find('input[type="checkbox"][name="cookie_consent"]'),
                        i = n.length > 0 && n.is(":checked");
                    $.ajax({
                        url: e.prop("action"),
                        type: "POST",
                        data: o,
                        processData: !1,
                        contentType: !1,
                        dataType: "json",
                        success: function(t) {
                            var n, s = t.error,
                                f = t.message;
                            if (void 0 !== (null === (n = window) || void 0 === n ? void 0 : n
                                    .Theme)) {
                                if (s)
                                    return void Theme.showError(f);
                                Theme.showSuccess(f)
                            }
                            i ? (a("name", o.get("name"), 365),
                                    a("email", o.get("email"), 365),
                                    a("website", o.get("website"), 365),
                                    a("cookie_consent", 1, 365),
                                    e.find('textarea[name="content"]').val("")) : (e[0].reset(),
                                    l("name"),
                                    l("email"),
                                    l("website"),
                                    l("cookie_consent")),
                                m(),
                                r && (r = !1,
                                    $(document).find(".fob-comment-form-section").remove(c),
                                    $(document).find(".fob-comment-list-section").after(c))
                        },
                        error: function(t) {
                            var e;
                            void 0 !== (null === (e = window) || void 0 === e ? void 0 : e
                                .Theme) && Theme.handleError(t)
                        }
                    })
                }
            }).on("click", ".fob-comment-pagination a", function(t) {
                t.preventDefault();
                var e = t.currentTarget.href;
                e && (m(e),
                    $("html, body").animate({
                        scrollTop: $(".fob-comment-list-section").offset().top
                    }))
            }).on("click", ".fob-comment-item-reply", function(t) {
                t.preventDefault();
                var e = $(t.currentTarget),
                    o = $(document).find(".fob-comment-form-section");
                o && o.remove(),
                    r || (c = o.clone()),
                    e.closest(".fob-comment-item").after(o),
                    o.find(".fob-comment-form-title").text(e.data("reply-to")),
                    o.find(".fob-comment-form-title").append(
                        '<a href="#" class="cancel-comment-reply-link" rel="nofollow">'.concat(e.data(
                            "cancel-reply"), "</a")),
                    o.find("form").prop("action", e.prop("href")),
                    r = !0
            }).on("click", ".cancel-comment-reply-link", function(t) {
                t.preventDefault(),
                    r = !1;
                var e = $(document).find(".fob-comment-form-section");
                e && e.remove(),
                    $(document).find(".fob-comment-list-section").after(c)
            }),
            m()
    });
</script>





<script>
    window.fobComment = {};

    window.fobComment = {
        listUrl: {{ Js::from(route('fob-comment.public.comments.index', isset($model) ? ['reference_type' => $model::class, 'reference_id' => $model->id] : url()->current())) }},
    };
</script>
<div class="fob-comment-form-section">
    <div class="d-none d-md-block">
        @include('ads.includes.adsrecentp3')
    </div>


    <h4 class="fob-comment-title fob-comment-form-title">
        {{ trans('plugins/fob-comment::comment.front.form.title') }}
    </h4>
    <p class="fob-comment-form-note">{{ trans('plugins/fob-comment::comment.front.form.description') }}</p>

    {!! CommentForm::createWithReference($model)->renderForm() !!}
</div>
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



<script src="https://cdn.ckeditor.com/ckeditor5/35.0.1/classic/ckeditor.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Find the textarea by its ID
        var textarea = document.querySelector("#content");
        if (!textarea) {
            console.error("Textarea with id 'editor' not found.");
            return;
        }

        // Hide the original textarea
        textarea.style.display = "none";

        // Create a container div for CKEditor
        var editorContainer = document.createElement("div");
        editorContainer.id = "editor-container";

        // Insert the container right after the textarea
        textarea.parentNode.insertBefore(editorContainer, textarea.nextSibling);

        // Initialize CKEditor on the container
        ClassicEditor
            .create(editorContainer, {
                placeholder: '' // No placeholder text
            })
            .then(editor => {
                // When the form is submitted, copy the editor data back to the textarea
                var form = textarea.closest("form");
                if (form) {
                    form.addEventListener("submit", function() {
                        textarea.value = editor.getData();
                    });
                }
            })
            .catch(error => {
                console.error("Error initializing CKEditor:", error);
            });
    });
</script>
