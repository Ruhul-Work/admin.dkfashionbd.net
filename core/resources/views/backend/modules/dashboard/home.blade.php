@extends('backend.layouts.master')
@section('meta')
    <title>Dashboard </title>
@endsection
@section('content')
    <div class="dashboard-main-body">

        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">

            <h6 class="fw-semibold mb-0 d-flex align-items-center gap-2">
                <span>
                    {{ optional($companySettings)->name ?? config('app.name') }}
                </span>

                <small class=" text-success fw-medium">
                    — POS & Inventory
                </small>
                <span
                    class="badge border text-sm fw-semibold text-success-600 bg-success-100 px-20 py-9 radius-4 text-white">{{ ucwords(Auth::user()?->role->name ?? 'Guest user') }}</span>

            </h6>

            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <a href="index.html" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        Dashboard
                    </a>
                </li>
                <li>-</li>
                <li class="fw-medium">POS Software & Inventory</li>
            </ul>
        </div>

        <div class="row gy-4">
            {{-- <div class="col-12">
                <div class="card radius-12">
                    <div class="card-body p-16">
                        <div class="row gy-4">
                            <div class="col-xxl-3 col-xl-4 col-sm-6">
                                <div
                                    class="px-20 py-16 shadow-none radius-8 h-100 gradient-deep-1 left-line line-bg-primary position-relative overflow-hidden">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                                        <div>
                                            <span class="mb-2 fw-medium text-secondary-light text-md">Gross Sales</span>
                                            <h6 class="fw-semibold mb-1">$40,000</h6>
                                        </div>
                                        <span
                                            class="w-44-px h-44-px radius-8 d-inline-flex justify-content-center align-items-center text-2xl mb-12 bg-primary-100 text-primary-600">
                                            <i class="ri-shopping-cart-fill"></i>
                                        </span>
                                    </div>
                                    <p class="text-sm mb-0"><span
                                            class="bg-success-focus px-1 rounded-2 fw-medium text-success-main text-sm"><i
                                                class="ri-arrow-right-up-line"></i> 80%</span> From last month </p>
                                </div>
                            </div>
                            <div class="col-xxl-3 col-xl-4 col-sm-6">
                                <div
                                    class="px-20 py-16 shadow-none radius-8 h-100 gradient-deep-2 left-line line-bg-lilac position-relative overflow-hidden">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                                        <div>
                                            <span class="mb-2 fw-medium text-secondary-light text-md">Total Purchase</span>
                                            <h6 class="fw-semibold mb-1">$35,000</h6>
                                        </div>
                                        <span
                                            class="w-44-px h-44-px radius-8 d-inline-flex justify-content-center align-items-center text-2xl mb-12 bg-lilac-200 text-lilac-600">
                                            <i class="ri-handbag-fill"></i>
                                        </span>
                                    </div>
                                    <p class="text-sm mb-0"><span
                                            class="bg-success-focus px-1 rounded-2 fw-medium text-success-main text-sm"><i
                                                class="ri-arrow-right-up-line"></i> 95%</span> From last month </p>
                                </div>
                            </div>
                            <div class="col-xxl-3 col-xl-4 col-sm-6">
                                <div
                                    class="px-20 py-16 shadow-none radius-8 h-100 gradient-deep-3 left-line line-bg-success position-relative overflow-hidden">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                                        <div>
                                            <span class="mb-2 fw-medium text-secondary-light text-md">Total Income</span>
                                            <h6 class="fw-semibold mb-1">$30,000</h6>
                                        </div>
                                        <span
                                            class="w-44-px h-44-px radius-8 d-inline-flex justify-content-center align-items-center text-2xl mb-12 bg-success-200 text-success-600">
                                            <i class="ri-shopping-cart-fill"></i>
                                        </span>
                                    </div>
                                    <p class="text-sm mb-0"><span
                                            class="bg-danger-focus px-1 rounded-2 fw-medium text-danger-main text-sm"><i
                                                class="ri-arrow-right-down-line"></i> 30%</span> From last month </p>
                                </div>
                            </div>
                            <div class="col-xxl-3 col-xl-4 col-sm-6">
                                <div
                                    class="px-20 py-16 shadow-none radius-8 h-100 gradient-deep-4 left-line line-bg-warning position-relative overflow-hidden">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                                        <div>
                                            <span class="mb-2 fw-medium text-secondary-light text-md">Total Expense</span>
                                            <h6 class="fw-semibold mb-1">$7,000</h6>
                                        </div>
                                        <span
                                            class="w-44-px h-44-px radius-8 d-inline-flex justify-content-center align-items-center text-2xl mb-12 bg-warning-focus text-warning-600">
                                            <i class="ri-shopping-cart-fill"></i>
                                        </span>
                                    </div>
                                    <p class="text-sm mb-0"><span
                                            class="bg-success-focus px-1 rounded-2 fw-medium text-success-main text-sm"><i
                                                class="ri-arrow-right-up-line"></i> 60%</span> From last month </p>
                                </div>
                            </div>
                            <div class="col-xxl-3 col-xl-4 col-sm-6">
                                <div
                                    class="px-20 py-16 shadow-none radius-8 h-100 gradient-deep-4 left-line line-bg-warning position-relative overflow-hidden">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                                        <div>
                                            <span class="mb-2 fw-medium text-secondary-light text-md">Total Expense</span>
                                            <h6 class="fw-semibold mb-1">$7,000</h6>
                                        </div>
                                        <span
                                            class="w-44-px h-44-px radius-8 d-inline-flex justify-content-center align-items-center text-2xl mb-12 bg-warning-focus text-warning-600">
                                            <i class="ri-shopping-cart-fill"></i>
                                        </span>
                                    </div>
                                    <p class="text-sm mb-0"><span
                                            class="bg-success-focus px-1 rounded-2 fw-medium text-success-main text-sm"><i
                                                class="ri-arrow-right-up-line"></i> 60%</span> From last month </p>
                                </div>
                            </div>
                            <div class="col-xxl-3 col-xl-4 col-sm-6">
                                <div
                                    class="px-20 py-16 shadow-none radius-8 h-100 gradient-deep-4 left-line line-bg-warning position-relative overflow-hidden">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                                        <div>
                                            <span class="mb-2 fw-medium text-secondary-light text-md">Total Expense</span>
                                            <h6 class="fw-semibold mb-1">$7,000</h6>
                                        </div>
                                        <span
                                            class="w-44-px h-44-px radius-8 d-inline-flex justify-content-center align-items-center text-2xl mb-12 bg-warning-focus text-warning-600">
                                            <i class="ri-shopping-cart-fill"></i>
                                        </span>
                                    </div>
                                    <p class="text-sm mb-0"><span
                                            class="bg-success-focus px-1 rounded-2 fw-medium text-success-main text-sm"><i
                                                class="ri-arrow-right-up-line"></i> 60%</span> From last month </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}

            <div class="col-12">
                <div class="card radius-12">
                    <div class="card-body p-16">
                        <div class="row gy-4">

                            <!-- 1. Today Sales -->
                            <div class="col-xxl-3 col-xl-4 col-sm-6">
                                <div
                                    class="px-20 py-16 shadow-none radius-8 h-100 gradient-deep-3 left-line line-bg-success position-relative overflow-hidden">
                                    <div class="d-flex align-items-center justify-content-between mb-8">
                                        <div>
                                            <span class="fw-medium text-secondary-light text-md">Today Sales</span>
                                            <h6 class="fw-semibold mb-1">৳ {{ number_format($todaySales, 2) }}</h6>
                                        </div>
                                        <span
                                            class="w-44-px h-44-px radius-8 d-inline-flex justify-content-center align-items-center text-2xl bg-success-200 text-success-600">
                                            <iconify-icon icon="ri:money-dollar-circle-fill"></iconify-icon>
                                        </span>
                                    </div>
                                    <p class="text-sm mb-0">
                                        <span class="bg-success-focus px-1 rounded-2 fw-medium text-success-main text-sm">
                                            <i class="ri-arrow-right-up-line"></i>
                                        </span> From yesterday
                                    </p>
                                </div>
                            </div>


                            <!-- 2. Gross Sales -->
                            <div class="col-xxl-3 col-xl-4 col-sm-6">
                                <div
                                    class="px-20 py-16 shadow-none radius-8 h-100 gradient-deep-1 left-line line-bg-primary position-relative overflow-hidden">
                                    <div class="d-flex align-items-center justify-content-between mb-8">
                                        <div>
                                            <span class="fw-medium text-secondary-light text-md">Monthly Gross Sales</span>
                                            <h6 class="fw-semibold mb-1">৳ {{ number_format($grossSales, 2) }}</h6>
                                        </div>
                                        <span
                                            class="w-44-px h-44-px radius-8 d-inline-flex justify-content-center align-items-center text-2xl bg-primary-100 text-primary-600">
                                            <iconify-icon icon="ri:shopping-cart-2-fill"></iconify-icon>
                                        </span>
                                    </div>
                                    <p class="text-sm mb-0">
                                        <span class="bg-success-focus px-1 rounded-2 fw-medium text-success-main text-sm">
                                            <i class="ri-arrow-right-up-line"></i>
                                        </span> From last month
                                    </p>
                                </div>
                            </div>

                            <!-- 3. Total Purchase -->
                            <div class="col-xxl-3 col-xl-4 col-sm-6">
                                <div
                                    class="px-20 py-16 shadow-none radius-8 h-100 gradient-deep-2 left-line line-bg-lilac position-relative overflow-hidden">
                                    <div class="d-flex align-items-center justify-content-between mb-8">
                                        <div>
                                            <span class="fw-medium text-secondary-light text-md">Total Purchase</span>
                                            <h6 class="fw-semibold mb-1">৳ {{ number_format($totalPurchase, 2) }}</h6>
                                        </div>
                                        <span
                                            class="w-44-px h-44-px radius-8 d-inline-flex justify-content-center align-items-center text-2xl bg-lilac-200 text-lilac-600">
                                            <iconify-icon icon="ri:handbag-fill"></iconify-icon>
                                        </span>
                                    </div>
                                    <p class="text-sm mb-0">
                                        <span class="bg-danger-focus px-1 rounded-2 fw-medium text-danger-main text-sm">
                                            <i class="ri-arrow-right-down-line"></i>
                                        </span> From last month
                                    </p>
                                </div>
                            </div>

                            <!-- 4. Total Orders -->
                            <div class="col-xxl-3 col-xl-4 col-sm-6">
                                <div
                                    class="px-20 py-16 shadow-none radius-8 h-100 gradient-deep-1 left-line line-bg-primary position-relative overflow-hidden">
                                    <div class="d-flex align-items-center justify-content-between mb-8">
                                        <div>
                                            <span class="fw-medium text-secondary-light text-md">Total Orders</span>
                                            <h6 class="fw-semibold mb-1">{{ number_format($totalOrders) }}</h6>
                                        </div>
                                        <span
                                            class="w-44-px h-44-px radius-8 d-inline-flex justify-content-center align-items-center text-2xl bg-primary-100 text-primary-600">
                                            <iconify-icon icon="ri:file-list-3-fill"></iconify-icon>
                                        </span>
                                    </div>
                                    <p class="text-sm mb-0">
                                        <span class="bg-success-focus px-1 rounded-2 fw-medium text-success-main text-sm">
                                            <i class="ri-arrow-right-up-line"></i>
                                        </span> From last month
                                    </p>
                                </div>
                            </div>


                            <!-- 5. Total Expense -->
                            <div class="col-xxl-3 col-xl-4 col-sm-6">
                                <div
                                    class="px-20 py-16 shadow-none radius-8 h-100 gradient-deep-4 left-line line-bg-warning position-relative overflow-hidden">
                                    <div class="d-flex align-items-center justify-content-between mb-8">
                                        <div>
                                            <span class="fw-medium text-secondary-light text-md">Total Expense</span>
                                            <h6 class="fw-semibold mb-1">৳ {{ number_format($totalExpense, 2) }}</h6>
                                        </div>
                                        <span
                                            class="w-44-px h-44-px radius-8 d-inline-flex justify-content-center align-items-center text-2xl bg-warning-focus text-warning-600">
                                            <iconify-icon icon="ri:money-dollar-circle-fill"></iconify-icon>
                                        </span>
                                    </div>
                                    <p class="text-sm mb-0">
                                        <span class="bg-success-focus px-1 rounded-2 fw-medium text-success-main text-sm">
                                            <i class="ri-arrow-right-up-line"></i>
                                        </span> From last month
                                    </p>
                                </div>
                            </div>


                            <!-- 6. Net Income -->
                            <div class="col-xxl-3 col-xl-4 col-sm-6">
                                <div
                                    class="px-20 py-16 shadow-none radius-8 h-100 gradient-deep-3 left-line line-bg-success position-relative overflow-hidden">
                                    <div class="d-flex align-items-center justify-content-between mb-8">
                                        <div>
                                            <span class="fw-medium text-secondary-light text-md">Net Income</span>
                                            <h6 class="fw-semibold mb-1">৳ {{ number_format($netIncome, 2) }}</h6>
                                        </div>
                                        <span
                                            class="w-44-px h-44-px radius-8 d-inline-flex justify-content-center align-items-center text-2xl bg-success-200 text-success-600">
                                            <iconify-icon icon="ri:line-chart-fill"></iconify-icon>
                                        </span>
                                    </div>
                                    <p class="text-sm mb-0">
                                        <span class="bg-danger-focus px-1 rounded-2 fw-medium text-danger-main text-sm">
                                            <i class="ri-arrow-right-down-line"></i>
                                        </span> From last month
                                    </p>
                                </div>
                            </div>



                        </div>
                    </div>
                </div>
            </div>

            {{--  Income vs Expense Chart --}}
            <div class="col-xxl-8">
                <div class="card h-100">
                    <div class="card-body p-24 mb-8">
                        <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                            <h6 class="mb-2 fw-bold text-lg mb-0">Sale Vs Purchase & Expense </h6>
                            <select id="incomeExpenseFilter"
                                class="form-select form-select-sm w-auto bg-base border text-secondary-light radius-8">
                                <option value="monthly">Monthly</option>
                                <option value="weekly">Weekly</option>
                                <option value="daily">Daily</option>
                                <option value="yearly">Yearly</option>
                            </select>
                        </div>
                        <ul class="d-flex flex-wrap align-items-center justify-content-center my-3 gap-24">
                            <li class="d-flex flex-column gap-1">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="w-8-px h-8-px rounded-pill bg-primary-600"></span>
                                    <span class="text-secondary-light text-sm fw-semibold">Sale </span>
                                </div>
                                <div class="d-flex align-items-center gap-8">
                                    <h6 class="mb-0" id="chartIncomeValue">৳ {{ number_format($grossSales, 2) }}</h6>
                                    <span class="text-success-600 d-flex align-items-center gap-1 text-sm fw-bolder">
                                        10%
                                        <i class="ri-arrow-up-s-fill d-flex"></i>
                                    </span>
                                </div>
                            </li>
                            <li class="d-flex flex-column gap-1">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="w-8-px h-8-px rounded-pill bg-warning-600"></span>
                                    <span class="text-secondary-light text-sm fw-semibold">Purchase & Expenses </span>
                                </div>
                                <div class="d-flex align-items-center gap-8">
                                    <h6 class="mb-0" id="chartExpenseValue">৳ {{ number_format($totalPurchaseAndExpense, 2) }}</h6>
                                    <span class="text-danger-600 d-flex align-items-center gap-1 text-sm fw-bolder">
                                        10%
                                        <i class="ri-arrow-down-s-fill d-flex"></i>
                                    </span>
                                </div>
                            </li>
                        </ul>
                        <div id="incomeExpense" class="apexcharts-tooltip-style-1"></div>
                    </div>
                </div>
            </div>

            {{-- USERS SHOW --}}
            <div class="col-xxl-4 col-md-6">
                <div class="card">
                    <div class="card-header border-bottom">
                        <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                            <h6 class="mb-2 fw-bold text-lg mb-0">Users</h6>
                            <a href="{{ route('usermanage.users.index') }}"
                                class="text-primary-600 hover-text-primary d-flex align-items-center gap-1">
                                View All
                                <iconify-icon icon="solar:alt-arrow-right-linear" class="icon"></iconify-icon>
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-20">
                        <div class="d-flex flex-column gap-24">
                            @forelse($users as $user)
                                <div class="d-flex align-items-center justify-content-between gap-3">
                                    <div class="d-flex align-items-center">
                                        <div class="w-40-px h-40-px rounded-circle flex-shrink-0 me-12 overflow-hidden d-flex align-items-center justify-content-center bg-primary-100"
                                            style="font-weight: bold; color: #3c50e0; font-size: 16px;">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="text-md mb-0">{{ $user->name }}</h6>
                                            <span
                                                class="text-sm text-secondary-light fw-normal">{{ $user->role?->name ?? 'User' }}</span>
                                        </div>
                                    </div>
                                    @if ($user->status == 1 || $user->status === 'active' || $user->status === true)
                                        <span class="text-success-main fw-medium text-md">Active</span>
                                    @elseif($user->status == 0 || $user->status === 'inactive' || $user->status === false)
                                        <span class="text-danger-main fw-medium text-md">Inactive</span>
                                    @else
                                        <span
                                            class="text-warning-main fw-medium text-md">{{ ucfirst($user->status) }}</span>
                                    @endif
                                </div>
                            @empty
                                <div class="text-center text-secondary-light py-4">
                                    <p>No users found</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- top supplier  show --}}
            <div class="col-xxl-4 col-md-6">
                <div class="card h-100">
                    <div class="card-header">
                        <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                            <h6 class="mb-2 fw-bold text-lg mb-0">Top Suppliers</h6>
                            <a href="{{ route('supplier.index') }}"
                                class="text-primary-600 hover-text-primary d-flex align-items-center gap-1">
                                View All
                                <iconify-icon icon="solar:alt-arrow-right-linear" class="icon"></iconify-icon>
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-24">
                        <div class="table-responsive scroll-sm">
                            <table class="table bordered-table mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col">SL</th>
                                        <th scope="col">Name </th>
                                        <th scope="col">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($supplierQuery as $item)
                                        <tr>
                                            <td>
                                                <span class="text-secondary-light">{{ $loop->iteration }}</span>
                                            </td>
                                            <td>
                                                <span class="text-secondary-light">{{ $item->name }}</span>
                                            </td>
                                            <td>
                                                <span class="text-secondary-light">৳
                                                    {{ number_format($item->total_purchase, 2) }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>


            {{-- top customer show --}}

            <div class="col-xxl-4 col-md-6">
                <div class="card h-100">
                    <div class="card-header">
                        <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                            <h6 class="mb-2 fw-bold text-lg mb-0">Top Customer</h6>
                            <a href="{{ route('customer.index') }}"
                                class="text-primary-600 hover-text-primary d-flex align-items-center gap-1">
                                View All
                                <iconify-icon icon="solar:alt-arrow-right-linear" class="icon"></iconify-icon>
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-24">
                        <div class="table-responsive scroll-sm">
                            <table class="table bordered-table mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col">SL</th>
                                        <th scope="col">Name </th>
                                        <th scope="col">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($customerQuery as $item)
                                        <tr>
                                            <td>
                                                <span class="text-secondary-light">{{ $loop->iteration }}</span>
                                            </td>
                                            <td>
                                                <span class="text-secondary-light">{{ $item->name }}</span>
                                            </td>
                                            <td>
                                                <span class="text-secondary-light">৳
                                                    {{ number_format($item->total_sale, 2) }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- overall report show --}}
            <div class="col-xxl-4 col-md-6">
                <div class="card h-100">
                    <div class="card-header">
                        <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                            <h6 class="mb-2 fw-bold text-lg">Overall Report</h6>
                            <select id="overallReportFilter"
                                class="form-select form-select-sm w-auto bg-base border text-secondary-light radius-8">
                                <option value="yearly">Yearly</option>
                                <option value="monthly" selected>Monthly</option>
                                <option value="weekly">Weekly</option>
                                <option value="today">Today</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body p-24">
                        <div class="mt-32">
                            <div id="userOverviewDonutChart" class="mx-auto apexcharts-tooltip-z-none"></div>
                        </div>
                        <div class="d-flex flex-wrap gap-20 justify-content-center mt-48">
                            <div class="d-flex align-items-center gap-8">
                                <span class="w-16-px h-16-px radius-2 bg-primary-600"></span>
                                <span class="text-secondary-light">Purchase</span>
                            </div>
                            <div class="d-flex align-items-center gap-8">
                                <span class="w-16-px h-16-px radius-2 bg-lilac-600"></span>
                                <span class="text-secondary-light">Sales</span>
                            </div>
                            <div class="d-flex align-items-center gap-8">
                                <span class="w-16-px h-16-px radius-2 bg-warning-600"></span>
                                <span class="text-secondary-light">Expense</span>
                            </div>
                            <div class="d-flex align-items-center gap-8">
                                <span class="w-16-px h-16-px radius-2 bg-success-600"></span>
                                <span class="text-secondary-light">Gross Profit</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            {{-- purchase and sales summary chart --}}
            <div class="col-xxl-4 col-md-6">
                <div class="card h-100">
                    <div class="card-header">
                        <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                            <h6 class="mb-2 fw-bold text-lg mb-0">Purchase & Sales</h6>
                            <select id="purchaseSaleFilter"
                                class="form-select form-select-sm w-auto bg-base text-secondary-light">
                                <option value="this_month">This Month</option>
                                <option value="this_week" selected>This Week</option>
                                <option value="this_year">This Year</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body p-24">
                        <ul class="d-flex flex-wrap align-items-center justify-content-center my-3 gap-3">
                            <li class="d-flex align-items-center gap-2">
                                <span class="w-12-px h-8-px rounded-pill bg-warning-600"></span>
                                <span class="text-secondary-light text-sm fw-semibold">Purchase: ৳<span
                                        id="purchaseSummaryVal"
                                        class="text-primary-light fw-bold">{{ number_format($purchaseSalesMetrics['this_week']['purchase'], 2) }}</span>
                                </span>
                            </li>
                            <li class="d-flex align-items-center gap-2">
                                <span class="w-12-px h-8-px rounded-pill bg-success-600"></span>
                                <span class="text-secondary-light text-sm fw-semibold">Sales: ৳<span id="salesSummaryVal"
                                        class="text-primary-light fw-bold">{{ number_format($purchaseSalesMetrics['this_week']['sales'], 2) }}</span>
                                </span>
                            </li>
                        </ul>
                        <div id="purchaseSaleChart" class="margin-16-minus y-value-left"></div>
                    </div>
                </div>
            </div>

            {{-- recent transactions show --}}
            <div class="col-xxl-8">
                <div class="card h-100">
                    <div class="card-header">
                        <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                            <h6 class="mb-2 fw-bold text-lg mb-0">Recent Transactions</h6>
                            <a href="javascript:void(0)"
                                class="text-primary-600 hover-text-primary d-flex align-items-center gap-1">
                                View All
                                <iconify-icon icon="solar:alt-arrow-right-linear" class="icon"></iconify-icon>
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-24">
                        <div class="table-responsive scroll-sm">
                            <table class="table bordered-table mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col">SL</th>
                                        <th scope="col">Date </th>
                                        <th scope="col">Type</th>
                                        <th scope="col">Payment Mode</th>
                                        <th scope="col">Paid Amount</th>
                                        <th scope="col">Due Amount</th>
                                        <th scope="col">Total Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentTransactions as $transaction)
                                    <tr>
                                        <td>
                                            <span class="text-secondary-light">{{ $loop->iteration }}</span>
                                        </td>
                                        <td>
                                            <span class="text-secondary-light">{{ \Carbon\Carbon::parse($transaction->date)->format('d M Y') }}</span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $transaction->source == 'Sale' ? 'bg-success-focus text-success-main' : 'bg-info-focus text-info-main' }}">
                                                {{ $transaction->source }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-secondary-light">{{ ucfirst($transaction->type) }}</span>
                                        </td>
                                        <td>
                                            <span class="text-secondary-light">৳ {{ number_format($transaction->paid, 2) }}</span>
                                        </td>
                                        <td>
                                            <span class="text-secondary-light">৳ {{ number_format($transaction->due, 2) }}</span>
                                        </td>
                                        <td>
                                            <span class="text-secondary-light">৳ {{ number_format($transaction->total, 2) }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @if($recentTransactions->isEmpty())
                                    <tr>
                                        <td colspan="7" class="text-center text-secondary-light">No transactions found</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('script')
    <script>
        // ===================== Income VS Expense Start =============================== 
        function createChartTwo(chartId, color1, color2, incomeData, expenseData, monthLabels) {
            // Destroy existing chart if it exists
            if (window.incomeExpenseChart) {
                window.incomeExpenseChart.destroy();
            }

            var options = {
                series: [{
                    name: 'Sale',
                    data: incomeData
                }, {
                    name: 'Purchase & Expenses',
                    data: expenseData
                }],
                legend: {
                    show: false
                },
                chart: {
                    type: 'area',
                    width: '100%',
                    height: 270,
                    toolbar: {
                        show: false
                    },
                    padding: {
                        left: 0,
                        right: 0,
                        top: 0,
                        bottom: 0
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 3,
                    colors: [color1, color2],
                    lineCap: 'round'
                },
                grid: {
                    show: true,
                    borderColor: '#D1D5DB',
                    strokeDashArray: 1,
                    position: 'back',
                    xaxis: {
                        lines: {
                            show: false
                        }
                    },
                    yaxis: {
                        lines: {
                            show: true
                        }
                    },
                    row: {
                        colors: undefined,
                        opacity: 0.5
                    },
                    column: {
                        colors: undefined,
                        opacity: 0.5
                    },
                    padding: {
                        top: -20,
                        right: 0,
                        bottom: -10,
                        left: 0
                    },
                },
                colors: [color1, color2],
                fill: {
                    type: 'gradient',
                    colors: [color1, color2],
                    gradient: {
                        shade: 'light',
                        type: 'vertical',
                        shadeIntensity: 0.5,
                        gradientToColors: [undefined, `${color2}00`],
                        inverseColors: false,
                        opacityFrom: [0.4, 0.6],
                        opacityTo: [0.3, 0.3],
                        stops: [0, 100],
                    },
                },
                markers: {
                    colors: [color1, color2],
                    strokeWidth: 3,
                    size: 0,
                    hover: {
                        size: 10
                    }
                },
                xaxis: {
                    labels: {
                        show: false
                    },
                    categories: monthLabels,
                    tooltip: {
                        enabled: false
                    },
                    labels: {
                        formatter: function(value) {
                            return value;
                        },
                        style: {
                            fontSize: "14px"
                        }
                    }
                },
                yaxis: {
                    labels: {
                        formatter: function(value) {
                            return "৳" + (value >= 1000 ? (value / 1000).toFixed(0) + "k" : value.toFixed(0));
                        },
                        style: {
                            fontSize: "14px"
                        }
                    },
                },
                tooltip: {
                    x: {
                        format: 'MMM'
                    },
                    y: {
                        formatter: function(value) {
                            return "৳" + value.toFixed(2);
                        }
                    }
                }
            };

            window.incomeExpenseChart = new ApexCharts(document.querySelector(`#${chartId}`), options);
            window.incomeExpenseChart.render();
        }

        // Data from PHP
        var chartData = {
            monthly: {
                income: @json($monthlyIncome),
                expense: @json($monthlyExpense),
                labels: @json($monthNames),
                totalIncome: {{ $grossSales }},
                totalExpense: {{ $totalPurchaseAndExpense }}
            },
            weekly: {
                income: @json($weeklyIncome),
                expense: @json($weeklyExpense),
                labels: @json($weeklyLabels),
                totalIncome: @json(array_sum($weeklyIncome)),
                totalExpense: @json(array_sum($weeklyExpense))
            },
            daily: {
                income: @json($dailyIncome),
                expense: @json($dailyExpense),
                labels: @json($dailyLabels),
                totalIncome: @json(array_sum($dailyIncome)),
                totalExpense: @json(array_sum($dailyExpense))
            },
            yearly: {
                income: @json($yearlyIncome),
                expense: @json($yearlyExpense),
                labels: @json($yearlyLabels),
                totalIncome: @json(array_sum($yearlyIncome)),
                totalExpense: @json(array_sum($yearlyExpense))
            }
        };

        // Function to format currency
        function formatCurrency(value) {
            return '৳ ' + value.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        // Initialize with monthly
        createChartTwo('incomeExpense', '#487FFF', '#FF9F29',
            chartData.monthly.income, chartData.monthly.expense, chartData.monthly.labels);
        document.getElementById('chartIncomeValue').textContent = formatCurrency(chartData.monthly.totalIncome);
        document.getElementById('chartExpenseValue').textContent = formatCurrency(chartData.monthly.totalExpense);

        // Dropdown change event
        document.getElementById('incomeExpenseFilter').addEventListener('change', function(e) {
            var period = e.target.value;
            var data = chartData[period];
            createChartTwo('incomeExpense', '#487FFF', '#FF9F29',
                data.income, data.expense, data.labels);

            // Update heading values
            document.getElementById('chartIncomeValue').textContent = formatCurrency(data.totalIncome);
            document.getElementById('chartExpenseValue').textContent = formatCurrency(data.totalExpense);
        });
        // ===================== Income VS Expense End =============================== 

        // ================================ Users Overview Donut chart Start ================================ 
        var overallData = @json($overallReportData);

        var donutOptions = {
            series: overallData.monthly,
            colors: ['#487FFF', '#9935FE', '#FF9F29', '#45B369'],
            labels: ['Purchase', 'Sales', 'Expense', 'Gross Profit'],
            legend: {
                show: false
            },
            chart: {
                type: 'donut',
                height: 270,
                sparkline: {
                    enabled: true // Remove whitespace
                },
                margin: {
                    top: 0,
                    right: 0,
                    bottom: 0,
                    left: 0
                },
                padding: {
                    top: 0,
                    right: 0,
                    bottom: 0,
                    left: 0
                }
            },
            stroke: {
                width: 0,
            },
            dataLabels: {
                enabled: true,
                formatter: function(val, opts) {
                    return opts.w.globals.series[opts.seriesIndex].toLocaleString('en-US', {
                        minimumFractionDigits: 0
                    });
                }
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return "৳" + val.toLocaleString('en-US', {
                            minimumFractionDigits: 2
                        });
                    }
                }
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }],
        };

        var donutChart = new ApexCharts(document.querySelector("#userOverviewDonutChart"), donutOptions);
        donutChart.render();

        document.getElementById('overallReportFilter').addEventListener('change', function(e) {
            var period = e.target.value;
            donutChart.updateSeries(overallData[period]);
        });
        // ================================ Users Overview Donut chart End ================================ 

                // ================================ Purchase & sale chart Start ================================ 
                var purchaseSalesMetrics = @json($purchaseSalesMetrics);
                var purchaseSaleChartFullData = @json($purchaseSaleChartData);
        
                var purchaseSaleOptions = {
                    series: [{
                        name: 'Purchase',
                        data: purchaseSaleChartFullData.this_week.purchase
                    }, {
                        name: 'Sales',
                        data: purchaseSaleChartFullData.this_week.sales
                    }],
                    colors: ['#FF9F29', '#45B369'],
                    legend: {
                        show: false
                    },
                    chart: {
                        type: 'bar',
                        height: 260,
                        toolbar: {
                            show: false
                        },
                    },
                    grid: {
                        show: true,
                        borderColor: '#D1D5DB',
                        strokeDashArray: 4,
                        position: 'back',
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: 4,
                            columnWidth: 8,
                        },
                    },
                    dataLabels: {
                        enabled: false
                    },
                    states: {
                        hover: {
                            filter: {
                                type: 'none'
                            }
                        }
                    },
                    stroke: {
                        show: true,
                        width: 0,
                        colors: ['transparent']
                    },
                    xaxis: {
                        categories: purchaseSaleChartFullData.this_week.labels,
                    },
                    fill: {
                        opacity: 1,
                        width: 18,
                    },
                    tooltip: {
                        y: {
                            formatter: function(val) {
                                return "৳" + val.toLocaleString('en-US', {minimumFractionDigits: 2});
                            }
                        }
                    }
                };
        
                var purchaseSaleChart = new ApexCharts(document.querySelector("#purchaseSaleChart"), purchaseSaleOptions);
                purchaseSaleChart.render();
        
                document.getElementById('purchaseSaleFilter').addEventListener('change', function(e) {
                    var period = e.target.value;
                    var metrics = purchaseSalesMetrics[period];
                    var chartData = purchaseSaleChartFullData[period];
                    
                    // Update summary text values
                    document.getElementById('purchaseSummaryVal').textContent = metrics.purchase.toLocaleString('en-US', {minimumFractionDigits: 2});
                    document.getElementById('salesSummaryVal').textContent = metrics.sales.toLocaleString('en-US', {minimumFractionDigits: 2});
        
                    // Update chart series and categories
                    purchaseSaleChart.updateOptions({
                        series: [{
                            name: 'Purchase',
                            data: chartData.purchase
                        }, {
                            name: 'Sales',
                            data: chartData.sales
                        }],
                        xaxis: {
                            categories: chartData.labels
                        }
                    });
                });
                // ================================ Purchase & sale chart End ================================ 
         
    </script>


    <style>
        @import url('https://fonts.googleapis.com/css?family=Poppins');

        * {
            font-family: 'Poppins', sans-serif;
        }

        #chart {

            margin: 35px auto;
            opacity: 0.9;
        }

        #timeline-chart .apexcharts-toolbar {
            opacity: 1;
            border: 0;
        }
    </style>
@endsection
