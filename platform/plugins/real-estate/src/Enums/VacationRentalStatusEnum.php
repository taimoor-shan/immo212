<?php

namespace Botble\RealEstate\Enums;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\Html;
use Botble\Base\Supports\Enum;
use Illuminate\Support\HtmlString;

/**
 * @method static VacationRentalStatusEnum DRAFT()
 * @method static VacationRentalStatusEnum RENTING()
 * @method static VacationRentalStatusEnum PUBLISHED()
 */
class VacationRentalStatusEnum extends Enum
{
    public const DRAFT = 'draft';

    public const RENTING = 'renting';

    public const PUBLISHED = 'published';

    public static $langPath = 'plugins/real-estate::vacation-rental.statuses';

    /**
     * Get statuses that should be considered active/visible on frontend
     */
    public static function getActiveStatuses(): array
    {
        return [self::RENTING, self::PUBLISHED];
    }

    public function toHtml(): HtmlString|string|null
    {
        if (! is_in_admin()) {
            $html = match ($this->value) {
                self::DRAFT => Html::tag('span', self::DRAFT()->label(), ['class' => 'label-default status-label'])->toHtml(),
                self::RENTING => Html::tag('span', self::RENTING()->label(), ['class' => 'label-success status-label'])->toHtml(),
                self::PUBLISHED => Html::tag('span', self::PUBLISHED()->label(), ['class' => 'label-success status-label'])->toHtml(),
                default => Html::tag('span', $this->label(), ['class' => 'label-default status-label'])->toHtml(),
            };

            return apply_filters('real_estate_vacation_rental_status_html', $html, $this->value);
        }

        $color = match ($this->value) {
            self::RENTING, self::PUBLISHED => 'success',
            self::DRAFT => 'warning',
            default => 'primary',
        };

        return BaseHelper::renderBadge($this->label(), $color);
    }
}
