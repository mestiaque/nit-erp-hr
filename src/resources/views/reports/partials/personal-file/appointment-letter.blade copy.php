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
    $spouseName = data_get($employee, 'spouse_name', $na);
    $joiningDate = blank($employee->joining_date) ? $na : \Illuminate\Support\Carbon::parse($employee->joining_date)->format('d/m/Y');

    $designationModel = optional($employee->designation);
    $designationAttr = optional(\ME\Hr\Models\Designation::find($employee->designation_id));
    $designation = $isBangla
        ? ($designationModel->bn_name ?? data_get($designationAttr, 'bn_name') ?? $designationModel->name ?? data_get($designationAttr, 'name') ?? data_get($employee, 'designation_bn_name') ?? data_get($employee, 'designation_name') ?? $na)
        : ($designationModel->name ?? data_get($designationAttr, 'name') ?? data_get($employee, 'designation_name') ?? $designationModel->bn_name ?? data_get($designationAttr, 'bn_name') ?? data_get($employee, 'designation_bn_name') ?? $na);

    $sectionAttr = optional(\App\Models\Attribute::where('type', 29)->find($employee->section_id));
    $section = $isBangla
        ? (data_get($sectionAttr, 'bn_name') ?? data_get($sectionAttr, 'name') ?? data_get($employee, 'section_bn_name') ?? data_get($employee, 'section_name') ?? $na)
        : (data_get($sectionAttr, 'name') ?? data_get($employee, 'section_name') ?? data_get($sectionAttr, 'bn_name') ?? data_get($employee, 'section_bn_name') ?? $na);

    $employeeId = data_get($employee, 'employee_id', $na);
    $presentAddress = collect([
        data_get($employee, 'present_address'),
        data_get($employee, 'present_village'),
        data_get($employee, 'present_post_office'),
        data_get($employee, 'present_upazila'),
        data_get($employee, 'present_district'),
    ])->filter(fn ($v) => filled($v))->implode(', ');
    $permanentAddress = collect([
        data_get($employee, 'permanent_address'),
        data_get($employee, 'permanent_village'),
        data_get($employee, 'permanent_post_office'),
        data_get($employee, 'permanent_upazila'),
        data_get($employee, 'permanent_district'),
    ])->filter(fn ($v) => filled($v))->implode(', ');
    $presentAddress = $presentAddress ?: data_get($employee, 'address', $na);
    $permanentAddress = $permanentAddress ?: data_get($employee, 'address', $na);
	// Resolve salary breakdown via compliance-aware helper (factory_no determines gross source & deduction base)
	$sal        = hr_employee_salary($employee, $factory ?? null, $salaryKey ?? null);
	$gross      = $sal['gross'];
	$basic      = $sal['basic'];
	$house      = $sal['house'];
	$medical    = $sal['medical'];
	$transport  = $sal['transport'];
	$food       = $sal['food'];
	$ot_rate    = $sal['ot_rate'];
	$deductFrom = $sal['deduct_from']; // 'gross' (factory_no=0) or 'basic' (factory_no=1/2)
@endphp


{{-- 
@php
    $general = general();
    $gradeName = optional(\App\Models\Attribute::query()->find($employee->grade_lavel))->name;
    $sectionName = optional(\App\Models\Attribute::query()->find($employee->section_id))->name;
    $presentAddress = collect([
        $employee->present_address ?? null,
        $employee->present_village ?? null,
        $employee->present_post_office ?? null,
        $employee->present_upazila ?? null,
        $employee->present_district ?? null,
    ])->filter(fn ($value) => filled($value))->implode(', ');
    $permanentAddress = collect([
        $employee->permanent_address ?? null,
        $employee->permanent_village ?? null,
        $employee->permanent_post_office ?? null,
        $employee->permanent_upazila ?? null,
        $employee->permanent_district ?? null,
    ])->filter(fn ($value) => filled($value))->implode(', ');
    $presentAddress = $presentAddress ?: ($employee->address ?? data_get($profile, 'present_address') ?: 'N/A');
    $permanentAddress = $permanentAddress ?: data_get($profile, 'permanent_address', 'N/A');
    $jobType = $employee->work_type ?: data_get($profile, 'job_type', data_get($profile, 'work_type', 'Worker'));
    $joiningDateShort = blank($employee->joining_date) ? 'N/A' : \Illuminate\Support\Carbon::parse($employee->joining_date)->format('d/m/y');
    $basicForOt = $basicSalary > 0 ? $basicSalary : (float) ($employee->gross_salary ?? 0);
    $otRate = $basicForOt > 0 ? round(($basicForOt / 208) * 2, 2) : 0;
@endphp
@php
	// Get gross from employee
	$gross = $employee->gross_salary ?? ($employee->basic_salary + $employee->house_rent + $employee->medical_allowance + $employee->transport_allowance + $employee->food_allowance ?? null);
	// Get Medical, Transport, Food (MTF) from salary key (if available)
	$medical = $salaryKey->medical ?? 0;
	$transport = $salaryKey->transport ?? 0;
	$food = $salaryKey->lunch ?? 0;
	$mtf = $medical + $transport + $food;
	// Calculate Basic and House
	$basic = $gross && $mtf ? ($gross - $mtf) / 1.5 : null;
	$house = $basic ? $basic / 2 : null;
	// OT Rate from factory (if available)
	$ot_rate = $factory->ot_rate ?? $employee->ot_rate ?? null;
@endphp --}}




