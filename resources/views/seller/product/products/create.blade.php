@extends('seller.layouts.app')

<!-- Styles for the toggle switch -->
<style>
    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 30px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: 0.4s;
        border-radius: 15px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 22px;
        width: 22px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: 0.4s;
        border-radius: 50%;
    }

    input:checked + .slider {
        background-color: #4CAF50;
    }

    input:checked + .slider:before {
        transform: translateX(30px);
    }
</style>
@section('panel_content')
    <div class="page-content mx-0">
        <div class="aiz-titlebar mt-2 mb-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="h3">{{ translate('Add Your Product') }}</h1>
                </div>
                <div class="col text-right">
                    <a class="btn btn-xs btn-soft-primary" href="javascript:void(0);" onclick="clearTempdata()">
                        {{ translate('Clear Tempdata') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Error Meassages -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Data type -->
        <input type="hidden" id="data_type" value="physical">

        <form class="" action="{{ route('seller.products.store') }}" method="POST" enctype="multipart/form-data" id="choice_form">
            <div class="row gutters-5">
                <div class="col-lg-8">
                    @csrf
                    <input type="hidden" name="added_by" value="seller">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{ translate('Product Information') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row" style="display: none;">
    <label class="col-md-3 col-from-label">{{ translate('Product Name') }} <span class="text-danger">*</span></label>
    <div class="col-md-8">
        <input type="text" class="form-control" id="product-name" name="name"
               placeholder="{{ translate('Product Name') }}" readonly required>
    </div>
</div>

<script>
    function generateRandomString(length) {
        var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        var result = '';
        for (var i = 0; i < length; i++) {
            result += characters.charAt(Math.floor(Math.random() * characters.length));
        }
        return result;
    }

    document.addEventListener('DOMContentLoaded', function() {
        var nameField = document.getElementById('product-name');
        nameField.value = generateRandomString(8); // Set a random string as the dummy value
    });

    document.querySelector('form').addEventListener('submit', function(e) {
        var nameField = document.getElementById('product-name');
        // Ensure the field has a random value before submission
        if (!nameField.value) {
            nameField.value = generateRandomString(8);
        }
    });
</script>

                          <!--
                            <div class="form-group row" id="brand">
                                <label class="col-md-3 col-from-label">{{ translate('Brand') }}</label>
                                <div class="col-md-8">
                                    <select class="form-control aiz-selectpicker" name="brand_id" id="brand_id"
                                        data-live-search="true">
                                        <option value="">{{ translate('Select Brand') }}</option>
                                        @foreach (\App\Models\Brand::all() as $brand)
                                            <option value="{{ $brand->id }}">{{ $brand->getTranslation('name') }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
-->

                         <div class="form-group row" style="display: none;">
    <label class="col-md-3 col-from-label">{{ translate('Unit') }}</label>
    <div class="col-md-8">
        <!-- Read-only input field -->
        <input type="text" class="form-control" name="unit" value="Pc" placeholder="piece" readonly>
        <small class="form-text text-muted">This field is fixed to "piece".</small>
    </div>
</div>



                          <!--
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{ translate('Weight') }}
                                    <small>({{ translate('In Kg') }})</small></label>
                                <div class="col-md-8">
                                    <input type="number" class="form-control" name="weight" step="0.01" value="0.00"
                                        placeholder="0.00">
                                </div>
                            </div>
-->

<div class="form-group row">
    <label class="col-md-3 col-from-label">
        {{ translate('Minimum Purchase Qty') }} <span class="text-danger">*</span>
    </label>
    <div class="col-md-8">
        <!-- Toggle button for Retail/Wholesale -->
        <div class="d-flex align-items-center mb-3">
            <label class="switch">
                <input type="checkbox" id="purchase_type_toggle">
                <span class="slider round"></span>
            </label>
            <span class="ml-2" id="toggle_label">Retail</span>
        </div>

        <!-- Input field for Minimum Purchase Quantity -->
        <div id="min_qty_wrapper" style="display: none;">
            <input 
                type="number" 
                lang="en" 
                class="form-control" 
                id="min_qty" 
                name="min_qty" 
                value="2" 
                min="2" 
                required>
          <small id="min_qty_hint" class="form-text text-muted">Minimum quantity is 2</small>
        </div>
    </div>
</div>
                          <div class="form-group row" style="display: none;">
                            <label class="col-xxl-3 col-from-label fs-13">{{translate('Tags')}}</label>
                            <div class="col-xxl-9">
                              <input type="text" class="form-control aiz-tag-input" name="tags[]" id="tags" value="" placeholder="{{ translate('Type to add a tag') }}" data-role="tagsinput">
                              <small class="text-muted">{{translate('This is used for search. Input those words by which cutomer can find this product.')}}</small>
                            </div>
                          </div>
<div class="form-group row">
  <!-- fabric -->
  <label class="col-xxl-3 col-from-label fs-13" style="margin-top: 25px;">{{translate('Fabric')}}</label>
  <div class="col-xxl-9" style="margin-top: 25px;">
    <input type="text" class="form-control" name="fabric" placeholder="{{ translate('Type to add a fabric') }}">
  </div>
</div>

                            @if (addon_is_activated('pos_system'))
                                <div class="form-group row">
                                    <label class="col-md-3 col-from-label">{{ translate('Barcode') }}</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" name="barcode"
                                            placeholder="{{ translate('Barcode') }}">
                                    </div>
                                </div>
                            @endif
                            @if (addon_is_activated('refund_request'))
                                <div class="form-group row">
                                    <label class="col-md-3 col-from-label">{{ translate('Refundable') }}</label>
                                    <div class="col-md-8">
                                        <label class="aiz-switch aiz-switch-success mb-0">
                                            <input type="checkbox" name="refundable" checked value="1">
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{ translate('Product Images') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label" for="signinSrEmail">{{ translate('Gallery Images') }}</label>
                                <div class="col-md-8">
                                    <div class="input-group" data-toggle="aizuploader" data-type="image"
                                        data-multiple="true">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text bg-soft-secondary font-weight-medium">
                                                {{ translate('Browse') }}</div>
                                        </div>
                                        <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                        <input type="hidden" name="photos" class="selected-files">
                                    </div>
                                    <div class="file-preview box sm">
                                    </div>
                                    <small class="text-muted">{{translate('These images are visible in product details page gallery. Minimum dimensions required: 900px width X 900px height.')}}</small>
                                </div>
                            </div>
                            <!-- Keep the thumbnail section but hide it -->
<div class="form-group row" style="display: none;" id="thumbnail-image">
    <label class="col-md-3 col-form-label" for="signinSrEmail">{{ translate('Thumbnail Image') }}</label>
    <div class="col-md-8">
        <div class="input-group" data-toggle="aizuploader" data-type="image">
            <div class="input-group-prepend">
                <div class="input-group-text bg-soft-secondary font-weight-medium">
                    {{ translate('Browse') }}
                </div>
            </div>
            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
            <input type="hidden" name="thumbnail_img" class="selected-files">
        </div>
        <div class="file-preview box sm"></div>
        <small class="text-muted">{{ translate("This image is visible in all product box. Minimum dimensions required: 195px width X 195px height.") }}</small>
    </div>
</div>

                        </div>
                    </div>
                  <!--
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{ translate('Product Videos') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{ translate('Video Provider') }}</label>
                                <div class="col-md-8">
                                    <select class="form-control aiz-selectpicker" name="video_provider" id="video_provider">
                                        <option value="youtube">{{ translate('Youtube') }}</option>
                                        <option value="dailymotion">{{ translate('Dailymotion') }}</option>
                                        <option value="vimeo">{{ translate('Vimeo') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{ translate('Video Link') }}</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="video_link"
                                        placeholder="{{ translate('Video Link') }}">
                                </div>
                            </div>
                        </div>
                    </div>
-->

                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{ translate('Product Variation') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row" style="display: none;">
                                <div class="col-md-3">
                                    <input type="text" class="form-control" value="{{ translate('Colors') }}" disabled>
                                </div>
                                <div class="col-md-8">
                                    <select class="form-control aiz-selectpicker" data-live-search="true" name="colors[]"
                                        data-selected-text-format="count" id="colors" multiple disabled>
                                        @foreach (\App\Models\Color::orderBy('name', 'asc')->get() as $key => $color)
                                            <option value="{{ $color->code }}"
                                                data-content="<span><span class='size-15px d-inline-block mr-2 rounded border' style='background:{{ $color->code }}'></span><span>{{ $color->name }}</span></span>">
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <label class="aiz-switch aiz-switch-success mb-0">
                                        <input value="1" type="checkbox" name="colors_active">
                                        <span></span>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-3">
                                    <input type="text" class="form-control" value="{{ translate('Attributes') }}"
                                        disabled>
                                </div>
                                <div class="col-md-8">
                                    <select name="choice_attributes[]" id="choice_attributes"
                                        class="form-control aiz-selectpicker" data-live-search="true"
                                        data-selected-text-format="count" multiple
                                        data-placeholder="{{ translate('Choose Attributes') }}">
                                        @foreach (\App\Models\Attribute::all() as $key => $attribute)
                                            <option value="{{ $attribute->id }}">{{ $attribute->getTranslation('name') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div>
                                <p>{{ translate('Choose the attributes of this product and then input values of each attribute') }}
                                </p>
                                <br>
                            </div>

                            <div class="customer_choice_options" id="customer_choice_options">

                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{ translate('Product price + stock') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{ translate('Unit price') }} <span class="text-danger">*</span></label>
                                <div class="col-md-6">
                                    <input type="number" lang="en" min="0" value="0" step="0.01"
                                        placeholder="{{ translate('Unit price') }}" name="unit_price" class="form-control"
                                        required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 control-label"
                                    for="start_date">{{ translate('Discount Date Range') }} </label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control aiz-date-range" name="date_range"
                                        placeholder="{{ translate('Select Date') }}" data-time-picker="true"
                                        data-format="DD-MM-Y HH:mm:ss" data-separator=" to " autocomplete="off">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{ translate('Discount') }} <span class="text-danger">*</span></label>
                                <div class="col-md-6">
                                    <input type="number" lang="en" min="0" value="0" step="0.01"
                                        placeholder="{{ translate('Discount') }}" name="discount" class="form-control"
                                        required>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-control aiz-selectpicker" name="discount_type">
                                        <option value="amount">{{ translate('Flat') }}</option>
                                        <option value="percent">{{ translate('Percent') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div id="show-hide-div">
                                <div class="form-group row">
                                    <label class="col-md-3 col-from-label">{{ translate('Quantity') }} <span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <input type="number" lang="en" min="0" value="0" step="1"
                                            placeholder="{{ translate('Quantity') }}" name="current_stock"
                                            class="form-control" required>
                                    </div>
                                </div>
                              
                                <div class="form-group row">
                                  <!--
                                    <label class="col-md-3 col-from-label">
                                        {{ translate('SKU') }}
                                    </label>
                                    <div class="col-md-6">
                                        <input type="text" placeholder="{{ translate('SKU') }}" name="sku"
                                            class="form-control">
                                    </div>
-->
                                </div>
                            </div>
                          <!--
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">
                                    {{ translate('External link') }}
                                </label>
                                <div class="col-md-9">
                                    <input type="text" placeholder="{{ translate('External link') }}"
                                        name="external_link" class="form-control">
                                    <small
                                        class="text-muted">{{ translate('Leave it blank if you do not use external site link') }}</small>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">
                                    {{ translate('External link button text') }}
                                </label>
                                <div class="col-md-9">
                                    <input type="text" placeholder="{{ translate('External link button text') }}"
                                        name="external_link_btn" class="form-control">
                                    <small
                                        class="text-muted">{{ translate('Leave it blank if you do not use external site link') }}</small>
                                </div>
                            </div>
-->

                            <br>
                            <div class="sku_combination" id="sku_combination">

                            </div>
                        </div>
                    </div>
                    <div class="card" style="display: none;">
    <div class="card-header">
        <h5 class="mb-0 h6">{{ translate('Product Description') }}</h5>
    </div>
    <div class="card-body">
        <div class="form-group row">
            <label class="col-md-3 col-from-label">{{ translate('Description') }}</label>
            <div class="col-md-8">
                <textarea class="aiz-text-editor" name="description"></textarea>
            </div>
        </div>
    </div>
</div>



<div class="card" style="display: none;">
    <div class="card-header">
        <h5 class="mb-0 h6">{{ translate('PDF Specification') }}</h5>
    </div>
    <div class="card-body">
        <div class="form-group row" style="display: none;">
            <label class="col-md-3 col-form-label" for="signinSrEmail">{{ translate('PDF Specification') }}</label>
            <div class="col-md-8">
                <div class="input-group" data-toggle="aizuploader" data-type="document">
                    <div class="input-group-prepend">
                        <div class="input-group-text bg-soft-secondary font-weight-medium">
                            {{ translate('Browse') }}</div>
                    </div>
                    <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                    <input type="hidden" name="pdf" class="selected-files">
                </div>
                <div class="file-preview box sm">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card" style="display: none;">
    <div class="card-header">
        <h5 class="mb-0 h6">{{ translate('SEO Meta Tags') }}</h5>
    </div>

    <div class="card-body">
        <div class="form-group row" style="display: none;">
            <label class="col-md-3 col-from-label">{{ translate('Meta Title') }}</label>
            <div class="col-md-8">
                <input type="text" class="form-control" name="meta_title"
                    placeholder="{{ translate('Meta Title') }}">
            </div>
        </div>
        <div class="form-group row" style="display: none;">
            <label class="col-md-3 col-from-label">{{ translate('Description') }}</label>
            <div class="col-md-8">
                <textarea name="meta_description" rows="8" class="form-control"></textarea>
            </div>
        </div>
        <div class="form-group row" style="display: none;">
            <label class="col-md-3 col-form-label" for="signinSrEmail">{{ translate('Meta Image') }}</label>
            <div class="col-md-8">
                <div class="input-group" data-toggle="aizuploader" data-type="image">
                    <div class="input-group-prepend">
                        <div class="input-group-text bg-soft-secondary font-weight-medium">
                            {{ translate('Browse') }}</div>
                    </div>
                    <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                    <input type="hidden" name="meta_img" class="selected-files">
                </div>
                <div class="file-preview box sm">
                </div>
            </div>
        </div>
    </div>
</div>


                    {{-- Frequently Bought Products --}}
<div class="card" style="display: none;">
                      
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{ translate('Frequently Bought') }}</h5>
                        </div>
                      
                      
                        <div class="w-100">
                          
                            <div class="d-flex my-3">
                                <div class="align-items-center d-flex mar-btm ml-4 mr-5 radio">
                                    <input id="fq_bought_select_products" type="radio" name="frequently_bought_selection_type" value="product" onchange="fq_bought_product_selection_type()" checked >
                                    <label for="fq_bought_select_products" class="fs-14 fw-500 mb-0 ml-2">{{translate('Select Product')}}</label>
                                </div>
                                <div class="radio mar-btm mr-3 d-flex align-items-center">
                                    <input id="fq_bought_select_category" type="radio" name="frequently_bought_selection_type" value="category" onchange="fq_bought_product_selection_type()">
                                    <label for="fq_bought_select_category" class="fs-14 fw-500 mb-0 ml-2">{{translate('Select Category')}}</label>
                                </div>
                            </div>

                            <div class="px-3 px-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="fq_bought_select_product_div">

                                            <div id="selected-fq-bought-products">

                                            </div>

                                            <button
                                                type="button"
                                                class="btn btn-block border border-dashed hov-bg-soft-secondary fs-14 rounded-0 d-flex align-items-center justify-content-center"
                                                onclick="showFqBoughtProductModal()">
                                                <i class="las la-plus"></i>
                                                <span class="ml-2">{{ translate('Add More') }}</span>
                                            </button>

                                        </div>

                                        {{-- Select Category for Frequently Bought Product --}}
                                        <div class="fq_bought_select_category_div d-none">
                                            <div class="form-group row">
                                                <label class="col-md-2 col-from-label">{{translate('Category')}}</label>
                                                <div class="col-md-10">
                                                    <select class="form-control aiz-selectpicker" data-placeholder="{{ translate('Select a Category')}}" name="fq_bought_product_category_id" data-live-search="true" required>
                                                        @foreach ($categories as $category)
                                                            <option value="{{ $category->id }}">{{ $category->getTranslation('name') }}</option>
                                                            @foreach ($category->childrenCategories as $childCategory)
                                                                @include('categories.child_category', ['child_category' => $childCategory])
                                                            @endforeach
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

<div class="col-lg-4">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Product Category') }}</h5>
            <h6 class="float-right fs-13 mb-0">
                {{ translate('Select Main') }}
                <span class="position-relative main-category-info-icon">
                    <i class="las la-question-circle fs-18 text-info"></i>
                    <span class="main-category-info bg-soft-info p-2 position-absolute d-none border">
                        {{ translate('This will be used for commission based calculations and homepage category wise product Show.') }}
                    </span>
                </span>
            </h6>
        </div>
        <div class="card-body">
            <ul class="list-unstyled">
                @foreach ($categories as $category)
                    <li>
                        <h6>
                            <!-- Parent category with an arrow -->
                            <span class="toggle-arrow" data-toggle="collapse" data-target="#sub-category-{{ $category->id }}">
                                <i class="las la-arrow-down"></i>
                            </span>
                            {{ $category->name }}
                        </h6>

                        <!-- Subcategories with collapse functionality -->
                        @if ($category->childrenCategories && count($category->childrenCategories) > 0)
                            <ul id="sub-category-{{ $category->id }}" class="collapse">
                                @foreach ($category->childrenCategories as $childCategory)
                                    <li>
                                        <input type="radio" name="category_id" value="{{ $childCategory->id }}" data-parent="{{ $category->id }}">
                                        {{ $childCategory->name }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach
            </ul>

            <!-- Hidden input to store the parent category -->
            <input type="hidden" id="parent_category" name="parent_category" value="">
        </div>
    </div>
</div>


<!-- Add some CSS for the collapsed list -->
<style>
    .collapse {
        display: none;
    }

    .toggle-arrow {
        cursor: pointer;
        margin-right: 10px;
    }
</style>

                        <!--<div class="card-body">
                            <div class="h-300px overflow-auto c-scrollbar-light">
                                <ul class="hummingbird-treeview-converter list-unstyled" data-checkbox-name="category_ids[]" data-radio-name="category_id">
                                    @foreach ($categories as $category)
                                    <li id="{{ $category->id }}">{{ $category->getTranslation('name') }}</li>
                                        @foreach ($category->childrenCategories as $childCategory)
                                            @include('backend.product.products.child_category', ['child_category' => $childCategory])
                                        @endforeach
                                    @endforeach
                                </ul>
                            </div>
                        </div>-->
                      
                    </div>

                    <div class="card"style="display: none;">
                        <div class="card-header">
                            <h5 class="mb-0 h6">
                                {{ translate('Shipping Configuration') }}
                            </h5>
                        </div>

                        <div class="card-body" style="display: none">
                            @if (get_setting('shipping_type') == 'product_wise_shipping')
                                <div class="form-group row">
                                    <label class="col-md-6 col-from-label">{{ translate('Free Shipping') }}</label>
                                    <div class="col-md-6">
                                        <label class="aiz-switch aiz-switch-success mb-0">
                                            <input type="radio" name="shipping_type" value="free" checked>
                                            <span></span>
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-6 col-from-label">{{ translate('Flat Rate') }}</label>
                                    <div class="col-md-6">
                                        <label class="aiz-switch aiz-switch-success mb-0">
                                            <input type="radio" name="shipping_type" value="flat_rate">
                                            <span></span>
                                        </label>
                                    </div>
                                </div>

                                <div class="flat_rate_shipping_div" style="display: none">
                                    <div class="form-group row">
                                        <label class="col-md-6 col-from-label">{{ translate('Shipping cost') }}</label>
                                        <div class="col-md-6">
                                            <input type="number" lang="en" min="0" value="0"
                                                step="0.01" placeholder="{{ translate('Shipping cost') }}"
                                                name="flat_shipping_cost" class="form-control" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row" >
                                    <label class="col-md-6 col-from-label">{{translate('Is Product Quantity Mulitiply')}}</label>
                                    <div class="col-md-6">
                                        <label class="aiz-switch aiz-switch-success mb-0">
                                            <input type="checkbox" name="is_quantity_multiplied" value="1">
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                            @else
                                <p>
                                    {{ translate('Shipping configuration is maintained by Admin.') }}
                                </p>
                            @endif
                        </div>
                    </div>
<!--
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{ translate('Low Stock Quantity Warning') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-3">
                                <label for="name">
                                    {{ translate('Quantity') }}
                                </label>
                                <input type="number" name="low_stock_quantity" value="1" min="0"
                                    step="1" class="form-control">
                            </div>
                        </div>
                    </div>
-->
                    <div class="card" style="display: none">
                        <div class="card-header">
                            <h5 class="mb-0 h6">
                                {{ translate('Stock Visibility State') }}
                            </h5>
                        </div>

                        <div class="card-body">

                            <div class="form-group row">
                                <label class="col-md-6 col-from-label">{{ translate('Show Stock Quantity') }}</label>
                                <div class="col-md-6">
                                    <label class="aiz-switch aiz-switch-success mb-0">
                                        <input type="radio" name="stock_visibility_state" value="quantity" checked>
                                        <span></span>
                                    </label>
                                </div>
                            </div>
<!--
                            <div class="form-group row">
                                <label class="col-md-6 col-from-label">{{ translate('Show Stock With Text Only') }}</label>
                                <div class="col-md-6">
                                    <label class="aiz-switch aiz-switch-success mb-0">
                                        <input type="radio" name="stock_visibility_state" value="text">
                                        <span></span>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-6 col-from-label">{{ translate('Hide Stock') }}</label>
                                <div class="col-md-6">
                                    <label class="aiz-switch aiz-switch-success mb-0">
                                        <input type="radio" name="stock_visibility_state" value="hide">
                                        <span></span>
                                    </label>
                                </div>
                            </div>
-->
                        </div>
                    </div>

                    <div class="card" style ="display: none">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{ translate('Cash On Delivery') }}</h5>
                        </div>
                        <div class="card-body">
                            @if (get_setting('cash_payment') == '1')
                                <div class="form-group row">
                                    <label class="col-md-6 col-from-label">{{ translate('Status') }}</label>
                                    <div class="col-md-6">
                                        <label class="aiz-switch aiz-switch-success mb-0">
                                            <input type="checkbox" name="cash_on_delivery" value="1" checked="">
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                            @else
                                <p>
                                    {{ translate('Cash On Delivery activation is maintained by Admin.') }}
                                </p>
                            @endif
                        </div>
                    </div>
<!--
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{ translate('Estimate Shipping Time') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-3">
                                <label for="name">
                                    {{ translate('Shipping Days') }}
                                </label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="est_shipping_days" min="1"
                                        step="1" placeholder="{{ translate('Shipping Days') }}">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroupPrepend">{{ translate('Days') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
-->
                    <div class="card" style="display: none">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{ translate('VAT & Tax') }}</h5>
                        </div>
                        <div class="card-body">
                            @foreach (\App\Models\Tax::where('tax_status', 1)->get() as $tax)
                                <label for="name">
                                    {{ $tax->name }}
                                    <input type="hidden" value="{{ $tax->id }}" name="tax_id[]">
                                </label>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <input type="number" lang="en" min="0" value="0" step="0.01"
                                            placeholder="{{ translate('Tax') }}" name="tax[]" class="form-control"
                                            required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <select class="form-control aiz-selectpicker" name="tax_type[]">
                                            <option value="amount">{{ translate('Flat') }}</option>
                                            <option value="percent">{{ translate('Percent') }}</option>
                                        </select>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="mar-all text-right mb-2">
                        <button type="submit" name="button" value="publish"
                            class="btn btn-primary">{{ translate('Upload Product') }}</button>
                    </div>
                </div>
            </div>

        </form>
    </div>
@endsection

@section('modal')
	<!-- Frequently Bought Product Select Modal -->
    @include('modals.product_select_modal')
@endsection

@section('script')
<!-- Treeview js -->
<script src="{{ static_asset('assets/js/hummingbird-treeview.js') }}"></script>

<script type="text/javascript">
  
  $(document).ready(function() {
    $("input[name='category_id']").on("change", function() {
        let parentId = $(this).data("parent"); // Get parent category ID

        // Set the parent category in the hidden input
        $("#parent_category").val(parentId);
    });
});
  
  
  
    /*$(document).ready(function() {
        $("#treeview").hummingbird();

        $('#treeview input:checkbox').on("click", function (){
            let $this = $(this);
            if ($this.prop('checked') && ($('#treeview input:radio:checked').length == 0)) {
                let val = $this.val();
                $('#treeview input:radio[value='+val+']').prop('checked',true);
            }
        });
    });*/

    $("[name=shipping_type]").on("change", function() {
        $(".product_wise_shipping_div").hide();
        $(".flat_rate_shipping_div").hide();
        if ($(this).val() == 'product_wise') {
            $(".product_wise_shipping_div").show();
        }
        if ($(this).val() == 'flat_rate') {
            $(".flat_rate_shipping_div").show();
        }

    });

    function add_more_customer_choice_option(i, name) {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "POST",
            url: '{{ route('seller.products.add-more-choice-option') }}',
            data: {
                attribute_id: i
            },
            success: function(data) {
                var obj = JSON.parse(data);
                $('#customer_choice_options').append('\
                    <div class="form-group row">\
                        <div class="col-md-3">\
                            <input type="hidden" name="choice_no[]" value="' + i + '">\
                            <input type="text" class="form-control" name="choice[]" value="' + name +
                    '" placeholder="{{ translate('Choice Title') }}" readonly>\
                        </div>\
                        <div class="col-md-8">\
                            <select class="form-control aiz-selectpicker attribute_choice" data-live-search="true" name="choice_options_' + i + '[]" multiple>\
                                ' + obj + '\
                            </select>\
                        </div>\
                    </div>');
                AIZ.plugins.bootstrapSelect('refresh');
            }
        });


    }

    $('input[name="colors_active"]').on('change', function() {
        if (!$('input[name="colors_active"]').is(':checked')) {
            $('#colors').prop('disabled', true);
            AIZ.plugins.bootstrapSelect('refresh');
        } else {
            $('#colors').prop('disabled', false);
            AIZ.plugins.bootstrapSelect('refresh');
        }
        update_sku();
    });

    $(document).on("change", ".attribute_choice", function() {
        update_sku();
    });

    $('#colors').on('change', function() {
            update_sku();
        });

    $('input[name="unit_price"]').on('keyup', function() {
        update_sku();
    });

    // $('input[name="name"]').on('keyup', function() {
    //     update_sku();
    // });

    function delete_row(em) {
        $(em).closest('.form-group row').remove();
        update_sku();
    }

    function delete_variant(em) {
        $(em).closest('.variant').remove();
    }

    function update_sku() {
        $.ajax({
            type: "POST",
            url: '{{ route('seller.products.sku_combination') }}',
            data: $('#choice_form').serialize(),
            success: function(data) {
                $('#sku_combination').html(data);
                AIZ.uploader.previewGenerate();
                AIZ.plugins.sectionFooTable('#sku_combination');
                if (data.trim().length > 1) {
                    $('#show-hide-div').hide();
                } else {
                    $('#show-hide-div').show();
                }
            }
        });
    }

    $('#choice_attributes').on('change', function() {
        $('#customer_choice_options').html(null);
        $.each($("#choice_attributes option:selected"), function() {
            add_more_customer_choice_option($(this).val(), $(this).text());
        });
        update_sku();
    });

    function fq_bought_product_selection_type(){
        var productSelectionType = $("input[name='frequently_bought_selection_type']:checked").val();
        if(productSelectionType == 'product'){
            $('.fq_bought_select_product_div').removeClass('d-none');
            $('.fq_bought_select_category_div').addClass('d-none');
        }
        else if(productSelectionType == 'category'){
            $('.fq_bought_select_category_div').removeClass('d-none');
            $('.fq_bought_select_product_div').addClass('d-none');
        }
    }

    function showFqBoughtProductModal() {
        $('#fq-bought-product-select-modal').modal('show', {backdrop: 'static'});
    }

    function filterFqBoughtProduct() {
        var searchKey = $('input[name=search_keyword]').val();
        var fqBroughCategory = $('select[name=fq_brough_category]').val();
        $.post('{{ route('seller.product.search') }}', { _token: AIZ.data.csrf, product_id: null, search_key:searchKey, category:fqBroughCategory, product_type:"physical" }, function(data){
            $('#product-list').html(data);
            AIZ.plugins.sectionFooTable('#product-list');
        });
    }

    function addFqBoughtProduct() {
        var selectedProducts = [];
        $("input:checkbox[name=fq_bought_product_id]:checked").each(function() {
            selectedProducts.push($(this).val());
        });

        var fqBoughtProductIds = [];
        $("input[name='fq_bought_product_ids[]']").each(function() {
            fqBoughtProductIds.push($(this).val());
        });

        var productIds = selectedProducts.concat(fqBoughtProductIds.filter((item) => selectedProducts.indexOf(item) < 0))

        $.post('{{ route('seller.get-selected-products') }}', { _token: AIZ.data.csrf, product_ids:productIds}, function(data){
            $('#fq-bought-product-select-modal').modal('hide');
            $('#selected-fq-bought-products').html(data);
            AIZ.plugins.sectionFooTable('#selected-fq-bought-products');
        });
    }

    
    
    document.addEventListener('DOMContentLoaded', function () {
        const toggle = document.getElementById('purchase_type_toggle');
        const minQtyWrapper = document.getElementById('min_qty_wrapper');
        const minQtyInput = document.getElementById('min_qty');
        const toggleLabel = document.getElementById('toggle_label');
    	const minQtyHint = document.getElementById('min_qty_hint'); // Reference to the hint text

        toggle.addEventListener('change', function () {
            if (toggle.checked) {
                // Wholesale mode
                toggleLabel.textContent = 'Wholesale';
                minQtyWrapper.style.display = 'block'; // Show input field
                minQtyInput.value = 2; // Default value for Wholesale
                minQtyInput.min = 2;   // Minimum value for Wholesale
                minQtyHint.style.display = 'block'; // Show hint text

            } else {
                // Retail mode
                toggleLabel.textContent = 'Retail';
                minQtyWrapper.style.display = 'none'; // Hide input field
                minQtyInput.value = 1; // Default value for Retail
                minQtyInput.min = 1;   // Minimum value for Retail
                minQtyHint.style.display = 'none'; // Hide hint text

            }
        });

        // Set default state (Retail)
        toggle.checked = false; // Ensure toggle starts in the "Retail" position
        toggle.dispatchEvent(new Event('change')); // Trigger initial state setup
    });
</script>



<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to sync thumbnail with first gallery image
    function syncThumbnailWithGallery() {
        const galleryInput = document.querySelector('input[name="photos"]');
        const thumbnailInput = document.querySelector('input[name="thumbnail_img"]');
        
        if (galleryInput && thumbnailInput) {
            const galleryValue = galleryInput.value;
            if (galleryValue) {
                // Split the comma-separated file IDs and get the first one
                const fileIds = galleryValue.split(',');
                if (fileIds.length > 0 && fileIds[0].trim()) {
                    thumbnailInput.value = fileIds[0].trim();
                    
                    // Trigger the thumbnail preview update
                    const thumbnailWrapper = thumbnailInput.closest('.input-group').parentNode;
                    const previewBox = thumbnailWrapper.querySelector('.file-preview.box.sm');
                    
                    // Update thumbnail preview (you may need to adjust this based on your aizuploader implementation)
                    if (typeof AIZ !== 'undefined' && AIZ.uploader && AIZ.uploader.previewGenerate) {
                        AIZ.uploader.previewGenerate();
                    }
                }
            }
        }
    }

    // Watch for changes in the gallery input
    const galleryInput = document.querySelector('input[name="photos"]');
    if (galleryInput) {
        // Use MutationObserver to watch for value changes
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
                    syncThumbnailWithGallery();
                }
            });
        });

        observer.observe(galleryInput, {
            attributes: true,
            attributeFilter: ['value']
        });

        // Also listen for input events
        galleryInput.addEventListener('input', syncThumbnailWithGallery);
        galleryInput.addEventListener('change', syncThumbnailWithGallery);
    }

    // Alternative approach: Listen for aizuploader events if available
    $(document).on('DOMNodeInserted', '.file-preview', function() {
        const galleryPreview = document.querySelector('input[name="photos"]').parentNode.parentNode.querySelector('.file-preview');
        const thumbnailGalleryPreview = this.parentNode.parentNode.querySelector('input[name="photos"]');
        
        if (thumbnailGalleryPreview) {
            setTimeout(syncThumbnailWithGallery, 100);
        }
    });

    // If using jQuery and aizuploader has custom events
    if (typeof $ !== 'undefined') {
        $(document).on('change', 'input[name="photos"]', function() {
            setTimeout(syncThumbnailWithGallery, 100);
        });
        
        // Listen for aizuploader completion
        $(document).on('aizuploader.complete', '[data-toggle="aizuploader"]', function() {
            if ($(this).find('input[name="photos"]').length > 0) {
                setTimeout(syncThumbnailWithGallery, 200);
            }
        });
    }
});
</script>


