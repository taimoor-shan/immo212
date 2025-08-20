<?php

namespace Botble\RealEstate\Tables;

use Botble\Base\Facades\Html;
use Botble\RealEstate\Models\VacationRentalBooking;
use Botble\RealEstate\Models\Account;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\Action;
use Botble\Table\Columns\Column;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\IdColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;

class AccountVacationRentalBookingTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(VacationRentalBooking::class)
            ->addActions([
                Action::make('view')
                    ->route('public.account.vacation-rentals.bookings.show')
                    ->icon('ti ti-eye')
                    ->label(__('View'))
                    ->color('info'),
            ]);
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('booking_number', function (VacationRentalBooking $item) {
                return Html::tag('strong', $item->booking_number);
            })
            ->editColumn('property_name', function (VacationRentalBooking $item) {
                return $item->vacationRental ? $item->vacationRental->name : '—';
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
                return $item->nights_count . ' ' . trans_choice('night|nights', $item->nights_count);
            })
            ->editColumn('guests_count', function (VacationRentalBooking $item) {
                return $item->guests_count . ' ' . trans_choice('guest|guests', $item->guests_count);
            })
            ->editColumn('total_amount', function (VacationRentalBooking $item) {
                return format_price($item->total_amount, $item->vacationRental->currency ?? null);
            })
            ->editColumn('status', function (VacationRentalBooking $item) {
                $statuses = $item->getStatuses();
                $status = $statuses[$item->status] ?? $item->status;
                $color = $item->getStatusColor();
                
                return Html::tag('span', $status, [
                    'class' => 'badge',
                    'style' => "background-color: {$color}; color: white;"
                ]);
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
                'booking_number',
                'vacation_rental_id',
                'guest_name',
                'guest_email',
                'guest_phone',
                'check_in_date',
                'check_out_date',
                'nights_count',
                'guests_count',
                'total_amount',
                'status',
                'created_at',
            ])
            ->with(['vacationRental:id,name,currency_id,author_id,author_type', 'vacationRental.currency'])
            ->whereHas('vacationRental', function ($query) {
                $query->where('author_id', auth('account')->id())
                      ->where('author_type', Account::class);
            });

        return $this->applyScopes($query);
    }

    public function columns(): array
    {
        return [
            IdColumn::make(),
            Column::make('booking_number')
                ->title(trans('plugins/real-estate::vacation-rental.booking_number')),
            Column::make('property_name')
                ->title(trans('plugins/real-estate::vacation-rental.vacation_rental'))
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
        return [];
    }

    public function getBulkChanges(): array
    {
        return [];
    }

    public function hasBulkActions(): bool
    {
        return false;
    }
}
