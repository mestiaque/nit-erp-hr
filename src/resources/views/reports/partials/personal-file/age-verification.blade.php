<h4 class="section-title">{{ $label('বয়স যাচাইকরণ', 'Age Verification') }}</h4>
@if(!empty($ageVerification))
    <table class="two-col">
        <tr><th>{{ $label('জন্ম তারিখ', 'Date of Birth') }}</th><td>{{ $fmtDate(data_get($ageVerification, 'date_of_birth')) }}</td></tr>
        <tr><th>{{ $label('যাচাইয়ের ধরন', 'Verification Type') }}</th><td>{{ data_get($ageVerification, 'verification_type', 'N/A') }}</td></tr>
        <tr><th>{{ $label('ডকুমেন্ট নম্বর', 'Document Number') }}</th><td>{{ data_get($ageVerification, 'document_number', 'N/A') }}</td></tr>
        <tr><th>{{ $label('প্রদানকারী কর্তৃপক্ষ', 'Issuing Authority') }}</th><td>{{ data_get($ageVerification, 'issuing_authority', 'N/A') }}</td></tr>
        <tr><th>{{ $label('মন্তব্য', 'Remarks') }}</th><td>{{ data_get($ageVerification, 'remarks', 'N/A') }}</td></tr>
    </table>
@else
    <p>{{ $label('কোন বয়স যাচাইকরণ তথ্য পাওয়া যায়নি।', 'No age verification information found.') }}</p>
@endif
