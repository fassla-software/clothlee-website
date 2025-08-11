@extends('frontend.layouts.app')

@section('meta_title'){{ $shop->meta_title }}@stop

@section('meta_description'){{ $shop->meta_description }}@stop

@section('meta')
    <!-- Schema.org markup for Google+ -->
    <meta itemprop="name" content="{{ $shop->meta_title }}">
    <meta itemprop="description" content="{{ $shop->meta_description }}">
    <meta itemprop="image" content="{{ uploaded_asset($shop->logo) }}">

    <!-- Twitter Card data -->
    <meta name="twitter:card" content="website">
    <meta name="twitter:site" content="@publisher_handle">
    <meta name="twitter:title" content="{{ $shop->meta_title }}">
    <meta name="twitter:description" content="{{ $shop->meta_description }}">
    <meta name="twitter:creator" content="@author_handle">
    <meta name="twitter:image" content="{{ uploaded_asset($shop->meta_img) }}">

    <!-- Open Graph data -->
    <meta property="og:title" content="{{ $shop->meta_title }}" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ route('shop.visit', $shop->slug) }}" />
    <meta property="og:image" content="{{ uploaded_asset($shop->logo) }}" />
    <meta property="og:description" content="{{ $shop->meta_description }}" />
    <meta property="og:site_name" content="{{ $shop->name }}" />
@endsection

@section('content')
    <section class="mt-3 mb-3 bg-white" style="display: none">
        <div class="container">
            <!--  Top Menu -->
            <div class="d-flex flex-wrap justify-content-center justify-content-md-start">
                <a class="fw-700 fs-11 fs-md-13 mr-3 mr-sm-4 mr-md-5 text-dark opacity-60 hov-opacity-100 @if(!isset($type)) opacity-100 @endif"
                        href="{{ route('shop.visit', $shop->slug) }}">{{ translate('Store Home')}}</a>
                <a class="fw-700 fs-11 fs-md-13 mr-3 mr-sm-4 mr-md-5 text-dark opacity-60 hov-opacity-100 @if(isset($type) && $type == 'top-selling') opacity-100 @endif"
                        href="{{ route('shop.visit.type', ['slug'=>$shop->slug, 'type'=>'top-selling']) }}">{{ translate('Top Selling')}}</a>
                <a class="fw-700 fs-11 fs-md-13 mr-3 mr-sm-4 mr-md-5 text-dark opacity-60 hov-opacity-100 @if(isset($type) && $type == 'cupons') opacity-100 @endif"
                        href="{{ route('shop.visit.type', ['slug'=>$shop->slug, 'type'=>'cupons']) }}">{{ translate('Coupons')}}</a>
                <a class="fw-700 fs-11 fs-md-13 text-dark opacity-60 hov-opacity-100 @if(isset($type) && $type == 'all-products') opacity-100 @endif"
                        href="{{ route('shop.visit.type', ['slug'=>$shop->slug, 'type'=>'all-products']) }}">{{ translate('All Products')}}</a>
            </div>
        </div>
    </section>

    @php
        $followed_sellers = [];
        if (Auth::check()) {
            $followed_sellers = get_followed_sellers();
        }
    @endphp

    @if (!isset($type) || $type == 'top-selling' || $type == 'cupons')
        @if ($shop->top_banner)
            <!-- Top Banner -->
            <section class="h-160px h-md-200px h-lg-300px h-xl-100 w-100">
                <img class="d-block lazyload h-100 img-fit"
                    src="{{ static_asset('assets/img/placeholder-rect.jpg') }}"
                    data-src="{{ uploaded_asset($shop->top_banner) }}" alt="{{ env('APP_NAME') }} offer">
            </section>
        @endif
    @endif

    <!-- Banner Slider -->
    <section class="mt-3 mb-3">
        <div class="container">
            <div class="aiz-carousel mobile-img-auto-height" data-arrows="false" data-dots="true" data-autoplay="true">
                @if ($shop->sliders != null)
                    @foreach (explode(',',$shop->sliders) as $key => $slide)
                        <div class="carousel-box w-100 h-140px h-md-300px h-xl-450px">
                            <img class="d-block lazyload h-100 img-fit" src="{{ static_asset('assets/img/placeholder-rect.jpg') }}" data-src="{{ uploaded_asset($slide) }}" alt="{{ $key }} offer">
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </section>

