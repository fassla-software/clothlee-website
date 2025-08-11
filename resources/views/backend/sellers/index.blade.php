@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{translate('All Brands')}}</h1>
        </div>
    </div>
</div>

<div class="card">
    <form class="" id="sort_sellers" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col">
                <h5 class="mb-md-0 h6">{{ translate('Brands') }}</h5>
            </div>

            @can('delete_seller')
                <div class="dropdown mb-2 mb-md-0">
                    <button class="btn border dropdown-toggle" type="button" data-toggle="dropdown">
                        {{translate('Bulk Action')}}
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item confirm-alert" href="javascript:void(0)"  data-target="#bulk-delete-modal">{{translate('Delete selection')}}</a>
                    </div>
                </div>
            @endcan

            <div class="col-md-3 ml-auto">
                <select class="form-control aiz-selectpicker" name="approved_status" id="approved_status" onchange="sort_sellers()">
                    <option value="">{{translate('Filter by Approval')}}</option>
                    <option value="1"  @isset($approved) @if($approved == '1') selected @endif @endisset>{{translate('Approved')}}</option>
                    <option value="0"  @isset($approved) @if($approved == '0') selected @endif @endisset>{{translate('Non-Approved')}}</option>
                </select>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-0">
                  <input type="text" class="form-control" id="search" name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type name or email & Enter') }}">
                </div>
            </div>
        </div>

        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                <tr>

                    <th>
                        @if(auth()->user()->can('delete_seller'))
                            <div class="form-group">
                                <div class="aiz-checkbox-inline">
                                    <label class="aiz-checkbox">
                                        <input type="checkbox" class="check-all">
                                        <span class="aiz-square-check"></span>
                                    </label>
                                </div>
                            </div>
                        @else
                            #
                        @endif
                    </th>
                    <th>{{translate('Name')}}</th>
                    <th data-breakpoints="lg">{{translate('Phone')}}</th>
                    <th data-breakpoints="lg">{{translate('Commision')}}</th>
                    <th data-breakpoints="lg">{{translate('Verification Info')}}</th>
                    <th data-breakpoints="lg">{{translate('Approval')}}</th>
                    <th data-breakpoints="lg">{{ translate('Num. of Products') }}</th>
                    <th data-breakpoints="lg">{{ translate('Due to brand') }}</th>
                    <th data-breakpoints="lg">{{ translate('Status') }}</th>
                    <th width="10%">{{translate('Options')}}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($shops as $key => $shop)
                    <tr>
                        <td>
                            @if(auth()->user()->can('delete_seller'))
                                <div class="form-group">
                                    <div class="aiz-checkbox-inline">
                                        <label class="aiz-checkbox">
                                            <input type="checkbox" class="check-one" name="id[]" value="{{$shop->id}}">
                                            <span class="aiz-square-check"></span>
                                        </label>
                                    </div>
                                </div>
                            @else
                                {{ ($key+1) + ($shops->currentPage() - 1)*$shops->perPage() }}
                            @endif
                        </td>
                        <td>@if($shop->user->banned == 1) <i class="fa fa-ban text-danger" aria-hidden="true"></i> @endif {{$shop->name}}</td>
                        <td>{{$shop->user->phone}}</td>
<td id="unit-price-{{ $shop->id }}">
    <span class="price-text" id="price-text-{{ $shop->id }}">{{ $shop->commision }}</span>
    <input type="number" id="price-input-{{ $shop->id }}" value="{{ $shop->commision }}" class="d-none" step="0.01" style="width: 70px; height: 30px; font-size: 0.8rem;">
    
    <!-- Edit button with Font Awesome edit icon -->
    <button type="button" class="btn btn-sm btn-warning" onclick="editPrice({{ $shop->id }})" id="edit-button-{{ $shop->id }}" style="padding: 0.1rem 0.3rem;; font-size: 0.8rem;">
        <i class="fa fa-edit"></i>
    </button>
    
    <!-- Apply button with Font Awesome check icon -->
    <button type="button" class="btn btn-sm btn-success d-none" onclick="applyPrice({{ $shop->id }})" id="apply-button-{{ $shop->id }}" style="padding: 0.1rem 0.3rem; font-size: 0.8rem;">
        <i class="fa fa-check"></i>
    </button>
