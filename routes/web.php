<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\SystemLanguageController;

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ExpenseController as ControllersExpenseController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InventoryController;
use App\Models\controllers\AdminRegisterController;
use App\Models\InventoryHistory;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/keep-alive', function () {
    return response()->noContent();
});
Route::get('/terms-and-conditions', [AdminController::class, 'tot'])->name('tot');

Route::get('/suspended', function () {
    return view('auth.suspended');
})->name('suspension.page');
Route::patch('/admin/employees/{id}/reactivate', [EmployeeController::class, 'reactivate'])
    ->name('admin.employees.reactivate');

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'sw', 'ar'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('language.switch');

Route::get('/privacy', function () {
    return view('privacy');
})->name('privacy');
    Route::get('/register', [LoginController::class, 'showRegistrationForm'])->name('register');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
  Route::resource('admin/employees', EmployeeController::class)
        ->names([
            'index' => 'admin.employees.index',
            'create' => 'admin.employees.create',
            'storemployee' => 'admin.employees.store',
            'edit' => 'admin.employees.edit',
            'update' => 'admin.employees.update',
            'destroy' => 'admin.employees.destroy',
        ]);
/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::post('/admin/expenses/store', [AdminController::class, 'cost'])->name('employee.expenses.store');

Route::get('/inventory/template/download', [InventoryController::class, 'downloadTemplate'])->name('inventory.template.download');

Route::post('/inventory/bulk/upload', [InventoryController::class, 'bulkUpload'])
    ->name('inventory.bulk.upload');

Route::get('/inventory/download/template', [InventoryController::class, 'downloadTemplate'])->name('inventory.download.template');

Route::get('/credit-sales', [SaleController::class, 'creditSales'])->name('credit.sales');
Route::post('/repayment', [SaleController::class, 'recordRepayment'])->name('repayment');
Route::patch('/repayment/{id}/next-date', [SaleController::class, 'updateNextInstallment'])->name('repayment.updateNextInstallment');
Route::patch('/credit-sales/{id}/returned', [SaleController::class, 'markAsReturned'])->name('credit.sales.returned');

Route::get('/admin/operational-costs', [ControllersExpenseController::class, 'returnOperationalcost'])->name('operational-costs.store');

Route::post('/admin/operational-costs', [ControllersExpenseController::class, 'storeoperationalcost'])->name('operational-costs.store');




Route::get('/reports/adminsale', [ProductController::class, 'salesReport'])->name('reports.admins');
Route::get('/reports/EmpRecord', [ProductController::class, 'salesemp'])->name('reports.admin');

Route::get('/reports/adminRecord', [SaleController::class, 'salesReports'])->name('reports.sales');

    Route::get('/sales', [ControllersExpenseController::class, 'adminSalesWithExpenses'])->name('admin.sales.index');

 Route::get('/admin/sales/create', [SaleController::class, 'create'])->name('admin.sales.create')->middleware('auth');
  Route::post('/admin/sales/store', [SaleController::class, 'storesales'])->name('admin.sales.store')->middleware('auth');


Route::get('/inventoryfullDetails', [InventoryController::class, 'fullInventoryDetails'])->name('products.full');

 Route::get('/purchase', [AdminController::class, 'showPurchaseForm'])->name('admin.stocks.purchase');
    Route::post('/store', [AdminController::class, 'storePurchase'])->name('purchases.store');
Route::get('/my-purchases', [AdminController::class, 'index'])
    ->name('purchases.view')
    ->middleware('auth');
    Route::get('/sales/report', [SaleController::class, 'salesReport'])->name('sales.report');
Route::delete('/messages/{id}/delete', [MessageController::class, 'delete'])->name('messages.delete')->middleware('auth');

Route::post('/admin/messages/send', [MessageController::class, 'adminSend'])->name('admin.messages.send')->middleware('auth', 'admin');
Route::get('/admin/messages/sent', [MessageController::class, 'sent'])->name('admin.messages.sent');

     Route::get('/admin/messages', [MessageController::class, 'adminIndex'])->name('admin.messages.index');
    Route::post('/admin/messages/{id}/reply', [MessageController::class, 'adminReply'])->name('admin.messages.reply');
Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard')->middleware('auth');
 Route::get('/inventory/upload', [InventoryController::class, 'showUploadForm'])->name('inventory.upload.form');
    Route::post('/inventory/upload', [InventoryController::class, 'uploadInventory'])->name('inventory.upload.process');
