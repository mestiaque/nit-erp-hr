@extends('printMaster2')

@section('title', 'Employee Report - Details')

@section('contents')
<div class="report-head text-center">
    <h3>{{ general()->title ?? 'Company Name' }}</h3>
    <div>{{ general()->address_one ?? data_get(general(), 'address') }}</div>
    <strong>Employee Report - Details</strong>
</div>
<div class="meta-line">
    <strong>Print Date:</strong> {{ now()->format('d-m-Y h:i A') }}
    <span style="margin-left: 18px;"><strong>Total Employee:</strong> {{ $detailsRows->count() }}</span>
</div>
<table class="report-table">
    <thead>
        <tr>
            <th>S.L</th>
            <th>Working Place</th>
            <th>Emp. ID</th>
            <th>Name</th>
            <th>Join Date</th>
            <th>Job Age</th>
            <th>DOB</th>
            <th>Age</th>
            <th>Sex</th>
            <th>Department</th>
            <th>Section</th>
            <th>Sub Section</th>
            <th>Designation</th>
            <th>Contact No.</th>
            <th>Grade</th>
            <th>Classification</th>
            <th>Line/Block</th>
            <th>Shift</th>
            <th>WeekEnd</th>
            <th>Gross Salary</th>
        </tr>
    </thead>
    <tbody>
        @foreach($detailsRows as $row)
            <tr>
                <td>{{ $row['sl'] }}</td>
                <td>{{ $row['working_place'] }}</td>
                <td>{{ $row['employee_id'] }}</td>
                <td>{{ $row['name'] }}</td>
                <td>{{ $row['join_date'] }}</td>
                <td>{{ $row['job_age'] ?? '' }}</td>
                <td>{{ $row['dob'] ?? '' }}</td>
                <td>{{ $row['age'] ?? '' }}</td>
                <td>{{ $row['sex'] ?? '' }}</td>
                <td>{{ $row['department'] }}</td>
                <td>{{ $row['section'] }}</td>
                <td>{{ $row['sub_section'] }}</td>
                <td>{{ $row['designation'] }}</td>
                <td>{{ $row['contact_no'] ?? '' }}</td>
                <td>{{ $row['grade'] }}</td>
                <td>{{ $row['classification'] }}</td>
                <td>{{ $row['line_block'] }}</td>
                <td>{{ $row['shift'] }}</td>
                <td>{{ $row['weekend'] }}</td>
                <td>{{ $row['gross_salary'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
