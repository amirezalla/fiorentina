@extends('layouts.app') <!-- or your layout -->

@section('content')
    <div class="container">

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <h1>Chat Settings</h1>

        <!-- Update Light Words Form -->
        <form action="{{ route('chat-settings.update-light-words') }}" method="POST" class="mb-5">
            @csrf
            <div class="form-group">
                <label for="light_words">Censored Words (comma-separated):</label>
                <textarea name="light_words[]" id="light_words" class="form-control" rows="5">{{ implode(',', $lightWords) }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary mt-2">Save Light Words</button>
        </form>

        <!-- Update Auto Message Form -->
        <form action="{{ route('chat-settings.update-auto-message') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="auto_message">First Auto Message:</label>
                <textarea name="auto_message" id="auto_message" class="form-control" rows="3">{{ $autoMessage }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary mt-2">Save Auto Message</button>
        </form>

    </div>
@endsection
