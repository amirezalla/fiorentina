@php use Botble\Member\Models\Member; @endphp

@foreach ($chats as $chat)
    @continue($loop->first)
    @php $user = Member::find($chat['user_id']); @endphp
    <tr id="row-{{ $chat->id }}">
        {{-- ACTIONS --}}
        @if (Str::contains(request()->url(), '/chat-view'))
            <td>
                <button class="btn btn-link p-0 me-2 text-danger delete-btn" data-id="{{ $chat->id }}"
                    aria-label="Delete">
                    <i class="fa-solid fa-trash"></i>
                </button>
                <button class="btn btn-link p-0 text-muted edit-btn" data-id="{{ $chat->id }}"
                    data-message="{{ e($chat['message']) }}" aria-label="Edit">
                    <i class="fa-solid fa-pen-to-square"></i>
                </button>
            </td>
        @endif

        {{-- USER --}}
        <td>
            @if ($user)
                <a href="{{ url("admin/members/edit/{$user->id}") }}">
                    {{ $user->first_name }} {{ $user->last_name }}
                </a>
            @else
                <em class="text-muted">Unknown</em>
            @endif
        </td>

        {{-- MESSAGE --}}
        <td class="chat-msg">{{ $chat['message'] }}</td>

        {{-- DATE --}}
        <td class="text-nowrap">{{ $chat['created_at'] }}</td>
    </tr>
@endforeach
