<?php

namespace App\Http\Controllers;

use App\Models\AdGroup;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdGroupController extends Controller
{
    public function index()
    {
        $groups = AdGroup::query()
            ->withCount('images')
            ->orderBy('name')
            ->paginate(20);

        return view('ad_groups.index', compact('groups'));
    }

    public function create()
    {
        return view('ad_groups.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:150',
            'slug'      => ['required','string','max:150', Rule::unique('ad_groups','slug')],
            'width'     => 'nullable|integer|min:0',
            'height'    => 'nullable|integer|min:0',
            'placement' => 'nullable|in:homepage,article,both',
            'status'    => 'required|boolean',
        ]);

        AdGroup::create($data);

    return redirect()
        ->route('adgroups.edit', $group)
        ->with('success', 'Group created. You can now add images.');    }

    public function edit(AdGroup $group)
    {
        $group->load('images');
        return view('ad_groups.edit', compact('group'));
    }

    public function update(Request $request, AdGroup $group)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:150',
            'slug'      => ['required','string','max:150', Rule::unique('ad_groups','slug')->ignore($group->id)],
            'width'     => 'nullable|integer|min:0',
            'height'    => 'nullable|integer|min:0',
            'placement' => 'nullable|in:homepage,article,both',
            'status'    => 'required|boolean',
        ]);

        $group->update($data);

        return back()->with('success','Group updated.');
    }

    public function destroy(AdGroup $group)
    {
        $group->delete();
        return redirect()->route('adgroups.index')->with('success','Group deleted.');
    }
}
