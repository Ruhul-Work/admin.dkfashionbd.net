<script>
    $(document).ready(function() {
        $('.dropdown > a').on('click', function(e) {
            e.preventDefault(); // stop page jump
            e.stopPropagation(); // stop parent toggling

            let $submenu = $(this).next('.sidebar-submenu');

            // Toggle only this submenu
            $submenu.slideToggle(200);

            // Optionally close sibling dropdowns (accordion style)
            $(this).parent().siblings('.dropdown').find('.sidebar-submenu').slideUp(200);
        });
        // Ajax DataTable init
        $(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            if ($('.AjaxDataTable').length) {
                const table = $('.AjaxDataTable').DataTable({
                    dom: '<"dt-top d-flex flex-wrap align-items-center justify-content-between gap-2 mb-12"lfr>t<"dt-bottom d-flex flex-wrap align-items-center justify-content-between mt-12"ip>',
                    ordering: true,
                    responsive: true,
                    stateSave: true,
                    order: [
                        [0, 'desc']
                    ],
                    scrollY: '50vh',
                    scrollX: true,
                    scrollCollapse: true,
                    processing: true,
                    serverSide: true,
                    serverMethod: 'post',
                    ajax: {
                        url: DATATABLE_URL
                    },

                    aLengthMenu: [
                        [10, 50, 100, 200, 500, 1000, -1],
                        [10, 50, 100, 200, 500, 1000, "ALL"]
                    ],
                    language: {
                        search: '',
                        sLengthMenu: '_MENU_',
                        searchPlaceholder: "Search",
                        info: "_START_ - _END_ of _TOTAL_ items",
                        paginate: {
                            next: '<i class="ri-arrow-right-s-line"></i>',
                            previous: '<i class="ri-arrow-left-s-line"></i>'
                        },
                    },


                    columnDefs: [{
                            targets: 0, // SL checkbox
                            orderable: false,
                            searchable: false,
                            className: 'no-export',
                            render: (data, type, row, meta) => {
                                const sl = String(meta.row + meta.settings
                                    ._iDisplayStart + 1).padStart(2, '0');
                                return `
                            <div class="form-check style-check d-flex align-items-center">
                            <input class="form-check-input row-check" type="checkbox" data-id="${data}">
                            <label class="form-check-label">${sl}</label>
                            </div>`;
                            }
                        },
                        {
                            targets: -1, // Action column 
                            orderable: false,
                            searchable: false,
                            className: 'no-export text-end',
                            width: '120px'
                        }
                    ],

                    buttons: [{
                            extend: 'copy',
                            className: 'btn btn-sm  btn-light d-none',
                            exportOptions: {
                                columns: ':visible:not(.no-export)'
                            }
                        },
                        {
                            extend: 'excel',
                            className: 'btn btn-sm btn-light d-none',
                            exportOptions: {
                                columns: ':visible:not(.no-export)'
                            }
                        },
                        {
                            extend: 'print',
                            className: 'btn btn-sm btn-light d-none',
                            exportOptions: {
                                columns: ':visible:not(.no-export)'
                            }
                        }

                    ],

                    drawCallback: function() {
                        const $w = $(this.api().table().container());
                        $w.find('.dataTables_paginate .pagination')
                            .addClass('pagination-sm justify-content-end');
                        $w.find('.dataTables_length select')
                            .addClass('form-select form-select-sm dt-len');
                    },


                    initComplete: function(settings) {
                        const $wrap = $(settings.nTableWrapper);
                        const $card = $(settings.nTable).closest('.card');

                        // ✅ এই টেবিলের নিজের ফিল্টারকেই move করুন (গ্লোবাল না)
                        $card.find('#tableSearch').empty().append($wrap.find(
                            '.dataTables_filter'));

                        // Length select bootstrapize
                        $wrap.find('.dataTables_length select').addClass(
                            'form-select form-select-sm dt-len');


                        // ==== External buttons → internal buttons trigger (duplicate free)
                        $(document).off('click.dtbtn');
                        $(document).on('click.dtbtn', '.export-copy', function(e) {
                            e.preventDefault();
                            table.button('.buttons-copy').trigger();
                        });
                        $(document).on('click.dtbtn', '.export-excel', function(e) {
                            e.preventDefault();
                            table.button('.buttons-excel').trigger();
                        });
                        $(document).on('click.dtbtn', '.export-print', function(e) {
                            e.preventDefault();
                            table.button('.buttons-print').trigger();
                        });

                        // ✅ Custom Hide/Show Columns using Swal (ColVis বাদ)
                        $(document).on('click.dtbtn', '.export-hide-column', function(e) {
                            e.preventDefault();

                            // কোন কোন কলাম দেখাব/লুকাব — first(0) ও last বাদ চাইলে logic বসান
                            let html = '';
                            table.columns().every(function(idx) {
                                // first: 0 (SL) বাদ, আর দরকার হলে last বাদ দিন
                                if (idx === 0) return;
                                const title = $(this.header()).text()
                                    .trim() || ('Column ' + (idx + 1));
                                const vis = this.visible();
                                html += `
                    <div class="mb-1 px-3 py-2 text-start">
                        <input class="form-check-input colvis-toggle" type="checkbox" id="col_${idx}" data-idx="${idx}" ${vis ? 'checked' : ''}>
                        <label class="form-check-label" for="col_${idx}">${title}</label>
                    </div>`;
                            });

                            Swal.fire({
                                title: 'Hide/Unhide Columns',
                                html: `<div style="max-height:50vh;overflow:auto;">${html}</div>`,
                                focusConfirm: false,
                                showCancelButton: true,
                                confirmButtonText: 'Apply',
                                cancelButtonText: 'Cancel',
                                customClass: {
                                    popup: 'text-start'
                                },
                                preConfirm: () => {
                                    document.querySelectorAll(
                                        '.colvis-toggle').forEach(
                                        cb => {
                                            const idx = parseInt(cb
                                                .dataset.idx, 10
                                            );
                                            table.column(idx)
                                                .visible(cb
                                                    .checked);
                                        });
                                }
                            });
                        });

                        // Refresh
                        $(document).on('click.dtbtn', '.export-refresh', function(e) {
                            e.preventDefault();
                            table.ajax.reload(null, false);
                            if (window.Swal) Swal.fire({
                                icon: 'success',
                                title: 'Reloaded',
                                timer: 900,
                                showConfirmButton: false
                            });
                        });

                        // Master checkbox
                        $(document).on('change.dtbtn', '#select-all', function() {
                            $('.row-check').prop('checked', this.checked);
                        });
                    }
                });
                $('#tableSearch input, #tableSearch').on('keyup change', function() {
                    t.search($(this).val() || '').draw();
                });

            }

            // Tooltips re-init after draw (error-safe)
            $(document).on('draw.dt', function() {
                if (window.bootstrap?.Tooltip) {
                    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
                        try {
                            new bootstrap.Tooltip(el);
                        } catch (_) {}
                    });
                }
            });
        });


        // DataTable init (non-ajax)
        if ($('.DataTable').length > 0) {

            table = $('.DataTable').DataTable({
                "bFilter": true,
                "sDom": 'fBtlpi',
                "ordering": true,
                "responsive": true,
                "scrollX": true,
                "scrollY": "60vh",
                "scrollCollapse": true,

                'aLengthMenu': [
                    [10, 50, 100, 200, 500, -1],
                    [10, 50, 100, 200, 500, "ALL"]
                ],
                "language": {
                    search: '',
                    sLengthMenu: '_MENU_',
                    searchPlaceholder: "Search",
                    info: "_START_ - _END_ of _TOTAL_ items",
                    paginate: {
                        next: '<i class="ri-arrow-right-s-line"></i>',
                        previous: '<i class="ri-arrow-left-s-line"></i> '
                    },
                },
                'buttons': [{
                        extend: 'copy',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'csv',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'excel',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'print',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    'colvis'

                ],
                initComplete: (settings, json) => {
                    $('.dataTables_filter').appendTo('#tableSearch');
                    $('.dataTables_filter').appendTo('.search-input');

                    $(document).on('click', '.export-excel', function() {
                        $('.dt-buttons .buttons-excel').click();
                    });

                    $(document).on('click', '.export-print', function() {
                        $('.dt-buttons .buttons-print').click();
                    });

                    $(document).on('click', '.export-copy', function() {
                        $('.dt-buttons .buttons-copy').click();
                        Swal.fire({
                            title: "Success",
                            text: "Successfully copied",
                            icon: "success",
                            showConfirmButton: false,
                            timer: 1500
                        });
                    });

                    $(document).on('click', '.export-refresh', function() {
                        //$('.DataTable').DataTable().ajax.reload();
                        $('.DataTable').DataTable().draw();
                        Swal.fire({
                            title: "Success",
                            text: "Successfully Reloaded",
                            icon: "success",
                            showConfirmButton: false,
                            timer: 1500
                        });
                    });


                    // Custom function to toggle column visibility
                    function toggleColumn(index) {
                        table.column(index).visible(!table.column(index).visible());
                    }

                    // Event listener for column hide/unhide button
                    $(document).on('click', '.export-hide-column', function() {
                        var columnCheckboxes = '';
                        table.columns().every(function() {
                            var column = this;
                            var columnTitle = $(column.header()).text().trim();

                            var columnIndex = column.index();
                            columnCheckboxes +=
                                `<div style="text-align:left;"><input type="checkbox" id="chk_${columnIndex}" class="column-checkbox" value="${columnIndex}" ${column.visible() ? 'checked' : ''}><label for="chk_${columnIndex}">${columnTitle}</label></div>`;
                        });

                        Swal.fire({
                            title: 'Hide/Unhide Columns',
                            html: columnCheckboxes,
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Apply',
                            cancelButtonText: 'Cancel',
                            preConfirm: () => {
                                $('.column-checkbox').each(function() {
                                    var columnIndex = $(this).val();
                                    var isChecked = $(this).prop(
                                        'checked');
                                    if (isChecked !== table.column(
                                            columnIndex).visible()) {
                                        toggleColumn(columnIndex);
                                    }
                                });
                            }
                        });
                    });



                },
            });
        }
        // $('#tableSearch input, #tableSearch').on('keyup change', function(){ t.search($(this).val()||'').draw();
    });


    // ====== Minimal Global AjaxModal Manager (keep this once in layout) ======

    (function() {
        const MODAL_ID = '#AjaxModal';

        function csrf() {
            return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        }

        function qs(obj) {
            try {
                return $.param(obj || {})
            } catch (e) {
                return ''
            }
        }

        // Safe dynamic function caller: "Namespace.fn" বা "fn" — window থেকে resolve করবে
        function callFnByName(fnName, args = []) {
            if (!fnName) return;
            try {
                const parts = String(fnName).split('.');
                let ctx = window,
                    fn = null;
                while (parts.length > 1) {
                    ctx = ctx[parts.shift()];
                    if (!ctx) return;
                }
                fn = ctx[parts[0]];
                if (typeof fn === 'function') return fn.apply(ctx, args);
            } catch (e) {}
        }

        // Open & load modal partial
        $(document).on('click', '.AjaxModal', function(e) {
            e.preventDefault();
            const $btn = $(this);
            const url = $btn.data('ajax-modal');
            const size = String($btn.data('size') || '').trim();
            const keyAttr = String($btn.data('key') || '').trim();
            const params = $btn.data('params') || null;
            const onLoadFn = $btn.data('onload') || null;
            const onSuccFn = $btn.data('onsuccess') || null;

            const $modal = $(MODAL_ID);
            const $dialog = $modal.find('.modal-dialog');
            const $content = $modal.find('.modal-content');

            // apply size
            $dialog.removeClass('modal-sm modal-lg modal-xl');
            if (['sm', 'lg', 'xl'].includes(size)) $dialog.addClass('modal-' + size);

            // loading
            $content.html(
                '<div class="p-5 text-center"><div class="spinner-border"></div><div class="mt-2">Loading...</div></div>'
            );

            // build final URL
            let finalUrl = url;
            const q = qs(params);
            if (q) finalUrl += (url.includes('?') ? '&' : '?') + q;

            // store per-trigger callbacks for later use
            $modal.data('onload-fn', onLoadFn || null);
            $modal.data('onsuccess-fn', onSuccFn || null);

            $.ajax({
                    url: finalUrl,
                    type: 'GET'
                })
                .done(function(html) {
                    $content.html(html);
                    $modal.modal('show');

                    // resolve key: content data-modal-key > trigger data-key
                    const foundKey = $content.find('[data-modal-key]').first().data('modal-key');
                    const activeKey = (foundKey || keyAttr || '').toString().trim();
                    $modal.data('modal-key', activeKey || null);

                    // 1) page-provided onLoad function (via data-onload)
                    callFnByName($modal.data('onload-fn'), [$modal]);

                    // 2) or fallback: window.ModalHooks[activeKey]?.onLoad
                    if (!$modal.data('onload-fn') && activeKey && window.ModalHooks && window
                        .ModalHooks[activeKey] && typeof window.ModalHooks[activeKey].onLoad ===
                        'function') {
                        try {
                            window.ModalHooks[activeKey].onLoad($modal);
                        } catch (e) {}
                    }
                })
                .fail(function() {
                    $content.html('<div class="p-4 text-danger">Failed to load modal.</div>');
                });
        });

        // Ajax form submit inside modal
        $(document).on('submit', '#AjaxModal form[data-ajax="true"]', function(e) {
            e.preventDefault();

            const $form = $(this);
            const $modal = $('#AjaxModal');
            const url = $form.attr('action');
            const $btn = $form.find('[type="submit"]');

            // IMPORTANT: use FormData (not serialize) to include files
            const formData = new FormData(this);

            $btn.prop('disabled', true);
            $form.find('.invalid-feedback.d-block').hide().text('');
            $form.find('.is-invalid').removeClass('is-invalid');

            $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || ''
                    }
                })
                .done(function(res) {
                    // success callbacks (unchanged)
                    const succName = $modal.data('onsuccess-fn') || null;
                    const key = $modal.data('modal-key') || null;

                    if (succName) {
                        try {
                            (function call() {
                                const parts = String(succName).split('.');
                                let ctx = window,
                                    fn = null;
                                while (parts.length > 1) {
                                    ctx = ctx[parts.shift()];
                                    if (!ctx) return;
                                }
                                fn = ctx[parts[0]];
                                if (typeof fn === 'function') fn(res);
                            })();
                        } catch (e) {}
                    } else if (key && window.ModalHooks && window.ModalHooks[key] && typeof window
                        .ModalHooks[key].onSuccess === 'function') {
                        try {
                            window.ModalHooks[key].onSuccess(res);
                        } catch (e) {}
                    }

                    const el = $modal.get(0);
                    bootstrap.Modal.getOrCreateInstance(el).hide();

                    if (window.Swal) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: res?.msg || 'Saved',
                            timer: 1000,
                            showConfirmButton: false
                        });
                    }
                })
                .fail(function(xhr) {
                    if (xhr.status === 422) {
                        const errs = xhr.responseJSON?.errors || {};
                        Object.keys(errs).forEach(function(k) {
                            const msg = errs[k][0];
                            $form.find(`.${k}-error`).text(msg).show();
                            const $f = $form.find(`[name="${k}"]`);
                            if ($f.length) {
                                $f.addClass('is-invalid');
                                let $fb = $f.siblings('.invalid-feedback');
                                if (!$fb.length) {
                                    $fb = $('<div class="invalid-feedback"></div>');
                                    $f.after($fb);
                                }
                                $fb.text(msg).show();
                            }
                        });
                    } else if (xhr.status === 403) {
                        window.Swal && Swal.fire({
                            icon: 'warning',
                            title: 'Forbidden',
                            text: xhr.responseJSON?.message || 'Permission denied'
                        });
                    } else {
                        window.Swal && Swal.fire({
                            icon: 'error',
                            title: 'Failed',
                            text:  xhr.responseJSON.msg?xhr.responseJSON.msg:'something went wrong'
                        });
                    }
                })
                .always(function() {
                    $btn.prop('disabled', false);
                });
        });


        // Cleanup
        $(MODAL_ID).on('hidden.bs.modal', function() {
            const $m = $(this);
            $m.find('.modal-dialog').removeClass('modal-sm modal-lg modal-xl');
            $m.find('.modal-content').empty();
            $m.removeData('modal-key onload-fn onsuccess-fn');
        });
    })();


    // ====== Global Ajax Form Handler (for non-modal ajax forms) ======
    $(document).on('submit', 'form[data-ajax-form="true"]', function(e) {
        e.preventDefault();
        const $form = $(this);
        $.ajax({
            url: $form.attr('action'),
            method: $form.attr('method') || 'POST',
            data: new FormData(this),
            contentType: false,
            processData: false,
            success(res) {
                $form.trigger('ajax:success', [res]);
            },
            error(err) {
                // optional error handler
            }
        });
    });


    // =================Global ajaxViwemodal VIEW / SHOW MODAL ===================
    $(document).on('click', '.AjaxViewModal', function(e) {
        e.preventDefault();

        const url = $(this).data('ajax-modal');
        const size = $(this).data('size') || 'lg';

        const $modal = $('#AjaxModal');

        // reset size
        $modal.find('.modal-dialog')
            .removeClass('modal-sm modal-lg modal-xl')
            .addClass('modal-' + size);

        // loading state
        $modal.find('.modal-content').html(`
        <div class="p-4 text-center text-muted">
            <div class="spinner-border spinner-border-sm"></div>
            Loading...
        </div>
    `);

        // load html
        $.get(url, function(html) {
            $modal.find('.modal-content').html(html);
            bootstrap.Modal.getOrCreateInstance($modal[0]).show();
        });
    });
    // ====== Bangla Slugify ======
    // Usage: slugify("বাংলা টেক্সট") → "bangla_text"


    window.slugify = function(text) {
        const clusters = {
            "ক্ষ": "kh",
            "জ্ঞ": "gg",
            "ঞ্চ": "nch",
            "ঞ্জ": "nj",
            "ন্ড": "nd",
            "ত্ত": "tt",
            "ত্থ": "tth",
            "ল্ল": "ll",
            "ষ্ঠ": "shth",
            "ষ্ট": "sht",
            "শ্চ": "shch",
            "ত্র": "tr",
            "দ্ম": "dm",
            "ম্ভ": "mbh",
            "ন্ধ": "ndh",
            "ঙ্ঘ": "nggh",
            "ঙ্খ": "ngkh",
            "ঙ্জ": "ngj",
            "ঙ্চ": "ngch",
            "ঙ্ঠ": "ngth",
            "ন্ড": "nd",
            "ত্ত": "tt",
            "ত্থ": "tth",
            "ল্ল": "ll",
            "ষ্ঠ": "shth",
            "ষ্ট": "sht",
            "শ্চ": "shch",
            "ত্র": "tr",
            "দ্ম": "dm",
            "ম্ভ": "mbh",
            "ন্ধ": "ndh",
            "ঙ্ঘ": "nggh",
            "ঙ্খ": "ngkh",
            "ঙ্জ": "ngj",
            "ঙ্চ": "ngch",
            "ঙ্ঠ": "ngth",
            "ওয়া": "wa",
            "ওয়": "wa"
        };


        const banglaMap = {
            "অ": "o",
            "আ": "a",
            "ই": "i",
            "ঈ": "i",
            "উ": "u",
            "ঊ": "u",
            "ঋ": "ri",
            "এ": "e",
            "ঐ": "oi",
            "ও": "o",
            "ঔ": "ou",
            "ক": "k",
            "খ": "kh",
            "গ": "g",
            "ঘ": "gh",
            "ঙ": "ng",
            "চ": "ch",
            "ছ": "chh",
            "জ": "j",
            "ঝ": "jh",
            "ঞ": "n",
            "ট": "t",
            "ঠ": "th",
            "ড": "d",
            "ঢ": "dh",
            "ণ": "n",
            "ত": "t",
            "থ": "th",
            "দ": "d",
            "ধ": "dh",
            "ন": "n",
            "প": "p",
            "ফ": "ph",
            "ব": "b",
            "ভ": "bh",
            "ম": "m",
            "য": "j",
            "র": "r",
            "ল": "l",
            "শ": "sh",
            "ষ": "sh",
            "স": "s",
            "হ": "h",
            "ড়": "r",
            "ঢ়": "rh",
            "য়": "y",
            "ঁ": "n",
            "ং": "ng",
            "ঃ": "h",
            "্": "",
            "া": "a",
            "ি": "i",
            "ী": "i",
            "ু": "u",
            "ূ": "u",
            "ৃ": "ri",
            "ে": "e",
            "ৈ": "oi",
            "ো": "o",
            "ৌ": "ou",
            "০": "0",
            "১": "1",
            "২": "2",
            "৩": "3",
            "৪": "4",
            "৫": "5",
            "৬": "6",
            "৭": "7",
            "৮": "8",
            "৯": "9",
            " ": "-",
            "।": "",
            ",": "",
            ".": "",
            "?": ""
        };

        let result = text;

        // Replace clusters first
        for (let key in clusters) {
            result = result.replace(new RegExp(key, "g"), clusters[key]);
        }

        // Replace single characters
        let final = "";
        for (let char of result) {
            final += banglaMap[char] !== undefined ? banglaMap[char] : char;
        }

        return final.toLowerCase();
    }


    // ---- Global Select2 Helper ----
    window.S2 = (function($) {
        // nearest modal কে dropdownParent বানাই (না থাকলে body)
        function parentOf($el) {
            const $m = $el.closest('.modal');
            return $m.length ? $m : $(document.body);
        }

        // core initializer
        function init($el, opts = {}) {
            if (!$el || !$el.length) return;

            // destroy before re-init (modal re-open edge-case)
            if ($el.hasClass('select2-hidden-accessible')) {
                $el.select2('destroy');
            }

            // base config
            const base = {
                dropdownParent: parentOf($el),
                width: '100%',
                placeholder: $el.attr('placeholder') || 'Select an option',
                allowClear: true
            };

            // merge + init
            const cfg = $.extend(true, {}, base, opts);
            $el.select2(cfg);


            // preselected support (server side selected option থাকলে টিকে যাবে)
            if ($el.find('option[selected]').length) {
                $el.trigger('change.select2');
            } else if ($el.data('initId') && $el.data('initText')) {
                // data-init-id / data-init-text দিলে client-side প্রিসেট
                const id = $el.data('initId');
                const text = $el.data('initText');
                if (!$el.find(`option[value="${id}"]`).length) {
                    $el.append(new Option(text, id, true, true)).trigger('change');
                }
            }
        }

        // ajax shortcut: শুধু url দিলেই চলবে
        function ajax($el, url, extra = {}) {
            return init($el, {
                ajax: {
                    url: url,
                    dataType: 'json',
                    delay: 250,
                    data: params => ({
                        q: params?.term || ''
                    }),
                    processResults: data => (

                        Array.isArray(data?.results) ? {
                            results: data.results
                        } : {
                            results: data
                        }
                    )
                },
                ...extra
            })
        }

        // scope-wise auto init: .js-s2, .js-s2-ajax[data-url]
        function auto(scope) {
            const $scope = scope ? $(scope) : $(document);
            // normal select2
            $scope.find('select.js-s2').each(function() {
                init($(this));
            });

            // ajax select2 (declare with data-url)
            $scope.find('select.js-s2-ajax').each(function() {
                const $el = $(this);
                const url = $el.data('url');
                if (!url) return;
                ajax($el, url);
            });
        }

        // public API
        return {
            init,
            ajax,
            auto
        };
    })(jQuery);
</script>