<div class="letter-box" style="border:none; padding:0; margin-top:0;">
    <div class="company-head" style="margin-bottom:10px;">
        <h3 style="margin:0; font-size:22px;">{{ $companyName }}</h3>
        <div style="font-size:13px;">{{ $companyAddress }}</div>
        <div style="margin-top:4px; font-weight:700; font-size:16px;">{{ $t('নিয়োগ পত্র', 'Appointment Letter') }}</div>
    </div>

    <table class="letter-grid" style="margin-bottom:12px;">
        <tr>
            <td class="k">{{ $t('শ্রমিকের নাম', 'Employee Name') }}</td><td class="s">:</td><td class="v">{{ $employeeName }}</td>
            <td class="k">{{ $t('পিতার নাম', 'Father Name') }}</td><td class="s">:</td><td class="v">{{ $fatherName }}</td>
        </tr>
        <tr>
            <td class="k">{{ $t('স্বামী/স্ত্রীর নাম', 'Spouse Name') }}</td><td class="s">:</td><td class="v">{{ $spouseName }}</td>
            <td class="k">{{ $t('মাতার নাম', 'Mother Name') }}</td><td class="s">:</td><td class="v">{{ $motherName }}</td>
        </tr>
        <tr>
            <td class="k">{{ $t('সেকশন', 'Section') }}</td><td class="s">:</td><td class="v">{{ $section }}</td>
            <td class="k">{{ $t('পদবী', 'Designation') }}</td><td class="s">:</td><td class="v">{{ $designation }}</td>
        </tr>
        <tr>
            <td class="k">{{ $t('কার্ড নং', 'Card No.') }}</td><td class="s">:</td><td class="v">{{ $employeeId }}</td>
            <td class="k">{{ $t('যোগদানের তারিখ', 'Joining Date') }}</td><td class="s">:</td><td class="v">{{ $joiningDate }}</td>
        </tr>
        <tr>
            <td class="k">{{ $t('বর্তমান ঠিকানা', 'Present Address') }}</td><td class="s">:</td><td colspan="4">{{ $presentAddress }}</td>
        </tr>
        <tr>
            <td class="k">{{ $t('স্থায়ী ঠিকানা', 'Permanent Address') }}</td><td class="s">:</td><td colspan="4">{{ $permanentAddress }}</td>
        </tr>
    </table>

    <p style="font-size:12px; line-height:1.7; text-align:justify; margin-bottom:10px;">
        {{ $t('আপনার আবেদন, সাক্ষাৎকার এবং যোগ্যতা যাচাইয়ের পরিপ্রেক্ষিতে আপনাকে উল্লিখিত পদে নিম্নোক্ত শর্তসাপেক্ষে নিয়োগ প্রদান করা হলো। প্রাথমিকভাবে ৩ (তিন) মাস প্রবেশনকাল হিসেবে গণ্য হবে। প্রয়োজনবোধে এই সময়সীমা বর্ধিত হতে পারে।', 'Based on your application, interview, and qualification review, you are hereby appointed to the mentioned position under the following terms and conditions. Initially, the first 3 (three) months will be treated as probation, which may be extended if required.') }}
    </p>

    <div style="font-size:12px; line-height:1.7;">
        <p style="margin:0 0 6px;"><strong>{{ $t('১। বেতন ও ভাতাসমূহ', '1. Salary and Allowances') }}:</strong></p>
        <table class="salary-table" style="margin-bottom:10px;">
            <tbody>
                <tr><td>{{ $t('মাসিক মূল বেতন', 'Monthly Basic Salary') }}</td><td style="width:70px;">{{ $t('টাকা', 'BDT') }}</td><td style="width:140px;">{{ number_format($basic, 2) }}</td></tr>
                <tr><td>{{ $t('বাড়ী ভাড়া ভাতা', 'House Rent Allowance') }}</td><td>{{ $t('টাকা', 'BDT') }}</td><td>{{ number_format($house, 2) }}</td></tr>
                <tr><td>{{ $t('চিকিৎসা ভাতা', 'Medical Allowance') }}</td><td>{{ $t('টাকা', 'BDT') }}</td><td>{{ number_format($medical, 2) }}</td></tr>
                <tr><td>{{ $t('যাতায়াত ভাতা', 'Transport Allowance') }}</td><td>{{ $t('টাকা', 'BDT') }}</td><td>{{ number_format($transport, 2) }}</td></tr>
                <tr><td>{{ $t('খাদ্য ভাতা', 'Food Allowance') }}</td><td>{{ $t('টাকা', 'BDT') }}</td><td>{{ number_format($food, 2) }}</td></tr>
                <tr><td><strong>{{ $t('মাসিক মোট বেতন', 'Monthly Gross Salary') }}</strong></td><td>{{ $t('টাকা', 'BDT') }}</td><td><strong>{{ number_format($gross, 2) }}</strong></td></tr>
                <tr><td>{{ $t('ওভারটাইম হার (ঘন্টা)', 'Overtime Rate (Per Hour)') }}</td><td>{{ $t('টাকা', 'BDT') }}</td><td>{{ number_format($ot_rate, 2) }}</td></tr>
            </tbody>
        </table>

        <p style="margin:0 0 4px;"><strong>{{ $t('২। কর্মঘন্টা ও ওভারটাইম', '2. Working Hours and Overtime') }}:</strong> {{ $t('প্রতিদিন ৮ ঘন্টা নিয়মিত কর্মঘন্টা এবং আইন অনুযায়ী ওভারটাইম প্রযোজ্য হবে।', 'Regular duty is 8 hours per day, and overtime will be applicable as per labor law and company policy.') }}</p>
        <p style="margin:0 0 4px;"><strong>{{ $t('৩। ছুটি', '3. Leave') }}:</strong> {{ $t('কারখানার নীতিমালা ও প্রযোজ্য শ্রম আইন অনুযায়ী সাপ্তাহিক, নৈমিত্তিক, অর্জিত, অসুস্থতা ও অন্যান্য ছুটি প্রযোজ্য হবে।', 'Weekly, casual, earned, sick, and other leaves will be provided according to company policy and applicable labor law.') }}</p>
        <p style="margin:0 0 4px;"><strong>{{ $t('৪। চাকরি সমাপ্তি', '4. Termination/Resignation') }}:</strong> {{ $t('বাংলাদেশ শ্রম আইন অনুযায়ী নোটিশ/নোটিশ বেতনের শর্ত প্রযোজ্য হবে।', 'Notice period or payment in lieu of notice will apply as per Bangladesh Labor Law.') }}</p>
        <p style="margin:0 0 4px;"><strong>{{ $t('৫। শৃঙ্খলা', '5. Discipline') }}:</strong> {{ $t('প্রতিষ্ঠানের সকল নিয়ম, নিরাপত্তা ও আচরণবিধি মেনে চলা বাধ্যতামূলক।', 'Compliance with all company rules, safety standards, and code of conduct is mandatory.') }}</p>

        <p style="font-size:12px; line-height:1.7; text-align:justify; margin:10px 0 18px;">
            {{ $t('আমি এই নিয়োগপত্রের শর্তাবলী বুঝে স্বেচ্ছায় গ্রহণ করলাম এবং প্রতিষ্ঠানের নীতিমালা মেনে দায়িত্ব পালনে সম্মত হলাম।', 'I have read and understood this appointment letter and voluntarily accepted all terms and conditions. I agree to perform my duties according to company policy.') }}
        </p>
    </div>

    <table style="width:100%; margin-top:24px; font-size:12px; border-collapse:collapse;">
        <tr>
            <td style="width:33%; text-align:left; padding-top:30px;">{{ $t('তারিখ', 'Date') }}</td>
            <td style="width:33%; text-align:center; padding-top:30px;">{{ $t('কর্মচারীর স্বাক্ষর', 'Employee Signature') }}</td>
            <td style="width:34%; text-align:right; padding-top:30px;">{{ $t('প্রশাসন, মানব সম্পদ বিভাগ', 'Administration, HR Department') }}</td>
        </tr>
    </table>
</div>
