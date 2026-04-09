<h4 class="section-title">{{ $label('কাজের দায়িত্ব', 'Job Responsibility') }}</h4>
<p><strong>{{ $label('পদবি', 'Designation') }}:</strong> {{ optional($employee->designation)->name ?? 'N/A' }}</p>
<p><strong>{{ $label('বিভাগ', 'Department') }}:</strong> {{ optional($employee->department)->name ?? 'N/A' }}</p>
<p><strong>{{ $label('দায়িত্বের সারাংশ', 'Responsibility Summary') }}:</strong> {{ data_get($employee, 'job_responsibility', data_get($profile, 'job_responsibility', $label('প্রতিষ্ঠানের নির্দেশনা অনুযায়ী অর্পিত দায়িত্ব যথাযথভাবে সম্পন্ন করতে হবে।', 'The employee must perform assigned duties as instructed by the organization.'))) }}</p>
