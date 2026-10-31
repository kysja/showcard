<?php

use App\Layouts\ItemsLayout;
use App\NativeComponents\ItemForm;
use App\NativeComponents\ItemList;
use App\NativeComponents\ItemShow;
use Illuminate\Support\Facades\Route;

Route::native('/', ItemList::class);
Route::native('/items/create', ItemForm::class)->layout(ItemsLayout::class);
Route::native('/items/{id}/edit', ItemForm::class)->layout(ItemsLayout::class);
Route::native('/items/{id}/show', ItemShow::class);
