@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <h5 class="mb-0 h6">{{translate('Edit Seller Information')}}</h5>
</div>
<div class="row">
    <!-- Seller Information Card -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{translate('Seller Information')}}</h5>
            </div>

            <div class="card-body">
                <form action="{{ route('sellers.update', $shop->id) }}" method="POST">
                    <input name="_method" type="hidden" value="PATCH">
                    @csrf
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="name">{{translate('Name')}}</label>
                        <div class="col-sm-9">
                            <input type="text" placeholder="{{translate('Name')}}" id="name" name="name" class="form-control" value="{{$shop->user->name}}" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="email">{{translate('Email Address')}}</label>
                        <div class="col-sm-9">
                            <input type="text" placeholder="{{translate('Email Address')}}" id="email" name="email" class="form-control" value="{{$shop->user->email}}" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="password">{{translate('Password')}}</label>
                        <div class="col-sm-9">
                            <input type="password" placeholder="{{translate('Password')}}" id="password" name="password" class="form-control">
                        </div>
                    </div>
                    <div class="form-group mb-0 text-right">
                        <button type="submit" class="btn btn-primary">{{translate('Save')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Subscription Plan Assignment Card -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{translate('Subscription Plan')}}</h5>
            </div>

            <div class="card-body">
                <form action="{{route('sellers.assign-plan', $shop->id)}}" method="POST">
                    @csrf
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="plan_id">{{translate('Select Plan')}}</label>
                        <div class="col-sm-9">
                            <select name="plan_id" id="plan_id" class="form-control" required>
                                <option value="">{{translate('Select a plan')}}</option>
                                @foreach($plans as $plan)
                                    <option value="{{$plan->id}}" 
                                        {{ $shop->subscription && $shop->subscription->plan_id == $plan->id ? 'selected' : '' }}>
                                        {{$plan->name}} - ${{$plan->commission}}/{{$plan->interval}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    @if($shop->subscription)
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label">{{translate('Current Plan')}}</label>
                        <div class="col-sm-9">
                            <p class="form-control-plaintext">
                                <strong>{{$shop->subscription->plan->name}}</strong><br>
                                <small class="text-muted">
                                    {{translate('Expires on')}}: {{$shop->subscription->end_date->format('M d, Y')}}
                                    @if($shop->subscription->status)
                                        <span class="badge badge-success">{{translate('Active')}}</span>
                                    @else
                                        <span class="badge badge-danger">{{translate('Expired')}}</span>
                                    @endif
                                </small>
                            </p>
                        </div>
                    </div>
                    @endif


                    <div class="form-group mb-0 text-right">
                        <button type="submit" class="btn btn-success">{{translate('Assign Plan')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection