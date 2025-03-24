$(function () {
    let t = !1;
    $(document).on("click", ".js-fob-comment-item-like-dislike-btn", function (e) {
        if (!t) {
            let o = $(this);
            $.ajax({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                },
                url: $(this).data("action"),
                type: "POST",
                dataType: "json",
                beforeSend: function () {
                    t = !0,
                        o.prop("disabled", !0)
                },
                success: function (e) {
                    t = !1,
                        o.prop("disabled", !1),
                        o.find("span").text(e.count)
                },
                error: function (e, n, i) {
                    t = !1,
                        o.prop("disabled", !1)
                }
            })
        }
    });
    let e = $(".btn-must-replies")
        , o = $(".btn-must-reaction")
        , n = ""
        , i = "latest";
    e.click(function () {
        o.removeClass("text-warning"),
            $(this).addClass("text-danger"),
            m(`${fobComment.listUrl}&sort=${n = "must-replies"}&sort2=${i}`)
    }),
        o.click(function () {
            e.removeClass("text-danger"),
                $(this).addClass("text-warning"),
                m(`${fobComment.listUrl}&sort=${n = "must-reaction"}&sort2=${i}`)
        }),
        $(".dropdown-item").click(function (t) {
            t.preventDefault();
            let e = $(t.target).text().trim()
                , o = $(t.target).data("key");
            o !== i && (i = o,
                $(t.target).closest(".btn-group").find("button").text(e),
                m(`${fobComment.listUrl}&sort=${n}&sort2=${i}`))
        });
    var r = !1
        , c = ""
        , a = function (t, e, o) {
            var n = new Date;
            n.setDate(n.getDate() + o),
                e = encodeURIComponent(e) + (null == o ? "" : "; expires=" + n.toUTCString()),
                document.cookie = "fob-comment-".concat(t, "=").concat(e, "; path=/")
        }
        , s = function (t) {
            var e = document.cookie.match(RegExp("(^| )fob-comment-".concat(t, "=([^;]*)(;|$)")));
            return null != e ? decodeURIComponent(e[2]) : null
        }
        , l = function (t) {
            document.cookie = "fob-comment-".concat(t, "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/")
        };
    $(document).find(".fob-comment-form input").each(function (t, e) {
        var o = $(e).prop("name");
        s(o) && ("cookie_consent" === o ? $(e).prop("checked", !0) : $(e).val($(e).val() || s(o)))
    });
    var m = function () {
        var t = arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : fobComment.listUrl;
        $.ajax({
            url: t,
            type: "GET",
            dataType: "json",
            success: function (t) {
                var e, o = t.error, n = t.data, i = t.message;
                if (void 0 !== (null === (e = window) || void 0 === e ? void 0 : e.Theme) && o)
                    Theme.showError(i);
                else {
                    var r = n.title
                        , c = n.html
                        , a = n.comments
                        , s = $(document).find(".fob-comment-list-section");
                    a.total < 1 ? s.hide() : (s.show(),
                        $(document).find(".fob-comment-list-title").text(r),
                        $(document).find(".fob-comment-list-wrapper").html(c))
                }
            }
        })
    };
    $(document).on("submit", ".fob-comment-form", function (t) {
        if (t.stopPropagation(),
            t.preventDefault(),
            void 0 === $.fn.validate || $(".fob-comment-form").valid()) {
            var e = $(t.currentTarget)
                , o = new FormData(e[0])
                , n = e.find('input[type="checkbox"][name="cookie_consent"]')
                , i = n.length > 0 && n.is(":checked");
            $.ajax({
                url: e.prop("action"),
                type: "POST",
                data: o,
                processData: !1,
                contentType: !1,
                dataType: "json",
                success: function (t) {
                    var n, s = t.error, f = t.message;
                    if (void 0 !== (null === (n = window) || void 0 === n ? void 0 : n.Theme)) {
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
                error: function (t) {
                    var e;
                    void 0 !== (null === (e = window) || void 0 === e ? void 0 : e.Theme) && Theme.handleError(t)
                }
            })
        }
    }).on("click", ".fob-comment-pagination a", function (t) {
        t.preventDefault();
        var e = t.currentTarget.href;
        e && (m(e),
            $("html, body").animate({
                scrollTop: $(".fob-comment-list-section").offset().top
            }))
    }).on("click", ".fob-comment-item-reply", function (t) {
        t.preventDefault();
        var e = $(t.currentTarget)
            , o = $(document).find(".fob-comment-form-section");
        o && o.remove(),
            r || (c = o.clone()),
            e.closest(".fob-comment-item").after(o),
            o.find(".fob-comment-form-title").text(e.data("reply-to")),
            o.find(".fob-comment-form-title").append('<a href="#" class="cancel-comment-reply-link" rel="nofollow">'.concat(e.data("cancel-reply"), "</a")),
            o.find("form").prop("action", e.prop("href")),
            r = !0
    }).on("click", ".cancel-comment-reply-link", function (t) {
        t.preventDefault(),
            r = !1;
        var e = $(document).find(".fob-comment-form-section");
        e && e.remove(),
            $(document).find(".fob-comment-list-section").after(c)
    }),
        m()
});
