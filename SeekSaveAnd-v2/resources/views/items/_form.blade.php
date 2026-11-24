{{-- Shared by create and edit. $item is null when creating. --}}
@php
    $categories = config('seeksave.categories');
    $locations  = config('seeksave.locations');

    $existingCatIsCustom = $item && $item->category && ! in_array($item->category, $categories, true);
    $existingLocIsCustom = $item && $item->location && ! in_array($item->location, $locations, true);

    $selectedCategory = old('category', $existingCatIsCustom ? 'Other' : $item?->category);
    $otherCategoryVal = old('other_category', $existingCatIsCustom ? $item->category : '');

    $selectedLocation = old('location', $existingLocIsCustom ? 'Other' : $item?->location);
    $otherLocationVal = old('other_location', $existingLocIsCustom ? $item->location : '');
@endphp

<form method="POST" enctype="multipart/form-data" novalidate
      action="{{ $item ? route('items.update', $item) : route('items.store') }}">
    @csrf

    <div class="field">
        <label>Listing Type</label>
        <div class="row">
            <label class="btn ghost inline small" style="cursor:pointer">
                <input type="radio" name="type" value="lost" @checked(old('type', $type) === 'lost')> SEEKING ITEM
            </label>
            <label class="btn ghost inline small" style="cursor:pointer">
            <input type="radio" name="type" value="found" @checked(old('type', $type) === 'found')> SAVING ITEM
            </label>
        </div>
        @error('type') <div class="error-text">{{ $message }}</div> @enderror
    </div>

    <div class="field">
        <label for="title">Title</label>
        <input class="input @error('title') is-invalid @enderror" id="title" type="text" name="title"
               value="{{ old('title', $item?->title) }}" placeholder="Title e.g. iPhone 14 Pro" maxlength="100">
        @error('title') <div class="error-text">{{ $message }}</div> @enderror
    </div>

    <div class="field">
        <label for="category">Category</label>
        <select class="input @error('category') is-invalid @enderror" id="category" name="category"
                onchange="toggleOtherField('category', 'other-category-box')">
            <option value="">Category</option>
            @foreach ($categories as $c)
                <option value="{{ $c }}" @selected($selectedCategory === $c)>{{ $c }}</option>
            @endforeach
        </select>
        @error('category') <div class="error-text">{{ $message }}</div> @enderror

        <div id="other-category-box" style="margin-top: 8px; {{ $selectedCategory === 'Other' ? '' : 'display:none;' }}">
            <input class="input @error('other_category') is-invalid @enderror" type="text" name="other_category"
                   value="{{ $otherCategoryVal }}" placeholder="Specify category (e.g. Tumbler, Umbrella)" maxlength="50">
            @error('other_category') <div class="error-text">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="field">
        <label for="location">Location</label>
        <select class="input @error('location') is-invalid @enderror" id="location" name="location"
                onchange="toggleOtherField('location', 'other-location-box')">
            <option value="">Choose a campus spot</option>
            @foreach ($locations as $l)
                <option value="{{ $l }}" @selected($selectedLocation === $l)>{{ $l }}</option>
            @endforeach
        </select>
        @error('location') <div class="error-text">{{ $message }}</div> @enderror

        <div id="other-location-box" style="margin-top: 8px; {{ $selectedLocation === 'Other' ? '' : 'display:none;' }}">
            <input class="input @error('other_location') is-invalid @enderror" type="text" name="other_location"
                   value="{{ $otherLocationVal }}" placeholder="Specify exact campus spot (e.g. Gate 2 Guardhouse)" maxlength="80">
            @error('other_location') <div class="error-text">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="field">
        <label for="description">Description</label>
        <textarea class="input @error('description') is-invalid @enderror" id="description" name="description"
                  placeholder="Color, marks, extra details..." maxlength="1000">{{ old('description', $item && $item->description !== 'No extra details provided.' ? $item->description : '') }}</textarea>
        @error('description') <div class="error-text">{{ $message }}</div> @enderror
    </div>

    <div class="field">
        <label for="photo">Photo (optional, JPG/PNG/WEBP up to 8 MB)</label>
        <p class="muted" style="margin:0 0 6px;font-size:.85rem">Item photos are hidden from the public feed for safety. Only you, claimers with proof, and admins can see them.</p>
        @if ($item && $item->photoUrl())
            <img class="form-photo" src="{{ $item->photoUrl() }}" alt="Current photo">
            <label class="check"><input type="checkbox" name="remove_photo" value="1"> <span>Remove current photo</span></label>
        @endif

        <input class="input @error('photo') is-invalid @enderror" id="photo" type="file" name="photo"
               accept="image/png,image/jpeg,image/webp" capture="environment">

        <div id="preview-wrapper" hidden style="margin-top: 10px;">
            <img class="form-photo" id="preview" alt="Selected photo preview">
            <button type="button" id="remove-selected-photo" class="btn ghost small inline" style="color: var(--lost); border-color: var(--lost);">
                Remove photo
            </button>
        </div>
        @error('photo') <div class="error-text">{{ $message }}</div> @enderror
    </div>

    <div class="field">
        <label>Return preference</label>
        <p class="muted" style="margin:0 0 8px;font-size:.85rem">Choose how you want the return handled when someone contacts you.</p>
        <label class="check" style="display:block;margin-bottom:8px">
            <input type="radio" name="wants_guidance" value="1" @checked(old('wants_guidance', $item?->wants_guidance ? '1' : '0') === '1')>
            <span><strong>Guided by admin</strong> — admin helps arrange office meetup</span>
        </label>
        <label class="check" style="display:block">
            <input type="radio" name="wants_guidance" value="0" @checked(old('wants_guidance', $item?->wants_guidance ? '1' : '0') === '0')>
            <span><strong>Talk independently</strong> — message the other person directly</span>
        </label>
        <p class="muted" style="margin-top:6px;font-size:.8rem">If either of you chooses guided, the chat will still be guided by an admin.</p>
    </div>

    <div class="row" style="margin-top: 18px;">
        <button class="btn" type="submit" style="flex: 1;">{{ $item ? 'Save changes' : 'Post item' }}</button>
        <a class="btn ghost inline" href="{{ $item ? route('items.show', $item) : route('items.index') }}">Cancel</a>
    </div>
</form>

@push('scripts')
<script>
function toggleOtherField(selectId, wrapperId) {
    var select = document.getElementById(selectId);
    var box = document.getElementById(wrapperId);
    box.style.display = (select.value === 'Other') ? 'block' : 'none';
}

(function () {
    var photoInput = document.getElementById('photo');
    var previewWrapper = document.getElementById('preview-wrapper');
    var previewImg = document.getElementById('preview');
    var removeBtn = document.getElementById('remove-selected-photo');

    photoInput.addEventListener('change', function () {
        var file = this.files[0];
        if (!file) {
            previewWrapper.hidden = true;
            previewImg.src = '';
            return;
        }
        previewImg.src = URL.createObjectURL(file);
        previewWrapper.hidden = false;
    });

    removeBtn.addEventListener('click', function () {
        photoInput.value = '';
        previewImg.src = '';
        previewWrapper.hidden = true;
    });
})();
</script>
@endpush
