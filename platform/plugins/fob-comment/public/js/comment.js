$((function () {
    var e = !1, o = "", t = function (e, o, t) {
        var n = new Date;
        n.setDate(n.getDate() + t), o = encodeURIComponent(o) + (null == t ? "" : "; expires=" + n.toUTCString()), document.cookie = "fob-comment-".concat(e, "=").concat(o, "; path=/")
    }, n = function (e) {
        var o = document.cookie.match(new RegExp("(^| )fob-comment-".concat(e, "=([^;]*)(;|$)")));
        return null != o ? decodeURIComponent(o[2]) : null
    }, c = function (e) {
        document.cookie = "fob-comment-".concat(e, "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/")
    };
    $(document).find(".fob-comment-form input").each((function (e, o) {
        var t = $(o).prop("name");
        n(t) && ("cookie_consent" === t ? $(o).prop("checked", !0) : $(o).val($(o).val() || n(t)))
    }));
    var m = function () {
        var e = arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : fobComment.listUrl;
        $.ajax({
            url: e, type: "GET", dataType: "json", success: function (e) {
                var o, t = e.error, n = e.data, c = e.message;
                if (void 0 !== (null === (o = window) || void 0 === o ? void 0 : o.Theme) && t) Theme.showError(c); else {
                    var m = n.title, i = n.html, a = n.comments, r = $(document).find(".fob-comment-list-section");
                    a.total < 1 ? r.hide() : (r.show(), $(document).find(".fob-comment-list-title").text(m), $(document).find(".fob-comment-list-wrapper").html(i))
                }
            }
        })
    };
    $(document).on("submit", ".fob-comment-form", (function (n) {
        if (n.stopPropagation(), n.preventDefault(), void 0 === $.fn.validate || $(".fob-comment-form").valid()) {
            var i = $(n.currentTarget), a = new FormData(i[0]),
                r = i.find('input[type="checkbox"][name="cookie_consent"]'), f = r.length > 0 && r.is(":checked");
            $.ajax({
                url: i.prop("action"),
                type: "POST",
                data: a,
                processData: !1,
                contentType: !1,
                dataType: "json",
                success: function (n) {
                    var r, l = n.error, d = n.message;
                    if (void 0 !== (null === (r = window) || void 0 === r ? void 0 : r.Theme)) {
                        if (l) return void Theme.showError(d);
                        Theme.showSuccess(d)
                    }
                    f ? (t("name", a.get("name"), 365), t("email", a.get("email"), 365), t("website", a.get("website"), 365), t("cookie_consent", 1, 365), i.find('textarea[name="content"]').val("")) : (i[0].reset(), c("name"), c("email"), c("website"), c("cookie_consent")), m(), e && (e = !1, $(document).find(".fob-comment-form-section").remove(o), $(document).find(".fob-comment-list-section").after(o))
                },
                error: function (e) {
                    var o;
                    void 0 !== (null === (o = window) || void 0 === o ? void 0 : o.Theme) && Theme.handleError(e)
                }
            })
        }
    })).on("click", ".fob-comment-pagination a", (function (e) {
        e.preventDefault();
        var o = e.currentTarget.href;
        o && (m(o), $("html, body").animate({scrollTop: $(".fob-comment-list-section").offset().top}))
    })).on("click", ".fob-comment-item-reply", (function (t) {
        t.preventDefault();
        var n = $(t.currentTarget), c = $(document).find(".fob-comment-form-section");
        c && c.remove(), e || (o = c.clone()), n.closest(".fob-comment-item").after(c), c.find(".fob-comment-form-title").text(n.data("reply-to")), c.find(".fob-comment-form-title").append('<a href="#" class="cancel-comment-reply-link" rel="nofollow">'.concat(n.data("cancel-reply"), "</a")), c.find("form").prop("action", n.prop("href")), e = !0
    })).on("click", ".cancel-comment-reply-link", (function (t) {
        t.preventDefault(), e = !1;
        var n = $(document).find(".fob-comment-form-section");
        n && n.remove(), $(document).find(".fob-comment-list-section").after(o)
    })), m()
}));
let loading = false;
$('.js-fob-comment-item-like-dislike-btn').click(function (e) {
    if (!loading) {
        const self = $(this);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: $(this).data('action'),
            type: 'POST',
            dataType: 'json',
            beforeSend: function () {
                loading = true;
                self.prop('disabled', true);
            },
            success: function (response) {
                loading = false;
                self.prop('disabled', false);
                self.find('span').text(response.count);
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                loading = false;
                self.prop('disabled', false);
            }
        });
    }
});
