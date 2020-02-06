<?php

Route::group(['middleware'=>['cors', 'authenticated']], function() {
    Route::any('search', 'BeverageController@search')->name('beverage.search');
});