</td>
                        <td>
                            @if ($shop->verification_status != 1 && $shop->verification_info != null)
                                <a href="{{ route('sellers.show_verification_request', $shop->id) }}">
                                    <span class="badge badge-inline badge-info">{{translate('Show')}}</span>
                                </a>
                            @endif
                        </td>
                        <td>
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input
                                    @can('approve_seller') onchange="update_approved(this)" @endcan
                                    value="{{ $shop->id }}" type="checkbox"
                                    <?php if($shop->verification_status == 1) echo "checked";?>
                                    @cannot('approve_seller') disabled @endcan
                                >
                                <span class="slider round"></span>
                            </label>
                        </td>
                        <td>{{ $shop->user->products->count() }}</td>
                     <td>
                        <div class="display-mode">
                            @if ($shop->admin_to_pay >= 0)
                                <span class="amount-value">{{ single_price($shop->admin_to_pay) }}</span>
                            @else
                                <span class="amount-value">{{ single_price(abs($shop->admin_to_pay)) }}</span> ({{ translate('Due to Admin') }})
                            @endif
                            <button type="button" class=" btn btn-sm btn-warning edit-btn">  <i class="fa fa-edit"></i> </button>
                        </div>

                        <div class="edit-mode" style="display: none;">
                            <div class="input-group">
                                <input type="number" 
                                       name="admin_to_pay" 
                                       class="form-control amount-input" 
                                       value="{{ $shop->admin_to_pay }}" 
                                       step="0.01"
                                       data-id="{{ $shop->id }}"
                                       >
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-sm btn-success save-btn">  <i class="fa fa-check"></i></button>
                                </div>
                            </div>
                            <div class="error-message text-danger mt-1"></div>
                        </div>
              	      </td>

                        <td>
                            @if($shop->user->banned)
                                <span class="badge badge-inline badge-danger">{{ translate('Ban') }}</span>
                 [2025-07-02 01:04:18] production.ERROR: Attempt to read property "banned" on null {"exception":"[object] (ErrorException(code: 0): Attempt to read property \"banned\" on null at /home/cloth/htdocs/www.clothlee.com/app/Http/Controllers/HomeController.php:343)
[stacktrace]
#0 /home/cloth/htdocs/www.clothlee.com/vendor/laravel/framework/src/Illuminate/Foundation/Bootstrap/HandleExceptions.php(272): Illuminate\\Foundation\\Bootstrap\\HandleExceptions->handleError()
#1 /home/cloth/htdocs/www.clothlee.com/app/Http/Controllers/HomeController.php(343): Illuminate\\Foundation\\Bootstrap\\HandleExceptions->{closure:Illuminate\\Foundation\\Bootstrap\\HandleExceptions::forwardsTo():271}()
#2 /home/cloth/htdocs/www.clothlee.com/vendor/laravel/framework/src/Illuminate/Routing/Controller.php(54): App\\Http\\Controllers\\HomeController->shop()
#3 /home/cloth/htdocs/www.clothlee.com/vendor/laravel/framework/src/Illuminate/Routing/ControllerDispatcher.php(43): Illuminate\\Routing\\Controller->callAction()
#4 /home/cloth/htdocs/www.clothlee.com/vendor/laravel/framework/src/Illuminate/Routing/Route.php(259): Illuminate\\Routing\\ControllerDispatcher->dispatch()
#5 /home/cloth/htdocs/www.clothlee.com/vendor/laravel/framework/src/Illuminate/Routing/Route.php(205): Illuminate\\Routing\\Route->runController()
#6 /home/cloth/htdocs/www.clothlee.com/vendor/laravel/framework/src/Illuminate/Routing/Router.php(798): Illuminate\\Routing\\Route->run()
#7 /home/cloth/htdocs/www.clothlee.com/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(141): Illuminate\\Routing\\Router->{closure:Illuminate\\Routing\\Router::runRouteWithinStack():797}()
#8 /home/cloth/htdocs/www.clothlee.com/app/Http/Middleware/CheckForMaintenanceMode.php(64): Illuminate\\Pipeline\\Pipeline->{closure:Illuminate\\Pipeline\\Pipeline::prepareDestination():139}()
#9 /home/cloth/htdocs/www.clothlee.com/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(180): App\\Http\\Middleware\\CheckForMaintenanceMode->handle()
#10 /home/cloth/htdocs/www.clothlee.com/app/Http/Middleware/HttpsProtocol.php(20): Illuminate\\Pipeline\\Pipeline->{closure:{closure:Illuminate\\Pipeline\\Pipeline::carry():155}:156}()
#11 /home/cloth/htdocs/www.clothlee.com/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(180): App\\Http\\Middleware\\HttpsProtocol->handle()
#12 /home/cloth/htdocs/www.clothlee.com/app/Http/Middleware/Language.php(35): Illuminate\\Pipeline\\Pipeline->{closure:{closure:Illuminate\\Pipeline\\Pipeline::carry():155}:156}()
#13 /home/cloth/htdocs/www.clothlee.com/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(180): App\\Http\\Middleware\\Language->handle()
#14 /home/cloth/htdocs/www.clothlee.com/vendor/laravel/framework/src/Illuminate/Routing/Middleware/SubstituteBindings.php(50): Illuminate\\Pipeline\\Pipeline->{closure:{closure:Illuminate\\Pipeline\\Pipeline::carry():155}:156}()
#15 /home/cloth/htdocs/www.clothlee.com/vendor/laravel/framework/src/Illumin           @else
                                <span class="badge badge-inline badge-success">{{ translate('Regular') }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn btn-sm btn-circle btn-soft-primary btn-icon dropdown-toggle no-arrow" data-toggle="dropdown" href="javascript:void(0);" role="button" aria-haspopup="false" aria-expanded="false">
                                    <i class="las la-ellipsis-v"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-xs">
                                    @can('view_seller_profile')
                                        <a href="javascript:void();" onclick="show_seller_profile('{{$shop->id}}');"  class="dropdown-item">
                                            {{translate('Profile')}}
                                        </a>
                                    @endcan
                                    @can('login_as_seller')
                                        <a href="{{route('sellers.login', encrypt($shop->id))}}" class="dropdown-item">
                                            {{translate('Log in as this Seller')}}
                                        </a>
                                    @endcan
                                    @can('pay_to_seller')
                                        <a href="javascript:void();" onclick="show_seller_payment_modal('{{$shop->id}}');" class="dropdown-item">
                                            {{translate('Go to Payment')}}
                                        </a>
                                    @endcan
                                    @can('seller_payment_history')
                                        <a href="{{route('sellers.payment_history', encrypt($shop->user_id))}}" class="dropdown-item">
                                            {{translate('Payment History')}}
                                        </a>
                                    @endcan
                                    @can('edit_seller')
                                        <a href="{{route('sellers.edit', encrypt($shop->id))}}" class="dropdown-item">
                                            {{translate('Edit')}}
                                        </a>
                                    @endcan
                                    @can('ban_seller')
                                        @if($shop->user->banned != 1)
                                            <a href="javascript:void();" onclick="confirm_ban('{{route('sellers.ban', $shop->id)}}');" class="dropdown-item">
                                                {{translate('Ban this seller')}}
                                                <i class="fa fa-ban text-danger" aria-hidden="true"></i>
                                            </a>
                                        @else
                                            <a href="javascript:void();" onclick="confirm_unban('{{route('sellers.ban', $shop->id)}}');" class="dropdown-item">
                                                {{translate('Unban this seller')}}
                                                <i class="fa fa-check text-success" aria-hidden="true"></i>
                                            </a>
                                        @endif
                                    @endcan
                                    @can('delete_seller')
                                        <a href="javascript:void();" class="dropdown-item confirm-delete" data-href="{{route('sellers.destroy', $shop->id)}}" class="">
                                            {{translate('Delete')}}
                                        </a>
                                    @endcan
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
              {{ $shops->appends(request()->input())->links() }}
            </div>
        </div>
    </form>
</div>

@endsection

@section('modal')
	<!-- Delete Modal -->
	@include('modals.delete_modal')
    <!-- Bulk Delete modal -->
    @include('modals.bulk_delete_modal')

	<!-- Seller Profile Modal -->
	<div class="modal fade" id="profile_modal">
		<div class="modal-dialog">
			<div class="modal-content" id="profile-modal-content">

			</div>
		</div>
	</div>

	<!-- Seller Payment Modal -->
	<div class="modal fade" id="payment_modal">
	    <div class="modal-dialog">
	        <div class="modal-content" id="payment-modal-content">

	        </div>
	    </div>
	</div>

	<!-- Ban Seller Modal -->
	<div class="modal fade" id="confirm-ban">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title h6">{{translate('Confirmation')}}</h5>
					<button type="button" class="close" data-dismiss="modal">
					</button>
				</div>
				<div class="modal-body">
                    <p>{{translate('Do you really want to ban this seller?')}}</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-light" data-dismiss="modal">{{translate('Cancel')}}</button>
					<a class="btn btn-primary" id="confirmation">{{translate('Proceed!')}}</a>
				</div>
			</div>
		</div>
	</div>

	<!-- Unban Seller Modal -->
	<div class="modal fade" id="confirm-unban">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title h6">{{translate('Confirmation')}}</h5>
						<button type="button" class="close" data-dismiss="modal">
						</button>
					</div>
					<div class="modal-body">
							<p>{{translate('Do you really want to unban this seller?')}}</p>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-light" data-dismiss="modal">{{translate('Cancel')}}</button>
						<a class="btn btn-primary" id="confirmationunban">{{translate('Proceed!')}}</a>
					</div>
				</div>
			</div>
		</div>
@endsection

@section('script')
    <script type="text/javascript">
        $(document).on("change", ".check-all", function() {
            if(this.checked) {
                // Iterate each checkbox
                $('.check-one:checkbox').each(function() {
                    this.checked = true;
                });
            } else {
                $('.check-one:checkbox').each(function() {
                    this.checked = false;
                });
            }

        });

        function show_seller_payment_modal(id){
            $.post('{{ route('sellers.payment_modal') }}',{_token:'{{ @csrf_token() }}', id:id}, function(data){
                $('#payment_modal #payment-modal-content').html(data);
                $('#payment_modal').modal('show', {backdrop: 'static'});
                $('.demo-select2-placeholder').select2();
            });
        }

        function show_seller_profile(id){
            $.post('{{ route('sellers.profile_modal') }}',{_token:'{{ @csrf_token() }}', id:id}, function(data){
                $('#profile_modal #profile-modal-content').html(data);
                $('#profile_modal').modal('show', {backdrop: 'static'});
            });
        }

        function update_approved(el){
            if('{{env('DEMO_MODE')}}' == 'On'){
                AIZ.plugins.notify('info', '{{ translate('Data can not change in demo mode.') }}');
                return;
            }

            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('{{ route('sellers.approved') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('Approved sellers updated successfully') }}');
                }
                else{
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function sort_sellers(el){
            $('#sort_sellers').submit();
        }

        function confirm_ban(url)
        {
            if('{{env('DEMO_MODE')}}' == 'On'){
                AIZ.plugins.notify('info', '{{ translate('Data can not change in demo mode.') }}');
                return;
            }

            $('#confirm-ban').modal('show', {backdrop: 'static'});
            document.getElementById('confirmation').setAttribute('href' , url);
        }

        function confirm_unban(url)
        {
            if('{{env('DEMO_MODE')}}' == 'On'){
                AIZ.plugins.notify('info', '{{ translate('Data can not change in demo mode.') }}');
                return;
            }

            $('#confirm-unban').modal('show', {backdrop: 'static'});
            document.getElementById('confirmationunban').setAttribute('href' , url);
        }

        function bulk_delete() {
            var data = new FormData($('#sort_sellers')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('bulk-seller-delete')}}",
                type: 'POST',
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                success: function (response) {
                    if(response == 1) {
                        location.reload();
                    }
                }
            });
        }
      
