@php
	$general = general();
	$companyName = $general->title ?? config('company.name', '');
	$companyAddress = $general->address_one ?? data_get($general, 'address') ?? config('company.address', '');
	$employeePhoto = method_exists($employee, 'image') ? $employee->image() : null;
	$dob = data_get($employee, 'date_of_birth', data_get($employee, 'dob'));
	$employeeAge = '';
	if (filled($dob)) {
		try {
			$employeeAge = \Illuminate\Support\Carbon::parse($dob)->age;
		} catch (\Throwable $e) {
			$employeeAge = '';
		}
	}
	$permanentAddress = collect([
		data_get($employee, 'permanent_village'),
		data_get($employee, 'permanent_post_office'),
		data_get($employee, 'permanent_upazila'),
		data_get($employee, 'permanent_district'),
	])->filter(fn ($value) => filled($value))->implode(', ');
	$presentAddress = collect([
		data_get($employee, 'present_village'),
		data_get($employee, 'present_post_office'),
		data_get($employee, 'present_upazila'),
		data_get($employee, 'present_district'),
	])->filter(fn ($value) => filled($value))->implode(', ');
	$permanentAddress = $permanentAddress ?: data_get($employee, 'permanent_address', data_get($employee, 'address', ''));
	$presentAddress = $presentAddress ?: data_get($employee, 'present_address', data_get($employee, 'address', ''));
@endphp


<style>
.age-verification-header {
	text-align: center;
	margin-bottom: 10px;
}
.age-verification-photo {
	width: 120px;
	height: 120px;
	object-fit: cover;
	border: 1px solid #888 !important;
	margin-bottom: 8px;
}
.age-verification-main-table {
	width: 100%;
	border-collapse: collapse;
	font-size: 15px;
	margin-bottom: 18px;
	border: 2px solid #222 !important;
}
.age-verification-main-table td, .age-verification-main-table th {
	border: 1.5px solid #222 !important;
	padding: 6px 8px;
	vertical-align: top;
}
.age-verification-main-table th {
	width: 220px;
	background: #f7f7f7;
	font-weight: 600;
}
.age-verification-main-table table {
	border-collapse: collapse;
	width: 100%;
}
.age-verification-main-table table td {
	border: 1px solid #222 !important;
	padding: 4px 6px;
}
.age-verification-footer-table {
	width: 100%;
	font-size: 15px;
	border: none;
	margin-top: 18px;
}
.age-verification-footer-table td {
	border: none;
	padding-top: 30px;
}
</style>


<div class="age-verification-header">
	<h3 style="margin:0;">{{ $companyName }}</h3>
	<div>{{ $companyAddress }}</div>
	<div style="margin-top:8px;font-weight:700;">ফরম-১-৫</div>
	<div style="margin-bottom:4px; font-size:13px;">[ধারা ৩৪,৩৫(১), ৩৭ এবং বিধি ৩৪(১) ও ৩৬(৪) চট্রগ্রাম]</div>
	<div style="margin-bottom:8px; font-weight:700;">বয়স ও সম্মততার প্রত্যয়নপত্র</div>
	<div style="margin-bottom:8px; font-size:15px; font-weight:600;">"নির্বাচিত চিকিৎসকের প্যাড-এ"</div>
</div>

