<?php

namespace ME\Hr\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use ME\Hr\Models\RegularToWeekend;
use App\Models\Attribute;

class RegularToWeekendController extends Controller
{
    public function index(Request $request)
    {
        $query = RegularToWeekend::query();
        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }
        if ($request->filled('date')) {
            $query->where('date', $request->date);
        }
        $items = $query->orderByDesc('id')->paginate(20)->appends($request->query());
        $sections = Attribute::where('type', 29)->get();
        return view('hr::regular-to-weekend.index', compact('items', 'sections', 'request'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'section_id' => 'required',
            'date' => 'required|date',
            'type' => 'required|in:regular,weekend',
            'is_active' => 'boolean',
        ]);
        RegularToWeekend::create($data);
        return back()->with('success', 'Entry created successfully.');
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'section_id' => 'required',
            'date' => 'required|date',
            'type' => 'required|in:regular,weekend',
            'is_active' => 'boolean',
        ]);
        $item = RegularToWeekend::findOrFail($id);
        $item->update($data);
        return back()->with('success', 'Entry updated successfully.');
    }
}
