@extends('backend.layouts.master')

@section('meta')
  <title>Edit Website Setting</title>
@endsection

@section('content')
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

  <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h6 class="fw-semibold mb-0">Edit Website Setting</h6>
      <p class="m-0">Update website profile: {{ $setting->key }}</p>
    </div>
    <ul class="d-flex align-items-center gap-2">
      <li class="fw-medium">
        <a href="{{ route('backend.dashboard') }}" class="d-flex align-items-center gap-1 hover-text-primary">
          <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon> Dashboard
        </a>
      </li>
      <li>-</li>
      <li class="fw-medium">
        <a href="{{ route('website-setting.website-settings.index') }}" class="hover-text-primary">Website Settings</a>
      </li>
      <li>-</li>
      <li class="fw-medium">Edit Website Setting</li>
    </ul>
  </div>

  <form action="{{ route('website-setting.website-settings.update', $setting->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="row gy-4">
        {{-- Left Column: Basic Info --}}
        <div class="col-lg-8">
            <div class="card mb-24">
                <div class="card-header border-bottom">
                    <h5 class="card-title mb-0">Company Information</h5>
                </div>
                <div class="card-body">
                    <div class="row gy-3">
                        <div class="col-md-6">
                            <label class="form-label">Setting Profile Key <span class="text-danger">*</span></label>
                            <input type="text" name="profile_key" class="form-control" value="{{ old('profile_key', $setting->profile_key) }}" required placeholder="e.g. General, Christmas_2024">
                            <small class="text-secondary-light">Unique identifier for this setting set</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Company Name <span class="text-danger">*</span></label>
                            <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $setting->company_name) }}" required placeholder="Enter company name">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Website URL</label>
                            <input type="url" name="website_url" class="form-control" value="{{ old('website_url', $setting->website_url) }}" placeholder="https://example.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $setting->phone) }}" placeholder="Enter phone number">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $setting->email) }}" placeholder="Enter email address">
                        </div>
                         <div class="col-md-6">
                            <label class="form-label">Currency</label>
                            <input type="text" name="currency_code" class="form-control" value="{{ old('currency_code', $setting->currency_code) }}" placeholder="Enter currency code">
                        </div>
                         <div class="col-md-6">
                            <label class="form-label">Shipping Charge</label>
                            <input type="number" name="shipping_charge" step="0.01" min="0" class="form-control" value="{{ old('shipping_charge', $setting->shipping_charge) }}" placeholder="Enter shipping charge">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Free Shipping Purchase Amount</label>
                            <input type="number" name="free_shipping_amount" min="0" class="form-control" value="{{ old('free_shipping_amount', $setting->free_shipping_amount) }}" placeholder="Enter amount">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Office Address</label>
                            <textarea name="address" class="form-control" rows="3" placeholder="Enter physical address">{{ old('address', $setting->address) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Social Links --}}
            <div class="card">
                <div class="card-header border-bottom">
                    <h5 class="card-title mb-0">Social Links</h5>
                </div>
                <div class="card-body">
                    <div class="row gy-3">
                        <div class="col-md-6">
                            <label class="form-label">Facebook</label>
                            <input type="url" name="facebook" class="form-control" value="{{ old('facebook', $setting->facebook) }}" placeholder="Facebook URL">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Instagram</label>
                            <input type="url" name="instagram" class="form-control" value="{{ old('instagram', $setting->instagram) }}" placeholder="Instagram URL">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Youtube</label>
                            <input type="url" name="youtube" class="form-control" value="{{ old('youtube', $setting->youtube) }}" placeholder="Youtube URL">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tiktok</label>
                            <input type="url" name="tiktok" class="form-control" value="{{ old('tiktok', $setting->tiktok) }}" placeholder="Tiktok URL">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Media --}}
        <div class="col-lg-4">
            <div class="card mb-24">
                <div class="card-header border-bottom">
                    <h5 class="card-title mb-0">Website Assets</h5>
                </div>
                <div class="card-body">
                    <div class="row gy-3">
                        {{-- Logo --}}
                        <div class="col-12 mb-3">
                            <label class="form-label">Company Logo</label>
                            <div class="upload-image-wrapper">
                                <label for="logoInput" class="upload-image-box border-dashed rounded text-center p-3 cursor-pointer d-block">
                                    <div id="logoPlaceholder" class="{{ $setting->logo ? 'd-none' : '' }}">
                                        <iconify-icon icon="solar:camera-add-outline" class="text-4xl text-secondary-light mb-2"></iconify-icon>
                                        <p class="text-secondary-light mb-0 text-sm">Upload Logo</p>
                                    </div>
                                    <div id="logoPreviewContainer" class="{{ $setting->logo ? '' : 'd-none' }} position-relative">
                                        <img src="{{ $setting->logo ? image($setting->logo) : '' }}" id="logoPreview" style="max-height: 120px;" class="rounded shadow-sm img-fluid">
                                    </div>
                                </label>
                                <input type="file" name="logo" class="d-none" id="logoInput" accept="image/*">
                            </div>
                        </div>

                        {{-- Favicon --}}
                        <div class="col-12 mt-3">
                            <label class="form-label">Favicon</label>
                            <div class="upload-image-wrapper">
                                <label for="faviconInput" class="upload-image-box border-dashed rounded text-center p-3 cursor-pointer d-block">
                                    <div id="faviconPlaceholder" class="{{ $setting->favicon ? 'd-none' : '' }}">
                                        <iconify-icon icon="solar:camera-add-outline" class="text-4xl text-secondary-light mb-2"></iconify-icon>
                                        <p class="text-secondary-light mb-0 text-sm">Upload Favicon</p>
                                    </div>
                                    <div id="faviconPreviewContainer" class="{{ $setting->favicon ? '' : 'd-none' }} position-relative">
                                        <img src="{{ $setting->favicon ? image($setting->favicon) : '' }}" id="faviconPreview" style="max-height: 60px;" class="rounded shadow-sm img-fluid">
                                    </div>
                                </label>
                                <input type="file" name="favicon" class="d-none" id="faviconInput" accept="image/*">
                            </div>
                        </div>

                        {{-- Merchant QR Code --}}
                        <div class="col-12 mt-3">
                            <label class="form-label">Merchant QR Code</label>
                            <div class="upload-image-wrapper">
                                <label for="merchantQrInput" class="upload-image-box border-dashed rounded text-center p-3 cursor-pointer d-block">
                                    <div id="merchantQrPlaceholder" class="{{ $setting->merchant_qr ? 'd-none' : '' }}">
                                        <iconify-icon icon="solar:camera-add-outline" class="text-4xl text-secondary-light mb-2"></iconify-icon>
                                        <p class="text-secondary-light mb-0 text-sm">Upload QR Code</p>
                                    </div>
                                    <div id="merchantQrPreviewContainer" class="{{ $setting->merchant_qr ? '' : 'd-none' }} position-relative">
                                        <img src="{{ $setting->merchant_qr ? image($setting->merchant_qr) : '' }}" id="merchantQrPreview" style="max-height: 120px;" class="rounded shadow-sm img-fluid">
                                    </div>
                                </label>
                                <input type="file" name="merchant_qr" class="d-none" id="merchantQrInput" accept="image/*">
                            </div>
                        </div>
                        <div class="col-12 mt-3">
                            <label class="form-label">Slider Image 1</label>
                            <div class="upload-image-wrapper">
                                <label for="sliderImage1Input" class="upload-image-box border-dashed rounded text-center p-3 cursor-pointer d-block">
                                    <div id="sliderImage1Placeholder" class="{{ $setting->slider_image1 ? 'd-none' : '' }}">
                                        <iconify-icon icon="solar:camera-add-outline" class="text-4xl text-secondary-light mb-2"></iconify-icon>
                                        <p class="text-secondary-light mb-0 text-sm">Upload Slider Image 1</p>
                                    </div>
                                    <div id="sliderImage1PreviewContainer" class="{{ $setting->slider_image1 ? '' : 'd-none' }} position-relative">
                                        <img src="{{ $setting->slider_image1 ? image($setting->slider_image1) : '' }}" id="sliderImage1Preview" style="max-height: 120px;" class="rounded shadow-sm img-fluid">
                                    </div>
                                </label>
                                <input type="file" name="slider_image1" class="d-none" id="sliderImage1Input" accept="image/*">
                            </div>
                        </div>
                        <div class="col-12 mt-3">
                            <label class="form-label">Slider Image 2</label>
                            <div class="upload-image-wrapper">
                                <label for="sliderImage2Input" class="upload-image-box border-dashed rounded text-center p-3 cursor-pointer d-block">
                                    <div id="sliderImage2Placeholder" class="{{ $setting->slider_image2 ? 'd-none' : '' }}">
                                        <iconify-icon icon="solar:camera-add-outline" class="text-4xl text-secondary-light mb-2"></iconify-icon>
                                        <p class="text-secondary-light mb-0 text-sm">Upload Slider Image 2</p>
                                    </div>
                                    <div id="sliderImage2PreviewContainer" class="{{ $setting->slider_image2 ? '' : 'd-none' }} position-relative">
                                        <img src="{{ $setting->slider_image2 ? image($setting->slider_image2) : '' }}" id="sliderImage2Preview" style="max-height: 120px;" class="rounded shadow-sm img-fluid">
                                    </div>
                                </label>
                                <input type="file" name="slider_image2" class="d-none" id="sliderImage2Input" accept="image/*">
                            </div>
                        </div>
                        <div class="col-12 mt-3">
                            <label class="form-label">Slider Image 3</label>
                            <div class="upload-image-wrapper">
                                <label for="sliderImage3Input" class="upload-image-box border-dashed rounded text-center p-3 cursor-pointer d-block">
                                    <div id="sliderImage3Placeholder" class="{{ $setting->slider_image3 ? 'd-none' : '' }}">
                                        <iconify-icon icon="solar:camera-add-outline" class="text-4xl text-secondary-light mb-2"></iconify-icon>
                                        <p class="text-secondary-light mb-0 text-sm">Upload Slider Image 3</p>
                                    </div>
                                    <div id="sliderImage3PreviewContainer" class="{{ $setting->slider_image3 ? '' : 'd-none' }} position-relative">
                                        <img src="{{ $setting->slider_image3 ? image($setting->slider_image3) : '' }}" id="sliderImage3Preview" style="max-height: 120px;" class="rounded shadow-sm img-fluid">
                                    </div>
                                </label>
                                <input type="file" name="slider_image3" class="d-none" id="sliderImage3Input" accept="image/*">
                            </div>
                        </div>
                        <div class="col-12 mt-3">
                            <label class="form-label">Popup Image</label>
                            <div class="upload-image-wrapper">
                                <label for="popupImageInput" class="upload-image-box border-dashed rounded text-center p-3 cursor-pointer d-block">
                                    <div id="popupImagePlaceholder" class="{{ $setting->popup_image ? 'd-none' : '' }}">
                                        <iconify-icon icon="solar:camera-add-outline" class="text-4xl text-secondary-light mb-2"></iconify-icon>
                                        <p class="text-secondary-light mb-0 text-sm">Upload Popup Image</p>
                                    </div>
                                    <div id="popupImagePreviewContainer" class="{{ $setting->popup_image ? '' : 'd-none' }} position-relative">
                                        <img src="{{ $setting->popup_image ? image($setting->popup_image) : '' }}" id="popupImagePreview" style="max-height: 120px;" class="rounded shadow-sm img-fluid">
                                    </div>
                                </label>
                                <input type="file" name="popup_image" class="d-none" id="popupImageInput" accept="image/*">
                            </div> 
                        </div>


                        {{-- Hero Video --}}
                        {{-- <div class="col-12 mt-3">
                            <label class="form-label">Hero Section Video</label>
                            <div class="upload-image-wrapper">
                                <label for="videoInput" class="upload-image-box border-dashed rounded text-center p-3 cursor-pointer d-block">
                                    <div id="videoPlaceholder" class="{{ $setting->display_video ? 'd-none' : '' }}">
                                        <iconify-icon icon="solar:videocamera-record-outline" class="text-4xl text-secondary-light mb-2"></iconify-icon>
                                        <p class="text-secondary-light mb-0 text-sm">Upload Video</p>
                                    </div>
                                    <div id="videoPreviewContainer" class="{{ $setting->display_video ? '' : 'd-none' }} position-relative">
                                        <video id="videoPreview" src="{{ $setting->display_video ? asset($setting->display_video) : '' }}" controls style="max-width: 100%; max-height: 200px;" class="rounded shadow-sm"></video>
                                    </div>
                                </label>
                                <input type="file" name="display_video" class="d-none" id="videoInput" accept="video/*">
                            </div>
                            <small class="text-secondary-light">Max size: 20MB. Formats: MP4, MOV, OGG, QT</small>
                        </div> --}}
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 mb-2">Update Settings</button>
                    <a href="{{ route('website-setting.website-settings.index') }}" class="btn btn-outline-secondary w-100">Cancel</a>
                </div>
            </div>
        </div>
    </div>
  </form>

