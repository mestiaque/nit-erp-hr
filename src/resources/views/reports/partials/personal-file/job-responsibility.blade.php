@php
	$general = general();
	$companyName = $general->title ?? config('company.name', '');
	$companyAddress = $general->address_one ?? data_get($general, 'address') ?? config('company.address', '');
	$designation = optional($employee->designation)->name ?? data_get($employee, 'designation_name', '');
	$section = optional($employee->section)->name ?? data_get($employee, 'section_name', '');
	$employeeId = $employee->employee_id ?? '';
	$employeeName = $employee->name ?? '';
	$supervisor = $employee->supervisor ?? $employee->supervisor_name ?? 'Head of Section (Supervisor)';
	$date = now()->format('d/m/Y');
@endphp

<style>
.job-resp-header {
	text-align: center;
	margin-bottom: 8px;
}
.job-resp-title {
	font-weight: bold;
	text-decoration: underline;
	font-size: 17px;
}
.job-resp-table {
	width: 100%;
	border-collapse: collapse;
	font-size: 15px;
	margin-top: 10px;
}
.job-resp-table th, .job-resp-table td {
	border: 1px solid #888;
	padding: 6px 10px;
	vertical-align: top;
}
.job-resp-table th {
	background: #f7f7f7;
	font-weight: 600;
	width: 180px;
}
</style>

<div class="job-resp-header">
	<h3 style="margin:0;">{{ $companyName }}</h3>
	<div>{{ $companyAddress }}</div>
	<div class="job-resp-title" style="margin-top:8px;">
		{{ $designation }} এর দায়িত্ব ও কর্তব্য
	</div>
	<div style="text-align:right; font-weight:600; margin-top:4px;">তারিখ: {{ $date }}</div>
</div>

<table class="job-resp-table">
	<tr>
		<th>নাম</th>
		<td>{{ $employeeName }}</td>
		<th>পদবী</th>
		<td>{{ $designation }}</td>
		<th>আই.ডি নম্বর</th>
		<td>{{ $employeeId }}</td>
	</tr>
	<tr>
		<th>সেকশন</th>
		<td>{{ $section }}</td>
		<th colspan="2">যার অধীনে নিয়োজিত করবেন</th>
		<td colspan="2">{{ $supervisor }}</td>
	</tr>
	<tr>
		<th colspan="1">দায়িত্ব-কর্তব্য চিহ্ন</th>
		<td colspan="5"></td>
	</tr>
</table>
