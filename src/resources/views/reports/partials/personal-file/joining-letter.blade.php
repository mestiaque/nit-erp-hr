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

    $employeeName = $isBangla
        ? (data_get($employee, 'bn_name') ?? data_get($employee, 'name') ?? $na)
        : (data_get($employee, 'name') ?? data_get($employee, 'bn_name') ?? $na);
    $fatherName = data_get($employee, 'father_name', $na);
    $motherName = data_get($employee, 'mother_name', $na);
    $joiningDate = blank($employee->joining_date) ? $na : \Illuminate\Support\Carbon::parse($employee->joining_date)->format('d/m/Y');
    $employeeId = data_get($employee, 'employee_id', $na);
    $designationModel = optional($employee->designation);
    $designation = $isBangla
        ? ($designationModel->bn_name ?? $designationModel->name ?? data_get($employee, 'designation_bn_name') ?? data_get($employee, 'designation_name') ?? $na)
        : ($designationModel->name ?? data_get($employee, 'designation_name') ?? $designationModel->bn_name ?? data_get($employee, 'designation_bn_name') ?? $na);
    $sectionModel = optional($employee->section);
    $section = $isBangla
        ? ($sectionModel->bn_name ?? $sectionModel->name ?? data_get($employee, 'section_bn_name') ?? data_get($employee, 'section_name') ?? $na)
        : ($sectionModel->name ?? data_get($employee, 'section_name') ?? $sectionModel->bn_name ?? data_get($employee, 'section_bn_name') ?? $na);
@endphp

<div style="text-align:center; margin-bottom:10px;">
    <h3 style="margin:0;">{{ $companyName }}</h3>
    <div>{{ $companyAddress }}</div>
</div>

<div style="margin-bottom:10px;">
    {{ $t('তারিখ', 'Date') }}: {{ $joiningDate }}<br>
    {{ $t('বরাবর', 'To') }},<br>
    {{ $t('ব্যবস্থাপনা পরিচালক', 'Managing Director') }}<br>
    {{ $companyName }}<br>
    {{ $companyAddress }}
</div>

<div style="margin-bottom:10px; font-weight:600;">{{ $t('বিষয়ঃ যোগদান পত্র।', 'Subject: Joining Letter.') }}</div>

<div style="margin-bottom:10px;">
    {{ $t('জনাব', 'Dear Sir/Madam') }},<br>
    {{ $t('আমি', 'I') }} {{ $employeeName }}, {{ $t('পিতা', 'Father') }}: {{ $fatherName }}, {{ $t('মাতা', 'Mother') }}: {{ $motherName }},
    {{ $t('আইডি নম্বর', 'ID No.') }} {{ $employeeId }}, {{ $t('পদবী', 'Designation') }} {{ $designation }}, {{ $t('সেকশন', 'Section') }} {{ $section }}।
    {{ $t('নিয়োগপত্রের শর্ত অনুযায়ী আমি আজ কর্মস্থলে যোগদান করলাম এবং কর্তৃপক্ষকে বিষয়টি অবহিত করলাম।', 'As per the terms of my appointment letter, I have joined my duty today and formally informed the authority.') }}
</div>

<div style="margin-bottom:10px;">
    {{ $t('আমি প্রতিষ্ঠানের সকল নিয়ম-কানুন ও শৃঙ্খলা মেনে নিষ্ঠার সাথে দায়িত্ব পালন করব বলে অঙ্গীকার করছি।', 'I hereby commit to follow all company rules and perform my responsibilities with sincerity and discipline.') }}
</div>

<div style="margin-bottom:10px;">
    {{ $t('অতএব, আমার যোগদানপত্রটি সদয় অনুমোদনের জন্য অনুরোধ করছি।', 'Therefore, I kindly request you to accept and approve my joining letter.') }}
</div>

<div style="margin-bottom:30px;">
    {{ $t('ধন্যবাদান্তে', 'Sincerely') }},<br>
    {{ $t('নাম', 'Name') }}: {{ $employeeName }}<br>
</div>

<div style="margin-top:30px;">
    {{ $t('আপনার সদয় অনুমোদনের অনুরোধে আবেদন করা হলো।', 'Submitted for your kind approval.') }}<br>
    {{ $t('কর্তৃপক্ষের স্বাক্ষর', 'Authority Signature') }}: ------------------------------
</div>
