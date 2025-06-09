@extends('auth.layouts.authentication')

@section('content')
    <!-- aiz-main-wrapper -->
    <div class="aiz-main-wrapper d-flex flex-column justify-content-md-center bg-white">
        <section class="bg-white overflow-hidden">
            <div class="row">
                <div class="col-xxl-6 col-xl-9 col-lg-10 col-md-7 mx-auto py-lg-4">
                    <div class="card shadow-none rounded-0 border-0">
                        <div class="row no-gutters">
                            <!-- Left Side Image-->
                            <div class="col-lg-6">
                                    <img src="{{ uploaded_asset(get_setting('seller_register_page_image')) }}" alt="" class="img-fit h-100">
                                </div>
                                    
                                <!-- Right Side -->
                                <div class="col-lg-6 p-4 p-lg-5 d-flex flex-column justify-content-center border right-content" style="height: auto;">
                                   <div class="mb-3 mt-4 mx-auto mx-lg-0 text-center">
    <img src="https://www.clothlee.com/public/uploads/all/GZJp5j8foZcqDeFQ3miXh8oh3hRDpFIH5BNzq4yd.png" 
         alt="Site Icon" 
         style="max-height: 80px; width: auto; margin-top: 30px;">
</div>


                                    <!-- Titles -->
                                    <div class="text-center text-lg-left">
                                        <h1 class="fs-20 fs-md-24 fw-700 text-primary" style="text-transform: uppercase;">{{ translate('Register your Brand')}}</h1>
                                    </div>
                                    <!-- Register form -->
                                    <div class="pt-3 pt-lg-4">
                                        <div class="">
                                            <form id="reg-form" class="form-default" role="form" action="{{ route('shops.store') }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <!-- Store Name -->
                                                <div class="form-group">
                                                    <label for="shop_name" class="fs-12 fw-700 text-soft-dark">{{  translate('Brand Name') }}</label>
                                                    <input type="text" class="form-control rounded-0{{ $errors->has('shop_name') ? ' is-invalid' : '' }}" value="{{ old('shop_name') }}" placeholder="{{  translate('Brand Name') }}" name="shop_name" required>
                                                    @if ($errors->has('shop_name'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('shop_name') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>

                                                <div class="form-group">
                                                    <label for="phone"  class="fs-12 fw-700 text-soft-dark">{{ translate('Brand Phone')}}</label>
                                                    <input type="text" class="form-control rounded-0{{ $errors->has('phone') ? ' is-invalid' : '' }}" value="{{ old('phone') }}" placeholder="{{  translate('Phone') }}" name="phone" required>
                                                    @if ($errors->has('phone'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('phone') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>

                                                <!-- password -->
                                                <div class="form-group mb-0">
                                                    <label for="password" class="fs-12 fw-700 text-soft-dark">{{  translate('Password') }}</label>
                                                    <div class="position-relative">
                                                        <input type="password" class="form-control rounded-0{{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="{{  translate('Password') }}" name="password" required>
                                                        <i class="password-toggle las la-2x la-eye"></i>
                                                    </div>
                                                    <div class="text-right mt-1">
                                                        <span class="fs-12 fw-400 text-gray-dark">{{ translate('Password must contain at least 6 digits') }}</span>
                                                    </div>
                                                    @if ($errors->has('password'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('password') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>

                                                <!-- password Confirm -->
                                                <div class="form-group">
                                                    <label for="password_confirmation" class="fs-12 fw-700 text-soft-dark">{{  translate('Confirm Password') }}</label>
                                                    <div class="position-relative">
                                                        <input type="password" class="form-control rounded-0" placeholder="{{  translate('Confirm Password') }}" name="password_confirmation" required>
                                                        <i class="password-toggle las la-2x la-eye"></i>
                                                    </div>
                                                </div>
                                              
                                               <div class="form-group">
    <label for="logo" class="fs-12 fw-700 text-soft-dark">{{ translate('Brand Logo') }}</label>
    <div class="input-group">
        <div class="input-group-prepend">
            <label for="logo" class="input-group-text bg-soft-secondary font-weight-medium cursor-pointer">
                {{ translate('Browse') }}
            </label>
        </div>
        <input type="text" id="file-name-display" class="form-control rounded-0" placeholder="{{ translate('Choose File') }}" readonly>
        <input type="file" id="logo" name="logo" class="d-none" accept="image/*" onchange="updateFileName()">
    </div>
    <div class="file-preview box sm mt-2" id="file-preview">
        <!-- Preview area -->
    </div>

    {{-- ✅ Add this to show validation error --}}
    @if ($errors->has('logo'))
        <small class="text-danger d-block mt-2">
            {{ $errors->first('logo') }}
        </small>
    @endif
</div>



                                                <div class="form-group">
                                                    <label for="address" class="fs-12 fw-700 text-soft-dark">{{  translate('Address') }}</label>
                                                    <input type="text" class="form-control rounded-0{{ $errors->has('address') ? ' is-invalid' : '' }}" value="{{ old('address') }}" placeholder="{{  translate('Address') }}" name="address">
                                                    @if ($errors->has('address'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('address') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                              
												<div class="form-group">
                                                  <label for="facebook" class="fs-12 fw-700 text-soft-dark">{{ translate('Facebook') }}</label>
                                                  <input type="text" class="form-control rounded-0{{ $errors->has('facebook') ? ' is-invalid' : '' }}" 
                                                         value="{{ old('facebook') }}" 
                                                         placeholder="{{ translate('Facebook') }}" 
                                                         name="facebook">
                                                  @if ($errors->has('facebook'))
                                                      <span class="invalid-feedback" role="alert">
                                                          <strong>{{ $errors->first('facebook') }}</strong>
                                                      </span>
                                                  @endif
                                                  <small class="text-muted">{{ translate('Insert link with https') }}</small>
                                              </div>

                                              <div class="form-group">
                                                  <label for="instagram" class="fs-12 fw-700 text-soft-dark">{{ translate('Instagram') }}</label>
                                                  <input type="text" class="form-control rounded-0{{ $errors->has('instagram') ? ' is-invalid' : '' }}" 
                                                         value="{{ old('instagram') }}" 
                                                         placeholder="{{ translate('Instagram') }}" 
                                                         name="instagram">
                                                  @if ($errors->has('instagram'))
                                                      <span class="invalid-feedback" role="alert">
                                                          <strong>{{ $errors->first('instagram') }}</strong>
                                                      </span>
                                                  @endif
                                                  <small class="text-muted">{{ translate('Insert link with https') }}</small>
                                              </div>

                                              <div class="form-group">
                                                  <label for="tiktok" class="fs-12 fw-700 text-soft-dark">{{ translate('Tiktok') }}</label>
                                                  <input type="text" class="form-control rounded-0{{ $errors->has('tiktok') ? ' is-invalid' : '' }}" 
                                                         value="{{ old('tiktok') }}" 
                                                         placeholder="{{ translate('Tiktok') }}" 
                                                         name="tiktok">
                                                  @if ($errors->has('tiktok'))
                                                      <span class="invalid-feedback" role="alert">
                                                          <strong>{{ $errors->first('tiktok') }}</strong>
                                                      </span>
                                                  @endif
                                                  <small class="text-muted">{{ translate('Insert link with https') }}</small>
                                              </div>

                                              <div class="form-group">
                                                  <label for="website" class="fs-12 fw-700 text-soft-dark">{{ translate('Website') }}</label>
                                                  <input type="text" class="form-control rounded-0{{ $errors->has('website') ? ' is-invalid' : '' }}" 
                                                         value="{{ old('website') }}" 
                                                         placeholder="{{ translate('Website') }}" 
                                                         name="website">
                                                  @if ($errors->has('website'))
                                                      <span class="invalid-feedback" role="alert">
                                                          <strong>{{ $errors->first('website') }}</strong>
                                                      </span>
                                                  @endif
                                                  <small class="text-muted">{{ translate('Insert link with https') }}</small>
                                              </div>

                                              <div class="form-group">
                                                  <label for="youtube" class="fs-12 fw-700 text-soft-dark">{{ translate('Youtube') }}</label>
                                                  <input type="text" class="form-control rounded-0{{ $errors->has('youtube') ? ' is-invalid' : '' }}" 
                                                         value="{{ old('youtube') }}" 
                                                         placeholder="{{ translate('Youtube') }}" 
                                                         name="youtube">
                                                  @if ($errors->has('youtube'))
                                                      <span class="invalid-feedback" role="alert">
                                                          <strong>{{ $errors->first('youtube') }}</strong>
                                                      </span>
                                                  @endif
                                                  <small class="text-muted">{{ translate('Insert link with https') }}</small>
                                              </div>

                                              
                                                <!-- Recaptcha -->
                                                @if(get_setting('google_recaptcha') == 1)
                                                    <div class="form-group">
                                                        <div class="g-recaptcha" data-sitekey="{{ env('CAPTCHA_KEY') }}"></div>
                                                    </div>
                                                    @if ($errors->has('g-recaptcha-response'))
                                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                                            <strong>{{ $errors->first('g-recaptcha-response') }}</strong>
                                                        </span>
                                                    @endif
                                                @endif
                                            
                                                <!-- Submit Button -->
                                                <div class="mb-4 mt-4">
                                                    <button type="submit" class="btn btn-primary btn-block fw-600 rounded-0">{{  translate('Register Your Brand') }}</button>
                                                </div>
                                            </form>
                                        </div>
                                        <!-- Log In -->
                                        <p class="fs-12 text-gray mb-0">
                                            {{ translate('Already have an account?')}}
                                            <a href="{{ route('seller.login') }}" class="ml-2 fs-14 fw-700 animate-underline-primary">{{ translate('Log In')}}</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Go Back -->
                        <div class="mt-3 mr-4 mr-md-0">
                            <a href="https://www.clothlee.com/" class="ml-auto fs-14 fw-700 d-flex align-items-center text-primary" style="max-width: fit-content;">
    <i class="las la-arrow-left fs-20 mr-1"></i>
    {{ translate('Back to Home') }}
</a>

                        </div>
                    </div>
                </div>
        </section>
    </div>
@endsection

@section('script')
    @if(get_setting('google_recaptcha') == 1)
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endif

    <script type="text/javascript">
        @if(get_setting('google_recaptcha') == 1)
        // making the CAPTCHA  a required field for form submission
        $(document).ready(function(){
            $("#reg-form").on("submit", function(evt)
            {
                var response = grecaptcha.getResponse();
                if(response.length == 0)
                {
                //reCaptcha not verified
                    alert("please verify you are human!");
                    evt.preventDefault();
                    return false;
                }
                //captcha verified
                //do the rest of your validations here
                $("#reg-form").submit();
            });
        });
        @endif
      
      
function updateFileName() {
    const input = document.getElementById('logo');
    const display = document.getElementById('file-name-display');
    const preview = document.getElementById('file-preview');

    if (input.files.length > 0) {
        const file = input.files[0];

        // Create an image preview
        const reader = new FileReader();
        reader.onload = function (e) {
            preview.innerHTML = `<img src="${e.target.result}" alt="Preview" class="img-thumbnail" style="max-height: 150px;">`;
        };
        reader.readAsDataURL(file);
    } else {
        display.value = ""; // Reset the display
        preview.innerHTML = ""; // Clear the preview
    }
}


    </script>
@endsection