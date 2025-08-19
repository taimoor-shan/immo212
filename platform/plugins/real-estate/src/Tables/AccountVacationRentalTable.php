<?php

namespace Botble\RealEstate\Tables;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\Html;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\VacationRental;
use Botble\Table\Actions\Action;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\Columns\Column;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\EnumColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\ImageColumn;
use Botble\Table\Columns\NameColumn;
use Botble\Table\Columns\StatusColumn;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;

class AccountVacationRentalTable extends VacationRentalTable
{
    public function setup(): void
    {
        $this
            ->model(VacationRental::class)
            ->addActions([
                Action::make('bookings')
                    ->route('public.account.vacation-rentals.bookings')
                    ->icon('ti ti-users')
                    ->label(__('Bookings'))
                    ->color('success'),
                EditAction::make()->route('public.account.vacation-rentals.edit'),
                DeleteAction::make()->route('public.account.vacation-rentals.destroy'),
            ]);
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('name', function (VacationRental $item) {
                return Html::link(route('public.account.vacation-rentals.edit', $item->getKey()), BaseHelper::clean($item->name));
            })
            ->editColumn('image', function (VacationRental $item) {
                return $this->displayThumbnail($item->image);
            })
            ->editColumn('price', function (VacationRental $item) {
                return $item->price_format.' / '.__('night');
            })
            ->editColumn('number_bedroom', function (VacationRental $item) {
                return $item->number_bedroom ?: '—';
            })
            ->editColumn('number_bathroom', function (VacationRental $item) {
                return $item->number_bathroom ?: '—';
            })
            ->editColumn('square', function (VacationRental $item) {
                return $item->square ? $item->square.' '.setting('real_estate_square_unit', 'm²') : '—';
            })
            ->editColumn('minimum_stay', function (VacationRental $item) {
                return $item->minimum_stay ? $item->minimum_stay.' '.trans_choice('night|nights', $item->minimum_stay) : '—';
            })
            ->editColumn('maximum_guests', function (VacationRental $item) {
                return $item->maximum_guests ? $item->maximum_guests.' '.trans_choice('guest|guests', $item->maximum_guests) : '—';
            })
            ->editColumn('check_in_time', function (VacationRental $item) {
                return $item->check_in_time ?: '—';
            })
            ->editColumn('check_out_time', function (VacationRental $item) {
                return $item->check_out_time ?: '—';
            })
            ->editColumn('cleaning_fee', function (VacationRental $item) {
                return $item->cleaning_fee ? format_price($item->cleaning_fee) : '—';
            })
            ->editColumn('security_deposit', function (VacationRental $item) {
                return $item->security_deposit ? format_price($item->security_deposit) : '—';
            })
            ->editColumn('availability_status', function (VacationRental $item) {
                $upcomingBookings = $item->bookings()->active()
                    ->where('check_in_date', '>=', now())
                    ->count();

                if ($upcomingBookings > 0) {
                    return Html::tag('span', __('Booked (:count)', ['count' => $upcomingBookings]), ['class' => 'badge bg-warning text-warning-fg']);
                }

                return Html::tag('span', __('Available'), ['class' => 'badge bg-success text-success-fg']);
            })
            ->editColumn('unique_id', function (VacationRental $item) {
                return BaseHelper::clean($item->unique_id ?: '&mdash;');
            })
            ->editColumn('expire_date', function (VacationRental $item) {
                if ($item->never_expired) {
                    return trans('plugins/real-estate::property.never_expired_label');
                }

                if (! $item->expire_date) {
                    return '&mdash;';
                }

                if ($item->expire_date->isPast()) {
                    return Html::tag('span', $item->expire_date->toDateString(), ['class' => 'text-danger'])->toHtml();
                }

                if (Carbon::now()->diffInDays($item->expire_date) < 3) {
                    return Html::tag('span', $item->expire_date->toDateString(), ['class' => 'text-warning'])->toHtml();
                }

                return $item->expire_date->toDateString();
            })
            ->editColumn('total_bookings', function (VacationRental $item) {
                return $item->bookings()->count();
            })
            ->editColumn('monthly_revenue', function (VacationRental $item) {
                $revenue = $item->bookings()
                    ->where('status', \Botble\RealEstate\Models\VacationRentalBooking::STATUS_CONFIRMED)
                    ->whereMonth('check_in_date', Carbon::now()->month)
                    ->whereYear('check_in_date', Carbon::now()->year)
                    ->sum('total_amount');

                return format_price($revenue, $item->currency);
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
                'number_bedroom',
                'number_bathroom',
                'square',
                'minimum_stay',
                'maximum_guests',
                'check_in_time',
                'check_out_time',
                'cleaning_fee',
                'security_deposit',
                'unique_id',
                'created_at',
                'status',
                'moderation_status',
                'expire_date',
                'views',
                'currency_id',
            ])
            ->where([
                'author_id' => auth('account')->id(),
                'author_type' => Account::class,
            ])
            ->with(['currency']);

        return $this->applyScopes($query);
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

    public function buttons(): array
    {
        $buttons = [];
        if (auth('account')->user()->canPost()) {
            $buttons = $this->addCreateButton(route('public.account.vacation-rentals.create'));
        }

        return $buttons;
    }

    public function columns(): array
    {
        return [
            IdColumn::make(),
            ImageColumn::make()
                ->searchable(false)
                ->orderable(false),
            NameColumn::make()->route('public.account.vacation-rentals.edit'),
            Column::make('price')
                ->title(trans('plugins/real-estate::property.form.price_per_night')),
            Column::make('number_bedroom')
                ->title(trans('plugins/real-estate::property.form.number_bedroom')),
            Column::make('number_bathroom')
                ->title(trans('plugins/real-estate::property.form.number_bathroom')),
            Column::make('square')
                ->title(trans('plugins/real-estate::property.form.square')),
            Column::make('minimum_stay')
                ->title(trans('plugins/real-estate::vacation-rental.minimum_stay')),
            Column::make('maximum_guests')
                ->title(trans('plugins/real-estate::vacation-rental.maximum_guests')),
            Column::make('check_in_time')
                ->title(trans('plugins/real-estate::property.form.check_in_time')),
            Column::make('check_out_time')
                ->title(trans('plugins/real-estate::property.form.check_out_time')),
            Column::make('cleaning_fee')
                ->title(trans('plugins/real-estate::vacation-rental.cleaning_fee')),
            Column::make('security_deposit')
                ->title(trans('plugins/real-estate::vacation-rental.security_deposit')),
            Column::make('availability_status')
                ->title(trans('plugins/real-estate::vacation-rental.availability_status'))
                ->searchable(false)
                ->orderable(false),
            Column::make('total_bookings')
                ->title(__('Total Bookings'))
                ->searchable(false)
                ->orderable(false),
            Column::make('monthly_revenue')
                ->title(__('Monthly Revenue'))
                ->searchable(false)
                ->orderable(false),
            Column::make('views')
                ->title(trans('plugins/real-estate::property.views')),
            Column::make('unique_id')
                ->title(trans('plugins/real-estate::property.unique_id')),
            Column::make('expire_date')
                ->title(trans('plugins/real-estate::property.expire_date'))
                ->width(150),
            CreatedAtColumn::make(),
            StatusColumn::make(),
            EnumColumn::make('moderation_status')
                ->title(trans('plugins/real-estate::property.moderation_status'))
                ->width(150),
        ];
    }
}
