@extends('printMaster2')

@section('title', 'Attendance Report - ' . $dateLabel)

@push('css')
<style>
.report-head { text-align:center; margin-bottom:10px; }
.report-head h3 { margin:0 0 2px; font-size:15px; }
.report-head p  { margin:0; font-size:11px; }
.sub-title  { font-size:12px; font-weight:700; margin:8px 0 4px; }
.section-title { font-size:11px; font-weight:700; background:#dde6f0; padding:3px 6px; margin:10px 0 2px; }
.t { width:100%; border-collapse:collapse; margin-bottom:10px; font-size:10px; }
.t th, .t td { border:1px solid #555; padding:3px 5px; }
.t th { background:#eef1d4; text-align:center; }
.tc { text-align:center; }
.present { color:green; font-weight:700; }
.absent  { color:red; }
</style>
@endpush

@section('contents')
@php
    use Carbon\Carbon;
    $company  = general()->title ?? 'Company Name';
    $address  = general()->address_one ?? '';
@endphp

<div class="report-head">
    <h3>{{ $company }}</h3>
    <p>{{ $address }}</p>
</div>

<div class="sub-title">Attendance Report — {{ $dateLabel }}</div>

@php
    $bySection = $employees->groupBy('section_id');
@endphp

@forelse($bySection as $sectionId => $sectionEmps)
    <div class="section-title">Section: {{ $sectionMap->get($sectionId, 'N/A') }}</div>

    <table class="t">
        <thead>
            <tr>
                <th>SI</th>
                <th>Emp. ID</th>
                <th>Name</th>
                <th>Designation</th>
                <th>Shift</th>
                <th>In Time</th>
                <th>Out Time</th>
                <th>OT Hrs</th>
                <th>Late (min)</th>
                <th>Status</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sectionEmps as $employee)
                @php
                    $att   = $attendanceMap->get($employee->id);
                    $status = $att ? ($att->status ?: ($att->in_time ? 'P' : 'A')) : 'A';
                    $otHrs = $att ? number_format((int)($att->overtime_minutes ?? 0) / 60, 2) : '0.00';
                    $lateMin = $att ? (int)($att->late_time ?? 0) : 0;
                @endphp
                <tr>
                    <td class="tc">{{ $loop->iteration }}</td>
                    <td>{{ $employee->employee_id }}</td>
                    <td>{{ $employee->name }}</td>
                    <td>{{ $designationMap->get($employee->designation_id, 'N/A') }}</td>
                    <td class="tc">{{ $shiftMap->get($employee->shift_id, '-') }}</td>
                    <td class="tc">{{ $att && $att->in_time ? \Carbon\Carbon::parse($att->in_time)->format('h:i A') : '-' }}</td>
                    <td class="tc">{{ $att && $att->out_time ? \Carbon\Carbon::parse($att->out_time)->format('h:i A') : '-' }}</td>
                    <td class="tc">{{ $otHrs }}</td>
                    <td class="tc">{{ $lateMin ?: '-' }}</td>
                    <td class="tc {{ $status === 'P' ? 'present' : 'absent' }}">{{ $status }}</td>
                    <td>{{ $att->remarks ?? '' }}</td>
                </tr>
            @empty
                <tr><td colspan="11" class="tc">No data.</td></tr>
            @endforelse
        </tbody>
    </table>
@empty
    <p>No employees found.</p>
@endforelse

@endsection
