@extends('backend.layouts.master')

@section('meta')
    <title>Business Journal Records</title>
@endsection

@section('content')

    {{-- Page Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h6 class="fw-semibold mb-0">Business Journal</h6>
            <p class="text-muted m-0">Track investments, drawings, and cash movements in accounts</p>
        </div>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="{{ route('backend.dashboard') }}" class="hover-text-primary d-flex align-items-center gap-1">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Business Journal Records</li>
        </ul>
    </div>

    {{-- Navigation Tabs --}}
    <ul class="nav nav-tabs mb-0 border-bottom" id="bjTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link fw-semibold px-20 py-10 d-flex align-items-center gap-1" href="{{ route('business-journals.index') }}">
                <iconify-icon icon="mdi:book-open-variant"></iconify-icon>
                Journal List
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link active fw-semibold px-20 py-10 d-flex align-items-center gap-1" href="{{ route('business-journals.records.index') }}">
                <iconify-icon icon="mdi:table-edit"></iconify-icon>
                Journal Records
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link fw-semibold px-20 py-10 d-flex align-items-center gap-1" href="{{ route('business-journals.ledger.index') }}">
                <iconify-icon icon="mdi:chart-box-outline"></iconify-icon>
                Ledger
            </a>
        </li>
    </ul>

    <div class="card basic-data-table rounded-top-0">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h5 class="card-title mb-0">Journal Records</h5>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <div class="search-set me-2">
                    <div id="tableSearch" class="search-input"></div>
                </div>
                <ul class="table-top-head list-unstyled d-flex align-items-center gap-2 mb-0">
                    @include('backend.include.buttons')
                </ul>
                <button class="btn btn-success btn-sm px-12 py-8 radius-8 d-flex align-items-center gap-2 AjaxModal"
                    data-ajax-modal="{{ route('business-journals.records.create') }}"
                    data-size="lg"
                    data-onsuccess="BizJournalIndex.onRecordSaved">
                    <iconify-icon icon="mdi:plus" class="text-xl"></iconify-icon>
                    New Journal Record
                </button>
            </div>
        </div>
        <div class="card-body p-3">
            <table class="table bordered-table table-scroll mb-0 AjaxDataTable" id="recordTable" style="width:100%">
                <thead>
                    <tr>
                        <th style="width:60px">SL</th>
                        <th>Journal</th>
                        <th>Payment Type</th>
                        <th>Date</th>
                        <th>Account</th>
                        <th class="text-end">Amount (৳)</th>
                        <th>Narration</th>
                        <th style="width:90px">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

@endsection

@section('script')
<script>
    /* ── DataTable URL ────────────────────────────── */
    var DATATABLE_URL = "{{ route('business-journals.records.list.ajax') }}";

    /* ── Success callbacks ─────────────────────────── */
    window.BizJournalIndex = {
        onRecordSaved: function(res) {
            $('.AjaxDataTable').DataTable().ajax.reload(null, false);
        }
    };

    /* ── Delete Record ────────────────────────────── */
    $(document).on('click', '.btn-delete-record', function() {
        const url = $(this).data('url');
        Swal.fire({
            title: 'Delete Record?',
            text: 'The account balance will be reverted.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#d33',
        }).then(res => {
            if (!res.isConfirmed) return;
            $.ajax({
                url: url, type: 'DELETE',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(r) {
                    $('.AjaxDataTable').DataTable().ajax.reload(null, false);
                    Swal.fire({ icon: 'success', title: r.msg || 'Deleted', timer: 1200, showConfirmButton: false });
                },
                error: function(xhr) {
                    Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.msg || 'Something went wrong' });
                }
            });
        });
    });
</script>
@endsection
