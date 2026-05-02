
@php
    $employeeDataFn = \App\Services\HrOptionsService::getOptionsForEmployee();
    $employeeData = $employeeDataFn($employee, $request ?? null, $factory ?? null, $salaryKey ?? null, $profile ?? null, $nominee ?? null);
    $language = $language ?? data_get($request ?? null, 'language', 'bn');
    $isBangla = $language === 'bn';
    $t = fn (string $bn, string $en) => $isBangla ? $bn : $en;
    $na = $t('প্রযোজ্য নয়', 'N/A');
    $companyName = $employeeData['company_name'];
    $companyAddress = $employeeData['company_address'];
    $employeeName = $employeeData['employee_name'];
    $fatherName = $employeeData['father_name'] ?? $na;
    $motherName = $employeeData['mother_name'] ?? $na;
    $designation = $employeeData['designation'] ?? $na;
    $department = $employeeData['department'] ?? $na;
    $section = $employeeData['section'] ?? $na;
    $presentAddress = $employeeData['present_address'] ?? $na;
    $permanentAddress = $employeeData['permanent_address'] ?? $na;
    $applicationDate = $applicationDate ?? ($employeeData['joining_date'] ?? now()->format('d/m/Y'));
    // Try to extract address parts if available
    $presentAddressFull = $isBangla ? ($employeeData['present_address_bn_full'] ?? $na) : ($employeeData['present_address_full'] ?? $na);
    $permanentAddressFull = $isBangla ? ($employeeData['permanent_address_bn_full'] ?? $na) : ($employeeData['permanent_address_full'] ?? $na);
@endphp