function editPrice(productId) {
    // Show the input field and Apply button, hide the original price text and Edit button
    document.getElementById('price-text-' + productId).classList.add('d-none');
    document.getElementById('price-input-' + productId).classList.remove('d-none');
    document.getElementById('apply-button-' + productId).classList.remove('d-none');
    document.getElementById('edit-button-' + productId).classList.add('d-none');
}

function applyPrice(productId) {
    const newPrice = document.getElementById('price-input-' + productId).value;
	
    // Ensure a valid price is entered
    if (newPrice && !isNaN(newPrice) && parseFloat(newPrice) >= 0) {

        // First, update the price
        $.ajax({
            url: '/admin/sellers/' + productId + '/update-commision',
            method: 'POST',
            data: {
                commision: newPrice,
                _token: '{{ csrf_token() }}' // Make sure to include the CSRF token
            },
            success: function(response) {
                console.log("commision updated successfully");

                // Update the price text and hide the input field
                document.getElementById('price-text-' + productId).textContent = newPrice;
                document.getElementById('price-text-' + productId).classList.remove('d-none');
                document.getElementById('price-input-' + productId).classList.add('d-none');
                document.getElementById('apply-button-' + productId).classList.add('d-none');
                document.getElementById('edit-button-' + productId).classList.remove('d-none');

            },
            error: function(error) {
                console.log("Error:", error);
                alert('Error updating price.');
            }
        });
    } else {
        alert('Please enter a valid price.');
    }
}
   $(document).ready(function() {
    // Show edit form
    $(document).on('click', '.edit-btn', function() {
        const td = $(this).closest('td');
        td.find('.display-mode').hide();
        td.find('.edit-mode').show();
        td.find('.amount-input').focus();
    });

    // Save changes via AJAX
    $(document).on('click', '.save-btn', function() {
        const td = $(this).closest('td');
        const input = td.find('.amount-input');
        const newValue = input.val();
        const shopId = input.data('id'); // ✅ Use data-id

        td.find('.error-message').text('');

        if (newValue === '' || isNaN(newValue)) {
            td.find('.error-message').text('Please enter a valid amount.');
            return;
        }

        $.ajax({
            url: '/admin/brands/' + shopId + '/update-admin-to-pay',
            method: 'PUT',
            data: {
                _token: '{{ csrf_token() }}',
                admin_to_pay: newValue
            },
            success: function(response) {
                if (response.success) {
                    const amountValue = td.find('.amount-value');
                    if (newValue >= 0) {
                        amountValue.text(response.formatted_amount);
                    } else {
                        amountValue.text(response.formatted_amount + ' ({{ translate("Due to Admin") }})');
                    }
                    td.find('.edit-mode').hide();
                    td.find('.display-mode').show();
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    td.find('.error-message').text(xhr.responseJSON.message || 'Validation error');
                } else {
                    td.find('.error-message').text(xhr.responseJSON.message);
                }
            }
        });
    });

    // Save on Enter key
    $(document).on('keypress', '.amount-input', function(e) {
        if (e.which === 13) {
            $(this).closest('td').find('.save-btn').click();
            return false;
        }
    });
});

    </script>
@endsection
