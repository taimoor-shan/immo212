@php
    $categories = get_property_categories([
        'indent' => '↳',
        'conditions' => ['status' => \Botble\Base\Enums\BaseStatusEnum::PUBLISHED],
    ]);
@endphp

<div class="form-group-3 form-style form-search-category" @if (theme_option('real_estate_enable_advanced_search', 'yes') != 'yes') style="border: none" @endif>
    <label for="category_id">{{ __('Category') }}</label>
    <div class="group-select">
        <select name="category_id" id="category_id" class="select_js">
            <option value="">{{ __('Property Type') }}</option>
            @foreach($categories as $category)
                <option value="{{ $category->getKey() }}"@selected(request()->query('category_id') == $category->getKey())>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
</div>
