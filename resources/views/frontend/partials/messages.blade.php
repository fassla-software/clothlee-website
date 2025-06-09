@if ($conversation->product_id)
    @php
        $product = \App\Models\Product::find($conversation->product_id);
    @endphp

    @if ($product)
        <div class="mb-4 border rounded p-3 d-flex align-items-center gap-3 shadow-sm">
            <img src="{{ uploaded_asset($product->thumbnail_img) }}"
                 alt="{{ $product->name }}"
                 class="img-thumbnail"
                 style="height: 80px; width: auto;">
            <div>
<h6 class="mb-0">{{ optional($product->main_category)->name ?? translate('Unknown Category') }}</h6>
                <small class="text-muted">{{ translate('This conversation is related to this product') }}</small>
            </div>
        </div>
    @endif
@endif


@foreach ($conversation->messages as $key => $message)
    @if ($message->user_id == Auth::user()->id)
        <div class="block block-comment mb-3">
            <div class="d-flex flex-row-reverse">
                <div class="pl-3">
                    <div class="block-image">
                        @if (Auth::user()->id != $message->user_id && $message->user->shop != null)
                            <a href="{{ route('shop.visit', $message->user->shop->slug) }}" class="avatar avatar-sm mr-3">
                                <img  class="" src="{{ uploaded_asset($message->user->shop->logo) }}" 
                                    onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-place.png') }}';">
                            </a>
                        @else
                        <span class="avatar avatar-sm mr-3">
                            <img class="" @if($message->user != null) src="{{ uploaded_asset($message->user->avatar_original) }}" @endif 
                                onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-place.png') }}';">
                        </span>
                        @endif
                    </div>
                </div>
                <div class="flex-grow ml-5 pl-5">
                    <div class="px-3 py-2 border rounded text-right">
                        {{ $message->message }}
                    </div>
                    <span class="comment-date alpha-7 small mt-1 d-block text-right">
                        {{ date('h:i:m d-m-Y', strtotime($message->created_at)) }}
                    </span>
                </div>
            </div>
        </div>
    @else
        <div class="block block-comment mb-3">
            <div class="d-flex">
                <div class="pr-3">
                    <div class="block-image">
                        @if (Auth::user()->id != $message->user_id && $message->user->shop != null)
                            <a href="{{ route('shop.visit', $message->user->shop->slug) }}" class="avatar avatar-sm mr-3">
                                <img  class="" src="{{ uploaded_asset($message->user->shop->logo) }}" 
                                    onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-place.png') }}';">
                            </a>
                        @else
                            <span class="avatar avatar-sm mr-3">
                                <img @if($message->user != null) src="{{ uploaded_asset($message->user->avatar_original) }}" @endif 
                                    onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-place.png') }}';">
                            </span>
                        @endif
                    </div>
                </div>
                <div class="flex-grow mr-5 pr-5">
                    <div class="px-3 py-2 border rounded">
                        {{ $message->message }}
                    </div>
                    <span class="comment-date alpha-7 small mt-1 d-block">
                        {{ date('h:i:m d-m-Y', strtotime($message->created_at)) }}
                    </span>
                </div>
            </div>
        </div>
    @endif
@endforeach
