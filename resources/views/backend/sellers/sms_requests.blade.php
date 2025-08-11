@extends('backend.layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="fs-18 mb-0">{{translate('Shop SMS Requests')}}</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>{{translate('Shop Logo')}}</th>
                            <th>{{translate('Shop Name')}}</th>
                            <th>{{translate('Total Requests')}}</th>
                            <th>{{translate('Action')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($shops as $shop)
                        <tr>
                            <td>
                                <img src="{{ uploaded_asset($shop->logo) }}" 
                                     alt="{{$shop->name}}" 
                                     class="h-50px"
                                     onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                            </td>
                            <td>{{$shop->name}}</td>
                            <td>{{$shop->smsRequests->count()}}</td>
                            <td>
                                <button class="btn btn-soft-primary btn-icon btn-circle btn-sm" 
                                        onclick="showRequests({{$shop->id}})" 
                                        title="{{translate('View Requests')}}">
                                    <i class="las la-eye"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- SMS Requests Modal -->
<div class="modal fade" id="smsRequestsModal" tabindex="-1" role="dialog" aria-labelledby="smsRequestsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="smsRequestsModalLabel">{{translate('SMS Requests')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>{{translate('Phone')}}</th>
                            <th>{{translate('Message')}}</th>
                            <th>{{translate('Status')}}</th>
                            <th>{{translate('Action')}}</th>
                        </tr>
                    </thead>
                    <tbody id="requestsTableBody">
                        <!-- Requests will be loaded here dynamically -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{translate('Close')}}</button>
                <button type="button" class="btn btn-primary" onclick="sendAllRequests(currentShopId)">{{translate('Send All Requests')}}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')

<script type="text/javascript">
  
  let currentShopId = null;

    function showRequests(shopId) {
  		currentShopId=shopId;
      $.ajax({
        url: '{{ route('admin.shop_requests') }}',
        type: 'POST',
        data: {
            shop_id: shopId,
            _token: '{{ csrf_token() }}'
        },
        success: function(data) {
            $('#requestsTableBody').html('');

            if (data.requests && data.requests.length > 0) {
                data.requests.forEach(function(request) {
                    $('#requestsTableBody').append(`
                        <tr>
                            <td>${request.phone}</td>
                            <td>${request.message}</td>
                            <td>
                                <span class="badge badge-inline badge-${
                                    request.status === 'pending' ? 'warning' :
                                    (request.status === 'approved' ? 'success' : 'danger')
                                }">
                                    ${request.status}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-soft-success btn-icon btn-circle btn-sm" 
                                        onclick="updateRequestStatus(${request.id}, 'approved')" 
                                        title="Approve">
                                    <i class="las la-check"></i>
                                </button>
                                <button class="btn btn-soft-danger btn-icon btn-circle btn-sm" 
                                        onclick="updateRequestStatus(${request.id}, 'rejected')" 
                                        title="Reject">
                                    <i class="las la-times"></i>
                                </button>
                            </td>
                        </tr>
                    `);
                });
            } else {
                $('#requestsTableBody').append(`
                    <tr>
                        <td colspan="4" class="text-center">No requests found</td>
                    </tr>
                `);
            }

            $('#smsRequestsModal').modal('show');
        },
        error: function(xhr) {
            alert('Failed to load requests.');
            console.error(xhr.responseText);
        }
    });
}

    function updateRequestStatus(requestId, status) {
        $.ajax({
            url: '{{route('admin.update-request-status')}}',
            type: 'POST',
            data: {
                request_id: requestId,
                status: status,
                _token: '{{ csrf_token() }}'
            },
            success: function(data) {
                AIZ.plugins.notify('success', '{{ translate("Status updated successfully") }}');
                // Refresh the requests table
                showRequests(data.shop_id);
            }
        });
    }

   function sendAllRequests(currentShopId) {
    if (confirm('{{ translate("Are you sure you want to send all approved requests?") }}')) {
        try {
            $.ajax({
                url: '{{ route('admin.send_all_sms') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    shop_id: currentShopId
                },
                success: function(data) {
                    AIZ.plugins.notify('success', '{{ translate("All requests have been sent successfully") }}');
                    $('#smsRequestsModal').modal('hide');
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    AIZ.plugins.notify('danger', '{{ translate("Failed to send requests. Please try again.") }}');
                }
            });
        } catch (err) {
            console.error('Exception:', err);
            AIZ.plugins.notify('danger', '{{ translate("Something went wrong in the request.") }}');
        }
    }
}

</script>
@endsection