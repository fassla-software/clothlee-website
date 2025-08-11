@extends('backend.layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{translate('Create New Seller Package')}}</h5>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('admin.seller_packages.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="name">
                            {{translate('Package Name')}}
                            <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-9">
                            <input type="text" placeholder="{{translate('Name')}}" id="name" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="description">
                            {{translate('Description')}}
                            <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-9">
                            <textarea placeholder="{{translate('Description')}}" id="description" name="description" class="form-control" rows="4" required>{{ old('description') }}</textarea>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="commission">
                            {{translate('Commission')}}
                            <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-9">
                            <input type="number" lang="en" min="0" step="0.01" placeholder="{{translate('Commission')}}" id="commission" name="commission" class="form-control" value="{{ old('commission') }}" required>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="interval">
                            {{translate('Billing Interval')}}
                            <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-9">
                            <select name="interval" id="interval" class="form-control" required>
                                <option value="month" {{ old('interval') == 'month' ? 'selected' : '' }}>{{translate('Monthly')}}</option>
                                <option value="year" {{ old('interval') == 'year' ? 'selected' : '' }}>{{translate('Yearly')}}</option>
                            </select>
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
                                <input type="hidden" name="logo" class="selected-files" value="{{ old('logo') }}">
                            </div>
                            <div class="file-preview box sm"></div>
                            <small class="text-muted">{{translate('Upload package logo (JPEG, PNG, JPG, GIF max 2MB)')}}</small>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="status">
                            {{translate('Status')}}
                            <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-9">
                            <select name="status" id="status" class="form-control" required>
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>{{translate('Active')}}</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>{{translate('Inactive')}}</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group mb-0 text-right">
                        <a href="{{ route('seller_packages.index') }}" class="btn btn-secondary">{{translate('Cancel')}}</a>
                        <button type="submit" class="btn btn-primary">{{translate('Save Package')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection