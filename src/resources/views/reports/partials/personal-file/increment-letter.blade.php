<h4 class="section-title">{{ $label('ইনক্রিমেন্ট পত্র', 'Increment Letter') }}</h4>
<table class="two-col">
    <tr><th>{{ $label('কর্মীর নাম', 'Employee Name') }}</th><td>{{ $employee->name ?? 'N/A' }}</td></tr>
    <tr><th>{{ $label('পদবি', 'Designation') }}</th><td>{{ optional($employee->designation)->name ?? 'N/A' }}</td></tr>
    <tr><th>{{ $label('বিভাগ', 'Department') }}</th><td>{{ optional($employee->department)->name ?? 'N/A' }}</td></tr>
    <tr><th>{{ $label('বর্তমান বেতন', 'Current Salary') }}</th><td>{{ number_format($grossSalary, 2) }}</td></tr>
    <tr><th>{{ $label('নতুন বেতন', 'Revised Salary') }}</th><td>{{ number_format((float) data_get($employee, 'increment_salary', $grossSalary), 2) }}</td></tr>
    <tr><th>{{ $label('কার্যকর তারিখ', 'Effective Date') }}</th><td>{{ $fmtDate(data_get($employee, 'increment_date', now())) }}</td></tr>
</table>
