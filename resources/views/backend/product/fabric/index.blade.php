@extends('backend.layouts.app')

@section('content')

    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class="align-items-center">
            <h1 class="h3">{{ translate('All Fabrics') }}</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form id="sort_fabrics" action="" method="GET">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 h6">{{ translate('Fabrics') }}</h5>
                        <div class="d-flex">
                            <input type="text" class="form-control form-control-sm mr-2" id="search" name="search"
                                value="{{ request()->search ?? '' }}"
                                placeholder="{{ translate('Type fabric name') }}">
                            <button type="submit" class="btn btn-primary btn-sm">{{ translate('Filter') }}</button>
                            <button type="button" id="clear_filter" class="btn btn-secondary btn-sm ml-2">{{ translate('Clear') }}</button>
                        </div>
                    </div>
                </form>
                
                <div class="card-body">
                    <table class="table aiz-table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ translate('Fabric Name') }}</th>
                                <th>{{ translate('Product Count') }}</th>
                                <th>{{ translate('Order Count') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($fabrics as $key => $fabric)
                                <tr>
                                    <td>{{ ($key+1) + ($fabrics->currentPage() - 1) * $fabrics->perPage() }}</td>
                                    <td>{{ $fabric->fabric }}</td>
                                    <td>{{ $fabric->product_count }}</td>
                                    <td>{{ $fabric->order_count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="aiz-pagination">
                        {{ $fabrics->appends(request()->input())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection


@section('script')
    <script type="text/javascript">
        document.getElementById('clear_filter').addEventListener('click', function() {
            document.getElementById('search').value = '';  // Clear the input field
            document.getElementById('sort_fabrics').submit();  // Submit the form
        });
    </script>
@endsection
