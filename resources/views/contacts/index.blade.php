@extends('layouts.app')

@section('title', 'Contacts')

@section('content')
    <div class="row space">
        <h1>Contacts</h1>
        <a href="{{ route('contacts_new') }}" class="btn btn-primary">+ New contact</a>
    </div>

    <form method="get" action="{{ route('contacts_index') }}" class="row space" style="gap:8px;flex-wrap:wrap;">
        <div style="flex: 1 1 220px;">
            <input type="text" name="q" placeholder="Search..." value="{{ $q }}">
        </div>

        <div style="flex: 0 0 180px;">
            <select name="group">
                <option value="">All groups</option>
                @foreach($groups as $g)
                    <option value="{{ $g->id }}" @selected($groupId == $g->id)>{{ $g->name }}</option>
                @endforeach
            </select>
        </div>

        <div style="flex: 0 0 140px;">
            <select name="perPage" onchange="this.form.submit()">
                @foreach([5,10,20,50] as $n)
                    <option value="{{ $n }}" @selected($perPage == $n)>{{ $n }} / page</option>
                @endforeach
            </select>
        </div>

        <div>
            <button type="submit" class="btn btn-secondary">Apply</button>
        </div>
    </form>

    @if($total === 0)
        <p>No contacts found.</p>
    @else
        <table>
            <thead>
            <tr>
                <th style="width:40px;">#</th>
                <th style="width:160px;">Name</th>
                <th style="width:200px;">Email</th>
                <th style="width:120px;">Phone</th>
                <th style="width:120px;">Group</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($contacts as $c)
                <tr>
                    <td>{{ $c->id }}</td>
                    <td><strong>{{ $c->name }}</strong></td>
                    <td>
                        <a href="mailto:{{ $c->email }}">{{ $c->email }}</a>
                    </td>
                    <td>{{ $c->phone ?? '—' }}</td>
                    <td>
                        @if($c->group)
                            <span class="badge">{{ $c->group->name }}</span>
                        @else
                            —
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('contacts_edit', ['id' => $c->id]) }}" class="btn btn-secondary">Edit</a>

                        <form method="post"
                              action="{{ route('contacts_delete', ['id' => $c->id]) }}"
                              style="display:inline"
                              onsubmit="return confirm('Delete this contact?');">
                            @csrf
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div class="pagination">
            <span>Page {{ $page }} of {{ $totalPages }}</span>

            @if($page > 1)
                <a href="{{ route('contacts_index', array_merge(request()->query(), ['page' => $page-1])) }}"
                   class="btn btn-secondary">Prev</a>
            @endif
            @if($page < $totalPages)
                <a href="{{ route('contacts_index', array_merge(request()->query(), ['page' => $page+1])) }}"
                   class="btn btn-secondary">Next</a>
            @endif
        </div>
    @endif
@endsection