<section class="py-3" style="background: #ffffff;">
    <div class="container d-flex justify-content-center">
        <div class="w-100" style="max-width: 360px;">
            <div class="bg-white rounded-4 shadow-sm p-4 text-center">

                <!-- Brand Image -->
                <div class="bg-white rounded-3 p-3 mb-3" style="box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                    <img src="{{ uploaded_asset($shop->logo) }}"
                         onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                         class="img-fluid mx-auto d-block"
                         style="max-height: 70px; object-fit: contain;">
                </div>

                <!-- Shop Name -->
                <h3 class="mb-3 fw-bold" style="font-size: 15px;">{{ $shop->name }}</h3>

                <!-- Social Media Icons -->
                @if($shop->facebook || $shop->instagram || $shop->website || $shop->tiktok || $shop->youtube)
                    <div class="social-icons mb-3">
                        @if($shop->facebook)
                            <a href="{{ $shop->facebook }}" target="_blank" class="social-icon facebook me-2">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                        @endif
                        @if($shop->instagram)
                            <a href="{{ $shop->instagram }}" target="_blank" class="social-icon instagram me-2">
                                <i class="fab fa-instagram"></i>
                            </a>
                        @endif
                        @if($shop->tiktok)
                            <a href="{{ $shop->tiktok }}" target="_blank" class="social-icon tiktok me-2">
                                <i class="fab fa-tiktok"></i>
                            </a>
                        @endif
                        @if($shop->youtube)
                            <a href="{{ $shop->youtube }}" target="_blank" class="social-icon youtube me-2">
                                <i class="fab fa-youtube"></i>
                            </a>
                        @endif
                        @if($shop->website)
                            <a href="{{ $shop->website }}" target="_blank" class="social-icon website">
                                <i class="fas fa-globe"></i>
                            </a>
                        @endif
                    </div>
                @endif

                <!-- Address -->
                @if ($shop->address)
                    <div class="text-muted d-flex align-items-center justify-content-center" style="font-size: 13px;">
                        <i class="las la-map-marker-alt text-primary mr-1"></i>
                        {{ $shop->address }}
                    </div>
                @endif

            </div>
        </div>
    </div>
</section>

<!-- Rest of your existing code continues unchanged... -->

