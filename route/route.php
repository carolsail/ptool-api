<?php
// auth
Route::post('auth/login', 'api/Auth/login');
Route::get('auth/verify', 'api/Auth/verify');
Route::get('auth/refresh', 'api/Auth/refresh');

// task
Route::post('task/items', 'api/Task/items');
Route::post('task/change/urgent', 'api/Task/changeUrgent');
Route::post('task/categories', 'api/Task/getCategories');
Route::post('task/item/add', 'api/Task/addItem');
Route::post('task/item/delete', 'api/Task/deleteItem');
Route::post('task/item/update', 'api/Task/updateItem');
