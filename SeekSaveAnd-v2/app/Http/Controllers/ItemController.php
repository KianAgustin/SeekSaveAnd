<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Services\SupabaseStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ItemController extends Controller
{
    /** Home: search + filter by location + Lost/Found tabs. */
    public function index(Request $request)
    {
        $type = $request->query('type');
        $location = $request->query('location');
        $q = trim((string) $request->query('q', ''));

        $items = Item::with('user')
            ->when(in_array($type, ['lost', 'found'], true), fn ($query) => $query->where('type', $type))
            ->when($location, fn ($query) => $query->where('location', $location))
            ->when($q !== '', function ($query) use ($q) {
                $like = '%' . $q . '%';
                $query->where(function ($w) use ($like) {
                    $w->where('title', 'ilike', $like)
                        ->orWhere('description', 'ilike', $like)
                        ->orWhere('location', 'ilike', $like)
                        ->orWhere('category', 'ilike', $like);
                });
            })
            ->orderBy('resolved')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('items.index', compact('items', 'type', 'location', 'q'));
    }

    public function create(Request $request)
    {
        $type = $request->query('type') === 'found' ? 'found' : 'lost';

        return view('items.create', ['item' => null, 'type' => $type]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            try {
                $photoPath = SupabaseStorage::upload($request->file('photo'));
            } catch (\Throwable $e) {
                report($e);

                return back()->withInput()->withErrors(['photo' => 'The photo could not be uploaded. Please try again.']);
            }
        }

        $item = $request->user()->items()->create([
            'type' => $data['type'],
            'title' => trim($data['title']),
            'category' => $data['category'],
            'location' => $data['location'],
            'description' => $this->description($data),
            'photo_path' => $photoPath,
        ]);

        return redirect()->route('items.show', $item)->with('status', 'Your item was posted.');
    }

    public function show(Request $request, Item $item)
    {
        $user = $request->user();
        $item->load('user');

        $isOwner = $item->user_id === $user->id;
        $canManage = $item->canBeManagedBy($user);
        $isSaved = $user->savedItems()->where('items.id', $item->id)->exists();
        $myClaim = $isOwner ? null : $item->claims()->where('user_id', $user->id)->first();
        $claims = $canManage ? $item->claims()->with('user')->latest()->get() : collect();

        return view('items.show', compact('item', 'isOwner', 'canManage', 'isSaved', 'myClaim', 'claims'));
    }

    public function edit(Request $request, Item $item)
    {
        abort_unless($item->canBeManagedBy($request->user()), 403);

        return view('items.edit', ['item' => $item, 'type' => $item->type]);
    }

    public function update(Request $request, Item $item): RedirectResponse
    {
        abort_unless($item->canBeManagedBy($request->user()), 403);

        $data = $this->validated($request);
        $photoPath = $item->photo_path;

        if ($request->hasFile('photo')) {
            try {
                $new = SupabaseStorage::upload($request->file('photo'));
            } catch (\Throwable $e) {
                report($e);

                return back()->withInput()->withErrors(['photo' => 'The photo could not be uploaded. Please try again.']);
            }
            SupabaseStorage::delete($photoPath);
            $photoPath = $new;
        } elseif ($request->boolean('remove_photo')) {
            SupabaseStorage::delete($photoPath);
            $photoPath = null;
        }

        $item->update([
            'type' => $data['type'],
            'title' => trim($data['title']),
            'category' => $data['category'],
            'location' => $data['location'],
            'description' => $this->description($data),
            'photo_path' => $photoPath,
        ]);

        return redirect()->route('items.show', $item)->with('status', 'Post updated.');
    }

    public function destroy(Request $request, Item $item): RedirectResponse
    {
        abort_unless($item->canBeManagedBy($request->user()), 403);

        SupabaseStorage::delete($item->photo_path);
        $item->delete();

        return redirect()
            ->route($request->user()->isAdmin() ? 'admin.index' : 'items.index')
            ->with('status', 'Post deleted.');
    }

    /** The poster or an admin can mark an item resolved. */
    public function resolve(Request $request, Item $item): RedirectResponse
    {
        abort_unless($item->canBeManagedBy($request->user()), 403, 'Only the poster or an admin can mark this item resolved.');

        if (! $item->resolved) {
            $item->update(['resolved' => true, 'resolved_at' => now()]);
        }

        return back()->with('status', 'Item marked as resolved.');
    }

    /** Admins can reopen an item that was resolved by mistake. */
    public function reopen(Request $request, Item $item): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        $item->update(['resolved' => false, 'resolved_at' => null]);

        return back()->with('status', 'Item reopened.');
    }

    public function toggleSave(Request $request, Item $item): RedirectResponse
    {
        $request->user()->savedItems()->toggle($item->id);

        return back();
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'type' => ['required', Rule::in(['lost', 'found'])],
            'title' => ['required', 'string', 'max:100'],
            'category' => ['required', Rule::in(config('seeksave.categories'))],
            'location' => ['required', Rule::in(config('seeksave.locations'))],
            'description' => ['nullable', 'string', 'max:1000'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
        ], [
            'title.required' => 'Please add a title.',
            'location.required' => 'Please choose a location.',
            'category.required' => 'Please choose a category.',
            'photo.image' => 'The photo must be an image (JPG, PNG or WEBP).',
            'photo.max' => 'The photo must be 8 MB or smaller.',
            'photo.uploaded' => 'The photo is too large for the server. Use a smaller photo (under 8 MB).',
        ]);
    }

    private function description(array $data): string
    {
        $d = trim($data['description'] ?? '');

        return $d !== '' ? $d : 'No extra details provided.';
    }
}
