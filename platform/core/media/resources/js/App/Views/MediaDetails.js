import { Helpers } from '../Helpers/Helpers'
import Clipboard from 'clipboard'

/**
 * MediaDetails
 * ------------------------------------------------------------
 * Renders the right-hand “Details” pane in the file manager and
 * lets the user edit the new `descrizione` field with a debounced
 * AJAX POST to `/media/{id}/descrizione`.
 */
export class MediaDetails {
    constructor() {
        /* ------------------------------------------------------------------
         * DOM nodes & templates
         * ---------------------------------------------------------------- */
        this.$detailsWrapper = $('.rv-media-main .rv-media-details')

        this.descriptionItemTemplate = `
            <div class="mb-3 rv-media-name">
                <label class="form-label">__title__</label>
                __url__
            </div>`

        /* ------------------------------------------------------------------
         * Config
         * ---------------------------------------------------------------- */
        this.onlyFields = [
            'name',
            'alt',
            'full_url',
            'size',
            'mime_type',
            'created_at',
            'updated_at',
            'descrizione',      // ← NEW
            'nothing_selected',
        ]

        // Debounce for autosave
        this.saveDelay = 400   // ms
        this.saveTimer = null
    }

    /* ======================================================================
     * PUBLIC API
     * ==================================================================== */
    renderData(data) {
        const _self = this
        const thumb = data.type === 'image'
            ? `<img src="${data.full_url}" alt="${data.name}">`
            : data.icon

        let description = ''
        let useClipboard = false

        Helpers.forEach(data, (val, index) => {
            if (Helpers.inArray(_self.onlyFields, index) && (val || index === 'descrizione')) {
                if (!Helpers.inArray(['mime_type'], index)) {

                    /* --------------------------------------------
                     * Editable `descrizione`
                     * ------------------------------------------ */
                    if (index === 'descrizione') {
                        description += _self.descriptionItemTemplate
                            .replace(/__title__/gi, Helpers.trans('descrizione'))
                            .replace(
                                /__url__/gi,
                                `<textarea class="form-control js-media-descrizione-input"
                                           data-id="${data.id}"
                                           rows="3"
                                           placeholder="${Helpers.trans('descrizione_placeholder') ?? ''}">${val ?? ''}</textarea>`
                            )
                        return               // continue to next field
                    }

                    /* --------------------------------------------
                     * Other read-only fields
                     * ------------------------------------------ */
                    description += _self.descriptionItemTemplate
                        .replace(/__title__/gi, Helpers.trans(index))
                        .replace(
                            /__url__/gi,
                            val
                                ? index === 'full_url'
                                    ? `<div class="input-group">
                                           <input type="text" id="file_details_url" class="form-control" value="${val}" />
                                           <button class="input-group-text btn btn-default js-btn-copy-to-clipboard"
                                                   type="button"
                                                   data-clipboard-target="#file_details_url">
                                               <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-clipboard me-0"
                                                    width="24" height="24" viewBox="0 0 24 24"
                                                    stroke-width="2" stroke="currentColor" fill="none"
                                                    stroke-linecap="round" stroke-linejoin="round">
                                                   <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                   <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"></path>
                                                   <path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z"></path>
                                               </svg>
                                           </button>
                                       </div>`
                                    : `<span title="${val}">${val}</span>`
                                : ''
                        )

                    if (index === 'full_url') {
                        useClipboard = true
                    }
                }
            }
        })

        /* ------------------------------------------------------------------
         * Paint Details Pane
         * ---------------------------------------------------------------- */
        _self.$detailsWrapper.find('.rv-media-thumbnail')
            .html(thumb)
            .css('color', data.color)

        _self.$detailsWrapper.find('.rv-media-description').html(description)

        /* ------------------------------------------------------------------
         * Clipboard on “full_url”
         * ---------------------------------------------------------------- */
        if (useClipboard) {
            new Clipboard('.js-btn-copy-to-clipboard')
            $('.js-btn-copy-to-clipboard')
                .tooltip()
                .on('mouseenter', e => $(e.currentTarget).tooltip('hide'))
                .on('mouseleave', e => $(e.currentTarget).tooltip('hide'))
        }

        /* ------------------------------------------------------------------
         * Debounced save for `descrizione`
         * ---------------------------------------------------------------- */
        this.$detailsWrapper.find('.js-media-descrizione-input')
            .off('keyup')              // prevent duplicate bindings
            .on('keyup', function () {
                clearTimeout(_self.saveTimer)

                const $el = $(this)
                const value = $el.val()
                const id = $el.data('id')

                _self.saveTimer = setTimeout(() => {
                    $.ajax({
                        type: 'POST',
                        url: `/media/${id}/descrizione`,
                        data: {
                            descrizione: value,
                            _token: $('meta[name="csrf-token"]').attr('content'),
                        },
                        error: () => Helpers.notify('error', Helpers.trans('save_failed')),
                    })
                }, _self.saveDelay)
            })

        /* ------------------------------------------------------------------
         * For images: show dimensions once loaded
         * ---------------------------------------------------------------- */
        if (data.mime_type?.includes('image')) {
            const image = new Image()
            image.src = data.full_url

            image.onload = () => {
                const dims =
                    _self.descriptionItemTemplate
                        .replace(/__title__/gi, Helpers.trans('width'))
                        .replace(/__url__/gi, `<span title="${image.width}">${image.width}px</span>`) +

                    _self.descriptionItemTemplate
                        .replace(/__title__/gi, Helpers.trans('height'))
                        .replace(/__url__/gi, `<span title="${image.height}">${image.height}px</span>`)

                _self.$detailsWrapper
                    .find('.rv-media-description')
                    .append(dims)
            }
        }
    }
}
