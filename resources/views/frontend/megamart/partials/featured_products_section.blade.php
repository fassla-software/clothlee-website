@if (count(get_featured_products()) > 0)
    @php 
        $lang = get_system_language()->code;
        $homeBanner1Images = get_setting('home_banner1_images', null, $lang);
        $todays_deal_banner = get_setting('todays_deal_banner', null, $lang);

        // Match the number of products per slide with Today's Deal
        $xxl_items = $todays_deal_banner != null ? 5 : 7;
        $xl_items = $todays_deal_banner != null ? 4 : 6;
        $lg_items = 4;
        $md_items = 3;
        $sm_items = 2;
        $xs_items = 2;
    @endphp
    <section class="py-3 bg-white">
        <div class="container-fluid d-flex justify-content-center">
            <div class="hot-deals-box rounded bg-white">
                <!-- Section Header -->
          <div class="d-flex align-items-center justify-content-between px-4 px-xl-5 pt-4 pb-2">
    <h3 class="fs-16 fs-md-20 fw-700 mb-0 text-black">{{ translate("Hot Deals") }}</h3>
    <a href="{{ route('featured.products') }}" class="text-primary fs-14 fw-600">
        {{ translate('View All') }}
    </a>
</div>


                <!-- Products Carousel -->
                <div class="px-xl-0">
                    <div id="hotDealsCarouselWrapper" class="position-relative">
                        <div id="hotDealsCarousel" class="aiz-carousel arrow-none" 
                            data-items="{{ $xxl_items }}" 
                            data-xl-items="{{ $xl_items }}" 
                            data-lg-items="{{ $lg_items }}" 
                            data-md-items="{{ $md_items }}" 
                            data-sm-items="{{ $sm_items }}" 
                            data-xs-items="{{ $xs_items }}" 
                            data-dots="false" 
                            data-infinite="false"
                          	data-autoplay="true">
                            
                            @foreach (get_featured_products() as $key => $product)
                            <div class="carousel-box position-relative">
                                <a href="{{ route('product', $product->slug) }}" class="product-link text-center d-flex flex-column align-items-center">
                                   <!-- Image -->
<div class="img w-80px h-80px">
    <img class="lazyload img-fit product-hover" 
        src="{{ static_asset('assets/img/placeholder.jpg') }}" 
        data-src="{{ get_image($product->thumbnail) }}" 
        alt="{{ $product->getTranslation('name') }}"
        onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
</div>

<!-- Subcategory Name Below Image -->
@php
    // Fetch the first subcategory of the product
    $subcategory = $product->categories->where('level', 1)->first();
@endphp
<div class="fs-14 text-center text-black fw-600 mt-2">
    {{ $subcategory ? $subcategory->getTranslation('name') : '-' }}
</div>

                                    <div class="fs-16 mt-2 text-dark fw-bold">
                                        {{ home_discounted_base_price($product) }}
                                    </div>
                                </a>
                            </div>
                            @endforeach
                        </div>

                        <!-- Fix for Dots Position -->
                        <div class="carousel-dots-container">
                            <ul id="hotDealsCarouselDots"></ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>   

    <!-- Custom Styling -->
    <style>
        .hot-deals-box {
            width: 100%;
            max-width: 1370px;
            margin: auto;
            min-height: 220px;
            background: #fff !important;
        }
        .img-fit {
            object-fit: contain !important;
            width: 100%;
            height: 100%;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .product-hover:hover {
            transform: scale(1.08);
            box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.15);
            border-radius: 8px;
        }
        .product-link {
            text-decoration: none;
            color: inherit;
        }

        /* Move dots under the carousel */
        .carousel-dots-container {
            text-align: center;
            margin-top: 10px; /* Space between carousel and dots */
        }
        .aiz-carousel .slick-dots {
            position: relative !important;
            bottom: -15px !important; /* Move dots lower */
            display: flex !important;
            justify-content: center;
            align-items: center;
        }
      /* Hide any unwanted list bullets */
ul.slick-dots {
    list-style: none !important;
    padding: 0 !important;
    margin: 0 auto !important;
}

/* Ensure dots are centered and properly displayed */
.slick-dots {
    text-align: center !important;
}

/* Hide any unexpected list dots that might appear */
.slick-dots li {
    display: inline-block !important;
}

/* Hide any unwanted extra elements */
.slick-dots li:before,
.slick-dots li::marker {
    display: none !important;
}

/* If additional unwanted elements appear */
ul, ol {
    list-style: none !important;
    padding: 0;
    margin: 0;
}

/* Remove unwanted extra elements that are rendering */
.carousel-dots-container ul {
    display: none !important;
}

      
      
      /* Only apply to Hot Deals and Today's Deal sections */
.hot-deals-box .carousel-box, 
.todays-deal-box .carousel-box {
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    justify-content: center !important;
    background: #fff !important;
    padding: 10px !important;
    border-radius: 8px !important;
    box-shadow: 0px 3px 10px rgba(0, 0, 0, 0.08) !important;
    min-width: 140px !important;
    max-width: 160px !important;
    height: 220px !important;
    text-align: center !important;
    margin: 5px !important; /* Reduce space between cards */
}

/* Ensure Images are Centered */
.hot-deals-box .img, 
.todays-deal-box .img {
    width: 90px !important;
    height: 90px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    margin: 0 auto !important;
}

/* Scale images properly */
.hot-deals-box .product-item img, 
.todays-deal-box .product-item img {
    width: 100% !important;
    height: 100% !important;
    object-fit: contain !important;
}

/* Adjust Text Alignment and Price */
.hot-deals-box .fs-16, 
.todays-deal-box .fs-16 {
    font-size: 14px !important;
    font-weight: bold !important;
    color: #000 !important;
    margin-top: 8px !important;
}

.hot-deals-box .fw-bold, 
.todays-deal-box .fw-bold {
    font-size: 16px !important;
    font-weight: 700 !important;
    color: #0088cc !important;
    margin-top: 5px !important;
}

/* Reduce Extra Spacing Between Products */
.hot-deals-box .aiz-carousel .slick-track, 
.todays-deal-box .aiz-carousel .slick-track {
    display: flex !important;
    gap: 5px !important; /* Reduce unnecessary space */
    justify-content: center !important;
}

/* Mobile View Adjustments */
@media (max-width: 767px) {
    .hot-deals-box .carousel-box, 
    .todays-deal-box .carousel-box {
        min-width: 120px !important;
        max-width: 140px !important;
        height: 200px !important;
    }
}

      
      /* Add small bottom margin in PC view */
@media (min-width: 992px) {
    .hot-deals-box .carousel-box, 
    .todays-deal-box .carousel-box {
        margin-bottom: 10px !important; /* Small bottom margin to fully show the card */
    }
}

    </style>

    <script>
        $(document).ready(function () {
            var $carousel = $('#hotDealsCarousel');

            $('#prevArrow').click(function() {
                $carousel.slick('slickPrev'); 
            });

            $('#nextArrow').click(function() {
                $carousel.slick('slickNext'); 
            });

            // Ensure dots are placed under the carousel
            $('#hotDealsCarouselDots').html($('#hotDealsCarousel .slick-dots').html());
        });
    </script>
@endif
