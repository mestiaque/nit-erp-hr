<h4 class="section-title">{{ $label('মূল্যায়নপত্র', 'Appraisal Letter') }}</h4>
<table class="two-col">
    <tr><th>{{ $label('কর্মীর নাম', 'Employee Name') }}</th><td>{{ $employee->name ?? 'N/A' }}</td></tr>
    <tr><th>{{ $label('পদবি', 'Designation') }}</th><td>{{ optional($employee->designation)->name ?? 'N/A' }}</td></tr>
    <tr><th>{{ $label('বিভাগ', 'Department') }}</th><td>{{ optional($employee->department)->name ?? 'N/A' }}</td></tr>
    <tr><th>{{ $label('মূল্যায়ন তারিখ', 'Appraisal Date') }}</th><td>{{ $fmtDate(data_get($employee, 'appraisal_date', now())) }}</td></tr>
    <tr><th>{{ $label('মন্তব্য', 'Remarks') }}</th><td>{{ data_get($employee, 'appraisal_note', $label('কর্মদক্ষতা সন্তোষজনক।', 'Performance is satisfactory.')) }}</td></tr>
</table>