<!-- Treeview js -->
<script src="{{ static_asset('assets/js/hummingbird-treeview.js') }}"></script>

<script type="text/javascript">
$(document).ready(function() {
    // Category selection handler
    $("input[name='category_id']").on("change", function() {
        let parentId = $(this).data("parent");
        $("#parent_category").val(parentId);
    });
    
    // Category toggle functionality
    $('.toggle-arrow').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const targetSelector = $(this).attr('data-target');
        const $subCategory = $(targetSelector);
        const $arrow = $(this);
        
        if ($subCategory.length) {
            if ($subCategory.hasClass('collapse') || $subCategory.is(':hidden')) {
                $subCategory.removeClass('collapse').show();
                $arrow.html('<i class="las la-arrow-up"></i>');
            } else {
                $subCategory.addClass('collapse').hide();
                $arrow.html('<i class="las la-arrow-down"></i>');
            }
        }
    });
    
    // Prevent clicks inside subcategories from bubbling up
    $('ul[id^="sub-category-"]').on('click', function(e) {
        e.stopPropagation();
    });
    
    // Prevent radio button clicks from closing dropdown
    $('input[name="category_id"]').on('click', function(e) {
        e.stopPropagation();
    });
});

// Rest of your existing functions...
// (keep all the other functions like add_more_customer_choice_option, etc.)
</script>


@include('partials.product.product_temp_data')

@endsection
