<?php

use App\Http\Controllers\backend\AccountController;
use App\Http\Controllers\backend\AccountLedgerController;
use App\Http\Controllers\backend\AccountTransferController;
use App\Http\Controllers\backend\AccountTypeController;
use App\Http\Controllers\backend\BranchAccountController;
use App\Http\Controllers\backend\BrandController;
use App\Http\Controllers\backend\BusinessJournalController;
use App\Http\Controllers\backend\CategoryController;
use App\Http\Controllers\backend\CategoryTypeController;
use App\Http\Controllers\backend\ColorController;
use App\Http\Controllers\backend\CompanySettingController;
use App\Http\Controllers\backend\CountryController;
use App\Http\Controllers\backend\CouponController;
use App\Http\Controllers\backend\CustomerController;
use App\Http\Controllers\backend\CustomerLedgerController;
use App\Http\Controllers\backend\DailyPaymentSummaryController;
use App\Http\Controllers\backend\DistrictController;
use App\Http\Controllers\backend\DivisionController;
use App\Http\Controllers\backend\ExpenseCategoryController;
use App\Http\Controllers\backend\ExpenseController;
use App\Http\Controllers\backend\FiscalYearController;
use App\Http\Controllers\backend\JournalEntryController;
use App\Http\Controllers\backend\LoyaltyController;
use App\Http\Controllers\backend\OpeningBalanceController;
use App\Http\Controllers\backend\PaperQualityController;
use App\Http\Controllers\backend\PaymentTypeController;
use App\Http\Controllers\backend\PosController;
use App\Http\Controllers\backend\PosSalePaymentController;
use App\Http\Controllers\backend\ProductController;
use App\Http\Controllers\backend\ProductTypeController;
use App\Http\Controllers\backend\PurchaseController;
use App\Http\Controllers\backend\PurchasePaymentController;
use App\Http\Controllers\backend\PurchaseReceiptController;
use App\Http\Controllers\backend\PurchaseReturnController;
use App\Http\Controllers\backend\SaleReturnController;
use App\Http\Controllers\backend\SizeController;
use App\Http\Controllers\backend\StockAdjustmentController;
use App\Http\Controllers\backend\StockLedgerController;
use App\Http\Controllers\backend\StockOpeningController;
use App\Http\Controllers\backend\StockTransferController;
use App\Http\Controllers\backend\SubCategoryController;
use App\Http\Controllers\backend\SupplierController;
use App\Http\Controllers\backend\UnitController;
use App\Http\Controllers\backend\UpazilaController;
use App\Http\Controllers\backend\WarehouseController;
use App\Http\Controllers\backend\SalesReportController;
use App\Http\Controllers\backend\PurchaseReportController;
use App\Http\Controllers\backend\ExpenseReportController;
use App\Http\Controllers\backend\OfferController;
use App\Http\Controllers\backend\ReportModuleController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'perm', 'branchscope'])->group(function () {
    Route::prefix('country')->name('country.')->group(function () {

        Route::get('countries', [CountryController::class, 'index'])->name('countries.index');
        Route::get('countries/create-modal', [CountryController::class, 'createModal'])->name('countries.createModal');
        Route::post('countries/list', [CountryController::class, 'listAjax'])->name('countries.list.ajax');
        Route::post('countries', [CountryController::class, 'store'])->name('countries.store');
        Route::get('countries/{country}/edit-modal', [CountryController::class, 'editModal'])->whereNumber('country')->name('countries.editModal');
        Route::get('countries/{country}', [CountryController::class, 'show'])->name('countries.show');
        Route::put('countries/{country}', [CountryController::class, 'update'])->name('countries.update');
        Route::delete('countries/{country}', [CountryController::class, 'destroy'])->name('countries.destroy');
    });

    // Division Management

    Route::prefix('division')->name('division.')->group(function () {

        Route::get('divisions', [DivisionController::class, 'index'])->name('divisions.index');
        Route::get('divisions/create-modal', [DivisionController::class, 'createModal'])->name('divisions.createModal');
        Route::post('divisions/list', [DivisionController::class, 'listAjax'])->name('divisions.list.ajax');
        Route::post('divisions', [DivisionController::class, 'store'])->name('divisions.store');
        Route::get('divisions/{division}/edit-modal', [DivisionController::class, 'editModal'])->whereNumber('division')->name('divisions.editModal');
        Route::get('divisions/{division}', [DivisionController::class, 'show'])->name('divisions.show');
        Route::put('divisions/{division}', [DivisionController::class, 'update'])->name('divisions.update');
        Route::delete('divisions/{division}', [DivisionController::class, 'destroy'])->name('divisions.destroy');
    });

    // District Management

    Route::prefix('district')->name('district.')->group(function () {

        Route::get('districts', [DistrictController::class, 'index'])->name('districts.index');
        Route::get('districts/create-modal', [DistrictController::class, 'createModal'])->name('districts.createModal');
        Route::post('districts/list', [DistrictController::class, 'listAjax'])->name('districts.list.ajax');
        Route::post('districts', [DistrictController::class, 'store'])->name('districts.store');
        Route::get('districts/{district}/edit-modal', [DistrictController::class, 'editModal'])->whereNumber('district')->name('districts.editModal');
        Route::get('districts/{district}', [DistrictController::class, 'show'])->name('districts.show');
        Route::put('districts/{district}', [DistrictController::class, 'update'])->name('districts.update');
        Route::delete('districts/{district}', [DistrictController::class, 'destroy'])->name('districts.destroy');
    });

    // Upazila Management

    Route::prefix('upazila')->name('upazila.')->group(function () {

        Route::get('upazilas', [UpazilaController::class, 'index'])->name('upazilas.index');
        Route::get('upazilas/create-modal', [UpazilaController::class, 'createModal'])->name('upazilas.createModal');
        Route::post('upazilas/list', [UpazilaController::class, 'listAjax'])->name('upazilas.list.ajax');
        Route::post('upazilas', [UpazilaController::class, 'store'])->name('upazilas.store');
        Route::get('upazilas/{upazila}/edit-modal', [UpazilaController::class, 'editModal'])->whereNumber('upazila')->name('upazilas.editModal');
        Route::get('upazilas/{upazila}', [UpazilaController::class, 'show'])->name('upazilas.show');
        Route::put('upazilas/{upazila}', [UpazilaController::class, 'update'])->name('upazilas.update');
        Route::delete('upazilas/{upazila}', [UpazilaController::class, 'destroy'])->name('upazilas.destroy');
    });
    //category type management
    Route::prefix('category-type')->name('category-type.')->group(function () {

        Route::get('category-types', [CategoryTypeController::class, 'index'])->name('category-types.index');
        Route::get('category-types/create-modal', [CategoryTypeController::class, 'createModal'])->name('category-types.createModal');
        Route::post('category-types/list', [CategoryTypeController::class, 'listAjax'])->name('category-types.list.ajax');
        Route::post('category-types', [CategoryTypeController::class, 'store'])->name('category-types.store');
        Route::get('category-types/{categoryType}/edit-modal', [CategoryTypeController::class, 'editModal'])->whereNumber('categoryType')->name('category-types.editModal');
        Route::put('category-types/{categoryType}', [CategoryTypeController::class, 'update'])->name('category-types.update');
        Route::get('category-types/{CategoryType}', [CategoryTypeController::class, 'show'])->name('category-types.show');
        Route::delete('category-types/{CategoryType}', [CategoryTypeController::class, 'destroy'])->name('category-types.destroy');

        // Route::get('category-types/select2', [CategoryTypeController::class, 'select2'])->name('category-types.select2');
        // Route::get('category-types/select2', [CategoryTypeController::class, 'select2'])->name('types.select2');

    });

    // Category Management

    Route::prefix('category')->name('category.')->group(function () {

        Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::get('categories/create-modal', [CategoryController::class, 'createModal'])->name('categories.createModal');
        Route::post('categories/list', [CategoryController::class, 'listAjax'])->name('categories.list.ajax');
        Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('categories/{category}/edit-modal', [CategoryController::class, 'editModal'])->whereNumber('category')->name('categories.editModal');
        Route::put('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::get('categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
        Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
        //live serach
        Route::get('category-types/select2', [CategoryTypeController::class, 'select2'])->name('types.select2');
        Route::get('categories/select2/type', [CategoryController::class, 'select2'])->name('cat.select2');
    });

    // Subcategory Management

    Route::prefix('subcategory')->name('subcategory.')->group(function () {

        Route::get('subcategories', [SubCategoryController::class, 'index'])->name('subcategories.index');
        Route::get('subcategories/create-modal', [SubCategoryController::class, 'createModal'])->name('subcategories.createModal');
        Route::post('subcategories/list', [SubCategoryController::class, 'listAjax'])->name('subcategories.list.ajax');
        Route::post('subcategories', [SubCategoryController::class, 'store'])->name('subcategories.store');
        Route::get('subcategories/{subcategory}/edit-modal', [SubCategoryController::class, 'editModal'])->whereNumber('subcategory')->name('subcategories.editModal');
        Route::get('subcategories/{subcategory}', [SubCategoryController::class, 'show'])->name('subcategories.show');
        Route::put('subcategories/{subcategory}', [SubCategoryController::class, 'update'])->name('subcategories.update');
        Route::delete('subcategories/{subcategory}', [SubCategoryController::class, 'destroy'])->name('subcategories.destroy');

        Route::get('subcategories/select2/type', [SubCategoryController::class, 'select2'])->name('select2');
    });

    // Brands Management

    Route::prefix('brand')->name('brand.')->group(function () {

        Route::get('brands', [BrandController::class, 'index'])->name('brands.index');
        Route::get('brands/create', [BrandController::class, 'createModal'])->name('brands.create');
        Route::post('brands/list', [BrandController::class, 'listAjax'])->name('brands.list.ajax');
        Route::post('brands', [BrandController::class, 'store'])->name('brands.store');
        Route::get('brands/edit/{brand}', [BrandController::class, 'editModal'])->whereNumber('brand')->name('brands.edit');
        Route::put('brands/{brand}', [BrandController::class, 'update'])->name('brands.update');
        Route::get('brands/{brand}', [BrandController::class, 'show'])->name('brands.show');
        Route::delete('brands/{brand}', [BrandController::class, 'destroy'])->name('brands.destroy');
        Route::get('brand/select2/type', [BrandController::class, 'select2'])->name('select2');
    });

    // Unit Management
    Route::prefix('units')->name('units.')->group(function () {

        Route::get('/', [UnitController::class, 'index'])->name('index');
        Route::post('/list', [UnitController::class, 'listAjax'])->name('list.ajax');
        Route::get('/create-modal', [UnitController::class, 'createModal'])->name('createModal');
        Route::get('/{unit}/edit-modal', [UnitController::class, 'editModal'])
            ->whereNumber('unit')->name('editModal');
        Route::post('/', [UnitController::class, 'store'])->name('store');
        Route::put('/{unit}', [UnitController::class, 'update'])->whereNumber('unit')->name('update');
        Route::delete('/{unit}', [UnitController::class, 'destroy'])->whereNumber('unit')->name('destroy');
        Route::get('unit/select2/type', [UnitController::class, 'select2'])->name('select2');
    });

    //color management
    Route::prefix('color')->name('color.')->group(function () {

        Route::get('colors', [ColorController::class, 'index'])->name('colors.index');
        Route::get('colors/create-modal', [ColorController::class, 'createModal'])->name('colors.createModal');
        Route::post('colors/list', [ColorController::class, 'listAjax'])->name('colors.list.ajax');
        Route::post('colors', [ColorController::class, 'store'])->name('colors.store');
        Route::get('colors/edit-modal/{color}', [ColorController::class, 'editModal'])->whereNumber('color')->name('colors.editModal');
        Route::put('colors/{color}', [ColorController::class, 'update'])->name('colors.update');
        Route::get('colors/{color}', [ColorController::class, 'show'])->name('colors.show');
        Route::delete('colors/{color}', [ColorController::class, 'destroy'])->name('colors.destroy');
        Route::get('colors/select2/type', [ColorController::class, 'select2'])->name('select2');
    });

    // Size Management
    Route::prefix('sizes')->name('sizes.')->group(function () {

        Route::get('/', [SizeController::class, 'index'])->name('index');
        Route::post('/list', [SizeController::class, 'listAjax'])->name('list.ajax');
        Route::get('/create-modal', [SizeController::class, 'createModal'])->name('createModal');
        Route::get('/{size}/edit-modal', [SizeController::class, 'editModal'])->whereNumber('size')->name('editModal');
        Route::post('/', [SizeController::class, 'store'])->name('store');
        Route::put('/{size}', [SizeController::class, 'update'])->whereNumber('size')->name('update');
        Route::delete('/{size}', [SizeController::class, 'destroy'])->whereNumber('size')->name('destroy');
        Route::get('size/select2/type', [SizeController::class, 'select2'])->name('select2');
    });
    // paper quality Management
    Route::prefix('paper_quality')->name('paper_quality.')->group(function () {

        Route::get('/', [PaperQualityController::class, 'index'])->name('index');
        Route::post('paper_quality/list', [PaperQualityController::class, 'listAjax'])->name('list.ajax');
        Route::get('paper_quality/create-modal', [PaperQualityController::class, 'createModal'])->name('createModal');
        Route::get('paper_quality/{paperQuality}/edit-modal', [PaperQualityController::class, 'editModal'])->whereNumber('size')->name('editModal');
        Route::post('/paper_quality', [PaperQualityController::class, 'store'])->name('store');
        Route::put('paper_quality/{paperQuality}', [PaperQualityController::class, 'update'])->whereNumber('paperQuality')->name('update');
        Route::delete('paper_quality/{paperQuality}', [PaperQualityController::class, 'destroy'])->whereNumber('paperQuality')->name('destroy');
        Route::get('paper_quality/select2/type', [PaperQualityController::class, 'select2'])->name('select2');
    });

    // Product Management
    Route::prefix('product')->name('product.')->group(function () {

        Route::get('products', [ProductController::class, 'index'])->name('products.index');
        // Route::get('product/view/{product}', [ProductController::class, 'index'])->name('products.view');
        Route::get('products/create', [ProductController::class, 'createModal'])->name('products.create');
        Route::post('products/list', [ProductController::class, 'listAjax'])->name('products.list.ajax');
        Route::post('products', [ProductController::class, 'store'])->name('products.store');
        Route::get('products/edit-modal/{product}', [ProductController::class, 'editModal'])->whereNumber('product')->name('products.editModal');
        Route::post('products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::get('products/{product}', [ProductController::class, 'show'])->name('products.show');
        Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
        Route::get('products/category', [ProductController::class, 'select2Category'])->name('products.category');
        Route::get('products/subcategory', [ProductController::class, 'select2Subcategory'])->name('products.subcategory');
        Route::get('products/brand', [ProductController::class, 'select2Brand'])->name('products.brand');
        Route::get('products/color', [ProductController::class, 'select2Color'])->name('products.color');
        Route::get('products/size', [ProductController::class, 'select2Size'])->name('products.size');
        Route::get('products/product-type', [ProductController::class, 'select2ProductType'])->name('products.product-type');
        Route::get('products/select2/all', [ProductController::class, 'select2'])->name('select2');
        // Parent list
        Route::get('/products/parents/get', [ProductController::class, 'parentsIndex'])->name('parents.index');
        Route::get('/products/parents/select2', [ProductController::class, 'parentsSelect2'])->name('parents.select2');
        Route::get('/products/{product}/variants', [ProductController::class, 'variants'])->name('variants');
        Route::get('products/import_csv/file', [ProductController::class, 'importCsvModal'])->name('import_csv');
        Route::post('products/handle/upload_csv', [ProductController::class, 'importCsv'])->name('handle_csv');

        Route::get('products/product-list/allProducts', [ProductController::class, 'productList'])->name('productsList');
        Route::get('products/childProductList/{product}', [ProductController::class, 'childProductList'])->name('childProductList');
        Route::get('products/product-search/{name}', [ProductController::class, 'productSearch'])->name('productsSearch');
        Route::get('products/product-byCategory/{category}', [ProductController::class, 'productByCategory'])->name('productsByCategory');
        Route::get('products/barcode/index', [ProductController::class, 'barcodeIndex'])->name('products.barcode');
        Route::get('products/{product}/barcode-preview', [ProductController::class, 'barcodePreview'])->name('barcode.preview');
    });

    // Product type Management
    Route::prefix('product-type')->name('product-type.')->group(function () {

        Route::get('product-types', [ProductTypeController::class, 'index'])->name('product-types.index');
        Route::get('product-types/create-modal', [ProductTypeController::class, 'createModal'])->name('product-types.createModal');
        Route::post('product-types/list', [ProductTypeController::class, 'listAjax'])->name('product-types.list.ajax');
        Route::post('product-types', [ProductTypeController::class, 'store'])->name('product-types.store');
        Route::get('product-types/edit-modal/{productType}', [ProductTypeController::class, 'editModal'])->whereNumber('productType')->name('product-types.editModal');
        Route::put('product-types/{productType}', [ProductTypeController::class, 'update'])->name('product-types.update');
        Route::get('product-types/{productType}', [ProductTypeController::class, 'show'])->name('product-types.show');
        Route::delete('product-types/{productType}', [ProductTypeController::class, 'destroy'])->name('product-types.destroy');
        Route::get('product-types/select2/type', [ProductTypeController::class, 'select2'])->name('select2');
    });

    // Inventory Opening Stock
    Route::prefix('inventory')->name('inventory.')->group(function () {

        // Warehouse Management
        Route::get('warehouses', [WarehouseController::class, 'index'])->name('warehouses.index');
        Route::post('warehouses/list', [WarehouseController::class, 'listAjax'])->name('warehouses.list.ajax');
        Route::get('warehouses/create-modal', [WarehouseController::class, 'createModal'])->name('warehouses.createModal');
        Route::get('warehouses/{warehouse}/edit-modal', [WarehouseController::class, 'editModal'])->name('warehouses.editModal');
        Route::post('warehouses/', [WarehouseController::class, 'store'])->name('warehouses.store');
        Route::put('warehouses/{warehouse}', [WarehouseController::class, 'update'])->name('warehouses.update');
        Route::delete('warehouses/{warehouse}', [WarehouseController::class, 'destroy'])->name('warehouses.destroy');
        Route::get('warehouses/select2', [WarehouseController::class, 'select2'])->name('warehouses.select2');
        Route::get('warehouses/{warehouse}/ajax', [WarehouseController::class, 'showForAjax'])->name('warehouses.showForAjax');

        // Opening Stock Management

        Route::get('openingStock', [StockOpeningController::class, 'index'])->name('openingStock.index');
        Route::post('openingStock/list', [StockOpeningController::class, 'listAjax'])->name('openingStock.list');
        Route::get('openingStock/create-modal', [StockOpeningController::class, 'createModal'])->name('openingStock.createModal');
        Route::post('openingStock', [StockOpeningController::class, 'store'])->name('openingStock.store');

        // Transfer Stock Management
        Route::get('transfers', [StockTransferController::class, 'index'])->name('transfers.index');
        Route::post('transfers/list', [StockTransferController::class, 'listAjax'])->name('transfers.list');
        Route::get('transfers/create', [StockTransferController::class, 'create'])->name('transfers.create');
        Route::post('transfers/store', [StockTransferController::class, 'store'])->name('transfers.store');
        Route::delete('transfers/{transfer}/delete', [StockTransferController::class, 'destroy'])->name('transfers.destroy');
        Route::get('transfers/{transfer}', [StockTransferController::class, 'show'])->name('transfers.show');
        Route::post('transfers/{transfer}/post', [StockTransferController::class, 'post'])->name('transfers.post');

        Route::get('transfers/{transfer}/edit', [StockTransferController::class, 'edit'])->name('transfers.edit');
        Route::post('transfers/{transfer}', [StockTransferController::class, 'update'])->name('transfers.update');

        // Adjust Stock Management
        Route::get('adjustments', [StockAdjustmentController::class, 'index'])->name('adjustments.index');
        Route::post('adjustments/list', [StockAdjustmentController::class, 'listAjax'])->name('adjustments.list');
        Route::get('adjustments/create', [StockAdjustmentController::class, 'create'])->name('adjustments.create');
        Route::post('adjustments', [StockAdjustmentController::class, 'store'])->name('adjustments.store');

        Route::POST('adjustments/{id}/post', [StockAdjustmentController::class, 'post'])->name('adjustments.post');
        Route::get('adjustments/{adjustment}', [StockAdjustmentController::class, 'show'])->name('adjustments.show');
        Route::get('adjustments/{ledger}/edit', [StockAdjustmentController::class, 'edit'])->name('adjustments.edit');
        Route::POST('adjustments/{ledger}/cancel', [StockAdjustmentController::class, 'cancel'])->name('adjustments.cancel');
        Route::put('adjustments/{ledger}', [StockAdjustmentController::class, 'update'])->name('adjustments.update');
        Route::delete('adjustments/{ledger}/delete', [StockAdjustmentController::class, 'destroy'])->name('adjustments.destroy');

        // bulk system qty endpoint
        Route::post('adjustments/stock-currents/bulk', [StockAdjustmentController::class, 'systemQtyBulk'])->name('adjustments.stock.currents.bulk');

        // store already exists per your previous code

        Route::get('adjustments/parent/{parent}/edit', [StockAdjustmentController::class, 'editParent'])
            ->name('adjustments.parent.edit');

        // product variants endpoint (if not already present)
        Route::get('product/{parent}/variants', [StockAdjustmentController::class, 'ajaxParentVariants'])->name('product.variants');

        Route::put('adjustments/parent/{parent}', [StockAdjustmentController::class, 'updateParent'])
            ->name('adjustments.parent.update');
        Route::get('adjustments/parent-variants', [StockAdjustmentController::class, 'ajaxParentVariants'])
            ->name('adjustments.parent.variants');

        //stock ledger report
        Route::get('stock-ledger', [StockLedgerController::class, 'index'])->name('reports.stock-ledger.index');
        Route::post('stock-ledger/list-ajax', [StockLedgerController::class, 'listAjax'])->name('reports.stock-ledger.list.ajax');
        Route::get('stock-ledger/summary', [StockLedgerController::class, 'summary'])->name('reports.stock-ledger.summary');
        Route::get('stock-ledger/reference/{type}/{id}', [StockLedgerController::class, 'referenceView'])->name('reports.stock-ledger.reference.view');
    });

    // General Reports (placeholder controllers)
    Route::prefix('reports')->name('reports.')->group(function () {

        // Reports module index (shows cards linking to each report)
        Route::get('/', [ReportModuleController::class, 'index'])->name('index');

        // Sales Report
        Route::get('sales', [SalesReportController::class, 'index'])->name('sales');
        Route::post('sales/list', [SalesReportController::class, 'listAjax'])->name('sales.list.ajax');
        Route::get('sales/summary', [SalesReportController::class, 'summary'])->name('sales.summary');

        //Source Base Sales Report
        Route::get('source-base-sales', [SalesReportController::class, 'sourceBasedIndex'])->name('source-base-sales');
        Route::post('source-base-sales/list', [SalesReportController::class, 'sourceBasedListAjax'])->name('source-base-sales.list.ajax');
        Route::get('source-base-sales/summary', [SalesReportController::class, 'sourceBasedSummary'])->name('source-base-sales.summary');
        //Product Wise Sales Report
        Route::get('product-wise-sales', [SalesReportController::class, 'productWiseIndex'])->name('product-wise-sales');
        Route::post('product-wise-sales/list', [SalesReportController::class, 'productWiseListAjax'])->name('product-wise-sales.list.ajax');
        Route::get('product-wise-sales/summary', [SalesReportController::class, 'productWiseSummary'])->name('product-wise-sales.summary');

        // Purchase Report
        Route::get('purchase', [PurchaseReportController::class, 'index'])->name('purchase');
        Route::post('purchase/list', [PurchaseReportController::class, 'listAjax'])->name('purchase.list.ajax');
        Route::get('purchase/summary', [PurchaseReportController::class, 'summary'])->name('purchase.summary');

        // Expense Report
        Route::get('expenses', [ExpenseReportController::class, 'index'])->name('expenses');
        Route::post('expenses/list', [ExpenseReportController::class, 'listAjax'])->name('expenses.list.ajax');
        Route::get('expenses/summary', [ExpenseReportController::class, 'summary'])->name('expenses.summary');

        // Inventory report (uses existing StockLedgerController)
        Route::get('inventory', [StockLedgerController::class, 'index'])->name('inventory');

    });

    // Supplier Management
    Route::prefix('supplier')->name('supplier.')->group(function () {

        Route::get('suppliers', [SupplierController::class, 'index'])->name('index');
        Route::get('suppliers/create', [SupplierController::class, 'create'])->name('create');
        Route::get('suppliers/createModal', [SupplierController::class, 'createModal'])->name('createModal');
        Route::post('suppliers/list', [SupplierController::class, 'listAjax'])->name('list.ajax');
        Route::post('suppliers', [SupplierController::class, 'store'])->name('store');
        Route::get('suppliers/edit/{supplier}', [SupplierController::class, 'editModal'])->whereNumber('supplier')->name('edit');
        Route::put('suppliers/{supplier}', [SupplierController::class, 'update'])->name('update');
        Route::get('suppliers/{supplier}', [SupplierController::class, 'show'])->name('show');
        Route::delete('suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('destroy');
        Route::get('suppliers/select2/type', [SupplierController::class, 'select2'])->name('select2');
        Route::get('suppliers/import_csv/file', [SupplierController::class, 'importCsvModal'])->name('import_csv');
        Route::post('suppliers/handle/upload_csv', [SupplierController::class, 'importCsv'])->name('handle_csv');
        Route::get('suppliers/recent/supplier', [SupplierController::class, 'recent_record'])->name('recent');
     
    });

    //supplier ledger
    Route::prefix('supplier-ledger')->name('supplier.')->group(function () {

        Route::get('suppliers/ledger/view', [SupplierController::class, 'ledger'])->name('ledger');
        Route::post('suppliers/ledger/list', [SupplierController::class, 'ledgerList'])->name('ledger.list');
        Route::get('suppliers/ledger/summary', [SupplierController::class, 'ledgerSummary'])->name('ledger.summary');
        Route::get('suppliers/ledger/reference/{type}/{id}',[SupplierController::class, 'supplierLedgerReference'])->name('ledger.reference.view');
    });

    // Customer Management
    Route::prefix('customer')->name('customer.')->group(function () {

        Route::get('customers', [CustomerController::class, 'index'])->name('index');
        Route::get('customers/create', [CustomerController::class, 'createModal'])->name('create');
        Route::post('customers/list', [CustomerController::class, 'listAjax'])->name('list.ajax');
        Route::post('customers', [CustomerController::class, 'store'])->name('store');
        Route::get('customers/edit/{customer}', [CustomerController::class, 'editModal'])->whereNumber('customer')->name('edit');
        Route::put('customers/{customer}', [CustomerController::class, 'update'])->name('update');
        Route::get('customers/{customer}', [CustomerController::class, 'show'])->name('show');
        Route::get('customers/import_csv/file', [CustomerController::class, 'importCsvModal'])->name('import_csv');
        Route::post('customers/handle/upload_csv', [CustomerController::class, 'importCsv'])->name('handle_csv');
        Route::delete('customers/{customer}', [CustomerController::class, 'destroy'])->name('destroy');
        Route::get('customers/select2/type', [CustomerController::class, 'select2'])->name('select2');
    });
    // Customer Ledger Route
    Route::prefix('customer-ledger')->name('customerLedger.')->group(function () {

        Route::get('customer-ledger', [CustomerLedgerController::class, 'index'])->name('index');
        Route::post('customer-ledger/list-ajax', [CustomerLedgerController::class, 'listAjax'])->name('list.ajax');
        Route::get('customer-ledger/summary', [CustomerLedgerController::class, 'summary'])->name('summary');
        Route::get('customer-ledger/reference/{type}/{id}', [CustomerLedgerController::class, 'referenceView'])->name('reference.view');

    });

    // POS Management
    Route::prefix('pos')->name('pos.')->group(function () {
 
        Route::get('pos', [PosController::class, 'index'])->name('index');
        Route::post('pos/sales', [PosController::class, 'store'])->name('sales.store');

        Route::post('pos/sales/hold', [PosController::class, 'hold'])->name('sales.hold');
        Route::get('pos/sales/hold/list', [PosController::class, 'holdList'])->name('sales.hold.list');
        Route::get('pos/sales/{sale}/resume', [PosController::class, 'resume'])->name('sales.resume');
        Route::get('pos/sales/{sale}/invoice', [PosController::class, 'invoice'])->name('sales.invoice');
        Route::get('pos/sales/today', [PosController::class, 'todayOrders'])->name('sales.today');
        Route::post('pos/sales/{sale}/void', [PosController::class, 'void'])->name('sales.void');
        Route::get('pos/sales/{sale}', [PosController::class, 'show'])->name('sales.show');
        Route::get('pos/sales/{sale}/invoice', [PosController::class, 'invoice'])->name('sales.invoice');
        Route::get('pos/transactions/today', [PosController::class, 'todayTransactions'])->name('transactions.today');
        Route::get('pos/product/by-barcode', [PosController::class, 'productByBarcode'])->name('product.byBarcode');
        Route::get('pos/sales_list', [PosController::class, 'list'])->name('sales.list');
        Route::post('pos/sales/list/ajax', [PosController::class, 'listAjax'])->name('sales.list.ajax');
        Route::get('pos/sales/{sale}/status-modal', [PosController::class, 'statusModal'])->name('sales.status.modal');
        Route::post('pos/sales/{sale}/update-status', [PosController::class, 'updateStatus'])->name('sales.updateStatus');

        Route::get('pos/sales/{sale}/payment-status-modal', [PosController::class, 'paymentStatusModal'])->name('sales.payment_status.modal');
        Route::post('pos/sales/{sale}/update-payment-status', [PosController::class, 'updatePaymentStatus'])->name('sales.updatePaymentStatus');

        // Payment receive modal
        Route::get('pos/sales/{sale}/payment-modal', [PosSalePaymentController::class, 'create'])->name('sales.payment.modal');
        // Store received payment
        Route::post('pos/sales/{sale}/payments', [PosSalePaymentController::class, 'store'])->name('sales.payments.store');

        // Cash Settlement
        // Route::get('cash-settlement/create', [CashSettlementController::class, 'create'])->name('cash-settlement.create');
        // Route::post('cash-settlement/store', [CashSettlementController::class, 'store'])->name('cash-settlement.store');

        // Sale Return
        Route::get('pos/{sale}/return', [SaleReturnController::class, 'create'])->name('sales.return.create');
        Route::post('pos/{sale}/return', [SaleReturnController::class, 'store'])->name('sales.return.store');
        Route::get('pos/sale-returns', [SaleReturnController::class, 'index'])->name('saleReturns.index');
        Route::post('pos/sale-returns/list', [SaleReturnController::class, 'listAjax'])->name('saleReturns.list.ajax');
        Route::get('pos/sale-returns/{saleReturn}', [SaleReturnController::class, 'show'])->name('saleReturns.show');
        Route::get('pos/sale-returns/{saleReturn}/payment-modal', [SaleReturnController::class, 'paymentModal'])->name('saleReturns.payment.modal');
        Route::post('pos/sale-returns/{saleReturn}/payments', [SaleReturnController::class, 'storePayment'])->name('saleReturns.payment.store');
        Route::delete('pos/sale-returns/{saleReturn}', [SaleReturnController::class, 'destroy'])->name('saleReturns.destroy');

    });

    // POS Reports Management

    Route::prefix('pos-reports')->name('pos.reports.')->group(function () {

        Route::get('daily-payment-summary', [DailyPaymentSummaryController::class, 'index'])->name('daily-payment.index');
        Route::post('daily-payment-summary/list', [DailyPaymentSummaryController::class, 'listAjax'])->name('daily-payment.list.ajax');

    });

    // Purchase Management
    Route::prefix('purchase')->name('purchase.')->group(function () {

        Route::get('purchase', [PurchaseController::class, 'index'])->name('index');
        Route::get('purchase/create', [PurchaseController::class, 'create'])->name('orders.create');
        Route::post('purchase/list', [PurchaseController::class, 'listAjax'])->name('list.ajax');
        Route::post('purchase', [PurchaseController::class, 'store'])->name('orders.store');
        Route::get('purchase/edit/{purchase}', [PurchaseController::class, 'edit'])->whereNumber('purchase')->name('orders.edit');

        Route::put('purchase/{purchase}', [PurchaseController::class, 'update'])->name('orders.update');
        Route::delete('purchase/{purchase}', [PurchaseController::class, 'destroy'])->name('orders.destroy');
        Route::get('purchase/select2/type', [PurchaseController::class, 'select2'])->name('orders.select2');
        Route::get('purchase/orders/{order}', [PurchaseController::class, 'show'])->name('orders.show');

        Route::get('purchase/orders/{order}/payment-modal', [PurchaseController::class, 'paymentModal'])->name('orders.payment.modal');
        Route::post('purchase/orders/{order}/payments', [PurchasePaymentController::class, 'storeForOrder'])
            ->name('orders.payments.store');

        Route::get('purchase/orders/{order}/receive-all-modal', [PurchaseReceiptController::class, 'receiveAllModal'])
            ->name('orders.receive-all.modal');
        Route::post('purchase/receipts', [PurchaseReceiptController::class, 'store'])
            ->name('receipts.store');

        Route::get('purchase/edit/{purchase}', [PurchaseController::class, 'edit'])->whereNumber('purchase')->name('orders.edit');
        Route::put('purchase/{purchase}', [PurchaseController::class, 'update'])->name('orders.update');

        Route::get('purchase/return/index', [PurchaseReturnController::class, 'index'])->name('return.index');
        Route::post('purchase/return/list', [PurchaseReturnController::class, 'listAjax'])->name('return.list.ajax');
        Route::get('purchase/return/show/{purchaseReturn}', [PurchaseReturnController::class, 'show'])->name('return.show');
        Route::get('purchase/purchase-return', [PurchaseReturnController::class, 'purchaseReturn'])->name('return');
        Route::get('purchase/return-order/details/{order}', [PurchaseReturnController::class, 'returnOrderDetails'])->name('returnOrder.details');
        Route::post('purchase/return/purchase', [PurchaseReturnController::class, 'returnPurchaseOrder'])->name('returnPurchaseOrder');
        Route::get('purchase/return/payment-modal/{purchaseReturn}', [PurchaseReturnController::class, 'paymentModal'])->name('return.payment.modal');
        Route::post('purchase/return/payments/{purchaseReturn}', [PurchaseReturnController::class, 'storePayment'])->name('return.payments.store');
        Route::delete('purchase/return/destroy/{purchaseReturn}', [PurchaseReturnController::class, 'destroy'])->name('return.destroy');

    });

    // Payment Type Management
    Route::prefix('paymentTypes')->name('paymentTypes.')->group(function () {

        Route::get('paymentType', [PaymentTypeController::class, 'index'])->name('index');
        Route::get('paymentType/create', [PaymentTypeController::class, 'createModal'])->name('create');
        Route::post('paymentType/list', [PaymentTypeController::class, 'listAjax'])->name('list.ajax');
        Route::post('paymentType', [PaymentTypeController::class, 'store'])->name('store');
        Route::get('paymentType/edit/{paymentType}', [PaymentTypeController::class, 'editModal'])->whereNumber('paymentType')->name('edit');
        Route::put('paymentType/{paymentType}', [PaymentTypeController::class, 'update'])->name('update');
        Route::get('paymentType/{paymentType}', [PaymentTypeController::class, 'show'])->name('show');
        Route::delete('paymentType/{paymentType}', [PaymentTypeController::class, 'destroy'])->name('destroy');
        Route::get('paymentType/select2/type', [PaymentTypeController::class, 'select2'])->name('select2');
    });

    // Coupon Management
    Route::prefix('coupon')->name('coupon.')->group(function () {

        Route::get('coupon', [CouponController::class, 'index'])->name('index');
        Route::get('coupon/create', [CouponController::class, 'create'])->name('create');
        Route::post('coupon/list', [CouponController::class, 'listAjax'])->name('list.ajax');
        Route::post('coupon', [CouponController::class, 'store'])->name('store');
        Route::get('coupon/edit/{coupon}', [CouponController::class, 'edit'])->whereNumber('coupon')->name('edit');
        Route::put('coupon/{coupon}', [CouponController::class, 'update'])->name('update');
        Route::get('coupon/{coupon}', [CouponController::class, 'show'])->name('show');
        Route::get('coupon/add/coupon-products/{coupon}', [CouponController::class, 'couponProducts'])->name('addProducts');
        Route::post('coupon/store/coupon-products/{coupon}', [CouponController::class, 'storeCouponProducts'])->name('storeCouponProducts');
        Route::delete('coupon/{coupon}', [CouponController::class, 'destroy'])->name('destroy');
        Route::get('coupon/select2/type', [CouponController::class, 'select2'])->name('select2');
        Route::get('coupon/allCoupon/{userId}', [CouponController::class, 'allCouponByUser'])->name('allCouponByUser');
        Route::post('coupon/calculate/discount/preview', [CouponController::class, 'preview'])->name('preview');

        Route::post('coupon/product/list/{coupon}', [CouponController::class, 'allCouponAssociates'])->name('allCouponAssociates');
        Route::delete('coupon-product/{pivot}/{type}', [CouponController::class, 'couponAssociatesDestroy'])->name('couponAssociates.destroy');
    });

    // Loyalty Management
    Route::prefix('loyalty')->name('loyalty.')->group(function () {

        Route::get('loyalty', [LoyaltyController::class, 'index'])->name('index');
        Route::get('loyalty/create', [LoyaltyController::class, 'createModal'])->name('createModal');
        Route::post('loyalty/list', [LoyaltyController::class, 'listAjax'])->name('list.ajax');
        Route::post('loyalty', [LoyaltyController::class, 'store'])->name('store');
        Route::get('loyalty/edit/{loyaltyRule}', [LoyaltyController::class, 'editModal'])->whereNumber('loyaltyRule')->name('editModal');
        Route::put('loyalty/{loyaltyRule}', [LoyaltyController::class, 'update'])->name('update');
        Route::get('loyalty/{loyaltyRule}', [LoyaltyController::class, 'show'])->name('show');
        Route::delete('loyalty/{loyaltyRule}', [LoyaltyController::class, 'destroy'])->name('destroy');

        Route::get('loyalty/points/{userId}', [LoyaltyController::class, 'userLoyaltyPoints'])->name('userLoyaltyPoints');

    });

    // Expense Category Management
    Route::prefix('expenseCategories')->name('expenseCategories.')->group(function () {

        Route::get('expenseCategories', [ExpenseCategoryController::class, 'index'])->name('index');
        Route::get('expenseCategories/create', [ExpenseCategoryController::class, 'createModal'])->name('create');
        Route::post('expenseCategories/list', [ExpenseCategoryController::class, 'listAjax'])->name('list.ajax');
        Route::post('expenseCategories', [ExpenseCategoryController::class, 'store'])->name('store');
        Route::get('expenseCategories/edit/{expenseCategory}', [ExpenseCategoryController::class, 'editModal'])->whereNumber('expenseCategory')->name('edit');
        Route::put('expenseCategories/{expenseCategory}', [ExpenseCategoryController::class, 'update'])->name('update');
        Route::get('expenseCategories/{expenseCategory}', [ExpenseCategoryController::class, 'show'])->name('show');
        Route::delete('expenseCategories/{expenseCategory}', [ExpenseCategoryController::class, 'destroy'])->name('destroy');
        Route::get('expenseCategories/select2/type', [ExpenseCategoryController::class, 'select2'])->name('select2');
    });

    // Expense Management
    Route::prefix('expenses')->name('expenses.')->group(function () {

        Route::get('expense', [ExpenseController::class, 'index'])->name('index');
        Route::get('expense/create', [ExpenseController::class, 'createModal'])->name('createModal');
        Route::post('expense/list', [ExpenseController::class, 'listAjax'])->name('list.ajax');
        Route::post('expense', [ExpenseController::class, 'store'])->name('store');
        Route::get('expense/edit/{expense}', [ExpenseController::class, 'editModal'])->whereNumber('expense')->name('editModal');
        Route::put('expense/{expense}', [ExpenseController::class, 'update'])->name('update');
        Route::post('expense/{expense}/toggle-status', [ExpenseController::class, 'toggleStatus'])->whereNumber('expense')->name('toggleStatus');
        Route::get('expense/{expense}', [ExpenseController::class, 'show'])->name('show');
        Route::delete('expense/{expense}', [ExpenseController::class, 'destroy'])->name('destroy');
        Route::get('expense/select2/type', [ExpenseController::class, 'select2'])->name('select2');
        Route::get('expenses/{expense}/invoice',[ExpenseController::class, 'invoice'])->name('invoice');
    });

    // Company Setting Routes
    Route::prefix('website-setting')->name('website-setting.')->group(function () { 
       Route::get('website-settings', [App\Http\Controllers\backend\WebsiteSettingController::class, 'index'])->name('website-settings.index');
        Route::post('website-settings/list', [App\Http\Controllers\backend\WebsiteSettingController::class, 'listAjax'])->name('website-settings.list.ajax');
        Route::get('website-settings/create', [App\Http\Controllers\backend\WebsiteSettingController::class, 'create'])->name('website-settings.create');
        Route::post('website-settings', [App\Http\Controllers\backend\WebsiteSettingController::class, 'store'])->name('website-settings.store');
        Route::get('website-settings/{setting}/edit', [App\Http\Controllers\backend\WebsiteSettingController::class, 'edit'])->name('website-settings.edit');
        Route::put('website-settings/{setting}', [App\Http\Controllers\backend\WebsiteSettingController::class, 'update'])->name('website-settings.update');
        Route::delete('website-settings/{setting}', [App\Http\Controllers\backend\WebsiteSettingController::class, 'destroy'])->name('website-settings.destroy');

    });

    // Fiscal Year Routes
    Route::prefix('fiscal-years')->name('fiscal-years.')->group(function () {
        Route::get('/', [FiscalYearController::class, 'index'])->name('index');
        Route::post('list/ajax', [FiscalYearController::class, 'listAjax'])->name('list.ajax');
        Route::get('create/modal', [FiscalYearController::class, 'createModal'])->name('createModal');
        Route::post('/', [FiscalYearController::class, 'store'])->name('store');
        Route::get('{fiscalYear}/edit/modal', [FiscalYearController::class, 'editModal'])->name('editModal');
        Route::post('{fiscalYear}', [FiscalYearController::class, 'update'])->name('update');
        Route::delete('{fiscalYear}', [FiscalYearController::class, 'destroy'])->name('destroy');
        Route::get('select2', [FiscalYearController::class, 'select2'])->name('select2');
    });

    // Account Type Routes
    Route::prefix('account-types')->name('account-types.')->group(function () {

        Route::get('/', [AccountTypeController::class, 'index'])->name('index');
        Route::post('account-types/list/ajax', [AccountTypeController::class, 'listAjax'])->name('list.ajax');

        Route::get('account-types/create/modal', [AccountTypeController::class, 'createModal'])->name('createModal');
        Route::post('account-types/', [AccountTypeController::class, 'store'])->name('store');
        Route::get('account-types/{type}/edit/modal', [AccountTypeController::class, 'editModal'])->name('editModal');
        Route::post('account-types/{type}', [AccountTypeController::class, 'update'])->name('update');

        Route::delete('account-types/{type}', [AccountTypeController::class, 'destroy'])->name('destroy');
    });

    // Account Routes
    Route::prefix('accounts')->name('accounts.')->group(function () {

        Route::get('/', [AccountController::class, 'index'])->name('index');
        Route::post('accounts/list/ajax', [AccountController::class, 'listAjax'])->name('list.ajax');
        Route::get('accounts/create/modal', [AccountController::class, 'createModal'])->name('createModal');
        Route::post('accounts/', [AccountController::class, 'store'])->name('store');
        Route::get('accounts/{account}/edit/modal', [AccountController::class, 'editModal'])->name('editModal');
        Route::post('accounts/{account}', [AccountController::class, 'update'])->name('update');
        Route::delete('accounts/{account}', [AccountController::class, 'destroy'])->name('destroy');
        Route::get('accounts/{account}/balance', [AccountController::class, 'balance'])->name('balance');

        Route::get('vouchers/{journalEntry}/view/modal', [JournalEntryController::class, 'viewModal'])->name('vouchers.view');

    });

    // Branch Account Routes
    Route::prefix('branch-accounts')->name('branch-accounts.')->group(function () {

        Route::get('/', [BranchAccountController::class, 'index'])->name('index');
        Route::post('branch-accounts/assign', [BranchAccountController::class, 'assign'])->name('assign');
        Route::get('/branch-accounts/{branch}/accounts', [BranchAccountController::class, 'assignedAccounts'])->name('assigned');
    });

    // Opening Balance Routes
    Route::prefix('opening-balances')->name('opening-balances.')->group(function () {

        Route::get('/', [OpeningBalanceController::class, 'index'])->name('index');
        Route::post('/save', [OpeningBalanceController::class, 'save'])->name('save');
        Route::get('/opening-balances/{branch}/{fiscalYear}/accounts', [OpeningBalanceController::class, 'loadAccounts'])->name('accounts');
    });

    // Account Transfer Routes
    Route::prefix('account-transfers')->name('account-transfers.')->group(function () {

        Route::get('account-transfers/', [AccountTransferController::class, 'index'])->name('index');

        Route::post('account-transfers/list/ajax', [AccountTransferController::class, 'listAjax'])->name('list.ajax');

        Route::get('account-transfers/create/modal', [AccountTransferController::class, 'create'])->name('create');

        Route::post('account-transfers/store', [AccountTransferController::class, 'store'])->name('store');

    });

    // Account Ledger Routes
    Route::prefix('accounts-ledger')->name('accounts.ledger.')->group(function () {

        Route::get('/', [AccountLedgerController::class, 'index'])->name('index');
        Route::post('/list/ajax', [AccountLedgerController::class, 'listAjax'])->name('list.ajax');
        Route::get('/summary', [AccountLedgerController::class, 'summary'])->name('summary');

    });

     // Business Journal Routes
    Route::prefix('business-journals')->name('business-journals.')->group(function () {
 
        Route::get('/', [BusinessJournalController::class, 'index'])->name('index');
        Route::get('/records', [BusinessJournalController::class, 'recordsIndex'])->name('records.index');
 
        // Journal CRUD
        Route::post('/list/ajax', [BusinessJournalController::class, 'journalListAjax'])->name('list.ajax');
        Route::get('/create/modal', [BusinessJournalController::class, 'createJournalModal'])->name('create');
        Route::post('/store', [BusinessJournalController::class, 'storeJournal'])->name('store');
        Route::delete('/{businessJournal}/destroy', [BusinessJournalController::class, 'destroyJournal'])->name('destroy');
 
        // Journal Records CRUD
        Route::post('/records/list/ajax', [BusinessJournalController::class, 'recordListAjax'])->name('records.list.ajax');
        Route::get('/records/create/modal', [BusinessJournalController::class, 'createRecordModal'])->name('records.create');
        Route::post('/records/store', [BusinessJournalController::class, 'storeRecord'])->name('records.store');
        Route::get('/records/{businessJournalRecord}/edit/modal', [BusinessJournalController::class, 'editRecordModal'])->name('records.edit');
        Route::post('/records/{businessJournalRecord}/update', [BusinessJournalController::class, 'updateRecord'])->name('records.update');
        Route::delete('/records/{businessJournalRecord}/destroy', [BusinessJournalController::class, 'destroyRecord'])->name('records.destroy');
 
        // Ledger
        Route::get('/ledger', [BusinessJournalController::class, 'ledgerIndex'])->name('ledger.index');
        Route::post('/ledger/list/ajax', [BusinessJournalController::class, 'ledgerListAjax'])->name('ledger.list.ajax');
        Route::get('/ledger/summary', [BusinessJournalController::class, 'ledgerSummary'])->name('ledger.summary');
 
    });

    // Offer Module routes
    Route::prefix('offers')->name('offers.')->group(function () {
        Route::get('/', [OfferController::class, 'index'])->name('index');
        Route::get('/create', [OfferController::class, 'create'])->name('create');
        Route::post('/', [OfferController::class, 'store'])->name('store');
        Route::get('/edit/{offer}', [OfferController::class, 'edit'])->name('edit');
        Route::post('/update/{offer}', [OfferController::class, 'update'])->name('update');
        Route::delete('/delete/{offer}', [OfferController::class, 'destroy'])->name('destroy');
        Route::post('/status', [OfferController::class, 'updateStatus'])->name('updateStatus');
        Route::post('/ajax-datatable', [OfferController::class, 'ajaxIndex'])->name('ajax');
    });

});