@if (!isset($type))
    @php
        $feature_products = $shop->user->products->where('published', 1)->where('approved', 1)->where('seller_featured', 1);
    @endphp
    @if (count($feature_products) > 0)
        <!-- Featured Products -->
        <section class="mt-3 mb-3" id="section_featured">
            <div class="container">
            <!-- Top Section -->
            <div class="d-flex mb-4 align-items-baseline justify-content-between">
                    <!-- Title -->
                    <h3 class="fs-16 fs-md-20 fw-700 mb-3 mb-sm-0">
                        <span class="">{{ translate('Featured Products') }}</span>
                    </h3>
                    <!-- Links -->
                    <div class="d-flex">
                        <a type="button" class="arrow-prev slide-arrow text-secondary mr-2" onclick="clickToSlide('slick-prev','section_featured')"><i class="las la-angle-left fs-20 fw-600"></i></a>
                        <a type="button" class="arrow-next slide-arrow text-secondary ml-2" onclick="clickToSlide('slick-next','section_featured')"><i class="las la-angle-right fs-20 fw-600"></i></a>
                    </div>
                </div>
                <!-- Products Section -->
                <div class="px-sm-3">
                    <div class="aiz-carousel sm-gutters-16 arrow-none" data-items="6" data-xl-items="5" data-lg-items="4"  data-md-items="3" data-sm-items="2" data-xs-items="2" data-arrows='true' data-autoplay='true' data-infinute="true">
                        @foreach ($feature_products as $key => $product)
                        <div class="carousel-box px-3 position-relative has-transition hov-animate-outline border-right border-top border-bottom @if($key == 0) border-left @endif">
                            @include('frontend.'.get_setting('homepage_select').'.partials.product_box_1',['product' => $product])
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif

    

    <!-- Coupons -->
    @php
        $coupons = get_coupons($shop->user->id);
    @endphp
    @if (count($coupons)>0)
        <section class="mt-3 mb-3" id="section_coupons">
            <div class="container">
            <!-- Top Section -->
            <div class="d-flex mb-4 align-items-baseline justify-content-between">
                    <!-- Title -->
                    <h3 class="fs-16 fs-md-20 fw-700 mb-3 mb-sm-0">
                        <span class="pb-3">{{ translate('Coupons') }}</span>
                    </h3>
                    <!-- Links -->
                    <div class="d-flex">
                        <a type="button" class="arrow-prev slide-arrow link-disable text-secondary mr-2" onclick="clickToSlide('slick-prev','section_coupons')"><i class="las la-angle-left fs-20 fw-600"></i></a>
                        <a class="text-blue fs-12 fw-700 hov-text-primary" href="{{ route('shop.visit.type', ['slug'=>$shop->slug, 'type'=>'cupons']) }}">{{ translate('View All') }}</a>
                        <a type="button" class="arrow-next slide-arrow text-secondary ml-2" onclick="clickToSlide('slick-next','section_coupons')"><i class="las la-angle-right fs-20 fw-600"></i></a>
                    </div>
                </div>
                <!-- Coupons Section -->
                <div class="aiz-carousel sm-gutters-16 arrow-none" data-items="3" data-lg-items="2" data-sm-items="1" data-arrows='true' data-infinite='false'>
                    @foreach ($coupons->take(10) as $key => $coupon)
                        <div class="carousel-box">
                            @include('frontend.partials.coupon_box',['coupon' => $coupon])
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($shop->banner_full_width_1)
        <!-- Banner full width 1 -->
        @foreach (explode(',',$shop->banner_full_width_1) as $key => $banner)
            <section class="container mb-3 mt-3">
                <div class="w-100">
                    <img class="d-block lazyload h-100 img-fit"
                        src="{{ static_asset('assets/img/placeholder-rect.jpg') }}"
                        data-src="{{ uploaded_asset($banner) }}" alt="{{ env('APP_NAME') }} offer">
                </div>
            </section>
        @endforeach
    @endif

    @if($shop->banners_half_width)
        <!-- Banner half width -->
        <section class="container  mb-3 mt-3">
            <div class="row gutters-16">
                @foreach (explode(',',$shop->banners_half_width) as $key => $banner)
                <div class="col-md-6 mb-3 mb-md-0">
                    <div class="w-100">
                        <img class="d-block lazyload h-100 img-fit"
                            src="{{ static_asset('assets/img/placeholder-rect.jpg') }}"
                            data-src="{{ uploaded_asset($banner) }}" alt="{{ env('APP_NAME') }} offer">
                    </div>
                </div>
                @endforeach
            </div>
        </section>
    @endif

@endif

@if (!isset($type))
@php
    // Fetch parent categories that have products in their subcategories for this shop
    $categories = \App\Models\Category::where('parent_id', 0)
        ->whereHas('childrenCategories.products', function ($query) use ($shop) {
            $query->where('user_id', $shop->user->id)->where('published', 1);
        })
        ->get();
@endphp

@if ($categories->isNotEmpty())
<section class="mt-3 mb-3" id="store_parent_categories">
    <div class="container">
        <div class="d-flex mb-4 align-items-baseline justify-content-between">
            <h3 class="fs-16 fs-md-20 fw-700 mb-3 mb-sm-0">
                {{ translate('') }}
            </h3>
        </div>

        <!-- Swiper -->
        <div class="swiper-container">
            <div class="swiper-wrapper">
                @foreach ($categories as $category)
                    <div class="swiper-slide text-center">
                        <a href="{{ route('shop.visit.type', ['slug' => $shop->slug, 'type' => 'all-products', 'category' => $category->id]) }}"
                           class="d-block text-center hover-shadow-lg transition-3d-hover">
                            <div class="category-image-wrapper mx-auto mb-2" style="width: 100px; height: 100px;">
                                <img class="lazyload img-fluid rounded-circle"
                                     src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                     data-src="{{ uploaded_asset($category->banner) }}"
                                     alt="{{ $category->name }}"
                                     onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                            </div>
                            <h5 class="fs-14 fw-700 text-dark mt-2">
                                {{ $category->name }}
                            </h5>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<!-- Swiper Init Script -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        new Swiper('.swiper-container', {
            spaceBetween: 20,
            loop: false,
            breakpoints: {
                // Mobile: 2 per slide
                0: {
                    slidesPerView: 2,
                },
                // Tablets: 3
                768: {
                    slidesPerView: 3,
                },
                // Desktops: 5 or 6
                992: {
                    slidesPerView: 5,
                },
                1200: {
                    slidesPerView: 6,
                }
            }
        });
    });
