@php
    $normalize = static fn ($value) => strtolower(trim((string) $value));
    $buildOptions = static function (array $defaults, $current) use ($normalize): array {
        $seen = [];
        $options = [];

        foreach (array_merge([(string) $current], $defaults) as $value) {
            $value = trim((string) $value);
            if ($value === '') {
                continue;
            }

            $key = $normalize($value);
            if (isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;
            $options[] = $value;
        }

        return $options;
    };

    $selectedMaritalStatus = $normalize($employee->marital_status);
    $selectedGender = $normalize($employee->gender);
    $selectedReligion = $normalize($employee->religion);

    $maritalStatusOptions = $buildOptions((array) data_get($basicInfoOptions ?? [], 'marital_status', []), $employee->marital_status);
    $genderOptions = $buildOptions((array) data_get($basicInfoOptions ?? [], 'gender', []), $employee->gender);
    $religionOptions = $buildOptions((array) data_get($basicInfoOptions ?? [], 'religion', []), $employee->religion);

    $selectedBloodGroup = strtoupper(trim((string) old('blood_group', $employee->blood_group)));
    $selectedPaymentMode = strtolower(trim((string) old('salary_type', $employee->salary_type)));
    $profileInfo = json_decode($employee->other_information ?? '{}', true);
    $profileInfo = is_array($profileInfo) ? data_get($profileInfo, 'profile', []) : [];
@endphp

<div class="row">
    <div class="col-md-4 mb-2"><label class="mb-1">Father's Name</label><input type="text" name="father_name" value="{{ old('father_name', $employee->father_name) }}" class="form-control form-control-sm"></div>
    <div class="col-md-4 mb-2"><label class="mb-1">Father's Name (Bangla)</label><input type="text" name="father_name_bn" value="{{ old('father_name_bn', $employee->father_name_bn) }}" class="form-control form-control-sm"></div>
    <div class="col-md-4 mb-2"><label class="mb-1">Mother's Name</label><input type="text" name="mother_name" value="{{ old('mother_name', $employee->mother_name) }}" class="form-control form-control-sm"></div>
    <div class="col-md-4 mb-2"><label class="mb-1">Mother's Name (Bangla)</label><input type="text" name="mother_name_bn" value="{{ old('mother_name_bn', $employee->mother_name_bn) }}" class="form-control form-control-sm"></div>
    <div class="col-md-4 mb-2">
        <label class="mb-1">Marital Status</label>
        <select name="marital_status" class="form-control form-control-sm">
            <option value="">Select</option>
            @foreach($maritalStatusOptions as $option)
                <option value="{{ $option }}" @selected($selectedMaritalStatus === $normalize($option))>{{ $option }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4 mb-2"><label class="mb-1">Marital Status (Bangla)</label><input type="text" name="marital_status_bn" value="{{ old('marital_status_bn', data_get($profileInfo, 'marital_status_bn')) }}" class="form-control form-control-sm"></div>
    <div class="col-md-4 mb-2"><label class="mb-1">Spouse Name</label><input type="text" name="spouse_name" value="{{ old('spouse_name', $employee->spouse_name) }}" class="form-control form-control-sm"></div>
    <div class="col-md-4 mb-2"><label class="mb-1">Spouse Name (Bangla)</label><input type="text" name="spouse_name_bn" value="{{ old('spouse_name_bn', $employee->spouse_name_bn) }}" class="form-control form-control-sm"></div>
    <div class="col-md-4 mb-2">
        <label class="mb-1">Sex</label>
        <select name="gender" class="form-control form-control-sm">
            <option value="">Select</option>
            @foreach($genderOptions as $option)
                <option value="{{ $option }}" @selected($selectedGender === $normalize($option))>{{ $option }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4 mb-2"><label class="mb-1">Sex (Bangla)</label><input type="text" name="gender_bn" value="{{ old('gender_bn', data_get($profileInfo, 'gender_bn')) }}" class="form-control form-control-sm"></div>
    <div class="col-md-4 mb-2"><label class="mb-1">Child</label><input type="number" name="boys" value="{{ old('boys', $employee->boys) }}" class="form-control form-control-sm" min="0"></div>
    <div class="col-md-4 mb-2">
        <label class="mb-1">Payment Mode</label>
        <select name="salary_type" class="form-control form-control-sm">
            <option value="">Select</option>
            <option value="Cash" @selected($selectedPaymentMode === 'cash')>Cash</option>
            <option value="Bank" @selected($selectedPaymentMode === 'bank')>Bank</option>
            <option value="Mobile Banking" @selected($selectedPaymentMode === 'mobile banking')>Mobile Banking</option>
            <option value="Cheque" @selected($selectedPaymentMode === 'cheque')>Cheque</option>
        </select>
    </div>
    <div class="col-md-4 mb-2">
        <label class="mb-1">Religion</label>
        <select name="religion" class="form-control form-control-sm">
            <option value="">Select</option>
            @foreach($religionOptions as $option)
                <option value="{{ $option }}" @selected($selectedReligion === $normalize($option))>{{ $option }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4 mb-2"><label class="mb-1">Religion (Bangla)</label><input type="text" name="religion_bn" value="{{ old('religion_bn', data_get($profileInfo, 'religion_bn')) }}" class="form-control form-control-sm"></div>
    <div class="col-md-4 mb-2"><label class="mb-1">Birth Date</label><input type="date" name="dob" value="{{ old('dob', optional($employee->dob)->format('Y-m-d') ?? (is_string($employee->dob) ? $employee->dob : '')) }}" class="form-control form-control-sm"></div>
    <div class="col-md-4 mb-2">
        <label class="mb-1">Blood Group</label>
        <select name="blood_group" class="form-control form-control-sm">
            <option value="">Select</option>
            @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
                <option value="{{ $bg }}" @selected($selectedBloodGroup === $bg)>{{ $bg }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4 mb-2"><label class="mb-1">Nationality</label><input type="text" name="nationality" value="{{ old('nationality', $employee->nationality) }}" class="form-control form-control-sm"></div>
    <div class="col-md-4 mb-2"><label class="mb-1">Nationality (Bangla)</label><input type="text" name="nationality_bn" value="{{ old('nationality_bn', data_get($profileInfo, 'nationality_bn')) }}" class="form-control form-control-sm"></div>
    <div class="col-md-4 mb-2"><label class="mb-1">National ID No.</label><input type="text" name="nid_number" value="{{ old('nid_number', $employee->nid_number) }}" class="form-control form-control-sm"></div>
    <div class="col-md-4 mb-2"><label class="mb-1">National ID No. (Bangla)</label><input type="text" name="nid_number_bn" value="{{ old('nid_number_bn', data_get($profileInfo, 'nid_number_bn')) }}" class="form-control form-control-sm"></div>
    <div class="col-md-4 mb-2"><label class="mb-1">Birth Registration No.</label><input type="text" name="birth_registration" value="{{ old('birth_registration', $employee->birth_registration) }}" class="form-control form-control-sm"></div>
    <div class="col-md-4 mb-2"><label class="mb-1">Birth Registration No. (Bangla)</label><input type="text" name="birth_registration_bn" value="{{ old('birth_registration_bn', data_get($profileInfo, 'birth_registration_bn')) }}" class="form-control form-control-sm"></div>
    <div class="col-md-4 mb-2"><label class="mb-1">Passport No.</label><input type="text" name="passport_no" value="{{ old('passport_no', $employee->passport_no) }}" class="form-control form-control-sm"></div>
    <div class="col-md-4 mb-2"><label class="mb-1">Passport No. (Bangla)</label><input type="text" name="passport_no_bn" value="{{ old('passport_no_bn', data_get($profileInfo, 'passport_no_bn')) }}" class="form-control form-control-sm"></div>
    <div class="col-md-4 mb-2"><label class="mb-1">Driving License No.</label><input type="text" name="driving_license" value="{{ old('driving_license', $employee->driving_license) }}" class="form-control form-control-sm"></div>
    <div class="col-md-4 mb-2"><label class="mb-1">Driving License No. (Bangla)</label><input type="text" name="driving_license_bn" value="{{ old('driving_license_bn', data_get($profileInfo, 'driving_license_bn')) }}" class="form-control form-control-sm"></div>
    <div class="col-md-4 mb-2"><label class="mb-1">Special Identification Sign</label><input type="text" name="distinguished_mark" value="{{ old('distinguished_mark', $employee->distinguished_mark) }}" class="form-control form-control-sm"></div>
    <div class="col-md-4 mb-2"><label class="mb-1">Special Identification Sign (Bangla)</label><input type="text" name="distinguished_mark_bn" value="{{ old('distinguished_mark_bn', data_get($profileInfo, 'distinguished_mark_bn')) }}" class="form-control form-control-sm"></div>
    <div class="col-md-4 mb-2"><label class="mb-1">Educational Experience</label><input type="text" name="education" value="{{ old('education', $employee->education) }}" class="form-control form-control-sm"></div>
    <div class="col-md-4 mb-2"><label class="mb-1">Educational Experience (Bangla)</label><input type="text" name="education_bn" value="{{ old('education_bn', data_get($profileInfo, 'education_bn')) }}" class="form-control form-control-sm"></div>
    <div class="col-md-4 mb-2"><label class="mb-1">Job Experience</label><input type="text" name="job_experience" value="{{ old('job_experience', data_get($profileInfo, 'job_experience')) }}" class="form-control form-control-sm"></div>
    <div class="col-md-4 mb-2"><label class="mb-1">Job Experience (Bangla)</label><input type="text" name="job_experience_bn" value="{{ old('job_experience_bn', data_get($profileInfo, 'job_experience_bn')) }}" class="form-control form-control-sm"></div>
    <div class="col-md-4 mb-2"><label class="mb-1">Previous Organization</label><input type="text" name="prev_organization" value="{{ old('prev_organization', data_get($profileInfo, 'prev_organization')) }}" class="form-control form-control-sm"></div>
    <div class="col-md-4 mb-2"><label class="mb-1">Previous Organization (Bangla)</label><input type="text" name="prev_organization_bn" value="{{ old('prev_organization_bn', data_get($profileInfo, 'prev_organization_bn')) }}" class="form-control form-control-sm"></div>
    <div class="col-md-4 mb-2"><label class="mb-1">Reference Name</label><input type="text" name="reference_1" value="{{ old('reference_1', $employee->reference_1) }}" class="form-control form-control-sm"></div>
    <div class="col-md-4 mb-2"><label class="mb-1">Reference Name (Bangla)</label><input type="text" name="reference_1_bn" value="{{ old('reference_1_bn', data_get($profileInfo, 'reference_1_bn')) }}" class="form-control form-control-sm"></div>
    <div class="col-md-4 mb-2"><label class="mb-1">Reference Designation</label><input type="text" name="reference_2" value="{{ old('reference_2', $employee->reference_2) }}" class="form-control form-control-sm"></div>
    <div class="col-md-4 mb-2"><label class="mb-1">Reference Designation (Bangla)</label><input type="text" name="reference_2_bn" value="{{ old('reference_2_bn', data_get($profileInfo, 'reference_2_bn')) }}" class="form-control form-control-sm"></div>
    <div class="col-md-4 mb-2"><label class="mb-1">Reference Card No.</label><input type="text" name="reference_card_no" value="{{ old('reference_card_no', data_get($profileInfo, 'reference_card_no')) }}" class="form-control form-control-sm"></div>
    <div class="col-md-4 mb-2"><label class="mb-1">Reference Card No. (Bangla)</label><input type="text" name="reference_card_no_bn" value="{{ old('reference_card_no_bn', data_get($profileInfo, 'reference_card_no_bn')) }}" class="form-control form-control-sm"></div>
    <div class="col-md-4 mb-2"><label class="mb-1">Reference Mobile No.</label><input type="text" name="reference_mobile" value="{{ old('reference_mobile', data_get($profileInfo, 'reference_mobile')) }}" class="form-control form-control-sm"></div>
    <div class="col-md-4 mb-2"><label class="mb-1">Reference Mobile No. (Bangla)</label><input type="text" name="reference_mobile_bn" value="{{ old('reference_mobile_bn', data_get($profileInfo, 'reference_mobile_bn')) }}" class="form-control form-control-sm"></div>
</div>
