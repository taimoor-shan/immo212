<?php

namespace Botble\RealEstate\Tables;

use Botble\Base\Facades\Html;
use Botble\RealEstate\Models\VacationRentalBooking;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\BulkChanges\CreatedAtBulkChange;
use Botble\Table\BulkChanges\SelectBulkChange;
use Botble\Table\Columns\Column;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\IdColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Relations\Relation as EloquentRelation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;

class VacationRentalBookingTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(VacationRentalBooking::class)
            ->addActions([
                EditAction::make()->route('vacation-rental.booking.edit'),
                DeleteAction::make()->route('vacation-rental.booking.destroy'),
            ]);
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('booking_number', function (VacationRentalBooking $item) {
                if (! $this->hasPermission('vacation-rental.booking.edit')) {
                    return $item->booking_number;
                }

                return Html::link(route('vacation-rental.booking.edit', $item->getKey()), $item->booking_number);
            })
            ->editColumn('property_name', function (VacationRentalBooking $item) {
                return $item->property ? $item->property->name : '&mdash;';
            })
            ->editColumn('guest_name', function (VacationRentalBooking $item) {
                return $item->guest_name;
            })
            ->editColumn('check_in_date', function (VacationRentalBooking $item) {
                return $item->check_in_date->format('M d, Y');
            })
            ->editColumn('check_out_date', function (VacationRentalBooking $item) {
                return $item->check_out_date->format('M d, Y');
            })
            ->editColumn('nights', function (VacationRentalBooking $item) {
                return $item->calculateNights() . ' ' . __('nights');
            })
            ->editColumn('guests_count', function (VacationRentalBooking $item) {
                return $item->guests_count . ' ' . __('guests');
            })
            ->editColumn('total_amount', function (VacationRentalBooking $item) {
                return format_price($item->total_amount, $item->property?->currency);
            })
            ->editColumn('status', function (VacationRentalBooking $item) {
                $statusLabels = [
                    VacationRentalBooking::STATUS_PENDING => ['class' => 'warning', 'label' => __('Pending')],
                    VacationRentalBooking::STATUS_CONFIRMED => ['class' => 'success', 'label' => __('Confirmed')],
                    VacationRentalBooking::STATUS_CANCELLED => ['class' => 'danger', 'label' => __('Cancelled')],
                    VacationRentalBooking::STATUS_COMPLETED => ['class' => 'info', 'label' => __('Completed')],
                ];

                $status = $statusLabels[$item->status] ?? ['class' => 'secondary', 'label' => $item->status];
                
                return '<span class="badge bg-' . $status['class'] . '">' . $status['label'] . '</span>';
            })
            ->addColumn('operations', function (VacationRentalBooking $item) {
                $operations = '';

                try {
                    if ($this->hasPermission('vacation-rental.booking.edit')) {
                        $operations .= Html::link(
                            route('vacation-rental.booking.show', $item->id),
                            '<i class="fa fa-eye"></i> ' . __('View'),
                            ['class' => 'btn btn-sm btn-primary me-1']
                        );

                        $operations .= Html::link(
                            route('vacation-rental.booking.edit', $item->id),
                            '<i class="fa fa-edit"></i> ' . __('Edit'),
                            ['class' => 'btn btn-sm btn-info me-1']
                        );
                    }
                } catch (\Exception $e) {
                    $operations = '<span class="text-muted">N/A</span>';
                }

                return $operations;
            })
            ->rawColumns(['status', 'operations']);

        return $this->toJson($data);
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        $query = $this
            ->getModel()
            ->query()
            ->select([
                'id',
                'booking_number',
                'property_id',
                'guest_name',
                'guest_email',
                'guest_phone',
                'check_in_date',
                'check_out_date',
                'guests_count',
                'total_amount',
                'status',
                'created_at',
            ])
            ->with(['property:id,name,currency_id', 'property.currency']);

        return $this->applyScopes($query);
    }

    public function columns(): array
    {
        return [
            IdColumn::make(),
            Column::make('booking_number')
                ->title(trans('plugins/real-estate::vacation-rental.booking_number')),
            Column::make('property_name')
                ->title(trans('plugins/real-estate::vacation-rental.property'))
                ->searchable(false)
                ->orderable(false),
            Column::make('guest_name')
                ->title(trans('plugins/real-estate::vacation-rental.guest_name')),
            Column::make('check_in_date')
                ->title(trans('plugins/real-estate::vacation-rental.check_in_date')),
            Column::make('check_out_date')
                ->title(trans('plugins/real-estate::vacation-rental.check_out_date')),
            Column::make('nights')
                ->title(trans('plugins/real-estate::vacation-rental.nights'))
                ->searchable(false)
                ->orderable(false),
            Column::make('guests_count')
                ->title(trans('plugins/real-estate::vacation-rental.guests')),
            Column::make('total_amount')
                ->title(trans('plugins/real-estate::vacation-rental.total_amount')),
            Column::make('status')
                ->title(trans('plugins/real-estate::vacation-rental.status')),
            CreatedAtColumn::make(),
        ];
    }

    public function buttons(): array
    {
        return [];
    }

    public function bulkActions(): array
    {
        return [
            DeleteBulkAction::make()->permission('vacation-rental.booking.destroy'),
        ];
    }

    public function getBulkChanges(): array
    {
        return [
            SelectBulkChange::make()
                ->name('status')
                ->title(trans('plugins/real-estate::vacation-rental.status'))
                ->searchable()
                ->choices([
                    VacationRentalBooking::STATUS_PENDING => __('Pending'),
                    VacationRentalBooking::STATUS_CONFIRMED => __('Confirmed'),
                    VacationRentalBooking::STATUS_CANCELLED => __('Cancelled'),
                    VacationRentalBooking::STATUS_COMPLETED => __('Completed'),
                ]),
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
            return $query->where('status', $value);
        }

        return parent::applyFilterCondition($query, $key, $operator, $value);
    }

    public function saveBulkChangeItem(Model $item, string $inputKey, ?string $inputValue): Model|bool
    {
        if ($inputKey === 'status') {
            $item->status = $inputValue;
            $item->save();
        }

        return parent::saveBulkChangeItem($item, $inputKey, $inputValue);
    }
}
