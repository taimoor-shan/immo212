<?php

use Botble\Base\Facades\BaseHelper;
use Botble\RealEstate\Http\Controllers\ConsultCustomFieldController;
use Botble\RealEstate\Http\Controllers\CouponController;
use Botble\RealEstate\Http\Controllers\CustomFieldController;
use Botble\RealEstate\Http\Controllers\DuplicatePropertyController;
use Botble\RealEstate\Http\Controllers\InvoiceController;
use Botble\RealEstate\Http\Controllers\PropertyController;
use Botble\RealEstate\Http\Controllers\UnverifiedAccountController;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Botble\RealEstate\Http\Controllers', 'middleware' => ['web', 'core']], function (): void {
    Route::group([
        'prefix' => BaseHelper::getAdminPrefix() . '/real-estate',
        'middleware' => 'auth',
    ], function (): void {
        Route::group(['prefix' => 'settings', 'as' => 'real-estate.settings.'], function (): void {
            Route::get('general', [
                'as' => 'general',
                'uses' => 'Settings\GeneralSettingController@edit',
            ]);

            Route::put('general', [
                'as' => 'general.update',
                'uses' => 'Settings\GeneralSettingController@update',
                'permission' => 'real-estate.settings.general',
            ]);

            Route::get('currencies', [
                'as' => 'currencies',
                'uses' => 'Settings\CurrencySettingController@edit',
            ]);

            Route::put('currencies', [
                'as' => 'currencies.update',
                'uses' => 'Settings\CurrencySettingController@update',
                'permission' => 'real-estate.settings.currencies',
            ]);

            Route::get('accounts', [
                'as' => 'accounts',
                'uses' => 'Settings\AccountSettingController@edit',
            ]);

            Route::put('accounts', [
                'as' => 'accounts.update',
                'uses' => 'Settings\AccountSettingController@update',
                'permission' => 'real-estate.settings.accounts',
            ]);

            Route::get('invoices', [
                'as' => 'invoices',
                'uses' => 'Settings\InvoiceSettingController@edit',
            ]);

            Route::put('invoices', [
                'as' => 'invoices.update',
                'uses' => 'Settings\InvoiceSettingController@update',
                'permission' => 'real-estate.settings.invoices',
            ]);

            Route::get('invoice-template', [
                'as' => 'invoice-template',
                'uses' => 'Settings\InvoiceTemplateSettingController@edit',
            ]);

            Route::put('invoice-template', [
                'as' => 'invoice-template.update',
                'uses' => 'Settings\InvoiceTemplateSettingController@update',
                'permission' => 'real-estate.settings.invoice-template',
            ]);

            Route::prefix('invoice-template')->name('invoice-template.')->group(function (): void {
                Route::post('reset', [
                    'as' => 'reset',
                    'uses' => 'Settings\InvoiceTemplateSettingController@reset',
                    'permission' => 'invoice.index',
                ]);

                Route::get('preview', [
                    'as' => 'preview',
                    'uses' => 'Settings\InvoiceTemplateSettingController@preview',
                    'permission' => 'invoice.index',
                ]);
            });
        });

        Route::group(['prefix' => 'properties', 'as' => 'property.'], function (): void {
            Route::resource('', 'PropertyController')->parameters(['' => 'property']);

            Route::group(['permission' => 'property.edit'], function (): void {
                Route::post('{property}/approve', [PropertyController::class, 'approve'])->name('approve')
                    ->wherePrimaryKey();
                Route::post('{property}/reject', [PropertyController::class, 'reject'])->name('reject')
                    ->wherePrimaryKey();
                Route::post('{property}/duplicate', [DuplicatePropertyController::class, '__invoke'])
                    ->name('duplicate-property')
                    ->wherePrimaryKey();
            });
        });

        Route::group(['prefix' => 'projects', 'as' => 'project.'], function (): void {
            Route::resource('', 'ProjectController')
                ->parameters(['' => 'project']);

            Route::group(['permission' => 'project.edit'], function (): void {
                Route::post('{project}/approve', ['Botble\RealEstate\Http\Controllers\ProjectController', 'approve'])
                    ->name('approve')
                    ->wherePrimaryKey();
                Route::post('{project}/reject', ['Botble\RealEstate\Http\Controllers\ProjectController', 'reject'])
                    ->name('reject')
                    ->wherePrimaryKey();
            });
        });

        Route::group(['prefix' => 'property-features', 'as' => 'property_feature.'], function (): void {
            Route::resource('', 'FeatureController')
                ->parameters(['' => 'property_feature']);
        });

        Route::group(['prefix' => 'investors', 'as' => 'investor.'], function (): void {
            Route::resource('', 'InvestorController')
                ->parameters(['' => 'investor']);
        });

        Route::group(['prefix' => 'consults', 'as' => 'consult.'], function (): void {
            Route::resource('', 'ConsultController')
                ->parameters(['' => 'consult'])
                ->except(['create', 'store']);

            Route::group(['prefix' => 'custom-fields', 'as' => 'custom-fields.', 'permission' => 'consult.edit'], function (): void {
                Route::resource('', ConsultCustomFieldController::class)->parameters(['' => 'custom-field']);
            });
        });

        Route::group(['prefix' => 'categories', 'as' => 'property_category.'], function (): void {
            Route::resource('', 'CategoryController')
                ->parameters(['' => 'category']);

            Route::put('update-tree', [
                'as' => 'update-tree',
                'uses' => 'CategoryController@updateTree',
                'permission' => 'property_category.edit',
            ]);
        });

        Route::group(['prefix' => 'facilities', 'as' => 'facility.'], function (): void {
            Route::resource('', 'FacilityController')
                ->parameters(['' => 'facility']);
        });

        Route::group(['prefix' => 'accounts', 'as' => 'account.'], function (): void {
            Route::resource('', 'AccountController')
                ->parameters(['' => 'account']);

            Route::get('list', [
                'as' => 'list',
                'uses' => 'AccountController@getList',
                'permission' => 'account.index',
            ]);

            Route::post('credits/{id}', [
                'as' => 'credits.add',
                'uses' => 'TransactionController@postCreate',
                'permission' => 'account.edit',
            ])->wherePrimaryKey();

            Route::post('verify-email/{id}', [
                'as' => 'verify-email',
                'uses' => 'AccountController@verifyEmail',
                'permission' => 'account.edit',
            ])->wherePrimaryKey();
        });

        Route::prefix('unverified-accounts')->name('unverified-accounts.')->group(function (): void {
            Route::group(['permission' => 'unverified-accounts.index'], function (): void {
                Route::match(['POST', 'GET'], '/', [UnverifiedAccountController::class, 'index'])->name('index');
                Route::get('{id}', [UnverifiedAccountController::class, 'show'])->name('show')->wherePrimaryKey();
                Route::post('{id}/approve', [UnverifiedAccountController::class, 'approve'])->name('approve')->wherePrimaryKey();
                Route::post('{id}/reject', [UnverifiedAccountController::class, 'reject'])->name('reject')->wherePrimaryKey();
            });
        });

        Route::group(['prefix' => 'packages', 'as' => 'package.'], function (): void {
            Route::resource('', 'PackageController')
                ->parameters(['' => 'package']);
        });

        Route::group(['prefix' => 'vacation-rentals', 'as' => 'vacation-rental.'], function (): void {
            // AJAX endpoint for vacation rental edit form calendar (must be before resource routes)
            Route::get('availability-data', 'VacationRentalController@getAvailabilityData')->name('availability-data');

            // Main CRUD routes for vacation rentals
            Route::resource('', 'VacationRentalController')
                ->parameters(['' => 'vacation_rental'])
                ->except(['show']);

            Route::get('{vacation_rental}', 'VacationRentalController@show')
                ->name('show')
                ->wherePrimaryKey();

            // Moderation routes
            Route::post('{vacation_rental}/approve', 'VacationRentalController@approve')
                ->name('approve')
                ->wherePrimaryKey();

            Route::post('{vacation_rental}/reject', 'VacationRentalController@reject')
                ->name('reject')
                ->wherePrimaryKey();

            // Admin overview and management
            Route::get('admin/overview', 'VacationRentalAdminController@overview')->name('admin.overview');
            Route::get('admin/dashboard', 'VacationRentalAdminController@dashboard')->name('admin.dashboard');
            Route::match(['GET', 'POST'], 'admin/bookings', 'VacationRentalAdminController@bookings')->name('admin.bookings');
            Route::get('admin/availability', 'VacationRentalAdminController@availability')->name('admin.availability');
            Route::get('admin/calendar', 'VacationRentalAdminController@calendar')->name('admin.calendar');
            Route::match(['GET', 'POST'], 'properties', 'VacationRentalAdminController@properties')->name('properties');

            // Admin API endpoints for calendar management
            Route::get('availability-data', 'VacationRentalAdminController@getAvailabilityData')->name('admin.availability-data');
            Route::post('block-dates', 'VacationRentalAdminController@blockDates')->name('admin.block-dates');
            Route::post('unblock-dates', 'VacationRentalAdminController@unblockDates')->name('admin.unblock-dates');
            Route::post('maintenance-dates', 'VacationRentalAdminController@maintenanceDates')->name('admin.maintenance-dates');

            // Booking management routes
            Route::group(['prefix' => 'bookings', 'as' => 'booking.'], function (): void {
                Route::get('{id}', 'VacationRentalAdminController@showBooking')->name('show')->wherePrimaryKey();

                Route::group(['permission' => 'vacation-rental.booking.edit'], function (): void {
                    Route::get('{id}/edit', 'VacationRentalAdminController@editBooking')->name('edit')->wherePrimaryKey();
                    Route::put('{id}', 'VacationRentalAdminController@updateBooking')->name('update')->wherePrimaryKey();
                });

                Route::delete('{id}', [
                    'as' => 'destroy',
                    'uses' => 'VacationRentalAdminController@destroyBooking',
                    'permission' => 'vacation-rental.booking.destroy',
                ])->wherePrimaryKey()->middleware('Botble\Base\Http\Middleware\RequiresJsonRequestMiddleware');
            });
        });

        Route::group(['prefix' => 'reviews', 'as' => 'review.'], function (): void {
            Route::resource('', 'ReviewController')->parameters(['' => 'review'])->only(['index', 'destroy']);
        });

        Route::prefix('custom-fields')->name('real-estate.custom-fields.')->group(function (): void {
            Route::resource('', CustomFieldController::class)->parameters(['' => 'custom-field']);

            Route::get('info', [
                'as' => 'get-info',
                'uses' => 'CustomFieldController@getInfo',
                'permission' => false,
            ]);
        });

        Route::group(['prefix' => 'invoices', 'as' => 'invoices.'], function (): void {
            Route::resource('', 'InvoiceController')->parameters(['' => 'invoice'])->except(['edit', 'update']);
            Route::get('{id}', [InvoiceController::class, 'show'])
                ->name('show')
                ->wherePrimaryKey();
            Route::get('{id}/generate', [InvoiceController::class, 'generate'])
                ->name('generate')
                ->wherePrimaryKey();
        });

        Route::group(['prefix' => 'properties', 'as' => 'properties.'], function (): void {
            Route::get('/import', [
                'as' => 'import.index',
                'uses' => 'PropertyImportController@index',
                'permission' => 'import-properties.index',
            ]);

            Route::post('import', [
                'as' => 'import',
                'uses' => 'Chunk\Properties\ChunkImportController@__invoke',
                'permission' => 'import-properties.index',
            ]);

            Route::post('upload/process', [
                'as' => 'upload.process',
                'uses' => 'Chunk\Properties\ChunkUploadController@__invoke',
                'permission' => 'import-properties.index',
            ]);

            Route::post('upload/validate', [
                'as' => 'upload.validate',
                'uses' => 'Chunk\Properties\ChunkValidateController@__invoke',
                'permission' => 'import-properties.index',
            ]);

            Route::post('download-template', [
                'as' => 'download-template',
                'uses' => 'PropertyImportController@downloadTemplate',
                'permission' => 'import-properties.index',
            ]);
        });

        Route::group(['prefix' => 'projects', 'as' => 'projects.'], function (): void {
            Route::get('/import', [
                'as' => 'import.index',
                'uses' => 'ProjectImportController@index',
                'permission' => 'import-projects.index',
            ]);

            Route::post('import', [
                'as' => 'import',
                'uses' => 'Chunk\Projects\ChunkImportController@__invoke',
                'permission' => 'import-projects.index',
            ]);

            Route::post('upload/process', [
                'as' => 'upload.process',
                'uses' => 'Chunk\Projects\ChunkUploadController@__invoke',
                'permission' => 'import-projects.index',
            ]);

            Route::post('upload/validate', [
                'as' => 'upload.validate',
                'uses' => 'Chunk\Projects\ChunkValidateController@__invoke',
                'permission' => 'import-projects.index',
            ]);

            Route::post('download-template', [
                'as' => 'download-template',
                'uses' => 'ProjectImportController@downloadTemplate',
                'permission' => 'import-projects.index',
            ]);
        });

        Route::group(['prefix' => 'export/properties', 'as' => 'export-properties.'], function (): void {
            Route::get('/', [
                'as' => 'index',
                'uses' => 'ExportPropertyController@index',
                'permission' => 'export-properties.index',
            ]);

            Route::post('/', [
                'as' => 'index.post',
                'uses' => 'ExportPropertyController@export',
                'permission' => 'export-properties.index',
            ]);
        });

        Route::group(['prefix' => 'export/projects', 'as' => 'export-projects.'], function (): void {
            Route::get('/', [
                'as' => 'index',
                'uses' => 'ExportProjectController@index',
                'permission' => 'export-projects.index',
            ]);

            Route::post('/', [
                'as' => 'index.post',
                'uses' => 'ExportProjectController@export',
                'permission' => 'export-projects.index',
            ]);
        });

        Route::group(['prefix' => 'coupons', 'as' => 'coupons.'], function (): void {
            Route::resource('', CouponController::class)
                ->parameters(['' => 'coupon']);

            Route::post('generate-coupon', [
                'as' => 'generate-coupon',
                'uses' => 'CouponController@generateCouponCode',
                'permission' => 'coupons.index',
            ]);

            Route::delete('deletes', [
                'as' => 'deletes',
                'uses' => 'CouponController@deletes',
                'permission' => 'coupons.destroy',
            ])->middleware('Botble\Base\Http\Middleware\RequiresJsonRequestMiddleware');
        });
    });
});

// Vacation rental booking routes moved to fronts.php to avoid duplication
// This section was causing route conflicts and has been consolidated
