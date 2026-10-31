<?php

namespace App\Models\Traits;

use App\Support\BranchScope;
use Illuminate\Database\Eloquent\Builder;

trait BranchScoped
{
   protected static function bootBranchScoped(): void
    {
        // Global scope: ব্রাঞ্চ ফিল্টার
        static::addGlobalScope('branch', function (Builder $builder) {
            $user = auth()->user();

            // Super + All হলে স্কোপ লাগবে না
            if ($user && method_exists($user, 'isSuper') && $user->isSuper() && BranchScope::isAll()) {
                return;
            }

            // অন্য সব কেসে currentId থাকলে ফিল্টার
            if ($bid = BranchScope::currentId()) {
                $builder->where($builder->getModel()->getTable().'.branch_id', $bid);
            }
        });

        // creating: সেভের সময় branch_id বসিয়ে দাও (super+All ছাড়া)
        static::creating(function ($model) {
            $user = auth()->user();
            $superAll = $user && method_exists($user, 'isSuper') && $user->isSuper() && BranchScope::isAll();

            if (! $superAll && is_null($model->branch_id)) {
                if ($bid = BranchScope::currentId()) {
                    $model->branch_id = $bid;
                }
            }
        });
    }

    // Helper scopes 
    public function scopeAllBranches(Builder $q): Builder
    {
        return $q->withoutGlobalScope('branch');
    }

    public function scopeOnlyBranch(Builder $q, int $branchId): Builder
    {
        return $q->withoutGlobalScope('branch')->where('branch_id', $branchId);
    }
}