<div style="font-family: 'Nikosh', 'Arial', sans-serif; max-width: 800px; margin: auto; color: #000; line-height: 1.6;">

    <!-- Header -->
    <div style="text-align:center; margin-bottom:20px; border-bottom: 2px solid #000; padding-bottom: 10px;">
        <h2 style="margin:0; font-size: 22px; text-transform: uppercase;">{{ $companyName }}</h2>
        <div style="font-size: 13px;">{{ $companyAddress }}</div>
        <div style="margin-top:8px; font-weight:bold; font-size:18px; text-decoration: underline;">
            {{ $t('চাকরির আবেদন পত্র', 'Application for Employment') }}
        </div>
    </div>

    <!-- Date and To -->
    <div style="margin-bottom:20px;">
        {{ $t('তারিখ', 'Date') }}: {{ $applicationDate }}<br>
        {{ $t('বরাবর', 'To') }},<br>
        <strong>{{ $t('ব্যবস্থাপক (এইচআর ও এডমিন)', 'Manager (HR & Admin)') }}</strong><br>
        {{ $companyName }}<br>
        {{ $companyAddress }}
    </div>

    <!-- Subject -->
    <div style="margin-bottom:20px; font-weight:bold;">
        {{ $t('বিষয়ঃ', 'Subject:') }} {{ $designation }} {{ $t('পদে চাকরির জন্য আবেদন।', ' পদে চাকরির জন্য আবেদন।') }}
    </div>

    <div style="margin-bottom:15px;">
        {{ $t('জনাব,', 'Dear Sir,') }}<br>
        {{ $t('বিনীত নিবেদন এই যে, আমি আপনার প্রতিষ্ঠানে উপরোক্ত শূন্য পদে একজন দক্ষ কর্মী হিসেবে যোগদান করতে আগ্রহী। আমার প্রয়োজনীয় জীবন-বৃত্তান্ত ও ব্যক্তিগত তথ্যাদি আপনার সদয় বিবেচনার জন্য নিচে উপস্থাপন করছিঃ',
           'With due respect, I would like to apply for the above-mentioned vacant position in your esteemed organization. My personal and professional details are presented below for your kind consideration:') }}
    </div>

    <!-- Personal Info Table -->
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 14px;">
        <tr>
            <td style="width: 30%; padding: 6px; border: 1px solid #000;"><strong>{{ $t('আবেদনকারীর নাম', 'Applicant Name') }}</strong></td>
            <td colspan="3" style="padding: 6px; border: 1px solid #000;">: {{ $employeeName }}</td>
        </tr>
        <tr>
            <td style="padding: 6px; border: 1px solid #000;"><strong>{{ $t('পিতার নাম', 'Father\'s Name') }}</strong></td>
            <td colspan="3" style="padding: 6px; border: 1px solid #000;">: {{ $fatherName }}</td>
        </tr>
        <tr>
            <td style="padding: 6px; border: 1px solid #000;"><strong>{{ $t('মাতার নাম', 'Mother\'s Name') }}</strong></td>
            <td colspan="3" style="padding: 6px; border: 1px solid #000;">: {{ $motherName }}</td>
        </tr>
        <tr>
            <td rowspan="1" style="padding: 6px; border: 1px solid #000;"><strong>{{ $t('বর্তমান ঠিকানা', 'Present Address') }}</strong></td>
            <td colspan="3" style="padding: 6px; border: 1px solid #000;">
                : {{ $presentAddressFull }}
            </td>
        </tr>
        <tr>
            <td rowspan="1" style="padding: 6px; border: 1px solid #000;"><strong>{{ $t('স্থায়ী ঠিকানা', 'Permanent Address') }}</strong></td>
            <td colspan="3" style="padding: 6px; border: 1px solid #000;">
                : {{ $permanentAddressFull }}
            </td>
        </tr>
        <tr>
            <td style="padding: 6px; border: 1px solid #000;"><strong>{{ $t('আবেদিত পদ', 'Applied Post') }}</strong></td>
            <td style="padding: 6px; border: 1px solid #000;">{{ $designation }}</td>
        </tr>
        <tr>
            <td style="padding: 6px; border: 1px solid #000;"><strong>{{ $t('বিভাগ', 'Department') }}</strong></td>
            <td style="padding: 6px; border: 1px solid #000;">{{ $department }}</td>
        </tr>
        <tr>
            <td style="padding: 6px; border: 1px solid #000;"><strong>{{ $t('সেকশন', 'Section') }}</strong></td>
            <td style="padding: 6px; border: 1px solid #000;">{{ $section }}</td>
        </tr>
    </table>

    <div style="margin-bottom:15px; text-align: justify;">
        {{ $t('আমি অঙ্গীকার করছি যে, উক্ত পদে নিয়োগ প্রাপ্ত হলে আমি প্রতিষ্ঠানের সকল নিয়ম-কানুন মেনে নিষ্ঠা ও সততার সাথে দায়িত্ব পালন করব।',
           'I assure you that if I am appointed, I will perform my duties with honesty, dedication, and strict adherence to company rules.') }}
    </div>

    <div style="margin-bottom:15px;">
        {{ $t('অতএব, বিনীত প্রার্থনা এই যে, আমার সার্বিক যোগ্যতা বিবেচনা করে আমাকে আপনার প্রতিষ্ঠানে উক্ত পদে কাজ করার সুযোগ দানে আপনার সদয় মর্জি হয়।',
           'Therefore, I pray and hope that you would be kind enough to grant me the opportunity to work in the mentioned position.') }}
    </div>

    <!-- Signatures -->
    <div style="margin-top: 50px;">
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%;">
                    {{ $t('ধন্যবাদান্তে,', 'Sincerely,') }}<br><br><br>
                    --------------------------<br>
                    <strong>{{ $t('আবেদনকারীর স্বাক্ষর', 'Applicant Signature') }}</strong>
                </td>
                <td style="width: 50%; text-align: right; vertical-align: bottom;">
                    --------------------------<br>
                    <strong>{{ $t('কর্তৃপক্ষের স্বাক্ষর', 'Authorized Signature') }}</strong>
                </td>
            </tr>
        </table>
    </div>

</div>
