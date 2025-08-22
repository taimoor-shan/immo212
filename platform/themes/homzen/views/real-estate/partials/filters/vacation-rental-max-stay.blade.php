@php
    $maxStayOptions = [
        '' => __('Any Duration'),
        3 => '3 ' . __('Nights'),
        7 => '1 ' . __('Week'),
        14 => '2 ' . __('Weeks'),
        30 => '1 ' . __('Month'),
        60 => '2 ' . __('Months'),
        90 => '3 ' . __('Months'),
    ];
@endphp

<div @class(['form-group-1 form-style', $class ?? null])>
    <label class="title-user fw-6">{{ __('Maximum Stay') }}</label>
    <div class="group-select">
        <select name="maximum_stay" class="nice-select" data-bb-toggle="select-dropdown">
            @foreach($maxStayOptions as $value => $label)
                <option value="{{ $value }}" @selected(request()->input('maximum_stay') == $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>
</div>
