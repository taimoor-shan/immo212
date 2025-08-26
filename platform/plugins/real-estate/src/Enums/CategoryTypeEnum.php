<?php

namespace Botble\RealEstate\Enums;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Supports\Enum;
use Illuminate\Support\HtmlString;

/**
 * @method static CategoryTypeEnum PROPERTY()
 * @method static CategoryTypeEnum PROJECT() 
 * @method static CategoryTypeEnum VACATION_RENTAL()
 */
class CategoryTypeEnum extends Enum
{
    public const PROPERTY = 'property';
    public const PROJECT = 'project'; 
    public const VACATION_RENTAL = 'vacation_rental';

    public static function labels(): array
    {
        return [
            self::PROPERTY => __('Property'),
            self::PROJECT => __('Project'),
            self::VACATION_RENTAL => __('Vacation Rental'),
        ];
    }

    public function toHtml(): HtmlString|string
    {
        $color = match ($this->value) {
            self::PROPERTY => 'info',
            self::PROJECT => 'success', 
            self::VACATION_RENTAL => 'warning',
            default => 'secondary',
        };

        return BaseHelper::renderBadge($this->label(), $color);
    }

    /**
     * Get all available types for vacation rentals
     */
    public static function getVacationRentalCompatibleTypes(): array
    {
        return [
            self::VACATION_RENTAL,
            // You can include PROPERTY if some property categories are suitable for rentals
        ];
    }

    /**
     * Get all available types for properties 
     */
    public static function getPropertyCompatibleTypes(): array
    {
        return [
            self::PROPERTY,
        ];
    }

    /**
     * Get all available types for projects
     */
    public static function getProjectCompatibleTypes(): array
    {
        return [
            self::PROJECT,
        ];
    }
}
