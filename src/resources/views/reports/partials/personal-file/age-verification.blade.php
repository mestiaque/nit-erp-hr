@php
    $employeeDataFn = \App\Services\HrOptionsService::getOptionsForEmployee();
    $employeeData = $employeeDataFn($employee, $request ?? null, $factory ?? null, $salaryKey ?? null, $profile ?? null, $nominee ?? null);
    $language = $language ?? data_get($request ?? null, 'language', 'en');
    $isBangla = $language === 'bn';
    $t = fn (string $bn, string $en) => $isBangla ? $bn : $en;
    $na = $t('প্রযোজ্য নয়', 'N/A');
    $companyName = $employeeData['company_name'];
    $companyAddress = $employeeData['company_address'];
    $designation = $employeeData['designation'];
    $employeeName = $employeeData['employee_name'];
    $qualification = $employeeData['qualification'];
    $nomineeName = $isBangla ? $employeeData['nominee_name_bn'] : $employeeData['nominee_name'];
    $nomineeRelation = $isBangla ? $employeeData['nominee_relation_bn'] : $employeeData['nominee_relation'];
    $nomineeAge = $isBangla ? en2bnNumber($employeeData['nominee_age']) : $employeeData['nominee_age'];
    $nomineeVillage = $isBangla ? $employeeData['nominee_village_bn'] : $employeeData['nominee_village'];
    $nomineePoStation = $isBangla ? $employeeData['nominee_po_station_bn'] : $employeeData['nominee_po_station'];
    $nomineePostOffice = $isBangla ? $employeeData['nominee_post_office_bn'] : $employeeData['nominee_post_office'];
    $nomineeDistrict = $isBangla ? $employeeData['nominee_district_bn'] : $employeeData['nominee_district'];
    $nomineeNid = $isBangla ? en2bnNumber($employeeData['nominee_nid']) : $employeeData['nominee_nid'];
    $nomineeMobile = $isBangla ? en2bnNumber($employeeData['nominee_mobile']) : $employeeData['nominee_mobile'];
    $nationality = $isBangla ? 'বাংলাদেশী' : 'Bangladeshi';
    $permanentAddress = $isBangla ? $employeeData['permanent_address_bn'] : $employeeData['permanent_address'];
    $presentAddress = $isBangla ? $employeeData['present_address_bn'] : $employeeData['present_address'];
    $birthDate = $isBangla ? bn_date($employeeData['birth_date']) : $employeeData['birth_date'];
    $employeeAge = $isBangla ? en2bnNumber($employeeData['employee_age']) : $employeeData['employee_age'];
    $employeePhoto = $employeeData['employee_photo'];
    $nomineeImage = $employeeData['nominee_image'];
    $fatherName = $employeeData['father_name'];
    $motherName = $employeeData['mother_name'];
    $joiningDate = $employeeData['joining_date'];
    $employeeId = $employeeData['employee_id'];
    $gender = $employeeData['gender'];
@endphp

<style>
.age-verification-header {
    text-align: center;
    margin-bottom: 10px;
}
.age-verification-photo {
    width: 120px;
    height: 120px;
    object-fit: cover;
    border: 1px solid #888 !important;
    margin-bottom: 8px;
}
.age-verification-main-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 15px;
    margin-bottom: 18px;
    border: 2px solid #222 !important;
}
.age-verification-main-table td, .age-verification-main-table th {
    border: 1.5px solid #222 !important;
    padding: 6px 8px;
    vertical-align: top;
}
.age-verification-main-table table {
    border-collapse: collapse;
    width: 100%;
}
.age-verification-main-table table td {
    /* border-top: 1px solid #222 !important; */
    padding: 4px 6px;
}
.age-verification-footer-table {
    width: 100%;
    font-size: 15px;
    border: none;
    margin-top: 40px;
    margin-bottom: 0px;
}
.age-verification-footer-table td {
    border: none !important;
    padding-top: 30px;
}
.age-verification-footer-table td span{
    border-top: 1px solid #222 !important;
    width: 200px;
    display: inline-block;
    text-align: center;
}
</style>

