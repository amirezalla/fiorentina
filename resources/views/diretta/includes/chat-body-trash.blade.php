@php use Botble\Member\Models\Member; @endphp

@foreach ($chats as $chat)
    <tr id="row-{{ $chat->id }}">
        <td><input type="checkbox" class="row-check" value="{{ $chat->id }}"></td>

        <td>{{ $user->full_name ?? 'Unknown' }}</td>
        <td>{{ $chat->message }}</td>
        <td class="text-nowrap">{{ $chat->created_at }}</td>

        {{-- single‑row restore --}}
        <td>
            <button class="btn btn-link restore-btn" data-id="{{ $chat->id }}">
                <i class="fa-solid fa-rotate-left"></i>
            </button>
        </td>
    </tr>
@endforeach
