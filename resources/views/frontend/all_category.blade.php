@extends('frontend.layouts.app')

@section('content')
    <!-- All Categories Page -->
    <section class="pt-4 mb-5 pb-3">
        <div class="container">
            @foreach ($categories as $key => $category)
               <!-- Parent Category Header -->
<div class="text-center mb-4">
    <a href="{{ route('products.category', $category->slug) }}" class="text-decoration-none text-reset d-block">
        <div class="mx-auto rounded-circle overflow-hidden border mb-2" style="width: 100px; height: 100px;">
            <img src="{{ uploaded_asset($category->banner) }}" alt="{{ $category->name }}" class="w-100 h-100 object-fit-cover">
        </div>
        <h5 class="fw-700 fs-18 text-dark">
            {{ $category->getTranslation('name') }}
        </h5>
    </a>
</div>


                <!-- Subcategories Grid -->
                <div class="row row-cols-3 row-cols-sm-4 row-cols-md-6 g-4 text-center mb-4">
                    @foreach ($category->childrenCategories as $child_category)
                        <div class="col">
                            <a href="{{ route('products.category', $child_category->slug) }}" class="text-decoration-none text-reset d-block">
                                <div class="mx-auto rounded-circle overflow-hidden border mb-2" style="width: 70px; height: 70px;">
                                    <img src="{{ uploaded_asset($child_category->banner) }}" alt="{{ $child_category->name }}" class="w-100 h-100 object-fit-cover">
                                </div>
                                <div class="fs-14 fw-500 text-dark">
                                    {{ $child_category->getTranslation('name') }}
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>

               <!-- Divider -->
<hr class="category-divider my-4">

            @endforeach
        </div>
    </section>
@endsection


<style>
    .object-fit-cover {
        object-fit: cover;
    }
  .category-divider {
    border: 0;
    height: 1px;
    background-color: #25ABBE;
}

</style>
