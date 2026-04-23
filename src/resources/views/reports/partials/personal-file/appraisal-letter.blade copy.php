@php
    $language = $language ?? data_get($request ?? null, 'language', 'en');
    $isBangla = $language === 'bn';
    $t = fn (string $bn, string $en) => $isBangla ? $bn : $en;
    $na = $t('প্রযোজ্য নয়', 'N/A');

    $companyName = $isBangla
        ? (hr_factory('bn_name') ?? hr_factory('name') ?? general()->name ?? $na)
        : (hr_factory('name') ?? general()->name ?? hr_factory('bn_name') ?? $na);
    $companyAddress = $isBangla
        ? (hr_factory('bn_address') ?? hr_factory('address') ?? general()->address ?? $na)
        : (hr_factory('address') ?? general()->address ?? hr_factory('bn_address') ?? $na);

    $designationModel = optional($employee->designation);
    $designation = $isBangla
        ? ($designationModel->bn_name ?? $designationModel->name ?? data_get($employee, 'designation_bn_name') ?? data_get($employee, 'designation_name') ?? $na)
        : ($designationModel->name ?? data_get($employee, 'designation_name') ?? $designationModel->bn_name ?? data_get($employee, 'designation_bn_name') ?? $na);
    $sectionModel = optional($employee->section);
    $section = $isBangla
        ? ($sectionModel->bn_name ?? $sectionModel->name ?? data_get($employee, 'section_bn_name') ?? data_get($employee, 'section_name') ?? $na)
        : ($sectionModel->name ?? data_get($employee, 'section_name') ?? $sectionModel->bn_name ?? data_get($employee, 'section_bn_name') ?? $na);

    $cardNo = data_get($employee, 'employee_id', $na);
    $employeeName = $isBangla
        ? (data_get($employee, 'bn_name') ?? data_get($employee, 'name') ?? $na)
        : (data_get($employee, 'name') ?? data_get($employee, 'bn_name') ?? $na);
    $joiningDate = blank($employee->joining_date) ? $na : \Illuminate\Support\Carbon::parse($employee->joining_date)->format('d/m/Y');

    $previousSalary = number_format((float) (data_get($employee, 'previous_salary', 0)), 2);
    $newSalary = data_get($employee, 'new_salary') !== null ? number_format((float) data_get($employee, 'new_salary'), 2) : '--';
    $increment = data_get($employee, 'increment_amount') !== null ? number_format((float) data_get($employee, 'increment_amount'), 2) : '--';
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
</style>

<div class="appraisal-header">
    <h3 style="margin:0;">{{ $companyName }}</h3>
    <div>{{ $companyAddress }}</div>
    <div class="appraisal-title">{{ $t('পদোন্নতি ও বেতনবৃদ্ধি মূল্যায়ন ফরম', 'Promotion and Salary Increment Appraisal Form') }}</div>
</div>

<table style="width:100%; border:none; margin-bottom:2px;">
    <tr>
        <td style="border:none; font-weight:600;">{{ $t('নাম', 'Name') }}: {{ $employeeName }}</td>
        <td style="border:none; font-weight:600;">{{ $t('সেকশন', 'Section') }}: {{ $section }}</td>
        <td style="border:none; text-align:right; font-weight:600;">{{ $t('কার্ড নম্বর', 'Card No.') }}: {{ $cardNo }}</td>
        <td style="border:none; text-align:right; font-weight:600;">{{ $t('যোগদানের তারিখ', 'Joining Date') }}: {{ $joiningDate }}</td>
    </tr>
    <tr>
        <td style="border:none; font-weight:600;">{{ $t('পদবী', 'Designation') }}: {{ $designation }}</td>
        <td style="border:none;"></td>
        <td style="border:none;"></td>
        <td style="border:none;"></td>
    </tr>
</table>

