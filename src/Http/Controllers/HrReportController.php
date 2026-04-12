<?php

namespace ME\Hr\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use ME\Hr\Models\EmployeeIncrement;
use ME\Hr\Models\BonusPolicy;
use ME\Hr\Models\Designation;
use ME\Hr\Models\ProductionBonus;
use ME\Hr\Models\Shift;
use ME\Hr\Models\SubSection;
use ME\Hr\Models\WorkingPlace;
use App\Models\Attribute;
use PHPUnit\Util\PHP\Job;

class HrReportController extends Controller
{
    public function lockMonthlyIncrement(Request $request)
    {
        $payload = $request->validate([
            'effective_date' => 'required|date',
            'increment_percent' => 'required|numeric|min:0|max:100',
            'from' => 'nullable|date',
            'to' => 'nullable|date',
            'classification' => 'nullable|integer',
            'department' => 'nullable|integer',
            'section' => 'nullable|integer',
            'sub_section' => 'nullable|integer',
            'working_place' => 'nullable|integer',
            'salary_type' => 'nullable|string|max:50',
            'designation' => 'nullable|integer',
            'line_number' => 'nullable|integer',
        ]);

        $filterRequest = new Request($payload);
        $employees = $this->employeeReportQuery($filterRequest)
            ->with(['designation', 'department'])
            ->orderBy('name')
            ->get();

        $options = $this->employeeReportOptions();
        $incrementMap = $this->latestIncrements($employees);
        $data = $this->monthlyIncrementData(
            $employees,
            $options,
            $filterRequest,
            $incrementMap,
            (float) $payload['increment_percent'],
            (string) $payload['effective_date'],
            false
        );

        $rows = collect($data['rows'] ?? []);
        if ($rows->isEmpty()) {
            return back()->with('error', 'No employee found for increment lock.');
        }

        DB::transaction(function () use ($rows, $payload) {
            foreach ($rows as $row) {
                $employee = User::query()
                    ->filterByType('employee')
                    ->find(data_get($row, 'user_id'));
                if (!$employee) {
                    continue;
                }

                $this->upsertIncrementRecord(
                    $employee,
                    (string) $payload['effective_date'],
                    (float) data_get($row, 'gross_salary', 0),
                    (float) data_get($row, 'inc_value', 0),
                    (float) data_get($row, 'inc_percent', 0),
                    (float) data_get($row, 'final_gross', 0)
                );
            }
        });

        return back()->with('success', 'Increment locked successfully for selected employees.');
    }

    public function index()
    {
        $reports = config('hr.reports', []);

        return view('hr::reports.index', compact('reports'));
    }

    public function show(string $report, Request $request)
    {
        abort_unless(array_key_exists($report, config('hr.reports', [])), 404);

        if ($report === 'employee') {
            return $this->employeeReportScreen($request, $report);
        }

        if ($report === 'monthly') {
            return $this->monthlyReportScreen($request, $report);
        }

        if ($report === 'personal-file') {
            return $this->personalFileReportScreen($request, $report);
        }

        [$columns, $rows] = match ($report) {
            'employee' => $this->employeeReport(),
            'monthly' => $this->monthlyReport(),
            'machine-id' => $this->machineIdReport(),
            'job-card' => $this->jobCardReport(),
            'personal-file' => $this->personalFileReport(),
            'attendance' => $this->attendanceReport(),
            'tiffin-night-dinner' => $this->mealAllowanceReport(),
            'pro-job-card' => $this->productionJobCardReport(),
            'bonus-salary-fixed' => $this->bonusSalaryFixedReport(),
            'bonus-salary-production' => $this->bonusSalaryProductionReport(),
            'salary-fixed' => $this->salaryFixedReport(),
            'salary-production' => $this->salaryProductionReport(),
            'salary-summary' => $this->salarySummaryReport(),
            default => [[], collect()],
        };

        return view('hr::reports.show', [
            'reportKey' => $report,
            'reportTitle' => config('hr.reports.' . $report),
            'columns' => $columns,
            'rows' => $rows,
            'request' => $request,
        ]);
    }

    private function monthlyReportScreen(Request $request, string $report)
    {
        $options = $this->employeeReportOptions();
        $reportTypes = [
            'recruitment' => 'Recruitment',
            'migration' => 'Migration',
            'long-absent' => 'Long Absent',
            'increment' => 'Increment',
            'increment-summary' => 'Increment Report',
        ];

        $reportType = (string) $request->input('report_type', 'recruitment');
        if (!array_key_exists($reportType, $reportTypes)) {
            $reportType = 'recruitment';
        }

        $incrementPercent = (float) $request->input('increment_percent', 0);
        $effectiveDate = $request->input('effective_date');

        $employees = $this->employeeReportQuery($request)
            ->with(['designation', 'department'])
            ->orderBy('name')
            ->get();

        $incrementMap = $this->latestIncrements($employees);

        $data = match ($reportType) {
            'recruitment' => $this->monthlyRecruitmentData($employees, $options, $request),
            'migration' => $this->monthlyMigrationData($employees, $options, $request),
            'long-absent' => $this->monthlyLongAbsentData($employees, $options, $request),
            'increment' => $this->monthlyIncrementData($employees, $options, $request, $incrementMap, $incrementPercent, $effectiveDate, false),
            'increment-summary' => $this->monthlyIncrementData($employees, $options, $request, $incrementMap, $incrementPercent, $effectiveDate, true),
            default => ['rows' => collect()],
        };

        if ($request->boolean('print')) {
            return view('hr::reports.monthly-print', [
                'reportKey' => $report,
                'reportTitle' => config('hr.reports.' . $report),
                'reportType' => $reportType,
                'reportTypeLabel' => $reportTypes[$reportType],
                'request' => $request,
                'options' => $options,
                'data' => $data,
                'incrementPercent' => $incrementPercent,
                'effectiveDate' => $effectiveDate,
            ]);
        }

        return view('hr::reports.monthly', [
            'reportKey' => $report,
            'reportTitle' => config('hr.reports.' . $report),
            'request' => $request,
            'options' => $options,
            'reportTypes' => $reportTypes,
            'reportType' => $reportType,
            'incrementPercent' => $incrementPercent,
            'effectiveDate' => $effectiveDate,
        ]);
    }

