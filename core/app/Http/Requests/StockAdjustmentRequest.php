<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockAdjustmentRequest extends FormRequest
{
    public function authorize()
    {
        return true; // add auth logic if needed
    }

    public function rules()
    {
        return [
            'warehouse_id' => ['required','integer'],
            'branch_id' => ['nullable','integer'],
            'when' => ['nullable','date'],
            'reference_no' => ['nullable','string','max:50'],
            'global_reason' => ['nullable','string','max:500'],
            'rows' => ['required','array','min:1'],
            'rows.*.product_id' => ['required','integer'],
            'rows.*.qty' => ['required','numeric'], // signed qty allowed
            'rows.*.unit_cost' => ['nullable','numeric','min:0'],
            'rows.*.reason' => ['nullable','string','max:500'],
            'post_now' => ['nullable','in:0,1'],
            // optional: rows.*.warehouse_id, rows.*.branch_id if you allow per-item override
        ];
    }

    public function messages()
    {
        return [
            'rows.required' => 'You must add at least one variant line.',
            'rows.*.qty.required' => 'Quantity is required for each line.',
        ];
    }
}
