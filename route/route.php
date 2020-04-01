<?php

Route::post('auth/login', 'api/Auth/login');
Route::get('auth/verify', 'api/Auth/verify');
Route::get('auth/refresh', 'api/Auth/refresh');