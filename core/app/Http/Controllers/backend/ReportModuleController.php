<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportModuleController extends Controller
{
    public function index()
    {
        $reports = [
            ['title' => 'Sales Report', 'route' => 'reports.sales', 'icon' => 'mdi:chart-line', 'perm' => 'reports.sales'],
            ['title' => 'Source Wise Sale Report', 'route' => 'reports.source-base-sales', 'icon' => 'mdi:chart-bar', 'perm' => 'reports.source-base-sales'],
            ['title' => 'Product Wise Sale Report', 'route' => 'reports.product-wise-sales', 'icon' => 'mdi:chart-bar', 'perm' => 'reports.product-wise-sales'],
            ['title' => 'Purchase Report', 'route' => 'reports.purchase', 'icon' => 'mdi:cart-outline', 'perm' => 'reports.purchase'],
            ['title' => 'Expense Report', 'route' => 'reports.expenses', 'icon' => 'mdi:cash-minus', 'perm' => 'reports.expenses'],
            ['title' => 'Inventory / Stock Ledger', 'route' => 'reports.inventory', 'icon' => 'mdi:book-open-variant', 'perm' => 'reports.inventory'],
        ];

        return view('backend.modules.reports.index', compact('reports'));
    }
}