    private function monthlyRecruitmentData($employees, array $options, Request $request): array
    {
        $classificationMap = collect($options['classifications'] ?? [])->pluck('name', 'id');
        $departmentMap = collect($options['departments'] ?? [])->pluck('name', 'id');
        $sectionMap = collect($options['sections'] ?? [])->pluck('name', 'id');
        $designationMap = collect($options['designations'] ?? [])->pluck('name', 'id');
        $gradeMap = Attribute::query()->pluck('name', 'id');

        $rows = $employees
            ->filter(function (User $employee) use ($request) {
                if (blank($employee->joining_date)) {
                    return false;
                }

                $joinDate = $employee->joining_date instanceof \Carbon\Carbon
                    ? $employee->joining_date
                    : \Carbon\Carbon::parse($employee->joining_date);

                if ($request->filled('from') && $joinDate->lt(\Carbon\Carbon::parse($request->from)->startOfDay())) {
                    return false;
                }
                if ($request->filled('to') && $joinDate->gt(\Carbon\Carbon::parse($request->to)->endOfDay())) {
                    return false;
                }

                return true;
            })
            ->values();

        $detailRows = $rows->map(function (User $employee) use ($classificationMap, $departmentMap, $sectionMap, $designationMap, $gradeMap) {
            return [
                'employee_id' => $employee->employee_id,
                'name' => $employee->name,
                'department' => $departmentMap->get($employee->department_id, 'N/A'),
                'section' => $sectionMap->get($employee->section_id, 'N/A'),
                'join_date' => optional($employee->joining_date)->format('d-M-Y'),
                'contact' => $employee->mobile,
                'classification' => $classificationMap->get($employee->employee_type, 'N/A'),
                'designation' => $designationMap->get($employee->designation_id, 'N/A'),
                'grade' => $gradeMap->get($employee->grade_lavel, 'N/A'),
                'gross_salary' => (float) ($employee->gross_salary ?? 0),
            ];
        })->values();

        $summaryRows = $rows
            ->groupBy(function (User $employee) {
                return implode('|', [
                    $employee->department_id,
                    $employee->section_id,
                    $employee->employee_type,
                    $employee->designation_id,
                ]);
            })
            ->map(function ($group) use ($departmentMap, $sectionMap, $classificationMap, $designationMap) {
                /** @var User $first */
                $first = $group->first();

                return [
                    'department' => $departmentMap->get($first->department_id, 'N/A'),
                    'section' => $sectionMap->get($first->section_id, 'N/A'),
                    'classification' => $classificationMap->get($first->employee_type, 'N/A'),
                    'designation' => $designationMap->get($first->designation_id, 'N/A'),
                    'total_employees' => $group->count(),
                    'total_gross_salary' => $group->sum(fn (User $employee) => (float) ($employee->gross_salary ?? 0)),
                ];
            })
            ->values();

        return [
            'rows' => $detailRows,
            'summary_rows' => $summaryRows,
        ];
    }

    private function monthlyMigrationData($employees, array $options, Request $request): array
    {
        $departmentMap = collect($options['departments'] ?? [])->pluck('name', 'id');
        $sectionMap = collect($options['sections'] ?? [])->pluck('name', 'id');
        $designationMap = collect($options['designations'] ?? [])->pluck('name', 'id');

        $rows = $employees
            ->filter(function (User $employee) use ($request) {
                $status = strtolower((string) ($employee->employment_status ?? ''));
                if (!in_array($status, ['transfer', 'lefty', 'left', 'resign', 'resigned'], true)) {
                    return false;
                }

                $migrationDate = $employee->exited_at;
                if (blank($migrationDate)) {
                    return !$request->filled('from') && !$request->filled('to');
                }

                $date = \Carbon\Carbon::parse($migrationDate);
                if ($request->filled('from') && $date->lt(\Carbon\Carbon::parse($request->from)->startOfDay())) {
                    return false;
                }
                if ($request->filled('to') && $date->gt(\Carbon\Carbon::parse($request->to)->endOfDay())) {
                    return false;
                }

                return true;
            })
            ->map(function (User $employee) use ($departmentMap, $sectionMap, $designationMap) {
                $other = json_decode($employee->other_information ?? '{}', true);
                $status = (string) ($employee->employment_status ?? 'N/A');

                return [
                    'employee_id' => $employee->employee_id,
                    'name' => $employee->name,
                    'department' => $departmentMap->get($employee->department_id, 'N/A'),
                    'section' => $sectionMap->get($employee->section_id, 'N/A'),
                    'designation' => $designationMap->get($employee->designation_id, 'N/A'),
                    'migration_type' => ucfirst($status),
                    'migration_date' => $employee->exited_at,
                    'remarks' => data_get($other, 'resign_info.remarks', ''),
                ];
            })
            ->values();

        return ['rows' => $rows];
    }

