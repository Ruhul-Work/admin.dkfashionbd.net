@extends('backend.layouts.master')

@section('content')
    <style>
        .barcode-queue-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .barcode-list-actions {
            margin-top: 14px;
            margin-bottom: 14px;
        }

        .barcode-list-actions .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        #btnClearBarcodes {
            min-width: 105px;
        }

        #btnPrintBarcodes {
            min-width: 130px;
        }

        .barcode-queue-row {
            display: grid;
            grid-template-columns: minmax(220px, 1fr) 110px auto;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            background: #fff;
        }

        .barcode-queue-name {
            min-width: 0;
            font-weight: 600;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .barcode-queue-actions {
            display: flex;
            gap: 6px;
            justify-content: flex-end;
        }

        .barcode-grid {
            display: grid;
            grid-template-columns: repeat(3, 55mm);
            gap: 4mm;
            align-items: start;
        }

        .barcode-label {
            box-sizing: border-box;
            width: 55mm;
            padding: 3mm;
            border: 1px dashed #adb5bd;
            background: #fff;
            color: #000;
            text-align: center;
            break-inside: avoid;
            page-break-inside: avoid;
        }

        .barcode-label .product-name {
            height: 8mm;
            margin-bottom: 1.5mm;
            overflow: hidden;
            font-size: 10px;
            font-weight: 600;
            line-height: 4mm;
        }

        .barcode-label .barcode-symbol {
            box-sizing: border-box;
            width: 39mm;
            margin: 0 auto;
            padding: 0 3.8mm;
            background: #fff;
        }

        .barcode-label .barcode-img {
            display: block;
            width: 31.4mm;
            height: 22.9mm;
            margin: 0 auto;
            image-rendering: crisp-edges;
        }

        .barcode-label .barcode-text {
            margin-top: 1mm;
            font-size: 10px;
            letter-spacing: 1px;
            line-height: 1.2;
        }

        @media (max-width: 991.98px) {
            .barcode-grid {
                grid-template-columns: repeat(2, 55mm);
            }
        }

        @media (max-width: 575.98px) {
            .barcode-queue-row {
                grid-template-columns: 1fr;
            }

            .barcode-queue-actions {
                justify-content: flex-start;
            }

            .barcode-grid {
                grid-template-columns: 55mm;
            }
        }
    </style>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">Product Barcode</h5>
        </div>

        <div class="card-body">
            <form id="barcodeQueueForm" class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label class="form-label">Select Product</label>
                    <select id="productFilter" class="form-control js-s2-ajax"
                        data-url="{{ route('product.select2', ['sellable' => 1]) }}"
                        data-placeholder="Search product (name, sku...)">
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Quantity</label>
                    <input type="number" id="barcodeQty" class="form-control" value="1" min="1" max="100">
                </div>

                <div class="col-md-3">
                    <button type="submit" id="btnAddBarcode" class="btn btn-warning w-100">
                        Add to Print List
                    </button>
                </div>
            </form>

            <div id="barcodeQueuePanel" class="mt-4 d-none">
                <div class="barcode-queue-toolbar mb-2">
                    <div>
                        <strong>Print List</strong>
                        <span class="text-muted ms-2">
                            <span id="barcodeProductCount">0</span> products,
                            <span id="barcodeLabelCount">0</span> labels
                        </span>
                    </div>

                    <div class="barcode-list-actions d-flex gap-2">
                        <button type="button" id="btnClearBarcodes" class="btn btn-outline-danger btn-sm">
                            <i class="ri-delete-bin-6-line"></i>
                            <span>Clear All</span>
                        </button>
                        <button type="button" id="btnPrintBarcodes" class="btn btn-success btn-sm">
                            <i class="ri-printer-line"></i>
                            <span>Print All</span>
                        </button>
                    </div>
                </div>

                <div id="barcodeQueueRows" class="d-grid gap-2"></div>
            </div>

            <hr class="my-4">

            <div id="barcodePreview" class="barcode-grid"></div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            window.S2.auto();

            const queue = new Map();
            const maxPerProduct = 100;
            const maxTotalLabels = 300;
            const previewUrl = "{{ route('product.barcode.preview', ':id') }}";

            function queueTotal() {
                let total = 0;
                queue.forEach(function(item) {
                    total += item.qty;
                });
                return total;
            }

            function refreshQueueSummary() {
                const total = queueTotal();

                $('#barcodeProductCount').text(queue.size);
                $('#barcodeLabelCount').text(total);
                $('#barcodeQueuePanel').toggleClass('d-none', queue.size === 0);
            }

            function selectedProductName() {
                return $.trim($('#productFilter option:selected').text()) || 'Selected product';
            }

            function createQueueRow(productId, name, qty) {
                const $row = $('<div>', {
                    class: 'barcode-queue-row',
                    'data-product-id': productId
                });

                $('<div>', {
                    class: 'barcode-queue-name',
                    text: name,
                    title: name
                }).appendTo($row);

                $('<input>', {
                    type: 'number',
                    class: 'form-control form-control-sm barcode-queue-qty',
                    min: 1,
                    max: maxPerProduct,
                    value: qty,
                    'aria-label': 'Barcode quantity'
                }).appendTo($row);

                const $actions = $('<div>', {
                    class: 'barcode-queue-actions'
                }).appendTo($row);

                $('<button>', {
                    type: 'button',
                    class: 'btn btn-outline-primary btn-sm btn-update-barcode',
                    text: 'Update'
                }).appendTo($actions);

                $('<button>', {
                    type: 'button',
                    class: 'btn btn-outline-danger btn-sm btn-remove-barcode',
                    text: 'Remove'
                }).appendTo($actions);

                return $row;
            }

            function replaceQueueRow(productId, name, qty) {
                const $newRow = createQueueRow(productId, name, qty);
                const $oldRow = $('#barcodeQueueRows .barcode-queue-row[data-product-id="' + productId + '"]');

                if ($oldRow.length) {
                    $oldRow.replaceWith($newRow);
                } else {
                    $('#barcodeQueueRows').append($newRow);
                }
            }

            function replaceProductLabels(productId, html) {
                $('#barcodePreview .barcode-label[data-product-id="' + productId + '"]').remove();
                $('#barcodePreview').append(html);
            }

            function showError(message) {
                Swal.fire('Error', message || 'Unable to generate barcode labels.', 'error');
            }

            function requestLabels(productId, qty, onSuccess, $button) {
                if ($button && $button.length) {
                    $button.prop('disabled', true);
                }

                $.get(previewUrl.replace(':id', productId), {
                        qty: qty
                    })
                    .done(function(res) {
                        if (!res.success) {
                            showError(res.message || 'Barcode not found.');
                            return;
                        }

                        onSuccess(res.html);
                    })
                    .fail(function(xhr) {
                        const message = xhr.responseJSON && xhr.responseJSON.message
                            ? xhr.responseJSON.message
                            : 'Unable to generate barcode labels.';
                        showError(message);
                    })
                    .always(function() {
                        if ($button && $button.length) {
                            $button.prop('disabled', false);
                        }
                    });
            }

            $('#barcodeQueueForm').on('submit', function(event) {
                event.preventDefault();

                const productId = $('#productFilter').val();
                const addedQty = parseInt($('#barcodeQty').val(), 10);

                if (!productId || !Number.isInteger(addedQty) || addedQty < 1) {
                    showError('Select a product and enter a valid quantity.');
                    return;
                }

                const key = String(productId);
                const existing = queue.get(key);
                const newQty = (existing ? existing.qty : 0) + addedQty;
                const currentTotalWithoutProduct = queueTotal() - (existing ? existing.qty : 0);

                if (newQty > maxPerProduct) {
                    showError('A maximum of ' + maxPerProduct + ' labels is allowed per product.');
                    return;
                }

                if (currentTotalWithoutProduct + newQty > maxTotalLabels) {
                    showError('A maximum of ' + maxTotalLabels + ' labels is allowed in one print list.');
                    return;
                }

                const productName = existing ? existing.name : selectedProductName();
                const $button = $('#btnAddBarcode');

                requestLabels(productId, newQty, function(html) {
                    queue.set(key, {
                        name: productName,
                        qty: newQty
                    });

                    replaceQueueRow(productId, productName, newQty);
                    replaceProductLabels(productId, html);
                    refreshQueueSummary();

                    $('#productFilter').val(null).trigger('change');
                    $('#barcodeQty').val(1);
                }, $button);
            });

            $(document).on('click', '.btn-update-barcode', function() {
                const $row = $(this).closest('.barcode-queue-row');
                const productId = String($row.data('product-id'));
                const item = queue.get(productId);
                const newQty = parseInt($row.find('.barcode-queue-qty').val(), 10);

                if (!item || !Number.isInteger(newQty) || newQty < 1 || newQty > maxPerProduct) {
                    showError('Quantity must be between 1 and ' + maxPerProduct + '.');
                    return;
                }

                const newTotal = queueTotal() - item.qty + newQty;
                if (newTotal > maxTotalLabels) {
                    showError('A maximum of ' + maxTotalLabels + ' labels is allowed in one print list.');
                    return;
                }

                const $button = $(this);
                requestLabels(productId, newQty, function(html) {
                    queue.set(productId, {
                        name: item.name,
                        qty: newQty
                    });

                    replaceProductLabels(productId, html);
                    refreshQueueSummary();
                }, $button);
            });

            $(document).on('click', '.btn-remove-barcode', function() {
                const $row = $(this).closest('.barcode-queue-row');
                const productId = String($row.data('product-id'));

                queue.delete(productId);
                $row.remove();
                $('#barcodePreview .barcode-label[data-product-id="' + productId + '"]').remove();
                refreshQueueSummary();
            });

            $('#btnClearBarcodes').on('click', function() {
                queue.clear();
                $('#barcodeQueueRows, #barcodePreview').empty();
                refreshQueueSummary();
            });

            $('#btnPrintBarcodes').on('click', function() {
                if (queue.size === 0) {
                    showError('Add at least one product to the print list.');
                    return;
                }

                const labels = $('#barcodePreview').html();
                $('#barcodePrintFrame').remove();

                const printFrame = $('<iframe>', {
                    id: 'barcodePrintFrame',
                    title: 'Barcode print frame'
                }).css({
                    position: 'fixed',
                    right: 0,
                    bottom: 0,
                    width: 0,
                    height: 0,
                    border: 0,
                    opacity: 0,
                    pointerEvents: 'none'
                }).appendTo(document.body)[0];

                const printWindow = printFrame.contentWindow;
                const printDocument = printWindow.document;

                printDocument.open();
                printDocument.write(
                    '<!doctype html><html><head><meta charset="utf-8"><title>Barcode Labels</title>' +
                    '<style>' +
                    '@page{size:A4;margin:8mm;}' +
                    '*{box-sizing:border-box;}' +
                    'html,body{margin:0;padding:0;background:#fff;color:#000;font-family:Arial,sans-serif;}' +
                    '.barcode-grid{display:grid;grid-template-columns:repeat(3,55mm);gap:4mm;align-items:start;}' +
                    '.barcode-label{width:55mm;padding:3mm;border:0;text-align:center;break-inside:avoid;page-break-inside:avoid;background:#fff;}' +
                    '.product-name{height:8mm;margin-bottom:1.5mm;overflow:hidden;font-size:10px;font-weight:600;line-height:4mm;}' +
                    '.barcode-symbol{width:39mm;margin:0 auto;padding:0 3.8mm;background:#fff;}' +
                    '.barcode-img{display:block;width:31.4mm;height:22.9mm;margin:0 auto;image-rendering:crisp-edges;}' +
                    '.barcode-text{margin-top:1mm;font-size:10px;letter-spacing:1px;line-height:1.2;}' +
                    '</style></head><body><div class="barcode-grid">' + labels + '</div></body></html>'
                );
                printDocument.close();

                let printStarted = false;
                const startPrint = function() {
                    if (printStarted) {
                        return;
                    }

                    printStarted = true;
                    printWindow.focus();
                    printWindow.print();
                };

                printWindow.onafterprint = function() {
                    $('#barcodePrintFrame').remove();
                };

                const pendingImages = Array.from(printDocument.images).filter(function(image) {
                    return !image.complete;
                });

                if (pendingImages.length === 0) {
                    setTimeout(startPrint, 150);
                } else {
                    let remainingImages = pendingImages.length;
                    const imageReady = function() {
                        remainingImages--;
                        if (remainingImages <= 0) {
                            setTimeout(startPrint, 100);
                        }
                    };

                    pendingImages.forEach(function(image) {
                        image.addEventListener('load', imageReady, { once: true });
                        image.addEventListener('error', imageReady, { once: true });
                    });

                    setTimeout(startPrint, 1500);
                }
            });

            refreshQueueSummary();
        });
    </script>
@endsection

