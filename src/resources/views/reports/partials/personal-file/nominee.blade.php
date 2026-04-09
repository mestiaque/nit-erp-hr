<h4 class="section-title">{{ $label('মনোনয়ন ফর্ম', 'Nominee Information') }}</h4>
@if(!empty($nomineeInfo))
    <table class="two-col">
        <tr><th>{{ $label('মনোনীত ব্যক্তির নাম', 'Nominee Name') }}</th><td>{{ data_get($nomineeInfo, 'name', 'N/A') }}</td></tr>
        <tr><th>{{ $label('সম্পর্ক', 'Relationship') }}</th><td>{{ data_get($nomineeInfo, 'relationship', 'N/A') }}</td></tr>
        <tr><th>{{ $label('জাতীয় পরিচয়পত্র', 'NID') }}</th><td>{{ data_get($nomineeInfo, 'nid', 'N/A') }}</td></tr>
        <tr><th>{{ $label('মোবাইল', 'Mobile') }}</th><td>{{ data_get($nomineeInfo, 'mobile', 'N/A') }}</td></tr>
        <tr><th>{{ $label('ঠিকানা', 'Address') }}</th><td>{{ data_get($nomineeInfo, 'address', 'N/A') }}</td></tr>
        <tr><th>{{ $label('শতকরা অংশ', 'Nominee Share') }}</th><td>{{ data_get($nomineeInfo, 'share', 'N/A') }}</td></tr>
    </table>
@else
    <p>{{ $label('কোন মনোনয়ন তথ্য পাওয়া যায়নি।', 'No nominee information found.') }}</p>
@endif
