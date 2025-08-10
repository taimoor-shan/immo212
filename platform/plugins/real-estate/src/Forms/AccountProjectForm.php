<?php

namespace Botble\RealEstate\Forms;

use Botble\Base\Forms\FieldOptions\ContentFieldOption;
use Botble\Base\Forms\FormFieldOptions;
use Botble\RealEstate\Forms\Fields\CustomEditorField;
use Botble\RealEstate\Forms\Fields\MultipleUploadField;
use Botble\RealEstate\Http\Requests\AccountProjectRequest;
use Botble\RealEstate\Models\Project;

class AccountProjectForm extends ProjectForm
{
    public function setup(): void
    {
        parent::setup();

        $this
            ->model(Project::class)
            ->template('plugins/real-estate::account.forms.base')
            ->hasFiles()
            ->setValidatorClass(AccountProjectRequest::class)
            ->remove('is_featured')
            ->remove('featured_priority')
            ->remove('moderation_status')
            ->remove('author_id')
            ->remove('images[]')
            ->remove('content')
            ->addAfter(
                'description',
                'content',
                CustomEditorField::class,
                ContentFieldOption::make()
                    ->label(trans('plugins/real-estate::property.form.content'))
                    ->required()
            )
            ->addAfter(
                'content',
                'images',
                MultipleUploadField::class,
                FormFieldOptions::make()
                    ->label(trans('plugins/real-estate::property.form.images'))
            );
    }
}
