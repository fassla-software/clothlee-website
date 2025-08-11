@extends('backend.layouts.app')

@section('content')

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{translate('Edit Seller Package')}}</h5>
            </div>
            <div class="card-body">
                <!-- Error Messages -->
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('seller_packages.update', $seller_package->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="name">
                            {{translate('Package Name')}}
                            <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-9">
                            <input type="text" placeholder="{{translate('Name')}}" id="name" name="name" class="form-control" value="{{ old('name', $seller_package->name) }}" required>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="description">
                            {{translate('Description')}}
                            <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-9">
                            <textarea placeholder="{{translate('Description')}}" id="description" name="description" class="form-control" rows="4" required>{{ old('description', $seller_package->description) }}</textarea>
                        </div>
                    </div>
                   
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="interval">
                            {{translate('Billing Interval')}}
                            <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-9">
                            <select name="interval" id="interval" class="form-control" required>
                                <option value="month" {{ old('interval', $seller_package->interval) == 'month' ? 'selected' : '' }}>{{translate('Monthly')}}</option>
                                <option value="year" {{ old('interval', $seller_package->interval) == 'year' ? 'selected' : '' }}>{{translate('Yearly')}}</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="commission">
                            {{translate('Commission')}}
                            <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-9">
                            <input type="number" lang="en" min="0" step="0.01" placeholder="{{translate('Commission')}}" id="commission" name="commission" class="form-control" value="{{ old('commission', $seller_package->commission) }}" required>
                            <small class="text-muted">{{translate('This commission will be applied per')}} {{ $seller_package->interval == 'month' ? translate('month') : translate('year') }}</small>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label" for="logo">
                            {{translate('Package Logo')}}
                        </label>
                        <div class="col-md-9">
                            <div class="input-group" data-toggle="aizuploader" data-type="image">
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                <input type="hidden" name="logo" class="selected-files" value="{{ old('logo', $seller_package->logo) }}">
                            </div>
                            <div class="file-preview box sm">
                                @if($seller_package->logo)
                                    <div class="d-flex justify-content-between align-items-center mt-2 file-preview-item" data-id="{{ $seller_package->logo }}">
                                        <div class="align-items-center align-self-stretch d-flex justify-content-center thumb">
                                            <img src="{{ uploaded_asset($seller_package->logo) }}" class="img-fit">
                                        </div>
                                        <div class="col body">
                                            <h6 class="d-flex">
                                                <span class="text-truncate title">{{ translate('Current Logo') }}</span>
                                            </h6>
                                            <p>{{ translate('Current package logo') }}</p>
                                        </div>
                                        <div class="remove">
                                            <a href="javascript:void(0)" class="btn btn-sm btn-link remove-attachment" data-id="{{ $seller_package->logo }}">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <small class="text-muted">{{translate('Upload package logo (optional)')}}</small>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="status">
                            {{translate('Status')}}
                        </label>
                        <div class="col-sm-9">
                            <select name="status" id="status" class="form-control">
                                <option value="active" {{ old('status', $seller_package->status) == 'active' ? 'selected' : '' }}>{{translate('Active')}}</option>
                                <option value="inactive" {{ old('status', $seller_package->status) == 'inactive' ? 'selected' : '' }}>{{translate('Inactive')}}</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group mb-0 text-right">
                        <a href="{{ route('seller_packages.index') }}" class="btn btn-secondary">{{translate('Cancel')}}</a>
                        <button type="submit" class="btn btn-primary">{{translate('Update Package')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection