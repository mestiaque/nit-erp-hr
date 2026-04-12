
@php
    $general = general();
    $companyName = $general->title ?? config('company.name', '');
    $companyAddress = $general->address_one ?? data_get($general, 'address') ?? config('company.address', '');
    $employeeName = $employee->name ?? '';
    $fatherName = $employee->father_name ?? '';
    $motherName = $employee->mother_name ?? '';
    $presentVillage = $employee->present_village ?? '';
    $presentPostOffice = $employee->present_post_office ?? '';
    $presentUpazila = $employee->present_upazila ?? '';
    $presentDistrict = $employee->present_district ?? '';
    $permanentVillage = $employee->permanent_village ?? '';
    $permanentPostOffice = $employee->permanent_post_office ?? '';
    $permanentUpazila = $employee->permanent_upazila ?? '';
    $permanentDistrict = $employee->permanent_district ?? '';
    $designation = optional($employee->designation)->name ?? data_get($employee, 'designation_name', '');
    $designationBn = optional($employee->designation)->bn_name ?? $designation;
    $section = optional($employee->section)->name ?? data_get($employee, 'section_name', '');
    $applicationDate = now()->format('d/m/Y');
@endphp

<div style="text-align:center; margin-bottom:10px;">
    <h3 style="margin:0;">{{ $companyName }}</h3>
    <div>{{ $companyAddress }}</div>
    <div style="margin-top:4px; font-weight:700; font-size:16px;">আবেদন পত্র</div>
</div>

<div style="margin-bottom:10px;">
    তারিখ: {{ $applicationDate }}<br>
    বরাবর,<br>
    ব্যবস্থাপনা পরিচালক<br>
    {{ $companyName }}<br>
    {{ $companyAddress }}
</div>

<div style="margin-bottom:10px; font-weight:600;">বিষয়ঃ চাকুরির জন্য আবেদন।</div>

<div style="margin-bottom:10px;">জনাব,<br>
আমি {{ $employeeName }}, পিতা: {{ $fatherName }}, মাতাঃ {{ $motherName }}।<br>
বর্তমান ঠিকানা: গ্রাম: {{ $presentVillage }}, ডাকঘর: {{ $presentPostOffice }}, উপজেলা: {{ $presentUpazila }}, জেলা: {{ $presentDistrict }}<br>
স্থায়ী ঠিকানা: গ্রাম: {{ $permanentVillage }}, ডাকঘর: {{ $permanentPostOffice }}, উপজেলা: {{ $permanentUpazila }}, জেলা: {{ $permanentDistrict }}<br>
{{ $companyName }}-এর {{ $section }} সেকশনে {{ $designationBn }} পদে চাকুরির জন্য আবেদন করছি।
</div>

<div style="margin-bottom:10px;">
আমার শিক্ষাগত যোগ্যতা, দক্ষতা ও অভিজ্ঞতা অনুযায়ী আমি উক্ত পদে নিয়োগের জন্য নিজেকে উপযুক্ত মনে করি। আমার সকল তথ্য ও প্রয়োজনীয় কাগজপত্র সংযুক্ত করা হলো।
</div>

<div style="margin-bottom:10px;">
আমি অত্র প্রতিষ্ঠানে নিয়মিত, নিষ্ঠার সাথে এবং সততার সাথে দায়িত্ব পালন করব বলে অঙ্গীকার করছি।
</div>

<div style="margin-bottom:10px;">
আমার আবেদনটি সদয় বিবেচনা করে আমাকে {{ $designationBn }} পদে নিয়োগ দেয়ার জন্য বিনীত অনুরোধ করছি।
</div>

<div style="margin-bottom:30px;">
ধন্যবাদান্তে,<br>
নাম: {{ $employeeName }}<br>
</div>

<div style="margin-top:30px;">
আপনার সদয় অনুমোদনের অনুরোধে আবেদন করা হলো।<br>
আবেদনকারীর স্বাক্ষর:------------------------------
</div>
