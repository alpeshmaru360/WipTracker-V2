@extends('layouts.main')

@section('content')
 <link href="{{ asset('css/settings.css') }}" rel="stylesheet" />
    <div class="page-container edit_container">
        <div class="form-container">
            <h1>Edit Email</h1>

            <form method="POST" action="{{ route('settings.update', $setting->id) }}">
                @csrf

                <label for="email">Email:</label>
                <input type="email" id="email" name="value" value="{{ $setting->value }}" required>

                @error('value')
                    <p class="error-message">{{ $message }}</p>
                @enderror

                <button type="submit">Update</button>
                <a href="{{ route('SettingsPage') }}">Back</a>
            </form>
        </div>
    </div>
@endsection
