{{-- Shared by create and edit. $item is null when creating. --}}
<form method="POST" enctype="multipart/form-data" novalidate
      action="{{ $item ? route('items.update', $item) : route('items.store') }}">
    @csrf
    <div class="field">
        <label>Listing Type</label>
        <div class="row">
            <label class="btn ghost inline small" style="cursor:pointer"><input type="radio" name="type" value="lost" @checked(old('type', $type) === 'lost')> I Lost It</label>
            <label class="btn ghost inline small" style="cursor:pointer"><input type="radio" name="type" value="found" @checked(old('type', $type) === 'found')> I Found It</label>
        </div>
        @error('type') <div class="error-text">{{ $message }}</div> @enderror
    </div>
    <div class="field">
        <label for="title">Title</label>
        <input class="input" id="title" type="text" name="title" value="{{ old('title', $item?->title) }}" placeholder="Title e.g. iPhone 14 Pro" maxlength="100">
        @error('title') <div class="error-text">{{ $message }}</div> @enderror
    </div>
    <div class="field">
        <label for="category">Category</label>
        <select class="input" id="category" name="category">
            <option value="">Category</option>
            @foreach (config('seeksave.categories') as $c) <option value="{{ $c }}" @selected(old('category', $item?->category) === $c)>{{ $c }}</option> @endforeach
        </select>
        @error('category') <div class="error-text">{{ $message }}</div> @enderror
    </div>
    <div class="field">
        <label for="location">Location</label>
        <select class="input" id="location" name="location">
            <option value="">Choose a campus spot</option>
            @foreach (config('seeksave.locations') as $l) <option value="{{ $l }}" @selected(old('location', $item?->location) === $l)>{{ $l }}</option> @endforeach
        </select>
        @error('location') <div class="error-text">{{ $message }}</div> @enderror
    </div>
    <div class="field">
        <label for="description">Description</label>
        <textarea class="input" id="description" name="description" placeholder="Color, marks, extra details..." maxlength="1000">{{ old('description', $item && $item->description !== 'No extra details provided.' ? $item->description : '') }}</textarea>
        @error('description') <div class="error-text">{{ $message }}</div> @enderror
    </div>
    <div class="field">
        <label for="photo">Photo (optional, JPG/PNG/WEBP up to 8 MB)</label>
        @if ($item && $item->photoUrl())
            <img class="form-photo" src="{{ $item->photoUrl() }}" alt="Current photo">
            <label class="check"><input type="checkbox" name="remove_photo" value="1"> <span>Remove current photo</span></label>
        @endif
        <input class="input" id="photo" type="file" name="photo" accept="image/png,image/jpeg,image/webp" capture="environment">
        <img class="form-photo" id="preview" alt="" hidden>
        @error('photo') <div class="error-text">{{ $message }}</div> @enderror
    </div>
    <button class="btn" type="submit">{{ $item ? 'Save changes' : 'Post item' }}</button>
</form>

@push('scripts')
<script>
document.getElementById('photo').addEventListener('change', function () {
    var p = document.getElementById('preview'), f = this.files[0];
    if (!f) { p.hidden = true; return; }
    p.src = URL.createObjectURL(f); p.hidden = false;
});
</script>
@endpush
