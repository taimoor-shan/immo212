<?php

namespace Botble\RealEstate\Enums;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\Html;
use Botble\Base\Supports\Enum;
use Illuminate\Support\HtmlString;

/**
 * @method static VacationRentalStatusEnum DRAFT()
 * @method static VacationRentalStatusEnum NOT_AVAILABLE()
 * @method static VacationRentalStatusEnum RENTING()
 */
class VacationRentalStatusEnum extends Enum
{
    public const DRAFT = 'draft';

    public const NOT_AVAILABLE = 'not_available';

    public const RENTING = 'renting';

    public static $langPath = 'plugins/real-estate::vacation-rental.statuses';

    public function toHtml(): HtmlString|string|null
    {
        if (! is_in_admin()) {
            $html = match ($this->value) {
                self::DRAFT => Html::tag('span', self::DRAFT()->label(), ['class' => 'label-default status-label'])
                    ->toHtml(),
                self::NOT_AVAILABLE => Html::tag(
                    'span',
                    self::NOT_AVAILABLE()->label(),
                    ['class' => 'label-default status-label']
                )
                    ->toHtml(),
                self::RENTING => Html::tag('span', self::RENTING()->label(), ['class' => 'label-success status-label'])
                    ->toHtml(),
                default => Html::tag('span', $this->label(), ['class' => 'label-default status-label'])->toHtml(),
            };

            return apply_filters('real_estate_vacation_rental_status_html', $html, $this->value);
        }

        $color = match ($this->value) {
            self::NOT_AVAILABLE => 'secondary',
            self::RENTING => 'success',
            self::DRAFT => 'warning',
            default => 'primary',
        };

        return BaseHelper::renderBadge($this->label(), $color);
    }
}
