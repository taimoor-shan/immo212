@php
    $guestOptions = [
        '' => __('Any Guests'),
        1 => '1 ' . __('Guest'),
        2 => '2 ' . __('Guests'),
        3 => '3 ' . __('Guests'),
        4 => '4 ' . __('Guests'),
        5 => '5 ' . __('Guests'),
        6 => '6 ' . __('Guests'),
        8 => '8+ ' . __('Guests'),
        10 => '10+ ' . __('Guests'),
        12 => '12+ ' . __('Guests'),
    ];
@endphp

<div @class(['form-group-1 form-style', $class ?? null])>
    <label class="title-user fw-6">{{ __('Guests') }}</label>
    <div class="group-select">
        <select name="maximum_guests" class="nice-select" data-bb-toggle="select-dropdown">
            @foreach($guestOptions as $value => $label)
                <option value="{{ $value }}" @selected(request()->input('maximum_guests') == $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>
</div>
