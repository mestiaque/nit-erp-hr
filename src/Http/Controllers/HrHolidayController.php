<?php

namespace ME\Hr\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use ME\Hr\Models\Holiday;
use Illuminate\Routing\Controller;

class HrHolidayController extends Controller
{
    private const TYPES = [
        'National Holiday',
        'Festival Holiday',
        'Weekly Holiday',
        'Factory Holiday',
        'Government Holiday',
        'Other',
    ];

    public function index(Request $request)
    {
        $query = Holiday::latest();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('type', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $holidays = $query->paginate(20)->appends($request->query());

        return view('hr::holidays.index', [
            'holidays' => $holidays,
            'request'  => $request,
            'types'    => self::TYPES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'     => 'required|string|max:191',
            'type'      => 'required|string|max:100',
            'from_date' => 'required|date',
            'to_date'   => 'required|date|after_or_equal:from_date',
            'days'      => 'required|integer|min:1',
            'remarks'   => 'nullable|string|max:500',
            'status'    => 'required|in:active,inactive',
        ]);

        Holiday::create($validated);

        return redirect()->route('hr-center.holidays.index')->with('success', 'Holiday created successfully.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $holiday = Holiday::findOrFail($id);

        $validated = $request->validate([
            'title'     => 'required|string|max:191',
            'type'      => 'required|string|max:100',
            'from_date' => 'required|date',
            'to_date'   => 'required|date|after_or_equal:from_date',
            'days'      => 'required|integer|min:1',
            'remarks'   => 'nullable|string|max:500',
            'status'    => 'required|in:active,inactive',
        ]);

        $holiday->update($validated);

        return redirect()->route('hr-center.holidays.index')->with('success', 'Holiday updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        Holiday::findOrFail($id)->delete();

        return redirect()->route('hr-center.holidays.index')->with('success', 'Holiday deleted successfully.');
    }
}
