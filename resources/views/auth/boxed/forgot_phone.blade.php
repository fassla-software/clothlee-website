@extends('auth.layouts.authentication')

@section('content')
<div class="aiz-main-wrapper d-flex flex-column justify-content-md-center bg-white">
    <section class="bg-white overflow-hidden">
        <div class="row">
            <div class="col-xxl-6 col-xl-9 col-lg-10 col-md-7 mx-auto py-lg-4">
                <div class="card shadow-none rounded-0 border-0">
                    <div class="row no-gutters">
                        <div class="col-lg-6">
                            <img src="{{ uploaded_asset(get_setting('password_reset_page_image')) }}" alt="{{ translate('Password Reset Page Image') }}" class="img-fit h-100">
                        </div>

                        <div class="col-lg-6 p-4 p-lg-5 d-flex flex-column justify-content-center border right-content">
                            <div class="size-48px mb-3 mx-auto mx-lg-0">
                                <img src="{{ uploaded_asset(get_setting('site_icon')) }}" alt="{{ translate('Site Icon')}}" class="img-fit h-100">
                            </div>

                            <div class="text-center text-lg-left">
                                <h1 class="fs-20 fw-700 text-primary">{{ translate('Forgot Password') }}</h1>
                                <h5 class="fs-14 fw-400 text-dark">{{ translate('Enter your phone number to receive a reset code') }}</h5>
                            </div>

                            <form method="POST" action="{{ route('password.phone') }}" class="pt-3">
                                @csrf

                                <div class="form-group">
                                    <input type="tel" name="phone" class="form-control" placeholder="{{ translate('Phone Number') }}" required>
                                </div>

                                <div class="form-group mt-4">
                                    <button type="submit" class="btn btn-primary btn-block fw-700">{{ translate('Send Reset Code') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="mt-3 mr-4 mr-md-0">
                    <a href="{{ url()->previous() }}" class="ml-auto fs-14 fw-700 d-flex align-items-center text-primary" style="max-width: fit-content;">
                        <i class="las la-arrow-left fs-20 mr-1"></i>
                        {{ translate('Back to Previous Page') }}
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