</script>
@endif

@endif


<style>
.gutters-16 {
margin-right: -16px;
margin-left: -16px;
}
#store_parent_categories .category-image-wrapper {
width: 120px;
height: 120px;
overflow: hidden;
border-radius: 50%;
display: flex;
justify-content: center;
align-items: center;
box-shadow: none; /* Removes any existing shadow */
}

#store_parent_categories img {
width: 100%;
height: 100%;
object-fit: cover;
transition: transform 0.3s ease-in-out;
}

#store_parent_categories a:hover img {
transform: scale(1.1);
}

/* Social Media Icons Styles */
.social-icons {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.social-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    color: white;
    text-decoration: none;
    transition: all 0.3s ease;
    font-size: 16px;
}

.social-icon:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    text-decoration: none;
    color: white;
}

.social-icon.facebook {
    background: #1877f2;
}

.social-icon.instagram {
    background: linear-gradient(45deg, #f09433 0%,#e6683c 25%,#dc2743 50%,#cc2366 75%,#bc1888 100%);
}

.social-icon.tiktok {
    background: #000000;
}

.social-icon.youtube {
    background: #ff0000;
}

.social-icon.website {
    background: #6c757d;
}

/* Responsive adjustments */
@media (max-width: 576px) {
    .social-icon {
        width: 35px;
        height: 35px;
        font-size: 14px;
    }
}
</style>

<section class="mb-3 mt-3" id="section_types">
    <div class="container">
        <!-- Top Section -->
        <div class="d-flex mb-4 align-items-baseline justify-content-between">
            <!-- Title -->
            <h3 class="fs-16 fs-md-20 fw-700 mb-3 mb-sm-0">
                <span class="pb-3">
                    @if (!isset($type))
                        {{ translate('')}}
                    @elseif ($type == 'top-selling')
                        {{ translate('')}}
                    @elseif ($type == 'cupons')
                        {{ translate('')}}
                    @endif
                </span>
            </h3>
          <!--
            @if (!isset($type))
                <!-- 
                <div class="d-flex">
                    <a type="button" class="arrow-prev slide-arrow link-disable text-secondary mr-2" onclick="clickToSlide('slick-prev','section_types')"><i class="las la-angle-left fs-20 fw-600"></i></a>
                    <a type="button" class="arrow-next slide-arrow text-secondary ml-2" onclick="clickToSlide('slick-next','section_types')"><i class="las la-angle-right fs-20 fw-600"></i></a>
                </div>
            @endif
-->
        </div>

        @php
            if (!isset($type)){
                $products = get_seller_products($shop->user->id);
            }
            elseif ($type == 'top-selling'){
                $products = get_shop_best_selling_products($shop->user->id);
            }
            elseif ($type == 'cupons'){
                $coupons = get_coupons($shop->user->id , 24);
            }
        @endphp

        @if (!isset($type))
      
      
   @php
$groupedProducts = collect();

foreach ($products as $product) {
    foreach ($product->categories as $category) {
        if ($category->parent_id && $category->parentCategory) {
            $key = $category->parentCategory->getTranslation('name');
            if (!$groupedProducts->has($key)) {
                $groupedProducts[$key] = collect();
            }
            $groupedProducts[$key]->push($product);
            break;
        }
    }
}
@endphp

<div class="px-sm-3 pb-3">
@foreach ($groupedProducts as $categoryName => $productsGroup)
    @php
        // Sort products according to shop's default_sort (like API)
        if ($shop->default_sort === 'cheapest') {
            $sortedProducts = $productsGroup->sortBy('unit_price');
        } elseif ($shop->default_sort === 'newest') {
            $sortedProducts = $productsGroup->sortByDesc('created_at');
        } else {
            $sortedProducts = $productsGroup;
        }
    @endphp
    <h4 class="mb-3">{{ $categoryName }}</h4>
    <div class="aiz-carousel sm-gutters-16 arrow-none" data-items="6" data-xl-items="5" data-lg-items="4" data-md-items="3" data-sm-items="2" data-xs-items="2" data-arrows="false" data-dots="false" data-infinite="false">
        @foreach ($sortedProducts as $product)
            <div class="carousel-box px-3 position-relative has-transition hov-animate-outline">
                @include('frontend.'.get_setting('homepage_select').'.partials.product_box_1', ['product' => $product])
            </div>
        @endforeach
    </div>
@endforeach
</div>

            </div>

            @if ($shop->banner_full_width_2)
                <!-- Banner full width 2 -->
                @foreach (explode(',',$shop->banner_full_width_2) as $key => $banner)
                    <div class="mt-3 mb-3 w-100">
                        <img class="d-block lazyload h-100 img-fit"
                            src="{{ static_asset('assets/img/placeholder-rect.jpg') }}"
                            data-src="{{ uploaded_asset($banner) }}" alt="{{ env('APP_NAME') }} offer">
                    </div>
                @endforeach
            @endif


        @elseif ($type == 'cupons')
            <!-- All Coupons Section -->
            <div class="row gutters-16 row-cols-xl-3 row-cols-md-2 row-cols-1">
                @foreach ($coupons as $key => $coupon)
                    <div class="col mb-4">
                        @include('frontend.partials.coupon_box',['coupon' => $coupon])
                    </div>
                @endforeach
            </div>
            <div class="aiz-pagination mt-4 mb-4">
                {{ $coupons->links() }}
            </div>

        @elseif ($type == 'all-products')
            <!-- All Products Section -->
            <form class="" id="search-form" action="" method="GET">
                <div class="row gutters-16 justify-content-center">
                    <!-- Sidebar -->
                    <div class="col-xl-3 col-md-6 col-sm-8">

                        <!-- Sidebar Filters -->
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
                                <div class="bg-white border mb-4 mx-3 mx-xl-0 mt-3 mt-xl-0">
                                    <div class="fs-16 fw-700 p-3">
                                        <a href="#collapse_1" class="dropdown-toggle filter-section text-dark d-flex align-items-center justify-content-between" data-toggle="collapse">
                                            {{ translate('Categories')}}
                                        </a>
                                    </div>
                                    <div class="collapse show px-3" id="collapse_1">
                                        @foreach (get_categories_by_products($shop->user->id) as $category)
                                            <label class="aiz-checkbox mb-3">
                                                <input
                                                    type="checkbox"
                                                    name="selected_categories[]"
                                                    value="{{ $category->id }}" @if (in_array($category->id, $selected_categories)) checked @endif
                                                    onchange="filter()"
                                                >
                                                <span class="aiz-square-check"></span>
                                                <span class="fs-14 fw-400 text-dark">{{ $category->getTranslation('name') }}</span>
                                            </label>
                                            <br>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Price range -->
                                <div class="bg-white border mb-3">
                                    <div class="fs-16 fw-700 p-3">
                                        {{ translate('Price range')}}
                                    </div>
                                    <div class="p-3 mr-3">
                                        <div class="aiz-range-slider">
                                            <div
                                                id="input-slider-range"
                                                data-range-value-min="@if(get_products_count($shop->user->id) < 1) 0 @else {{ get_product_min_unit_price($shop->user->id) }} @endif"
                                                data-range-value-max="@if(get_products_count($shop->user->id) < 1) 0 @else {{ get_product_max_unit_price($shop->user->id) }} @endif"
                                            ></div>

                                            <div class="row mt-2">
                                                <div class="col-6">
                                                    <span class="range-slider-value value-low fs-14 fw-600 opacity-70"
                                                        @if ($min_price != null)
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
                                                        @if ($max_price != null)
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

                                <!-- Ratings -->
                                <div class="bg-white border mb-4 mx-3 mx-xl-0 mt-3 mt-xl-0">
                                    <div class="fs-16 fw-700 p-3">
                                        <a href="#collapse_2" class="dropdown-toggle filter-section text-dark d-flex align-items-center justify-content-between" data-toggle="collapse">
                                            {{ translate('Ratings')}}
                                        </a>
                                    </div>
                                    <div class="collapse show px-3" id="collapse_2">
                                        <label class="aiz-checkbox mb-3">
                                            <input
                                                type="radio"
                                                name="rating"
                                                value="5" @if ($rating==5) checked @endif
                                                onchange="filter()"
                                            >
                                            <span class="aiz-square-check"></span>
                                            <span class="rating rating-mr-2">{{ renderStarRating(5) }}</span>
                                        </label>
                                        <br>
                                        <label class="aiz-checkbox mb-3">
                                            <input
                                                type="radio"
                                                name="rating"
                                                value="4" @if ($rating==4) checked @endif
                                                onchange="filter()"
                                            >
                                            <span class="aiz-square-check"></span>
                                            <span class="rating rating-mr-2">{{ renderStarRating(4) }}</span>
                                            <span class="fs-14 fw-400 text-dark">{{ translate('And Up')}}</span>
                                        </label>
                                        <br>
                                        <label class="aiz-checkbox mb-3">
                                            <input
                                                type="radio"
                                                name="rating"
                                                value="3" @if ($rating==3) checked @endif
                                                onchange="filter()"
                                            >
                                            <span class="aiz-square-check"></span>
                                            <span class="rating rating-mr-2">{{ renderStarRating(3) }}</span>
                                            <span class="fs-14 fw-400 text-dark">{{ translate('And Up')}}</span>
                                        </label>
                                        <br>
                                        <label class="aiz-checkbox mb-3">
                                            <input
                                                type="radio"
                                                name="rating"
                                                value="2" @if ($rating==2) checked @endif
                                                onchange="filter()"
                                            >
                                            <span class="aiz-square-check"></span>
                                            <span class="rating rating-mr-2">{{ renderStarRating(2) }}</span>
                                            <span class="fs-14 fw-400 text-dark">{{ translate('And Up')}}</span>
                                        </label>
                                        <br>
                                        <label class="aiz-checkbox mb-3">
                                            <input
                                                type="radio"
                                                name="rating"
                                                value="1" @if ($rating==1) checked @endif
                                                onchange="filter()"
                                            >
                                            <span class="aiz-square-check"></span>
                                            <span class="rating rating-mr-2">{{ renderStarRating(1) }}</span>
                                            <span class="fs-14 fw-400 text-dark">{{ translate('And Up')}}</span>
                                        </label>
                                        <br>
                                    </div>
                                </div>

                                <!-- Brands -->
                                <div class="bg-white border mb-4 mx-3 mx-xl-0 mt-3 mt-xl-0">
                                    <div class="fs-16 fw-700 p-3">
                                        <a href="#collapse_3" class="dropdown-toggle filter-section text-dark d-flex align-items-center justify-content-between" data-toggle="collapse">
                                            {{ translate('Brands')}}
                                        </a>
                                    </div>
                                    <div class="collapse show px-3" id="collapse_3">
                                        <div class="row gutters-10">
                                            @foreach (get_brands_by_products($shop->user->id) as $key => $brand)
                                                <div class="col-6">
                                                    <label class="aiz-megabox d-block mb-3">
                                                        <input value="{{ $brand->slug }}" type="radio" onchange="filter()"
                                                            name="brand" @isset($brand_id) @if ($brand_id == $brand->id) checked @endif @endisset>
                                                        <span class="d-block aiz-megabox-elem rounded-0 p-3 border-transparent hov-border-primary">
                                                            <img src="{{ uploaded_asset($brand->logo) }}"
                                                                class="img-fit mb-2" alt="{{ $brand->getTranslation('name') }}">
                                                            <span class="d-block text-center">
                                                                <span
                                                                    class="d-block fw-400 fs-14">{{ $brand->getTranslation('name') }}</span>
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Contents -->
                    <div class="col-xl-9">
                        <!-- Top Filters -->
                        <div class="text-left mb-2">
                            <div class="row gutters-5 flex-wrap">
                                <div class="col-lg col-10">
                                    <h1 class="fs-20 fs-md-24 fw-700 text-dark">
                                        {{ translate('All Products') }}
                                    </h1>
                                </div>
                                <div class="col-2 col-lg-auto d-xl-none mb-lg-3 text-right">
                                    <button type="button" class="btn btn-icon p-0" data-toggle="class-toggle" data-target=".aiz-filter-sidebar">
                                        <i class="la la-filter la-2x"></i>
                                    </button>
                                </div>
                                <div class="col-6 col-lg-auto mb-3 w-lg-200px">
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

                      
                      
                    @php
$current_category_id = request()->get('category');
$current_category = \App\Models\Category::find($current_category_id);
$sub_categories = collect();

// Extract seller ID from the first product (assuming all are from the same seller)
$seller_id = optional($products->first())->user_id;

if ($current_category && $seller_id) {
    $sub_categories = \App\Models\Category::where('parent_id', $current_category->id)
        ->whereHas('products', function ($query) use ($seller_id) {
            $query->where('user_id', $seller_id);
        })
        ->get();
}
@endphp

@if ($sub_categories->isNotEmpty())
<div class="mb-4">
    <div class="row text-center">
        @foreach($sub_categories as $subcategory)
            <div class="col-4 col-sm-3 col-md-2 mb-3">
<a href="{{ url()->current() . '?category=' . $subcategory->id . '&user_id=' . $seller_id }}" class="text-reset d-block">
                    <div class="position-relative mb-2">
                        <img src="{{ uploaded_asset($subcategory->banner) }}" class="img-fluid rounded-circle border p-1" style="width: 100px; height: 100px; object-fit: cover;" alt="{{ $subcategory->getTranslation('name') }}">
                    </div>
                    <div class="fs-14 fw-600 text-truncate">{{ $subcategory->getTranslation('name') }}</div>
                </a>
            </div>
        @endforeach
    </div>
</div>
@endif

                        <!-- Products -->
                        <div class="px-3">
                            <div class="row gutters-16 row-cols-xxl-4 row-cols-xl-3 row-cols-lg-4 row-cols-md-3 row-cols-2 border-top border-left">
@php
$selected_category = request()->get('category');
$selected_seller = request()->get('user_id');
$filtered_products = $products;

if ($selected_seller) {
    $filtered_products = $filtered_products->where('user_id', $selected_seller);
}

if ($selected_category) {
    $category = \App\Models\Category::find($selected_category);

    if ($category) {
        // Check if it's a parent category
        if ($category->categories()->exists()) {
            // It's a main category, get all subcategory IDs
            $sub_ids = $category->categories()->pluck('id')->toArray();
            $filtered_products = $filtered_products->whereIn('category_id', $sub_ids);
        } else {
            // It's a subcategory, filter normally
            $filtered_products = $filtered_products->where('category_id', $selected_category);
        }
    }
}
@endphp


@foreach ($filtered_products as $key => $product)
                                    <div class="col border-right border-bottom has-transition hov-shadow-out z-1">
                                        @include('frontend.'.get_setting('homepage_select').'.partials.product_box_1',['product' => $product])
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="aiz-pagination mt-4">
                            {{ $products->appends(request()->input())->links() }}
                        </div>
                    </div>
                </div>
            </form>
        @else
            <!-- Top Selling Products Section -->
            <div class="px-3">
                <div class="row gutters-16 row-cols-xxl-6 row-cols-xl-5 row-cols-lg-4 row-cols-md-3 row-cols-2 border-left border-top">
                    @foreach ($products as $key => $product)
                        <div class="col border-bottom border-right overflow-hidden has-transition hov-shadow-out z-1">
                            @include('frontend.'.get_setting('homepage_select').'.partials.product_box_1',['product' => $product])
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="aiz-pagination mt-4 mb-4">
                {{ $products->links() }}
            </div>
        @endif
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