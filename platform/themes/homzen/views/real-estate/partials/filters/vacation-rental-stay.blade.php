@php
    $stayOptions = [
        '' => __('Any Stay'),
        1 => '1 ' . __('Night'),
        2 => '2 ' . __('Nights'),
        3 => '3 ' . __('Nights'),
        7 => '1 ' . __('Week'),
        14 => '2 ' . __('Weeks'),
        30 => '1 ' . __('Month'),
    ];
@endphp

<div @class(['form-group-1 form-style', $class ?? null])>
    <label class="title-user fw-5">{{ __('Minimum Stay') }}</label>
    <div class="group-select">
        <select name="minimum_stay" class="select_js nice-select" data-bb-toggle="select-dropdown">
            @foreach($stayOptions as $value => $label)
                <option value="{{ $value }}" @selected(request()->input('minimum_stay') == $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>
</div>
