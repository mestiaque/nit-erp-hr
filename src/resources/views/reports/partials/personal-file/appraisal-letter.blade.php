@php
	$general = general();
	$companyName = $general->title ?? config('company.name', '');
	$companyAddress = $general->address_one ?? data_get($general, 'address') ?? config('company.address', '');
	$designation = optional($employee->designation)->name ?? data_get($employee, 'designation_name', '');
	$section = optional($employee->section)->name ?? data_get($employee, 'section_name', '');
	$employeeId = $employee->employee_id ?? '';
	$employeeName = $employee->name ?? '';
	$joiningDate = $employee->joining_date ? \Illuminate\Support\Carbon::parse($employee->joining_date)->format('d/m/y') : '';
	$cardNo = $employee->employee_id ?? '';
	$appraisalDate = now()->format('d/m/Y');
	$previousSalary = number_format((float)($employee->previous_salary ?? 11000), 2);
	$newSalary = '--';
	$increment = '--';
	$effectiveDate = now()->format('d-m-Y');
@endphp

<style>
.appraisal-header {
	text-align: center;
	margin-bottom: 8px;
}
.appraisal-title {
	font-weight: bold;
	font-size: 18px;
	margin-bottom: 2px;
}
.appraisal-table, .appraisal-table th, .appraisal-table td {
	border: 1px solid #888 !important;
	border-collapse: collapse;
}
.appraisal-table {
	width: 100%;
	font-size: 15px;
	margin-bottom: 10px;
}
.appraisal-table th, .appraisal-table td {
	padding: 6px 8px;
	text-align: left;
}
.appraisal-table th {
	background: #f7f7f7;
	font-weight: 600;
	text-align: center;
}
.appraisal-footer-table {
	width: 100%;
	border-collapse: collapse;
	font-size: 15px;
	margin-top: 18px;
}
.appraisal-footer-table th, .appraisal-footer-table td {
	border: 1px solid #888 !important;
	padding: 6px 8px;
	text-align: left;
}
.appraisal-sign-row td {
	border: none !important;
	text-align: center;
	padding-top: 40px;
}
</style>

<div class="appraisal-header">
	<h3 style="margin:0;">{{ $companyName }}</h3>
	<div>{{ $companyAddress }}</div>
	<div class="appraisal-title">পদোন্নতি ও বেতনবৃদ্ধি ফরমুলার</div>
</div>

<table style="width:100%; border:none; margin-bottom:2px;">
	<tr>
		<td style="border:none; font-weight:600;">নাম: {{ $employeeName }}</td>
		<td style="border:none; font-weight:600;">সেকশন: {{ $section }}</td>
		<td style="border:none; text-align:right; font-weight:600;">কার্ড নম্বর: {{ $cardNo }}</td>
		<td style="border:none; text-align:right; font-weight:600;">যোগদানের তারিখঃ {{ $joiningDate }}</td>
	</tr>
	<tr>
		<td style="border:none; font-weight:600;">পদবী: {{ $designation }}</td>
		<td style="border:none;"></td>
		<td style="border:none;"></td>
		<td style="border:none;"></td>
	</tr>
</table>

<table class="appraisal-table">
	<tr>
		<th style="width:30%;">মূল্যায়নের বিষয়সমূহ (টিক দিন)</th>
		<th style="width:10%;">উত্তম (১০)</th>
		<th style="width:10%;">ভাল (৮)</th>
		<th style="width:10%;">সন্তোষজনক (৬)</th>
		<th style="width:10%;">মোটকৃত (৪)</th>
		<th style="width:10%;">খারাপ (২)</th>
	</tr>
	<tr><td>উপস্থিতি ও নিয়মিততা</td><td></td><td></td><td></td><td></td><td></td></tr>
	<tr><td>কাজের জ্ঞান ও দক্ষতা</td><td></td><td></td><td></td><td></td><td></td></tr>
	<tr><td>প্রতিষ্ঠানের নিয়ম-কানুনের প্রতি আনুগত্য</td><td></td><td></td><td></td><td></td><td></td></tr>
	<tr><td>সম্পর্কিত কাজের জ্ঞান এবং কাজ সম্পর্কে স্পষ্ট ধারণা ও ধারণা</td><td></td><td></td><td></td><td></td><td></td></tr>
	<tr><td>দায়িত্ববোধ বা সচেতনতা</td><td></td><td></td><td></td><td></td><td></td></tr>
	<tr><td>উর্ধ্বতন কর্তৃপক্ষের সাথে আচরণ</td><td></td><td></td><td></td><td></td><td></td></tr>
	<tr><td>ছুটি নেয়ার প্রবণতা</td><td></td><td></td><td></td><td></td><td></td></tr>
	<tr><td>অধীনস্থ কর্মীদের সাথে আচরণ</td><td></td><td></td><td></td><td></td><td></td></tr>
	<tr><td>নির্ভরযোগ্যতা</td><td></td><td></td><td></td><td></td><td></td></tr>
	<tr><td>সততা</td><td></td><td></td><td></td><td></td><td></td></tr>
	<tr>
		<td colspan="4" style="text-align:right;"><b>মোট নম্বর:</b></td>
		<td colspan="2"></td>
	</tr>
	<tr>
		<td colspan="4" style="text-align:right;"><b>সর্বমোট নম্বর:</b></td>
		<td colspan="2"></td>
	</tr>
</table>

<div style="margin: 10px 0 10px 0; text-align:right; font-size:15px;">
	উক্ত গুণাবলীর কারণে এই ব্যক্তির □ পদোন্নতি □ বেতনবৃদ্ধি □ পদোন্নতি ও বেতনবৃদ্ধি করা হয়েছে।<br>
	<span style="font-size:13px;">কাজের দক্ষতার জন্য</span>
	<hr style="margin:8px 0; border:0; border-top:1px dashed #888;">
</div>

<table class="appraisal-footer-table">
	<tr>
		<th style="width:30%;">অফিসার (এইচ.আর এন্ড কমপ্লায়েন্স)</th>
		<th style="width:30%;">বেতন</th>
		<th style="width:20%;">পূর্বের বেতন (টাকা)</th>
		<th style="width:20%;">পরিবর্তিত (টাকা)</th>
		<th style="width:20%;">বর্ধিত বেতন</th>
		<th style="width:20%;">কার্যকরী তারিখ</th>
	</tr>
	<tr>
		<td>পদবী: {{ $designation }}</td>
		<td></td>
		<td>{{ $previousSalary }}</td>
		<td>{{ $newSalary }}</td>
		<td>{{ $increment }}</td>
		<td>{{ $effectiveDate }} ইং</td>
	</tr>
</table>

<div style="margin-top: 30px;">
	<div style="float:left; width:33%; text-align:center;">------------------------------<br>বাহক (এইচ.আর এন্ড কমপ্লায়েন্স)</div>
	<div style="float:left; width:33%; text-align:center;">------------------------------<br>বাহক (প্রশাসন)</div>
	<div style="float:left; width:33%; text-align:center;">------------------------------<br>এ.জি.এম</div>
	<div style="clear:both;"></div>
</div>