Route::get('/inventory/lookup/{sku}', [InventoryController::class, 'lookup'])->name('inventory.lookup');
Route::get('/inventory/upload/logs', [InventoryController::class, 'uploadLogs'])->name('inventory.upload.logs');
Route::get('/inventory/listings', [InventoryController::class, 'list'])->name('inventory.list');
Route::get('/inventory/adjust', [InventoryController::class, 'showAdjustmentForm'])->name('inventory.adjust.form');
Route::post('/inventory/adjust', [InventoryController::class, 'processAdjustment'])->name('inventory.adjust.process');
Route::get('/inventory/history', [InventoryController::class, 'history'])->name('inventory.history');
Route::get('/inventory/{id}/edit', [InventoryController::class, 'editPrice'])->name('inventory.edit');
Route::put('/inventory/{id}', [InventoryController::class, 'updatePrice'])->name('inventory.update');
Route::delete('/inventory/{id}', [InventoryController::class, 'destroy'])->name('inventory.destroy');
    Route::get('/inventorys', [InventoryController::class, 'index'])->name('inventory.index');
Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.list');
        Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.products.index');
Route::patch('/admin/employees/{id}/suspend', [EmployeeController::class, 'suspend'])->name('admin.employees.suspend');
    Route::get('/expenses', [ ControllersExpenseController::class, 'index'])->name('expenses.index');

