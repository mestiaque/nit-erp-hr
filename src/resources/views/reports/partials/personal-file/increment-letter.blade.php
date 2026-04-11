@php
	$general = general();
	$companyName = $general->title ?? config('company.name', '');
	$companyAddress = $general->address_one ?? data_get($general, 'address') ?? config('company.address', '');
	$employeeName = $employee->name ?? '';
	$fatherName = $employee->father_name ?? '';
	$motherName = $employee->mother_name ?? '';
	$incrementDate = now()->format('d/m/Y');
	$employeeId = $employee->employee_id ?? '';
	$designation = optional($employee->designation)->name ?? data_get($employee, 'designation_name', '');
	$section = optional($employee->section)->name ?? data_get($employee, 'section_name', '');
	$previousSalary = number_format((float)($employee->previous_salary ?? 11000), 2);
	$newSalary = number_format((float)($employee->new_salary ?? 12000), 2);
	$incrementAmount = number_format((float)($employee->increment_amount ?? 1000), 2);
@endphp

<div style="text-align:center; margin-bottom:10px;">
	<h3 style="margin:0;">{{ $companyName }}</h3>
	<div>{{ $companyAddress }}</div>
</div>

<div style="margin-bottom:10px;">
	তারিখ: {{ $incrementDate }}<br>
	বরাবর,<br>
	ব্যবস্থাপনা পরিচালক<br>
	{{ $companyName }}<br>
	{{ $companyAddress }}
</div>

<div style="margin-bottom:10px; font-weight:600;">বিষয়ঃ বেতন বৃদ্ধি পত্র।</div>

<div style="margin-bottom:10px;">জনাব,<br>
আমি {{ $employeeName }} পিতা: {{ $fatherName }} মাতাঃ {{ $motherName }} আইডি নম্বর {{ $employeeId }} পদবী {{ $designation }} সেকশন {{ $section }}।<br>
আপনার সদয় অনুমোদনক্রমে আমার পূর্বের বেতন {{ $previousSalary }} টাকা হতে বৃদ্ধি পেয়ে বর্তমান বেতন {{ $newSalary }} টাকা নির্ধারিত হয়েছে।<br>
বেতন বৃদ্ধির তারিখ: {{ $incrementDate }} ইং।
</div>

<div style="margin-bottom:10px;">
আমি এই মর্মে অঙ্গীকার করিতেছি যে, আমি কোম্পানির সকল প্রকার নিয়মকানুন ও শৃঙ্খলা যথাযথভাবে পালন করিব এবং কর্তৃপক্ষের নির্দেশ যথাযথভাবে পালন করিব এবং সদা সর্বদা কোম্পানির স্বার্থকে অগ্রাধিকার দিব এবং আমি আমার উপর অর্পিত সকল দায়িত্ব নিষ্ঠার সাথে পালন করিব।
</div>

<div style="margin-bottom:10px;">
অতএব, মহোদয় কর্তৃক উপরোক্ত শর্তে আমার বেতন বৃদ্ধি অনুগ্রহপূর্বক গ্রহণ করার জন্য আবেদন করিলাম।
</div>

<div style="margin-bottom:30px;">
ধন্যবাদান্তে,<br>
নাম: {{ $employeeName }}<br>
</div>

<div style="margin-top:30px;">
আপনার সদয় অনুমোদনের অনুরোধে আবেদন করা হলো।<br>
কর্তৃপক্ষের সাক্ষর:------------------------------
</div>
