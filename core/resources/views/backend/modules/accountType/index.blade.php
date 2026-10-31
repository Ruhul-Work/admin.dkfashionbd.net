@extends('backend.layouts.master')

@section('meta')
<title>Account Types</title>
@endsection

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-0">Account Types</h6>
        <p class="text-muted m-0">Accounting account categories</p>
    </div>

    <ul class="d-flex align-items-center gap-2">
        <li class="fw-medium">
            <a href="{{ route('backend.dashboard') }}" class="hover-text-primary d-flex align-items-center gap-1">
                <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                Dashboard
            </a>
        </li>
        <li>-</li>
        <li class="fw-medium">Account Types</li>
    </ul>
</div>

<div class="card basic-data-table">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0">Types</h5>

        <div class="actions-bar d-flex align-items-center gap-2 flex-wrap">
            <div class="search-set me-2">
                <div id="tableSearch" class="search-input"></div>
            </div>

            <ul class="table-top-head list-unstyled d-flex align-items-center gap-2 mb-0">
                @include('backend.include.buttons')
            </ul>

            @perm('account_types.store')
            <button
                class="btn btn-primary btn-sm px-12 py-8 radius-8 d-flex align-items-center gap-2 AjaxModal"
                data-ajax-modal="{{ route('account-types.createModal') }}"
                data-size="sm"
                data-onsuccess="AccountTypesIndex.onSaved">
                <iconify-icon icon="ic:baseline-plus" class="text-xl"></iconify-icon>
                Add Type
            </button>
            @endperm
        </div>
    </div>

    <div class="card-body">
        <table class="table bordered-table table-scroll mb-0 AjaxDataTable" id="accountTypesTable" style="width: 100%">
            <thead>
                <tr>
                    <th >S.L</th>
                    <th>Name</th>
                    <th>Code</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
@endsection

@section('script')
<script>
var DATATABLE_URL = "{{ route('account-types.list.ajax') }}";

window.AccountTypesIndex = {
    onSaved: function(){
        $('.AjaxDataTable').DataTable().ajax.reload(null, false);
    }
};

$(document).on('click', '.btn-account-type-del', function () {
    let url = $(this).data('url');
    Swal.fire({icon:'warning', title:'Delete this type?', showCancelButton:true})
    .then(res=>{
        if(res.isConfirmed){
            $.ajax({
                type:'DELETE',
                url,
                headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}
            })
            .done(()=> $('.AjaxDataTable').DataTable().ajax.reload(null,false))
            .fail(err=>{
                Swal.fire({icon:'error', title: err.responseJSON?.message || 'Failed'});
            });
        }
    });
});
</script>
@endsection
