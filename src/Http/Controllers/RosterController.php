<?php

namespace ME\Hr\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use ME\Hr\Models\Roaster;
use ME\Hr\Models\Shift;
use ME\Hr\Models\Section;
use ME\Hr\Models\SubSection;
use App\Models\User;
use Attribute;

class RosterController extends Controller
{
    public function index()
    {
        $rosters = Roaster::with(['employee', 'shift', 'section', 'subSection'])->orderBy('date', 'desc')->paginate(30);
        return view('hr::rosters.index', compact('rosters'));
    }

    public function create()
    {
        $employees = User::filterByType('employee')->get();
        $masterData = \App\Services\HrOptionsService::getOptions();
        $shifts = $masterData['shifts'];
        $sections = $masterData['sections'];
        $subSections = $masterData['subSections'];
        return view('hr::rosters.create', compact('employees', 'shifts', 'sections', 'subSections'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'nullable|exists:employees,id',
            'shift_id' => 'required|exists:hr_shifts,id',
            'date' => 'required|date',
            'section_id' => 'nullable|exists:sections,id',
            'sub_section_id' => 'nullable|exists:sub_sections,id',
            'remarks' => 'nullable|string',
        ]);
        Roaster::create($data);
        return redirect()->route('hr-center.rosters.index')->with('success', 'Roster assigned successfully.');
    }

    public function destroy($id)
    {
        $roster = Roaster::findOrFail($id);
        $roster->delete();
        return redirect()->route('hr-center.rosters.index')->with('success', 'Roster deleted.');
    }
}
