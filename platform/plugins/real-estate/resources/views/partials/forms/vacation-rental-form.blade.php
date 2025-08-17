@extends('core/base::forms.form')

@section('form_end')
    @if($form->getModel()?->is_pending_moderation && is_in_admin(true))
        <x-core::modal.action
            id="approve-vacation-rental-modal"
            type="success"
            :title="trans('plugins/real-estate::vacation-rental.status_moderation.approve_title')"
            :description="trans('plugins/real-estate::vacation-rental.status_moderation.approve_message')"
            :form-action="route('vacation-rental.approve', $form->getModel())"
        >
            <x-slot:footer>
                <div class="w-100">
                    <div class="row">
                        <div class="col">
                            <x-core::button type="submit" color="success" class="w-100">
                                {{ trans('core/base::tables.submit') }}
                            </x-core::button>
                        </div>
                        <div class="col">
                            <x-core::button type="button" class="w-100" data-bs-dismiss="modal">
                                {{ trans('core/base::base.close') }}
                            </x-core::button>
                        </div>
                    </div>
                </div>
            </x-slot:footer>
        </x-core::modal.action>

        <x-core::modal.action
            id="reject-vacation-rental-modal"
            type="danger"
            :title="trans('plugins/real-estate::vacation-rental.status_moderation.reject_title')"
            :form-action="route('vacation-rental.reject', $form->getModel())"
        >
            <div class="text-muted">{{ trans('plugins/real-estate::vacation-rental.status_moderation.reject_message') }}</div>

            <textarea
                name="reason"
                class="form-control mt-3"
                placeholder="{{ trans('plugins/real-estate::vacation-rental.status_moderation.reject_reason') }}"
            ></textarea>

            <x-slot:footer>
                <div class="w-100">
                    <div class="row">
                        <div class="col">
                            <x-core::button type="submit" color="danger" class="w-100">
                                {{ trans('core/base::tables.submit') }}
                            </x-core::button>
                        </div>
                        <div class="col">
                            <x-core::button type="button" class="w-100" data-bs-dismiss="modal">
                                {{ trans('core/base::base.close') }}
                            </x-core::button>
                        </div>
                    </div>
                </div>
            </x-slot:footer>
        </x-core::modal.action>
    @endif
@endsection
