@if (theme_option('real_estate_enable_filter_by_floor', 'yes') == 'yes')
    <div @class(['box-select form-search-floor',  $class ?? null])>
    <label for="floor" class="title-select">{{ __('Floors') }}</label>
        <select name="floor" id="floor" class="select_js">
            <option value="">{{ __('All') }}</option>
            @foreach(range(1, 5) as $i)
                <option value="{{ $i }}">
                    @if($i === 1)
                        {{ __('1 Floor') }}
                    @elseif($i < 5)
                        {{ $i }} {{ __('Floors') }}
                    @else
                        {{ $i }}+ {{ __('Floors') }}
                    @endif
                </option>
            @endforeach
        </select>
    </div>
@endif
