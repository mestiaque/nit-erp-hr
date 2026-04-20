@extends('admin.layouts.app')

@section('title')
<title>Shift Rosters</title>
@endsection

@section('contents')
<div class="flex-grow-1">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Shift Rosters</h4>
            <a href="{{ route('hr-center.rosters.create') }}" class="btn btn-primary btn-sm">+ Assign Roster</a>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead class="thead-light">
                        <tr>
                            <th>SL</th>
                            <th>Date</th>
                            <th>Employee</th>
                            <th>Section</th>
                            <th>Sub Section</th>
                            <th>Shift</th>
                            <th>Remarks</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rosters as $index => $roster)
                        <tr>
                            <td>{{ $rosters->firstItem() + $index }}</td>
                            <td>{{ $roster->date }}</td>
                            <td>{{ $roster->employee->name ?? '-' }}</td>
                            <td>{{ $roster->section->name ?? '-' }}</td>
                            <td>{{ $roster->subSection->name ?? '-' }}</td>
                            <td>{{ $roster->shift->name ?? '-' }}</td>
                            <td>{{ $roster->remarks }}</td>
                            <td>
                                <form action="{{ route('hr-center.rosters.destroy', $roster->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this roster?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">No data found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-2">
                {{ $rosters->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
