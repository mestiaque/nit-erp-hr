@if(!empty($nominee) || filled($employee->nominee) || filled($employee->nominee_relation) || filled($employee->nominee_age))
@php
  $general = general();
  $companyName = $general->title ?? config('company.name', '');
  $companyAddress = $general->address_one ?? data_get($general, 'address') ?? config('company.address', '');
  $designation = optional($employee->designation)->name ?? data_get($employee, 'designation_name', '');
  $qualification = data_get($employee, 'qualification', data_get($profile, 'qualification', ''));
  $nomineeName = data_get($employee, 'nominee', data_get($nominee, 'nominee', ''));
  $nomineeRelation = data_get($employee, 'nominee_relation', data_get($nominee, 'nominee_relation', ''));
  $nomineeAge = data_get($employee, 'nominee_age', data_get($nominee, 'nominee_age', ''));
  $nomineeVillage = data_get($nominee, 'nominee_village', '');
  $nomineePoStation = data_get($nominee, 'nominee_po_station', '');
  $nomineePostOffice = data_get($nominee, 'nominee_post_office', '');
  $nomineeDistrict = data_get($nominee, 'nominee_district', '');
  $nomineeNid = data_get($nominee, 'nominee_nid', '');
  $nomineeMobile = data_get($nominee, 'nominee_mobile', '');
  $nationality = data_get($nominee, 'nominee_nationality', data_get($employee, 'nationality', 'বাংলাদেশী'));
  $permanentAddress = collect([
    data_get($employee, 'permanent_village'),
    data_get($employee, 'permanent_post_office'),
    data_get($employee, 'permanent_upazila'),
    data_get($employee, 'permanent_district'),
  ])->filter(fn ($value) => filled($value))->implode(', ');
  $presentAddress = collect([
    data_get($employee, 'present_village'),
    data_get($employee, 'present_post_office'),
    data_get($employee, 'present_upazila'),
    data_get($employee, 'present_district'),
  ])->filter(fn ($value) => filled($value))->implode(', ');
  $permanentAddress = $permanentAddress ?: data_get($employee, 'permanent_address', data_get($employee, 'address', ''));
  $presentAddress = $presentAddress ?: data_get($employee, 'present_address', data_get($employee, 'address', ''));
  $birthDate = data_get($employee, 'date_of_birth', data_get($employee, 'dob'));
  $employeeAge = '';
  if (filled($birthDate)) {
    try {
      $employeeAge = \Illuminate\Support\Carbon::parse($birthDate)->age;
    } catch (\Throwable $e) {
      $employeeAge = '';
    }
  }
  $employeePhoto = method_exists($employee, 'image') ? $employee->image() : null;
@endphp

<style>
  .nominee-sheet {
    font-family: SolaimanLipi, 'Noto Sans Bengali', Arial, sans-serif;
    color: #111;
    font-size: 14px;
    line-height: 1.35;
  }
  .nominee-sheet * {
    box-sizing: border-box;
  }
  .nominee-head {
    text-align: center;
    margin-bottom: 10px;
  }
  .nominee-company {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 3px;
  }
  .nominee-address {
    font-size: 13px;
    margin-bottom: 6px;
  }
  .nominee-form-no {
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 2px;
  }
  .nominee-rule {
    font-size: 11px;
    margin-bottom: 8px;
  }
  .nominee-title {
    font-size: 16px;
    font-weight: 700;
    text-decoration: underline;
  }
  .nominee-top {
    display: table;
    width: 100%;
    margin-top: 14px;
  }
  .nominee-top-main,
  .nominee-top-photo {
    display: table-cell;
    vertical-align: top;
  }
  .nominee-top-main {
    width: calc(100% - 138px);
    padding-right: 12px;
  }
  .nominee-top-photo {
    width: 138px;
  }
  .photo-box {
    width: 122px;
    height: 144px;
    border: 1px solid #555;
    margin-left: auto;
    background: #f7f7f7;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
  }
  .photo-box img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  .details-table,
  .nominee-table,
  .dist-table {
    width: 100%;
    border-collapse: collapse;
  }
  .details-table td {
    padding: 1px 2px;
    vertical-align: top;
    font-size: 13px;
    border: none !important;
  }
  .details-table .sl {
    width: 22px;
  }
  .details-table .label {
    width: 126px;
    white-space: nowrap;
  }
  .details-table .colon {
    width: 10px;
    text-align: center;
  }
  .details-table .value {
    width: auto;
  }
  .declaration {
    margin-top: 10px;
    text-align: justify;
    font-size: 13px;
  }
  .nominee-table {
    margin-top: 12px;
  }
  .nominee-table th,
  .nominee-table td {
    border: 1px solid #6d8c95 !important;
    padding: 4px 6px;
    vertical-align: top;
    font-size: 13px;
  }
  .nominee-table th {
    text-align: center;
    font-weight: 700;
  }
  .nominee-table .name-col {
    width: 42%;
  }
  .nominee-table .relation-col {
    width: 15%;
    text-align: center;
    vertical-align: middle;
  }
  .nominee-table .age-col {
    width: 12%;
    text-align: center;
    vertical-align: middle;
  }
  .nominee-table .share-col {
    width: 31%;
    padding: 0;
  }
  .dist-table th,
  .dist-table td {
    border: 1px solid #6d8c95 !important;
    padding: 3px 5px;
    font-size: 12px;
  }
  .dist-table th {
    background: #fff;
  }
  .dist-table .percent {
    width: 52px;
    text-align: center;
  }
  .note-text {
    margin-top: 12px;
    font-size: 13px;
  }
  .signatures {
    width: 100%;
    margin-top: 28px;
    border-collapse: collapse;
  }
  .signatures td {
    width: 50%;
    vertical-align: top;
    border: none !important;
    padding: 0;
    font-size: 13px;
  }
  .signature-block {
    margin-top: 54px;
    width: 250px;
  }
  .signature-block.right {
    margin-left: auto;
  }
  .signature-title {
    margin-bottom: 8px;
  }
  .signature-line {
    margin-top: 10px;
  }
