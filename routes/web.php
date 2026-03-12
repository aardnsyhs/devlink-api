<?php

use Illuminate\Support\Facades\Route;

Route::any('{any}', fn() => abort(404))->where('any', '.*');
