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
    $incrementDate = now()->format('d/m/Y');
    $employeeId = data_get($employee, 'employee_id', $na);

    $designationModel = optional($employee->designation);
    $designation = $isBangla
        ? ($designationModel->bn_name ?? $designationModel->name ?? data_get($employee, 'designation_bn_name') ?? data_get($employee, 'designation_name') ?? $na)
        : ($designationModel->name ?? data_get($employee, 'designation_name') ?? $designationModel->bn_name ?? data_get($employee, 'designation_bn_name') ?? $na);
    $sectionModel = optional($employee->section);
    $section = $isBangla
        ? ($sectionModel->bn_name ?? $sectionModel->name ?? data_get($employee, 'section_bn_name') ?? data_get($employee, 'section_name') ?? $na)
        : ($sectionModel->name ?? data_get($employee, 'section_name') ?? $sectionModel->bn_name ?? data_get($employee, 'section_bn_name') ?? $na);

    $previousSalary = number_format((float) (data_get($employee, 'previous_salary', 0)), 2);
    $newSalary = number_format((float) (data_get($employee, 'new_salary', 0)), 2);
    $incrementAmount = number_format((float) (data_get($employee, 'increment_amount', 0)), 2);
@endphp

<div style="text-align:center; margin-bottom:10px;">
    <h3 style="margin:0;">{{ $companyName }}</h3>
    <div>{{ $companyAddress }}</div>
</div>

<div style="margin-bottom:10px;">
    {{ $t('তারিখ', 'Date') }}: {{ $incrementDate }}<br>
    {{ $t('বরাবর', 'To') }},<br>
    {{ $t('ব্যবস্থাপনা পরিচালক', 'Managing Director') }}<br>
    {{ $companyName }}<br>
    {{ $companyAddress }}
</div>

<div style="margin-bottom:10px; font-weight:600;">{{ $t('বিষয়ঃ বেতন বৃদ্ধি পত্র।', 'Subject: Salary Increment Letter.') }}</div>

<div style="margin-bottom:10px;">
    {{ $t('জনাব', 'Dear Sir/Madam') }},<br>
    {{ $t('আমি', 'I') }} {{ $employeeName }}, {{ $t('পিতা', 'Father') }}: {{ $fatherName }}, {{ $t('মাতা', 'Mother') }}: {{ $motherName }},
    {{ $t('আইডি নম্বর', 'ID No.') }} {{ $employeeId }}, {{ $t('পদবী', 'Designation') }} {{ $designation }}, {{ $t('সেকশন', 'Section') }} {{ $section }}।<br>
    {{ $t('আপনার সদয় অনুমোদনক্রমে আমার পূর্বের বেতন', 'With your kind approval, my previous salary of') }} {{ $previousSalary }}
    {{ $t('টাকা থেকে বৃদ্ধি পেয়ে বর্তমান বেতন', 'has been revised to') }} {{ $newSalary }} {{ $t('টাকা নির্ধারিত হয়েছে।', '.') }}<br>
    {{ $t('বেতন বৃদ্ধির পরিমাণ', 'Increment Amount') }}: {{ $incrementAmount }}<br>
    {{ $t('বেতন বৃদ্ধির তারিখ', 'Effective Increment Date') }}: {{ $incrementDate }}
</div>

<div style="margin-bottom:10px;">
    {{ $t('আমি প্রতিষ্ঠানের সকল নিয়ম-কানুন ও শৃঙ্খলা যথাযথভাবে মেনে দায়িত্ব পালন করব বলে অঙ্গীকার করছি।', 'I commit to follow all company rules and discipline and to carry out my responsibilities sincerely.') }}
</div>

<div style="margin-bottom:10px;">
    {{ $t('অতএব, উপরোক্ত শর্তে আমার বেতন বৃদ্ধি অনুগ্রহপূর্বক গ্রহণ করার জন্য আবেদন করছি।', 'Therefore, I request you to kindly acknowledge and approve this increment under the above terms.') }}
</div>

<div style="margin-bottom:30px;">
    {{ $t('ধন্যবাদান্তে', 'Sincerely') }},<br>
    {{ $t('নাম', 'Name') }}: {{ $employeeName }}<br>
</div>

<div style="margin-top:30px;">
    {{ $t('আপনার সদয় অনুমোদনের অনুরোধে আবেদন করা হলো।', 'Submitted for your kind approval.') }}<br>
    {{ $t('কর্তৃপক্ষের স্বাক্ষর', 'Authority Signature') }}: ------------------------------
</div>
