<?php

Route::any('user/register/email-check', 'User\RegisterController@emailCheck')->name('user.register.email-check')->middleware('cors');
Route::any('user/register', 'User\RegisterController@register')->name('user.register')->middleware('cors');
Route::any('user/register/password', 'User\RegisterController@password')->name('user.register.password')->middleware('cors');

Route::any('user/login', 'User\AuthenticationController@login')->name('user.authentication.login')->middleware('cors');
Route::any('user/logout', 'User\AuthenticationController@logout')->name('user.authentication.logout')->middleware('cors');