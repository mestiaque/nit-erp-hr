@php
    $companyName = general()->title ?? 'Company Name';
    $companyAddress = general()->address_one ?? data_get(general(), 'address');
    $issueDate = now()->format('d/m/Y');
    $joinDate = $fmtDate($employee->joining_date);

    $designation = optional($employee->designation)->name
        ?? data_get($employee, 'designation_name')
        ?? data_get(optional(\App\Models\Attribute::where('type', 2)->find($employee->designation_id)), 'name')
        ?? 'N/A';

    $department = optional($employee->department)->name
        ?? data_get($employee, 'department_name')
        ?? data_get(optional(\App\Models\Attribute::where('type', 3)->find($employee->department_id)), 'name')
        ?? 'N/A';

    $section = data_get(optional(\App\Models\Attribute::where('type', 29)->find($employee->section_id)), 'name')
        ?? data_get($employee, 'section')
        ?? 'N/A';

    $employeeTypeValue = data_get($employee, 'employee_type');
    if (is_numeric($employeeTypeValue)) {
        $classification = data_get(optional(\App\Models\Attribute::where('type', 16)->find((int) $employeeTypeValue)), 'name', 'N/A');
    } else {
        $classification = !blank($employeeTypeValue) ? (string) $employeeTypeValue : 'N/A';
    }

    $bloodGroup = data_get($employee, 'blood_group', 'N/A');
    $emergency = data_get($employee, 'emergency_mobile', data_get($employee, 'emergency_contact_no', data_get($employee, 'mobile', 'N/A')));
    $idNumber = data_get($employee, 'employee_id', data_get($employee, 'id', 'N/A'));
    $permanentAddress = data_get($employee, 'permanent_address', data_get($employee, 'address', 'N/A'));
@endphp

<div class="id-card-sheet">
    <div class="id-card-side id-card-front">
        <div class="id-card-logo-wrap">
            @if(!blank(general()->logo()))
                <img src="{{ asset(general()->logo()) }}" alt="Company Logo" class="id-card-logo">
            @endif
        </div>
        <h4 class="id-card-company">{{ $companyName }}</h4>
        <p class="id-card-address">{{ $companyAddress }}</p>

        <div class="id-card-strip">ID CARD</div>

        <div class="id-card-photo-wrap">
            <img src="{{ asset($employee->image()) }}" alt="Employee Photo" class="id-card-photo">
        </div>

        <table class="id-card-info">
            <tr><td>Name</td><td>: {{ $employee->name ?? 'N/A' }}</td></tr>
            <tr><td>Designation</td><td>: {{ $designation }}</td></tr>
            <tr><td>ID No.</td><td>: {{ $idNumber }}</td></tr>
            <tr><td>Dept.</td><td>: {{ $department }}</td></tr>
            <tr><td>Section</td><td>: {{ $section }}</td></tr>
            <tr><td>Join Date</td><td>: {{ $joinDate }}</td></tr>
            <tr><td>Classification</td><td>: {{ $classification }}</td></tr>
            <tr><td>Issue Date</td><td>: {{ $issueDate }}</td></tr>
        </table>

        <div class="id-sign-row">
            <div>
                <div class="id-sign-line"></div>
                <div class="id-sign-label">Staff Signature</div>
            </div>
            <div>
                <div class="id-sign-line"></div>
                <div class="id-sign-label">Authority Signature</div>
            </div>
        </div>
    </div>

    <div class="id-card-side id-card-back">
        <div class="id-card-logo-wrap">
            @if(!blank(general()->logo()))
                <img src="{{ asset(general()->logo()) }}" alt="Company Logo" class="id-card-logo">
            @endif
        </div>

        <p class="id-back-head">Blood Group : <strong>{{ $bloodGroup }}</strong></p>
        <p class="id-back-head">Permanent Address</p>
        <p class="id-back-text">{{ $permanentAddress }}</p>

        <p class="id-back-head" style="margin-top: 10px;">Emergency Contact No.:</p>
        <p class="id-back-text"><strong>{{ $emergency }}</strong></p>

        <p class="id-back-text" style="margin-top: 8px;">
            Please return to the following address or nearest office station.
        </p>
        <p class="id-back-company"><strong>{{ $companyName }}</strong></p>
        <p class="id-back-text">{{ $companyAddress }}</p>
        <p class="id-back-text">Contact No.: 0</p>

        <div class="id-card-strip id-card-strip-bottom">Exp. Date: Up to the last date of job.</div>
    </div>
</div>
