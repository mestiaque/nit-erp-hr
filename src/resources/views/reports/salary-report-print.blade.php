@extends('printMaster2')

@section('title', $reportTypeLabel . ' - ' . $fromLabel . ' To ' . $toLabel)

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
.photo-cell img { max-width:28px; max-height:34px; }
</style>
@endpush
@section('contents')
@php
    $company = general()->title ?? 'Company Name';
    $address = general()->address_one ?? '';
    $fmt     = fn($v) => number_format((float)$v, 2);
    $byDept  = $employees->groupBy('department_id');
    $employeeDataFn = \App\Services\HrOptionsService::getOptionsForEmployee();

    // Use central HR options service for all lookups
    $hrOptions = \App\Services\HrOptionsService::getOptions();
    $departmentMap = collect($hrOptions['departments'])->pluck('name', 'id');
    $sectionMap = collect($hrOptions['sections'])->pluck('name', 'id');
    $subSectionMap = collect($hrOptions['subSections'])->pluck('name', 'id');
    $designationMap = \ME\Hr\Models\Designation::query()->pluck('name', 'id');

    // Helper: use same earnings/deductions + OT adjustment logic as payslip
    $empSalary = function($userId, $emp = null) use ($salarySheets, $employeeDataFn, $request, $from, $to) {
        $sheets = $salarySheets->get($userId, collect());
        if (!$emp) {
            return [
                'gross' => 0,
                'basic' => 0,
                'house_rent' => 0,
                'medical' => 0,
                'transport' => 0,
                'total_earn' => 0,
                'total_deduct' => 0,
                'net' => 0,
                'ot' => 0,
                'ot_hours' => 0,
                'present' => 0,
                'absent' => 0,
            ];
        }

        $factoryNo = (int) (hr_factory('factory_no') ?? 0);
        $employeeData = $employeeDataFn($emp, $request ?? null, null, null, null, null);
        $salaryReport = $employeeData['getSalaryReport']($from, $to);
        $sal          = hr_employee_salary($emp);
        $otRate       = (float) ($employeeData['salary']['ot_rate'] ?? $sal['ot_rate'] ?? 0);

        $attendancePack = \App\Services\EmployeeAttendanceService::getEmployeeAttendanceByDate(
            $emp->id,
            $from,
            $to
        );
        $summary = $attendancePack['summary'] ?? [];
        $otHours = ($factoryNo === 1 || $factoryNo === 2)
            ? (float) ($summary['totalComplianceOt'] ?? 0)
            : (float) ($summary['totalOt'] ?? 0);
        $otAmount = round($otHours * $otRate, 2);

        $present = $sheets->isNotEmpty() ? (int) $sheets->sum('present_days') : 0;
        $absent  = $sheets->isNotEmpty() ? (int) $sheets->sum('absent_days') : 0;

        return [
            'gross'        => (float) ($salaryReport['gross'] ?? $sal['gross'] ?? 0),
            'basic'        => (float) ($salaryReport['basic'] ?? $sal['basic'] ?? 0),
            'house_rent'   => (float) ($sal['house'] ?? 0),
            'medical'      => (float) ($sal['medical'] ?? 0),
            'transport'    => (float) ($sal['transport'] ?? 0),
            'total_earn'   => (float) ($salaryReport['total_earn'] ?? 0),
            'total_deduct' => (float) ($salaryReport['total_deduct'] ?? 0),
            'net'          => (float) ($salaryReport['net'] ?? 0),
            // OT hours/amount follow payslip & job-card factory logic
            'ot'           => $otAmount,
            'ot_hours'     => $otHours,
            'present'      => $present,
            'absent'       => $absent,
        ];
    };
@endphp


<div class="report-head">
    <h3>{{ $company }}</h3>
    <p>{{ $address }}</p>
</div>

<div class="sub-title">{{ $reportTypeLabel }} ({{ $fromLabel }} To {{ $toLabel }})</div>

