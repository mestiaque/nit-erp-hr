<h4 class="section-title">{{ $label('যোগদানপত্র', 'Joining Letter') }}</h4>
<p><strong>{{ $label('কর্মীর নাম', 'Employee Name') }}:</strong> {{ $employee->name ?? 'N/A' }}</p>
<p><strong>{{ $label('কর্মী আইডি', 'Employee ID') }}:</strong> {{ $employee->employee_id ?? 'N/A' }}</p>
<p><strong>{{ $label('পদবি', 'Designation') }}:</strong> {{ optional($employee->designation)->name ?? 'N/A' }}</p>
<p><strong>{{ $label('বিভাগ', 'Department') }}:</strong> {{ optional($employee->department)->name ?? 'N/A' }}</p>
<p><strong>{{ $label('যোগদানের তারিখ', 'Joining Date') }}:</strong> {{ $fmtDate($employee->joining_date) }}</p>
<p>{{ $label('উপরোক্ত কর্মী নির্ধারিত তারিখে প্রতিষ্ঠানে যোগদান করেছেন।', 'The above employee has joined the organization on the specified date.') }}</p>
