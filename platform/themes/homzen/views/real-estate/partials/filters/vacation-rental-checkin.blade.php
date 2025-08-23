<div @class(['form-group-1 form-style', $class ?? null])>
    <label class="title-user fw-5">{{ __('Check-in Date') }}</label>
    <div class="group-select">
        <input type="date"
               name="check_in_date"
               id="check_in_date"
               class="form-control vacation-rental-checkin-date"
               value="{{ request()->input('check_in_date') }}"
               min="{{ date('Y-m-d') }}"
               placeholder="{{ __('Select check-in date') }}">
    </div>
</div>
