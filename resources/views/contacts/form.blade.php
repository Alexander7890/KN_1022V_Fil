@extends('layouts.app')

@section('title', $isEdit ? 'Edit contact' : 'New contact')

@section('content')
    <a href="{{ route('contacts_index') }}" class="btn btn-secondary" style="margin-bottom:10px;">← Back</a>

    <h2 class="space">{{ $isEdit ? 'Edit contact' : 'Create new contact' }}</h2>

    @if($errors->any())
        <div style="color:#b00;margin-bottom:10px;">
            <ul>
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="post">
        @csrf

        <div class="space">
            <label>Name *</label><br>
            <input type="text" name="name" required
                   value="{{ old('name', $item->name) }}">
        </div>

        <div class="space">
            <label>Email *</label><br>
            <input type="email" name="email" required
                   value="{{ old('email', $item->email) }}">
        </div>

        <div class="space">
            <label>Phone</label><br>
            <input type="text" name="phone"
                   value="{{ old('phone', $item->phone) }}">
        </div>

        <div class="space">
            <label>Group</label><br>
            <select name="group_id">
                <option value="">No group</option>
                @foreach($groups as $g)
                    <option value="{{ $g->id }}"
                        @selected(old('group_id', optional($item->group)->id) == $g->id)>
                        {{ $g->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="space">
            <label>Note</label><br>
            <textarea name="note" rows="4">{{ old('note', $item->note) }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">
            {{ $isEdit ? 'Save changes' : 'Create' }}
        </button>
    </form>
@endsection
