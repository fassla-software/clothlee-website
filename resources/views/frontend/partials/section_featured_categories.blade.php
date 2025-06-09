@if (count($featured_categories) > 0)
    <section class="py-3 bg-light">
        <div class="container-fluid d-flex justify-content-center">
            <div class="hot-deals-box rounded bg-white shadow-sm p-3">
                <!-- Section Header -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="fs-22 fw-bold text-dark">
                        {{ translate('Featured Categories') }}
                    </h3>
                </div>

                <!-- Categories Carousel -->
                <div class="px-xl-0">
                    <div id="featuredCategoriesCarouselWrapper" class="position-relative">
                        <div id="featuredCategoriesCarousel" class="aiz-carousel arrow-none" 
                            data-items="7" 
                            data-xl-items="6" 
                            data-lg-items="5" 
                            data-md-items="4" 
                            data-sm-items="3" 
                            data-xs-items="2" 
                            data-dots="true" 
                            data-infinite="false">
                            
                            @foreach ($featured_categories as $category)
                            <div class="carousel-box position-relative">
                                <a href="{{ route('products.category', $category->slug) }}" class="product-link text-center d-flex flex-column align-items-center">
                                    <div class="img w-100px h-100px">
                                        <img class="lazyload img-fit product-hover" 
                                            src="{{ static_asset('assets/img/placeholder.jpg') }}" 
                                            data-src="{{ isset($category->bannerImage->file_name) ? my_asset($category->bannerImage->file_name) : static_asset('assets/img/placeholder.jpg') }}" 
                                            alt="{{ $category->getTranslation('name') }}"
                                            onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                                    </div>
                                    <div class="fs-16 mt-2 text-dark fw-bold">
                                        {{ $category->getTranslation('name') }}
                                    </div>
                                </a>
                            </div>
                            @endforeach
                        </div>

                        <!-- Fix for Dots Position -->
                        <div class="carousel-dots-container">
                            <ul id="featuredCategoriesCarouselDots"></ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif

<!-- Custom Styling -->
<style>
    .hot-deals-box {
        width: 100%;
        max-width: 1370px;
        margin: auto;
        min-height: 220px;
        background: #fff;
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
        margin-top: 10px;
    }
    .aiz-carousel .slick-dots {
        position: relative !important;
        bottom: -15px !important;
        display: flex !important;
        justify-content: center;
        align-items: center;
    }
</style>

<script>
    $(document).ready(function () {
        var $carousel = $('#featuredCategoriesCarousel');

        // Fix dots positioning
        $('#featuredCategoriesCarouselDots').html($('#featuredCategoriesCarousel .slick-dots').html());
    });
</script>
