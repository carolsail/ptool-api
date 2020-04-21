<?php
// auth
Route::post('auth/login', 'api/Auth/login');
Route::get('auth/verify', 'api/Auth/verify');
Route::get('auth/refresh', 'api/Auth/refresh');

// task
Route::post('task/items', 'api/Task/items');
Route::post('task/change/urgent', 'api/Task/changeItemUrgent');
Route::post('task/get/categories', 'api/Task/getCategories');
Route::post('task/item/add', 'api/Task/addItem');
Route::post('task/item/delete', 'api/Task/deleteItem');
Route::post('task/item/update', 'api/Task/updateItem');
Route::post('task/timer/add', 'api/Task/addTimer');
// task category
Route::post('task/categories', 'api/Task/categories');
Route::post('task/change/active', 'api/Task/changeCategoryActive');
Route::post('task/category/add', 'api/Task/addCategory');
Route::post('task/category/delete', 'api/Task/deleteCategory');
Route::post('task/category/update', 'api/Task/updateCategory');