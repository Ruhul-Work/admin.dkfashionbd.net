<div class="modal-header py-16 px-24 border-0" data-modal-key="branch-create">
    <h5 class="modal-title">Import Customer</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body p-24">
    <form id="branchCreateForm" data-ajax="true">
        @csrf
        <div class="row">
            <div class="col-md-12 mb-20">
                <label class="form-label text-sm mb-8">CSV/XLSX File <span class="text-danger">*</span></label>
                <input type="file" name="excel_file" accept=".xlsx, .xls, .csv" class="form-control p-1 radius-8"
                    id="csv" required>
                <div class="invalid-feedback d-block name-error" style="display:none"></div>
            </div>



            <div class="d-flex align-items-center justify-content-center gap-3 mt-16">
                <button type="button" class="btn border border-danger-600 text-danger-600 px-40 py-11 radius-8"
                    data-bs-dismiss="modal" id="cancel">Cancel</button>
                <button type="button" class="btn btn-primary px-48 py-12 radius-8" id="uploadButton">Import</button>
            </div>
    </form>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
    $(document).ready(function() {
        let jsonData = null; // store parsed JSON data

        // When a file is selected
        $('#csv').on('change', function(e) {
            const file = e.target.files[0];
            if (!file) return alert("Please select a file.");

            const reader = new FileReader();

            reader.onload = function(event) {
                const data = new Uint8Array(event.target.result);
                const workbook = XLSX.read(data, {
                    type: 'array'
                });

                // Get the first sheet
                const sheetName = workbook.SheetNames[0];
                const worksheet = workbook.Sheets[sheetName];

                // Convert to JSON
                jsonData = XLSX.utils.sheet_to_json(worksheet, {
                    defval: ""
                });
                console.log("Parsed JSON data:", jsonData);
            };

            reader.readAsArrayBuffer(file);
        });

        // When Upload button is clicked
        $('#uploadButton').on('click', function() {
            if (!jsonData) return alert("No file data found! Please select a file first.");

            $.ajax({
                url: "{{ route('customer.handle_csv') }}", // <-- change this to your backend endpoint
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(jsonData),
                success: function(response) {
                    console.log('Upload success:', response);
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Data uploaded successfully!',
                        confirmButtonText: 'OK'
                    }).then(() => {
                      $('#cancel').click(); // close modal
                     $('.AjaxDataTable').DataTable().ajax.reload(null, false);// reload DataTable
                    });
                },
                error: function(err) {
                    console.error('Upload failed:', err);
                     Swal.fire({
                        icon: 'error',
                        title: 'Failed!',
                        text: 'Invalid file type or format!',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
    });
</script>
