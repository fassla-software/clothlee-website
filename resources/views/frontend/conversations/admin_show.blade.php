@extends('frontend.layouts.app')

@section('content')
    <div class="aiz-titlebar mt-2 mb-4">
        <div class="h6">{{ translate('Conversation with Admin') }}</div>
    </div>
    <div class="card">
        <div class="card-header">
            <h5 class="card-title fs-16 fw-600 mb-0">#{{ $conversation->title }}</h5>
        </div>

        <div class="card-body">
            <div id="messages">
                @include('frontend.partials.messages', ['conversation' => $conversation])
            </div>

            <form class="pt-4" action="{{ route('user.conversations.message_store') }}" method="POST">
                @csrf
                <input type="hidden" name="conversation_id" value="{{ $conversation->id }}">
                <div class="form-group">
                    <textarea class="form-control" rows="4" name="message" placeholder="{{ translate('Type your reply') }}" required></textarea>
                </div>
                <div class="form-group mb-0 text-right">
                    <button type="submit" class="btn btn-primary">{{ translate('Send') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        function refresh_messages() {
            $.post('{{ route('user.conversations.refresh') }}', {
                _token: '{{ csrf_token() }}',
                id: '{{ encrypt($conversation->id) }}'
            }, function(data) {
                $('#messages').html(data);
            });
        }

        refresh_messages(); // Initial load
        setInterval(refresh_messages, 5000); // Refresh every 5s
    </script>
@endsection
