@php
    $language = $language ?? data_get($request ?? null, 'language', 'bn');
    $isBangla = $language === 'bn';
    $t = fn ($bn, $en) => $isBangla ? $bn : $en;
@endphp

@foreach($employees as $employee)
    @php
        $employeeDataFn = \App\Services\HrOptionsService::getOptionsForEmployee();
        $employeeData = $employeeDataFn($employee, $request ?? null, $factory ?? null, $salaryKey ?? null, $profile ?? null, $nominee ?? null);
        $attendancePack = \App\Services\EmployeeAttendanceService::getEmployeeAttendanceByDate(
            $employee->id,
            $from,
            $to
        );
        $summary = $attendancePack['summary'];
        $salary = hr_employee_salary($employee, $factory ?? null, $salaryKey ?? null);
        $leave = $attendancePack['leave'] ?? [];
        $totalDays = $summary['totalDays'] ?? 0;
        $present = $summary['totalPresentAll'] ?? 0;
        $absent = $summary['totalAbsent'] ?? 0;
        $casual = $leave['casual'] ?? 0;
        $sick = $leave['sick'] ?? 0;
        $earned = $leave['earned'] ?? 0;
        $weekly = $leave['weekly'] ?? 0;
        $festival = $leave['festival'] ?? 0;
        $general = $leave['general'] ?? 0;
        $maternity = $leave['maternity'] ?? 0;
        $otRate = $employeeData['salary']['ot_rate'] ?? 0;
        if(hr_factory('factory_no') == 2 || hr_factory('factory_no') == 1) {
            $otHour = $summary['totalComplianceOt'] ?? 0;
        }else{
            $otHour = $summary['totalOt'] ?? 0;
        }
        $earnDeductSummary = $employeeData['getEarningsDeductionsSummary']($from, $to);
        $salaryReport = $employeeData['getSalaryReport']($from, $to);
        $totalEarnings = $salaryReport['total_earn'] ?? 0;
        $totalDeductions = $salaryReport['total_deduct'] - ($earnDeductSummary['advanceIou'] ?? 0) ?? 0;
        $otAmount = $otHour*$otRate ?? 0;
        $totalSalary = $salary['gross'] ?? 0;
        $attendanceBonus = $salary['attendance_bonus'] ?? 0;
        $deductAbsent = $summary['deductAbsent'] ?? 0;
        $advance = $earnDeductSummary['advanceIou'] ?? 0;
        $payable = $totalSalary + $otAmount;
        $netPay = $summary['netPay'] ?? 0;
    @endphp

