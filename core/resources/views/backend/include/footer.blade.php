    
<footer class="d-footer">
  <div class="row align-items-center justify-content-between">
    <div class="col-auto">
      <p class="mb-0">© 2025 Bintel Future Tech All Rights Reserved.</p>
    </div>
    <div class="col-auto">
      <p class="mb-0">Made by <span class="text-primary-600">Bintel</span></p>
    </div>
  </div>
</footer>
</main>
    <!-- jQuery library js -->
    <script src="{{ asset('theme/admin/assets/js/lib/jquery-3.7.1.min.js') }}" type="text/javascript"></script>
    <!-- Bootstrap js -->
    <script src="{{ asset('theme/admin/assets/js/lib/bootstrap.bundle.min.js') }}" type="text/javascript"></script>
    <!-- Apex Chart js -->
    <script src="{{ asset('theme/admin/assets/js/lib/apexcharts.min.js') }}" type="text/javascript"></script>
    <!-- Data Table js -->
    <script src="{{ asset('theme/admin/assets/js/lib/dataTables.min.js') }}" type="text/javascript"></script>
    <!-- Iconify Font js -->
    <script src="{{ asset('theme/admin/assets/js/lib/iconify-icon.min.js') }}" type="text/javascript"></script>
    <!-- jQuery UI js -->
    <script src="{{ asset('theme/admin/assets/js/lib/jquery-ui.min.js') }}" type="text/javascript"></script>
    <!-- Vector Map js -->
    <script src="{{ asset('theme/admin/assets/js/lib/jquery-jvectormap-2.0.5.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('theme/admin/assets/js/lib/jquery-jvectormap-world-mill-en.js') }}" type="text/javascript">
    </script>
    <!-- Popup js -->
    <script src="{{ asset('theme/admin/assets/js/lib/magnifc-popup.min.js') }}" type="text/javascript"></script>
    <!-- Slick Slider js -->
    <script src="{{ asset('theme/admin/assets/js/lib/slick.min.js') }}" type="text/javascript"></script>
    <!-- Prism js -->
    <script src="{{ asset('theme/admin/assets/js/lib/prism.js') }}" type="text/javascript"></script>
    <!-- File Upload js -->
    <script src="{{ asset('theme/admin/assets/js/lib/file-upload.js') }}" type="text/javascript"></script>
    <!-- Audio Player js -->
    <script src="{{ asset('theme/admin/assets/js/lib/audioplayer.js') }}" type="text/javascript"></script>

    <!-- Main js -->
    <script src="{{ asset('theme/admin/assets/js/app.js') }}?v=20261013-4" type="text/javascript"></script>

    {{-- sweetalert js --}}
    <script src="{{ asset('theme/admin/assets/js/lib/sweetalert/sweetalerts.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('theme/admin/assets/js/lib/sweetalert/sweetalert2.all.min.js') }}" type="text/javascript"></script>

    {{-- select tow js --}}
    <script src="{{asset('theme/admin/assets/js/lib/select2/js/select2.min.js')}}" type="text/javascript"></script>

    {{-- ajax data table js --}}
        <script src="{{asset('theme/admin/assets/js/lib/ajaxdatatable/dataTables.bootstrap5.min.js')}}" type="text/javascript"></script>
    {{-- <script src="{{asset('theme/admin/assets/js/lib/ajaxdatatable/jquery.dataTables.min.js')}}" type="text/javascript"></script> --}}
    {{-- <script src="{{asset('theme/admin/assets/js/lib/ajaxdatatable/datatable.js')}}" type="text/javascript"></script> --}}

    <!-- DataTables core + Bootstrap 5 skin Buttons extension (CSS + JS)-->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.dataTables.min.css">
    <!-- Buttons features -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.print.min.js"></script>
{{-- <script src="/path/to/dist/js/spartan-multi-image-picker.min.js"></script>  --}}
   <link rel="stylesheet" href="{{asset('theme/admin/assets/css/image-uploader.min.css')}}">
   <script src="{{asset('theme/admin/assets/plugins/fileupload/image-uploader.min.js')}}" type="text/javascript"></script>
   <script src="{{asset('theme/admin/assets/plugins/fileupload/spartan-multi-image-picker-min.js')}}" type="text/javascript"></script>

   
   {{--Global Form submit Modal  --}}
    <div class="modal fade" id="AjaxModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog  modal-dialog-centered">
            <div class="modal-content">
              

            </div>
        </div>
    </div>

    {{-- Global Ajax View Modal --}}
<div class="modal fade" id="AjaxViewModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            {{-- AJAX content will be loaded here --}}
        </div>
    </div>
</div>

