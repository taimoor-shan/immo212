@if (theme_option('real_estate_enable_filter_by_block', 'yes') == 'yes')
    <div @class(['box-select form-search-block',  $class ?? null])>
    <label for="block" class="title-select fw-5">{{ __('Blocks') }}</label>
        <select name="block" id="block" class="select_js">
            <option value="">{{ __('All') }}</option>
            @foreach(range(1, 5) as $i)
                <option value="{{ $i }}">
                    @if($i === 1)
                        {{ __('1 Block') }}
                    @elseif($i < 5)
                        {{ $i }} {{ __('Blocks') }}
                    @else
                        {{ $i }}+ {{ __('Blocks') }}
                    @endif
                </option>
            @endforeach
        </select>
    </div>
@endif