<div class="age-verification-header">
    <h3 style="margin:0;">{{ $companyName }}</h3>
    <div>{{ $companyAddress }}</div>
    <div style="margin-top:8px;font-weight:700;">{{ $t('ফরম', 'Form') }} - {{ $isBangla ? en2bnNumber($employee->id) : $employee->id }}</div>
    <div style="margin-bottom:8px; font-weight:700;">{{ $t('বয়স ও সম্মততার প্রত্যয়নপত্র', 'Age and Fitness Verification Certificate') }}</div>
</div>

<table class="age-verification-main-table">
    <tr>
        <td style="width:50%; vertical-align:top;">
            <table style="width:100%; border:none; font-size:15px;">
                <tr><td>{{ $t('আই.ডি নম্বর', 'Employee ID') }}: {{ $employeeId }}</td></tr>
                <tr><td>{{ $t('তারিখ', 'Date') }}: {{ $joiningDate }}</td></tr>
                <tr><td>{{ $t('নাম', 'Name') }}: {{ $employeeName }}</td></tr>
                <tr><td>{{ $t('পিতার নাম', 'Father Name') }}: {{ $fatherName }}</td></tr>
                <tr><td>{{ $t('মাতার নাম', 'Mother Name') }}: {{ $motherName }}</td></tr>
                <tr><td>{{ $t('লিঙ্গ', 'Gender') }}: {{ $gender }}</td></tr>
                <tr><td>{{ $t('স্থায়ী ঠিকানা', 'Permanent Address') }}: {{ $permanentAddress }}</td></tr>
                <tr><td>{{ $t('বর্তমান ঠিকানা', 'Present Address') }}: {{ $presentAddress }}</td></tr>
                <tr><td>{{ $t('জন্ম তারিখ', 'Date of Birth') }}: {{ $birthDate }}</td></tr>
                <tr><td>{{ $t('নির্ধারিত বয়স', 'Verified Age') }}: {{ $employeeAge ? $employeeAge . ' ' . $t('বছর', 'years') : $na }}</td></tr>
            </table>
        </td>
        <td style="width:50%; vertical-align:top;">
            <div style="text-align:right;">
                @if($employeePhoto)
                    <img src="{{ asset($employeePhoto) }}" class="age-verification-photo" alt="{{ $t('কর্মচারীর ছবি', 'Employee Photo') }}">
                @endif
            </div>
            <div style="margin-top:8px; font-size:15px;">
                {{ $t('আমি এই মর্মে প্রত্যয়ন করছি যে, উপরোক্ত ব্যক্তিকে পরীক্ষা করে তাকে প্রতিষ্ঠানে কাজের জন্য উপযুক্ত পাওয়া গেছে।', 'I hereby certify that I have examined the above person and found him/her fit for employment in this establishment.') }}
                <br><br>
                {{ $t('পরীক্ষার ভিত্তিতে তার বয়স নির্ধারিত হয়েছে', 'Based on examination, the assessed age is') }}
                {{ $employeeAge ? $employeeAge . ' ' . $t('বছর', 'years') : $na }}.
            </div>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <table class="age-verification-footer-table">
                <tr>
                    <td style="width:33%; text-align:left;"><span>{{ $t('সংশ্লিষ্ট স্বাক্ষর', 'Employee Signature') }}</span></td>
                    <td style="width:34%; text-align:center;"><span>{{ $t('নির্বাচিত চিকিৎসকের স্বাক্ষর', 'Authorized Doctor Signature') }}</span></td>
                    <td style="width:33%; text-align:right;"><span>{{ $t('সংশ্লিষ্ট টিপসই', 'Thumb Impression') }}</span></td>
                </tr>
            </table>
        </td>
    </tr>
</table>
