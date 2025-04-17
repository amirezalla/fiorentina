@php use Botble\Member\Models\Member; @endphp

<div class="container mt-3">
    <div class="table-responsive">
        <table class="table table-sm table-striped align-middle">
            <thead class="table-light">
                <tr>
                    @if (Str::contains(request()->url(), '/chat-view'))
                        <th style="width:70px;">Actions</th>
                    @endif
                    <th>User</th>
                    <th>Message</th>
                    <th>Date&nbsp;/&nbsp;Time</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($chats as $chat)
                    @continue($loop->first) {{-- skip the very first chat if that’s intentional --}}
                    @php
                        $user = Member::find($chat['user_id']);
                    @endphp

                    <tr>
                        {{-- ACTION BUTTONS – only in chat‑view --}}
                        @if (Str::contains(request()->url(), '/chat-view'))
                            <td>
                                <a href="/delete-chat?id={{ $chat->id }}" class="text-danger me-2" aria-label="Delete">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                                <a href="/modify-commentary?id={{ $chat->id }}" class="text-muted" aria-label="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                            </td>
                        @endif

                        {{-- USER --}}
                        <td>
                            @if ($user)
                                <a href="{{ url("admin/members/edit/{$user->id}") }}" class="text-decoration-underline">
                                    {{ $user->first_name }} {{ $user->last_name }}
                                </a>
                            @else
                                <em class="text-muted">Unknown user</em>
                            @endif
                        </td>

                        {{-- MESSAGE --}}
                        <td>{{ $chat['message'] }}</td>

                        {{-- DATE --}}
                        <td class="text-nowrap">{{ $chat['created_at'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
