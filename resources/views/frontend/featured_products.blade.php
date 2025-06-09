@extends('frontend.layouts.app')

@section('content')
<section class="pt-4 mb-4">
    <div class="container">
       

        <!-- Products Section -->
        <div class="px-3">
            <div class="row row-cols-xxl-6 row-cols-xl-5 row-cols-lg-4 row-cols-md-3 row-cols-sm-2 row-cols-2 gutters-16 border-top border-left">
                @foreach ($featured_products as $product)
                    <div class="col text-center border-right border-bottom has-transition hov-shadow-out z-1">
                        @include('frontend.' . get_setting('homepage_select') . '.partials.product_box_1', ['product' => $product])
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-4 d-flex justify-content-center">
            {{ $featured_products->links() }}
        </div>
    </div>
</section>
@endsection
