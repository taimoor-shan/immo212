<div @class(['form-group-1 form-style', $class ?? null])>
    <label class="title-user fw-5">{{ __('Check-out Date') }}</label>
    <div class="group-select">
        <input type="date"
               name="check_out_date"
               id="check_out_date"
               class="form-control vacation-rental-checkout-date"
               value="{{ request()->input('check_out_date') }}"
               min="{{ date('Y-m-d', strtotime('+1 day')) }}"
               placeholder="{{ __('Select check-out date') }}">
    </div>
</div>
