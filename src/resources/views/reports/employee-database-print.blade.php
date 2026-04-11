@extends('printMaster2')

@section('title', 'Employee Report - Database')

@section('contents')
<div class="report-head text-center">
    <h3>{{ general()->title ?? 'Company Name' }}</h3>
    <div>{{ general()->address_one ?? data_get(general(), 'address') }}</div>
    <strong>Employee Report - Database</strong>
</div>
<div class="meta-line">
    <strong>Print Date:</strong> {{ now()->format('d-m-Y h:i A') }}
    <span style="margin-left: 18px;"><strong>Total Employee:</strong> {{ $employees->count() }}</span>
</div>
<table class="report-table">
    <thead>
        <tr>
            <th colspan="4">Employee Profile</th>
            <th colspan="11">Salary Info.</th>
            <th colspan="22">Basic Info.</th>
            <th colspan="8">Reference</th>
            <th colspan="4">Permanent Address</th>
            <th colspan="4">Present Address</th>
            <th colspan="8">Nominee Info.</th>
        </tr>
        <tr>
            <th>S.L</th>
            <th>Working Place</th>
            <th>Name</th>
            <th>Emp.ID</th>
            <th>Join Date</th>
            <th>Classification</th>
            <th>Department</th>
            <th>Section</th>
            <th>Sub Section</th>
            <th>Line/ Block</th>
            <th>Designation</th>
            <th>Grade</th>
            <th>Shift</th>
            <th>WeekEnd</th>
            <th>Personal Contact No.</th>
            <th>Emergency Contact No.</th>
            <th>Gross Salary</th>
            <th>Pay Mode</th>
            <th>Bank/Mobile No.</th>
            <th>Car & Fuel</th>
            <th>Phone & Internet</th>
            <th>Extra Facility</th>
            <th>Tax</th>
            <th>Fathers Name</th>
            <th>Mothers Name</th>
            <th>Marital Status</th>
            <th>Spouse Name</th>
            <th>Sex</th>
            <th>Kids</th>
            <th>Religion</th>
            <th>DOB</th>
            <th>Blood Group</th>
            <th>Nationality</th>
            <th>NID No.</th>
            <th>Birth Reg. No.</th>
            <th>Passport No.</th>
            <th>Driving License No.</th>
            <th>Special Ident. Sign</th>
            <th>Edu. Exp.</th>
            <th>Job Exp.</th>
            <th>Previous Org.</th>
            <th>Reference Name</th>
            <th>Designation</th>
            <th>Card No.</th>
            <th>Mobile No.</th>
            <th>District</th>
            <th>Po. Station</th>
            <th>Post Office</th>
            <th>Village</th>
            <th>District</th>
            <th>Po. Station</th>
            <th>Post Office</th>
            <th>Village</th>
            <th>Nominee Name</th>
            <th>Po. Station</th>
            <th>Post Office</th>
            <th>Village</th>
            <th>NID No.</th>
            <th>Mobile No.</th>
            <th>Relation</th>
            <th>Age</th>
        </tr>
    </thead>
    <tbody>
        <!-- Data rows should be updated to match these columns -->
        @foreach($employees as $i => $employee)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $employee->working_place ?? 'N/A' }}</td>
                <td>{{ $employee->name }}</td>
                <td>{{ $employee->employee_id }}</td>
                <td>{{ optional($employee->joining_date)?->format('d-m-Y') }}</td>
                <td>{{ $employee->classification ?? 'N/A' }}</td>
                <td>{{ optional($employee->department)->name }}</td>
                <td>{{ $employee->section ?? 'N/A' }}</td>
                <td>{{ $employee->sub_section ?? 'N/A' }}</td>
                <td>{{ $employee->line_block ?? 'N/A' }}</td>
                <td>{{ optional($employee->designation)->name }}</td>
                <td>{{ $employee->grade ?? 'N/A' }}</td>
                <td>{{ $employee->shift ?? 'N/A' }}</td>
                <td>{{ $employee->weekend ?? 'N/A' }}</td>
                <td>{{ $employee->personal_contact_no ?? 'N/A' }}</td>
                <td>{{ $employee->emergency_contact_no ?? 'N/A' }}</td>
                <td>{{ $employee->gross_salary ?? 'N/A' }}</td>
                <td>{{ $employee->pay_mode ?? 'N/A' }}</td>
                <td>{{ $employee->bank_mobile_no ?? 'N/A' }}</td>
                <td>{{ $employee->car_fuel ?? 'N/A' }}</td>
                <td>{{ $employee->phone_internet ?? 'N/A' }}</td>
                <td>{{ $employee->extra_facility ?? 'N/A' }}</td>
                <td>{{ $employee->tax ?? 'N/A' }}</td>
                <td>{{ $employee->father_name ?? 'N/A' }}</td>
                <td>{{ $employee->mother_name ?? 'N/A' }}</td>
                <td>{{ $employee->marital_status ?? 'N/A' }}</td>
                <td>{{ $employee->spouse_name ?? 'N/A' }}</td>
                <td>{{ $employee->sex ?? 'N/A' }}</td>
                <td>{{ $employee->kids ?? 'N/A' }}</td>
                <td>{{ $employee->religion ?? 'N/A' }}</td>
                <td>{{ $employee->dob ?? 'N/A' }}</td>
                <td>{{ $employee->blood_group ?? 'N/A' }}</td>
                <td>{{ $employee->nationality ?? 'N/A' }}</td>
                <td>{{ $employee->nid_no ?? 'N/A' }}</td>
                <td>{{ $employee->birth_reg_no ?? 'N/A' }}</td>
                <td>{{ $employee->passport_no ?? 'N/A' }}</td>
                <td>{{ $employee->driving_license_no ?? 'N/A' }}</td>
                <td>{{ $employee->special_ident_sign ?? 'N/A' }}</td>
                <td>{{ $employee->edu_exp ?? 'N/A' }}</td>
                <td>{{ $employee->job_exp ?? 'N/A' }}</td>
                <td>{{ $employee->previous_org ?? 'N/A' }}</td>
                <td>{{ $employee->reference_name ?? 'N/A' }}</td>
                <td>{{ $employee->reference_designation ?? 'N/A' }}</td>
                <td>{{ $employee->reference_card_no ?? 'N/A' }}</td>
                <td>{{ $employee->reference_mobile_no ?? 'N/A' }}</td>
                <td>{{ $employee->permanent_district ?? 'N/A' }}</td>
                <td>{{ $employee->permanent_po_station ?? 'N/A' }}</td>
                <td>{{ $employee->permanent_post_office ?? 'N/A' }}</td>
                <td>{{ $employee->permanent_village ?? 'N/A' }}</td>
                <td>{{ $employee->present_district ?? 'N/A' }}</td>
                <td>{{ $employee->present_po_station ?? 'N/A' }}</td>
                <td>{{ $employee->present_post_office ?? 'N/A' }}</td>
                <td>{{ $employee->present_village ?? 'N/A' }}</td>
                <td>{{ $employee->nominee_name ?? 'N/A' }}</td>
                <td>{{ $employee->nominee_po_station ?? 'N/A' }}</td>
                <td>{{ $employee->nominee_post_office ?? 'N/A' }}</td>
                <td>{{ $employee->nominee_village ?? 'N/A' }}</td>
                <td>{{ $employee->nominee_nid_no ?? 'N/A' }}</td>
                <td>{{ $employee->nominee_mobile_no ?? 'N/A' }}</td>
                <td>{{ $employee->nominee_relation ?? 'N/A' }}</td>
                <td>{{ $employee->nominee_age ?? 'N/A' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