<style>
    .upload-image-box {
        background-color: #f8f9fa;
        border: 2px dashed #dee2e6;
        transition: all 0.3s ease;
    }
    .upload-image-box:hover {
        border-color: #0d6efd;
        background-color: #f0f7ff;
    }
    .cursor-pointer {
        cursor: pointer;
    }
</style>
@endsection

@section('script')
<script>
    function setupImagePreview(inputId, previewId, containerId, placeholderId) {
        $(`#${inputId}`).on('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $(`#${previewId}`).attr('src', e.target.result);
                    $(`#${containerId}`).removeClass('d-none');
                    $(`#${placeholderId}`).addClass('d-none');
                }
                reader.readAsDataURL(file);
            }
        });
    }

    setupImagePreview('logoInput', 'logoPreview', 'logoPreviewContainer', 'logoPlaceholder');
    setupImagePreview('faviconInput', 'faviconPreview', 'faviconPreviewContainer', 'faviconPlaceholder');
    setupImagePreview('merchantQrInput', 'merchantQrPreview', 'merchantQrPreviewContainer', 'merchantQrPlaceholder');
    setupImagePreview('sliderImage1Input', 'sliderImage1Preview', 'sliderImage1PreviewContainer', 'sliderImage1Placeholder');
    setupImagePreview('sliderImage2Input', 'sliderImage2Preview', 'sliderImage2PreviewContainer', 'sliderImage2Placeholder');
    setupImagePreview('sliderImage3Input', 'sliderImage3Preview', 'sliderImage3PreviewContainer', 'sliderImage3Placeholder');
    setupImagePreview('popupImageInput', 'popupImagePreview', 'popupImagePreviewContainer', 'popupImagePlaceholder'); 

    $('#videoInput').on('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#videoPreview').attr('src', e.target.result);
                $('#videoPreviewContainer').removeClass('d-none');
                $('#videoPlaceholder').addClass('d-none');
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection
