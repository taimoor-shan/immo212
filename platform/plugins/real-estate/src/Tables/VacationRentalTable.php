<?php

namespace Botble\RealEstate\Tables;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\Html;
use Botble\RealEstate\Models\VacationRental;
use Botble\RealEstate\Enums\VacationRentalStatusEnum;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\BulkChanges\CreatedAtBulkChange;
use Botble\Table\BulkChanges\NameBulkChange;
use Botble\Table\BulkChanges\StatusBulkChange;
use Botble\Table\Columns\Column;
use Botble\Table\Columns\CreatedAtColumn;
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

class VacationRentalTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(VacationRental::class)
            ->addActions([
                EditAction::make()->route('vacation-rental.edit'),
                DeleteAction::make()->route('vacation-rental.destroy'),
            ]);
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
                return $this->displayThumbnail($item->image);
            })
            ->editColumn('price', function (VacationRental $item) {
                return $item->price_format . ' / ' . __('night');
            })
            ->editColumn('minimum_stay', function (VacationRental $item) {
                return $item->minimum_stay ? $item->minimum_stay . ' ' . trans_choice('night|nights', $item->minimum_stay) : '—';
            })
            ->editColumn('maximum_guests', function (VacationRental $item) {
                return $item->maximum_guests ? $item->maximum_guests . ' ' . trans_choice('guest|guests', $item->maximum_guests) : '—';
            })
            ->editColumn('availability_status', function (VacationRental $item) {
                $totalBookings = $item->bookings()->active()->count();
                $upcomingBookings = $item->bookings()->active()
                    ->where('check_in_date', '>=', now())
                    ->count();
                
                if ($upcomingBookings > 0) {
                    return Html::tag('span', __('Booked (:count)', ['count' => $upcomingBookings]), ['class' => 'badge bg-warning']);
                }
                
                return Html::tag('span', __('Available'), ['class' => 'badge bg-success']);
            })
            ->editColumn('unique_id', function (VacationRental $item) {
                return $item->unique_id ?: '—';
            })
            ->editColumn('checkbox', function (VacationRental $item) {
                return $this->getCheckbox($item->getKey());
            })
            ->editColumn('created_at', function (VacationRental $item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->editColumn('status', function (VacationRental $item) {
                return $item->status->toHtml();
            })
            ->addColumn('operations', function (VacationRental $item) {
                return $this->getOperations('vacation-rental.edit', 'vacation-rental.destroy', $item);
            });

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
                'minimum_stay',
                'maximum_guests',
                'unique_id',
                'created_at',
                'status',
                'moderation_status',
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
            StatusBulkChange::make()->choices(VacationRentalStatusEnum::labels()),
            CreatedAtBulkChange::make(),
        ];
    }

    public function getFilters(): array
    {
        return $this->getBulkChanges();
    }

    public function renderTable(array $data = [], array $mergeData = []): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Symfony\Component\HttpFoundation\Response
    {
        if ($this->isEmpty()) {
            return view('plugins/real-estate::vacation-rental.intro');
        }

        return parent::renderTable($data, $mergeData);
    }

    public function getDefaultButtons(): array
    {
        return [
            'export',
            'reload',
        ];
    }
}
