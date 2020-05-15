<?php
// auth
Route::post('auth/login', 'api/Auth/login');
Route::get('auth/verify', 'api/Auth/verify');
Route::get('auth/refresh', 'api/Auth/refresh');

// task item
Route::post('task/items', 'api/Task/items');
Route::post('task/item/add', 'api/Task/addItem');
Route::post('task/item/delete', 'api/Task/deleteItem');
Route::post('task/item/update', 'api/Task/updateItem');
Route::post('task/item/change/urgent', 'api/Task/changeItemUrgent');

// task category
Route::post('task/categories', 'api/Task/categories');
Route::post('task/category/add', 'api/Task/addCategory');
Route::post('task/category/delete', 'api/Task/deleteCategory');
Route::post('task/category/update', 'api/Task/updateCategory');
Route::post('task/category/change/active', 'api/Task/changeCategoryActive');
Route::post('task/category/search', 'api/Task/searchCategory');

//task timer
Route::post('task/timer/add', 'api/Task/addTimer');

//task deadline
Route::get('task/deadline/mark', 'api/Task/deadlineMark');
Route::get('task/deadlines', 'api/Task/deadlines');
Route::post('task/deadline/change/check', 'api/Task/changeDeadlineCheck');