<?php

namespace Botble\RealEstate\Tables;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\Html;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\Columns\Column;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\NameColumn;
use Botble\Table\Columns\StatusColumn;
use Botble\RealEstate\Models\VacationRental;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

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

    public function __construct(DataTables $table, UrlGenerator $urlGenerator)
    {
        parent::__construct($table, $urlGenerator);

        $this->setOption('id', 'plugins-vacation-rental-table');
        $this->setOption('class', 'table table-striped table-hover vertical-middle managed-table');
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('name', function (VacationRental $item) {
                if (Auth::user()->hasPermission('vacation-rental.edit')) {
                    return Html::link(route('vacation-rental.edit', $item->id), BaseHelper::clean($item->name));
                }

                return BaseHelper::clean($item->name);
            })
            ->editColumn('image', function (VacationRental $item) {
                if (!empty($item->images) && is_array($item->images)) {
                    return $this->displayThumbnail($item->images[0] ?? null, ['width' => 50]);
                }
                return '&mdash;';
            })
            ->editColumn('price', function (VacationRental $item) {
                return format_price($item->price) . ' / ' . __('night');
            })
            ->editColumn('location', function (VacationRental $item) {
                return $item->location ?: '&mdash;';
            })
            ->editColumn('status', function (VacationRental $item) {
                return BaseHelper::renderBadge(ucfirst($item->status), $item->status === 'published' ? 'success' : 'warning');
            })
            ->editColumn('moderation_status', function (VacationRental $item) {
                $color = match ($item->moderation_status) {
                    'approved' => 'success',
                    'pending' => 'warning',
                    'rejected' => 'danger',
                    default => 'info',
                };
                return BaseHelper::renderBadge(ucfirst($item->moderation_status), $color);
            })
            ->rawColumns(['name', 'image', 'price', 'status', 'moderation_status']);

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
                'location',
                'city_id',
                'state_id',
                'status',
                'moderation_status',
                'is_featured',
                'created_at',
                'updated_at',
            ]);

        return $this->applyScopes($query);
    }

    public function columns(): array
    {
        return [
            IdColumn::make(),
            Column::make('image')
                ->title(trans('plugins/real-estate::vacation-rental.image'))
                ->alignCenter()
                ->width(70),
            NameColumn::make('name')
                ->route('vacation-rental.edit'),
            Column::make('location')
                ->title(trans('plugins/real-estate::vacation-rental.location'))
                ->alignStart(),
            Column::make('price')
                ->title(trans('plugins/real-estate::vacation-rental.price'))
                ->alignCenter(),
            StatusColumn::make('status'),
            Column::make('moderation_status')
                ->title(trans('plugins/real-estate::vacation-rental.moderation_status'))
                ->alignCenter(),
            CreatedAtColumn::make(),
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
            'name' => [
                'title' => trans('core/base::tables.name'),
                'type' => 'text',
                'validate' => 'required|max:120',
            ],
            'status' => [
                'title' => trans('core/base::tables.status'),
                'type' => 'select',
                'choices' => BaseStatusEnum::labels(),
                'validate' => 'required|in:' . implode(',', BaseStatusEnum::values()),
            ],
            'moderation_status' => [
                'title' => trans('plugins/real-estate::vacation-rental.moderation_status'),
                'type' => 'select',
                'choices' => [
                    'approved' => trans('plugins/real-estate::vacation-rental.approved'),
                    'pending' => trans('plugins/real-estate::vacation-rental.pending'),
                    'rejected' => trans('plugins/real-estate::vacation-rental.rejected'),
                ],
                'validate' => 'required|in:approved,pending,rejected',
            ],
            'created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type' => 'datePicker',
            ],
        ];
    }

    public function getFilters(): array
    {
        return [
            'name' => [
                'title' => trans('core/base::tables.name'),
                'type' => 'text',
                'validate' => 'required|max:120',
            ],
            'status' => [
                'title' => trans('core/base::tables.status'),
                'type' => 'select',
                'choices' => ['' => trans('core/base::tables.all')] + BaseStatusEnum::labels(),
                'validate' => 'sometimes|nullable|in:' . implode(',', BaseStatusEnum::values()),
            ],
            'moderation_status' => [
                'title' => trans('plugins/real-estate::vacation-rental.moderation_status'),
                'type' => 'select',
                'choices' => [
                    '' => trans('core/base::tables.all'),
                    'approved' => trans('plugins/real-estate::vacation-rental.approved'),
                    'pending' => trans('plugins/real-estate::vacation-rental.pending'),
                    'rejected' => trans('plugins/real-estate::vacation-rental.rejected'),
                ],
                'validate' => 'sometimes|nullable|in:approved,pending,rejected',
            ],
            'created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type' => 'datePicker',
            ],
        ];
    }

}
