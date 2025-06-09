@extends('frontend.layouts.app')

@if (isset($category_id))
    @php
        $meta_title = $category->meta_title;
        $meta_description = $category->meta_description;
    @endphp
@elseif (isset($brand_id))
    @php
        $meta_title = get_single_brand($brand_id)->meta_title;
        $meta_description = get_single_brand($brand_id)->meta_description;
    @endphp
@else
    @php
        $meta_title         = get_setting('meta_title');
        $meta_description   = get_setting('meta_description');
    @endphp
@endif

@section('meta_title'){{ $meta_title }}@stop
@section('meta_description'){{ $meta_description }}@stop

@section('meta')
    <!-- Schema.org markup for Google+ -->
    <meta itemprop="name" content="{{ $meta_title }}">
    <meta itemprop="description" content="{{ $meta_description }}">

    <!-- Twitter Card data -->
    <meta name="twitter:title" content="{{ $meta_title }}">
    <meta name="twitter:description" content="{{ $meta_description }}">

    <!-- Open Graph data -->
    <meta property="og:title" content="{{ $meta_title }}" />
    <meta property="og:description" content="{{ $meta_description }}" />
@endsection

@section('content')

    <section class="mb-4 pt-4">
        <div class="container sm-px-0 pt-2">
            <form class="" id="search-form" action="" method="GET">
                <div class="row">

                    <!-- Sidebar Filters -->
                    <div class="col-xl-3">
                        <div class="aiz-filter-sidebar collapse-sidebar-wrap sidebar-xl sidebar-right z-1035">
                            <div class="overlay overlay-fixed dark c-pointer" data-toggle="class-toggle" data-target=".aiz-filter-sidebar" data-same=".filter-sidebar-thumb"></div>
                            <div class="collapse-sidebar c-scrollbar-light text-left">
                                <div class="d-flex d-xl-none justify-content-between align-items-center pl-3 border-bottom">
                                    <h3 class="h6 mb-0 fw-600">{{ translate('Filters') }}</h3>
                                    <button type="button" class="btn btn-sm p-2 filter-sidebar-thumb" data-toggle="class-toggle" data-target=".aiz-filter-sidebar" >
                                        <i class="las la-times la-2x"></i>
                                    </button>
                                </div>

                                <!-- Categories -->
                                <div class="bg-white border mb-3">
                                    <div class="fs-16 fw-700 p-3">
                                        <a href="#collapse_1" class="dropdown-toggle filter-section text-dark d-flex align-items-center justify-content-between" data-toggle="collapse">
                                            {{ translate('Categories')}}
                                        </a>
                                    </div>
                                    <div class="collapse show" id="collapse_1">
                                        <ul class="p-3 mb-0 list-unstyled">
                                            @if (!isset($category_id))
                                                @foreach ($categories as $category)
                                                    <li class="mb-3 text-dark">
                                                        <a class="text-reset fs-14 hov-text-primary" href="{{ route('products.category', $category->slug) }}">
                                                            {{ $category->getTranslation('name') }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            @else
                                                <li class="mb-3">
                                                    <a class="text-reset fs-14 fw-600 hov-text-primary" href="{{ route('search') }}">
                                                        <i class="las la-angle-left"></i>
                                                        {{ translate('All Categories')}}
                                                    </a>
                                                </li>
                                                
                                                @if ($category->parent_id != 0)
                                                    <li class="mb-3">
                                                        <a class="text-reset fs-14 fw-600 hov-text-primary" href="{{ route('products.category', get_single_category($category->parent_id)->slug) }}">
                                                            <i class="las la-angle-left"></i>
                                                            {{ get_single_category($category->parent_id)->getTranslation('name') }}
                                                        </a>
                                                    </li>
                                                @endif
                                                <li class="mb-3">
                                                    <a class="text-reset fs-14 fw-600 hov-text-primary" href="{{ route('products.category', $category->slug) }}">
                                                        <i class="las la-angle-left"></i>
                                                        {{ $category->getTranslation('name') }}
                                                    </a>
                                                </li>
                                                @foreach ($category->childrenCategories as $key => $immediate_children_category)
                                                    <li class="ml-4 mb-3">
                                                        <a class="text-reset fs-14 hov-text-primary" href="{{ route('products.category', $immediate_children_category->slug) }}">
                                                            {{ $immediate_children_category->getTranslation('name') }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            @endif
                                        </ul>
                                    </div>
                                </div>

                                <!-- Price range -->
                                <div class="bg-white border mb-3" style="display: none">
                                    <div class="fs-16 fw-700 p-3">
                                        {{ translate('Price range')}}
                                    </div>
                                    <div class="p-3 mr-3">
                                        @php
                                            $product_count = get_products_count()
                                        @endphp
                                        <div class="aiz-range-slider">
                                            <div
                                                id="input-slider-range"
                                                data-range-value-min="@if($product_count < 1) 0 @else {{ get_product_min_unit_price() }} @endif"
                                                data-range-value-max="@if($product_count < 1) 0 @else {{ get_product_max_unit_price() }} @endif"
                                            ></div>

                                            <div class="row mt-2">
                                                <div class="col-6">
                                                    <span class="range-slider-value value-low fs-14 fw-600 opacity-70"
                                                        @if (isset($min_price))
                                                            data-range-value-low="{{ $min_price }}"
                                                        @elseif($products->min('unit_price') > 0)
                                                            data-range-value-low="{{ $products->min('unit_price') }}"
                                                        @else
                                                            data-range-value-low="0"
                                                        @endif
                                                        id="input-slider-range-value-low"
                                                    ></span>
                                                </div>
                                                <div class="col-6 text-right">
                                                    <span class="range-slider-value value-high fs-14 fw-600 opacity-70"
                                                        @if (isset($max_price))
                                                            data-range-value-high="{{ $max_price }}"
                                                        @elseif($products->max('unit_price') > 0)
                                                            data-range-value-high="{{ $products->max('unit_price') }}"
                                                        @else
                                                            data-range-value-high="0"
                                                        @endif
                                                        id="input-slider-range-value-high"
                                                    ></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Hidden Items -->
                                    <input type="hidden" name="min_price" value="">
                                    <input type="hidden" name="max_price" value="">
                                </div>
                                
                                <!-- 
                                @foreach ($attributes as $attribute)
                                    <div class="bg-white border mb-3">
                                        <div class="fs-16 fw-700 p-3">
                                            <a href="#" class="dropdown-toggle text-dark filter-section collapsed d-flex align-items-center justify-content-between" 
                                                data-toggle="collapse" data-target="#collapse_{{ str_replace(' ', '_', $attribute->name) }}" style="white-space: normal;">
                                                {{ $attribute->getTranslation('name') }}
                                            </a>
                                        </div>
                                        @php
                                            $show = '';
                                            foreach ($attribute->attribute_values as $attribute_value){
                                                if(in_array($attribute_value->value, $selected_attribute_values)){
                                                    $show = 'show';
                                                }
                                            }
                                        @endphp
                                        <div class="collapse {{ $show }}" id="collapse_{{ str_replace(' ', '_', $attribute->name) }}">
                                            <div class="p-3 aiz-checkbox-list">
                                                @foreach ($attribute->attribute_values as $attribute_value)
                                                    <label class="aiz-checkbox mb-3">
                                                        <input
                                                            type="checkbox"
                                                            name="selected_attribute_values[]"
                                                            value="{{ $attribute_value->value }}" @if (in_array($attribute_value->value, $selected_attribute_values)) checked @endif
                                                            onchange="filter()"
                                                        >
                                                        <span class="aiz-square-check"></span>
                                                        <span class="fs-14 fw-400 text-dark">{{ $attribute_value->value }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                    -->
                                <!-- Color -->
                                @if (get_setting('color_filter_activation'))
                                    <div class="bg-white border mb-3">
                                        <div class="fs-16 fw-700 p-3">
                                            <a href="#" class="dropdown-toggle text-dark filter-section collapsed d-flex align-items-center justify-content-between" data-toggle="collapse" data-target="#collapse_color">
                                                {{ translate('Filter by color')}}
                                            </a>
                                        </div>
                                        @php
                                            $show = '';
                                            foreach ($colors as $key => $color){
                                                if(isset($selected_color) && $selected_color == $color->code){
                                                    $show = 'show';
                                                }
                                            }
                                        @endphp
                                        <div class="collapse {{ $show }}" id="collapse_color">
                                            <div class="p-3 aiz-radio-inline">
                                                @foreach ($colors as $key => $color)
                                                <label class="aiz-megabox pl-0 mr-2" data-toggle="tooltip" data-title="{{ $color->name }}">
                                                    <input
                                                        type="radio"
                                                        name="color"
                                                        value="{{ $color->code }}"
                                                        onchange="filter()"
                                                        @if(isset($selected_color) && $selected_color == $color->code) checked @endif
                                                    >
                                                    <span class="aiz-megabox-elem rounded d-flex align-items-center justify-content-center p-1 mb-2">
                                                        <span class="size-30px d-inline-block rounded" style="background: {{ $color->code }};"></span>
                                                    </span>
                                                </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Contents -->
                    <div class="col-xl-9">
                        
                        <!-- 
<ul class="breadcrumb bg-transparent py-0 px-1">
    <li class="breadcrumb-item has-transition opacity-50 hov-opacity-100 hidden-breadcrumb">
        <a class="text-reset" href="{{ route('home') }}">{{ translate('Home')}}</a>
    </li>
    @if(!isset($category_id))
        <li class="breadcrumb-item fw-700 text-dark hidden-breadcrumb">
            "{{ translate('All Categories')}}"
        </li>
    @else
        <li class="breadcrumb-item opacity-50 hov-opacity-100 hidden-breadcrumb">
            <a class="text-reset" href="{{ route('search') }}">{{ translate('All Categories')}}</a>
        </li>
    @endif
    @if(isset($category_id))
        <li class="text-dark fw-600 breadcrumb-item hidden-breadcrumb">
            "{{ $category->getTranslation('name') }}"
        </li>
    @endif
</ul>
-->
                        
                        <!-- Top Filters -->
                       <div class="text-left">
    <div class="row gutters-5 flex-wrap align-items-center">
        <div class="col-lg col-10">
            <input type="hidden" name="keyword" value="{{ $query }}">
        </div>
        <div class="col-2 col-lg-auto d-xl-none mb-lg-3 text-right">

                                    <!-- 
<button type="button" class="btn btn-icon p-0" data-toggle="class-toggle" data-target=".aiz-filter-sidebar">
                                        <i class="la la-filter la-2x"></i>
                                    </button>
-->
                                </div>
                                {{-- <div class="col-6 col-lg-auto mb-3 w-lg-200px mr-xl-4 mr-lg-3">
                                    @if (Route::currentRouteName() != 'products.brand')
                                        <select class="form-control form-control-sm aiz-selectpicker rounded-0" data-live-search="true" name="brand" onchange="filter()">
                                            <option value="">{{ translate('Brands')}}</option>
                                            @foreach (get_all_brands() as $brand)
                                                <option value="{{ $brand->slug }}" @isset($brand_id) @if ($brand_id == $brand->id) selected @endif @endisset>{{ $brand->getTranslation('name') }}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div> --}}
                                <div class="col-6 col-lg-auto mb-3 w-lg-200px" style="visibility: hidden;">
    <select class="form-control form-control-sm aiz-selectpicker rounded-0" name="sort_by" onchange="filter()">
        <option value="">{{ translate('Sort by')}}</option>
        <option value="newest" @isset($sort_by) @if ($sort_by == 'newest') selected @endif @endisset>{{ translate('Newest')}}</option>
        <option value="oldest" @isset($sort_by) @if ($sort_by == 'oldest') selected @endif @endisset>{{ translate('Oldest')}}</option>
        <option value="price-asc" @isset($sort_by) @if ($sort_by == 'price-asc') selected @endif @endisset>{{ translate('Price low to high')}}</option>
        <option value="price-desc" @isset($sort_by) @if ($sort_by == 'price-desc') selected @endif @endisset>{{ translate('Price high to low')}}</option>
    </select>
</div>

                            </div>
                        </div>
                        
                      
            @if(isset($category_id) && count($category->childrenCategories) > 0)
    <section class="py-3 bg-white">
        <div class="container-fluid d-flex flex-column align-items-center">
            <!-- Subcategories Carousel -->
            <div class="subcategory-wrapper">
                <div id="subcategoriesCarousel" class="aiz-carousel arrow-none" 
                    data-items="4"
                    data-xl-items="4" 
                    data-lg-items="3"
                    data-md-items="3"
                    data-sm-items="2"
                    data-xs-items="2"
                    data-dots="false" 
                    data-infinite="false"
                    data-autoplay="true">
                    
                    @foreach ($category->childrenCategories as $childCategory)
                        <div class="carousel-box">
                            <a href="{{ route('products.category', $childCategory->slug) }}" class="subcategory-link">
                                <div class="subcategory-img">
                                    <img class="lazyload" 
                                        src="{{ static_asset('assets/img/placeholder.jpg') }}" 
                                        data-src="{{ uploaded_asset($childCategory->banner) }}" 
                                        alt="{{ $childCategory->getTranslation('name') }}"
                                        onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                                </div>
                                <div class="subcategory-name">
                                    {{ $childCategory->getTranslation('name') }}
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>   
@elseif(isset($category_id) && count($category->childrenCategories) == 0)
    <!-- Show Filter Buttons ONLY if the category has NO children -->
    <div class="text-center mb-3">
        <div class="d-flex justify-content-center gap-2">
          
           <button type="button" class="filter-btn" data-toggle="modal" data-target="#priceModal">
                {{ translate('Price') }}
            </button>
            <button type="button" class="filter-btn" data-toggle="modal" data-target="#colorModal">
                {{ translate('Color') }}
            </button>
           
            <button type="button" class="filter-btn" data-toggle="modal" data-target="#sizeModal">
                {{ translate('Size') }}
            </button>
        </div>
    </div>
@endif


                      
                      
             <!-- Color Filter Modal -->
<div class="modal fade" id="colorModal" tabindex="-1" aria-labelledby="colorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">{{ translate('Filter by Color') }}</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <div class="d-flex flex-wrap gap-2">
                    @foreach ($colors as $color)
                        <label class="color-swatch position-relative">
                            <input type="radio" name="color" value="{{ $color->code }}" class="d-none" onchange="filter()"> 
                            <span class="d-inline-block size-30px rounded-circle border" style="background: {{ $color->code }};"></span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-danger" onclick="resetColorFilter()">
                    {{ translate('Reset') }}
                </button>
                <button type="button" class="btn btn-primary" data-dismiss="modal">
                    {{ translate('Apply') }}
                </button>
            </div>
        </div>
    </div>
</div>


<!-- ✅ Price Range Modal (Fully Fixed) -->
<div class="modal fade" id="priceModal" tabindex="-1" aria-labelledby="priceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">{{ translate('Price range') }}</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- ✅ Modal Body (Fixed Layout) -->
            <div class="modal-body text-center">
                <!-- ✅ Slider Track (Fixed inside container) -->
             <div class="slider-container">
    <input type="range" id="minRange" class="slider" min="0" max="126000" value="0">
    <input type="range" id="maxRange" class="slider slider-upper" min="0" max="126000" value="126000">
</div>


                <!-- ✅ Price Input Fields -->
                <div class="row mt-3">
                    <div class="col-6">
                        <input type="number" id="min_price" name="min_price" class="form-control text-center rounded-pill border bg-light" readonly>
                    </div>
                    <div class="col-6">
                        <input type="number" id="max_price" name="max_price" class="form-control text-center rounded-pill border bg-light" readonly>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="button" class="btn btn-outline-danger px-4 rounded-pill" onclick="resetPriceFilter()">
                        {{ translate('Reset') }}
                    </button>
                    <button type="button" class="btn btn-primary px-4 rounded-pill" onclick="filter()">
                        {{ translate('Apply') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ✅ Improved Slider Styling (Fixed) -->
<style>
    /* ✅ Fix the Modal Width to Prevent Overflow */
    .modal-dialog {
        max-width: 420px !important; /* Prevents horizontal scrolling */
        width: 90%;
    }

    /* ✅ Fix the Slider Layout */
    .slider-container {
        position: relative;
        width: 100%;
        max-width: 320px; /* Prevents slider from overflowing */
        margin: auto;
        display: flex;
        align-items: center;
    }

    /* ✅ Keep Sliders Inside the Modal */
    .slider {
        width: 100%;
        appearance: none;
        height: 6px;
        background: #ddd;
        border-radius: 5px;
        position: relative;
    }

    .slider::-webkit-slider-thumb {
        appearance: none;
        width: 18px;
        height: 18px;
        background: #0088cc;
        border-radius: 50%;
        cursor: pointer;
    }

    .slider::-moz-range-thumb {
        width: 18px;
        height: 18px;
        background: #0088cc;
        border-radius: 50%;
        cursor: pointer;
    }

    /* ✅ Fix Button & Input Layout */
    .form-control {
        font-size: 14px;
        padding: 8px;
    }

    .btn {
        font-size: 14px;
        padding: 8px 12px;
    }

    /* ✅ Mobile Fixes */
    @media (max-width: 768px) {
        .modal-dialog {
            max-width: 90%;
        }

        .slider-container {
            max-width: 280px;
        }

        .form-control {
            font-size: 12px;
            padding: 6px;
        }
    }
</style>

                      
                      <style>
    .slider-container {
        position: relative;
        width: 100%;
        max-width: 320px;
        margin: auto;
    }

    .slider {
        position: absolute;
        pointer-events: none;
        width: 100%;
        height: 6px;
        background: #ddd;
        border-radius: 5px;
        appearance: none;
    }

    .slider::-webkit-slider-thumb {
        pointer-events: all;
        appearance: none;
        width: 18px;
        height: 18px;
        background: #0088cc;
        border-radius: 50%;
        cursor: pointer;
    }

    .slider-upper {
        z-index: 2;
    }
</style>

                 

<!-- ✅ Optimized Slider Script -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const minRange = document.getElementById("minRange");
        const maxRange = document.getElementById("maxRange");
        const minPrice = document.getElementById("min_price");
        const maxPrice = document.getElementById("max_price");

        const maxPriceValue = 126000;

        minRange.max = maxRange.max = maxPriceValue;
        maxRange.value = maxPriceValue;
        minRange.value = 0;

        function getStep(value) {
            if (value < 10000) return 200;
            if (value < 30000) return 7000;
            if (value < 60000) return 2000;
            if (value < 100000) return 5000;
            return 10000;
        }

        function updateInputs() {
            let min = parseInt(minRange.value);
            let max = parseInt(maxRange.value);

            // Enforce minimum gap
            if (min >= max) {
                min = max - getStep(max);
                minRange.value = min;
            }

            minPrice.value = min;
            maxPrice.value = max;
        }

        function handleMinChange() {
            const step = getStep(parseInt(minRange.value));
            minRange.step = step;
            minRange.value = Math.round(minRange.value / step) * step;
            updateInputs();
        }

        function handleMaxChange() {
            const step = getStep(parseInt(maxRange.value));
            maxRange.step = step;
            maxRange.value = Math.round(maxRange.value / step) * step;
            updateInputs();
        }

        minRange.addEventListener("input", handleMinChange);
        maxRange.addEventListener("input", handleMaxChange);

        window.resetPriceFilter = function () {
            minRange.value = 0;
            maxRange.value = maxPriceValue;
            updateInputs();
        };

        updateInputs();
    });
</script>




<!-- Size Filter Modal -->
<div class="modal fade" id="sizeModal" tabindex="-1" aria-labelledby="sizeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">{{ translate('Filter by Size') }}</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <div class="d-flex flex-wrap gap-2">
                    @foreach ($sizes as $size)
                        <label class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                            <input type="radio" name="size" value="{{ $size }}" class="d-none" onchange="filter()"
                            @if(isset($selected_size) && $selected_size == $size) checked @endif>
                            {{ $size }}
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-danger" onclick="resetSizeFilter()">
                    {{ translate('Reset') }}
                </button>
                <button type="button" class="btn btn-primary" data-dismiss="modal">
                    {{ translate('Apply') }}
                </button>
            </div>
        </div>
    </div>
</div>






<script>
    function resetColorFilter() {
        document.querySelectorAll("#colorModal input[type=radio]").forEach(el => el.checked = false);
        filter();
    }

    function resetPriceFilter() {
        document.querySelector("input[name='min_price']").value = "";
        document.querySelector("input[name='max_price']").value = "";
        filter();
    }

    function resetSizeFilter() {
        document.querySelectorAll("#sizeModal input[type=checkbox]").forEach(el => el.checked = false);
        filter();
    }
</script>


     
                      
                      
        <style>
       /* ✅ PC Layout: Display Products in a Grid (4-5 per row) */
@media (min-width: 768px) {
    .category-product-container {
        display: flex !important;
        flex-wrap: wrap !important;
        justify-content: flex-start !important;
        gap: 15px !important;
    }

    .category-product-box {
        flex: 0 0 calc(20% - 15px) !important; /* 5 products per row */
        min-width: calc(20% - 15px) !important;
        max-width: calc(20% - 15px) !important;
        background: #fff !important;
        padding: 12px !important;
        border-radius: 8px !important;
        box-shadow: 0px 3px 10px rgba(0, 0, 0, 0.08) !important;
        text-align: center !important;
    }

    .category-product-box img {
        width: 120px !important;
        height: 120px !important;
        object-fit: contain !important;
    }
}

/* 📱 Mobile: Make Products Display Horizontally */
@media (max-width: 767px) {
    .category-product-container {
        display: flex !important;
        flex-wrap: nowrap !important;
        overflow-x: auto !important;
        scroll-snap-type: x mandatory !important;
        -webkit-overflow-scrolling: touch !important;
        padding-bottom: 10px !important;
        gap: 10px !important;
    }

    /* Hide Scrollbar */
    .category-product-container::-webkit-scrollbar {
        display: none;
    }

    /* 🔥 Ensure Products Are in a Row Like "Today's Deal" */
    .category-product-box {
        flex: 0 0 calc(50% - 10px) !important; /* 2 products per row */
        min-width: calc(50% - 10px) !important;
        max-width: calc(50% - 10px) !important;
        background: #fff !important;
        padding: 12px !important;
        border-radius: 8px !important;
        box-shadow: 0px 3px 10px rgba(0, 0, 0, 0.08) !important;
        text-align: center !important;
        scroll-snap-align: start !important;
    }

    /* 🔥 Ensure Images are Sized Properly */
    .category-product-box img {
        width: 90px !important;
        height: 90px !important;
        object-fit: contain !important;
    }

    /* 🔥 Subcategory Name Below the Image */
    .category-product-box .fs-14 {
        font-size: 14px !important;
        font-weight: bold !important;
        color: #000 !important;
        margin-top: 5px !important;
    }

    /* 🔥 Price Formatting */
    .category-product-box .fw-700 {
        font-size: 16px !important;
        font-weight: 700 !important;
        color: #0088cc !important;
        margin-top: 3px !important;
    }
}


                      </style>
                      
                      
                      
                                      <!-- Check if inside a specific subcategory -->
@if(isset($category_id) && $category->level == 1)
    <div class="container px-3">
        <div class="row">
            @foreach ($products as $product)
                <div class="col-6 col-md-4 col-lg-3 mb-4">
                    <div class="h-100 p-2 shadow-sm border rounded d-flex flex-column align-items-center text-center bg-white">
                        <a href="{{ route('product', $product->slug) }}" class="d-block w-100 mb-2">
                            <img src="{{ get_image($product->thumbnail) }}"
                                 class="img-fluid w-100 rounded"
                                 style="aspect-ratio: 1/1; object-fit: cover;"
                                 alt="{{ $product->getTranslation('name') }}"
                                 onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                        </a>

                        @php
                            $subcategory = $product->categories->where('level', 1)->first();
                        @endphp

                        <div class="fw-600 fs-14 mt-1">
                            {{ $subcategory ? $subcategory->getTranslation('name') : '-' }}
                        </div>

                        <div class="fw-700 text-primary mt-1">
                            {{ home_discounted_base_price($product) }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@else
    <!-- Group products by subcategory -->
    @php
        $groupedProducts = $products->groupBy(function($product) {
            return optional($product->categories->where('level', 1)->first())->getTranslation('name') ?? 'Uncategorized';
        });
    @endphp

    <div class="px-3">
        @foreach ($groupedProducts as $subcategoryName => $subcategoryProducts)
@php
    $firstProduct = $subcategoryProducts->first();
    $subcategoryModel = $firstProduct?->categories->where('level', 1)->first();
@endphp

<div class="d-flex align-items-center justify-content-between mt-4 mb-2">
    <h5 class="fw-600 fs-14 mb-0">{{ $subcategoryName }}</h5>
    @if($subcategoryModel)
        <a href="{{ route('products.category', $subcategoryModel->slug) }}" class="fs-13 text-primary fw-600">
            {{ translate('View All') }}
        </a>
    @endif
</div>
            <div class="category-product-container d-flex flex-wrap justify-content-start">
                @foreach ($subcategoryProducts as $product)
                    <div class="category-product-box">
                        <a href="{{ route('product', $product->slug) }}" class="d-block text-reset">
                            <img src="{{ get_image($product->thumbnail) }}" class="lazyload img-fit border rounded"
                                 alt="{{ $product->getTranslation('name') }}"
                                 onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                        </a>
                      <!-- Subcategory Name Below Image -->
        @php
            // Fetch the first subcategory of the product
            $subcategory = $product->categories->where('level', 1)->first();
        @endphp
        <div class="fs-14 text-center text-black fw-600 mt-2">
            {{ $subcategory ? $subcategory->getTranslation('name') : '-' }}
        </div>
                        <span class="d-block fw-700 text-primary mt-1">
                            {{ home_discounted_base_price($product) }}
                        </span>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
@endif


                        <div class="aiz-pagination mt-4">
                            {{ $products->appends(request()->input())->links() }}
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

@endsection

@section('script')
    <script type="text/javascript">
        function filter(){
            $('#search-form').submit();
        }
        function rangefilter(arg){
            $('input[name=min_price]').val(arg[0]);
            $('input[name=max_price]').val(arg[1]);
            filter();
        }
    </script>
@endsection

      <style>
         .filter-btn {
        margin-top: -50px;
    background-color: #fff;
    border: 2px solid #ddd;
    border-radius: 12px;
    padding: 8px 16px;
    font-weight: bold;
    font-size: 16px;
    color: #222;
    width: 30%;
    text-align: middle; /* Align text to the left */
    box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
    margin-right: 8px; /* Small space between buttons */
}

.filter-btn:last-child {
    margin-right: 0; /* Remove margin from the last button */
}

.filter-btn:focus,
.filter-btn:hover {
    border-color: #222;
    background-color: #f5f5f5;
}
 /* ✅ Subcategory Wrapper - Enables Smooth Horizontal Scrolling */
.subcategory-wrapper {
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scroll-snap-type: x mandatory;
    white-space: nowrap; /* Forces items in a single row */
    padding-bottom: 10px;
    position: relative;
    margin-top: -70px !important; /* Adjusts spacing */
}

/* ✅ Enable Horizontal Scrolling */
.subcategory-wrapper::-webkit-scrollbar {
    display: none; /* Hide scrollbar */
}

/* ✅ Subcategory Carousel - Ensures Items are in a Row */
.aiz-carousel {
    display: flex !important;
    flex-wrap: nowrap !important; /* Ensures one row */
    justify-content: flex-start !important;
    align-items: center;
    gap: 10px;
    overflow-x: auto;
    scroll-snap-type: x mandatory;
    -webkit-overflow-scrolling: touch;
    width: 100%;
}

/* ✅ Subcategory Box */
.carousel-box {
    flex: 0 0 auto;
    width: 100px; /* Adjust width */
    text-align: center;
    scroll-snap-align: start;
}

/* ✅ Circular Image */
.subcategory-img {
    width: 85px;
    height: 85px;
    border-radius: 50%;
    overflow: hidden;
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

/* ✅ Image Inside the Circle */
.subcategory-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
}

/* ✅ Hover Effect */
.subcategory-img:hover {
    transform: scale(1.1);
}

/* ✅ Subcategory Name */
.subcategory-name {
    font-size: 14px;
    font-weight: bold;
    color: #333;
    margin-top: 5px;
    text-align: center;
}

/* ✅ Fix for Mobile */
@media (max-width: 768px) {
    .subcategory-wrapper {
        overflow-x: auto !important;
        white-space: nowrap !important;
    }

    .carousel-box {
        min-width: 90px !important;
        max-width: 90px !important;
    }

    .subcategory-img {
        width: 75px !important;
        height: 75px !important;
    }

    .subcategory-name {
        font-size: 12px !important;
    }
}

      </style>


<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Remove empty divs that cause spacing issues
        document.querySelectorAll("div:empty").forEach(div => div.remove());

        // Force move subcategories even higher
        document.querySelector(".subcategory-wrapper").style.marginTop = "-70px";
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const carousel = document.querySelector(".aiz-carousel");

        // Enable touch/mouse drag scrolling
        let isDown = false;
        let startX;
        let scrollLeft;

        carousel.addEventListener("mousedown", (e) => {
            isDown = true;
            carousel.classList.add("active");
            startX = e.pageX - carousel.offsetLeft;
            scrollLeft = carousel.scrollLeft;
        });

        carousel.addEventListener("mouseleave", () => {
            isDown = false;
            carousel.classList.remove("active");
        });

        carousel.addEventListener("mouseup", () => {
            isDown = false;
            carousel.classList.remove("active");
        });

        carousel.addEventListener("mousemove", (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - carousel.offsetLeft;
            const walk = (x - startX) * 2; // Adjust scrolling speed
            carousel.scrollLeft = scrollLeft - walk;
        });

        // Auto-scroll if needed
        function autoScroll() {
            if (carousel.scrollWidth > carousel.clientWidth) {
                carousel.scrollBy({ left: 200, behavior: "smooth" });
            }
        }

        setInterval(autoScroll, 5000); // Auto-scroll every 5 seconds
    });
</script>


      