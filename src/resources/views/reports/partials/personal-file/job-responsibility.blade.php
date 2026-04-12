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

    $employeeId = data_get($employee, 'employee_id', $na);
    $employeeName = $isBangla
        ? (data_get($employee, 'bn_name') ?? data_get($employee, 'name') ?? $na)
        : (data_get($employee, 'name') ?? data_get($employee, 'bn_name') ?? $na);
    $supervisor = data_get($employee, 'supervisor', data_get($employee, 'supervisor_name', $t('সেকশন প্রধান', 'Section Supervisor')));
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
    border: 1px solid #888 !important;
    padding: 6px 10px;
    vertical-align: top;
}
.job-resp-table th {
    background: #f7f7f7 !important;
    font-weight: 600;
    width: 180px;
}
</style>

<div class="job-resp-header">
    <h3 style="margin:0;">{{ $companyName }}</h3>
    <div>{{ $companyAddress }}</div>
    <div class="job-resp-title" style="margin-top:8px;">
        {{ $designation }} {{ $t('এর দায়িত্ব ও কর্তব্য', 'Job Responsibilities and Duties') }}
    </div>
    <div style="text-align:right; font-weight:600; margin-top:4px;">{{ $t('তারিখ', 'Date') }}: {{ $date }}</div>
</div>

<table class="job-resp-table">
    <tr>
        <th>{{ $t('নাম', 'Name') }}</th>
        <td>{{ $employeeName }}</td>
        <th>{{ $t('পদবী', 'Designation') }}</th>
        <td>{{ $designation }}</td>
        <th>{{ $t('আই.ডি নম্বর', 'ID No.') }}</th>
        <td>{{ $employeeId }}</td>
    </tr>
    <tr>
        <th>{{ $t('সেকশন', 'Section') }}</th>
        <td>{{ $section }}</td>
        <th colspan="2">{{ $t('যার অধীনে নিয়োজিত থাকবেন', 'Reporting Supervisor') }}</th>
        <td colspan="2">{{ $supervisor }}</td>
    </tr>
    <tr>
        <th colspan="1">{{ $t('দায়িত্বের তালিকা', 'Responsibility Checklist') }}</th>
        <td colspan="5">
            {{ $t('১) নির্ধারিত কাজ সময়মতো সম্পন্ন করা, ২) উপস্থিতি ও শৃঙ্খলা বজায় রাখা, ৩) নিরাপত্তা নির্দেশনা অনুসরণ করা, ৪) ঊর্ধ্বতন কর্মকর্তার নির্দেশনা মেনে চলা।', '1) Complete assigned tasks on time, 2) Maintain attendance and discipline, 3) Follow safety instructions, 4) Comply with supervisor directives.') }}
        </td>
    </tr>
</table>