    private function monthlyLongAbsentData($employees, array $options, Request $request): array
    {
        $departmentMap = collect($options['departments'] ?? [])->pluck('name', 'id');
        $sectionMap = collect($options['sections'] ?? [])->pluck('name', 'id');
        $designationMap = collect($options['designations'] ?? [])->pluck('name', 'id');

        $rows = $employees
            ->map(function (User $employee) {
                $other = json_decode($employee->other_information ?? '{}', true);
                $absentDate = data_get($other, 'final_settlement.absent_date');

                return [
                    'employee' => $employee,
                    'absent_date' => $absentDate,
                    'remarks' => data_get($other, 'resign_info.remarks', data_get($other, 'final_settlement.remarks', '')),
                ];
            })
            ->filter(function ($row) use ($request) {
                if (blank($row['absent_date'])) {
                    return false;
                }

                $date = \Carbon\Carbon::parse($row['absent_date']);
                if ($request->filled('from') && $date->lt(\Carbon\Carbon::parse($request->from)->startOfDay())) {
                    return false;
                }
                if ($request->filled('to') && $date->gt(\Carbon\Carbon::parse($request->to)->endOfDay())) {
                    return false;
                }

                return true;
            })
            ->map(function ($row) use ($departmentMap, $sectionMap, $designationMap, $request) {
                /** @var User $employee */
                $employee = $row['employee'];
                $absentDate = \Carbon\Carbon::parse($row['absent_date']);
                $endDate = $request->filled('to') ? \Carbon\Carbon::parse($request->to) : now();

                return [
                    'employee_id' => $employee->employee_id,
                    'name' => $employee->name,
                    'doj' => optional($employee->joining_date)->format('d-M-Y'),
                    'designation' => $designationMap->get($employee->designation_id, 'N/A'),
                    'department' => $departmentMap->get($employee->department_id, 'N/A'),
                    'section' => $sectionMap->get($employee->section_id, 'N/A'),
                    'absent_days' => max(0, $absentDate->diffInDays($endDate)),
                    'absent_date' => $absentDate->format('d-M-Y'),
                    'remarks' => $row['remarks'] ?: 'N/A',
                ];
            })
            ->values();

        return ['rows' => $rows];
    }

    private function monthlyIncrementData($employees, array $options, Request $request, array $incrementMap, float $incrementPercent, ?string $effectiveDate, bool $withRemarks): array
    {
        $classificationMap = collect($options['classifications'] ?? [])->pluck('name', 'id');
        $departmentMap = collect($options['departments'] ?? [])->pluck('name', 'id');
        $sectionMap = collect($options['sections'] ?? [])->pluck('name', 'id');
        $subSectionMap = collect($options['subSections'] ?? [])->pluck('name', 'id');
        $designationMap = collect($options['designations'] ?? [])->pluck('name', 'id');
        $lineMap = collect($options['lines'] ?? [])->mapWithKeys(fn ($row) => [
            $row->id => trim(($row->name ?? '') . (filled($row->slug ?? null) ? ' - ' . $row->slug : '')),
        ]);
        $gradeMap = Attribute::query()->pluck('name', 'id');

        $rows = $employees
            ->map(function (User $employee) use ($incrementMap, $request) {
                $increment = $incrementMap[$employee->id] ?? null;
                $lastIncDate = data_get($increment, 'increment_date', data_get($increment, 'date'));

                if ($request->filled('from') || $request->filled('to')) {
                    if (blank($lastIncDate)) {
                        return null;
                    }

                    $date = \Carbon\Carbon::parse($lastIncDate);
                    if ($request->filled('from') && $date->lt(\Carbon\Carbon::parse($request->from)->startOfDay())) {
                        return null;
                    }
                    if ($request->filled('to') && $date->gt(\Carbon\Carbon::parse($request->to)->endOfDay())) {
                        return null;
                    }
                }

                return [
                    'employee' => $employee,
                    'increment' => $increment,
                ];
            })
            ->filter()
            ->values()
            ->map(function ($item) use ($classificationMap, $departmentMap, $sectionMap, $subSectionMap, $designationMap, $lineMap, $gradeMap, $incrementPercent, $effectiveDate, $withRemarks) {
                /** @var User $employee */
                $employee = $item['employee'];
                $increment = $item['increment'];

                $other = json_decode($employee->other_information ?? '{}', true);
                $profile = is_array($other) ? data_get($other, 'profile', []) : [];
                $grossSalary = (float) ($employee->gross_salary ?? 0);
                $incValue = ($grossSalary * max(0, $incrementPercent)) / 100;
                $finalGross = $grossSalary + $incValue;

                $lastIncValue = (float) data_get($increment, 'increment_amount', data_get($increment, 'gross_increment_amount', data_get($increment, 'amount', 0)));
                $lastIncDate = data_get($increment, 'increment_date', data_get($increment, 'date'));

                $serviceLength = 'N/A';
                if (!blank($employee->joining_date)) {
                    $join = $employee->joining_date instanceof \Carbon\Carbon
                        ? $employee->joining_date
                        : \Carbon\Carbon::parse($employee->joining_date);
                    $ref = !blank($effectiveDate) ? \Carbon\Carbon::parse($effectiveDate) : now();
                    $diff = $join->diff($ref);
                    $serviceLength = sprintf('%dy %dm %dd', $diff->y, $diff->m, $diff->d);
                }

                $row = [
                    'user_id' => $employee->id,
                    'employee_id' => $employee->employee_id,
                    'name' => $employee->name,
                    'service_length' => $serviceLength,
                    'department' => $departmentMap->get($employee->department_id, 'N/A'),
                    'section' => $sectionMap->get($employee->section_id, 'N/A'),
                    'sub_section' => $subSectionMap->get($employee->sub_section_id ?? data_get($profile, 'sub_section_id'), 'N/A'),
                    'designation' => $designationMap->get($employee->designation_id, 'N/A'),
                    'grade' => $gradeMap->get($employee->grade_lavel, 'N/A'),
                    'classification' => $classificationMap->get($employee->employee_type, 'N/A'),
                    'line_block' => $lineMap->get($employee->line_number, 'N/A'),
                    'join_date' => optional($employee->joining_date)->format('d-M-Y'),
                    'last_inc_date' => $lastIncDate ? \Carbon\Carbon::parse($lastIncDate)->format('d-M-Y') : 'N/A',
                    'last_inc_value' => $lastIncValue,
                    'gross_salary' => $grossSalary,
                    'inc_percent' => max(0, $incrementPercent),
                    'inc_value' => $incValue,
                    'final_gross' => $finalGross,
                    'effective_date' => !blank($effectiveDate) ? \Carbon\Carbon::parse($effectiveDate)->format('d-M-Y') : 'N/A',
                ];

                if ($withRemarks) {
                    $row['remarks'] = data_get($increment, 'remarks', '');
                }

                return $row;
            })
            ->values();

        $summary = [
            'employee_count' => $rows->count(),
            'total_increment_value' => $rows->sum('inc_value'),
            'total_final_gross' => $rows->sum('final_gross'),
        ];

        return [
            'rows' => $rows,
            'summary' => $summary,
        ];
    }

