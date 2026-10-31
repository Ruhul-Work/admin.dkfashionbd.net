<aside class="sidebar">
    <button type="button" class="sidebar-close-btn">
        <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
    </button>
    <div>
        @php
        // dd($companySettings);
            $logoUrl = optional($companySettings)->logo
                ? image($companySettings->logo)
                : asset('theme/admin/assets/images/logo1.png');
        @endphp
        <a href="{{ route('backend.dashboard') }}" class="sidebar-logo">
            <img src="{{ $logoUrl }}" alt="site logo" class="light-logo img-fluid">
            <img src="{{ $logoUrl }}" alt="site logo" class="dark-logo ">
            <img src="{{ $logoUrl }}" alt="site logo" class="logo-icon">
        </a>

    </div>
    <div class="sidebar-menu-area">
        <ul class="sidebar-menu" id="sidebar-menu">
            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="menu-icon"></iconify-icon>
                    <span>Dashboard</span>
                </a>
                <ul class="sidebar-submenu">

                    <li>
                        <a href="{{ route('backend.dashboard') }}">
                            <i class="ri-circle-fill circle-icon text-warning-main w-auto"></i> POS (Admin)
                        </a>
                    </li>


                </ul>
            </li>
            {{-- user management module start --}}

            @permgroup(['usermanage', 'rbac', 'security'])

                <li class="sidebar-menu-group-title">User Management</li>

                {{-- Dropdown parent --}}
                <li
                    class="dropdown {{ Route::is('usermanage.users.*') || Route::is('rbac.*') || Route::is('security.firewall.*') ? 'active' : '' }}">
                    <a href="javascript:void(0)" class="-toggle d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <iconify-icon icon="flowbite:users-group-outline" class="menu-icon"></iconify-icon>
                            <span>User</span>
                        </div>
                    </a>

                    {{-- Submenu items --}}
                    <ul class="sidebar-submenu">

                        {{-- Users --}}
                        @perm('usermanage.users.index')
                            <li class="{{ Route::is('usermanage.users.index') ? 'active' : '' }}">
                                <a href="{{ route('usermanage.users.index') }}">
                                    <iconify-icon icon="flowbite:users-outline" class="menu-icon"></iconify-icon>
                                    <span>Users</span>
                                </a>
                            </li>
                        @endperm

                        {{-- Roles --}}
                        @perm('rbac.role.list')
                            <li class="{{ Route::is('rbac.role.list') ? 'active' : '' }}">
                                <a href="{{ route('rbac.role.list') }}">
                                    <iconify-icon icon="mdi:shield-account-outline" class="menu-icon"></iconify-icon>
                                    <span>Roles</span>
                                </a>
                            </li>
                        @endperm

                        {{-- Permissions --}}
                        @perm('rbac.permissions.index')
                            <li class="{{ Route::is('rbac.permissions.index') ? 'active' : '' }}">
                                <a href="{{ route('rbac.permissions.index') }}">
                                    <iconify-icon icon="mdi:lock-check-outline" class="menu-icon"></iconify-icon>
                                    <span>Permissions</span>
                                </a>
                            </li>
                        @endperm

                        {{-- Firewall --}}
                        @perm('security.firewall.index')
                            <li class="{{ Route::is('security.firewall.index') ? 'active' : '' }}">
                                <a href="{{ route('security.firewall.index') }}">
                                    <iconify-icon icon="mdi:shield-outline" class="menu-icon"></iconify-icon>
                                    <span>Firewall</span>
                                </a>
                            </li>
                        @endperm

                    </ul>
                </li>
            @endpermgroup


            {{-- branch management module start --}}
            @permgroup(['org.branches.index'])
                <li class="sidebar-menu-group-title">Branch Management</li>
                @permgroup(['org'])
                    @perm('org.branches.index')
                        <li class=" dropdown ">
                            <a href="javascript:void(0)">
                                <i class="ri-store-3-line"></i>
                                <span>Branch</span>
                            </a>

                            <ul class="sidebar-submenu">

                                @perm('org.branches.index')
                                    <li>
                                        <a class="{{ Route::is('org.branches.index') ? 'active' : '' }}"
                                            href="{{ route('org.branches.index') }}">
                                            <iconify-icon icon="mdi:store-outline" class="menu-icon"></iconify-icon>
                                            <span>Branch List</span>
                                        </a>
                                    </li>
                                @endperm
                            </ul>
                        </li>
                    @endperm
                @endpermgroup
            @endpermgroup

            {{-- branch management module end --}}

            {{-- accounting module start --}}
            @permgroup([
                'fiscal-years',
                'accounts',
                'account-types',
                'branch-accounts',
                'opening-balances',
                'account-transfers',
                'business-journals',
                'accounts-ledger',
                'customer-ledger',
                'supplier-ledger'
            ])
                <li class="sidebar-menu-group-title">Accounting</li>

                <li
                    class="dropdown {{ Route::is('fiscal-years.*') ||
                    Route::is('accounts.*') ||
                    Route::is('account-types.*') ||
                    Route::is('branch-accounts.*') ||
                    Route::is('opening-balances.*') ||
                    Route::is('account-transfers.*') ||
                    Route::is('business-journals.*') ||
                    Route::is('accounts.ledger.*') ||
                    Route::is('customerLedger.*') ||
                    Route::is('supplier.*') ||
                    Route::is('business-journals.ledger.*')
                        ? 'active'
                        : '' }}">

                    <a href="javascript:void(0)">
                        <iconify-icon icon="mdi:bank-outline" class="menu-icon"></iconify-icon>
                        <span>Accounts</span>
                    </a>

                    <ul class="sidebar-submenu">

                        {{-- Fiscal Years --}}
                        @perm('fiscal-years.index')
                            <li>
                                <a class="{{ Route::is('fiscal-years.*') ? 'active' : '' }}"
                                    href="{{ route('fiscal-years.index') }}">
                                    <iconify-icon icon="mdi:calendar-clock" class="menu-icon"></iconify-icon>
                                    Fiscal Years
                                </a>
                            </li>
                        @endperm

                        {{-- Account Types --}}
                        @perm('account-types.index')
                            <li>
                                <a class="{{ Route::is('account-types.*') ? 'active' : '' }}"
                                    href="{{ route('account-types.index') }}">
                                    <iconify-icon icon="mdi:shape-outline" class="menu-icon"></iconify-icon>
                                    Account Types
                                </a>
                            </li>
                        @endperm

                        {{-- Account List --}}
                        @perm('accounts.index')
                            <li>
                                <a class="{{ Route::is('accounts.*') ? 'active' : '' }}" href="{{ route('accounts.index') }}">
                                    <iconify-icon icon="mdi:format-list-bulleted" class="menu-icon"></iconify-icon>
                                    Account List
                                </a>
                            </li>
                        @endperm

                        {{-- Branch Accounts --}}
                        @perm('branch-accounts.index')
                            <li>
                                <a class="{{ Route::is('branch-accounts.*') ? 'active' : '' }}"
                                    href="{{ route('branch-accounts.index') }}">
                                    <iconify-icon icon="mdi:source-branch" class="menu-icon"></iconify-icon>
                                    Branch Accounts
                                </a>
                            </li>
                        @endperm

                        {{-- Opening Balances --}}
                        @perm('opening-balances.index')
                            <li>
                                <a class="{{ Route::is('opening-balances.*') ? 'active' : '' }}"
                                    href="{{ route('opening-balances.index') }}">
                                    <iconify-icon icon="mdi:scale-balance" class="menu-icon"></iconify-icon>
                                    Opening Balances
                                </a>
                            </li>
                        @endperm

                        {{-- Account Transfers --}}
                        @perm('account-transfers.index')
                            <li>
                                <a class="{{ Route::is('account-transfers.*') ? 'active' : '' }}"
                                    href="{{ route('account-transfers.index') }}">
                                    <iconify-icon icon="mdi:bank-transfer" class="menu-icon"></iconify-icon>
                                    Account Transfers
                                </a>
                            </li>
                        @endperm

                        {{-- Business Journals --}}
                        @perm('business-journals.index')
                            <li>
                                <a class="{{ Route::is('business-journals.*') ? 'active' : '' }}"
                                    href="{{ route('business-journals.index') }}">
                                    <iconify-icon icon="mdi:notebook-edit-outline" class="menu-icon"></iconify-icon>
                                    Business Journal
                                </a>
                            </li>
                        @endperm



                        {{-- =================Ledger REPORTS ================= --}}
                        @permgroup(['accounts-ledger', 'customer-ledger', 'supplier', 'business-journals'])
                            <li class="dropdown">
                                <a href="javascript:void(0)">
                                    <iconify-icon icon="mdi:chart-box-outline" class="menu-icon"></iconify-icon>
                                    <span>Ledger</span>
                                </a>

                                <ul class="sidebar-submenu">

                                    {{-- Account Ledger --}}
                                    @perm('accounts.ledger.index')
                                        <li>
                                            <a class="{{ Route::is('accounts.ledger.*') ? 'active' : '' }}"
                                                href="{{ route('accounts.ledger.index') }}">
                                                <iconify-icon icon="mdi:book-open-variant" class="menu-icon"></iconify-icon>
                                                Account Ledger
                                            </a>
                                        </li>
                                    @endperm

                                    {{-- Customer Ledger --}}
                                    @perm('customerLedger.index')
                                        <li>
                                            <a class="{{ Route::is('customerLedger.*') ? 'active' : '' }}"
                                                href="{{ route('customerLedger.index') }}">
                                                <iconify-icon icon="mdi:account-cash-outline" class="menu-icon"></iconify-icon>
                                                Customer Ledger
                                            </a>
                                        </li>
                                    @endperm

                                    {{-- supplier ledger  --}}
                                    @perm('supplier.ledger')
                                        <li>
                                            <a class="{{ Route::is('supplier.ledger') ? 'active' : '' }}"
                                                href="{{ route('supplier.ledger') }}">
                                                <iconify-icon icon="mdi:clipboard-text-outline" class="menu-icon"></iconify-icon>
                                                <span>Supplier Ledger</span>
                                            </a>
                                        </li>
                                    @endperm

                                    {{-- Business Journal Ledger --}}
                                    @perm('business-journals.index')
                                        <li>
                                            <a class="{{ Route::is('business-journals.ledger.*') ? 'active' : '' }}"
                                                href="{{ route('business-journals.ledger.index') }}">
                                                <iconify-icon icon="mdi:chart-box-outline" class="menu-icon"></iconify-icon>
                                                <span>Business Journal Ledger</span>
                                            </a>
                                        </li>
                                    @endperm

                                </ul>
                            </li>
                        @endpermgroup
                        {{-- ================= END Ledger REPORTS ================= --}}

                    </ul>
                </li>
            @endpermgroup
            {{-- accounting module end --}}

            {{-- Contacts module start --}}
            @permgroup(['customer', 'supplier'])
                <li class="sidebar-menu-group-title">Contacts</li>
                <li class="dropdown {{ Route::is('supplier.*') || Route::is('customer.*') ? 'active' : '' }}">
                    <a href="javascript:void(0)">
                        <iconify-icon icon="mdi:account-circle-outline" class="menu-icon"></iconify-icon>
                        <span>Contact</span>
                    </a>
                    <ul class="sidebar-submenu">
                        @perm('supplier.index')
                            <li class="{{ Route::is('supplier.index') ? 'active' : '' }}">
                                <a href="{{ route('supplier.index') }}">
                                    <i class="ri-truck-line text-xl me-14 d-flex w-auto"></i>
                                    <span>Suppliers</span>
                                </a>
                            </li>
                        @endperm
                        @perm('customer.index')
                            <li class="{{ Route::is('customer.index') ? 'active' : '' }}">
                                <a href="{{ route('customer.index') }}">
                                    <i class="ri-group-line text-xl me-14 d-flex w-auto"></i>
                                    <span>Customers</span>
                                </a>
                            </li>
                        @endperm
                    </ul>
                </li>
            @endpermgroup
            {{-- contacts module end --}}



            {{-- sale management module start --}}

            @permgroup(['purchase', 'pos', 'paymentTypes', 'expenseCategories', 'expenses', 'pos-reports'])

                <li class="sidebar-menu-group-title">Sale Management</li>

                {{-- Sales parent with children (own permission group) --}}
                @permgroup(['pos', 'pos-reports'])
                    <li class="dropdown {{ Route::is('pos.*') || Route::is('pos.reports.*') ? 'active' : '' }}">
                        <a href="javascript:void(0)">
                            <iconify-icon icon="mdi:point-of-sale" class="menu-icon"></iconify-icon>
                            <span>Sales</span>
                        </a>

                        <ul class="sidebar-submenu">

                            {{-- POS --}}
                            @perm('pos.index')
                                <li>
                                    <a class="{{ Route::is('pos.index') ? 'active' : '' }}" href="{{ route('pos.index') }}">
                                        <iconify-icon icon="mdi:point-of-sale" class="menu-icon"></iconify-icon>
                                        <span>POS</span>
                                    </a>
                                </li>
                            @endperm

                            {{-- POS Sales --}}
                            @perm('pos.sales.list')
                                <li>
                                    <a class="{{ Route::is('pos.sales.list') ? 'active' : '' }}"
                                        href="{{ route('pos.sales.list') }}">
                                        <iconify-icon icon="mdi:cash-register" class="menu-icon"></iconify-icon>
                                        <span>POS Sales</span>
                                    </a>
                                </li>
                            @endperm

                            {{-- Sale Returns --}}
                            @perm('pos.saleReturns.index')
                                <li>
                                    <a class="{{ Route::is('pos.saleReturns.index') ? 'active' : '' }}"
                                        href="{{ route('pos.saleReturns.index') }}">
                                        <iconify-icon icon="mdi:undo-variant" class="menu-icon"></iconify-icon>
                                        <span>Sale Returns</span>
                                    </a>
                                </li>
                            @endperm

                            {{-- Coupon (moved from Item Management) --}}
                            @perm('coupon.index')
                                <li class="{{ Route::is('coupon.index') ? 'active' : '' }}">
                                    <a href="{{ route('coupon.index') }}">
                                        <iconify-icon icon="mdi:ticket-percent-outline" class="menu-icon"></iconify-icon>
                                        <span>Coupon</span>
                                    </a>
                                </li>
                            @endperm
                            {{-- Loyalty Point (moved from Item Management) --}}
                            @perm('loyalty.index')
                                <li class="{{ Route::is('loyalty.index') ? 'active' : '' }}">
                                    <a href="{{ route('loyalty.index') }}">
                                        <iconify-icon icon="mdi:coin" class="menu-icon"></iconify-icon>
                                        <span>Loyalty Point</span>
                                    </a>
                                </li>
                            @endperm

                            {{-- POS Reports (kept as nested dropdown) --}}
                            @perm('pos.reports.daily-payment.index')
                                <li class="dropdown {{ Route::is('pos.reports.*') ? 'active' : '' }}">
                                    <a href="javascript:void(0)">
                                        <iconify-icon icon="mdi:chart-box-outline" class="menu-icon"></iconify-icon>
                                        <span>POS Reports</span>
                                    </a>

                                    <ul class="sidebar-submenu">
                                        @perm('pos.reports.daily-payment.index')
                                            <li>
                                                <a class="{{ Route::is('pos.reports.daily-payment.index') ? 'active' : '' }}"
                                                    href="{{ route('pos.reports.daily-payment.index') }}">
                                                    <iconify-icon icon="mdi:cash-multiple" class="menu-icon"></iconify-icon>
                                                    <span>Daily Payment Summary</span>
                                                </a>
                                            </li>
                                        @endperm
                                    </ul>
                                </li>
                            @endperm

                        </ul>
                    </li>
                @endpermgroup

                {{-- Purchases parent with children (own permission group) --}}
                @permgroup(['purchase'])
                    <li
                        class="dropdown {{ Route::is('purchase.*') || Route::is('purchase.orders.*') || Route::is('purchase.return*') ? 'active' : '' }}">
                        <a href="javascript:void(0)">
                            <iconify-icon icon="tabler:shopping-cart" class="menu-icon"></iconify-icon>
                            <span>Purchases</span>
                        </a>

                        <ul class="sidebar-submenu">
                            @perm('purchase.index')
                                <li>
                                    <a class="{{ Route::is('purchase.index') ? 'active' : '' }}"
                                        href="{{ route('purchase.index') }}">
                                        <iconify-icon icon="tabler:shopping-cart" class="menu-icon"></iconify-icon>
                                        <span>Purchase</span>
                                    </a>
                                </li>
                            @endperm

                            @perm('purchase.orders.create')
                                <li>
                                    <a class="{{ Route::is('purchase.orders.create') ? 'active' : '' }}"
                                        href="{{ route('purchase.orders.create') }}">
                                        <iconify-icon icon="tabler:shopping-cart-plus" class="menu-icon"></iconify-icon>
                                        <span>Add Purchase</span>
                                    </a>
                                </li>
                            @endperm

                            @perm('purchase.return')
                                <li>
                                    <a class="{{ Route::is('purchase.return') ? 'active' : '' }}"
                                        href="{{ route('purchase.return') }}">
                                        <iconify-icon icon="mdi:undo-variant" class="menu-icon"></iconify-icon>
                                        <span>Purchase Return</span>
                                    </a>
                                </li>
                            @endperm

                            @perm('purchase.return.index')
                                <li>
                                    <a class="{{ Route::is('purchase.return.index') ? 'active' : '' }}"
                                        href="{{ route('purchase.return.index') }}">
                                        <iconify-icon icon="mdi:undo-variant" class="menu-icon"></iconify-icon>
                                        <span>Purchase Return Orders</span>
                                    </a>
                                </li>
                            @endperm
                        </ul>
                    </li>
                @endpermgroup

                {{-- Payments & Expenses parent --}}
                @permgroup(['paymentTypes', 'expenseCategories', 'expenses'])
                    <li
                        class="dropdown {{ Route::is('paymentTypes.index') || Route::is('expenseCategories.index') || Route::is('expenses.index') ? 'active' : '' }}">
                        <a href="javascript:void(0)">
                            <iconify-icon icon="mdi:credit-card-outline" class="menu-icon"></iconify-icon>
                            <span>Expenses & Payments</span>
                        </a>

                        <ul class="sidebar-submenu">
                            @perm('paymentTypes.index')
                                <li>
                                    <a class="{{ Route::is('paymentTypes.index') ? 'active' : '' }}"
                                        href="{{ route('paymentTypes.index') }}">
                                        <iconify-icon icon="mdi:credit-card-outline" class="menu-icon"></iconify-icon>
                                        <span>Payment Method</span>
                                    </a>
                                </li>
                            @endperm

                            @perm('expenseCategories.index')
                                <li>
                                    <a class="{{ Route::is('expenseCategories.index') ? 'active' : '' }}"
                                        href="{{ route('expenseCategories.index') }}">
                                        <iconify-icon icon="mdi:finance" class="menu-icon"></iconify-icon>
                                        <span>Expense Categories</span>
                                    </a>
                                </li>
                            @endperm

                            @perm('expenses.index')
                                <li>
                                    <a class="{{ Route::is('expenses.index') ? 'active' : '' }}"
                                        href="{{ route('expenses.index') }}">
                                        <iconify-icon icon="mdi:cash-minus" class="menu-icon"></iconify-icon>
                                        <span>Expenses</span>
                                    </a>
                                </li>
                            @endperm
                        </ul>
                    </li>
                @endpermgroup


            @endpermgroup

            {{-- sale management module end --}}



            {{-- item managemnt module start --}}


            @permgroup([
                'product',
                'category-type',
                'category',
                'subcategory',
                'brand',
                'color',
                'units',
                'product-type',
                'paper_quality',
                'sizes'
            ])
                <li class="sidebar-menu-group-title">Item Management</li>

                <li class="dropdown {{ Route::is('product.products.index') ? 'active' : '' }}">
                    <a href="javascript:void(0)">
                        <iconify-icon icon="mdi:package-variant-closed" class="menu-icon"></iconify-icon>
                        <span>Product</span>
                    </a>

                    <ul class="sidebar-submenu">

                        @perm('product.products.index')
                            <li class="{{ Route::is('product.products.index') ? 'active' : '' }}">
                                <a href="{{ route('product.products.index') }}">
                                    <i class="ri-shopping-bag-3-line text-xl me-14 d-flex w-auto"></i>
                                    <span>All Products</span>
                                </a>
                            </li>
                        @endperm

                        @permgroup(['category-type', 'category', 'subcategory'])
                            <li class="dropdown">
                                <a href="javascript:void(0)">
                                    <iconify-icon icon="mdi:label-outline" class="menu-icon"></iconify-icon>
                                    <span>Categories</span>
                                </a>
                                <ul class="sidebar-submenu">
                                    {{-- @perm('category-type')
                                        <li class="mb-8">
                                            <a class="{{ Route::is('category-type.category-types.index') ? 'active' : '' }}"
                                                href="{{ route('category-type.category-types.index') }}">

                                                <iconify-icon icon="mdi:label-outline" class="menu-icon"></iconify-icon>
                                                <span>Category Type</span>
                                            </a>
                                        </li>
                                    @endperm --}}
                                    @perm('category')
                                        <li class="mb-8">
                                            <a class="{{ Route::is('category.categories.index') ? 'active' : '' }}"
                                                href="{{ route('category.categories.index') }}">

                                                <iconify-icon icon="mdi:folder-outline" class="menu-icon"></iconify-icon>
                                                <span>Category List</span>
                                            </a>
                                        </li>
                                    @endperm
                                    @perm('subcategory')
                                        <li class="mb-8">
                                            <a class="{{ Route::is('subcategory.subcategories.index') ? 'active' : '' }}"
                                                href="{{ route('subcategory.subcategories.index') }}">

                                                <iconify-icon icon="mdi:folder-multiple-outline" class="menu-icon"></iconify-icon>
                                                <span>Subcategory List</span>
                                            </a>
                                        </li>
                                    @endperm
                                </ul>
                            </li>
                        @endpermgroup
                        @perm('color.colors.index')
                            <li class="{{ Route::is('color.colors.index') ? 'active' : '' }}">
                                <a href="{{ route('color.colors.index') }}">
                                    <i class="ri-palette-line"></i>
                                    <span>Color</span>
                                </a>
                            </li>
                        @endperm
                        @perm('product.products.barcode')
                            <li class="{{ Route::is('product.products.barcode') ? 'active' : '' }}">
                                <a href="{{ route('product.products.barcode') }}">
                                    <i class="ri-barcode-line"></i>
                                    <span>Barcode / Label Print</span>
                                </a>
                            </li>
                        @endperm
                        @perm('sizes.index')
                            <li class="{{ Route::is('sizes.index') ? 'active' : '' }}">
                                <a href="{{ route('sizes.index') }}">
                                    <i class="ri-ruler-line text-xl me-14 d-flex w-auto"></i>
                                    <span>Sizes</span>
                                </a>
                            </li>
                        @endperm
                        {{-- @perm('paper_quality.index')
                            <li class="{{ Route::is('paper_quality.index') ? 'active' : '' }}">
                                <a href="{{ route('paper_quality.index') }}">
                                    <i class="ri-book-open-line text-xl me-14 d-flex w-auto"></i>
                                    <span>Paper Quality</span>
                                </a>
                            </li>
                        @endperm --}}
                        @perm('brand.brands.index')
                            <li class="mb-8">
                                <a class="{{ Route::is('brand.brands.index') ? 'active' : '' }}"
                                    href="{{ route('brand.brands.index') }}">
                                    <iconify-icon icon="ri-price-tag-3-line" class="menu-icon"></iconify-icon>
                                    <span>Brand List</span>
                                </a>
                            </li>
                        @endperm
                </li>

                {{-- @perm('units.index')
                    <li class="{{ Route::is('units.index') ? 'active' : '' }}">
                        <a href="{{ route('units.index') }}">
                            <i class="ri-weight-line"></i>
                            <span>Unit</span>
                        </a>
                    </li>
                @endperm --}}

                {{-- @perm('product-type.product-types.index')
                    <li class="{{ Route::is('product-type.product-types.index') ? 'active' : '' }}">
                        <a href="{{ route('product-type.product-types.index') }}">
                            <i class="ri-function-line text-xl me-14 d-flex w-auto"></i>
                            <span>Product Type</span>
                        </a>
                    </li>
                @endperm --}}
            </ul>
            </li>

        @endpermgroup
        {{-- item managemnt module end --}}

        {{-- stock management module (old commented duplicate removed) --}}



        {{-- inventory module start --}}

        @permgroup(['inventory'])
            <li class="sidebar-menu-group-title">Inventory</li>
            <li class=" dropdown ">
                <a href="javascript:void(0)">
                    <i class="ri-stock-line"></i>
                    <span>Stock</span>
                </a>

                <ul class="sidebar-submenu">
                    {{-- warehouse --}}
                    @perm('inventory.warehouses.index')
                        <li class="{{ Route::is('inventory.warehouses.index') ? 'active' : '' }}">
                            <a href="{{ route('inventory.warehouses.index') }}">
                                <i class="ri-building-4-line me-1"></i>
                                <span> Warehouses</span>
                            </a>
                        </li>
                    @endperm
                    {{-- opening stock --}}
                    {{-- @perm('inventory.openingStock.index')
                            <li class="{{ Route::is('inventory.openingStock.index') ? 'active' : '' }}">
                                <a href="{{ route('inventory.openingStock.index') }}">
                                    <i class="ri-upload-2-line"></i>
                                    <span>Opening Stock</span>
                                </a>
                            </li>
                        @endperm --}}
                    {{-- stock adjustment --}}
                    @perm('inventory.adjustments.index')
                        <li class="{{ Route::is('inventory.adjustments.index') ? 'active' : '' }}">
                            <a href="{{ route('inventory.adjustments.index') }}">
                                <i class="ri-equalizer-line"></i><span>Adjustments</span>
                            </a>
                        </li>
                    @endperm
                    {{-- stock transfer --}}
                    @perm('inventory.transfers.index')
                        <li class="{{ Route::is('inventory.transfers.index') ? 'active' : '' }}">
                            <a href="{{ route('inventory.transfers.index') }}">
                                <i class="ri-arrow-left-right-line"></i><span>Transfers</span>
                            </a>
                        </li>
                    @endperm

                </ul>
            </li>

            {{-- === INVENTORY Ledger REPORTS ==== --}}


            <li class="dropdown {{ Route::is('inventory.reports.*') ? 'active' : '' }}">
                <a href="javascript:void(0)">
                    <i class="ri-bar-chart-box-line"></i>
                    <span>Ledger</span>
                </a>

                <ul class="sidebar-submenu">
                    {{-- Stock Ledger --}}
                    @perm('inventory.reports.stock-ledger.index')
                        <li class="{{ Route::is('inventory.reports.stock-ledger.index') ? 'active' : '' }}">
                            <a href="{{ route('inventory.reports.stock-ledger.index') }}">
                                <i class="ri-book-open-line"></i>
                                <span>Stock Ledger</span>
                            </a>
                        </li>
                    @endperm
                </ul>
            </li>

            {{-- === END INVENTORY Ledger REPORTS === --}}

        @endpermgroup
        {{-- inventory module end --}}

         {{--offer management module start --}}
        @permgroup(['offers'])
            <li class="sidebar-menu-group-title">Offer Management</li>
            <li class="dropdown {{ Route::is('offers.*') ? 'active' : '' }}">
                <a href="javascript:void(0)">
                    <iconify-icon icon="mdi:gift" class="menu-icon"></iconify-icon>
                    <span>Offers</span>
                </a>
                <ul class="sidebar-submenu">
                    <li class="{{ Route::is('offers.index') ? 'active' : '' }}">
                        <a href="{{ route('offers.index') }}">
                            <iconify-icon icon="mdi:gift-outline" class="menu-icon"></iconify-icon>
                            Offer List
                        </a>
                    </li>
                    @perm('offers.create')
                        <li class="{{ Route::is('offers.create') ? 'active' : '' }}">
                            <a href="{{ route('offers.create') }}">
                                <iconify-icon icon="mdi:gift-open-outline" class="menu-icon"></iconify-icon>
                                Create Offer
                            </a>
                        </li>
                    @endperm
                </ul>
            </li>
        @endpermgroup
        {{-- offer management module end --}}

        {{-- Reports module start --}}
        @permgroup(['reports'])
            <li class="sidebar-menu-group-title">Reports</li>
            <li class="{{ Route::is('reports.index') ? 'active' : '' }}">
                <a href="{{ route('reports.index') }}">
                    <iconify-icon icon="mdi:file-chart-outline" class="menu-icon"></iconify-icon>
                    <span>Reports</span>
                </a>
            </li>
        @endpermgroup
        {{-- Reports module end --}}

        {{-- Location managemnt module start --}}

        @permgroup(['country', 'division', 'district', 'upazila'])
            <li class="sidebar-menu-group-title">Location Management</li>
            @permgroup(['country', 'division', 'district', 'upazila'])
                <li class=" dropdown ">
                    <a href="javascript:void(0)">
                        <i class="ri-map-pin-line"></i>
                        <span>Countries</span>
                    </a>

                    <ul class="sidebar-submenu">

                        @perm('country.countries.index')
                            <li class="mb-8">
                                <a class="{{ Route::is('country.countries.index') ? 'active' : '' }}"
                                    href="{{ route('country.countries.index') }}">
                                    <iconify-icon icon="flowbite:users-group-outline" class="menu-icon"></iconify-icon>
                                    <span>Country List</span>
                                </a>
                            </li>
                        @endperm
                        @perm('division.divisions.index')
                            <li class="mb-8">
                                <a class="{{ Route::is('division.divisions.index') ? 'active' : '' }}"
                                    href="{{ route('division.divisions.index') }}">
                                    <iconify-icon icon="flowbite:users-group-outline" class="menu-icon"></iconify-icon>
                                    <span>Division List</span>
                                </a>
                            </li>
                        @endperm
                        @perm('district.districts.index')
                            <li class="mb-8">
                                <a class="{{ Route::is('district.districts.index') ? 'active' : '' }}"
                                    href="{{ route('district.districts.index') }}">
                                    <iconify-icon icon="flowbite:users-group-outline" class="menu-icon"></iconify-icon>
                                    <span>District List</span>
                                </a>
                            </li>
                        @endperm
                        @perm('upazila.upazilas.index')
                            <li class="mb-8">
                                <a class="{{ Route::is('upazila.upazilas.index') ? 'active' : '' }}"
                                    href="{{ route('upazila.upazilas.index') }}">
                                    <iconify-icon icon="flowbite:users-group-outline" class="menu-icon"></iconify-icon>
                                    <span>Upazila List</span>
                                </a>
                            </li>
                        @endperm

                    </ul>
                </li>
            @endpermgroup
        @endpermgroup

        {{-- location managemnt module end --}}


        {{-- organization management module start --}}
        @permgroup(['org.btypes', 'website-setting'])
            <li class="sidebar-menu-group-title">Organization</li>
            @permgroup(['org.btypes'])
                <li class="dropdown {{ Route::is('org.btypes.*') ? 'active' : '' }}">
                    <a href="javascript:void(0)">
                        <iconify-icon icon="mdi:shape-outline" class="menu-icon"></iconify-icon>
                        <span>Business Types</span>
                    </a>
                    <ul class="sidebar-submenu">
                        @perm('org.btypes.index')
                            <li>
                                <a class="{{ Route::is('org.btypes.index') ? 'active' : '' }}"
                                    href="{{ route('org.btypes.index') }}">
                                    <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Types List
                                </a>
                            </li>
                        @endperm
                    </ul>
                @endpermgroup
                @perm('website-setting.website-settings.index')
                <li>
                    <a class="{{ Route::is('website-setting.website-settings.index') ? 'active' : '' }}"
                        href="{{ route('website-setting.website-settings.index') }}">
                        <iconify-icon icon="mdi:cog" class="menu-icon"></iconify-icon> <span>Website Setting</span>
                    </a>
                </li>
            @endperm
            </li>

        @endpermgroup
        {{-- organization management module end --}}


        </ul>


    </div>
</aside>
