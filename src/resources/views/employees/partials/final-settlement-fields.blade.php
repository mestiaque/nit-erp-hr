@php($other = json_decode($employee->other_information ?? '{}', true))
@php($settlement = data_get($other, 'final_settlement', []))
<div class="row">
    <div class="col-md-12 mb-2"><label class="mb-1">Absent Date</label><input type="date" name="absent_date" value="{{ old('absent_date', data_get($settlement, 'absent_date')) }}" class="form-control form-control-sm"></div>
    <div class="col-md-12 mb-2"><label class="mb-1">1st Letter Date</label><input type="date" name="letter_1_date" value="{{ old('letter_1_date', data_get($settlement, 'letter_1_date')) }}" class="form-control form-control-sm"></div>
    <div class="col-md-12 mb-2"><label class="mb-1">2nd Letter Date</label><input type="date" name="letter_2_date" value="{{ old('letter_2_date', data_get($settlement, 'letter_2_date')) }}" class="form-control form-control-sm"></div>
    <div class="col-md-12 mb-2"><label class="mb-1">3rd Letter Date</label><input type="date" name="letter_3_date" value="{{ old('letter_3_date', data_get($settlement, 'letter_3_date')) }}" class="form-control form-control-sm"></div>
    <div class="col-12"><small class="text-muted">Print letters can be generated from report module.</small></div>
</div>