    private function upsertIncrementRecord(User $employee, string $effectiveDate, float $previousSalary, float $incrementValue, float $incrementPercent, float $newSalary): void
    {
        $table = (new EmployeeIncrement())->getTable();

        if (Schema::hasTable($table)) {
            $query = EmployeeIncrement::query();
            if (Schema::hasColumn($table, 'user_id')) {
                $query->where('user_id', $employee->id);
            } elseif (Schema::hasColumn($table, 'employee_id')) {
                $query->where('employee_id', $employee->id);
            }

            if (Schema::hasColumn($table, 'increment_date')) {
                $query->whereDate('increment_date', $effectiveDate);
            } elseif (Schema::hasColumn($table, 'date')) {
                $query->whereDate('date', $effectiveDate);
            }

            $row = $query->first() ?? new EmployeeIncrement();

            if (Schema::hasColumn($table, 'user_id')) {
                $row->user_id = $employee->id;
            }
            if (Schema::hasColumn($table, 'employee_id')) {
                $row->employee_id = $employee->id;
            }
            if (Schema::hasColumn($table, 'increment_date')) {
                $row->increment_date = $effectiveDate;
            } elseif (Schema::hasColumn($table, 'date')) {
                $row->date = $effectiveDate;
            }
            if (Schema::hasColumn($table, 'previous_salary')) {
                $row->previous_salary = $previousSalary;
            }
            if (Schema::hasColumn($table, 'increment_amount')) {
                $row->increment_amount = $incrementValue;
            }
            if (Schema::hasColumn($table, 'gross_increment_amount')) {
                $row->gross_increment_amount = $incrementValue;
            }
            if (Schema::hasColumn($table, 'amount')) {
                $row->amount = $incrementValue;
            }
            if (Schema::hasColumn($table, 'increment_percentage')) {
                $row->increment_percentage = $incrementPercent;
            }
            if (Schema::hasColumn($table, 'new_salary')) {
                $row->new_salary = $newSalary;
            }
            if (Schema::hasColumn($table, 'remarks')) {
                $row->remarks = 'Locked from monthly increment report';
            }
            if (Schema::hasColumn($table, 'approved_by')) {
                $row->approved_by = Auth::id();
            }

            $row->save();

            return;
        }

        $other = json_decode($employee->other_information ?? '{}', true);
        $other = is_array($other) ? $other : [];
        $rows = collect(data_get($other, 'increments', []));

        $existingIndex = $rows->search(function ($row) use ($effectiveDate) {
            $date = data_get($row, 'increment_date', data_get($row, 'date'));
            return (string) $date === (string) $effectiveDate;
        });

        $newRow = [
            'amount' => $incrementValue,
            'increment_date' => $effectiveDate,
            'increment_percentage' => $incrementPercent,
            'previous_salary' => $previousSalary,
            'new_salary' => $newSalary,
            'remarks' => 'Locked from monthly increment report',
            'created_at' => now()->toDateTimeString(),
        ];

        if ($existingIndex !== false) {
            $rows[$existingIndex] = array_merge((array) $rows[$existingIndex], $newRow);
        } else {
            $rows->push($newRow);
        }

        $other['increments'] = $rows->values()->all();
        $employee->other_information = json_encode($other);
        $employee->save();
    }

    private function employeeReportScreen(Request $request, string $report)
    {
        $employees = $this->employeeReportQuery($request)
            ->with(['designation', 'department'])
            ->orderBy('name')
            ->get();

        $options = $this->employeeReportOptions();
        $reportTypes = [
            'database' => 'Database',
            'manpower-summary' => 'Manpower Summary',
            'details' => 'Details',
        ];
        $language = $request->input('language', 'en');

        if ($request->boolean('print')) {
            $reportType = (string) $request->input('report_type', 'database');
            if (! array_key_exists($reportType, $reportTypes)) {
                $reportType = 'database';
            }

            if ($reportType === 'database') {
                return view('hr::reports.employee-database-print', [
                    'employees' => $employees,
                    'request' => $request,
                    'options' => $options,
                ]);
            } elseif ($reportType === 'details') {
                $detailsRows = $this->employeeDetailsRows($employees, $options);
                return view('hr::reports.employee-details-print', [
                    'detailsRows' => $detailsRows,
                    'request' => $request,
                    'options' => $options,
                ]);
            } elseif ($reportType === 'manpower-summary') {
                $manpowerRows = $this->employeeManpowerSummaryRows($employees, $options);
                return view('hr::reports.employee-manpower-print', [
                    'manpowerRows' => $manpowerRows,
                    'request' => $request,
                    'options' => $options,
                ]);
            }
            // fallback
            return view('hr::reports.employee-print', [
                'employees' => $employees,
                'request' => $request,
                'options' => $options,
                'reportType' => $reportType,
                'reportTypeLabel' => $reportTypes[$reportType],
                'language' => $language,
                'manpowerRows' => $this->employeeManpowerSummaryRows($employees, $options),
                'detailsRows' => null,
            ]);
        }

        return view('hr::reports.employee', [
            'reportKey' => $report,
            'reportTitle' => config('hr.reports.' . $report),
            'employees' => $employees,
            'options' => $options,
            'reportTypes' => $reportTypes,
            'request' => $request,
            'language' => $language,
        ]);
    }