</style>

<div class="nominee-sheet">
  <div class="nominee-head">
    <div class="nominee-company">{{ $companyName }}</div>
    <div class="nominee-address">{{ $companyAddress }}</div>
    <div class="nominee-form-no">ফরম-{{ $employee->id ?? '' }}</div>
    <div class="nominee-rule">[ধারা ১৩১, ১৩২(১), ১৩৫(২), ১২৪, ১২৬ ও ১২৭ এবং বিধি ১১৯(১), ১৩০(২), ১৩১(১) ও ১৩২(১)]</div>
    <div class="nominee-title">প্রভিডেন্ট ও বিভিন্ন খাতে প্রাপ্ত অর্থ পরিশোধের ক্ষেত্রে ও মনোনয়নকারীর ফরম</div>
  </div>

  <div class="nominee-top">
    <div class="nominee-top-main">
      <table class="details-table">
        <tr>
          <td class="sl">১.</td>
          <td class="label">প্রতিষ্ঠানের নাম</td>
          <td class="colon">:</td>
          <td class="value" colspan="4">{{ $companyName }}</td>
        </tr>
        <tr>
          <td class="sl">২.</td>
          <td class="label">প্রতিষ্ঠানের ঠিকানা</td>
          <td class="colon">:</td>
          <td class="value" colspan="4">{{ $companyAddress }}</td>
        </tr>
        <tr>
          <td class="sl">৩.</td>
          <td class="label">কর্মচারীর নাম</td>
          <td class="colon">:</td>
          <td class="value">{{ $employee->name ?? '' }}</td>
          <td class="label">আইডি</td>
          <td class="colon">:</td>
          <td class="value">{{ $employee->employee_id ?? '' }}</td>
        </tr>
        <tr>
          <td class="sl">৪.</td>
          <td class="label">পিতার নাম</td>
          <td class="colon">:</td>
          <td class="value">{{ $employee->father_name ?? '' }}</td>
          <td class="label">শিক্ষাগত যোগ্যতা</td>
          <td class="colon">:</td>
          <td class="value">{{ $qualification }}</td>
        </tr>
        <tr>
          <td class="sl">৫.</td>
          <td class="label">মাতার নাম</td>
          <td class="colon">:</td>
          <td class="value">{{ $employee->mother_name ?? '' }}</td>
          <td class="label">পদের নাম</td>
          <td class="colon">:</td>
          <td class="value">{{ $designation }}</td>
        </tr>
        <tr>
          <td class="sl">৬.</td>
          <td class="label">স্থায়ী ঠিকানা</td>
          <td class="colon">:</td>
          <td class="value">{{ $permanentAddress }}</td>
          <td class="label">জাতীয়তা</td>
          <td class="colon">:</td>
          <td class="value">{{ $nationality }}</td>
        </tr>
        <tr>
          <td class="sl">৭.</td>
          <td class="label">বর্তমান ঠিকানা</td>
          <td class="colon">:</td>
          <td class="value">{{ $presentAddress }}</td>
          <td class="label">জন্ম তারিখ</td>
          <td class="colon">:</td>
          <td class="value">{{ $fmtDate($birthDate) }}</td>
        </tr>
        <tr>
          <td class="sl">৮.</td>
          <td class="label">যোগদানের তারিখ</td>
          <td class="colon">:</td>
          <td class="value">{{ $fmtDate($employee->joining_date) }}</td>
          <td class="label">বয়স</td>
          <td class="colon">:</td>
          <td class="value">{{ $employeeAge }}</td>
        </tr>
        <tr>
          <td class="sl">৯.</td>
          <td class="label">রক্তের গ্রুপ</td>
          <td class="colon">:</td>
          <td class="value">{{ $employee->blood_group ?? '' }}</td>
          <td class="label">ধর্ম</td>
          <td class="colon">:</td>
          <td class="value">{{ $employee->religion ?? '' }}</td>
        </tr>
        <tr>
          <td class="sl">১০.</td>
          <td class="label">জাতীয় পরিচয়পত্র নম্বর</td>
          <td class="colon">:</td>
          <td class="value">{{ $employee->nid_number ?? '' }}</td>
          <td class="label">মোবাইল নম্বর</td>
          <td class="colon">:</td>
          <td class="value">{{ $employee->mobile ?? '' }}</td>
        </tr>
      </table>
    </div>

    <div class="nominee-top-photo">
      <div class="photo-box">
        @if(filled($employeePhoto))
          <img src="{{ asset($employeePhoto) }}" alt="Employee Photo">
        @endif
      </div>
    </div>
  </div>

  <div class="declaration">
    আমি স্বীকার করিতেছি যে, আমার মৃত্যু হইলে অথবা আমার অক্ষমতার কারণে আমার প্রাপ্য বকেয়া মজুরি, প্রভিডেন্ট ফান্ড, বীমা, দুর্ঘটনা ক্ষতিপূরণ, লভ্যাংশ ও অন্যান্য পাওনা নিম্নে উল্লিখিত মনোনীত ব্যক্তিকে প্রদেয় হইবে।
  </div>

  <table class="nominee-table">
    <tr>
      <th class="name-col">মনোনীত ব্যক্তির নাম, ঠিকানা ও এন.আই.ডি নম্বর</th>
      <th class="relation-col">সম্পর্ক</th>
      <th class="age-col">বয়স</th>
      <th class="share-col">শ্রমিকের প্রাপ্য অর্থের যে অংশ প্রাপ্য হইবে</th>
    </tr>
    <tr>
      <td class="name-col">
        নাম: {{ $nomineeName }}<br>
        গ্রাম: {{ $nomineeVillage }}<br>
        ডাকঘর: {{ $nomineePostOffice }}<br>
        থানা: {{ $nomineePoStation }}<br>
        জেলা: {{ $nomineeDistrict }}<br>
        এন. আই. ডি নং: {{ $nomineeNid }}<br>
        মোবাইল নং: {{ $nomineeMobile }}
      </td>
      <td class="relation-col">{{ $nomineeRelation }}</td>
      <td class="age-col">{{ $nomineeAge }} {{ filled($nomineeAge) ? 'বছর' : '' }}</td>
      <td class="share-col">
        <table class="dist-table">
          <tr>
            <th>খাতসমূহ</th>
            <th class="percent">অংশ</th>
          </tr>
          <tr>
            <td>অনাদায় মজুরি</td>
            <td class="percent">{{ data_get($nominee, 'distribution_net_payment', '0') }}%</td>
          </tr>
          <tr>
            <td>প্রভিডেন্ট ফান্ড</td>
            <td class="percent">{{ data_get($nominee, 'distribution_provident_fund', '0') }}%</td>
          </tr>
          <tr>
            <td>বীমা</td>
            <td class="percent">{{ data_get($nominee, 'distribution_insurance', '0') }}%</td>
          </tr>
          <tr>
            <td>দুর্ঘটনা ক্ষতিপূরণ</td>
            <td class="percent">{{ data_get($nominee, 'distribution_accident_fine', '0') }}%</td>
          </tr>
          <tr>
            <td>লভ্যাংশ</td>
            <td class="percent">{{ data_get($nominee, 'distribution_profit', '0') }}%</td>
          </tr>
          <tr>
            <td>অন্যান্য</td>
            <td class="percent">{{ data_get($nominee, 'distribution_others', '0') }}%</td>
          </tr>
        </table>
      </td>
    </tr>
  </table>

  <div class="note-text">
    প্রত্যয়ন করিতেছি যে, আমার উপস্থিতিতে উপরোক্ত তথ্যসমূহ লিখিত বিবৃতির সহিত সঙ্গতিপূর্ণ এবং উহাতে কোনো আপত্তি নাই।
  </div>

  <table class="signatures">
    <tr>
      <td>
        <div class="signature-block">
          <div class="signature-title">তারিখসহ মনোনীত ব্যক্তির/অভিভাবকের স্বাক্ষর অথবা টিপসই</div>
        </div>
      </td>
      <td>
        <div class="signature-block right">
          <div class="signature-title">মনোনয়নকারী শ্রমিকের</div>
          <div class="signature-line">স্বাক্ষর/টিপসই........................</div>
          <div class="signature-line">তারিখ............................</div>
        </div>

        <div class="signature-block right" style="margin-top: 42px;">
          <div class="signature-title">ম্যানেজার বা ক্ষমতাপ্রাপ্ত কর্মকর্তার</div>
          <div class="signature-line">স্বাক্ষর........................</div>
          <div class="signature-line">তারিখ............................</div>
        </div>
      </td>
    </tr>
  </table>
</div>
@endif
