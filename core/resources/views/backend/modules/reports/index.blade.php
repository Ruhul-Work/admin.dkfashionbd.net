@extends('backend.layouts.master')

@section('title','Reports')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h6 class="fw-semibold mb-0">Reports</h6>
      <p class="text-muted m-0">Manage reports</p>
    </div>
    <ul class="d-flex align-items-center gap-2">
      <li class="fw-medium">
        <a href="{{ route('backend.dashboard') }}" class="d-flex align-items-center gap-1 hover-text-primary">
          <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon> Dashboard
        </a>
      </li>
      <li>-</li>
      <li class="fw-medium">Reports</li>
    </ul>
  </div>

    <style>
        .report-card {
            border: 1px solid rgba(16,24,40,0.04);
            background: linear-gradient(135deg, #f5f3e0 0%, #bdf2ff 100%);
            border-radius: 8px;
            box-shadow: 0 6px 18px rgba(16,24,40,0.04);
            transition: transform .15s ease, box-shadow .15s ease;
            color: inherit;
            text-decoration: none;
        }
         
        .report-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 18px 40px rgba(27, 41, 70, 0.08);
            text-decoration: none;
        }
        .report-card .card-body { padding: 1rem; }
        .report-card .card-title { margin-top:.3rem; font-weight:600; color: #4d3b00a2; }
        .report-card .card-text { margin-bottom:0; color:#747e91; }
    </style>

    <div class="row">
        @foreach($reports as $report)
            @perm($report['perm'])
                <div class="col-md-3 col-sm-6 mb-4">
                    <a href="{{ route($report['route']) }}" class="card report-card text-decoration-none h-100">
                        <div class="card-body shadow-md d-flex flex-column align-items-start">
                            <div class="mb-3">
                                <iconify-icon icon="{{ $report['icon'] }}" width="36" height="36"></iconify-icon>
                            </div>
                            <h5 class="card-title ">{{ $report['title'] }}</h5>
                            <p class="card-text text-muted">Open the {{ strtolower($report['title']) }} page.</p>
                        </div>
                    </a>
                </div>
            @endperm
        @endforeach
    </div>
</div>
@endsection