@if($reportType === 'wages-salary-summary')
    {{-- ── WAGES & SALARY SUMMARY ── --}}
    <table class="t">
        <thead>
            <tr>
                <th>SI</th>
                <th>Department</th>
                <th>Section</th>
                <th>Employees</th>
                <th>Gross Salary</th>
                <th>Total Earning</th>
                <th>Total Deduction</th>
                <th>Net Salary</th>
            </tr>
        </thead>
        <tbody>
            @php $sl = 1; $gEarning = 0; $gDeduct = 0; $gNet = 0; $gEmp = 0; @endphp
            @forelse($byDept as $deptId => $deptEmps)
                @php
                    $bySec = $deptEmps->groupBy('section_id');
                @endphp
                @foreach($bySec as $secId => $secEmps)
                    @php
                        $totalEarn = 0; $totalDeduct = 0; $totalNet = 0;
                        $grossSum = 0;
                        foreach($secEmps as $emp) {
                            $sd = $empSalary($emp->id, $emp);
                            $grossSum    += $sd['gross'];
                            $totalEarn   += $sd['total_earn'];
                            $totalDeduct += $sd['total_deduct'];
                            $totalNet    += $sd['net'];
                        }
                        $cnt = $secEmps->count();
                        $gEarning += $totalEarn;
                        $gDeduct  += $totalDeduct;
                        $gNet     += $totalNet;
                        $gEmp     += $cnt;
                    @endphp
                    <tr>
                        <td class="tc">{{ $sl++ }}</td>
                        <td>{{ $departmentMap->get($deptId, 'N/A') }}</td>
                        <td>{{ $sectionMap->get($secId, 'N/A') }}</td>
                        <td class="tc">{{ $cnt }}</td>
                        <td class="tr">{{ $fmt($grossSum) }}</td>
                        <td class="tr">{{ $fmt($totalEarn) }}</td>
                        <td class="tr">{{ $fmt($totalDeduct) }}</td>
                        <td class="tr">{{ $fmt($totalNet) }}</td>
                    </tr>
                @endforeach
            @empty
                <tr><td colspan="8" class="tc">No data.</td></tr>
            @endforelse
            <tr class="summary-row">
                <td colspan="3" class="tr">Grand Total:</td>
                <td class="tc">{{ $gEmp }}</td>
                <td></td>
                <td class="tr">{{ $fmt($gEarning) }}</td>
                <td class="tr">{{ $fmt($gDeduct) }}</td>
                <td class="tr">{{ $fmt($gNet) }}</td>
            </tr>
        </tbody>
    </table>

@else
    {{-- ── FIXED / PRODUCTION / BONUS SALARY DETAILS ── --}}
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
                    <th>Section</th>
                    <th>Sub-Section</th>
                    <th>Block/Line</th>
                    <th>Join Date</th>
                    <th>Gross</th>
                    <th>Basic</th>
                    <th>OT Hrs</th>
                    <th>OT Amt</th>
                    <th>Total Earn</th>
                    <th>Deduction</th>
                    <th>Net Pay</th>
                    <th>Present</th>
                    <th>Absent</th>
                    <th>Signature</th>
                </tr>
            </thead>
            <tbody>
                @php $sl = 1; $totalNet = 0; @endphp
                @foreach($deptEmps as $employee)
                    @php
                        $sd = $empSalary($employee->id, $employee);
                        $totalNet += $sd['net'];
                    @endphp
                    <tr>
                        <td class="tc">{{ $sl++ }}</td>
                        @if($withPicture)
                            <td class="tc photo-cell">
                                @if($employee->photo)
                                    <img src="{{ asset('storage/' . $employee->photo) }}" alt="">
                                @else —
                                @endif
                            </td>
                        @endif
                        <td>{{ $employee->employee_id }}</td>
                        <td>{{ $language === 'bn' && $employee->bn_name ? $employee->bn_name : $employee->name }}</td>
                        <td>{{ $designationMap->get($employee->designation_id, 'N/A') }}</td>
                        <td>{{ $sectionMap->get($employee->section_id, 'N/A') }}</td>
                        <td>{{ $subSectionMap->get($employee->otherInfo()['profile']['sub_section_id']) }}</td>
                        <td>{{ $lineMap->get($employee->line_number, 'N/A') }}</td>
                        <td class="tc">{{ optional($employee->joining_date)->format('d-M-y') ?? '-' }}</td>
                        <td class="tr">{{ $fmt($sd['gross']) }}</td>
                        <td class="tr">{{ $fmt($sd['basic']) }}</td>
                        <td class="tc">{{ number_format($sd['ot_hours'], 2) }}</td>
                        <td class="tr">{{ $fmt($sd['ot']) }}</td>
                        <td class="tr">{{ $fmt($sd['total_earn']) }}</td>
                        <td class="tr">{{ $fmt($sd['total_deduct']) }}</td>
                        <td class="tr">{{ $fmt($sd['net']) }}</td>
                        <td class="tc">{{ $sd['present'] }}</td>
                        <td class="tc">{{ $sd['absent'] }}</td>
                        <td></td>
                    </tr>
                @endforeach
                <tr class="summary-row">
                    <td colspan="{{ $withPicture ? 15 : 14 }}" class="tr">Total Net Pay:</td>
                    <td class="tr">{{ $fmt($totalNet) }}</td>
                    <td></td><td></td><td></td>
                </tr>
            </tbody>
        </table>
    @empty
        <p>No employees found.</p>
    @endforelse
@endif

@endsection
