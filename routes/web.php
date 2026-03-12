<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Inertia\Inertia;
use App\Models\Product;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

Route::get('/products', function () {
    return Inertia::render('products/index');
});
Route::get('/products/create', function () {
    return Inertia::render('products/create');
});
Route::get('/products/{product}/edit', function (Product $product) {
    return Inertia::render('products/edit', [
        'product' => [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'stock' => $product->stock,
            'description' => $product->description,
            // INI KUNCINYA: Paksa menjadi URL absolut
            'image' => $product->image ? asset('storage/' . $product->image) : null,
        ]
    ]);
});
require __DIR__.'/settings.php';