       /**
     * Generate rows for the 'details' employee report type.
     * Table header:
     * S.L | Working Place | Emp. ID | Name | Join Date | Job Age | DOB | Age | Sex | Department | Section | Sub Section | Designation | Contact No. | Grade | Classification | Line/Block | Shift | WeekEnd | Gross Salary
     */
    private function employeeDetailsRows($employees, array $options)
    {
        $workingPlaceMap = collect($options['workingPlaces'] ?? [])->pluck('name', 'id');
        $departmentMap = collect($options['departments'] ?? [])->pluck('name', 'id');
        $sectionMap = collect($options['sections'] ?? [])->pluck('name', 'id');
        $subSectionMap = collect($options['subSections'] ?? [])->pluck('name', 'id');
        $designationMap = collect($options['designations'] ?? [])->pluck('name', 'id');
        $gradeMap = \App\Models\Attribute::query()->pluck('name', 'id');
        $classificationMap = collect($options['classifications'] ?? [])->pluck('name', 'id');
        $lineMap = collect($options['lines'] ?? [])->mapWithKeys(fn ($row) => [
            $row->id => trim(($row->name ?? '') . (filled($row->slug ?? null) ? ' - ' . $row->slug : '')),
        ]);
        $shiftMap = \ME\Hr\Models\Shift::query()->pluck('name_of_shift', 'id');

        $rows = collect();
        $serial = 1;
        foreach ($employees as $employee) {
            $other = json_decode($employee->other_information ?? '{}', true);
            $rows->push([
                'sl' => $serial++,
                'working_place' => $workingPlaceMap->get($employee->working_place_id, 'N/A'),
                'name' => $employee->name,
                'employee_id' => $employee->employee_id,
                'join_date' => $employee->joining_date ? \Carbon\Carbon::parse($employee->joining_date)->format('d-M-Y') : 'N/A',
                'gross_salary' => (float) ($employee->gross_salary ?? 0),
                'pay_mode' => $employee->salary_type ?? 'N/A',
                'bank_mobile_no' => $employee->bank_account_no ?? $employee->mobile ?? 'N/A',
                'car_fuel' => $employee->car_fuel ?? '0.00',
                'phone_internet' => $employee->phone_internet ?? '0.00',
                'extra_facility' => $employee->extra_facility ?? '0.00',
                'tax' => $employee->tax ?? '0.00',
                'classification' => $classificationMap->get($employee->employee_type, 'N/A'),
                'department' => $departmentMap->get($employee->department_id, 'N/A'),
                'section' => $sectionMap->get($employee->section_id, 'N/A'),
                'sub_section' => $subSectionMap->get($employee->sub_section_id, 'N/A'),
                'line_block' => $lineMap->get($employee->line_number, 'N/A'),
                'designation' => $designationMap->get($employee->designation_id, 'N/A'),
                'grade' => $gradeMap->get($employee->grade_lavel, 'N/A'),
                'shift' => $shiftMap->get($employee->shift_id, 'N/A'),
                'weekend' => $employee->weekend ?? 'N/A',
                'personal_contact_no' => $employee->personal_contact_no ?? 'N/A',
                'emergency_contact_no' => $employee->emergency_contact_no ?? 'N/A',
                'father_name' => $employee->father_name ?? 'N/A',
                'mother_name' => $employee->mother_name ?? 'N/A',
                'marital_status' => $employee->marital_status ?? 'N/A',
                'spouse_name' => $employee->spouse_name ?? 'N/A',
                'sex' => $employee->gender ?? 'N/A',
                'kids' => $employee->kids ?? 'N/A',
            ]);
        }
        return $rows;
    }

    private function employeeReportQuery(Request $request)
    {
        $query = User::query()->filterByType('employee');

        if ($request->filled('employee_id')) {
            $query->where('employee_id', 'like', '%' . trim((string) $request->employee_id) . '%');
        }

        if ($request->filled('classification')) {
            $query->where('employee_type', (int) $request->classification);
        }

        if ($request->filled('department')) {
            $query->where('department_id', (int) $request->department);
        }

        if ($request->filled('section')) {
            $query->where('section_id', (int) $request->section);
        }

        if ($request->filled('sub_section')) {
            $query->where('sub_section_id', (int) $request->sub_section);
        }

        if ($request->filled('working_place')) {
            $query->where('working_place_id', (int) $request->working_place);
        }

        if ($request->filled('line_number')) {
            $query->where('line_number', (int) $request->line_number);
        }

        if ($request->filled('salary_type')) {
            $query->where('salary_type', (string) $request->salary_type);
        }

        if ($request->filled('designation')) {
            $query->where('designation_id', (int) $request->designation);
        }

        if ($request->filled('gender')) {
            $query->where('gender', (string) $request->gender);
        }

        if ($request->filled('employee_status')) {
            $status = (string) $request->employee_status;
            $query->where(function ($builder) use ($status) {
                if ($status === 'regular') {
                    $builder->whereNull('employment_status')
                        ->orWhere('employment_status', '')
                        ->orWhere('employment_status', 'regular');

                    return;
                }

                $builder->where('employment_status', $status);
                if ($status === 'lefty') {
                    $builder->orWhere('employment_status', 'left');
                }
                if ($status === 'resign') {
                    $builder->orWhere('employment_status', 'resigned');
                }
            });
        }

        return $query;
    }

