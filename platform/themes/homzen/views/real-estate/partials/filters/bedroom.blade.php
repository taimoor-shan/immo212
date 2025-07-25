@if (theme_option('real_estate_enable_filter_by_bedroom', 'yes') == 'yes')
    <div @class(['box-select form-search-bedroom form-style',  $class ?? null])>
    <label for="bedroom" class="title-select">{{ __('Bedrooms') }}</label>
        <select name="bedroom" id="bedroom" class="select_js nice-select">
            <option value="">{{ __('All') }}</option>
            @foreach(range(1, 5) as $i)
                <option value="{{ $i }}">
                    @if($i < 5)
                        {{ $i === 1 ? __('1 Bedroom') : __(':number Bedrooms', ['number' => $i]) }}
                    @else
                        {{ __(':number+ Bedrooms', ['number' => $i]) }}
                    @endif
                </option>
            @endforeach
        </select>
    </div>
@endif
