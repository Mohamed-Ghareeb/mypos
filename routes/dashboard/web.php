<?php

Route::group(
['prefix' => LaravelLocalization::setLocale(), 'middleware' => [ 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath' ]],
    function () {
        Route::prefix('dashboard')->name('dashboard.')->middleware(['auth'])->group(function () {
            Route::get('/index', 'DashboardController@index')->name('index');

            // Users Routes
            Route::resource('users', 'UsersController')->except(['show']);
            // Categories Routes
            Route::resource('categories', 'CategoriesController')->except(['show']);
        }); // end of Dashboard Routes
    }
);
