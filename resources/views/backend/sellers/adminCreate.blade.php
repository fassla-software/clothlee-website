@extends('backend.layouts.app')

@section('content')
    <!-- aiz-main-wrapper -->
    <div class="aiz-main-wrapper d-flex flex-column justify-content-md-center bg-white">
        <section class="bg-white overflow-hidden">
            <div class="row">
                <div class="col-xxl-10 col-xl-11 col-lg-12 col-md-10 mx-auto py-lg-4">
                    <div class="card shadow-none rounded-0 border-0">
                        <div class="row no-gutters">
                                    
                                <!-- Right Side -->
                                <div class="col-lg-12 p-4 p-lg-5 d-flex flex-column justify-content-center border right-content" style="height: auto;">

                                    <!-- Titles -->
                                    <div class="text-center text-lg-left">
                                        <h1 class="fs-20 fs-md-24 fw-700 text-primary" style="text-transform: uppercase;">{{ translate('Add New Store')}}</h1>
                                    </div>
                                    <!-- Register form -->
<div class="pt-3 pt-lg-4">
    <div class="">
        <form id="reg-form" class="form-default" role="form" action="{{ route('sellers.adminStore') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <!-- Left Column -->
                <div class="col-md-6">
                    <!-- Store Name -->
                    <div class="form-group">
                        <label for="shop_name" class="fs-12 fw-700 text-soft-dark">{{ translate('Store Name') }}</label>
                        <input type="text" class="form-control rounded-0" value="{{ old('shop_name') }}" placeholder="{{ translate('Shop Name') }}" name="shop_name" required>
                    </div>

                    <!-- Phone -->
                    <div class="form-group">
                        <label for="phone" class="fs-12 fw-700 text-soft-dark">{{ translate('Store Phone') }}</label>
                        <input type="text" class="form-control rounded-0" value="{{ old('phone') }}" placeholder="{{ translate('Phone') }}" name="phone" required>
                    </div>
                    <!-- Password -->
                    <div class="form-group">
                        <label for="password" class="fs-12 fw-700 text-soft-dark">{{ translate('Password') }}</label>
                        <div class="position-relative">
                            <input type="password" id="password" class="form-control rounded-0" placeholder="{{ translate('Password') }}" name="password" required>
                            <i class="password-toggle las la-2x la-eye" onclick="togglePassword('password', this)"></i>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-group">
                        <label for="password_confirmation" class="fs-12 fw-700 text-soft-dark">{{ translate('Confirm Password') }}</label>
                        <div class="position-relative">
                            <input type="password" id="password_confirmation" class="form-control rounded-0" placeholder="{{ translate('Confirm Password') }}" name="password_confirmation" required>
                            <i class="password-toggle las la-2x la-eye" onclick="togglePassword('password_confirmation', this)"></i>
                        </div>
                    </div>

                    <!-- Store Logo -->
                  <div class="form-group">
                    <label for="logo" class="fs-12 fw-700 text-soft-dark">{{ translate('Store Logo') }}</label>
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
                  </div>
                </div>

                <!-- Right Column -->
                <div class="col-md-6">
                    <!-- Address -->
                    <div class="form-group">
                        <label for="address" class="fs-12 fw-700 text-soft-dark">{{ translate('Address') }}</label>
                        <input type="text" class="form-control rounded-0" value="{{ old('address') }}" placeholder="{{ translate('Address') }}" name="address" required>
                    </div>

                    <!-- Social Media Links -->
                    <div class="form-group">
                        <label for="facebook" class="fs-12 fw-700 text-soft-dark">{{ translate('Facebook') }}</label>
                        <input type="text" class="form-control rounded-0" value="{{ old('facebook') }}" placeholder="{{ translate('Facebook') }}" name="facebook">
                    </div>

                    <div class="form-group">
                        <label for="instagram" class="fs-12 fw-700 text-soft-dark">{{ translate('Instagram') }}</label>
                        <input type="text" class="form-control rounded-0" value="{{ old('instagram') }}" placeholder="{{ translate('Instagram') }}" name="instagram">
                    </div>

                    <div class="form-group">
                        <label for="tiktok" class="fs-12 fw-700 text-soft-dark">{{ translate('Tiktok') }}</label>
                        <input type="text" class="form-control rounded-0" value="{{ old('tiktok') }}" placeholder="{{ translate('Tiktok') }}" name="tiktok">
                    </div>

                    <div class="form-group">
                        <label for="website" class="fs-12 fw-700 text-soft-dark">{{ translate('Website') }}</label>
                        <input type="text" class="form-control rounded-0" value="{{ old('website') }}" placeholder="{{ translate('Website') }}" name="website">
                    </div>
                  	<div class="form-group">
                        <label for="youtube" class="fs-12 fw-700 text-soft-dark">{{ translate('Youtube') }}</label>
                        <input type="text" class="form-control rounded-0" value="{{ old('youtube') }}" placeholder="{{ translate('Youtube') }}" name="youtube">
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-4">
                <button type="submit" class="btn btn-primary btn-block fw-600 rounded-0">{{ translate('Register Your Shop') }}</button>
            </div>
        </form>
    </div>
</div>

                                </div>
                            </div>
                        </div>
                        <!-- Go Back -->
                        <div class="mt-3 mr-4 mr-md-0">
                            <a href="{{ url()->previous() }}" class="ml-auto fs-14 fw-700 d-flex align-items-center text-primary" style="max-width: fit-content;">
                                <i class="las la-arrow-left fs-20 mr-1"></i>
                                {{ translate('Back to Previous Page')}}
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


function togglePassword(inputId, icon) {
        const input = document.getElementById(inputId);
        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove('la-eye');
            icon.classList.add('la-eye-slash');
        } else {
            input.type = "password";
            icon.classList.remove('la-eye-slash');
            icon.classList.add('la-eye');
        }
    }

    </script>
@endsection
