@php
    $layout = 'plugins/real-estate::themes.dashboard.layouts.master';
@endphp

@extends('core/table::table')

@section('content')
    @if (request()->routeIs('public.account.projects.*'))
        <div class="mb-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">{{ trans('plugins/real-estate::property.moderation_status') }}</label>
                    <select name="moderation_status" class="form-select" onchange="this.form.submit()">
                        <option value="">{{ __('All') }}</option>
                        <option value="pending" {{ request('moderation_status') === 'pending' ? 'selected' : '' }}>{{ trans('plugins/real-estate::property.moderation-statuses.pending') }}</option>
                        <option value="approved" {{ request('moderation_status') === 'approved' ? 'selected' : '' }}>{{ trans('plugins/real-estate::property.moderation-statuses.approved') }}</option>
                        <option value="rejected" {{ request('moderation_status') === 'rejected' ? 'selected' : '' }}>{{ trans('plugins/real-estate::property.moderation-statuses.rejected') }}</option>
                    </select>
                </div>
            </form>
        </div>
    @endif

    @parent
@stop

@push('footer')
    <x-core::modal.action
        class="modal-confirm-renew"
        :title="__('Renew confirmation')"
:description="(RealEstateHelper::isEnabledCreditsSystem()
            ? __('Are you sure you want to renew this item, it will takes 1 credit from your credits')
            : __('Are you sure you want to renew this item')) . '?'"
        :submit-button-label="__('Yes')"
        :submit-button-attrs="['class' => 'button-confirm-renew']"
    />
@endpush
