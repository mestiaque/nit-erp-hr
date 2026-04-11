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

<div class="letter-box" style="border:none; padding:0; margin-top:0;">
    <div class="company-head" style="margin-bottom:10px;">
        <h3 style="margin:0; font-size:22px;">{{ $general->title ?? 'বঙ্গ নীটওয়্যার লিমিটেড' }}</h3>
        <div style="font-size:13px;">{{ $general->address_one ?? data_get($general, 'address') ?? 'প্লট # 1595, গাছা, গাজীপুর সদর, গাজীপুর।' }}</div>
        <div style="margin-top:4px; font-weight:700; font-size:16px;">আবেদন পত্র (Application Letter)</div>
    </div>

    {{-- <table class="letter-grid" style="margin-bottom:12px;">
        <tr>
            <td class="k">নাম</td><td class="s">:</td><td class="v">{{ $employee->name ?? 'N/A' }}</td>
            <td class="k">পিতার নাম</td><td class="s">:</td><td class="v">{{ $employee->father_name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="k">স্বামী/স্ত্রীর নাম</td><td class="s">:</td><td class="v">{{ $employee->spouse_name ?? 'N/A' }}</td>
            <td class="k">মাতার নাম</td><td class="s">:</td><td class="v">{{ $employee->mother_name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="k">সেকশন</td><td class="s">:</td><td class="v">{{ $sectionName ?: 'N/A' }}</td>
            <td class="k">পদবী</td><td class="s">:</td><td class="v">{{ optional($employee->designation)->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="k">কার্ড নং</td><td class="s">:</td><td class="v">{{ $employee->employee_id ?? 'N/A' }}</td>
            <td class="k">যোগদানের তারিখ</td><td class="s">:</td><td class="v">{{ $joiningDateShort }}</td>
        </tr>
        <tr>
            <td class="k">বর্তমান ঠিকানা</td><td class="s">:</td><td colspan="4">{{ $presentAddress }}</td>
        </tr>
        <tr>
            <td class="k">স্থায়ী ঠিকানা</td><td class="s">:</td><td colspan="4">{{ $permanentAddress }}</td>
        </tr>
    </table> --}}

    <p style="font-size:12px; line-height:1.7; text-align:justify; margin-bottom:10px;">
        <strong>প্রাপক:</strong><br>
        ব্যবস্থাপনা পরিচালক/মানব সম্পদ বিভাগ<br>
        {{ $general->title ?? '................................' }}<br>
        {{ $general->address_one ?? data_get($general, 'address') ?? '................................' }}<br>
        <br>
        বিষয়ঃ {{ $general->title ?? '................................' }}-এর {{ $factoryName ?? 'ফ্যাক্টরি' }}-তে {{ optional($employee->designation)->bn_name ?? '................................' }} পদে চাকুরির জন্য আবেদন।<br>
        <br>
        মহোদয়,<br>
        <br>
        বিনীত নিবেদন এই যে, আমি {{ $employee->name ?? '................................' }}, পিতা: {{ $employee->father_name ?? '................................' }}, মাতাঃ {{ $employee->mother_name ?? '................................' }},<br>
        <span style="display:inline-block; min-width:120px;">বর্তমান ঠিকানা:</span>
        গ্রাম: {{ $employee->present_village ?? '........' }}, ডাকঘর: {{ $employee->present_post_office ?? '........' }}, উপজেলা: {{ $employee->present_upazila ?? '........' }}, জেলা: {{ $employee->present_district ?? '........' }}<br>
        <span style="display:inline-block; min-width:120px;">স্থায়ী ঠিকানা:</span>
        গ্রাম: {{ $employee->permanent_village ?? '........' }}, ডাকঘর: {{ $employee->permanent_post_office ?? '........' }}, উপজেলা: {{ $employee->permanent_upazila ?? '........' }}, জেলা: {{ $employee->permanent_district ?? '........' }}<br>
        <br>
        আমি {{ $general->title ?? '................................' }}-এর {{ $factoryName ?? 'ফ্যাক্টরি' }}-তে {{ optional($employee->designation)->btn_name ?? '................................' }} পদে চাকুরির জন্য আবেদন করছি।<br>
        <br>
        আমার শিক্ষাগত যোগ্যতা, দক্ষতা ও অভিজ্ঞতা অনুযায়ী আমি উক্ত পদে নিয়োগের জন্য নিজেকে উপযুক্ত মনে করি। আমার সকল তথ্য ও প্রয়োজনীয় কাগজপত্র সংযুক্ত করা হলো।<br>
        <br>
        আমি অত্র প্রতিষ্ঠানে নিয়মিত, নিষ্ঠার সাথে এবং সততার সাথে দায়িত্ব পালন করব বলে অঙ্গীকার করছি।<br>
        <br>
        আমার আবেদনটি সদয় বিবেচনা করে আমাকে {{ optional($employee->designation)->bn_name ?? '................................' }} পদে নিয়োগ দেয়ার জন্য বিনীত অনুরোধ করছি।<br>
        <br>
        ধন্যবাদান্তে,<br>
        <span style="margin-left:40px;">বিনীত,</span>
    </p>

    <!-- ...application letter does not require employer's terms and conditions, so this section is removed... -->
    </div>

    <table style="width:100%; margin-top:24px; font-size:12px; border-collapse:collapse;">
        <tr>
            <td style="width:33%; text-align:left; padding-top:30px;">তারিখ: {{ date('d/m/Y') }}</td>
            <td style="width:67%; text-align:right; padding-top:30px;">আবেদনকারীর স্বাক্ষর</td>
        </tr>
    </table>
</div>
