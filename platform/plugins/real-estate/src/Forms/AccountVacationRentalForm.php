<?php

namespace Botble\RealEstate\Forms;

use Botble\Base\Forms\FieldOptions\ContentFieldOption;
use Botble\Base\Forms\FieldOptions\OnOffFieldOption;
use Botble\Base\Forms\Fields\OnOffField;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\RealEstate\Forms\Fields\CustomEditorField;
use Botble\RealEstate\Forms\Fields\MultipleUploadField;
use Botble\RealEstate\Http\Requests\AccountVacationRentalRequest;
use Botble\RealEstate\Models\VacationRental;

class AccountVacationRentalForm extends VacationRentalForm
{
    public function setup(): void
    {
        parent::setup();

        $this
            ->model(VacationRental::class)
            ->template('plugins/real-estate::account.forms.base')
            ->hasFiles()
            ->setValidatorClass(AccountVacationRentalRequest::class)
            ->remove('is_featured')
            ->remove('moderation_status')
            ->remove('content')
            ->remove('images[]')
            ->remove('never_expired')
            ->modify(
                'auto_renew',
                OnOffField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/real-estate::vacation-rental.renew_notice', [
                        'days' => RealEstateHelper::propertyExpiredDays(),
                    ]))
                    ->defaultValue(false),
                true
            )
            ->remove('author_id')
            ->addAfter(
                'description',
                'content',
                CustomEditorField::class,
                ContentFieldOption::make()
                    ->label(trans('plugins/real-estate::vacation-rental.form.content'))
                    ->required()
                    ->allowedShortcodes()
            )
            ->addAfter(
                'content',
                'images[]',
                MultipleUploadField::class,
                [
                    'label' => trans('plugins/real-estate::vacation-rental.form.images'),
                    'values' => $this->getModel() ? $this->getModel()->images : [],
                ]
            );
    }
}
