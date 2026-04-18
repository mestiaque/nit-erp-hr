@extends('admin.layouts.app')

@section('title')
<title>{{ $reportTitle }}</title>
@endsection

@section('contents')
<div class="flex-grow-1 p-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">{{ $reportTitle }}</h4>
            <a href="{{ route('hr-center.reports.index') }}" class="btn btn-light btn-sm">Back</a>
        </div>
        <div class="card-body">
            <form method="get" action="{{ route('hr-center.reports.show', $reportKey) }}">
                <div class="row">

                    <div class="col-md-4 mb-3">
                        <label class="mb-1">Employee ID(s) <small class="text-muted">(use , for multiple)</small></label>
                        <input type="text" name="employee_ids" class="form-control form-control-sm"
                               value="{{ $request->employee_ids }}" placeholder="B00144,B00145">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="mb-1">From</label>
                        <input type="date" name="from" class="form-control form-control-sm" value="{{ $request->from }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="mb-1">To</label>
                        <input type="date" name="to" class="form-control form-control-sm" value="{{ $request->to }}">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="mb-1">Classification</label>
                        <select name="classification" class="form-control form-control-sm">
                            <option value="">All</option>
                            @foreach($options['classifications'] as $item)
                                <option value="{{ $item->id }}" @selected((string)$request->classification === (string)$item->id)>{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="mb-1">Department</label>
                        <select name="department" class="form-control form-control-sm">
                            <option value="">All</option>
                            @foreach($options['departments'] as $item)
                                <option value="{{ $item->id }}" @selected((string)$request->department === (string)$item->id)>{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="mb-1">Section</label>
                        <select name="section" class="form-control form-control-sm">
                            <option value="">All</option>
                            @foreach($options['sections'] as $item)
                                <option value="{{ $item->id }}" @selected((string)$request->section === (string)$item->id)>{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="mb-1">Sub-Section</label>
                        <select name="sub_section" class="form-control form-control-sm">
                            <option value="">All</option>
                            @foreach($options['subSections'] as $item)
                                <option value="{{ $item->id }}" @selected((string)$request->sub_section === (string)$item->id)>{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="mb-1">Shift</label>
                        <select name="shift" class="form-control form-control-sm">
                            <option value="">All</option>
                            @foreach($options['shifts'] as $item)
                                <option value="{{ $item->id }}" @selected((string)$request->shift === (string)$item->id)>{{ $item->name_of_shift }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="mb-1">Working Place</label>
                        <select name="working_place" class="form-control form-control-sm">
                            <option value="">All</option>
                            @foreach($options['workingPlaces'] as $item)
                                <option value="{{ $item->id }}" @selected((string)$request->working_place === (string)$item->id)>{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="mb-1">Block / Line</label>
                        <select name="line_number" class="form-control form-control-sm">
                            <option value="">All</option>
                            @foreach($options['lines'] as $item)
                                <option value="{{ $item->id }}" @selected((string)$request->line_number === (string)$item->id)>{{ $item->name }}{{ $item->slug ? ' - '.$item->slug : '' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="mb-1">Salary Type</label>
                        <select name="salary_type" class="form-control form-control-sm">
                            <option value="">All</option>
                            <option value="fixed_rate" @selected($request->salary_type === 'fixed_rate')>Fixed Rate</option>
                            <option value="price_rate" @selected($request->salary_type === 'price_rate')>Price Rate</option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="mb-1">Employee Status</label>
                        <select name="employee_status" class="form-control form-control-sm">
                            <option value="">All</option>
                            <option value="regular" @selected($request->employee_status === 'regular')>Regular</option>
                            <option value="lefty" @selected($request->employee_status === 'lefty')>Lefty</option>
                            <option value="resign" @selected($request->employee_status === 'resign')>Resign</option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="mb-1">Language</label>
                        <select name="language" class="form-control form-control-sm">
                            <option value="en" @selected(($request->language ?? 'en') === 'en')>English</option>
                            <option value="bn" @selected($request->language === 'bn')>বাংলা</option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="mb-1">Report Type</label>
                        <select name="report_type" id="jobCardReportType" class="form-control form-control-sm">
                            @foreach($reportTypes as $key => $label)
                                <option value="{{ $key }}" @selected(($request->report_type ?? 'job-card') === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Lock switch (visible only for lock report types) --}}
                    <div class="col-md-3 mb-3" id="lockSwitchWrap" style="display:none;">
                        <label class="mb-1">Lock Apply</label>
                        <div class="d-flex align-items-center mt-1">
                            <div class="form-check form-switch me-3 mb-0">
                                <input class="form-check-input" type="checkbox" id="lockSwitch" name="apply_lock" value="1"
                                    @checked($request->boolean('apply_lock'))>
                                <label class="form-check-label" for="lockSwitch">Enable Lock</label>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 mb-3">
                        <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
                        <a href="{{ route('hr-center.reports.show', $reportKey) }}" class="btn btn-light btn-sm">Reset</a>
                        <button type="submit" name="print" value="1" formtarget="_blank" class="btn btn-primary btn-sm">
                            Report
                        </button>
                        {{-- Lock Apply button (posts to lock endpoint) --}}
                        <button type="button" id="lockApplyBtn" class="btn btn-warning btn-sm" style="display:none;">
                            Apply Lock
                        </button>
                    </div>

                </div>
            </form>

            {{-- Hidden lock form --}}
            <form id="lockForm" method="post" action="{{ route('hr-center.reports.job-card-report.lock') }}" style="display:none;">
                @csrf
                <div id="lockFormInputs"></div>
            </form>

        </div>
    </div>
</div>
@endsection

@push('js')
<script>
(function () {
    const typeEl  = document.getElementById('jobCardReportType');
    const lockWrap = document.getElementById('lockSwitchWrap');
    const lockApplyBtn = document.getElementById('lockApplyBtn');
    const lockSwitch = document.getElementById('lockSwitch');

    function isLockType(val) {
        return val === 'job-card-lock' || val === 'job-card-summary-lock';
    }

    function syncLockUI() {
        const show = typeEl && isLockType(typeEl.value);
        if (lockWrap) lockWrap.style.display = show ? 'block' : 'none';
        if (lockApplyBtn) lockApplyBtn.style.display = (show && lockSwitch && lockSwitch.checked) ? 'inline-block' : 'none';
    }

    if (typeEl) typeEl.addEventListener('change', syncLockUI);
    if (lockSwitch) lockSwitch.addEventListener('change', syncLockUI);
    syncLockUI();

    if (lockApplyBtn) {
        lockApplyBtn.addEventListener('click', function () {
            const mainForm = document.querySelector('form[method="get"]');
            const lockInputs = document.getElementById('lockFormInputs');
            lockInputs.innerHTML = '';
            const data = new FormData(mainForm);
            data.forEach(function (val, key) {
                const inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = key;
                inp.value = val;
                lockInputs.appendChild(inp);
            });
            if (confirm('Apply lock for selected employees and date range?')) {
                document.getElementById('lockForm').submit();
            }
        });
    }
})();
</script>
@endpush