<table class="appraisal-table">
    <tr>
        <th style="width:30%;">{{ $t('মূল্যায়নের বিষয়সমূহ', 'Evaluation Criteria') }}</th>
        <th style="width:14%;">{{ $t('উত্তম (১০)', 'Excellent (10)') }}</th>
        <th style="width:14%;">{{ $t('ভাল (৮)', 'Good (8)') }}</th>
        <th style="width:14%;">{{ $t('সন্তোষজনক (৬)', 'Satisfactory (6)') }}</th>
        <th style="width:14%;">{{ $t('মাঝারি (৪)', 'Average (4)') }}</th>
        <th style="width:14%;">{{ $t('খারাপ (২)', 'Poor (2)') }}</th>
    </tr>
    <tr><td>{{ $t('উপস্থিতি ও নিয়মিততা', 'Attendance and Regularity') }}</td><td></td><td></td><td></td><td></td><td></td></tr>
    <tr><td>{{ $t('কাজের জ্ঞান ও দক্ষতা', 'Job Knowledge and Skills') }}</td><td></td><td></td><td></td><td></td><td></td></tr>
    <tr><td>{{ $t('প্রতিষ্ঠানের নিয়ম-কানুন মেনে চলা', 'Compliance with Company Rules') }}</td><td></td><td></td><td></td><td></td><td></td></tr>
    <tr><td>{{ $t('দায়িত্ববোধ', 'Sense of Responsibility') }}</td><td></td><td></td><td></td><td></td><td></td></tr>
    <tr><td>{{ $t('উর্ধ্বতন ও সহকর্মীদের সাথে আচরণ', 'Behavior with Supervisors and Coworkers') }}</td><td></td><td></td><td></td><td></td><td></td></tr>
    <tr><td>{{ $t('সততা ও নির্ভরযোগ্যতা', 'Integrity and Reliability') }}</td><td></td><td></td><td></td><td></td><td></td></tr>
    <tr>
        <td colspan="4" style="text-align:right;"><b>{{ $t('মোট নম্বর', 'Total Score') }}:</b></td>
        <td colspan="2"></td>
    </tr>
</table>

<div style="margin: 10px 0 10px 0; text-align:right; font-size:15px;">
    {{ $t('উপরোক্ত মূল্যায়নের ভিত্তিতে: □ পদোন্নতি □ বেতনবৃদ্ধি □ উভয়ই প্রযোজ্য', 'Based on this appraisal: □ Promotion □ Salary Increment □ Both are applicable') }}
    <hr style="margin:8px 0; border:0; border-top:1px dashed #888;">
</div>

<table class="appraisal-footer-table">
    <tr>
        <th style="width:20%;">{{ $t('এইচআর কর্মকর্তা', 'HR Officer') }}</th>
        <th style="width:20%;">{{ $t('পদবী', 'Designation') }}</th>
        <th style="width:20%;">{{ $t('পূর্বের বেতন', 'Previous Salary') }}</th>
        <th style="width:20%;">{{ $t('পরিবর্তিত বেতন', 'Revised Salary') }}</th>
        <th style="width:10%;">{{ $t('বৃদ্ধির পরিমাণ', 'Increment') }}</th>
        <th style="width:10%;">{{ $t('কার্যকর তারিখ', 'Effective Date') }}</th>
    </tr>
    <tr>
        <td>{{ $t('এইচ.আর এন্ড কমপ্লায়েন্স', 'HR and Compliance') }}</td>
        <td>{{ $designation }}</td>
        <td>{{ $previousSalary }}</td>
        <td>{{ $newSalary }}</td>
        <td>{{ $increment }}</td>
        <td>{{ $effectiveDate }}</td>
    </tr>
</table>

<div style="margin-top: 30px;">
    <div style="float:left; width:33%; text-align:center;">------------------------------<br>{{ $t('প্রস্তুতকারক (এইচআর)', 'Prepared By (HR)') }}</div>
    <div style="float:left; width:33%; text-align:center;">------------------------------<br>{{ $t('যাচাইকারী (প্রশাসন)', 'Verified By (Admin)') }}</div>
    <div style="float:left; width:33%; text-align:center;">------------------------------<br>{{ $t('অনুমোদনকারী', 'Approving Authority') }}</div>
    <div style="clear:both;"></div>
</div>
