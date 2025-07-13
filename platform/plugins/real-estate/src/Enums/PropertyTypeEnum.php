<?php

namespace Botble\RealEstate\Enums;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Supports\Enum;
use Illuminate\Support\HtmlString;

/**
 * @method static PropertyTypeEnum SALE()
 * @method static PropertyTypeEnum RENT()
 * @method static PropertyTypeEnum VACATION_RENTAL()
 */
class PropertyTypeEnum extends Enum
{
    public const SALE = 'sale';

    public const RENT = 'rent';

    public const VACATION_RENTAL = 'vacation_rental';

    public static $langPath = 'plugins/real-estate::property.types';

    public function toHtml(): HtmlString|string|null
    {
        $color = match ($this->value) {
            self::SALE => 'success',
            self::RENT => 'info',
            self::VACATION_RENTAL => 'warning',
            default => 'primary',
        };

        return BaseHelper::renderBadge($this->label(), $color);
    }
}
