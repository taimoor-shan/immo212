<?php

use Botble\Base\Facades\BaseHelper;
use Illuminate\Support\Facades\Route;
use VigStudio\VigAutoTranslations\Http\Controllers\VigAutoTranslationsController;

Route::group([
    'namespace' => 'VigStudio\VigAutoTranslations\Http\Controllers',
    'middleware' => ['web', 'core']
], function () {
    Route::group([
        'prefix' => BaseHelper::getAdminPrefix() . '/vig-auto-translations',
        'middleware' => 'auth',
        'permission' => 'vig-auto-translations.index',
        'as' => 'vig-auto-translations.',
    ], function () {

        Route::group(['prefix' => 'settings'], function () {
            Route::get('', [
                'as' => 'settings',
                'uses' => 'SettingsController@settings',
            ]);

            Route::put('', [
                'as' => 'settings.update',
                'uses' => 'SettingsController@update',
            ]);

        });


        Route::group(['prefix' => 'theme'], function () {
            Route::get('/', [
                'as' => 'theme',
                'uses' => 'VigAutoTranslationsController@getThemeTranslations',
            ]);

            Route::post('/', [
                'as' => 'theme.post',
                'uses' => 'VigAutoTranslationsController@postThemeTranslations',
            ]);

            Route::post('all', [
                'as' => 'theme.post-all',
                'uses' => 'VigAutoTranslationsController@postThemeAllTranslations',
            ]);
        });

        Route::group(['prefix' => 'plugin'], function () {
            Route::get('', [
                'as' => 'plugin',
                'uses' => 'VigAutoTranslationsController@getPluginsTranslations',
            ]);

            Route::post('', [
                'as' => 'plugin.post',
                'uses' => 'VigAutoTranslationsController@postPluginsTranslations',
            ]);

            Route::post('all', [
                'as' => 'plugin.all',
                'uses' => 'VigAutoTranslationsController@postAllPluginsTranslations',
            ]);
        });

        Route::get('auto-translate', [
            'as' => 'auto-translate',
            'uses' => 'VigAutoTranslationsController@getAutoTranslate',
        ]);
        
        // New dashboard routes
        Route::group(['prefix' => 'dashboard'], function () {
            Route::get('/', [
                'as' => 'dashboard',
                'uses' => 'AdminTranslationController@index',
            ]);
            
            // AJAX endpoints
            Route::post('/translate-theme', [
                'as' => 'dashboard.translate-theme',
                'uses' => 'AdminTranslationController@translateTheme',
            ]);
            Route::post('/translate-core', [
                'as' => 'dashboard.translate-core',
                'uses' => 'AdminTranslationController@translateCore',
            ]);
            Route::get('/progress', [
                'as' => 'dashboard.progress',
                'uses' => 'AdminTranslationController@getProgress',
            ]);
            Route::get('/stats', [
                'as' => 'dashboard.stats',
                'uses' => 'AdminTranslationController@getStats',
            ]);
            Route::get('/groups', [
                'as' => 'dashboard.groups',
                'uses' => 'AdminTranslationController@getGroups',
            ]);
            Route::post('/clear-cache', [
                'as' => 'dashboard.clear-cache',
                'uses' => 'AdminTranslationController@clearCache',
            ]);
            Route::post('/test-provider', [
                'as' => 'dashboard.test-provider',
                'uses' => 'AdminTranslationController@testProvider',
            ]);
        });
    });

    // Global translation group publish route for compatibility with VIG views
    Route::group([
        'prefix' => BaseHelper::getAdminPrefix() . '/translations/group',
        'middleware' => ['auth', 'core'],
        'permission' => 'vig-auto-translations.index',
    ], function () {
        Route::post('publish', [
            'as' => 'translations.group.publish',
            'uses' => 'VigAutoTranslationsController@publishTranslationGroup',
        ]);
    });
});
