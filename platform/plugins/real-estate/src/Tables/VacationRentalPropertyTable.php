<?php

namespace Botble\RealEstate\Tables;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\Html;
use Botble\RealEstate\Enums\ModerationStatusEnum;
use Botble\RealEstate\Enums\PropertyStatusEnum;
use Botble\RealEstate\Models\VacationRental;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\BulkChanges\CreatedAtBulkChange;
use Botble\Table\BulkChanges\NameBulkChange;
use Botble\Table\BulkChanges\SelectBulkChange;
use Botble\Table\BulkChanges\StatusBulkChange;
use Botble\Table\Columns\Column;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\EnumColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\ImageColumn;
use Botble\Table\Columns\NameColumn;
use Botble\Table\Columns\StatusColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Relations\Relation as EloquentRelation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;

class VacationRentalPropertyTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(VacationRental::class)
            ->addActions([
                EditAction::make()->route('vacation-rental.edit'),
                DeleteAction::make()->route('vacation-rental.destroy'),
            ])
            ->setAjaxUrl(route('vacation-rental.properties'));
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('name', function (VacationRental $item) {
                if (! $this->hasPermission('vacation-rental.edit')) {
                    return BaseHelper::clean($item->name);
                }

                return Html::link(route('vacation-rental.edit', $item->getKey()), BaseHelper::clean($item->name));
            })
            ->editColumn('image', function (VacationRental $item) {
                $firstImage = null;
                if (is_array($item->images) && !empty($item->images) && isset($item->images[0])) {
                    $firstImage = $item->images[0];
                }
                return $this->displayThumbnail($firstImage);
            })
            ->editColumn('unique_id', function (VacationRental $item) {
                return $item->unique_id ?: '&mdash;';
            })
            ->editColumn('price', function (VacationRental $item) {
                return format_price($item->price, $item->currency) . ' / ' . __('night');
            })
            ->editColumn('minimum_stay', function (VacationRental $item) {
                return $item->minimum_stay ? $item->minimum_stay . ' ' . __('nights') : '&mdash;';
            })
            ->editColumn('maximum_guests', function (VacationRental $item) {
                return $item->maximum_guests ?: '&mdash;';
            })
            ->addColumn('availability_status', function (VacationRental $item) {
                // Simple status based on vacation rental status
                return '<span class="badge bg-success text-success-fg">Available</span>';
            })
            ->addColumn('operations', function (VacationRental $item) {
                $operations = '';

                try {
                    if ($this->hasPermission('vacation-rental.edit')) {
                        $operations .= Html::link(
                            route('vacation-rental.availability') . '?vacation_rental_id=' . $item->id,
                            '<i class="fa fa-calendar"></i> ' . __('Availability'),
                            ['class' => 'btn btn-sm btn-info me-1']
                        );
                    }
                } catch (\Exception $e) {
                    // Handle any route or permission errors gracefully
                    $operations = '<span class="text-muted">N/A</span>';
                }

                return $operations;
            })
            ->rawColumns(['image', 'availability_status', 'operations']);

        return $this->toJson($data);
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        $query = $this
            ->getModel()
            ->query()
            ->select([
                'id',
                'name',
                'images',
                'price',
                'currency_id',
                'status',
                'moderation_status',
                'created_at',
                'unique_id',
                'location',
                'minimum_stay',
                'maximum_guests',
                'check_in_time',
                'check_out_time',
            ])
            ->with(['currency']);

        return $this->applyScopes($query);
    }

    public function columns(): array
    {
        return [
            IdColumn::make(),
            ImageColumn::make()
                ->searchable(false)
                ->orderable(false),
            NameColumn::make()->route('vacation-rental.edit'),
            Column::make('price')
                ->title(trans('plugins/real-estate::property.form.price_per_night')),
            Column::make('minimum_stay')
                ->title(trans('plugins/real-estate::vacation-rental.minimum_stay')),
            Column::make('maximum_guests')
                ->title(trans('plugins/real-estate::vacation-rental.maximum_guests')),
            Column::make('availability_status')
                ->title(trans('plugins/real-estate::vacation-rental.availability_status'))
                ->searchable(false)
                ->orderable(false),
            Column::make('unique_id')
                ->title(trans('plugins/real-estate::property.unique_id')),
            CreatedAtColumn::make(),
            StatusColumn::make(),
            EnumColumn::make('moderation_status')
                ->title(trans('plugins/real-estate::property.moderation_status'))
                ->width(150),
        ];
    }

    public function buttons(): array
    {
        return $this->addCreateButton(route('vacation-rental.create'), 'vacation-rental.create');
    }

    public function bulkActions(): array
    {
        return [
            DeleteBulkAction::make()->permission('vacation-rental.destroy'),
        ];
    }

    public function getBulkChanges(): array
    {
        return [
            NameBulkChange::make(),
            StatusBulkChange::make(),
            SelectBulkChange::make()
                ->name('moderation_status')
                ->title(trans('plugins/real-estate::property.moderation_status'))
                ->searchable()
                ->choices(ModerationStatusEnum::labels()),
            CreatedAtBulkChange::make(),
        ];
    }

    public function getFilters(): array
    {
        return $this->getBulkChanges();
    }

    public function applyFilterCondition(EloquentBuilder|QueryBuilder|EloquentRelation $query, string $key, string $operator, ?string $value): EloquentRelation|EloquentBuilder|QueryBuilder
    {
        if ($key == 'status') {
            switch ($value) {
                case 'expired':
                    // @phpstan-ignore-next-line
                    return $query->expired();
                case 'active':
                    // @phpstan-ignore-next-line
                    return $query->active();
            }
        }

        return parent::applyFilterCondition($query, $key, $operator, $value);
    }

    public function saveBulkChangeItem(Model $item, string $inputKey, ?string $inputValue): Model|bool
    {
        if ($inputKey === 'moderation_status') {
            $item->moderation_status = $inputValue;
            $item->save();
        }

        return parent::saveBulkChangeItem($item, $inputKey, $inputValue);
    }
}
