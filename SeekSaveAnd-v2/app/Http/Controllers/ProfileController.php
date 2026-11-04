<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('profile.edit', ['user' => $request->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'campus' => ['required', Rule::in(config('seeksave.campuses'))],
            'program' => ['required', Rule::in(config('seeksave.programs'))],
            'year' => ['required', Rule::in(config('seeksave.years'))],
            'section' => ['required', Rule::in(config('seeksave.sections'))],
            'student_number' => ['required', 'string', 'max:30', Rule::unique('users', 'student_number')->ignore($user->id)],
            'contact' => ['required', 'string', 'max:20'],
        ], [
            'student_number.unique' => 'This student number is already registered.',
        ]);

        $user->update($data);

        return redirect()->route('items.index')->with('status', 'Profile saved.');
    }
}
