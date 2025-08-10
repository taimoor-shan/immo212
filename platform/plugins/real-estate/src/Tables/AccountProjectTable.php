<?php

namespace Botble\RealEstate\Tables;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\Html;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\Project;
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
use Illuminate\Support\Facades\Schema;

class AccountProjectTable extends ProjectTable
{
    public function setup(): void
    {
        $this
            ->model(Project::class)
            ->addActions([
                Action::make('renew')
                    ->route('public.account.projects.renew')
                    ->icon('ti ti-refresh')
                    ->label(__('Renew'))
                    ->color('info')
                    ->attributes([
                        'data-bb-toggle' => 'property-renew-modal',
                    ]),
                EditAction::make()->route('public.account.projects.edit'),
                DeleteAction::make()->route('public.account.projects.destroy'),
            ]);
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('views', function (Project $item) {
                return number_format($item->views);
            })
            ->editColumn('unique_id', function (Project $item) {
                $uid = $item->unique_id ?: '&mdash;';

                $expired = false;
                if (Schema::hasColumn('re_projects', 'expire_date')) {
                    if ($item->expire_date) {
                        $date = $item->expire_date instanceof Carbon ? $item->expire_date : Carbon::parse($item->expire_date);
                        $expired = $date->isPast();
                    }
                }

                if (! $expired) {
                    return BaseHelper::clean($uid);
                }

                $badge = Html::tag('span', __('Expired'), ['class' => 'badge bg-danger ms-2'])->toHtml();
                $renewButton = Html::link(
                    route('public.account.projects.renew', $item->getKey()),
                    __('Renew'),
                    [
                        'class' => 'btn btn-sm btn-info ms-2',
                        'data-bb-toggle' => 'property-renew-modal',
                    ]
                )->toHtml();

                return $uid . ' ' . $badge . ' ' . $renewButton;
            });
        
        if (Schema::hasColumn('re_projects', 'expire_date')) {
            $data->editColumn('expire_date', function (Project $item) {
                if (! $item->expire_date) {
                    return '&mdash;';
                }

                if ($item->expire_date instanceof Carbon ? $item->expire_date->isPast() : Carbon::parse($item->expire_date)->isPast()) {
                    return Html::tag('span', (string) ( $item->expire_date instanceof Carbon ? $item->expire_date->toDateString() : Carbon::parse($item->expire_date)->toDateString() ), ['class' => 'text-danger'])->toHtml();
                }

                $date = $item->expire_date instanceof Carbon ? $item->expire_date : Carbon::parse($item->expire_date);
                if (Carbon::now()->diffInDays($date) < 3) {
                    return Html::tag('span', $date->toDateString(), ['class' => 'text-warning'])->toHtml();
                }

                return $date->toDateString();
            });
        }
        
        if (Schema::hasColumn('re_projects', 'moderation_status')) {
            $data->editColumn('moderation_status', function (Project $item) {
                return BaseHelper::clean($item->moderation_status_html);
            });
        }

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
                'views',
                'status',
                'created_at',
                'unique_id',
            ])
            ->where([
                'author_id' => auth('account')->id(),
                'author_type' => Account::class,
            ]);

        if (Schema::hasColumn('re_projects', 'expire_date')) {
            $query->addSelect('expire_date');
        }
        if (Schema::hasColumn('re_projects', 'moderation_status')) {
            $query->addSelect('moderation_status');

            // Simple filter by moderation_status via query parameter
            if (request()->filled('moderation_status')) {
                $query->where('moderation_status', request('moderation_status'));
            }
        }

        return $this->applyScopes($query);
    }

    public function buttons(): array
    {
        $buttons = [];
        if (auth('account')->user()->canPost()) {
            $buttons = $this->addCreateButton(route('public.account.projects.create'));
        }

        return $buttons;
    }

    public function columns(): array
    {
        $columns = [
            IdColumn::make(),
            ImageColumn::make()
                ->searchable(false)
                ->orderable(false),
            NameColumn::make()->route('public.account.projects.edit'),
            Column::make('views')->title(trans('plugins/real-estate::project.views')),
            Column::make('unique_id')->title(trans('plugins/real-estate::project.unique_id')),
        ];

        if (Schema::hasColumn('re_projects', 'expire_date')) {
            $columns[] = Column::make('expire_date')
                ->title(trans('plugins/real-estate::property.expire_date'))
                ->width(150);
        }

        $columns[] = CreatedAtColumn::make();
        
        if (Schema::hasColumn('re_projects', 'moderation_status')) {
            $columns[] = EnumColumn::make('moderation_status')
                ->title(trans('plugins/real-estate::property.moderation_status'))
                ->width(150);
        }

        $columns[] = StatusColumn::make();

        return $columns;
    }
}
