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
        document.addEventListener('click', function(event) {
            // Capture clicks on any button with one of the specified classes.
            const btn = event.target.closest('.restore-btn, .approved-btn, .notapproved-btn, .spam-btn');
            if (btn) {
                // Determine the action type based on the button class
                let actionType = '';
                if (btn.classList.contains('restore-btn')) {
                    actionType = 'restore';
                } else if (btn.classList.contains('approved-btn')) {
                    actionType = 'approved';
                } else if (btn.classList.contains('notapproved-btn')) {
                    actionType = 'not approved';
                } else if (btn.classList.contains('spam-btn')) {
                    actionType = 'spam';
                }

                console.log(`Delegated ${actionType} button click detected`);

                // Retrieve the URL from the data attribute
                const url = btn.getAttribute('data-url');
                console.log(`${actionType} URL:`, url);

                // Send a POST request using the Fetch API
                fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}', // Include CSRF token for Laravel
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => {
                        console.log("Response received:", response);
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log(`${actionType} successful:`, data);
                        // Optionally update the UI or reload the page to reflect changes
                        location.reload();
                    })
                    .catch(error => {
                        console.error(`Error executing ${actionType}:`, error);
                    });
            }
        });
    </script>
@endpush
