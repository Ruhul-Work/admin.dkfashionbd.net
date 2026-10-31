@for ($i = 0; $i < $qty; $i++)
    <div class="barcode-label" data-product-id="{{ $product->id }}">
        <div class="product-name">{{ $product->name }}</div>

        <div class="barcode-symbol">
            <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($barcodeValue, 'EAN13', 3, 90) }}"
                class="barcode-img" alt="EAN-13 barcode {{ $barcodeValue }}">
        </div>

        <div class="barcode-text">{{ $barcodeValue }}</div>

        {{-- Price is intentionally hidden for now.
        <div class="price">
            {{ currencySymbol() }} {{ number_format($product->price, 2) }}
        </div>
        --}}
    </div>
@endfor
