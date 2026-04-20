@extends('admin.layouts.app')

@section('title')
<title>Shift Rosters</title>
@endsection

@section('contents')
<div class="flex-grow-1">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Shift Rosters</h4>
            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#rosterModal" id="createRosterBtn">+ Assign Roster</button>
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
                                <button type="button" class="btn btn-info btn-sm editRosterBtn" 
                                    data-id="{{ $roster->id }}"
                                    data-date="{{ $roster->date }}"
                                    data-employee="{{ $roster->employee_id }}"
                                    data-section="{{ $roster->section_id }}"
                                    data-subsection="{{ $roster->sub_section_id }}"
                                    data-shift="{{ $roster->shift_id }}"
                                    data-remarks="{{ $roster->remarks }}"
                                    data-toggle="modal" data-target="#rosterModal">
                                    Edit
                                </button>
                                <form action="{{ route('hr-center.rosters.destroy', $roster->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this roster?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        @endsection

                        <!-- Roster Create/Edit Modal (must be outside table) -->
                        <div class="modal fade" id="rosterModal" tabindex="-1" role="dialog" aria-labelledby="rosterModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="rosterModalLabel">Assign Roster</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form id="rosterForm">
                                        <div class="modal-body">
                                            <input type="hidden" name="id" id="rosterId">
                                            <div class="form-group">
                                                <label for="rosterDate">Date</label>
                                                <input type="date" class="form-control" name="date" id="rosterDate" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="rosterEmployee">Employee</label>
                                                <select class="form-control" name="employee_id" id="rosterEmployee" required>
                                                    <option value="">Select Employee</option>
                                                    @foreach($employees as $employee)
                                                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="rosterSection">Section</label>
                                                <select class="form-control" name="section_id" id="rosterSection" required>
                                                    <option value="">Select Section</option>
                                                    @foreach($sections as $section)
                                                        <option value="{{ $section->id }}">{{ $section->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="rosterSubSection">Sub Section</label>
                                                <select class="form-control" name="sub_section_id" id="rosterSubSection">
                                                    <option value="">Select Sub Section</option>
                                                    @foreach($subSections as $subSection)
                                                        <option value="{{ $subSection->id }}">{{ $subSection->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="rosterShift">Shift</label>
                                                <select class="form-control" name="shift_id" id="rosterShift" required>
                                                    <option value="">Select Shift</option>
                                                    @foreach($shifts as $shift)
                                                        <option value="{{ $shift->id }}">{{ $shift->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="rosterRemarks">Remarks</label>
                                                <input type="text" class="form-control" name="remarks" id="rosterRemarks">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary" id="saveRosterBtn">Save</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        @push('scripts')
                        <script>
                        $(document).ready(function() {
                            // Open modal for create
                            $('#createRosterBtn').on('click', function() {
                                $('#rosterModalLabel').text('Assign Roster');
                                $('#rosterForm')[0].reset();
                                $('#rosterId').val('');
                            });

                            // Open modal for edit
                            $('.editRosterBtn').on('click', function() {
                                $('#rosterModalLabel').text('Edit Roster');
                                $('#rosterId').val($(this).data('id'));
                                $('#rosterDate').val($(this).data('date'));
                                $('#rosterEmployee').val($(this).data('employee'));
                                $('#rosterSection').val($(this).data('section'));
                                $('#rosterSubSection').val($(this).data('subsection'));
                                $('#rosterShift').val($(this).data('shift'));
                                $('#rosterRemarks').val($(this).data('remarks'));
                            });

                            // Submit form via AJAX
                            $('#rosterForm').on('submit', function(e) {
                                e.preventDefault();
                                var id = $('#rosterId').val();
                                var url = id ? '/hr-center/rosters/' + id : '/hr-center/rosters';
                                var method = id ? 'PUT' : 'POST';
                                var formData = $(this).serialize();
                                $.ajax({
                                    url: url,
                                    type: method,
                                    data: formData,
                                    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                                    success: function(response) {
                                        location.reload();
                                    },
                                    error: function(xhr) {
                                        alert('Something went wrong!');
                                    }
                                });
                            });
                        });
                        </script>
                        @endpush
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
