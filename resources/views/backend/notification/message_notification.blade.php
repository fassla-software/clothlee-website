@extends('backend.layouts.app')

@section('content')
<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('message_notification.send') }}" method="POST">
                    @csrf
                    <p class="fs-13 fw-700 mb-3">{{ translate('Send Message') }}</p>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="form-group">
                        <label>{{ translate('Select Customers') }}</label>
                        <select name="user_ids[]" id="customer_select" class="form-control aiz-selectpicker" multiple data-live-search="true">
                            @foreach ($customers->where('user_type', 'customer') as $customer)
                                <option value="{{ $customer->id }}">
                                    {{ $customer->name }} - ({{ $customer->email ?? $customer->phone }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mt-3">
                        <label>{{ translate('Select Sellers') }}</label>
                        <select name="user_ids[]" id="seller_select" class="form-control aiz-selectpicker" multiple data-live-search="true">
                            @foreach ($customers->where('user_type', 'seller') as $seller)
                                <option value="{{ $seller->id }}">
                                    {{ $seller->name }} - ({{ $seller->email ?? $seller->phone }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <button type="button" class="btn btn-sm btn-soft-blue mt-2" id="select_all">{{ translate('Select All') }}</button>
                        <button type="button" class="btn btn-sm btn-soft-blue mt-2" id="deselect_all">{{ translate('Deselect All') }}</button>
                    </div>

                    <div class="form-group">
                        <label>{{ translate('Title') }}</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>{{ translate('Body') }}</label>
                        <textarea name="body" class="form-control" rows="4" required></textarea>
                    </div>

                    <div class="text-right">
                        <button type="submit" class="btn btn-primary">{{ translate('Send') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script type="text/javascript">
    $('#select_all').click(function () {
        $('#customer_select, #seller_select').selectpicker('selectAll');
    });
    $('#deselect_all').click(function () {
        $('#customer_select, #seller_select').selectpicker('deselectAll');
    });
</script>
@endsection
