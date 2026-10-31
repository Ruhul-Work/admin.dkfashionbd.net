<?php


function current_branch_id(): ?int {
    return \App\Support\BranchScope::currentId();
}

function current_warehouse_id(): ?int {
    return \App\Support\WarehouseScope::get();
}