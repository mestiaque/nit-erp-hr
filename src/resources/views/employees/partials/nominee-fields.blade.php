@php($other = json_decode($employee->other_information ?? '{}', true))
@php($nomineeInfo = data_get($other, 'nominee_info', []))
@php($nomineeImage = data_get($nomineeInfo, 'nominee_image'))
@php($districtNames = collect($options['districts'] ?? [])->pluck('name')->map(fn ($name) => (string) $name)->all())
@php($thanaNames = collect($options['thanas'] ?? [])->pluck('name')->map(fn ($name) => (string) $name)->all())
@php($countryNames = collect($options['countries'] ?? [])->pluck('name')->map(fn ($name) => (string) $name)->all())
@php($nomineeDistrict = old('nominee_district', data_get($nomineeInfo, 'nominee_district')))
@php($nomineePoStation = old('nominee_po_station', data_get($nomineeInfo, 'nominee_po_station')))
@php($nomineePostOffice = old('nominee_post_office', data_get($nomineeInfo, 'nominee_post_office')))
@php($nomineeNationality = old('nominee_nationality', data_get($nomineeInfo, 'nominee_nationality')))
<div class="row">
    <div class="col-md-6 mb-2">
        <label class="mb-1">Nominee Photo</label>
        <input type="file" name="nominee_image" accept="image/*" class="form-control form-control-sm" onchange="previewNomineeImage_{{ $employee->id ?? 'new' }}(this)">
        <small class="text-muted d-block mt-1">jpg, jpeg, png, gif, webp (max 2MB)</small>
        <img id="nominee_preview_{{ $employee->id ?? 'new' }}" src="{{ $nomineeImage ? asset($nomineeImage) : '' }}" alt="Nominee Photo" style="width:50px;height:50px;object-fit:cover;border:1px solid #ddd;margin-top:6px;{{ $nomineeImage ? '' : 'display:none;' }}">
    </div>
    <div class="col-md-6 mb-2"><label class="mb-1">Nominee Name</label><input type="text" name="nominee" value="{{ old('nominee', $employee->nominee) }}" class="form-control form-control-sm"></div>
    <div class="col-md-6 mb-2"><label class="mb-1">District</label><select name="nominee_district" class="form-control form-control-sm"><option value="">Select</option>@foreach(($options['districts'] ?? []) as $row)<option value="{{ $row->name }}" @selected((string) $nomineeDistrict === (string) $row->name)>{{ $row->name }}</option>@endforeach @if(!empty($nomineeDistrict) && !in_array((string) $nomineeDistrict, $districtNames, true))<option value="{{ $nomineeDistrict }}" selected>{{ $nomineeDistrict }}</option>@endif</select></div>
    <div class="col-md-6 mb-2"><label class="mb-1">Po. Station</label><select name="nominee_po_station" class="form-control form-control-sm"><option value="">Select</option>@foreach(($options['thanas'] ?? []) as $row)<option value="{{ $row->name }}" @selected((string) $nomineePoStation === (string) $row->name)>{{ $row->name }}</option>@endforeach @if(!empty($nomineePoStation) && !in_array((string) $nomineePoStation, $thanaNames, true))<option value="{{ $nomineePoStation }}" selected>{{ $nomineePoStation }}</option>@endif</select></div>
    <div class="col-md-6 mb-2"><label class="mb-1">Post Office</label><select name="nominee_post_office" class="form-control form-control-sm"><option value="">Select</option>@foreach(($options['thanas'] ?? []) as $row)<option value="{{ $row->name }}" @selected((string) $nomineePostOffice === (string) $row->name)>{{ $row->name }}</option>@endforeach @if(!empty($nomineePostOffice) && !in_array((string) $nomineePostOffice, $thanaNames, true))<option value="{{ $nomineePostOffice }}" selected>{{ $nomineePostOffice }}</option>@endif</select></div>
    <div class="col-md-6 mb-2"><label class="mb-1">Country/Nationality</label><select name="nominee_nationality" class="form-control form-control-sm"><option value="">Select</option>@foreach(($options['countries'] ?? []) as $row)<option value="{{ $row->name }}" @selected((string) $nomineeNationality === (string) $row->name)>{{ $row->name }}</option>@endforeach @if(!empty($nomineeNationality) && !in_array((string) $nomineeNationality, $countryNames, true))<option value="{{ $nomineeNationality }}" selected>{{ $nomineeNationality }}</option>@endif</select></div>
    <div class="col-md-6 mb-2"><label class="mb-1">Village</label><input type="text" name="nominee_village" value="{{ old('nominee_village', data_get($nomineeInfo, 'nominee_village')) }}" class="form-control form-control-sm"></div>
    <div class="col-md-6 mb-2"><label class="mb-1">NID No.</label><input type="text" name="nominee_nid" value="{{ old('nominee_nid', data_get($nomineeInfo, 'nominee_nid')) }}" class="form-control form-control-sm"></div>
    <div class="col-md-6 mb-2"><label class="mb-1">Mobile No.</label><input type="text" name="nominee_mobile" value="{{ old('nominee_mobile', data_get($nomineeInfo, 'nominee_mobile')) }}" class="form-control form-control-sm"></div>
    <div class="col-md-6 mb-2"><label class="mb-1">Relation</label><input type="text" name="nominee_relation" value="{{ old('nominee_relation', $employee->nominee_relation) }}" class="form-control form-control-sm"></div>
    <div class="col-md-6 mb-2"><label class="mb-1">Age</label><input type="number" name="nominee_age" value="{{ old('nominee_age', $employee->nominee_age) }}" class="form-control form-control-sm"></div>

    <div class="col-12 mt-2"><h6 class="mb-2">Payment Distribution (%)</h6></div>
    <div class="col-md-4 mb-2"><label class="mb-1">Net Payment</label><input type="number" step="0.01" name="distribution_net_payment" value="{{ old('distribution_net_payment', data_get($nomineeInfo, 'distribution_net_payment')) }}" class="form-control form-control-sm"></div>
    <div class="col-md-4 mb-2"><label class="mb-1">Provident Fund</label><input type="number" step="0.01" name="distribution_provident_fund" value="{{ old('distribution_provident_fund', data_get($nomineeInfo, 'distribution_provident_fund')) }}" class="form-control form-control-sm"></div>
    <div class="col-md-4 mb-2"><label class="mb-1">Insurance</label><input type="number" step="0.01" name="distribution_insurance" value="{{ old('distribution_insurance', data_get($nomineeInfo, 'distribution_insurance')) }}" class="form-control form-control-sm"></div>
    <div class="col-md-4 mb-2"><label class="mb-1">Accident Fine</label><input type="number" step="0.01" name="distribution_accident_fine" value="{{ old('distribution_accident_fine', data_get($nomineeInfo, 'distribution_accident_fine')) }}" class="form-control form-control-sm"></div>
    <div class="col-md-4 mb-2"><label class="mb-1">Profit</label><input type="number" step="0.01" name="distribution_profit" value="{{ old('distribution_profit', data_get($nomineeInfo, 'distribution_profit')) }}" class="form-control form-control-sm"></div>
    <div class="col-md-4 mb-2"><label class="mb-1">Others</label><input type="number" step="0.01" name="distribution_others" value="{{ old('distribution_others', data_get($nomineeInfo, 'distribution_others')) }}" class="form-control form-control-sm"></div>
</div>

<script>
function previewNomineeImage_{{ $employee->id ?? 'new' }}(input) {
    var preview = document.getElementById('nominee_preview_{{ $employee->id ?? 'new' }}');
    if (!preview || !input.files || !input.files[0]) {
        return;
    }
    preview.src = URL.createObjectURL(input.files[0]);
    preview.style.display = 'inline-block';
}
</script>