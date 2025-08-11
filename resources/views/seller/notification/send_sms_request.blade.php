@extends('seller.layouts.app')

@section('panel_content')
<div class="row">
    <div class="col-lg-6 mx-auto">
        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="las la-check-circle"></i>
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="las la-times-circle"></i>
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="las la-exclamation-triangle"></i>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <!-- Single SMS Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="fs-18 mb-0">{{translate('Send Single SMS')}}</h3>
            </div>
            <form class="form-horizontal" action="{{ route('sms_requests.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-sm-2 control-label" for="phones">{{translate('Phone Numbers')}}</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="phones" rows="3" placeholder="{{ translate('Enter comma-separated phone numbers') }}" required></textarea>
                            <small class="form-text text-muted">{{ translate('Example: 01012345678,01123456789') }}</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 control-label" for="message">{{translate('SMS content')}}</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="message" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-primary" type="submit">{{translate('Send')}}</button>
                </div>
            </form>
        </div>

        <!-- Bulk SMS Upload Form -->
        <div class="card">
            <div class="card-header">
                <h3 class="fs-18 mb-0">{{translate('Bulk SMS Import')}}</h3>
            </div>
            <form class="form-horizontal" action="{{ route('sms_requests.bulk-upload') }}" method="POST" enctype="multipart/form-data" id="bulkUploadForm">
                @csrf
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-sm-2 control-label" for="bulk_file">{{translate('Excel File')}}</label>
                        <div class="col-sm-10">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="bulk_file" id="bulk_file" accept=".xlsx,.xls,.csv" required>
                                <label class="custom-file-label" for="bulk_file">{{ translate('Choose File') }}</label>
                            </div>
                            <small class="form-text text-muted">
                                {{ translate('File should contain "phone" column (required) and "message" column (optional). Max 1000 records.') }}
                            </small>
                            
                            <div class="progress mt-2" id="uploadProgressBar" style="display: none; height: 20px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                     role="progressbar" 
                                     style="width: 0%" 
                                     aria-valuenow="0" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                     <span class="progress-text">0%</span>
                                </div>
                            </div>

                            <div class="alert alert-success mt-2" id="uploadSuccess" style="display: none;">
                                <i class="las la-check-circle"></i>
                                <span id="successText"></span>
                            </div>

                            <div class="alert alert-danger mt-2" id="uploadError" style="display: none;">
                                <i class="las la-times-circle"></i>
                                <span id="errorText"></span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 control-label" for="default_message">{{translate('Default Message')}}</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="default_message" placeholder="{{ translate('Will be used if message column is empty') }}"></textarea>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="las la-info-circle"></i>
                        {{ translate('Download our template file to ensure proper formatting:') }}
<a href="{{ route("admin.download_sms_file") }}" class="btn btn-sm btn-info ml-2" download>
                            <i class="las la-download"></i> {{ translate('Download Template') }}
                        </a>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-success" type="submit" id="submitBtn">
                        <i class="las la-upload"></i> {{translate('Import & Send')}}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
$(document).ready(function() {
    $('#bulk_file').on('change', function() {
        const fileName = this.files.length > 0 ? this.files[0].name : '{{ translate("Choose File") }}';
        $(this).next('.custom-file-label').text(fileName);
    });
});
</script>
@endpush
