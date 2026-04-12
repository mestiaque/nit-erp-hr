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
    $employeeId = data_get($employee, 'employee_id', $na);
    $employeePhoto = method_exists($employee, 'image') ? $employee->image() : null;
    $dob = data_get($employee, 'date_of_birth', data_get($employee, 'dob'));
    $employeeAge = '';

    if (filled($dob)) {
        try {
            $employeeAge = \Illuminate\Support\Carbon::parse($dob)->age;
        } catch (\Throwable $e) {
            $employeeAge = '';
        }
    }
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
    border: 1px solid #222 !important;
    padding: 4px 6px;
}
.age-verification-footer-table {
    width: 100%;
    font-size: 15px;
    border: none;
    margin-top: 18px;
}
.age-verification-footer-table td {
    border: none;
    padding-top: 30px;
}
</style>

<div class="age-verification-header">
    <h3 style="margin:0;">{{ $companyName }}</h3>
    <div>{{ $companyAddress }}</div>
    <div style="margin-top:8px;font-weight:700;">{{ $t('ফরম-১-৫', 'Form-1-5') }}</div>
    <div style="margin-bottom:8px; font-weight:700;">{{ $t('বয়স ও সম্মততার প্রত্যয়নপত্র', 'Age and Fitness Verification Certificate') }}</div>
</div>

<table class="age-verification-main-table">
    <tr>
        <td style="width:50%; vertical-align:top;">
            <table style="width:100%; border:none; font-size:15px;">
                <tr><td>{{ $t('১. আই.ডি নম্বর', '1. Employee ID') }}: {{ $employeeId }}</td></tr>
                <tr><td>{{ $t('তারিখ', 'Date') }}: {{ now()->format('d/m/Y') }}</td></tr>
                <tr><td>{{ $t('২. নাম', '2. Name') }}: {{ $employeeName }}</td></tr>
                <tr><td>{{ $t('৩. পিতার নাম', '3. Father Name') }}: {{ data_get($employee, 'father_name', $na) }}</td></tr>
                <tr><td>{{ $t('৪. মাতার নাম', '4. Mother Name') }}: {{ data_get($employee, 'mother_name', $na) }}</td></tr>
                <tr><td>{{ $t('৫. লিঙ্গ', '5. Gender') }}: {{ data_get($employee, 'sex', data_get($employee, 'gender', $na)) }}</td></tr>
                <tr><td>{{ $t('৬. স্থায়ী ঠিকানা', '6. Permanent Address') }}: {{ data_get($employee, 'permanent_address', data_get($employee, 'address', $na)) }}</td></tr>
                <tr><td>{{ $t('৭. বর্তমান ঠিকানা', '7. Present Address') }}: {{ data_get($employee, 'present_address', data_get($employee, 'address', $na)) }}</td></tr>
                <tr><td>{{ $t('৮. জন্ম তারিখ', '8. Date of Birth') }}: {{ $dob ?: $na }}</td></tr>
                <tr><td>{{ $t('৯. নির্ধারিত বয়স', '9. Verified Age') }}: {{ $employeeAge ? $employeeAge . ' ' . $t('বছর', 'years') : $na }}</td></tr>
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
                    <td style="width:33%; text-align:left;">{{ $t('সংশ্লিষ্ট স্বাক্ষর', 'Employee Signature') }}</td>
                    <td style="width:34%; text-align:center;">{{ $t('নির্বাচিত চিকিৎসকের স্বাক্ষর', 'Authorized Doctor Signature') }}</td>
                    <td style="width:33%; text-align:right;">{{ $t('সংশ্লিষ্ট টিপসই', 'Thumb Impression') }}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
