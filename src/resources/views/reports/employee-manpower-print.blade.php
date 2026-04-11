@extends('printMaster2')

@section('title', 'Employee Report - Manpower Summary')

@section('contents')
<div class="report-head text-center">
    <h3>{{ general()->title ?? 'Company Name' }}</h3>
    <div>{{ general()->address_one ?? data_get(general(), 'address') }}</div>
    <strong>Employee Report - Manpower Summary</strong>
</div>
<div class="meta-line">
    <strong>Print Date:</strong> {{ now()->format('d-m-Y h:i A') }}
    <span style="margin-left: 18px;"><strong>Total Employee:</strong> {{ $manpowerRows->sum('recruited') }}</span>
</div>
<table class="report-table">
    <thead>
        <tr>
            <th>SL</th>
            <th>Department</th>
            <th>Section</th>
            <th>Sub Section</th>
            <th>Designation</th>
            <th>Approve Manpower</th>
            <th>Recruited</th>
            <th>Deviation</th>
            <th>Total Gross Salary(TK)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($manpowerRows as $row)
            <tr class="{{ $row['row_type'] === 'grand_total' ? 'grandtotal-row' : ($row['row_type'] === 'total' ? 'subtotal-row' : '') }}">
                <td class="text-center">{{ $row['sl'] }}</td>
                <td>{{ $row['department'] }}</td>
                <td>{{ $row['section'] }}</td>
                <td>{{ $row['sub_section'] }}</td>
                <td>{{ $row['designation'] }}</td>
                <td class="text-center">{{ $row['approve_manpower'] }}</td>
                <td class="text-center">{{ $row['recruited'] }}</td>
                <td class="text-center">{{ $row['deviation'] }}</td>
                <td class="text-right">{{ number_format($row['total_gross_salary'], 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
