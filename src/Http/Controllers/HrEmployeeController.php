<?php

namespace ME\Hr\Http\Controllers;

use App\Models\Attribute;
use App\Models\Country;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use ME\Hr\Models\Designation;
use ME\Hr\Models\EmployeeIncrement;
use ME\Hr\Models\Leave;
use ME\Hr\Models\LeaveInfo;
use ME\Hr\Models\Location;
use ME\Hr\Models\PaymentMethod;
use ME\Hr\Models\Shift;
use ME\Hr\Models\SubSection;
use ME\Hr\Models\Weekday;
use ME\Hr\Models\WorkingPlace;

class HrEmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->filterByType('employee');

        if ($request->filled('emp_id')) {
            $query->where('employee_id', 'like', '%' . trim((string) $request->emp_id) . '%');
        }

        if ($request->filled('name_filter')) {
            $query->where('name', 'like', '%' . trim((string) $request->name_filter) . '%');
        }

        if ($request->filled('joining_date')) {
            $query->whereDate('joining_date', $request->joining_date);
        }

        if ($request->filled('contact')) {
            $query->where('mobile', 'like', '%' . trim((string) $request->contact) . '%');
        }

        if ($request->filled('classification_id')) {
            $this->applyIntegerFilter($query, 'employee_type', (int) $request->classification_id);
        }

        if ($request->filled('department_id')) {
            $this->applyIntegerFilter($query, 'department_id', (int) $request->department_id);
        }

        if ($request->filled('section_id')) {
            $this->applyIntegerFilter($query, 'section_id', (int) $request->section_id);
        }

        if ($request->filled('sub_section_id')) {
            $this->applyIntegerFilter($query, 'sub_section_id', (int) $request->sub_section_id, 'sub_section_id');
        }

        if ($request->filled('designation_id')) {
            $this->applyIntegerFilter($query, 'designation_id', (int) $request->designation_id);
        }

        if ($request->filled('shift_id')) {
            $this->applyIntegerFilter($query, 'shift_id', (int) $request->shift_id);
        }

        if ($request->filled('working_place_id')) {
            $this->applyIntegerFilter($query, 'working_place_id', (int) $request->working_place_id, 'working_place_id');
        }

        if ($request->filled('line_id')) {
            $this->applyIntegerFilter($query, 'line_number', (int) $request->line_id);
        }

        if ($request->filled('weekend')) {
            $this->applyStringFilter($query, 'weekend', (string) $request->weekend, 'weekend');
        }

        if ($request->filled('status')) {
            $employmentStatus = (string) $request->status;
            $query->where(function ($builder) use ($employmentStatus) {
                if ($employmentStatus === 'regular') {
                    $builder->whereNull('employment_status')
                        ->orWhere('employment_status', '')
                        ->orWhere('employment_status', 'regular');

                    return;
                }

                $builder->where('employment_status', $employmentStatus);
                if ($employmentStatus === 'lefty') {
                    $builder->orWhere('employment_status', 'left');
                }
                if ($employmentStatus === 'resign') {
                    $builder->orWhere('employment_status', 'resigned');
                }
            });
        }

        if ($request->filled('is_active')) {
            $query->where('status', $this->normalizeUserStatus((string) $request->is_active));
        }

        $employees = $query->latest()->paginate(20)->appends($request->query());

        $pluckDistinct = static function (string $column): array {
            return User::query()
                ->filterByType('employee')
                ->whereNotNull($column)
                ->pluck($column)
                ->map(static fn ($value) => trim((string) $value))
                ->filter(static fn ($value) => $value !== '')
                ->unique(static fn ($value) => strtolower($value))
                ->values()
                ->all();
        };

        $basicInfoOptions = [
            'marital_status' => $pluckDistinct('marital_status'),
            'gender' => $pluckDistinct('gender'),
            'religion' => $pluckDistinct('religion'),
        ];

        return view('hr::employees.index', [
            'employees' => $employees,
            'request' => $request,
            'options' => $this->options(),
            'basicInfoOptions' => $basicInfoOptions,
            'newEmployee' => new User(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $payload = $request->validate([
            'name' => 'required|string|max:191',
            'bn_name' => 'nullable|string|max:191',
            'employee_id' => 'required|string|max:100|unique:users,employee_id',
            'joining_date' => 'nullable|date',
            'employee_type' => 'nullable|integer',
            'department_id' => 'nullable|integer',
            'section_id' => 'nullable|integer',
            'sub_section_id' => 'nullable|integer',
            'designation_id' => 'nullable|integer',
            'working_place_id' => 'nullable|integer',
            'shift_id' => 'nullable|integer',
            'line_number' => 'nullable|integer',
            'weekend' => 'nullable|string|max:100',
            'mobile' => 'nullable|string|max:30',
            'emergency_mobile' => 'nullable|string|max:30',
            'is_active_01' => 'nullable|in:0,1',
            'is_active_02' => 'nullable|in:0,1',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'status' => 'required|in:active,inactive',
        ]);
        $payload['status'] = $this->normalizeUserStatus((string) $payload['status']);

        $employee = new User();
        $employee->fill($payload);
        $this->applyExtendedProfileFields($employee, $payload);
        $employee->addedby_id = Auth::id();
        $employee->password = bcrypt('123456');
        $employee->password_show = '123456';
        $employee->setTypes('employee');
        $employee->save();

        return redirect()->route('hr-center.employees.index')->with('success', 'Employee created successfully.');
    }

    public function updateProfile(Request $request, User $employee): RedirectResponse
    {
        $this->ensureEmployee($employee);

        $payload = $request->validate([
            'name' => 'required|string|max:191',
            'bn_name' => 'nullable|string|max:191',
            'employee_id' => 'required|string|max:100|unique:users,employee_id,' . $employee->id,
            'joining_date' => 'nullable|date',
            'employee_type' => 'nullable|integer',
            'department_id' => 'nullable|integer',
            'section_id' => 'nullable|integer',
            'sub_section_id' => 'nullable|integer',
            'designation_id' => 'nullable|integer',
            'working_place_id' => 'nullable|integer',
            'shift_id' => 'nullable|integer',
            'line_number' => 'nullable|integer',
            'weekend' => 'nullable|string|max:100',
            'mobile' => 'nullable|string|max:30',
            'emergency_mobile' => 'nullable|string|max:30',
            'is_active_01' => 'nullable|in:0,1',
            'is_active_02' => 'nullable|in:0,1',
            'status' => 'required|in:active,inactive',
        ]);
        $payload['status'] = $this->normalizeUserStatus((string) $payload['status']);

        $employee->fill($payload);
        $this->applyExtendedProfileFields($employee, $payload);
        $employee->setTypes('employee');
        $employee->save();

        if ($request->hasFile('profile_image')) {
            uploadFile($request->file('profile_image'), $employee->id, 6, 1, Auth::id());
        }

        return redirect()->route('hr-center.employees.index')->with('success', 'Employee profile updated.');
    }

    public function updateSalary(Request $request, User $employee): RedirectResponse
    {
        $this->ensureEmployee($employee);

        $payload = $request->validate([
            'gross_salary' => 'nullable|numeric|min:0',
            'gross_salary_comp_1' => 'nullable|numeric|min:0',
            'gross_salary_comp_2' => 'nullable|numeric|min:0',
            'salary_type' => 'nullable|string|max:50',
            'bank_or_phone' => 'nullable|string|max:191',
            'car_fuel' => 'nullable|numeric|min:0',
            'phone_internet' => 'nullable|numeric|min:0',
            'extra_facility' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'tax_calculate_by' => 'nullable|in:percent,amount',
            'salary_info_date' => 'nullable|date',
            'salary_info_status' => 'required|in:active,inactive',
        ]);

        $employee->gross_salary = $payload['gross_salary'] ?? null;
        $employee->salary_type = $payload['salary_type'] ?? null;

        $other = $this->otherInfo($employee);
        $other['salary_info'] = Arr::except($payload, ['gross_salary', 'salary_type']);
        $employee->other_information = json_encode($other);
        $employee->setTypes('employee');
        $employee->save();

        return redirect()->route('hr-center.employees.index')->with('success', 'Salary info updated.');
    }

    public function updateAddress(Request $request, User $employee): RedirectResponse
    {
        $this->ensureEmployee($employee);

        $payload = $request->validate([
            'permanent_district' => 'nullable|string|max:191',
            'permanent_upazila' => 'nullable|string|max:191',
            'permanent_post_office' => 'nullable|string|max:191',
            'permanent_village' => 'nullable|string|max:191',
            'present_district' => 'nullable|string|max:191',
            'present_upazila' => 'nullable|string|max:191',
            'present_post_office' => 'nullable|string|max:191',
            'present_village' => 'nullable|string|max:191',
            'permanent_post_office_bn' => 'nullable|string|max:191',
            'permanent_village_bn' => 'nullable|string|max:191',
            'present_post_office_bn' => 'nullable|string|max:191',
            'present_village_bn' => 'nullable|string|max:191',
        ]);

        $employee->fill(Arr::except($payload, [
            'permanent_post_office_bn',
            'permanent_village_bn',
            'present_post_office_bn',
            'present_village_bn',
        ]));
        $other = $this->otherInfo($employee);
        $other['address_info'] = array_merge(data_get($other, 'address_info', []), [
            'permanent_post_office_bn' => $payload['permanent_post_office_bn'] ?? null,
            'permanent_village_bn' => $payload['permanent_village_bn'] ?? null,
            'present_post_office_bn' => $payload['present_post_office_bn'] ?? null,
            'present_village_bn' => $payload['present_village_bn'] ?? null,
        ]);
        $employee->other_information = json_encode($other);
        $employee->setTypes('employee');
        $employee->present_village_bn = $payload['present_village_bn'] ?? null;
        $employee->present_post_office_bn = $payload['present_post_office_bn'] ?? null;
        $employee->permanent_district_bn = \App\Models\Country::where('name', $request->permanent_district)->first()->bn_name;
        $employee->permanent_upazila_bn = \App\Models\Country::where('name', $request->permanent_upazila)->first()->bn_name;

        $employee->permanent_village_bn = $payload['permanent_village_bn'] ?? null;
        $employee->permanent_post_office_bn = $payload['permanent_post_office_bn'] ?? null;
        $employee->present_district_bn = \App\Models\Country::where('name', $request->present_district)->first()->bn_name;
        $employee->present_upazila_bn = \App\Models\Country::where('name', $request->present_upazila)->first()->bn_name;

        $employee->save();

        return redirect()->route('hr-center.employees.index')->with('success', 'Address info updated.');
    }

    public function updateBasicInfo(Request $request, User $employee): RedirectResponse
    {
        $this->ensureEmployee($employee);

        $payload = $request->validate([
            'father_name'         => 'nullable|string|max:191',
            'father_name_bn'      => 'nullable|string|max:191',
            'mother_name'         => 'nullable|string|max:191',
            'mother_name_bn'      => 'nullable|string|max:191',
            'marital_status'      => 'nullable|string|max:50',
            'spouse_name'         => 'nullable|string|max:191',
            'spouse_name_bn'      => 'nullable|string|max:191',
            'gender'              => 'nullable|string|max:20',
            'boys'                => 'nullable|integer|min:0',
            'religion'            => 'nullable|string|max:100',
            'dob'                 => 'nullable|date',
            'blood_group'         => 'nullable|string|max:10',
            'nationality'         => 'nullable|string|max:100',
            'nid_number'          => 'nullable|string|max:100',
            'birth_registration'  => 'nullable|string|max:100',
            'passport_no'         => 'nullable|string|max:100',
            'driving_license'     => 'nullable|string|max:100',
            'distinguished_mark'  => 'nullable|string|max:191',
            'distinguished_mark_bn' => 'nullable|string|max:191',
            'education'           => 'nullable|string|max:191',
            'education_bn'        => 'nullable|string|max:191',
            'reference_1'         => 'nullable|string|max:191',
            'reference_1_bn'      => 'nullable|string|max:191',
            'reference_2'         => 'nullable|string|max:191',
            'reference_2_bn'      => 'nullable|string|max:191',
            'salary_type'         => 'nullable|string|max:50',
            'job_experience'      => 'nullable|string|max:191',
            'job_experience_bn'   => 'nullable|string|max:191',
            'prev_organization'   => 'nullable|string|max:191',
            'prev_organization_bn' => 'nullable|string|max:191',
            'reference_card_no'   => 'nullable|string|max:100',
            'reference_card_no_bn' => 'nullable|string|max:100',
            'reference_mobile'    => 'nullable|string|max:30',
            'reference_mobile_bn' => 'nullable|string|max:30',
        ]);

        $other = $this->otherInfo($employee);
        $currentProfile = data_get($other, 'profile', []);

        $options = $this->options();
        $maritalStatusMap = array_replace([
            'single' => 'অবিবাহিত',
            'unmarried' => 'অবিবাহিত',
            'married' => 'বিবাহিত',
            'divorced' => 'তালাকপ্রাপ্ত',
            'widowed' => 'বিধবা/বিপত্নীক',
            'widow' => 'বিধবা/বিপত্নীক',
            'widower' => 'বিধবা/বিপত্নীক',
        ], $this->banglaMapFromOptions(data_get($options, 'maritalStatuses', []), ['name', 'code']));
        $genderMap = array_replace([
            'male' => 'পুরুষ',
            'female' => 'মহিলা',
            'other' => 'অন্যান্য',
        ], $this->banglaMapFromOptions(data_get($options, 'sexes', []), ['name', 'code']));
        $religionMap = array_replace([
            'islam' => 'ইসলাম',
            'hindu' => 'হিন্দু',
            'buddhist' => 'বৌদ্ধ',
            'christian' => 'খ্রিস্টান',
            'others' => 'অন্যান্য',
            'other' => 'অন্যান্য',
        ], $this->banglaMapFromOptions(data_get($options, 'religions', []), ['name', 'code']));
        $paymentModeMap = array_replace([
            'cash' => 'নগদ',
            'bank' => 'ব্যাংক',
            'mobile banking' => 'মোবাইল ব্যাংকিং',
            'cheque' => 'চেক',
        ], $this->banglaMapFromOptions(data_get($options, 'paymentMethods', []), ['name', 'code']));
        $nationalityMap = array_replace([
            'bangladeshi' => 'বাংলাদেশি',
            'bangladesh' => 'বাংলাদেশ',
            'indian' => 'ভারতীয়',
            'india' => 'ভারত',
            'pakistani' => 'পাকিস্তানি',
            'pakistan' => 'পাকিস্তান',
            'nepali' => 'নেপালি',
            'nepal' => 'নেপাল',
            'bhutanese' => 'ভুটানি',
            'bhutan' => 'ভুটান',
            'sri lankan' => 'শ্রীলঙ্কান',
            'sri lanka' => 'শ্রীলঙ্কা',
        ], $this->banglaMapFromOptions(data_get($options, 'countries', []), ['name', 'code']));

        $payload['marital_status_bn'] = $this->toBanglaLabel(
            $payload['marital_status'] ?? null,
            $maritalStatusMap,
            data_get($currentProfile, 'marital_status_bn')
        );

        $payload['gender_bn'] = $this->toBanglaLabel(
            $payload['gender'] ?? null,
            $genderMap,
            data_get($currentProfile, 'gender_bn')
        );

        $payload['religion_bn'] = $this->toBanglaLabel(
            $payload['religion'] ?? null,
            $religionMap,
            data_get($currentProfile, 'religion_bn')
        );

        $payload['nationality_bn'] = $this->toBanglaLabel(
            $payload['nationality'] ?? null,
            $nationalityMap,
            data_get($currentProfile, 'nationality_bn')
        );

        $payload['salary_type_bn'] = $this->toBanglaLabel(
            $payload['salary_type'] ?? null,
            $paymentModeMap,
            data_get($currentProfile, 'salary_type_bn')
        );

        $profileFields = [
            'job_experience',
            'job_experience_bn',
            'prev_organization',
            'prev_organization_bn',
            'reference_card_no',
            'reference_card_no_bn',
            'reference_mobile',
            'reference_mobile_bn',
            'marital_status_bn',
            'gender_bn',
            'religion_bn',
            'nationality_bn',
            'salary_type_bn',
            'distinguished_mark_bn',
            'education_bn',
            'reference_1_bn',
            'reference_2_bn',
        ];

        $directFields = Arr::except($payload, $profileFields);
        $employee->fill($directFields);

        $other = $this->otherInfo($employee);
        $other['profile'] = array_merge(data_get($other, 'profile', []), [
            'job_experience'    => $payload['job_experience'] ?? null,
            'job_experience_bn' => $payload['job_experience_bn'] ?? null,
            'prev_organization' => $payload['prev_organization'] ?? null,
            'prev_organization_bn' => $payload['prev_organization_bn'] ?? null,
            'reference_card_no' => $payload['reference_card_no'] ?? null,
            'reference_card_no_bn' => $payload['reference_card_no_bn'] ?? null,
            'reference_mobile'  => $payload['reference_mobile'] ?? null,
            'reference_mobile_bn' => $payload['reference_mobile_bn'] ?? null,
            'marital_status_bn' => $payload['marital_status_bn'] ?? null,
            'gender_bn' => $payload['gender_bn'] ?? null,
            'religion_bn' => $payload['religion_bn'] ?? null,
            'nationality_bn' => $payload['nationality_bn'] ?? null,
            'salary_type_bn' => $payload['salary_type_bn'] ?? null,
            'nid_number_bn' => null,
            'birth_registration_bn' => null,
            'passport_no_bn' => null,
            'driving_license_bn' => null,
            'distinguished_mark_bn' => $payload['distinguished_mark_bn'] ?? null,
            'education_bn' => $payload['education_bn'] ?? null,
            'reference_1_bn' => $payload['reference_1_bn'] ?? null,
            'reference_2_bn' => $payload['reference_2_bn'] ?? null,
        ]);
        $employee->other_information = json_encode($other);
        $employee->setTypes('employee');
        $employee->save();

        return redirect()->route('hr-center.employees.index')->with('success', 'Basic info updated.');
    }

    public function updateNominee(Request $request, User $employee): RedirectResponse
    {
        $this->ensureEmployee($employee);

        $payload = $request->validate([
            'nominee' => 'nullable|string|max:191',
            'nominee_age' => 'nullable|integer|min:0',
            'nominee_relation' => 'nullable|string|max:100',
            'nominee_district' => 'nullable|string|max:191',
            'nominee_po_station' => 'nullable|string|max:191',
            'nominee_post_office' => 'nullable|string|max:191',
            'nominee_post_office_bn' => 'nullable|string|max:191',
            'nominee_nationality' => 'nullable|string|max:191',
            'nominee_village' => 'nullable|string|max:191',
            'nominee_village_bn' => 'nullable|string|max:191',
            'nominee_nid' => 'nullable|string|max:100',
            'nominee_mobile' => 'nullable|string|max:30',
            'nominee_bn_name' => 'nullable|string|max:191',
            'nominee_relation_bn' => 'nullable|string|max:100',
            'distribution_net_payment' => 'nullable|numeric|min:0|max:100',
            'distribution_provident_fund' => 'nullable|numeric|min:0|max:100',
            'distribution_insurance' => 'nullable|numeric|min:0|max:100',
            'distribution_accident_fine' => 'nullable|numeric|min:0|max:100',
            'distribution_profit' => 'nullable|numeric|min:0|max:100',
            'distribution_others' => 'nullable|numeric|min:0|max:100',
            'nominee_image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        $employee->nominee = $payload['nominee'] ?? null;
        $employee->nominee_age = $payload['nominee_age'] ?? null;
        $employee->nominee_relation = $payload['nominee_relation'] ?? null;

        $other = $this->otherInfo($employee);
        $nomineeInfo = Arr::except($payload, ['nominee', 'nominee_age', 'nominee_relation', 'nominee_image']);
        if ($request->hasFile('nominee_image')) {
            $file = $request->file('nominee_image');
            $folder = public_path('medies/nominees');
            if (!is_dir($folder)) {
                @mkdir($folder, 0755, true);
            }
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($folder, $filename);
            $nomineeInfo['nominee_image'] = 'medies/nominees/' . $filename;
        } else {
            $nomineeInfo['nominee_image'] = data_get($other, 'nominee_info.nominee_image');
        }
        $other['nominee_info'] = $nomineeInfo;
        $employee->other_information = json_encode($other);
        $employee->setTypes('employee');
        $employee->save();

        return redirect()->route('hr-center.employees.index')->with('success', 'Nominee info updated.');
    }

    public function updateAgeVerification(Request $request, User $employee): RedirectResponse
    {
        $this->ensureEmployee($employee);

        $payload = $request->validate([
            'physical_ability' => 'nullable|string|max:191',
            'distinguished_mark' => 'nullable|string|max:191',
            'verified_age' => 'nullable|integer|min:0',
            'age_verification_date' => 'nullable|date',
        ]);

        $employee->distinguished_mark = $payload['distinguished_mark'] ?? null;
        $other = $this->otherInfo($employee);
        $other['age_verification'] = Arr::except($payload, ['distinguished_mark']);
        $employee->other_information = json_encode($other);
        $employee->setTypes('employee');
        $employee->save();

        return redirect()->route('hr-center.employees.index')->with('success', 'Age verification info updated.');
    }

    public function updateResign(Request $request, User $employee): RedirectResponse
    {
        $this->ensureEmployee($employee);

        $payload = $request->validate([
            'employment_status' => 'nullable|in:regular,resign,lefty,transfer',
            'resign_remarks' => 'nullable|string|max:500',
            'resign_date' => 'nullable|date',
            'final_settlement_type' => 'nullable|string|max:191',
            'with_paid' => 'nullable|boolean',
        ]);

        $employee->employment_status = $payload['employment_status'] ?? null;
        $employee->exited_at = $payload['resign_date'] ?? null;

        $other = $this->otherInfo($employee);
        $other['resign_info'] = [
            'remarks' => $payload['resign_remarks'] ?? null,
            'final_settlement_type' => $payload['final_settlement_type'] ?? null,
            'with_paid' => $request->boolean('with_paid'),
        ];
        $employee->other_information = json_encode($other);
        $employee->setTypes('employee');
        $employee->save();

        return redirect()->route('hr-center.employees.index')->with('success', 'Lefty/Resign info updated.');
    }

    public function updateFinalSettlement(Request $request, User $employee)
    {
        $this->ensureEmployee($employee);

        $payload = $request->validate([
            'absent_date' => 'nullable|date',
            'letter_1_date' => 'nullable|date',
            'letter_2_date' => 'nullable|date',
            'letter_3_date' => 'nullable|date',
            'final_settlement_option' => 'nullable|in:1st Letter,2nd Letter,3rd Letter',
        ]);

        $other = $this->otherInfo($employee);
        $other['final_settlement'] = $payload;
        $employee->other_information = json_encode($other);
        $employee->setTypes('employee');
        $employee->save();

        if ($request->routeIs('hr-center.employees.final-settlement.print')) {
            $designationBn = null;
            $designationEn = null;

            if (!empty($employee->designation_id)) {
                if (Schema::hasTable((new Designation())->getTable())) {
                    $designationRow = Designation::query()->find($employee->designation_id);
                    $designationBn = data_get($designationRow, 'bn_name');
                    $designationEn = data_get($designationRow, 'name');
                }

                if (!$designationBn && !$designationEn) {
                    $designationAttr = Attribute::query()
                        ->where('id', $employee->designation_id)
                        ->first(['id', 'name', 'bn_name']);
                    $designationBn = data_get($designationAttr, 'bn_name');
                    $designationEn = data_get($designationAttr, 'name');
                }
            }

            return view('hr::employees.print.final-settlement', [
                'employee' => $employee,
                'settlement' => $payload,
                'designation_bn' => $designationBn,
                'designation_en' => $designationEn,
            ]);
        }

        return redirect()->route('hr-center.employees.index')->with('success', 'Final settlement info updated.');
    }

    public function incrementsPage(User $employee)
    {
        $this->ensureEmployee($employee);

        $rows = collect();
        $table = (new EmployeeIncrement())->getTable();
        if (Schema::hasTable($table)) {
            $query = EmployeeIncrement::query();
            if (Schema::hasColumn($table, 'user_id')) {
                $query->where('user_id', $employee->id);
            } elseif (Schema::hasColumn($table, 'employee_id')) {
                $query->where('employee_id', $employee->id);
            }
            $rows = $query->latest()->limit(50)->get()->map(function ($row) {
                return [
                    'source' => 'db',
                    'identifier' => (string) ($row->id ?? ''),
                    'previous_salary' => (float) $row->previous_salary,
                    'increment_amount' => (float) $row->increment_amount,
                    'new_salary' => (float) $row->new_salary,
                    'increment_date' => $row->increment_date,
                ];
            })->values();
        } else {
            $other = $this->otherInfo($employee);
            $rows = collect(data_get($other, 'increments', []))
                ->sortByDesc(function ($row) {
                    return data_get($row, 'increment_date') ?: data_get($row, 'created_at');
                })
                ->values()
                ->map(function ($row, $index) {
                    return [
                        'source' => 'other',
                        'identifier' => (string) $index,
                        'previous_salary' => (float) data_get($row, 'previous_salary', 0),
                        'increment_amount' => (float) data_get($row, 'increment_amount', 0),
                        'new_salary' => (float) data_get($row, 'new_salary', 0),
                        'increment_date' => data_get($row, 'increment_date'),
                    ];
                });
        }

        $options = $this->options();
        $employeeMeta = [
            'classification' => optional(collect($options['classifications'] ?? [])->firstWhere('id', $employee->employee_type))->name,
            'department' => optional(collect($options['departments'] ?? [])->firstWhere('id', $employee->department_id))->name,
            'section' => optional(collect($options['sections'] ?? [])->firstWhere('id', $employee->section_id))->name,
            'designation' => optional(collect($options['designations'] ?? [])->firstWhere('id', $employee->designation_id))->name,
        ];

        return view('hr::employees.pages.increments', compact('employee', 'rows', 'employeeMeta'));
    }

    public function incrementsStore(Request $request, User $employee): RedirectResponse
    {
        $this->ensureEmployee($employee);
        $payload = $request->validate([
            'increment_date' => 'required|date',
            'amount' => 'required|numeric',
        ]);

        $oldIncrement = EmployeeIncrement::where('user_id', $employee->id)->latest()->first();

        $previous_salary =  $oldIncrement ? $oldIncrement->new_salary : $employee->gross_salary;
        $increment_amount = $payload['amount'];
        $new_salary = $previous_salary + $increment_amount;
        $increment_percentage = $previous_salary > 0 ? ($increment_amount / $previous_salary) * 100 : null;

        $otherInfo = $this->otherInfo($employee); // existing decoded array
        $prev_comp_1 = isset($otherInfo['salary_info']['gross_salary_comp_1']) ? (float)$otherInfo['salary_info']['gross_salary_comp_1'] : 0;
        $prev_comp_2 = isset($otherInfo['salary_info']['gross_salary_comp_2']) ? (float)$otherInfo['salary_info']['gross_salary_comp_2'] : 0;
        $new_comp_1 = $prev_comp_1 + $increment_amount;
        $new_comp_2 = $prev_comp_2 + $increment_amount;

        $increment = EmployeeIncrement::create([
            'user_id' => $employee->id,
            'increment_date' => $payload['increment_date'],
            'previous_salary' => $previous_salary,
            'increment_amount' => $increment_amount,
            'increment_percentage' => $increment_percentage,
            'new_salary' => $new_salary,
            'previous_salary_comp_1' => $prev_comp_1,
            'new_salary_comp_1' => $new_comp_1,
            'previous_salary_comp_2' => $prev_comp_2,
            'new_salary_comp_2' => $new_comp_2,
        ]);
        $employee->gross_salary = $new_salary;
        if (isset($otherInfo['salary_info'])) {
            $otherInfo['salary_info']['gross_salary_comp_1'] = $new_comp_1;
            $otherInfo['salary_info']['gross_salary_comp_2'] = $new_comp_2;
            $otherInfo['gross_salary_comp_1'] = $new_comp_1;
            $otherInfo['gross_salary_comp_2'] = $new_comp_2;
        }
        $employee->other_information = json_encode($otherInfo);
        $employee->save();
        return redirect()->route('hr-center.employees.increments.page', $employee->id)->with('success', 'Increment added.');
    }

    public function incrementsUpdate(Request $request, User $employee): RedirectResponse
    {
        $this->ensureEmployee($employee);
        
        $payload = $request->validate([
            'identifier' => 'required|integer|exists:employee_increments,id',
            'increment_date' => 'required|date',
            'amount' => 'required|numeric',
        ]);

        // ১. বিদ্যমান ইনক্রিমেন্ট রেকর্ডটি খুঁজে বের করা
        $increment = EmployeeIncrement::findOrFail($payload['identifier']);

        // ২. স্যালারি রিভার্স করা (আগের ইনক্রিমেন্ট বাদ দিয়ে বেস স্যালারিতে ফিরে যাওয়া)
        $old_increment_amount = $increment->increment_amount;
        $base_salary = $employee->gross_salary - $old_increment_amount;

        // ৩. নতুন ক্যালকুলেশন
        $new_increment_amount = $payload['amount'];
        $new_gross_salary = $base_salary + $new_increment_amount;
        $new_percentage = $base_salary > 0 ? ($new_increment_amount / $base_salary) * 100 : 0;

        $otherInfo = $this->otherInfo($employee);
        $prev_comp_1 = isset($otherInfo['salary_info']['gross_salary_comp_1']) ? (float)$otherInfo['salary_info']['gross_salary_comp_1'] : 0;
        $prev_comp_2 = isset($otherInfo['salary_info']['gross_salary_comp_2']) ? (float)$otherInfo['salary_info']['gross_salary_comp_2'] : 0;
        // Reverse old increment and add new
        $new_comp_1 = $prev_comp_1 - $old_increment_amount + $new_increment_amount;
        $new_comp_2 = $prev_comp_2 - $old_increment_amount + $new_increment_amount;

        // ৪. ইনক্রিমেন্ট টেবিল আপডেট
        $increment->update([
            'increment_date' => $payload['increment_date'],
            'increment_amount' => $new_increment_amount,
            'increment_percentage' => $new_percentage,
            'new_salary' => $new_gross_salary,
            'previous_salary_comp_1' => $prev_comp_1,
            'new_salary_comp_1' => $new_comp_1,
            'previous_salary_comp_2' => $prev_comp_2,
            'new_salary_comp_2' => $new_comp_2,
        ]);

        // ৫. Employee টেবিল আপডেট
        $employee->gross_salary = $new_gross_salary;

        // ৬. JSON (other_information) ডেটা আপডেট
        if (isset($otherInfo['salary_info'])) {
            $otherInfo['salary_info']['gross_salary_comp_1'] = $new_comp_1;
            $otherInfo['salary_info']['gross_salary_comp_2'] = $new_comp_2;
            $otherInfo['gross_salary_comp_1'] = $new_comp_1;
            $otherInfo['gross_salary_comp_2'] = $new_comp_2;
        }

        $employee->other_information = json_encode($otherInfo);
        $employee->save();

        return redirect()->route('hr-center.employees.increments.page', $employee->id)
                        ->with('success', 'Increment updated successfully.');
    }


    public function earningsDeductionsPage(User $employee)
    {
        $this->ensureEmployee($employee);

        $other = $this->otherInfo($employee);
        $rows = collect($other['earnings_deductions'] ?? [])
            ->sortByDesc(fn ($row) => data_get($row, 'date') ?: data_get($row, 'created_at'))
            ->values()
            ->map(function ($row, $index) {
                $date = data_get($row, 'date');
                $year = '-';
                $month = '-';
                if ($date) {
                    try {
                        $parsed = \Illuminate\Support\Carbon::parse($date);
                        $year = $parsed->format('Y');
                        $month = $parsed->format('F');
                    } catch (\Throwable $exception) {
                        // Keep fallback values when date is malformed in legacy rows.
                    }
                }

                return [
                    'source' => 'other',
                    'identifier' => (string) $index,
                    'date' => $date,
                    'year' => $year,
                    'month' => $month,
                    'advance_iou' => (float) data_get($row, 'advance_iou', 0),
                    'ot' => (float) data_get($row, 'ot', 0),
                    'day' => (float) data_get($row, 'day', 0),
                    'earnings' => (float) data_get($row, 'earnings', 0),
                    'deductions' => (float) data_get($row, 'deductions', 0),
                    'remarks' => data_get($row, 'remarks'),
                ];
            });

        $options = $this->options();
        $employeeMeta = [
            'department' => optional(collect($options['departments'] ?? [])->firstWhere('id', $employee->department_id))->name,
            'designation' => optional(collect($options['designations'] ?? [])->firstWhere('id', $employee->designation_id))->name,
        ];

        return view('hr::employees.pages.earnings-deductions', compact('employee', 'rows', 'employeeMeta'));
    }

    public function earningsDeductionsStore(Request $request, User $employee): RedirectResponse
    {
        $this->ensureEmployee($employee);

        $payload = $request->validate([
            'date' => 'required|date',
            'advance_iou' => 'nullable|numeric',
            'ot' => 'nullable|numeric',
            'day' => 'nullable|numeric',
            'earnings' => 'nullable|numeric',
            'deductions' => 'nullable|numeric',
            'remarks' => 'nullable|string|max:500',
        ]);

        $other = $this->otherInfo($employee);
        $rows = collect(data_get($other, 'earnings_deductions', []));
        $rows->push([
            'date' => $payload['date'],
            'advance_iou' => (float) ($payload['advance_iou'] ?? 0),
            'ot' => (float) ($payload['ot'] ?? 0),
            'day' => (float) ($payload['day'] ?? 0),
            'earnings' => (float) ($payload['earnings'] ?? 0),
            'deductions' => (float) ($payload['deductions'] ?? 0),
            'remarks' => $payload['remarks'] ?? null,
            'created_at' => now()->toDateTimeString(),
        ]);

        $other['earnings_deductions'] = $rows->values()->all();
        $employee->other_information = json_encode($other);
        $employee->save();

        return redirect()->route('hr-center.employees.earnings.page', $employee->id)->with('success', 'Earnings & deductions entry added.');
    }

    public function earningsDeductionsUpdate(Request $request, User $employee): RedirectResponse
    {
        $this->ensureEmployee($employee);

        $payload = $request->validate([
            'identifier' => 'required|integer|min:0',
            'date' => 'required|date',
            'advance_iou' => 'nullable|numeric',
            'ot' => 'nullable|numeric',
            'day' => 'nullable|numeric',
            'earnings' => 'nullable|numeric',
            'deductions' => 'nullable|numeric',
            'remarks' => 'nullable|string|max:500',
        ]);

        $identifier = (int) $payload['identifier'];
        $other = $this->otherInfo($employee);
        $rows = collect(data_get($other, 'earnings_deductions', []));

        if (!isset($rows[$identifier])) {
            return redirect()->route('hr-center.employees.earnings.page', $employee->id)->with('error', 'Row not found.');
        }

        $rows[$identifier] = array_merge((array) $rows[$identifier], [
            'date' => $payload['date'],
            'advance_iou' => (float) ($payload['advance_iou'] ?? 0),
            'ot' => (float) ($payload['ot'] ?? 0),
            'day' => (float) ($payload['day'] ?? 0),
            'earnings' => (float) ($payload['earnings'] ?? 0),
            'deductions' => (float) ($payload['deductions'] ?? 0),
            'remarks' => $payload['remarks'] ?? null,
        ]);

        $other['earnings_deductions'] = $rows->values()->all();
        $employee->other_information = json_encode($other);
        $employee->save();

        return redirect()->route('hr-center.employees.earnings.page', $employee->id)->with('success', 'Earnings & deductions entry updated.');
    }

    public function earningsDeductionsDelete(Request $request, User $employee): RedirectResponse
    {
        $this->ensureEmployee($employee);

        $payload = $request->validate([
            'identifier' => 'required|integer|min:0',
        ]);

        $identifier = (int) $payload['identifier'];
        $other = $this->otherInfo($employee);
        $rows = collect(data_get($other, 'earnings_deductions', []));

        if (!isset($rows[$identifier])) {
            return redirect()->route('hr-center.employees.earnings.page', $employee->id)->with('error', 'Row not found.');
        }

        $rows->forget($identifier);
        $other['earnings_deductions'] = $rows->values()->all();
        $employee->other_information = json_encode($other);
        $employee->save();

        return redirect()->route('hr-center.employees.earnings.page', $employee->id)->with('success', 'Earnings & deductions entry deleted.');
    }

    public function leavesPage(User $employee)
    {
        $this->ensureEmployee($employee);

        $rows = Leave::query()
            ->where('employee_id', $employee->id)
            ->latest()
            ->limit(100)
            ->get()
            ->map(function ($row) {
                $leaveType = optional($row->leaveType);
                $leaveFrom = data_get($row, 'start_date');
                $leaveTo = data_get($row, 'end_date');
                return [
                    'source' => 'db',
                    'identifier' => (string) ($row->id ?? ''),
                    'application_date' => data_get($row, 'application_date'),
                    'application_no' => data_get($row, 'application_no'),
                    'leave_code' => $leaveType->code ?? null,
                    'leave_type' => $leaveType->name ?? null,
                    'leave_type_id' => $row->leave_type_id,
                    'leave_from' => $leaveFrom,
                    'leave_to' => $leaveTo,
                    'purpose' => data_get($row, 'reason'),
                    'remarks' => data_get($row, 'remarks'),
                    'status' => data_get($row, 'status'),
                    'total_days' => data_get($row, 'total_days') ?? $this->calculateTotalDays($leaveFrom, $leaveTo),
                ];
            });

        $leaveTypes = Schema::hasTable((new LeaveInfo())->getTable())
            ? LeaveInfo::query()->where('status', 'active')->orderBy('name')->get(['id', 'name', 'code', 'days'])
            : collect();

        $takenByTypeId = $rows
            ->groupBy(fn ($row) => (int) $row['leave_type_id'])
            ->map(fn ($group) => (int) round($group->sum(fn ($row) => (float) data_get($row, 'total_days', 0))));

        $leaveSummary = $leaveTypes->map(function ($leaveType) use ($takenByTypeId) {
            $typeId = (int) $leaveType->id;
            $totalDays = (int) ($leaveType->days ?? 0);
            $takenDays = (int) ($takenByTypeId->get($typeId, 0));

            return [
                'code' => $leaveType->code,
                'name' => $leaveType->name,
                'remaining_days' => $totalDays,
                'taken_days' => $takenDays,
                'available_days' => max($totalDays - $takenDays, 0),
            ];
        });

        $options = $this->options();
        $employeeMeta = [
            'classification' => optional(collect($options['classifications'] ?? [])->firstWhere('id', $employee->employee_type))->name,
            'department' => optional(collect($options['departments'] ?? [])->firstWhere('id', $employee->department_id))->name,
            'section' => optional(collect($options['sections'] ?? [])->firstWhere('id', $employee->section_id))->name,
            'designation' => optional(collect($options['designations'] ?? [])->firstWhere('id', $employee->designation_id))->name,
        ];

        return view('hr::employees.pages.leaves', compact('employee', 'rows', 'leaveTypes', 'leaveSummary', 'employeeMeta'));
    }

    public function leavesStore(Request $request, User $employee): RedirectResponse
    {
        $this->ensureEmployee($employee);
        $payload = $request->validate([
            'leave_type_id' => 'required|exists:hr_leave_infos,id',
            'application_date' => 'required|date',
            'application_no' => 'nullable|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
            'remarks' => 'nullable|string',
            'status' => 'nullable|string|max:20',
        ]);

        $total_days = $this->calculateTotalDays($payload['start_date'], $payload['end_date']);

        Leave::create([
            'employee_id' => $employee->id,
            'leave_type_id' => $payload['leave_type_id'],
            'application_date' => $payload['application_date'],
            'application_no' => $payload['application_no'] ?? null,
            'start_date' => $payload['start_date'],
            'end_date' => $payload['end_date'],
            'total_days' => $total_days,
            'reason' => $payload['reason'] ?? null,
            'remarks' => $payload['remarks'] ?? null,
            'status' => $payload['status'] ?? 'pending',
        ]);
        return redirect()->route('hr-center.employees.leaves.page', $employee->id)->with('success', 'Leave added.');
    }

    public function leavesUpdate(Request $request, User $employee): RedirectResponse
    {
        $this->ensureEmployee($employee);

        $payload = $request->validate([
            'identifier' => 'required|string',
            'leave_type_id' => 'required|exists:hr_leave_infos,id',
            'application_date' => 'required|date',
            'application_no' => 'nullable|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
            'remarks' => 'nullable|string',
            'status' => 'nullable|string|max:20',
        ]);

        $row = Leave::where('id', $payload['identifier'])->where('employee_id', $employee->id)->first();
        if (!$row) {
            return redirect()->route('hr-center.employees.leaves.page', $employee->id)->with('error', 'Leave row not found.');
        }
        $row->leave_type_id = $payload['leave_type_id'];
        $row->application_date = $payload['application_date'];
        $row->application_no = $payload['application_no'] ?? null;
        $row->start_date = $payload['start_date'];
        $row->end_date = $payload['end_date'];
        $row->total_days = $this->calculateTotalDays($payload['start_date'], $payload['end_date']);
        $row->reason = $payload['reason'] ?? null;
        $row->remarks = $payload['remarks'] ?? null;
        $row->status = $payload['status'] ?? 'pending';
        $row->save();
        return redirect()->route('hr-center.employees.leaves.page', $employee->id)->with('success', 'Leave updated.');
    }

    public function leavesDelete(Request $request, User $employee): RedirectResponse
    {
        $this->ensureEmployee($employee);

        $payload = $request->validate([
            'source' => 'required|in:db,other',
            'identifier' => 'required|string',
        ]);

        $row = Leave::where('id', $payload['identifier'])->where('employee_id', $employee->id)->first();
        if (!$row) {
            return redirect()->route('hr-center.employees.leaves.page', $employee->id)->with('error', 'Leave row not found.');
        }
        $row->delete();
        return redirect()->route('hr-center.employees.leaves.page', $employee->id)->with('success', 'Leave deleted.');
    }

    public function printSection(User $employee, string $section)
    {
        $this->ensureEmployee($employee);

        dd("Print section: {$section} for employee ID: {$employee->id}");
    }

    private function options(): array
    {
        $maritalStatuses = Location::query()
            ->whereIn('type', ['marital_status', 'marital-status'])
            ->orderBy('name')
            ->get(['id', 'name', 'bn_name', 'code']);

        $religions = Location::query()
            ->whereIn('type', ['religion', 'religions'])
            ->orderBy('name')
            ->get(['id', 'name', 'bn_name', 'code']);

        $sexes = Location::query()
            ->whereIn('type', ['sex', 'gender'])
            ->orderBy('name')
            ->get(['id', 'name', 'bn_name', 'code']);

        $paymentMethodColumns = ['id', 'name', 'code'];
        if (Schema::hasColumn((new PaymentMethod())->getTable(), 'bn_name')) {
            $paymentMethodColumns[] = 'bn_name';
        }

        $paymentMethods = PaymentMethod::query()
            ->orderBy('name')
            ->get($paymentMethodColumns);

        $countryColumns = ['id', 'name'];
        if (Schema::hasColumn((new Country())->getTable(), 'bn_name')) {
            $countryColumns[] = 'bn_name';
        }

        $countries = Country::query()->orderBy('name')->get($countryColumns);

        return [
            'classifications' => Attribute::where('type', 16)->where('status', '<>', 'temp')->orderBy('name')->get(['id', 'name']),
            'departments' => Attribute::where('type', 3)->where('status', '<>', 'temp')->orderBy('name')->get(['id', 'name']),
            'sections' => Attribute::where('type', 29)->where('status', '<>', 'temp')->orderBy('name')->get(['id', 'name']),
            'subSections' => SubSection::orderBy('name')->get(['id', 'name']),
            'lines' => Attribute::where('type', 4)->where('status', '<>', 'temp')->orderBy('name')->get(['id', 'name', 'slug']),
            'designations' => Schema::hasTable((new Designation())->getTable())
                ? Designation::query()->orderBy('name')->get(['id', 'name'])
                : Attribute::where('type', 2)->where('status', '<>', 'temp')->orderBy('name')->get(['id', 'name']),
            'workingPlaces' => WorkingPlace::orderBy('name')->get(['id', 'name']),
            'weeks' => Schema::hasTable((new Weekday())->getTable())
                ? Weekday::orderBy('id')->get(['id', 'name'])
                : collect([
                    (object) ['id' => 1, 'name' => 'Sunday'],
                    (object) ['id' => 2, 'name' => 'Monday'],
                    (object) ['id' => 3, 'name' => 'Tuesday'],
                    (object) ['id' => 4, 'name' => 'Wednesday'],
                    (object) ['id' => 5, 'name' => 'Thursday'],
                    (object) ['id' => 6, 'name' => 'Friday'],
                    (object) ['id' => 7, 'name' => 'Saturday'],
                ]),
            'shifts' => Shift::orderBy('name_of_shift')->get(['id', 'name_of_shift']),
            'maritalStatuses' => $maritalStatuses,
            'religions' => $religions,
            'sexes' => $sexes,
            'paymentMethods' => $paymentMethods,
            'countries' => $countries,
            'districts' => Country::where('type', 3)->orderBy('name')->get(['id', 'name']),
            'thanas' => Country::where('type', 4)->orderBy('name')->get(['id', 'parent_id', 'name']),
        ];
    }

    private function resolveLeaveType(string $leaveCode): ?LeaveInfo
    {
        $table = (new LeaveInfo())->getTable();
        if (!Schema::hasTable($table)) {
            return null;
        }

        return LeaveInfo::query()
            ->where('status', 'active')
            ->where('code', $leaveCode)
            ->first(['id', 'name', 'code', 'days']);
    }

    public function destroy(User $employee): RedirectResponse
    {
        $this->ensureEmployee($employee);
        $employee->delete();

        return redirect()->route('hr-center.employees.index')->with('success', 'Employee deleted successfully.');
    }

    private function ensureEmployee(User $employee): void
    {
        // dd($employee, User::query()->filterByType('employee')->whereKey($employee->id)->exists());
        abort_unless(
            User::query()->filterByType('employee')->whereKey($employee->id)->exists(),
            404
        );
    }

    private function applyIntegerFilter($query, string $column, int $value, ?string $profileKey = null): void
    {
        $query->where(function ($builder) use ($column, $value, $profileKey) {
            $matched = false;

            if (Schema::hasColumn('users', $column)) {
                $builder->where($column, $value);
                $matched = true;
            }

            if ($profileKey !== null) {
                $jsonPath = 'other_information->profile->' . $profileKey;
                if ($matched) {
                    $builder->orWhereJsonContains($jsonPath, $value)
                        ->orWhereJsonContains($jsonPath, (string) $value);
                } else {
                    $builder->whereJsonContains($jsonPath, $value)
                        ->orWhereJsonContains($jsonPath, (string) $value);
                }
            }
        });
    }

    private function applyStringFilter($query, string $column, string $value, ?string $profileKey = null): void
    {
        $text = trim($value);
        if ($text === '') {
            return;
        }

        $query->where(function ($builder) use ($column, $text, $profileKey) {
            $matched = false;

            if (Schema::hasColumn('users', $column)) {
                $builder->where($column, $text);
                $matched = true;
            }

            if ($profileKey !== null) {
                $jsonPath = 'other_information->profile->' . $profileKey;
                if ($matched) {
                    $builder->orWhereJsonContains($jsonPath, $text);
                } else {
                    $builder->whereJsonContains($jsonPath, $text);
                }
            }
        });
    }

    private function calculateTotalDays(?string $leaveFrom, ?string $leaveTo): int
    {
        if (!$leaveFrom || !$leaveTo) {
            return 0;
        }

        try {
            return \Illuminate\Support\Carbon::parse($leaveFrom)
                ->startOfDay()
                ->diffInDays(\Illuminate\Support\Carbon::parse($leaveTo)->startOfDay()) + 1;
        } catch (\Throwable $exception) {
            return 0;
        }
    }

    private function otherInfo(User $employee): array
    {
        $current = $employee->other_information;
        if (is_array($current)) {
            return $current;
        }

        $decoded = json_decode((string) $current, true);
        return is_array($decoded) ? $decoded : [];
    }

    private function normalizeUserStatus(string $status): int|string
    {
        if (!$this->isUserStatusNumeric()) {
            return $status;
        }

        return $status === 'active' ? 1 : 0;
    }

    private function isUserStatusNumeric(): bool
    {
        if (!Schema::hasColumn('users', 'status')) {
            return false;
        }

        $columnType = strtolower((string) Schema::getColumnType('users', 'status'));

        return in_array($columnType, ['tinyint', 'smallint', 'mediumint', 'int', 'integer', 'bigint', 'boolean'], true);
    }

    private function toBanglaLabel(?string $value, array $map, ?string $fallback = null): ?string
    {
        $text = trim((string) $value);
        if ($text === '') {
            return null;
        }

        $normalized = strtolower($text);
        if (isset($map[$normalized])) {
            return $map[$normalized];
        }

        $fallbackText = trim((string) $fallback);
        if ($fallbackText !== '') {
            return $fallbackText;
        }

        return $text;
    }

    private function banglaMapFromOptions(iterable $items, array $sourceFields = ['name', 'code']): array
    {
        $map = [];

        foreach ($items as $item) {
            $bangla = trim((string) data_get($item, 'bn_name', data_get($item, 'name_bn', '')));
            if ($bangla === '') {
                continue;
            }

            foreach ($sourceFields as $field) {
                $source = trim((string) data_get($item, $field));
                if ($source === '') {
                    continue;
                }

                $map[strtolower($source)] = $bangla;
            }
        }

        return $map;
    }

    private function applyExtendedProfileFields(User $employee, array $payload): void
    {
        if (Schema::hasColumn('users', 'sub_section_id')) {
            $employee->sub_section_id = $payload['sub_section_id'] ?? null;
        }

        if (Schema::hasColumn('users', 'working_place_id')) {
            $employee->working_place_id = $payload['working_place_id'] ?? null;
        }

        if (Schema::hasColumn('users', 'weekend')) {
            $employee->weekend = $payload['weekend'] ?? null;
        }

        if (Schema::hasColumn('users', 'is_active_01')) {
            $employee->is_active_01 = isset($payload['is_active_01']) ? (int) $payload['is_active_01'] : null;
        }

        if (Schema::hasColumn('users', 'is_active_02')) {
            $employee->is_active_02 = isset($payload['is_active_02']) ? (int) $payload['is_active_02'] : null;
        }

        $other = $this->otherInfo($employee);
        $profile = data_get($other, 'profile', []);
        $profile['sub_section_id'] = $payload['sub_section_id'] ?? null;
        $profile['working_place_id'] = $payload['working_place_id'] ?? null;
        $profile['weekend'] = $payload['weekend'] ?? null;
        $profile['is_active_01'] = isset($payload['is_active_01']) ? (int) $payload['is_active_01'] : null;
        $profile['is_active_02'] = isset($payload['is_active_02']) ? (int) $payload['is_active_02'] : null;
        $other['profile'] = $profile;
        $employee->other_information = json_encode($other);

        if (!Schema::hasColumn('users', 'working_place_id') && Schema::hasColumn('users', 'location') && !empty($payload['working_place_id'])) {
            $workingPlace = WorkingPlace::query()->find($payload['working_place_id']);
            $employee->location = $workingPlace?->name;
        }
    }
}
