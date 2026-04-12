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
    $presentVillage = data_get($employee, 'present_village', $na);
    $presentPostOffice = data_get($employee, 'present_post_office', $na);
    $presentUpazila = data_get($employee, 'present_upazila', $na);
    $presentDistrict = data_get($employee, 'present_district', $na);
    $permanentVillage = data_get($employee, 'permanent_village', $na);
    $permanentPostOffice = data_get($employee, 'permanent_post_office', $na);
    $permanentUpazila = data_get($employee, 'permanent_upazila', $na);
    $permanentDistrict = data_get($employee, 'permanent_district', $na);

    $designationModel = optional($employee->designation);
    $designation = $isBangla
        ? ($designationModel->bn_name ?? $designationModel->name ?? data_get($employee, 'designation_bn_name') ?? data_get($employee, 'designation_name') ?? $na)
        : ($designationModel->name ?? data_get($employee, 'designation_name') ?? $designationModel->bn_name ?? data_get($employee, 'designation_bn_name') ?? $na);
    $sectionModel = optional($employee->section);
    $section = $isBangla
        ? ($sectionModel->bn_name ?? $sectionModel->name ?? data_get($employee, 'section_bn_name') ?? data_get($employee, 'section_name') ?? $na)
        : ($sectionModel->name ?? data_get($employee, 'section_name') ?? $sectionModel->bn_name ?? data_get($employee, 'section_bn_name') ?? $na);

    $applicationDate = now()->format('d/m/Y');
@endphp

<div style="text-align:center; margin-bottom:10px;">
    <h3 style="margin:0;">{{ $companyName }}</h3>
    <div>{{ $companyAddress }}</div>
    <div style="margin-top:4px; font-weight:700; font-size:16px;">{{ $t('আবেদন পত্র', 'Application Letter') }}</div>
</div>

<div style="margin-bottom:10px;">
    {{ $t('তারিখ', 'Date') }}: {{ $applicationDate }}<br>
    {{ $t('বরাবর', 'To') }},<br>
    {{ $t('ব্যবস্থাপনা পরিচালক', 'Managing Director') }}<br>
    {{ $companyName }}<br>
    {{ $companyAddress }}
</div>

<div style="margin-bottom:10px; font-weight:600;">{{ $t('বিষয়ঃ চাকুরির জন্য আবেদন।', 'Subject: Application for Employment.') }}</div>

<div style="margin-bottom:10px;">
    {{ $t('জনাব', 'Dear Sir/Madam') }},<br>
    {{ $t('আমি', 'I') }} {{ $employeeName }}, {{ $t('পিতা', 'Father') }}: {{ $fatherName }}, {{ $t('মাতা', 'Mother') }}: {{ $motherName }}.<br>
    {{ $t('বর্তমান ঠিকানা', 'Present Address') }}: {{ $t('গ্রাম', 'Village') }}: {{ $presentVillage }}, {{ $t('ডাকঘর', 'Post Office') }}: {{ $presentPostOffice }}, {{ $t('উপজেলা', 'Upazila') }}: {{ $presentUpazila }}, {{ $t('জেলা', 'District') }}: {{ $presentDistrict }}<br>
    {{ $t('স্থায়ী ঠিকানা', 'Permanent Address') }}: {{ $t('গ্রাম', 'Village') }}: {{ $permanentVillage }}, {{ $t('ডাকঘর', 'Post Office') }}: {{ $permanentPostOffice }}, {{ $t('উপজেলা', 'Upazila') }}: {{ $permanentUpazila }}, {{ $t('জেলা', 'District') }}: {{ $permanentDistrict }}<br>
    {{ $t('আমি', 'I') }} {{ $companyName }} {{ $t('এর', 'at') }} {{ $section }} {{ $t('সেকশনে', 'section as') }} {{ $designation }} {{ $t('পদে চাকুরির জন্য আবেদন করছি।', 'position, hereby apply for employment.') }}
</div>

<div style="margin-bottom:10px;">
    {{ $t('আমার শিক্ষাগত যোগ্যতা, দক্ষতা ও অভিজ্ঞতা অনুযায়ী আমি উক্ত পদে নিয়োগের জন্য নিজেকে উপযুক্ত মনে করি। আমার সকল তথ্য ও প্রয়োজনীয় কাগজপত্র সংযুক্ত করা হলো।', 'Based on my educational background, skills, and experience, I consider myself suitable for this position. I have attached all required information and documents.') }}
</div>

<div style="margin-bottom:10px;">
    {{ $t('আমি অত্র প্রতিষ্ঠানে নিয়মিত, নিষ্ঠার সাথে এবং সততার সাথে দায়িত্ব পালন করব বলে অঙ্গীকার করছি।', 'I sincerely commit to perform my duties with honesty, discipline, and dedication.') }}
</div>

<div style="margin-bottom:10px;">
    {{ $t('আমার আবেদনটি সদয় বিবেচনা করে আমাকে উক্ত পদে নিয়োগ দেয়ার জন্য বিনীত অনুরোধ করছি।', 'I kindly request you to consider my application and appoint me to the mentioned position.') }}
</div>

<div style="margin-bottom:30px;">
    {{ $t('ধন্যবাদান্তে', 'Sincerely') }},<br>
    {{ $t('নাম', 'Name') }}: {{ $employeeName }}<br>
</div>

<div style="margin-top:30px;">
    {{ $t('আপনার সদয় অনুমোদনের অনুরোধে আবেদন করা হলো।', 'Submitted for your kind approval.') }}<br>
    {{ $t('আবেদনকারীর স্বাক্ষর', 'Applicant Signature') }}: ------------------------------
</div>
