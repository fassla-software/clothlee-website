@extends('seller.layouts.app')

@section('panel_content')

    <div class="aiz-titlebar mt-2 mb-4">
      <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{ translate('Items') }}</h1>
        </div>
      </div>
    </div>

    <!-- Modern Stats Row with Gradients -->
    <div class="row g-4 mb-4">
        @if (addon_is_activated('seller_subscription'))
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
                <div class="card bg-gradient-primary text-white border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="d-flex align-items-center justify-content-center mb-3">
                            <div class="bg-white bg-opacity-20 rounded-circle p-3">
                                <i class="las la-upload la-2x"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold mb-1">{{ max(0, auth()->user()->shop->product_upload_limit - auth()->user()->products()->count()) }}</h3>
                        <p class="mb-0 opacity-75">{{ translate('Remaining Uploads') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
            <div class="card bg-gradient-success text-white border-0 shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <div class="bg-white bg-opacity-20 rounded-circle p-3">
                            <i class="las la-box la-2x"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1">{{ $products->total() }}</h3>
                    <p class="mb-0 opacity-75">{{ translate('Total Products') }}</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
            <div class="card bg-gradient-info text-white border-0 shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <div class="bg-white bg-opacity-20 rounded-circle p-3">
                            <i class="las la-eye la-2x"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1">{{ auth()->user()->products()->where('published', 1)->count() }}</h3>
                    <p class="mb-0 opacity-75">{{ translate('Published') }}</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
            <a href="{{ route('seller.products.create')}}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 hover-lift">
                    <div class="card-body text-center p-4">
                        <div class="d-flex align-items-center justify-content-center mb-3">
                            <div class="size-60px rounded-circle bg-secondary d-flex align-items-center justify-content-center">
                                <i class="las la-plus la-3x text-white"></i>
                            </div>
                        </div>
                        <h5 class="text-primary mb-0">{{ translate('Add New Item') }}</h5>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Package Info -->
    @if (addon_is_activated('seller_subscription'))
        @php
            $seller_package = \App\Models\SellerPackage::find(Auth::user()->shop->seller_package_id);
        @endphp
        <div class="row mb-4">
            <div class="col-lg-4 col-md-6">
                <a href="{{ route('seller.seller_packages_list') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 hover-lift">
                        <div class="card-body text-center p-4">
                            @if($seller_package != null)
                                <img src="{{ uploaded_asset($seller_package->logo) }}" height="60" class="mb-3 rounded">
                                <h6 class="mb-2">{{ translate('Current Package')}}</h6>
                                <p class="text-muted mb-3">{{ $seller_package->getTranslation('name') }}</p>
                            @else
                                <i class="las la-frown-o la-3x text-muted mb-3"></i>
                                <h6 class="text-muted mb-3">{{ translate('No Package Found')}}</h6>
                            @endif
                            <div class="btn btn-outline-primary">{{ translate('Upgrade Package')}}</div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    @endif

    <!-- Controls Row -->
    <div class="row mb-4">
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <label for="default_sort" class="form-label fw-semibold">{{ translate('Default Product Sort') }}</label>
                    <select name="default_sort" class="form-select" id="default_sort_select">
                        <option value="newest" {{ $shop->default_sort == 'newest' ? 'selected' : '' }}>{{ translate('Newest') }}</option>
                        <option value="cheapest" {{ $shop->default_sort == 'cheapest' ? 'selected' : '' }}>{{ translate('Cheapest') }}</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Products Card -->
    <div class="card border-0 shadow-sm">
        <!-- Enhanced Card Header -->
        <div class="card-header bg-white border-bottom py-4">
            <div class="row align-items-center g-3">
                <div class="col-lg-6 col-md-12">
                    <h5 class="mb-0 fw-bold">
                        {{ translate('All Items') }} 
                        <span class="badge bg-primary ms-2">{{ $products->total() }}</span>
                    </h5>
                </div>
                <div class="col-lg-6 col-md-12">
                    <form method="GET" action="" class="d-flex flex-column flex-lg-row gap-2">
                        <select name="category_id" class="form-select flex-fill" onchange="this.form.submit()">
                            <option value="">{{ translate('All Categories') }}</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ $category->id == $category_id ? 'selected' : '' }}>
                                    {{ $category->getTranslation('name') }}
                                </option>
                            @endforeach
                        </select>
                        <div class="input-group flex-fill">
                            <input type="text" name="search" class="form-control" 
                                   placeholder="{{ translate('Search products...') }}" 
                                   value="{{ $search ?? '' }}">
                            <button type="submit" class="btn btn-primary">
                                <i class="las la-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Enhanced Card Body -->
        <div class="card-body p-0 bg-white">
            <!-- Desktop/Tablet Table -->
            <div class="d-none d-md-block">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 bg-white">
                        <thead class="bg-white border-bottom">
                            <tr>
                                <th width="50" class="ps-4 bg-white">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input check-all" id="selectAll">
                                        <label class="form-check-label" for="selectAll"></label>
                                    </div>
                                </th>
                                <th width="80" class="bg-white">{{ translate('Image') }}</th>
                                <th class="bg-white">{{ translate('Product Details') }}</th>
                                <th width="100" class="text-center bg-white">{{ translate('Stock') }}</th>
                                <th width="140" class="bg-white">{{ translate('Price') }}</th>
                                @if(get_setting('product_approve_by_admin') == 1)
                                    <th width="100" class="text-center bg-white">{{ translate('Status') }}</th>
                                @endif
                                <th width="100" class="text-center bg-white">{{ translate('Published') }}</th>
                                <th width="100" class="text-center bg-white">{{ translate('Featured') }}</th>
                                <th width="160" class="text-center bg-white">{{ translate('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            @foreach($products as $product)
                                <tr class="product-row bg-white">
                                    <td class="ps-4 bg-white">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input check-one" 
                                                   value="{{ $product->id }}" id="product{{ $product->id }}">
                                            <label class="form-check-label" for="product{{ $product->id }}"></label>
                                        </div>
                                    </td>
                                    
                                    <td class="bg-white">
                                        <div class="product-img-wrapper">
                                            <img src="{{ uploaded_asset($product->thumbnail_img) ?: static_asset('assets/img/placeholder.jpg') }}" 
                                                 class="rounded-3 shadow-sm" 
                                                 style="width: 60px; height: 60px; object-fit: cover;">
                                        </div>
                                    </td>
                                    
                                    <td class="bg-white">
                                        <div class="product-details">
                                            <h6 class="mb-1 fw-semibold text-dark">{{ $product->main_category->name }}</h6>
                                            <div class="text-muted small">
                                                <span class="badge bg-light text-dark me-2">ID: {{ $product->id }} <span></span>  </span>
                                                @if($product->main_category)
                                                    <span class="text-info">{{ $product->name }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td class="text-center bg-white">
                                        @php
                                            $totalStock = 0;
                                            if($product->stocks && count($product->stocks) > 0) {
                                                foreach($product->stocks as $stock) {
                                                    $totalStock += $stock->qty ?? 0;
                                                }
                                            }
                                        @endphp
                                        <span class="badge bg-{{ $totalStock > 0 ? 'success' : 'danger' }} fs-6 px-3 py-2">
                                            {{ $totalStock }}
                                        </span>
                                    </td>
                                    
                                    <td class="bg-white">
                                        <div class="price-container">
                                            <div id="price-display-{{ $product->id }}">
                                                <div class="fw-bold text-primary fs-6 mb-1">{{ format_price($product->unit_price) }}</div>
                                                <button class="btn btn-outline-secondary btn-sm" onclick="editPrice({{ $product->id }})">
                                                    <i class="las la-edit me-1"></i>{{ translate('Edit') }}
                                                </button>
                                            </div>
                                            
                                            <div id="price-edit-{{ $product->id }}" class="d-none">
                                                <div class="input-group input-group-sm mb-2">
                                                    <input type="number" id="price-input-{{ $product->id }}" 
                                                           value="{{ $product->unit_price }}" 
                                                           class="form-control" step="0.01">
                                                    <button class="btn btn-success" onclick="savePrice({{ $product->id }})">
                                                        <i class="las la-check"></i>
                                                    </button>
                                                    <button class="btn btn-secondary" onclick="cancelEditPrice({{ $product->id }})">
                                                        <i class="las la-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    @if(get_setting('product_approve_by_admin') == 1)
                                        <td class="text-center bg-white">
                                            @if($product->approved == 1)
                                                <span class="badge bg-success">{{ translate('Approved') }}</span>
                                            @else
                                                <span class="badge bg-warning">{{ translate('Pending') }}</span>
                                            @endif
                                        </td>
                                    @endif
                                    
                                    <td class="text-center bg-white">
                                        <div class="form-check form-switch d-flex justify-content-center">
                                            <input type="checkbox" class="form-check-input" 
                                                   id="published-{{ $product->id }}"
                                                   onchange="updatePublished({{ $product->id }}, this.checked)"
                                                   {{ $product->published ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                    
                                    <td class="text-center bg-white">
                                        <div class="form-check form-switch d-flex justify-content-center">
                                            <input type="checkbox" class="form-check-input" 
                                                   id="featured-{{ $product->id }}"
                                                   onchange="updateFeatured({{ $product->id }}, this.checked)"
                                                   {{ $product->seller_featured ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                    
                                    <td class="text-center bg-white">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('seller.products.edit', ['id' => $product->id, 'lang' => env('DEFAULT_LANGUAGE')]) }}" 
                                               class="btn btn-outline-primary btn-sm" 
                                               data-bs-toggle="tooltip" title="{{ translate('Edit') }}">
                                                <i class="las la-edit"></i>
                                            </a>
                                            <a href="{{ route('seller.products.duplicate', $product->id) }}" 
                                               class="btn btn-outline-success btn-sm"
                                               data-bs-toggle="tooltip" title="{{ translate('Duplicate') }}">
                                                <i class="las la-copy"></i>
                                            </a>
                                            <button class="btn btn-outline-danger btn-sm" 
                                                    onclick="deleteProduct({{ $product->id }})"
                                                    data-bs-toggle="tooltip" title="{{ translate('Delete') }}">
                                                <i class="las la-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            
                            @if($products->count() == 0)
                                <tr class="bg-white">
                                    <td colspan="20" class="text-center py-5 bg-white">
                                        <div class="empty-state">
                                            <div class="mb-4">
                                                <i class="las la-box-open" style="font-size: 4rem; color: #e9ecef;"></i>
                                            </div>
                                            <h5 class="text-muted mb-3">{{ translate('No Products Found') }}</h5>
                                            <p class="text-muted mb-4">{{ translate('Start by adding your first product to get started.') }}</p>
                                            <a href="{{ route('seller.products.create') }}" class="btn btn-primary btn-lg">
                                                <i class="las la-plus me-2"></i>{{ translate('Add Your First Product') }}
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mobile Cards View -->
            <div class="d-block d-md-none p-3 bg-white">
                @foreach($products as $product)
                    @php
                        $totalStock = 0;
                        if($product->stocks && count($product->stocks) > 0) {
                            foreach($product->stocks as $stock) {
                                $totalStock += $stock->qty ?? 0;
                            }
                        }
                    @endphp
                    <div class="card border-0 shadow-sm mb-3 mobile-product-card bg-white">
                        <div class="card-body p-3 bg-white">
                            <div class="row g-3">
                                <div class="col-4">
                                    <img src="{{ uploaded_asset($product->thumbnail_img) ?: static_asset('assets/img/placeholder.jpg') }}" 
                                         class="w-100 rounded-3 shadow-sm" 
                                         style="height: 80px; object-fit: cover;">
                                </div>
                                <div class="col-8">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="fw-semibold mb-1 text-truncate">{{ $product->name }}</h6>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input" 
                                                   onchange="updatePublished({{ $product->id }}, this.checked)"
                                                   {{ $product->published ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                    
                                    <p class="text-muted small mb-2">ID: {{ $product->id }}</p>
                                    
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="fw-bold text-primary">{{ format_price($product->unit_price) }}</span>
                                        <span class="badge bg-{{ $totalStock > 0 ? 'success' : 'danger' }}">
                                            Stock: {{ $totalStock }}
                                        </span>
                                    </div>
                                    
                                    <div class="btn-group w-100" role="group">
                                        <a href="{{ route('seller.products.edit', ['id' => $product->id, 'lang' => env('DEFAULT_LANGUAGE')]) }}" 
                                           class="btn btn-outline-primary btn-sm flex-fill">
                                            <i class="las la-edit"></i>
                                        </a>
                                        <a href="{{ route('seller.products.duplicate', $product->id) }}" 
                                           class="btn btn-outline-success btn-sm flex-fill">
                                            <i class="las la-copy"></i>
                                        </a>
                                        <button class="btn btn-outline-danger btn-sm flex-fill" 
                                                onclick="deleteProduct({{ $product->id }})">
                                            <i class="las la-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                
                @if($products->count() == 0)
                    <div class="text-center py-5 bg-white">
                        <div class="mb-4">
                            <i class="las la-box-open" style="font-size: 4rem; color: #e9ecef;"></i>
                        </div>
                        <h5 class="text-muted mb-3">{{ translate('No Products Found') }}</h5>
                        <a href="{{ route('seller.products.create') }}" class="btn btn-primary">
                            <i class="las la-plus me-2"></i>{{ translate('Add Product') }}
                        </a>
                    </div>
                @endif
            </div>

            <!-- Enhanced Pagination -->
            @if($products->hasPages())
                <div class="card-footer bg-white border-top py-3">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="text-muted small">
                                {{ translate('Showing') }} {{ $products->firstItem() }} - {{ $products->lastItem() }} 
                                {{ translate('of') }} {{ $products->total() }} {{ translate('results') }}
                            </div>
                        </div>
                        <div class="col-md-6 d-flex justify-content-md-end justify-content-center mt-2 mt-md-0">
                            {{ $products->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

@endsection

@section('script')
<script>
// Enhanced Select all functionality
$(document).on('change', '.check-all', function() {
    $('.check-one').prop('checked', this.checked);
    updateBulkActionsVisibility();
});

$(document).on('change', '.check-one', function() {
    updateBulkActionsVisibility();
});

function updateBulkActionsVisibility() {
    const checkedCount = $('.check-one:checked').length;
    // Add bulk actions UI here if needed
}

// Enhanced Price editing functions
function editPrice(productId) {
    $('#price-display-' + productId).addClass('d-none');
    $('#price-edit-' + productId).removeClass('d-none');
    $('#price-input-' + productId).focus().select();
}

function cancelEditPrice(productId) {
    $('#price-display-' + productId).removeClass('d-none');
    $('#price-edit-' + productId).addClass('d-none');
}

function savePrice(productId) {
    const newPrice = $('#price-input-' + productId).val();
    
    if (newPrice && !isNaN(newPrice) && parseFloat(newPrice) >= 0) {
        // Add loading state
        const saveBtn = $('#price-edit-' + productId + ' .btn-success');
        const originalContent = saveBtn.html();
        saveBtn.html('<i class="las la-spinner la-spin"></i>').prop('disabled', true);
        
        $.ajax({
            url: '/seller/products/' + productId + '/update-price',
            method: 'POST',
            data: {
                unit_price: newPrice,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#price-display-' + productId + ' .fw-bold').text(response.formatted_price);
                cancelEditPrice(productId);
                showNotification('success', '{{ translate('Price updated successfully') }}');
            },
            error: function() {
                showNotification('error', '{{ translate('Error updating price') }}');
            },
            complete: function() {
                saveBtn.html(originalContent).prop('disabled', false);
            }
        });
    } else {
        showNotification('warning', '{{ translate('Please enter a valid price') }}');
    }
}

// Enhanced Status update functions
function updatePublished(productId, status) {
    $.post('{{ route('seller.products.published') }}', {
        _token: '{{ csrf_token() }}',
        id: productId,
        status: status ? 1 : 0
    }, function(data) {
        if (data == 1) {
            showNotification('success', '{{ translate('Status updated successfully') }}');
        } else {
            showNotification('error', '{{ translate('Something went wrong') }}');
            $(`#published-${productId}`).prop('checked', !status);
        }
    }).fail(function() {
        showNotification('error', '{{ translate('Something went wrong') }}');
        $(`#published-${productId}`).prop('checked', !status);
    });
}

function updateFeatured(productId, status) {
    $.post('{{ route('seller.products.featured') }}', {
        _token: '{{ csrf_token() }}',
        id: productId,
        status: status ? 1 : 0
    }, function(data) {
        if (data == 1) {
            showNotification('success', '{{ translate('Featured status updated') }}');
        } else {
            showNotification('error', '{{ translate('Something went wrong') }}');
            $(`#featured-${productId}`).prop('checked', !status);
        }
    }).fail(function() {
        showNotification('error', '{{ translate('Something went wrong') }}');
        $(`#featured-${productId}`).prop('checked', !status);
    });
}

// Enhanced Delete product with confirmation
function deleteProduct(productId) {
    const confirmation = confirm('{{ translate('Are you sure you want to delete this product? This action cannot be undone.') }}');
    if (confirmation) {
        $.ajax({
            url: '{{ route('seller.products.destroy', '') }}/' + productId,
            method: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: function() {
                showNotification('success', '{{ translate('Product deleted successfully') }}');
                setTimeout(() => location.reload(), 1000);
            },
            error: function() {
                showNotification('error', '{{ translate('Error deleting product') }}');
            }
        });
    }
}

// Default sort update
$('#default_sort_select').on('change', function() {
    const select = $(this);
    const originalValue = select.data('original-value') || select.val();
    
    $.ajax({
        url: '{{ route("seller.shop.update-default-sort") }}',
        type: 'POST',
        data: {
            default_sort: select.val(),
            _token: '{{ csrf_token() }}'
        },
        success: function() {
            showNotification('success', '{{ translate('Default sort updated successfully') }}');
            select.data('original-value', select.val());
        },
        error: function() {
            showNotification('error', '{{ translate('Failed to update default sort') }}');
            select.val(originalValue);
        }
    });
});

// Enhanced Notification helper
function showNotification(type, message) {
    if (typeof AIZ !== 'undefined' && AIZ.plugins && AIZ.plugins.notify) {
        AIZ.plugins.notify(type, message);
    } else {
        // Fallback notification
        const alertClass = type === 'success' ? 'alert-success' : type === 'error' ? 'alert-danger' : 'alert-warning';
        const notification = $(`
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        $('body').append(notification);
        setTimeout(() => notification.remove(), 5000);
    }
}

// Initialize tooltips
$(document).ready(function() {
    // Initialize Bootstrap tooltips if available
    if (typeof bootstrap !== 'undefined') {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
    
    // Store original values for selects
    $('#default_sort_select').data('original-value', $('#default_sort_select').val());
});

// Add smooth transitions
$('.product-row').hover(
    function() { $(this).addClass('table-active'); },
    function() { $(this).removeClass('table-active'); }
);
</script>

<style>
/* Modern CSS Variables */
:root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    --info-gradient: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    --warning-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    --card-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    --card-shadow-hover: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    --border-radius: 0.75rem;
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Enhanced Gradients */
.bg-gradient-primary {
    background: var(--primary-gradient) !important;
}

.bg-gradient-success {
    background: var(--success-gradient) !important;
}

.bg-gradient-info {
    background: var(--info-gradient) !important;
}

.bg-gradient-warning {
    background: var(--warning-gradient) !important;
}

/* Card Enhancements */
.card {
    border: none;
    border-radius: var(--border-radius);
    box-shadow: var(--card-shadow);
    transition: var(--transition);
    background-color: #ffffff !important;
}

.card:hover,
.hover-lift:hover {
    transform: translateY(-2px);
    box-shadow: var(--card-shadow-hover);
}

/* Add Item Card Styling */
.size-60px {
    width: 60px;
    height: 60px;
}

/* Table Enhancements - Force White Background */
.table {
    border-collapse: separate;
    border-spacing: 0;
    background-color: #ffffff !important;
}

.table th {
    background-color: #ffffff !important;
    font-weight: 600;
    border-bottom: 2px solid #e9ecef;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #495057 !important;
}

.table td {
    vertical-align: middle;
    border-bottom: 1px solid #f0f0f0;
    padding: 1rem 0.75rem;
    background-color: #ffffff !important;
    color: #495057 !important;
}

.table tbody {
    background-color: #ffffff !important;
}

.table thead {
    background-color: #ffffff !important;
}

.product-row {
    transition: var(--transition);
    background-color: #ffffff !important;
}

.product-row:hover {
    background-color: #f8f9fa !important;
    transform: scale(1.01);
}

.product-row td {
    background-color: inherit !important;
}

/* Force white background on all table elements */
.table-responsive {
    background-color: #ffffff !important;
}

.card-body {
    background-color: #ffffff !important;
}

.card-header {
    background-color: #ffffff !important;
}

.card-footer {
    background-color: #ffffff !important;
}

/* Enhanced Form Controls */
.form-select,
.form-control {
    border: 2px solid #e9ecef;
    border-radius: 0.5rem;
    transition: var(--transition);
    font-size: 0.875rem;
    background-color: #ffffff !important;
    color: #495057 !important;
}

.form-select:focus,
.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    background-color: #ffffff !important;
}

/* Enhanced Switches */
.form-check-input {
    width: 2.5em;
    height: 1.25em;
    border-radius: 2rem;
    transition: var(--transition);
}

.form-check-input:checked {
    background-color: #28a745;
    border-color: #28a745;
}

/* Button Enhancements */
.btn {
    border-radius: 0.5rem;
    font-weight: 500;
    transition: var(--transition);
    border-width: 2px;
}

.btn-lg {
    padding: 0.75rem 1.5rem;
    font-size: 1rem;
}

.btn-group .btn {
    margin: 0;
    border-radius: 0.375rem;
}

.btn-group .btn:not(:last-child) {
    margin-right: 0.25rem;
}

/* Badge Enhancements */
.badge {
    font-weight: 500;
    border-radius: 0.5rem;
    padding: 0.5em 0.75em;
}

/* Mobile Card Enhancements */
.mobile-product-card {
    transition: var(--transition);
    border-radius: var(--border-radius);
    background-color: #ffffff !important;
}

.mobile-product-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--card-shadow-hover);
}

.mobile-product-card .card-body {
    background-color: #ffffff !important;
}

/* Responsive Utilities */
.g-4 {
    gap: 1.5rem !important;
}

/* Loading States */
.la-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Responsive Breakpoints */
@media (max-width: 768px) {
    .card-body {
        padding: 1rem;
        background-color: #ffffff !important;
    }
    
    .btn-group .btn {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    
    .table th,
    .table td {
        padding: 0.5rem;
        font-size: 0.875rem;
        background-color: #ffffff !important;
    }
    
    .mobile-product-card .card-body {
        padding: 1rem;
        background-color: #ffffff !important;
    }
}

@media (max-width: 576px) {
    .aiz-titlebar h1 {
        font-size: 1.5rem;
    }
    
    .btn-lg {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
    }
    
    .card-header .row > div {
        margin-bottom: 1rem;
    }
    
    .form-select,
    .form-control {
        font-size: 0.875rem;
        background-color: #ffffff !important;
    }
}

/* Enhanced empty state */
.empty-state {
    padding: 3rem 1rem;
    background-color: #ffffff !important;
}

.empty-state i {
    opacity: 0.5;
}

/* Price container improvements */
.price-container {
    min-width: 140px;
}

.price-container .input-group-sm .form-control {
    font-size: 0.875rem;
    background-color: #ffffff !important;
}

/* Product image improvements */
.product-img-wrapper {
    position: relative;
    overflow: hidden;
    border-radius: 0.75rem;
}

.product-img-wrapper img {
    transition: var(--transition);
}

.product-img-wrapper:hover img {
    transform: scale(1.05);
}

/* Remove any dark backgrounds */
* {
    background-color: inherit;
}

.table * {
    background-color: #ffffff !important;
}
</style>
@endsection