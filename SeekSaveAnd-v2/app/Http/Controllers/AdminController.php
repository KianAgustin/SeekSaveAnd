<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Models\Item;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /** Admin dashboard: every lost & found post in one table. */
    public function index(Request $request)
    {
        $status = $request->query('status');
        $type = $request->query('type');
        $q = trim((string) $request->query('q', ''));

        $items = Item::with('user')
            ->withCount(['claims as pending_claims' => fn ($c) => $c->where('status', 'pending')])
            ->when($status === 'open', fn ($query) => $query->where('resolved', false))
            ->when($status === 'resolved', fn ($query) => $query->where('resolved', true))
            ->when(in_array($type, ['lost', 'found'], true), fn ($query) => $query->where('type', $type))
            ->when($q !== '', function ($query) use ($q) {
                $like = '%' . $q . '%';
                $query->where(function ($w) use ($like) {
                    $w->where('title', 'ilike', $like)
                        ->orWhere('location', 'ilike', $like)
                        ->orWhereHas('user', fn ($u) => $u->where('name', 'ilike', $like)->orWhere('email', 'ilike', $like));
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'Open lost' => Item::where('type', 'lost')->where('resolved', false)->count(),
            'Open found' => Item::where('type', 'found')->where('resolved', false)->count(),
            'Resolved' => Item::where('resolved', true)->count(),
            'Pending claims' => Claim::where('status', 'pending')->count(),
            'Students' => User::where('role', 'user')->count(),
        ];

        return view('admin.index', compact('items', 'stats', 'status', 'type', 'q'));
    }

    public function users(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $users = User::withCount('items')
            ->when($q !== '', fn ($query) => $query->where(function ($w) use ($q) {
                $w->where('name', 'ilike', "%{$q}%")
                    ->orWhere('email', 'ilike', "%{$q}%")
                    ->orWhere('student_number', 'ilike', "%{$q}%");
            }))
            ->orderByDesc('role')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.users', compact('users', 'q'));
    }

    public function toggleRole(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->withErrors(['chat' => 'You cannot change your own role.']);
        }

        $user->forceFill(['role' => $user->isAdmin() ? 'user' : 'admin'])->save();

        return back()->with('status', $user->name . ' is now ' . ($user->isAdmin() ? 'an admin.' : 'a regular user.'));
    }
}