    private function employeeReportOptions(): array
    {
        $genderOptions = User::query()
            ->filterByType('employee')
            ->whereNotNull('gender')
            ->pluck('gender')
            ->map(fn ($value) => trim((string) $value))
            ->filter()
            ->unique(fn ($value) => strtolower($value))
            ->values();

        return [
            'classifications' => Attribute::where('type', 16)->where('status', '<>', 'temp')->orderBy('name')->get(['id', 'name']),
            'departments' => Attribute::where('type', 3)->where('status', '<>', 'temp')->orderBy('name')->get(['id', 'name']),
            'sections' => Attribute::where('type', 29)->where('status', '<>', 'temp')->orderBy('name')->get(['id', 'name']),
            'subSections' => SubSection::orderBy('name')->get(['id', 'name', 'department_id', 'section_id', 'salary_type', 'approve_man_power']),
            'lines' => Attribute::where('type', 4)->where('status', '<>', 'temp')->orderBy('name')->get(['id', 'name', 'slug']),
            'designations' => Attribute::where('type', 2)->where('status', '<>', 'temp')->orderBy('name')->get(['id', 'name']),
            'workingPlaces' => WorkingPlace::orderBy('name')->get(['id', 'name']),
            'gender' => $genderOptions,
            'employeeStatuses' => collect([
                ['id' => 'regular', 'name' => 'Regular'],
                ['id' => 'lefty', 'name' => 'Lefty'],
                ['id' => 'resign', 'name' => 'Resign'],
                ['id' => 'transfer', 'name' => 'Transfer'],
            ]),
            'salaryTypes' => collect([
                ['id' => 'price_rate', 'name' => 'Price Rate'],
                ['id' => 'fixed_rate', 'name' => 'Fixed Rate'],
                ['id' => 'Cash', 'name' => 'Cash'],
                ['id' => 'Bank', 'name' => 'Bank'],
                ['id' => 'Mobile Banking', 'name' => 'Mobile Banking'],
                ['id' => 'Cheque', 'name' => 'Cheque'],
            ]),
        ];
    }

    private function employeeManpowerSummaryRows($employees, array $options)
    {
        $departmentMap = collect($options['departments'] ?? [])->pluck('name', 'id');
        $sectionMap = collect($options['sections'] ?? [])->pluck('name', 'id');
        $subSectionMap = collect($options['subSections'] ?? [])->keyBy('id');
        $designationMap = collect($options['designations'] ?? [])->pluck('name', 'id');

        $rows = collect();
        $serial = 1;
        $grandApprove = 0;
        $grandRecruited = 0;
        $grandGrossSalary = 0;

        $employees
            ->groupBy(function (User $employee) {
                return implode('|', [
                    $employee->department_id,
                    $employee->section_id,
                    $employee->sub_section_id,
                ]);
            })
            ->each(function ($subSectionGroup) use (&$rows, &$serial, &$grandApprove, &$grandRecruited, &$grandGrossSalary, $departmentMap, $sectionMap, $subSectionMap, $designationMap) {
                /** @var User $subSectionFirst */
                $subSectionFirst = $subSectionGroup->first();
                $subSection = $subSectionMap->get($subSectionFirst->sub_section_id);
                $subSectionApprove = (int) ($subSection->approve_man_power ?? 0);
                $subSectionRecruited = 0;
                $subSectionGrossSalary = 0;

                $subSectionGroup
                    ->groupBy('designation_id')
                    ->each(function ($designationGroup) use (&$rows, &$serial, &$subSectionRecruited, &$subSectionGrossSalary, $departmentMap, $sectionMap, $subSection, $designationMap, $subSectionFirst, $subSectionApprove) {
                        /** @var User $first */
                        $first = $designationGroup->first();
                        $recruited = $designationGroup->count();
                        $totalGrossSalary = $designationGroup->sum(function (User $employee) {
                            return (float) ($employee->gross_salary ?? 0);
                        });

                        $subSectionRecruited += $recruited;
                        $subSectionGrossSalary += $totalGrossSalary;

                        $rows->push([
                            'row_type' => 'detail',
                            'sl' => $serial++,
                            'department' => $departmentMap->get($subSectionFirst->department_id, 'N/A'),
                            'section' => $sectionMap->get($subSectionFirst->section_id, 'N/A'),
                            'sub_section' => data_get($subSection, 'name', 'N/A'),
                            'designation' => $designationMap->get($first->designation_id, 'N/A'),
                            'approve_manpower' => $subSectionApprove,
                            'recruited' => $recruited,
                            'deviation' => $recruited - $subSectionApprove,
                            'total_gross_salary' => $totalGrossSalary,
                        ]);
                    });

                $subSectionDeviation = $subSectionRecruited - $subSectionApprove;
                $grandApprove += $subSectionApprove;
                $grandRecruited += $subSectionRecruited;
                $grandGrossSalary += $subSectionGrossSalary;

                $rows->push([
                    'row_type' => 'total',
                    'sl' => 'Total',
                    'department' => '',
                    'section' => '',
                    'sub_section' => '',
                    'designation' => '',
                    'approve_manpower' => $subSectionApprove,
                    'recruited' => $subSectionRecruited,
                    'deviation' => $subSectionDeviation,
                    'total_gross_salary' => $subSectionGrossSalary,
                ]);
            });

        $rows->push([
            'row_type' => 'grand_total',
            'sl' => 'Grand Total',
            'department' => '',
            'section' => '',
            'sub_section' => '',
            'designation' => '',
            'approve_manpower' => $grandApprove,
            'recruited' => $grandRecruited,
            'deviation' => $grandRecruited - $grandApprove,
            'total_gross_salary' => $grandGrossSalary,
        ]);

        return $rows;
    }

