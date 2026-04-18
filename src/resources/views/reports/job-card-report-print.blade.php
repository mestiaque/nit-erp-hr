@extends('printMaster2')

@section('title', 'Job Card Report - ' . $fromLabel . ' To ' . $toLabel)

@push('css')
<style>
.report-head { text-align:center; margin-bottom:10px; }
.report-head h3 { margin:0 0 2px; font-size:15px; }
.report-head p  { margin:0; font-size:11px; }
.sub-title { font-size:12px; font-weight:700; margin:8px 0 4px; }
.section-title { font-size:11px; font-weight:700; background:#dde6f0; padding:3px 6px; margin:10px 0 2px; }
.t { width:100%; border-collapse:collapse; margin-bottom:10px; font-size:10px; }
.t th, .t td { border:1px solid #555; padding:3px 5px; }
.t th { background:#eef1d4; text-align:center; }
.tc { text-align:center; }
.tr { text-align:right; }
.info-grid { width:100%; border-collapse:collapse; margin-bottom:6px; font-size:10px; }
.info-grid td { padding:2px 6px; border:1px solid #ccc; width:25%; }
.info-grid td:nth-child(odd) { font-weight:700; background:#f5f5f5; }
.page-break { page-break-after: always; }
.badge-lock { background:#e74c3c; color:#fff; padding:1px 5px; border-radius:3px; font-size:9px; }
</style>
@endpush

@section('contents')
@php
    use Carbon\Carbon;
    $company   = general()->title ?? 'Company Name';
    $address   = general()->address_one ?? '';
    $fmtTime   = fn($t) => $t ? Carbon::parse($t)->format('h:i A') : '-';
    $fmtOT     = fn($min) => $min > 0 ? number_format($min / 60, 2) : '0.00';
    $getAtt    = fn($uid, $date) => ($attendanceMap->get($uid . '_' . $date) ?? collect())->first();
    $isPresent = fn($att) => $att && $att->in_time;
    $dayName   = fn(Carbon $d) => $d->format('D');
    // Use central HR options service for all lookups
    $hrOptions = \App\Services\HrOptionsService::getOptions();
    $departmentMap = collect($hrOptions['departments'])->pluck('name', 'id');
    $sectionMap = collect($hrOptions['sections'])->pluck('name', 'id');
    $subSectionMap = collect($hrOptions['subSections'])->pluck('name', 'id');
    $designationMap = collect($hrOptions['designations'])->pluck('name', 'id');
    $classificationMap = collect($hrOptions['classifications'])->pluck('name', 'id');
    $shiftMap = collect($hrOptions['shifts'])->pluck('name_of_shift', 'id');
@endphp

{{-- =============================================================== --}}
{{-- JOB CARD (individual per employee) --}}
{{-- =============================================================== --}}
@if(in_array($reportType, ['job-card', 'job-card-lock']))
    @forelse($employees as $employee)
        @php
            $isLocked = false;
            if ($reportType === 'job-card-lock') {
                $other = json_decode($employee->other_information ?? '{}', true);
                $lockKey = 'job_card_lock';
                $key = $from . '_' . $to;
                $isLocked = !empty($other[$lockKey][$key]);
            }
        @endphp

        <div class="report-head">
            <h3>{{ $company }}</h3>
            <p>{{ $address }}</p>
        </div>

        <div class="sub-title">
            Job Card {{ $reportType === 'job-card-lock' ? '(Lock)' : '' }}
            ({{ $fromLabel }} To {{ $toLabel }})
            @if($isLocked) <span class="badge-lock">LOCKED</span> @endif
        </div>

        <table class="info-grid">
            <tr>
                <td>Employee ID</td><td>{{ $employee->employee_id }}</td>
                <td>Department</td><td>{{ $departmentMap->get($employee->department_id, 'N/A') }}</td>
            </tr>
            <tr>
                <td>Name</td><td>{{ $employee->name }}</td>
                <td>Section</td><td>{{ $sectionMap->get($employee->section_id, 'N/A') }}</td>
            </tr>
            <tr>
                <td>Classification</td><td>{{ $classificationMap->get($employee->employee_type, 'N/A') }}</td>
                <td>Designation</td><td>{{ $designationMap->get($employee->designation_id, 'N/A') }}</td>
            </tr>
            <tr>
                <td>Join Date</td>
                <td>{{ optional($employee->joining_date)->format('d-M-y') ?? 'N/A' }}</td>
                <td></td><td></td>
            </tr>
        </table>

        <table class="t">
            <thead>
                <tr>
                    <th style="width:30px">SL</th>
                    <th>Date</th>
                    <th>Shift</th>
                    <th>Day</th>
                    <th>In Time</th>
                    <th>Out Time</th>
                    <th>OT Hrs</th>
                    <th>Status</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dates as $d)
                    @php
                        $att    = $getAtt($employee->id, $d->toDateString());
                        $status = $att ? ($att->status ?: ($att->in_time ? 'P' : 'A')) : 'A';
                        $otHrs  = $att ? $fmtOT((int)($att->overtime_minutes ?? 0)) : '0.00';
                    @endphp
                    <tr>
                        <td class="tc">{{ $loop->iteration }}</td>
                        <td class="tc">{{ $d->format('d-M-y') }}</td>
                        <td class="tc">{{ $shiftMap->get($employee->shift_id, '-') }}</td>
                        <td class="tc">{{ $dayName($d) }}</td>
                        <td class="tc">{{ $att ? $fmtTime($att->in_time) : '-' }}</td>
                        <td class="tc">{{ $att ? $fmtTime($att->out_time) : '-' }}</td>
                        <td class="tc">{{ $otHrs }}</td>
                        <td class="tc">{{ $status }}</td>
                        <td>{{ $att->remarks ?? '' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="tc">No dates in range.</td></tr>
                @endforelse
            </tbody>
        </table>

        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @empty
        <p>No employees found.</p>
    @endforelse
@endif

{{-- =============================================================== --}}
{{-- JOB CARD SUMMARY (section-wise, day columns) --}}
{{-- =============================================================== --}}
@if(in_array($reportType, ['job-card-summary', 'job-card-summary-lock']))
    @php
        $bySection = $employees->groupBy('section_id');
    @endphp

    @forelse($bySection as $sectionId => $sectionEmps)
        <div class="report-head">
            <h3>{{ $company }}</h3>
            <p>{{ $address }}</p>
        </div>

        <div class="sub-title">
            Job Card Summary {{ $reportType === 'job-card-summary-lock' ? '(Lock)' : '' }}
            ({{ $fromLabel }} To {{ $toLabel }})
        </div>
        <div class="section-title">Section: {{ $sectionMap->get($sectionId, 'N/A') }}</div>

        <div style="overflow-x:auto;">
        <table class="t">
            <thead>
                <tr>
                    <th>SI</th>
                    <th>Emp. ID</th>
                    <th>Name</th>
                    <th>Designation</th>
                    <th>DOJ</th>
                    <th>Section</th>
                    <th>Sub-Section</th>
                    <th>Block/Line</th>
                    @foreach($dates as $d)
                        <th class="tc" style="min-width:28px;">{{ $d->format('d') }}</th>
                    @endforeach
                    <th>P/OT</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sectionEmps as $employee)
                    @php
                        $totalPresent = 0;
                        $totalOTMin   = 0;
                    @endphp
                    <tr>
                        <td class="tc">{{ $loop->iteration }}</td>
                        <td>{{ $employee->employee_id }}</td>
                        <td>{{ $employee->name }}</td>
                        <td>{{ $designationMap->get($employee->designation_id, 'N/A') }}</td>
                        <td class="tc">{{ optional($employee->joining_date)->format('d-M-y') ?? '-' }}</td>
                        <td>{{ $sectionMap->get($employee->section_id, 'N/A') }}</td>
                        <td>{{ $subSectionMap->get($employee->hr_sub_section_id ?? $employee->sub_section_id ?? null, 'N/A') }}</td>
                        <td>{{ $lineMap->get($employee->line_number, 'N/A') }}</td>
                        @foreach($dates as $d)
                            @php
                                $att    = $getAtt($employee->id, $d->toDateString());
                                $pres   = $isPresent($att);
                                $otMin  = $att ? (int)($att->overtime_minutes ?? 0) : 0;
                                if($pres) $totalPresent++;
                                $totalOTMin += $otMin;
                            @endphp
                            <td class="tc" style="font-size:9px;">
                                @if($pres)
                                    A@if($otMin > 0) | {{ number_format($otMin/60,2) }}@endif
                                @else
                                    0
                                @endif
                            </td>
                        @endforeach
                        <td class="tc" style="font-size:9px;white-space:nowrap;">
                            {{ $totalPresent }} | {{ number_format($totalOTMin/60,2) }}
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="{{ 9 + count($dates) }}" class="tc">No data.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>

        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @empty
        <p>No employees found.</p>
    @endforelse
@endif

{{-- =============================================================== --}}
{{-- ATTENDANCE SUMMARY (section-wise) --}}
{{-- =============================================================== --}}
@if($reportType === 'attendance-summary')
    @php
        $totalDays = count($dates);
        $bySection = $employees->groupBy('section_id');
    @endphp

    @forelse($bySection as $sectionId => $sectionEmps)
        <div class="report-head">
            <h3>{{ $company }}</h3>
            <p>{{ $address }}</p>
        </div>

        <div class="sub-title">Attendance Summary ({{ $fromLabel }} To {{ $toLabel }})</div>
        <div class="section-title">Section: {{ $sectionMap->get($sectionId, 'N/A') }}</div>

        <table class="t">
            <thead>
                <tr>
                    <th>SI</th><th>ID</th><th>Name</th><th>Designation</th>
                    <th>Join Date</th><th>Section</th><th>Sub-Section</th><th>Block/Line</th>
                    <th>Month<br>Day</th><th>Late</th><th>Absent</th><th>Leave</th>
                    <th>Weekend</th><th>Fac.<br>Holiday</th><th>Present</th>
                    <th>Earn<br>Days</th><th>OT</th><th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sectionEmps as $employee)
                    @php
                        $present = 0; $late = 0; $absent = 0;
                        $leave   = 0; $weekend = 0; $holiday = 0;
                        $totalOTMin = 0;
                        foreach($dates as $d) {
                            $att = $getAtt($employee->id, $d->toDateString());
                            $st  = $att ? strtoupper($att->status ?? '') : '';
                            if ($att && $att->in_time) {
                                $present++;
                                if ((int)($att->late_time ?? 0) > 0) $late++;
                                $totalOTMin += (int)($att->overtime_minutes ?? 0);
                            } elseif ($st === 'L') {
                                $leave++;
                            } elseif ($st === 'W') {
                                $weekend++;
                            } elseif ($st === 'H') {
                                $holiday++;
                            } else {
                                $absent++;
                            }
                        }
                        $earnDays = $present + $leave;
                    @endphp
                    <tr>
                        <td class="tc">{{ $loop->iteration }}</td>
                        <td>{{ $employee->employee_id }}</td>
                        <td>{{ $employee->name }}</td>
                        <td>{{ $designationMap->get($employee->designation_id, 'N/A') }}</td>
                        <td class="tc">{{ optional($employee->joining_date)->format('d-M-y') ?? '-' }}</td>
                        <td>{{ $sectionMap->get($employee->section_id, 'N/A') }}</td>
                        <td>{{ $subSectionMap->get($employee->hr_sub_section_id ?? $employee->sub_section_id ?? null, 'N/A') }}</td>
                        <td>{{ $lineMap->get($employee->line_number, 'N/A') }}</td>
                        <td class="tc">{{ $totalDays }}</td>
                        <td class="tc">{{ $late }}</td>
                        <td class="tc">{{ $absent }}</td>
                        <td class="tc">{{ $leave }}</td>
                        <td class="tc">{{ $weekend }}</td>
                        <td class="tc">{{ $holiday }}</td>
                        <td class="tc">{{ $present }}</td>
                        <td class="tc">{{ $earnDays }}</td>
                        <td class="tc">{{ number_format($totalOTMin/60, 2) }}</td>
                        <td></td>
                    </tr>
                @empty
                    <tr><td colspan="18" class="tc">No data.</td></tr>
                @endforelse
            </tbody>
        </table>

        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @empty
        <p>No employees found.</p>
    @endforelse
@endif

{{-- =============================================================== --}}
{{-- OT DETAILS (section-wise, day-OT columns) --}}
{{-- =============================================================== --}}
@if($reportType === 'ot-details')
    @php
        $bySection = $employees->groupBy('section_id');
    @endphp

    @forelse($bySection as $sectionId => $sectionEmps)
        <div class="report-head">
            <h3>{{ $company }}</h3>
            <p>{{ $address }}</p>
        </div>

        <div class="sub-title">OT Details ({{ $fromLabel }} To {{ $toLabel }})</div>
        <div class="section-title">Section: {{ $sectionMap->get($sectionId, 'N/A') }}</div>

        <div style="overflow-x:auto;">
        <table class="t">
            <thead>
                <tr>
                    <th>SI</th><th>Emp. ID</th><th>Name</th><th>Designation</th>
                    <th>DOJ</th><th>Section</th><th>Sub-Section</th><th>Block/Line</th>
                    @foreach($dates as $d)
                        <th class="tc" style="min-width:28px;">{{ $d->format('d') }}</th>
                    @endforeach
                    <th>To. OT</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sectionEmps as $employee)
                    @php $totalOTMin = 0; @endphp
                    <tr>
                        <td class="tc">{{ $loop->iteration }}</td>
                        <td>{{ $employee->employee_id }}</td>
                        <td>{{ $employee->name }}</td>
                        <td>{{ $designationMap->get($employee->designation_id, 'N/A') }}</td>
                        <td class="tc">{{ optional($employee->joining_date)->format('d-M-y') ?? '-' }}</td>
                        <td>{{ $sectionMap->get($employee->section_id, 'N/A') }}</td>
                        <td>{{ $subSectionMap->get($employee->hr_sub_section_id ?? $employee->sub_section_id ?? null, 'N/A') }}</td>
                        <td>{{ $lineMap->get($employee->line_number, 'N/A') }}</td>
                        @foreach($dates as $d)
                            @php
                                $att   = $getAtt($employee->id, $d->toDateString());
                                $otMin = $att ? (int)($att->overtime_minutes ?? 0) : 0;
                                $totalOTMin += $otMin;
                            @endphp
                            <td class="tc" style="font-size:9px;">
                                {{ $otMin > 0 ? number_format($otMin/60,2) : '' }}
                            </td>
                        @endforeach
                        <td class="tc">{{ number_format($totalOTMin/60, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="{{ 9 + count($dates) }}" class="tc">No data.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>

        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @empty
        <p>No employees found.</p>
    @endforelse
@endif

{{-- =============================================================== --}}
{{-- OT SUMMARY (designation/section grouping, day columns) --}}
{{-- =============================================================== --}}
@if($reportType === 'ot-summary')
    @php
        $bySection = $employees->groupBy('section_id');
    @endphp

    <div class="report-head">
        <h3>{{ $company }}</h3>
        <p>{{ $address }}</p>
    </div>

    <div class="sub-title">OT Summary ({{ $fromLabel }} To {{ $toLabel }})</div>

    @forelse($bySection as $sectionId => $sectionEmps)
        <div class="section-title">Section: {{ $sectionMap->get($sectionId, 'N/A') }}</div>

        @php
            $byDesignation = $sectionEmps->groupBy('designation_id');
        @endphp

        <div style="overflow-x:auto;">
        <table class="t">
            <thead>
                <tr>
                    <th>SI</th>
                    <th>Designation</th>
                    <th>Section</th>
                    @foreach($dates as $d)
                        <th class="tc" style="min-width:28px;">{{ $d->format('d') }}</th>
                    @endforeach
                    <th>To. OT</th>
                </tr>
            </thead>
            <tbody>
                @forelse($byDesignation as $desigId => $desigEmps)
                    @php
                        $dayTotals = [];
                        $grandOTMin = 0;
                        foreach($dates as $d) {
                            $dayOT = 0;
                            foreach($desigEmps as $emp) {
                                $att = $getAtt($emp->id, $d->toDateString());
                                $dayOT += $att ? (int)($att->overtime_minutes ?? 0) : 0;
                            }
                            $dayTotals[$d->toDateString()] = $dayOT;
                            $grandOTMin += $dayOT;
                        }
                    @endphp
                    <tr>
                        <td class="tc">{{ $loop->iteration }}</td>
                        <td>{{ $designationMap->get($desigId, 'N/A') }}</td>
                        <td>{{ $sectionMap->get($sectionId, 'N/A') }}</td>
                        @foreach($dates as $d)
                            <td class="tc" style="font-size:9px;">
                                {{ $dayTotals[$d->toDateString()] > 0 ? number_format($dayTotals[$d->toDateString()]/60,2) : '' }}
                            </td>
                        @endforeach
                        <td class="tc">{{ number_format($grandOTMin/60, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="{{ 4 + count($dates) }}" class="tc">No data.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    @empty
        <p>No employees found.</p>
    @endforelse
@endif

@endsection
