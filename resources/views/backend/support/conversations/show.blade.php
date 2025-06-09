@extends('backend.layouts.app')

@section('content')
<div class="col-lg-12">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">
                #{{ $conversation->title }}
                (Between 
                @if($conversation->sender) {{ $conversation->sender->name }} @endif 
                and 
                @if($conversation->receiver) {{ $conversation->receiver->name }} @endif)
            </h5>
        </div>

        <div class="card-body">
            <ul class="list-group list-group-flush">
                @foreach($conversation->messages as $message)
                    <li class="list-group-item px-0">
                        <div class="media mb-2">
                            <img class="avatar avatar-xs mr-3" 
                                 @if($message->user) 
                                    src="{{ uploaded_asset($message->user->avatar_original) }}" 
                                 @endif 
                                 onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-place.png') }}';">
                            <div class="media-body">
                                <h6 class="mb-0 fw-600">{{ $message->user->name ?? 'Unknown User' }}</h6>
                                <p class="opacity-50">{{ $message->created_at }}</p>
                            </div>
                        </div>
                        <p>{{ $message->message }}</p>
                    </li>
                @endforeach
            </ul>

            {{-- ✅ Reply Box (Always visible to Admin) --}}
            <form action="{{ route('messages.store') }}" method="POST">
                @csrf
                <input type="hidden" name="conversation_id" value="{{ $conversation->id }}">
                <div class="form-group mt-4">
                    <label for="message">{{ translate('Reply') }}</label>
                    <textarea class="form-control" rows="4" name="message" placeholder="{{ translate('Type your reply') }}" required></textarea>
                </div>
                <div class="form-group text-right">
                    <button type="submit" class="btn btn-primary">{{ translate('Send') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
