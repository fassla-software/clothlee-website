@extends('frontend.layouts.app')

@section('content')
<div class="position-relative">
    <div class="container">

        <!-- Page Header -->
        <section class="pt-4 mb-3 text-center">
            <h2 class="fw-bold fs-22 text-dark">{{ translate('Brands') }}</h2>
        </section>

        <!-- Brands List -->
        <section class="mb-3 pb-3">
            <div class="row row-cols-1 row-cols-sm-2 justify-content-center gx-3 gy-4">
                @foreach ($shops as $shop)
                    @if ($shop->user != null)
						<div class="col mb-4">
                            <div class="brand-box bg-white shadow-sm p-3 rounded-4 text-center d-flex flex-column align-items-center">
                                <a href="{{ route('shop.visit', $shop->slug) }}" class="d-block w-100">
                                    <div class="brand-image-box mb-2 mx-auto">
                                        <img src="{{ uploaded_asset($shop->logo) }}"
                                             alt="{{ $shop->name }}"
                                             class="img-fluid"
                                             onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder-rect.jpg') }}';">
                                    </div>
                                </a>
                                <h3 class="brand-name small-text fw-bold text-dark mb-1">{{ $shop->name }}</h3>
                                <div class="stars small-text text-muted mb-2">
                                    ★★★★★
                                </div>
                                <a href="{{ route('shop.visit', $shop->slug) }}"
                                   class="btn store-btn small-text w-100">
                                    {{ translate('Visit Store') }}
                                </a>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="aiz-pagination aiz-pagination-center mt-4">
                {{ $shops->links() }}
            </div>
        </section>
    </div>
</div>

<!-- CSS Styling -->
<style>
    body {
        background-color: #f5f5f5;
    }

    .brand-box {
        border-radius: 16px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.06);
        transition: all 0.2s ease-in-out;
        height: 100%;
  
    }

    .brand-box:hover {
        transform: translateY(-3px);
    }

    .brand-image-box {
        width: 100%;
        max-width: 90px;
        height: 90px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #ffffff;
        border-radius: 12px;
        overflow: hidden;
    }

    .brand-image-box img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    .small-text {
        font-size: 13px;
    }

    .store-btn {
        padding: 6px 12px;
        border: 1px solid #0d6efd;
        color: #0d6efd;
        background-color: #fff;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .store-btn:hover {
        background-color: #0d6efd;
        color: #fff;
    }

    .stars {
        letter-spacing: 3px;
        color: #ccc;
    }

    @media (min-width: 576px) {
        .row-cols-sm-2 > * {
            flex: 0 0 auto;
            width: 50%;
        }
    }
</style>
@endsection