<div class="payslip-container">
    <!-- Office Copy -->
    <div class="payslip-half">
        <div class="copy-type">অফিস কপি:</div>
        <div class="header">
            <h2>{{ $employeeData['company_name'] ?? '' }}</h2>
            <p>{{ $employeeData['company_address'] ?? '' }}</p>
            <p>Month: {{ $monthLabel ?? '' }}</p>
        </div>

        <div class="section-info">
            <div>
                <strong>সেকশন: {{ $employeeData['section'] ?? '-' }}</strong><br>
                <strong>কার্ড নং: {{ $employee->employee_id }}</strong><br>
                <strong>নাম: {{ $employeeData['employee_name'] ?? $employee->name }}</strong>
            </div>
            <div style="text-align: right;">
                <strong>ব্লক নং - {{ $employeeData['line'] ?? '-' }}</strong><br>
                হাজিরা বোনাস {{ en2bnNumber($attendanceBonus) }}<br>
                পদবী: {{ $employeeData['designation'] ?? '-' }}
            </div>
        </div>

        <table>
            <tr>
                <td class="label">মূল বেতন:</td>
                <td class="value">{{ en2bnNumber(number_format($salary['basic'] ?? 0, 0)) }}</td>
                <td></td><td></td>
                <td class="label right-align">মোট দিন:</td>
                <td class="value right-align">{{ en2bnNumber($totalDays) }}</td>
            </tr>
            <tr>
                <td class="label">বাড়ি ভাড়া:</td>
                <td class="value">{{ en2bnNumber(number_format($salary['house'] ?? 0, 0)) }}</td>
                <td class="label">&nbsp;&nbsp;চিকিৎসা ভাতা:</td>
                <td class="value">{{ en2bnNumber(number_format($salary['medical'] ?? 0, 0)) }}</td>
                <td class="label right-align">হাজিরা (দিন):</td>
                <td class="value right-align">{{ en2bnNumber($present) }}</td>
            </tr>
            <tr>
                <td class="label">যাতায়াত ভাতা:</td>
                <td class="value">{{ en2bnNumber(number_format($salary['transport'] ?? 0, 0)) }}</td>
                <td class="label">&nbsp;&nbsp;খাদ্য ভাতা:</td>
                <td class="value">{{ en2bnNumber(number_format($salary['food'] ?? 0, 0)) }}</td>
                <td class="label right-align">অনুপস্থিত:</td>
                <td class="value right-align">{{ en2bnNumber($absent) }}</td>
            </tr>
            <tr>
                <td class="label" style="border-bottom:1px solid red !important;">মোট বেতন:</td>
                <td class="value" style="border-bottom:1px solid red !important;">{{ en2bnNumber(number_format($totalSalary, 0)) }}</td>
                <td class="label">&nbsp;&nbsp;ওটি রেট:</td>
                <td class="value">{{ en2bnNumber($otRate) }}</td>
                <td class="label right-align">নৈমিত্তিক ছুটি:</td>
                <td class="value right-align">{{ en2bnNumber($casual) }}</td>
            </tr>
            <tr>
                <td class="label">মোট ওটি টাকা:</td>
                <td class="value">{{ en2bnNumber(bn2enNumber(number_format($otAmount, 0))) }}</td>
                <td class="label">&nbsp;&nbsp;ওটি ঘন্টা:</td>
                <td class="value">{{ en2bnNumber($otHour) }}</td>
                <td class="label right-align">অসুস্থতা ছুটি:</td>
                <td class="value right-align">{{ en2bnNumber($sick) }}</td>
            </tr>
            <tr>
                <td class="label" style="border-bottom:1px solid red !important;">প্রাপ্য বেতন:</td>
                <td class="value" style="border-bottom:1px solid red !important;">{{ en2bnNumber(number_format($payable, 0)) }}</td>
                <td class="label"></td>
                <td class="value"></td>
                <td class="label right-align">অর্জিত ছুটি:</td>
                <td class="value right-align">{{ en2bnNumber($earned) }}</td>
            </tr>
            <tr>
                <td class="label">অনুপ: কর্তন টাকা:</td>
                <td class="value">{{ en2bnNumber($totalDeductions) }}</td>
                <td class="label"></td>
                <td class="value"></td>
                <td class="label right-align">সাপ্তাহিক ছুটি:</td>
                <td class="value right-align">{{ en2bnNumber($weekly) }}</td>
            </tr>
            <tr>
                <td class="label" style="border-bottom:1px solid red !important;">অগ্রিম প্রদেয় টাকা:</td>
                <td class="value" style="border-bottom:1px solid red !important;">{{ en2bnNumber(number_format($advance, 0)) }}</td>
                <td class="label"></td>
                <td class="value"></td>
                <td class="label right-align">উৎসব ছুটি:</td>
                <td class="value right-align">{{ en2bnNumber($festival) }}</td>
            </tr>
            <tr>
                <td class="label">মোট প্রদেয় টাকা:</td>
                <td class="value">{{ en2bnNumber(number_format($payable - ($advance + $totalDeductions), 0)) }}</td>
                <td class="label"></td>
                <td class="value"></td>
                <td class="label right-align">সাধারণ ছুটি:</td>
                <td class="value right-align">{{ en2bnNumber($general) }}</td>
            </tr>
            <tr>
                <td colspan="4"></td>
                <td class="label right-align">মাতৃত্বকালীন ছুটি:</td>
                <td class="value right-align">{{ en2bnNumber($maternity) }}</td>
            </tr>
        </table>

        <div class="footer">
            **আপনার যে কোন অভিযোগ এবং পরামর্শ মানব সম্পদ<br>
            ও কমপ্লায়েন্স বিভাগকে অবহিত করুন।
        </div>
        <div class="signature">স্বাক্ষর</div>
    </div>

    <!-- Dashed Divider -->
    <div class="dashed-line"></div>

    <!-- Worker Copy -->
    <div class="payslip-half">
        <div class="copy-type">শ্রমিক কপি:</div>
        <div class="header">
            <h2>{{ $employeeData['company_name'] ?? '' }}</h2>
            <p>{{ $employeeData['company_address'] ?? '' }}</p>
            <p>Month: {{ $monthLabel ?? '' }}</p>
        </div>

        <div class="section-info">
            <div>
                <strong>সেকশন: {{ $employeeData['section'] ?? '-' }}</strong><br>
                <strong>কার্ড নং: {{ $employee->employee_id }}</strong><br>
                <strong>নাম: {{ $employeeData['employee_name'] ?? $employee->name }}</strong>
            </div>
            <div style="text-align: right;">
                <strong>ব্লক নং - {{ $employeeData['line'] ?? '-' }}</strong><br>
                হাজিরা বোনাস {{ en2bnNumber($attendanceBonus) }}<br>
                পদবী: {{ $employeeData['designation'] ?? '-' }}
            </div>
        </div>

        <table>
            <tr>
                <td class="label">মূল বেতন:</td>
                <td class="value">{{ en2bnNumber($salary['basic'] ?? 0) }}</td>
                <td></td><td></td>
                <td class="label right-align">মোট দিন:</td>
                <td class="value right-align">{{ en2bnNumber($totalDays) }}</td>
            </tr>
            <tr>
                <td class="label">বাড়ি ভাড়া:</td>
                <td class="value">{{ en2bnNumber($salary['house_rent'] ?? 0) }}</td>
                <td class="label">চিকিৎসা ভাতা:</td>
                <td class="value">{{ en2bnNumber($salary['medical'] ?? 0) }}</td>
                <td class="label right-align">হাজিরা (দিন):</td>
                <td class="value right-align">{{ en2bnNumber($present) }}</td>
            </tr>
            <tr>
                <td class="label">যাতায়াত ভাতা:</td>
                <td class="value">{{ en2bnNumber($salary['transport'] ?? 0) }}</td>
                <td class="label">খাদ্য ভাতা:</td>
                <td class="value">{{ en2bnNumber($salary['food'] ?? 0) }}</td>
                <td class="label right-align">অনুপস্থিত:</td>
                <td class="value right-align">{{ en2bnNumber($absent) }}</td>
            </tr>
            <tr>
                <td class="label" style="border-bottom:1px solid red !important;">মোট বেতন:</td>
                <td class="value" style="border-bottom:1px solid red !important;">{{ en2bnNumber($totalSalary) }}</td>
                <td class="label">ওটি রেট:</td>
                <td class="value">{{ en2bnNumber($otRate) }}</td>
                <td class="label right-align">নৈমিত্তিক ছুটি:</td>
                <td class="value right-align">{{ en2bnNumber($casual) }}</td>
            </tr>
            <tr>
                <td class="label">মোট ওটি টাকা:</td>
                <td class="value">{{ en2bnNumber($otAmount) }}</td>
                <td class="label">ওটি ঘন্টা:</td>
                <td class="value">{{ en2bnNumber($otHour) }}</td>
                <td class="label right-align">অসুস্থতা ছুটি:</td>
                <td class="value right-align">{{ en2bnNumber($sick) }}</td>
            </tr>
            <tr>
                <td class="label" style="border-bottom:1px solid red !important;">প্রাপ্য বেতন:</td>
                <td class="value" style="border-bottom:1px solid red !important;">{{ en2bnNumber($payable) }}</td>
                <td class="label"></td>
                <td class="value"></td>
                <td class="label right-align">অর্জিত ছুটি:</td>
                <td class="value right-align">{{ en2bnNumber($earned) }}</td>
            </tr>
            <tr>
                <td class="label">অনুপ: কর্তন টাকা:</td>
                <td class="value">{{ en2bnNumber($deductAbsent) }}</td>
                <td class="label"></td>
                <td class="value"></td>
                <td class="label right-align">সাপ্তাহিক ছুটি:</td>
                <td class="value right-align">{{ en2bnNumber($weekly) }}</td>
            </tr>
            <tr>
                <td class="label" style="border-bottom:1px solid red !important;">অগ্রিম প্রদেয় টাকা:</td>
                <td class="value" style="border-bottom:1px solid red !important;">{{ en2bnNumber($advance) }}</td>
                <td class="label"></td>
                <td class="value"></td>
                <td class="label right-align">উৎসব ছুটি:</td>
                <td class="value right-align">{{ en2bnNumber($festival) }}</td>
            </tr>
            <tr>
                <td class="label">মোট প্রদেয় টাকা:</td>
                <td class="value">{{ en2bnNumber($netPay) }}</td>
                <td class="label"></td>
                <td class="value"></td>
                <td class="label right-align">সাধারণ ছুটি:</td>
                <td class="value right-align">{{ en2bnNumber($general) }}</td>
            </tr>
            <tr>
                <td colspan="4"></td>
                <td class="label right-align">মাতৃত্বকালীন ছুটি:</td>
                <td class="value right-align">{{ en2bnNumber($maternity) }}</td>
            </tr>
        </table>

        <div class="footer">
            **আপনার যে কোন অভিযোগ এবং পরামর্শ মানব সম্পদ<br>
            ও কমপ্লায়েন্স বিভাগকে অবহিত করুন।
        </div>
        <div class="signature">স্বাক্ষর</div>
    </div>
</div>
@endforeach

    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }

        .payslip-container {
            width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border: 1px dashed #000;
            display: flex;
            justify-content: space-between;
        }

        .payslip-half {
            width: 48%;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .header h2 {
            margin: 0;
            font-size: 18px;
        }

        .header p {
            margin: 2px 0;
            font-size: 11px;
        }

        .copy-type {
            text-align: right;
            font-weight: bold;
            font-size: 13px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .section-info {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 2px 0;
            vertical-align: top;
            font-size: 10px !important;
            border: none !important;
        }

        .label {
            font-weight: bold;
            width: 90px;
        }

        .value {
            text-align: left;
            color: #000;
            font-weight: bold;
        }

        .right-align {
            text-align: right;
        }

        .footer {
            margin-top: 15px;
            font-size: 10px;
            font-weight: bold;
        }

        .signature {
            margin-top: 20px;
            text-align: right;
            border-top: 1px solid #000;
            width: 80px;
            float: right;
        }

        .dashed-line {
            border-left: 1px dashed #000;
            height: auto;
        }
    </style>
