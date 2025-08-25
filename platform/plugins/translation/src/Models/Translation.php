<?php

namespace Botble\Translation\Models;

use Botble\Base\Models\BaseModel;

class Translation extends BaseModel
{
    protected $table = 'translations';

    protected $fillable = [
        'locale',
        'group',
        'key',
        'value',
        'status'
    ];

    const STATUS_SAVED = 0;
    const STATUS_CHANGED = 1;

    public static function getStatuses(): array
    {
        return [
            self::STATUS_SAVED => 'Saved',
            self::STATUS_CHANGED => 'Changed',
        ];
    }

    // Create the table if it doesn't exist
    public static function ensureTableExists()
    {
        if (!app('db')->getSchemaBuilder()->hasTable('translations')) {
            app('db')->getSchemaBuilder()->create('translations', function ($table) {
                $table->id();
                $table->integer('status')->default(0);
                $table->string('locale', 20);
                $table->string('group');
                $table->string('key');
                $table->text('value')->nullable();
                $table->timestamps();
            });
        }
    }
}