<table class="age-verification-main-table">
	<tr>
		<td style="width:50%; vertical-align:top;">
			<table style="width:100%; border:none; font-size:15px;">
				<tr><td>১. আই.ডি নম্বরঃ {{ $employee->employee_id ?? '' }}</td></tr>
				<tr><td>তারিখঃ {{ now()->format('d/m/Y') }}</td></tr>
				<tr><td>২. নামঃ {{ $employee->name ?? '' }}</td></tr>
				<tr><td>৩. পিতার নামঃ {{ $employee->father_name ?? '' }}</td></tr>
				<tr><td>৪. মাতার নামঃ {{ $employee->mother_name ?? '' }}</td></tr>
				<tr><td>৫. লিঙ্গঃ {{ $employee->sex ?? '' }}</td></tr>
				<tr><td>৬. স্থায়ী ঠিকানাঃ <br>
					গ্রামঃ {{ data_get($employee, 'permanent_village') }}<br>
					ডাকঘরঃ {{ data_get($employee, 'permanent_post_office') }}<br>
					থানা/উপজেলাঃ {{ data_get($employee, 'permanent_upazila') }}<br>
					জেলা: {{ data_get($employee, 'permanent_district') }}
				</td></tr>
				<tr><td>৭. অস্থায়ী ঠিকানাঃ <br>
					গ্রামঃ {{ data_get($employee, 'present_village') }}<br>
					ডাকঘরঃ {{ data_get($employee, 'present_post_office') }}<br>
					থানা/উপজেলাঃ {{ data_get($employee, 'present_upazila') }}<br>
					জেলা: {{ data_get($employee, 'present_district') }}
				</td></tr>
				<tr><td>৮. শিক্ষাগত যোগ্যতাঃ <br>
					গ্রামঃ {{ data_get($employee, 'education_village') }}<br>
					ডাকঘরঃ {{ data_get($employee, 'education_post_office') }}<br>
					থানা/উপজেলাঃ {{ data_get($employee, 'education_upazila') }}<br>
					জেলা: {{ data_get($employee, 'education_district') }}<br>
					প্রতিষ্ঠানঃ {{ data_get($employee, 'education_institute') }}<br>
					বিভাগঃ {{ data_get($employee, 'education_board') }}<br>
					জেলা: {{ data_get($employee, 'education_district') }}
				</td></tr>
				<tr><td>৯. জন্ম তারিখ/সনদ অনুযায়ী বয়স/জন্ম তারিখঃ {{ $employee->date_of_birth ?? $employee->dob ?? '' }}</td></tr>
				<tr><td>১০. দৈনিক সক্ষমতাঃ</td></tr>
				<tr><td>১১. সামগ্রিক সক্ষমতা চিহ্নঃ</td></tr>
			</table>
		</td>
		<td style="width:50%; vertical-align:top;">
			<div style="text-align:right;">
				@if($employeePhoto)
					<img src="{{ asset($employeePhoto) }}" class="age-verification-photo" alt="Employee Photo">
				@endif
			</div>
			<table style="width:100%; border:none; font-size:15px; margin-top:8px;">
				<tr><td>আই.ডি নম্বরঃ {{ $employee->employee_id ?? '' }}</td></tr>
				<tr><td>তারিখঃ {{ now()->format('d/m/Y') }}</td></tr>
			</table>
			<div style="margin-top:8px; font-size:15px;">
				আমি এই মর্মে প্রত্যয়ন করিতেছি যে,<br>
				নাম {{ $employee->name ?? '................................' }},<br>
				পিতা নাম {{ $employee->father_name ?? '................................' }},<br>
				মাতার নাম {{ $employee->mother_name ?? '................................' }}।<br>
				<br>
				স্থায়ী ঠিকানা:<br>
				গ্রামঃ {{ data_get($employee, 'permanent_village') }}, ডাকঘরঃ {{ data_get($employee, 'permanent_post_office') }}, থানা/উপজেলাঃ {{ data_get($employee, 'permanent_upazila') }}, জেলা: {{ data_get($employee, 'permanent_district') }}<br>
				<br>
				কে আমি পরীক্ষা করিয়াছি।<br>
				তিনি প্রতিষ্ঠানে নিয়োগের জন্য উপযুক্ত, এবং তার পরীক্ষা সূত্রে এক্ষণে পাওয়া গিয়াছে যে, তার বয়স {{ $employeeAge ? $employeeAge . ' বছর' : '০০ বছর' }} এবং তিনি প্রতিষ্ঠানে গ্রহীত বয়স / হিসেব হিসাবে নিয়োগ উপযুক্ত।
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<table class="age-verification-footer-table">
				<tr>
					<td style="width:33%; text-align:left;">সংশ্লিষ্ট স্বাক্ষর</td>
					<td style="width:34%; text-align:center;">নির্বাচিত চিকিৎসকের স্বাক্ষর</td>
					<td style="width:33%; text-align:right;">সংশ্লিষ্ট টিপসই</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
