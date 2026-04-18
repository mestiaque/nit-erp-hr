@extends('printMaster2')

@section('title', ($categoryLabel ?? 'Bonus') . ' Bonus Sheet')

@push('css')
<style>
.report-head { text-align:center; margin-bottom:10px; }
.report-head h3 { margin:0 0 2px; font-size:15px; }
.report-head p  { margin:0; font-size:11px; }
.sub-title  { font-size:12px; font-weight:700; margin:8px 0 4px; }
.dept-title { font-size:11px; font-weight:700; background:#dde6f0; padding:3px 6px; margin:10px 0 2px; }
.t { width:100%; border-collapse:collapse; margin-bottom:10px; font-size:10px; }
.t th, .t td { border:1px solid #555; padding:3px 5px; }
.t th { background:#eef1d4; text-align:center; }
.tc { text-align:center; }
.tr { text-align:right; }
.summary-row td { background:#e8f5e9; font-weight:700; }
.photo-cell img { max-width:30px; max-height:35px; }
</style>
@endpush

@section('contents')
@php
    $company = general()->title ?? 'Company Name';
    $address = general()->address_one ?? '';
    $fmt     = fn($v) => number_format((float)$v, 2);
    $byDept  = $employees->groupBy('department_id');
    // Use central HR options service for all lookups
    $hrOptions = \App\Services\HrOptionsService::getOptions();
    $departmentMap = collect($hrOptions['departments'])->pluck('name', 'id');
    $sectionMap = collect($hrOptions['sections'])->pluck('name', 'id');
    $subSectionMap = collect($hrOptions['subSections'])->pluck('name', 'id');
    $designationMap = collect($hrOptions['designations'])->pluck('name', 'id');
@endphp

<div class="report-head">
    <h3>{{ $company }}</h3>
    <p>{{ $address }}</p>
</div>

<div class="sub-title">
    {{ $categoryLabel }} Bonus Sheet
    @if($bonusTitle) — {{ $bonusTitle->title }} @endif
    @if($category === 'fixed')
        (Up To {{ $upToDateLabel }})
    @else
        ({{ $fromLabel }} To {{ $toLabel }})
    @endif
</div>

@forelse($byDept as $deptId => $deptEmps)
    <div class="dept-title">Department: {{ $departmentMap->get($deptId, 'N/A') }}</div>

    <table class="t">
        <thead>
            <tr>
                <th>SL</th>
                @if($withPicture)<th>Photo</th>@endif
                <th>Emp. ID</th>
                <th>Name</th>
                <th>Designation</th>
                <th>Join Date</th>
                @if($category === 'fixed')
                    <th>Job Age</th>
                    <th>Gross Salary</th>
                    <th>Basic Salary</th>
                    <th>Present (%)</th>
                    <th>Stamp</th>
                    <th>Bonus Amount</th>
                    <th>Signature &amp; Stamp</th>
                @else
                    <th>Section</th>
                    <th>Sub-Section</th>
                    <th>Block/Line</th>
                    <th>Present Days</th>
                    <th>Bonus Amount</th>
                    <th>Signature</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @php
                $sl = 1;
                $totalBonus = 0;
            @endphp
            @foreach($deptEmps as $employee)
                @php
                    $info = $attendanceSummary[$employee->id] ?? [];
                    $gross   = (float)($employee->gross_salary ?? 0);
                    $basic   = (float)($employee->basic_salary ?? 0);
                    $percent = $info['percent'] ?? 0;
                    $present = $info['present'] ?? 0;
                    // Simple bonus = gross salary (100% present) or proportional
                    $bonus = $category === 'fixed'
                        ? round($gross * $percent / 100, 2)
                        : 0; // production bonus calculation depends on policy
                    $totalBonus += $bonus;

                    // Job age
                    $jobAge = 'N/A';
                    if ($employee->joining_date) {
                        $jd  = $employee->joining_date instanceof \Carbon\Carbon
                            ? $employee->joining_date
                            : \Carbon\Carbon::parse($employee->joining_date);
                        $ref = \Carbon\Carbon::parse($upToDate);
                        $diff = $jd->diff($ref);
                        $jobAge = sprintf('%dy %dm %dd', $diff->y, $diff->m, $diff->d);
                    }
                @endphp
                <tr>
                    <td class="tc">{{ $sl++ }}</td>
                    @if($withPicture)
                        <td class="tc photo-cell">
                            @if($employee->photo)
                                <img src="{{ asset('storage/' . $employee->photo) }}" alt="">
                            @else
                                —
                            @endif
                        </td>
                    @endif
                    <td>{{ $employee->employee_id }}</td>
                    <td>{{ $language === 'bn' && $employee->bn_name ? $employee->bn_name : $employee->name }}</td>
                    <td>{{ $designationMap->get($employee->designation_id, 'N/A') }}</td>
                    <td class="tc">{{ optional($employee->joining_date)->format('d-M-y') ?? '-' }}</td>
                    @if($category === 'fixed')
                        <td class="tc">{{ $jobAge }}</td>
                        <td class="tr">{{ $fmt($gross) }}</td>
                        <td class="tr">{{ $fmt($basic) }}</td>
                        <td class="tc">{{ $percent }}%</td>
                        <td></td>
                        <td class="tr">{{ $fmt($bonus) }}</td>
                        <td></td>
                    @else
                        <td>{{ $sectionMap->get($employee->section_id, 'N/A') }}</td>
                        <td>{{ $subSectionMap->get($employee->hr_sub_section_id ?? $employee->sub_section_id ?? null, 'N/A') }}</td>
                        <td>{{ $lineMap->get($employee->line_number, 'N/A') }}</td>
                        <td class="tc">{{ $present }}</td>
                        <td class="tr">{{ $fmt($bonus) }}</td>
                        <td></td>
                    @endif
                </tr>
            @endforeach
            <tr class="summary-row">
                <td colspan="{{ $withPicture ? 6 : 5 }}" class="tr">Total:</td>
                @if($category === 'fixed')
                    <td></td><td></td><td></td><td></td><td></td>
                    <td class="tr">{{ $fmt($totalBonus) }}</td>
                    <td></td>
                @else
                    <td></td><td></td><td></td><td></td>
                    <td class="tr">{{ $fmt($totalBonus) }}</td>
                    <td></td>
                @endif
            </tr>
        </tbody>
    </table>
@empty
    <p>No employees found.</p>
@endforelse

@endsection
