<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Accounting Configuration
    |--------------------------------------------------------------------------
    */

    // Sales Revenue Account (Credit)
    'sales_revenue_account_id' => env('SALES_REVENUE_ACCOUNT_ID'),
    
    'payment_accounts' => [
        'cash'  => 201,
        'bkash' => 202,
        'card'  => 203,
    ],
    

];
