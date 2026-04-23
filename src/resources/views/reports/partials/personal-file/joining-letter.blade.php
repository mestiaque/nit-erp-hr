


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
    $employeeId = $employeeData['employee_id'] ?? $na;
    $designation = $employeeData['designation'] ?? $na;
    $section = $employeeData['section'] ?? $na;
    $grade = $isBangla ? en2bnNumber($employeeData['grade']) ?? $na : $employeeData['grade'] ?? $na;
    $joiningDate = $employeeData['joining_date'] ?? $na;
    $department = $employeeData['department'] ?? $na;
    $subSection = $employeeData['sub_section'] ?? $na;
@endphp

<div style="font-family: 'Nikosh', 'Arial', sans-serif; max-width: 800px; margin: auto; color: #000; line-height: 1.5;">

    <!-- Header -->
    <div style="text-align:center; margin-bottom:20px; border-bottom: 2px solid #000; padding-bottom: 10px;">
        <h2 style="margin:0; font-size: 24px; text-transform: uppercase;">{{ $companyName }}</h2>
        <div style="font-size: 14px;">{{ $companyAddress }}</div>
    </div>

    <!-- Date -->
    <div style="text-align: right; margin-bottom: 20px;">
        <strong>{{ $t('তারিখ', 'Date') }}:</strong> {{ $joiningDate }}
    </div>

    <!-- To Address -->
    <div style="margin-bottom:25px;">
        {{ $t('বরাবর', 'To') }},<br>
        <strong>{{ $t('ব্যবস্থাপক (এইচআর, এডমিন ও কমপ্লায়েন্স)', 'Manager (HR, Admin & Compliance)') }}</strong><br>
        {{ $companyName }}<br>
        {{ $companyAddress }}
    </div>

    <!-- Subject -->
    <div style="margin-bottom:20px; font-weight:bold;">
        {{ $t('বিষয়ঃ যোগদান পত্র প্রদান প্রসঙ্গে।', 'Subject: Submission of Joining Letter.') }}
    </div>

    <!-- Salutation & Body -->
    <div style="margin-bottom:15px; text-align: justify;">
        {{ $t('জনাব,', 'Dear Sir,') }}<br>
        {{ $t('আপনার নিকট হতে প্রাপ্ত নিয়োগপত্রের শর্তানুযায়ী আমি আজ', 'In accordance with the terms and conditions of the appointment letter issued by your organization, I am pleased to join today') }}
        <strong>{{ $joiningDate }}</strong>
        {{ $t('ইং তারিখ সকাল ৯:০০ ঘটিকায় আপনার প্রতিষ্ঠানে নির্ধারিত পদে যোগদান করলাম। আমার কর্মসংস্থান সংক্রান্ত প্রয়োজনীয় তথ্যাদি নিম্নরূপঃ', ' at 09:00 AM in the assigned position. My employment details are as follows:') }}
    </div>

    <!-- Information Table -->
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 14px;">
        <tr>
            <td style="width: 25%; padding: 8px; border: 1px solid #000;"><strong>{{ $t('নাম', 'Name') }}</strong></td>
            <td style="width: 25%; padding: 8px; border: 1px solid #000;">{{ $employeeName }}</td>
            <td style="width: 25%; padding: 8px; border: 1px solid #000;"><strong>{{ $t('আইডি নম্বর', 'ID No.') }}</strong></td>
            <td style="width: 25%; padding: 8px; border: 1px solid #000;">{{ $employeeId }}</td>
        </tr>
        <tr>
            <td style="padding: 8px; border: 1px solid #000;"><strong>{{ $t('বিভাগ', 'Department') }}</strong></td>
            <td style="padding: 8px; border: 1px solid #000;">{{ $department }}</td>
            <td style="padding: 8px; border: 1px solid #000;"><strong>{{ $t('সেকশন', 'Section') }}</strong></td>
            <td style="padding: 8px; border: 1px solid #000;">{{ $section }}</td>
        </tr>
        <tr>
            <td style="padding: 8px; border: 1px solid #000;"><strong>{{ $t('উপ-সেকশন', 'Sub-Section') }}</strong></td>
            <td style="padding: 8px; border: 1px solid #000;">{{ $subSection }}</td>
            <td style="padding: 8px; border: 1px solid #000;"><strong>{{ $t('পদবী', 'Designation') }}</strong></td>
            <td style="padding: 8px; border: 1px solid #000;">{{ $designation }}</td>
        </tr>
        <tr>
            <td style="padding: 8px; border: 1px solid #000;"><strong>{{ $t('গ্রেড', 'Grade') }}</strong></td>
            <td colspan="3" style="padding: 8px; border: 1px solid #000;">{{ $grade }}</td>
        </tr>
    </table>

    <div style="margin-bottom:20px; text-align: justify;">
        {{ $t('আমি অঙ্গীকার করছি যে, আমি আপনার প্রতিষ্ঠানের সকল বিদ্যমান বিধি-বিধান ও শৃঙ্খলা যথাযথভাবে মেনে চলব এবং আমাকে অর্পিত দায়িত্ব অত্যন্ত নিষ্ঠা ও সততার সাথে পালন করতে সচেষ্ট থাকব।', 'I hereby declare that I will strictly abide by all the rules and regulations of the company and will perform my duties with the utmost sincerity, honesty, and dedication.') }}
    </div>

    <div style="margin-bottom:40px;">
        {{ $t('অতএব, আমার যোগদানপত্রটি সদয় গ্রহণ পূর্বক প্রয়োজনীয় ব্যবস্থা গ্রহণের জন্য আপনাকে বিনীত অনুরোধ জানাচ্ছি।', 'I, therefore, request you to kindly accept my joining letter and take the necessary administrative actions.') }}
    </div>

    <!-- Signatures -->
    <div style="margin-top: 60px;">
        <table style="width: 100%;">
            <tr>
                <td style="width: 40%; text-align: center;">
                    <div style="border-top: 1px solid #000; width: 180px; margin: auto;"></div>
                    <strong>{{ $t('আবেদনকারীর স্বাক্ষর', 'Employee Signature') }}</strong>
                </td>
                <td style="width: 20%;"></td>
                <td style="width: 40%; text-align: center;">
                    <div style="border-top: 1px solid #000; width: 180px; margin: auto;"></div>
                    <strong>{{ $t('কর্তৃপক্ষের স্বাক্ষর', 'Authorized Signature') }}</strong>
                </td>
            </tr>
        </table>
    </div>

</div>
