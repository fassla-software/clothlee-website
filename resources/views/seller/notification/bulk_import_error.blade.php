@if(session()->has('import_errors'))
<div class="row">
    <div class="col-lg-12">
        <div class="alert alert-danger">
            <h5 class="alert-heading">{{ translate('Import Errors') }}</h5>
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th width="10%">{{ translate('Row') }}</th>
                            <th width="20%">{{ translate('Phone') }}</th>
                            <th>{{ translate('Error') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(session('import_errors') as $error)
                        <tr>
                            <td>{{ $error['row'] }}</td>
                            <td>{{ $error['phone'] }}</td>
                            <td>{{ $error['error'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endif