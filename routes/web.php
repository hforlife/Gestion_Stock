<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
//List
use App\Livewire\Management\ListUsers;
use App\Livewire\Management\ListPaymentMethods;
use App\Livewire\Items\ListItems;
use App\Livewire\Items\ListInventories;
use App\Livewire\Sales\ListSales;
use App\Livewire\Customer\ListCustomers;
//Edit
use App\Livewire\Management\EditPaymentMethods;
use App\Livewire\Management\EditUsers;
use App\Livewire\Items\EditItems;
use App\Livewire\Items\EditInventories;
use App\Livewire\Sales\EditSales;
use App\Livewire\Customer\EditCustomers;
//Create
use App\Livewire\Management\CreateUsers;
use App\Livewire\Management\CreatePaymentMethods;
use App\Livewire\Items\CreateInventories;
use App\Livewire\Items\CreateItems;
use App\Livewire\Sales\CreateSales;
use App\Livewire\Customer\CreateCustomers;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

    // Routes Par Défaut
Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('profile.edit');
    Route::get('settings/password', Password::class)->name('user-password.edit');
    Route::get('settings/appearance', Appearance::class)->name('appearance.edit');

    Route::get('settings/two-factor', TwoFactor::class)
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});



// Routes Projets
Route::middleware(['auth'])->group(function () {
    Route::get('/manage-users', ListUsers::class)->name('users.index');
    Route::get('/create-users', CreateUsers::class)->name('users.create');
    Route::get('/edit-users/{record}', EditUsers::class)->name('users.edit');
    //Items
    Route::get('/manage-items', ListItems::class)->name('items.index');
    Route::get('/create-items', CreateItems::class)->name('items.create');
    Route::get('/edit-items/{record}', EditItems::class)->name('items.edit');
    //Inventories
    Route::get('/manage-inventories', ListInventories::class)->name('inventories.index');
    Route::get('/create-inventories', CreateInventories::class)->name('inventories.create');
    Route::get('/edit-inventories/{record}', EditInventories::class)->name('inventories.edit');
    //Sales
    Route::get('/manage-sales', ListSales::class)->name('sales.index');
    Route::get('/create-sales', CreateSales::class)->name('sales.create');
    Route::get('/edit-sales/{record}', EditSales::class)->name('sales.edit');
    //Customer
    Route::get('/manage-customers', ListCustomers::class)->name('customers.index');
    Route::get('/create-customers', CreateCustomers::class)->name('customers.create');
    Route::get('/edit-customers/{record}', EditCustomers::class)->name('customers.edit');
    //Payment Methods
    Route::get('/manage-payment-methods', ListPaymentMethods::class)->name('payment.method.index');
    Route::get('/create-payment-methods', CreatePaymentMethods::class)->name('payment.method.create');
    Route::get('/edit-payment-methods/{record}', EditPaymentMethods::class)->name('payment.method.edit');
    // POS
    Route::get('/pos', \App\Livewire\POS::class)->name('pos');
});