Route::get('/register', [LoginController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [LoginController::class, 'store'])->name('store');
        Route::post('/register-admin', [LoginController::class, 'register'])->name('admin.store');

Route::middleware(['auth', 'subscription'])->group(function () {
    Route::post('/settings/language', [SystemLanguageController::class, 'update'])->name('admin.settings.language.update');
    Route::get('/settings/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/settings/profile', [ProfileController::class, 'update'])->name('profile.update');
});

Route::middleware(['auth', 'subscription', 'admin'])->group(function () {
    Route::resource('/admin/products', ProductController::class);
    Route::get('/admin/sales', [AdminController::class, 'salesReport'])->name('admin.sales');
    Route::post('/admin/employees', [EmployeeController::class, 'storemployee'])->name('admin.employees.store');
    Route::post('/admin/users/status/{id}', [AdminController::class, 'toggleStatus'])->name('admin.users.status');
    Route::post('/settings/clear-data', [ProfileController::class, 'clearData'])->name('admin.settings.clear-data');
});

/*
|--------------------------------------------------------------------------
| Employee Routes
|--------------------------------------------------------------------------

*/
// For products resource CRUD routes
Route::resource('products', ProductController::class);
Route::resource('products', ProductController::class)->only(['edit', 'update', 'destroy']);

  Route::get('/expenses', [AdminController::class, 'indexs'])->name('employee.expenses.index');
    Route::post('/expenses/store', [ControllersExpenseController::class, 'store'])->name('employee.expenses.store');
    Route::delete('/expenses/{expense}', [ControllersExpenseController::class, 'destroy'])->name('employee.expenses.destroy');
Route::get('/expenses/{expense}/edit', [ControllersExpenseController::class, 'edit'])->name('employee.expenses.edit');
Route::get('/expenses/{expense}/edit', [ControllersExpenseController::class, 'edit'])->name('employee.expenses.edit');
Route::put('/expenses/{expense}', [ControllersExpenseController::class, 'update'])->name('employee.expenses.update');

 Route::get('/message', [MessageController::class, 'index'])->name('chat.index');
    Route::post('/message', [MessageController::class, 'store'])->name('chat.store');
Route::middleware(['auth', 'employee', 'subscription'])->group(function () {

    Route::get('/employee/dashboard', [EmployeeController::class, 'dashboard'])->name('employee.dashboard');
    Route::get('/employee/sales/create', [SaleController::class, 'create'])->name('employee.sales.create');
    Route::post('/employee/sales', [SaleController::class, 'store'])->name('employee.sales.store');

    Route::get('/employee/sales/history', [SaleController::class, 'history'])->name('employee.sales.history');
Route::get('/employee/sales/report', [SaleController::class, 'report'])->name('employee.sales.report');

    Route::get('/employee/prices', [InventoryController::class, 'priceLookup'])->name('employee.prices');
Route::delete('/admin/messages/{id}', [MessageController::class, 'destroy'])->name('admin.messages.delete');
  Route::get('/expenses/create', [ControllersExpenseController::class, 'create'])->name('employee.expenses.create]');
    Route::post('/expenses/store', [ControllersExpenseController::class, 'store'])->name('employee.expenses.store');
    Route::get('/employee/messages', [MessageController::class, 'create'])->name('employee.messages.create');
    Route::post('/employee/messages', [MessageController::class, 'store'])->name('employee.messages.store');
});
// Public download routes
Route::get('/download/app', [DownloadController::class, 'downloadApp'])->name('app.download');
Route::get('/download/version', [DownloadController::class, 'getVersionInfo'])->name('app.version');

// AI chat
Route::middleware(['auth'])->post('/ai/chat', [App\Http\Controllers\Ai\ChatController::class, 'sendMessage'])->name('ai.chat');

// Analytics
Route::middleware(['auth'])->get('/admin/analytics', [App\Http\Controllers\AnalyticsController::class, 'index'])->name('admin.analytics');
Route::middleware(['auth', 'admin'])->get('/admin/financial-position', [App\Http\Controllers\AnalyticsController::class, 'financialPosition'])->name('admin.financial-position');

// Secure download routes (require authentication)
Route::middleware(['auth'])->group(function () {
    Route::post('/download/generate-link', [DownloadController::class, 'generateSecureLink'])->name('app.generate-link');
    Route::get('/download/secure/{platform}', [DownloadController::class, 'secureDownload'])->name('app.secure-download');
});

/*
|--------------------------------------------------------------------------
| System Admin Routes (Super Admin only)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'super_admin'])->prefix('system-admin')->name('system-admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\SystemAdminController::class, 'dashboard'])->name('dashboard');

    // Businesses
    Route::get('/businesses', [App\Http\Controllers\SystemAdminController::class, 'businesses'])->name('businesses');
    Route::get('/businesses/{id}', [App\Http\Controllers\SystemAdminController::class, 'businessDetail'])->name('businesses.detail');
    Route::post('/businesses/{id}/toggle-status', [App\Http\Controllers\SystemAdminController::class, 'toggleBusinessStatus'])->name('businesses.toggle-status');

    // Subscriptions
    Route::get('/subscriptions', [App\Http\Controllers\SystemAdminController::class, 'subscriptions'])->name('subscriptions');
    Route::post('/subscriptions/assign', [App\Http\Controllers\SystemAdminController::class, 'assignSubscription'])->name('subscriptions.assign');
    Route::post('/subscriptions/{id}/status', [App\Http\Controllers\SystemAdminController::class, 'updateSubscriptionStatus'])->name('subscriptions.status');
    Route::post('/subscriptions/{id}/approve', [App\Http\Controllers\SystemAdminController::class, 'approveSubscription'])->name('subscriptions.approve');

    // Payments
    Route::post('/payments/record', [App\Http\Controllers\SystemAdminController::class, 'recordPayment'])->name('payments.record');

    // Plans
    Route::get('/plans', [App\Http\Controllers\SystemAdminController::class, 'plans'])->name('plans');
    Route::post('/plans', [App\Http\Controllers\SystemAdminController::class, 'storePlan'])->name('plans.store');
    Route::get('/plans/{id}/edit', function ($id) {
        $plan = App\Models\SubscriptionPlan::findOrFail($id);
        return response()->json($plan);
    })->name('plans.edit');
    Route::post('/plans/{id}', [App\Http\Controllers\SystemAdminController::class, 'updatePlan'])->name('plans.update');

    // Maintenance
    Route::post('/run-subscription-check', [App\Http\Controllers\SystemAdminController::class, 'runSubscriptionCheck'])->name('run-subscription-check');

    // AJAX
    Route::get('/search-businesses', [App\Http\Controllers\SystemAdminController::class, 'searchBusinesses'])->name('search-businesses');
});

/*
|--------------------------------------------------------------------------
| Business Admin Subscription Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/subscription', [App\Http\Controllers\SystemAdminController::class, 'mySubscription'])->name('admin.subscription.my');
    Route::get('/admin/subscription-required', [App\Http\Controllers\SystemAdminController::class, 'subscriptionRequired'])->name('admin.subscription.required');
    Route::post('/admin/subscription/subscribe', [App\Http\Controllers\SystemAdminController::class, 'subscribe'])->name('admin.subscription.subscribe');
    Route::get('/admin/subscription/status', [App\Http\Controllers\SystemAdminController::class, 'subscriptionStatus'])->name('admin.subscription.status');

    /*
    |--------------------------------------------------------------------------
    | Real-time AJAX Endpoints
    |--------------------------------------------------------------------------
    */
    Route::get('/api/stock/{id}', [App\Http\Controllers\Api\RealtimeController::class, 'stockCheck'])->name('api.stock.check');
    Route::get('/api/admin/dashboard/stats', [App\Http\Controllers\Api\RealtimeController::class, 'adminDashboardStats'])->name('api.admin.dashboard.stats');
    Route::get('/api/admin/dashboard/charts', [App\Http\Controllers\Api\RealtimeController::class, 'adminDashboardCharts'])->name('api.admin.dashboard.charts');
    Route::get('/api/employee/dashboard/stats', [App\Http\Controllers\Api\RealtimeController::class, 'employeeDashboardStats'])->name('api.employee.dashboard.stats');
    Route::get('/api/system-admin/dashboard/stats', [App\Http\Controllers\Api\RealtimeController::class, 'systemAdminDashboardStats'])->name('api.system-admin.dashboard.stats');
    Route::get('/api/messages/new', [App\Http\Controllers\Api\RealtimeController::class, 'newMessages'])->name('api.messages.new');
    Route::get('/api/inventory/stats', [App\Http\Controllers\Api\RealtimeController::class, 'inventoryStats'])->name('api.inventory.stats');
    Route::get('/api/credit-sales/updates', [App\Http\Controllers\Api\RealtimeController::class, 'creditSalesUpdates'])->name('api.credit-sales.updates');
});