    private function personalFileReportScreen(Request $request, string $report)
    {
        $query = User::query()->filterByType('employee');

        // ID card should skip placeholder IDs but must not hide valid employees
        // just because designation/department is missing.
        if ((string) $request->input('report_type') === 'id-card') {
            // $query->where('employee_id', '<>', '00000');
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', trim((string) $request->employee_id));
        }

        if ($request->filled('employee_ids')) {
            $ids = collect(explode(',', (string) $request->employee_ids))
                ->map(fn ($id) => trim($id))
                ->filter()
                ->values();
            if ($ids->isNotEmpty()) {
                $query->whereIn('employee_id', $ids->all());
            }
        }

        if ($request->filled('from')) {
            $query->whereDate('joining_date', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('joining_date', '<=', $request->to);
        }

        if ($request->filled('classification')) {
            $query->where('employee_type', $request->classification);
        }

        if ($request->filled('department')) {
            $query->where('department_id', $request->department);
        }

        if ($request->filled('section')) {
            $query->where('section_id', $request->section);
        }

        if ($request->filled('subsection') && Schema::hasColumn('users', 'sub_section_id')) {
            $query->where('sub_section_id', $request->subsection);
        }

        if ($request->filled('shift')) {
            $query->where('shift_id', $request->shift);
        }

        if ($request->filled('working_place')) {
            $workingPlace = trim((string) $request->working_place);
            $query->where(function ($builder) use ($workingPlace) {
                if (Schema::hasColumn('users', 'working_place_id')) {
                    $builder->orWhere('working_place_id', $workingPlace);
                }
                if (Schema::hasColumn('users', 'location')) {
                    $builder->orWhere('location', 'like', '%' . $workingPlace . '%');
                }
            });
        }

        if ($request->filled('employee_status')) {
            $status = (string) $request->employee_status;
            $query->where(function ($builder) use ($status) {
                if ($status === 'regular') {
                    $builder->whereNull('employment_status')
                        ->orWhere('employment_status', '')
                        ->orWhere('employment_status', 'regular');

                    return;
                }

                $builder->where('employment_status', $status);
                if ($status === 'lefty') {
                    $builder->orWhere('employment_status', 'left');
                }
                if ($status === 'resign') {
                    $builder->orWhere('employment_status', 'resigned');
                }
            });
        }

        $employees = $query->with(['designation', 'department'])->orderBy('name')->get();

        $options = [
            'classifications' => Attribute::where('type', 16)->where('status', '<>', 'temp')->orderBy('name')->get(['id', 'name']),
            'departments' => Attribute::where('type', 3)->where('status', '<>', 'temp')->orderBy('name')->get(['id', 'name']),
            'sections' => Attribute::where('type', 29)->where('status', '<>', 'temp')->orderBy('name')->get(['id', 'name']),
            'subsections' => SubSection::orderBy('name')->get(['id', 'name']),
            'shifts' => Shift::orderBy('name_of_shift')->get(['id', 'name_of_shift']),
            'workingPlaces' => WorkingPlace::orderBy('name')->get(['id', 'name']),
        ];

        $reportTypes = [
            'id-card' => 'ID Card',
            'application' => 'Application',
            'appointment-letter' => 'Appoinment Letter',
            'employment-letter' => 'Employment Letter',
            'nominee' => 'Nominee',
            'age-verification' => 'Age Verification',
            'job-responsibility' => 'Job Responsibility',
            'appraisal-letter' => 'Apprasial Letter',
            'joining-letter' => 'Joining Letter',
            'increment-letter' => 'Increment Letter',
        ];

        if ($request->boolean('print')) {
            $validated = $request->validate([
                'report_type' => 'required|string',
            ]);

            $reportType = (string) $validated['report_type'];
            abort_unless(array_key_exists($reportType, $reportTypes), 422);

            return view('hr::reports.personal-file-print', [
                'employees' => $employees,
                'request' => $request,
                'reportType' => $reportType,
                'reportTypeLabel' => $reportTypes[$reportType],
                'language' => $request->input('language', 'en'),
                'increments' => $this->latestIncrements($employees),
            ]);
        }

        if ($request->filled('report_type')) {
            $reportType = (string) $request->report_type;
            abort_unless(array_key_exists($reportType, $reportTypes), 422);
        }

        return view('hr::reports.personal-file', [
            'reportKey' => $report,
            'reportTitle' => config('hr.reports.' . $report),
            'employees' => $employees,
            'options' => $options,
            'reportTypes' => $reportTypes,
            'request' => $request,
        ]);
    }

    private function latestIncrements($employees): array
    {
        $map = [];
        $table = (new EmployeeIncrement())->getTable();
        if (!Schema::hasTable($table)) {
            return $map;
        }

        foreach ($employees as $employee) {
            $query = EmployeeIncrement::query();
            if (Schema::hasColumn($table, 'user_id')) {
                $query->where('user_id', $employee->id);
            } elseif (Schema::hasColumn($table, 'employee_id')) {
                $query->where('employee_id', $employee->id);
            } else {
                continue;
            }
            $map[$employee->id] = $query->latest()->first();
        }

        return $map;
    }

    private function employeeReport(): array
    {
        $rows = User::query()
            ->filterByType('employee')
            ->with(['designation', 'department'])
            ->orderBy('name')
            ->get()
            ->map(function (User $user) {
                return [
                    'employee_id' => $user->employee_id,
                    'name' => $user->name,
                    'designation' => optional($user->designation)->name,
                    'department' => optional($user->department)->name,
                    'joining_date' => optional($user->joining_date)?->format('Y-m-d'),
                    'status' => $user->status,
                ];
            });

        return [['employee_id', 'name', 'designation', 'department', 'joining_date', 'status'], $rows];
    }

    private function monthlyReport(): array
    {
        $rows = User::query()
            ->filterByType('employee')
            ->selectRaw("DATE_FORMAT(joining_date, '%Y-%m') as month")
            ->selectRaw('count(*) as total_employee')
            ->whereNotNull('joining_date')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->get();

        return [['month', 'total_employee'], $rows->map(fn ($row) => ['month' => $row->month, 'total_employee' => $row->total_employee])];
    }

    private function machineIdReport(): array
    {
        $rows = User::query()
            ->filterByType('employee')
            ->orderBy('employee_id')
            ->get(['employee_id', 'name', 'mobile', 'status'])
            ->map(fn (User $user) => ['employee_id' => $user->employee_id, 'name' => $user->name, 'mobile' => $user->mobile, 'status' => $user->status]);

        return [['employee_id', 'name', 'mobile', 'status'], $rows];
    }

    private function jobCardReport(): array
    {
        $rows = User::query()
            ->filterByType('employee')
            ->with(['designation', 'department'])
            ->orderBy('name')
            ->get()
            ->map(function (User $user) {
                return [
                    'employee_id' => $user->employee_id,
                    'name' => $user->name,
                    'department' => optional($user->department)->name,
                    'designation' => optional($user->designation)->name,
                    'line_number' => $user->line_number,
                    'shift_id' => $user->shift_id,
                ];
            });

        return [['employee_id', 'name', 'department', 'designation', 'line_number', 'shift_id'], $rows];
    }

    private function personalFileReport(): array
    {
        $rows = User::query()
            ->filterByType('employee')
            ->orderBy('name')
            ->get()
            ->map(function (User $user) {
                return [
                    'employee_id' => $user->employee_id,
                    'name' => $user->name,
                    'father_name' => $user->father_name,
                    'mother_name' => $user->mother_name,
                    'dob' => optional($user->dob)?->format('Y-m-d'),
                    'age_verify_ref' => $user->birth_registration ?: $user->nid_number,
                    'nominee' => $user->nominee,
                    'nominee_age' => $user->nominee_age,
                    'nominee_relation' => $user->nominee_relation,
                    'mobile' => $user->mobile,
                    'nid_number' => $user->nid_number,
                ];
            });

        return [[
            'employee_id',
            'name',
            'father_name',
            'mother_name',
            'dob',
            'age_verify_ref',
            'nominee',
            'nominee_age',
            'nominee_relation',
            'mobile',
            'nid_number',
        ], $rows];
    }

    private function attendanceReport(): array
    {
        $rows = User::query()
            ->filterByType('employee')
            ->with(['designation', 'department'])
            ->orderBy('name')
            ->get()
            ->map(function (User $user) {
                return [
                    'employee_id' => $user->employee_id,
                    'name' => $user->name,
                    'login_status' => $user->login_status,
                    'department' => optional($user->department)->name,
                    'designation' => optional($user->designation)->name,
                    'shift_id' => $user->shift_id,
                ];
            });

        return [['employee_id', 'name', 'login_status', 'department', 'designation', 'shift_id'], $rows];
    }

    private function mealAllowanceReport(): array
    {
        $rows = Designation::query()
            ->orderBy('name')
            ->get()
            ->map(function (Designation $designation) {
                return [
                    'designation' => $designation->name,
                    'tiffin_allowance' => $designation->tiffin_allowance,
                    'night_allowance' => $designation->night_allowance,
                    'dinner_allowance' => $designation->dinner_allowance,
                    'payment_way' => $designation->meal_payment_way,
                ];
            });

        return [['designation', 'tiffin_allowance', 'night_allowance', 'dinner_allowance', 'payment_way'], $rows];
    }

    private function productionJobCardReport(): array
    {
        $rows = User::query()
            ->filterByType('employee')
            ->whereNotNull('line_number')
            ->orderBy('line_number')
            ->orderBy('name')
            ->get()
            ->map(fn (User $user) => ['line_number' => $user->line_number, 'employee_id' => $user->employee_id, 'name' => $user->name, 'salary_type' => $user->salary_type]);

        return [['line_number', 'employee_id', 'name', 'salary_type'], $rows];
    }

    private function bonusSalaryFixedReport(): array
    {
        $rows = BonusPolicy::query()
            ->where('amount_type', 'fixed')
            ->orderBy('name')
            ->get()
            ->map(fn (BonusPolicy $policy) => ['policy' => $policy->name, 'basis' => $policy->salary_basis, 'amount' => $policy->amount, 'status' => $policy->status]);

        return [['policy', 'basis', 'amount', 'status'], $rows];
    }

    private function bonusSalaryProductionReport(): array
    {
        $rows = ProductionBonus::query()
            ->orderBy('name')
            ->get()
            ->map(fn (ProductionBonus $bonus) => ['name' => $bonus->name, 'percentage' => $bonus->percentage, 'effective_from' => $bonus->effective_from, 'effective_to' => $bonus->effective_to]);

        return [['name', 'percentage', 'effective_from', 'effective_to'], $rows];
    }

    private function salaryFixedReport(): array
    {
        $rows = User::query()
            ->filterByType('employee')
            ->where(function ($builder) {
                $builder->where('salary_type', 'fixed_rate')
                    ->orWhereNull('salary_type');
            })
            ->orderBy('name')
            ->get()
            ->map(fn (User $user) => ['employee_id' => $user->employee_id, 'name' => $user->name, 'gross_salary' => $user->gross_salary, 'basic_salary' => $user->basic_salary]);

        return [['employee_id', 'name', 'gross_salary', 'basic_salary'], $rows];
    }

    private function salaryProductionReport(): array
    {
        $rows = User::query()
            ->filterByType('employee')
            ->where('salary_type', 'price_rate')
            ->orderBy('name')
            ->get()
            ->map(fn (User $user) => ['employee_id' => $user->employee_id, 'name' => $user->name, 'gross_salary' => $user->gross_salary, 'basic_salary' => $user->basic_salary]);

        return [['employee_id', 'name', 'gross_salary', 'basic_salary'], $rows];
    }

    private function salarySummaryReport(): array
    {
        $rows = User::query()
            ->filterByType('employee')
            ->leftJoin('attributes as departments', function ($join) {
                $join->on('users.department_id', '=', 'departments.id');
            })
            ->select('departments.name as department')
            ->selectRaw('count(users.id) as total_employee')
            ->selectRaw('sum(coalesce(users.gross_salary, 0)) as gross_salary')
            ->selectRaw('sum(coalesce(users.basic_salary, 0)) as basic_salary')
            ->groupBy('departments.name')
            ->orderBy('departments.name')
            ->get()
            ->map(fn ($row) => ['department' => $row->department ?: 'Undefined', 'total_employee' => $row->total_employee, 'gross_salary' => $row->gross_salary, 'basic_salary' => $row->basic_salary]);

        return [['department', 'total_employee', 'gross_salary', 'basic_salary'], $rows];
    }
}





