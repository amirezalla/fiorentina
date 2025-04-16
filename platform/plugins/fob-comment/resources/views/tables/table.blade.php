@extends($layout ?? BaseHelper::getAdminMasterLayoutTemplate())

@section('content')

    @include('core/table::base-table')

    <x-core::modal id="reply-comment-modal" :title="trans('plugins/fob-comment::comment.reply_modal.title')" size="lg">
        {!! \FriendsOfBotble\Comment\Forms\ReplyCommentForm::create()->renderForm() !!}

        <x-slot:footer>
            <x-core::button type="submit" color="primary" form="reply-comment-form">
                {{ trans('plugins/fob-comment::comment.reply') }}
            </x-core::button>
            <x-core::button type="button" data-bs-dismiss="modal">
                {{ trans('plugins/fob-comment::comment.reply_modal.cancel') }}
            </x-core::button>
        </x-slot:footer>
    </x-core::modal>
@stop

@push('footer')
    <script>
        'use strict';

        $(() => {
            $('#reply-comment-modal').on('show.bs.modal', function(event) {
                const relatedTarget = $(event.relatedTarget).parent()

                $('form#reply-comment-form').prop('action', relatedTarget.data('url'))
                $('#reply-comment-modal .modal-title').text(relatedTarget.data('modal-title'))
            })

            $('form#reply-comment-form').on('submit', function(e) {
                e.preventDefault()

                const dataTable = $('#fob-comment-table')
                const modal = $('#reply-comment-modal')
                const form = $(this)

                $httpClient
                    .make()
                    .withButtonLoading(modal.find('button[type="submit"]'))
                    .post(form.prop('action'), form.serialize())
                    .then(() => {
                        dataTable.DataTable().ajax.reload()
                        modal.modal('hide')
                    })
            })
        })
        document.addEventListener('DOMContentLoaded', function() {
            console.log("DOM fully loaded and parsed");
            document.querySelectorAll('.restore-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    console.log("Restore button clicked");

                    // Retrieve the URL from the data attribute
                    var url = btn.getAttribute('data-url');
                    console.log("Request URL:", url);

                    // Send a POST request using the Fetch API
                    fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}', // Include CSRF token for security
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                        })
                        .then(response => {
                            console.log("Response received:", response);
                            if (!response.ok) {
                                console.error("Response not ok:", response);
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('Restore successful:', data);
                            // Optionally update the UI or reload the page to reflect the change
                            location.reload();
                        })
                        .catch(error => {
                            console.error('Error restoring comment:', error);
                        });
                });
            });
        });
    </script>
@endpush
