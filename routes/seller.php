<?php

use App\Http\Controllers\AizUploadController;
use App\Http\Controllers\AttributeController;
use App\Http\Controllers\Seller\NotificationController_seller;
use App\Http\Controllers\Seller\SmsRequestController;
use App\Http\Controllers\Seller\NotificationTypeController_seller;
use App\Http\Controllers\Seller\DashboardController;


Route::controller(NotificationController_seller::class)->group(function () {
    Route::get('/all-notifications', 'adminIndex')->name('seller.all-notifications');
    Route::get('/notification-settings', 'notificationSettings')->name('seller.notification.settings');

    Route::post('/notifications/bulk-delete', 'bulkDeleteAdmin')->name('seller.notifications.bulk_delete');
    Route::get('/notification/read-and-redirect/{id}', 'readAndRedirect')->name('seller.notification.read-and-redirect');

    Route::get('/custom-notification', 'customNotification')->name('seller.custom_notification');
    Route::post('/custom-notification/send', 'sendCustomNotification')->name('seller.custom_notification.send');

    Route::get('/custom-notification/history', 'customNotificationHistory')->name('seller.custom_notification.history');
    Route::get('/custom-notifications.delete/{identifier}', 'customNotificationSingleDelete')->name('seller.custom-notifications.delete');
    Route::post('/custom-notifications.bulk_delete', 'customNotificationBulkDelete')->name('seller.custom-notifications.bulk_delete');
    Route::post('/custom-notified-customers-list', 'customNotifiedCustomersList')->name('seller.custom_notified_customers_list');
});

Route::resource('notification-type', NotificationTypeController_seller::class)->names([
    'index'   => 'seller.notification-type.index',
    'store'   => 'seller.notification-type.store',
    'edit'    => 'seller.notification-type.edit',
    'update'  => 'seller.notification-type.update',
    'destroy' => 'seller.notification-type.destroy',
]);

Route::controller(NotificationTypeController_seller::class)->group(function () {
    Route::get('/notification-type/edit/{id}', 'edit')->name('seller.notification-type.edit');
    Route::post('/notification-type/update-status', 'updateStatus')->name('seller.notification-type.update-status');
    Route::get('/notification-type/destroy/{id}', 'destroy')->name('seller.notification-type.destroy');
    Route::post('/notification-type/bulk_delete', 'bulkDelete')->name('seller.notifications-type.bulk_delete');
    Route::post('/notification-type.get-default-text', 'getDefaulText')->name('seller.notification_type.get_default_text');
  	
});


Route::controller(SmsRequestController::class)->group(function () {
	Route::get('/sms-requests','index')->name('sms_requests.index');
  	Route::post('/sms-requests/store','store')->name('sms_requests.store');
  	Route::post('/sms-requests/bulk-upload','bulkStore')->name('sms_requests.bulk-upload');
  	Route::get('/admin/sms-requests','getAllRequestedShops')->name('admin.sms_requests');
  	Route::post('/admin/sms-requests/shop-request','getShopRequest')->name('admin.shop_requests');
	Route::post('/admin/sms-requests/update-status','updateStatus')->name('admin.update-request-status');
  	Route::post('/admin/sms-requests/send-all','sendAll')->name('admin.send_all_sms');
  	Route::get('/download-sms-file',  'downloadFile')->name('admin.download_sms_file');

});


//Upload
Route::group(['prefix' => 'seller', 'middleware' => ['seller', 'verified', 'user', 'prevent-back-history'], 'as' => 'seller.'], function () {
    Route::controller(AizUploadController::class)->group(function () {
        Route::any('/uploads', 'index')->name('uploaded-files.index');
        Route::any('/uploads/create', 'create')->name('uploads.create');
        Route::any('/uploads/file-info', 'file_info')->name('my_uploads.info');
        Route::get('/uploads/destroy/{id}', 'destroy')->name('my_uploads.destroy');
        Route::post('/bulk-uploaded-files-delete', 'bulk_uploaded_files_delete')->name('bulk-uploaded-files-delete');
    });
});

Route::group(['namespace' => 'App\Http\Controllers\Seller', 'prefix' => 'seller', 'middleware' => ['seller', 'verified', 'user', 'prevent-back-history'], 'as' => 'seller.'], function () {
;
  
  Route::controller(DashboardController::class)->group(function () {
    Route::get('/dashboard', 'index')->name('dashboard');
  });
  Route::get('/dashboard/FCM={token}', [DashboardController::class, 'dashboardWithToken'])->name('dashboard.token');


    // Product
    Route::controller(ProductController::class)->group(function () {
        Route::get('/products', 'index')->name('products');
        Route::get('/product/create', 'create')->name('products.create');
        Route::post('/products/store/', 'store')->name('products.store');
        Route::get('/product/{id}/edit', 'edit')->name('products.edit');
        Route::post('/products/update/{product}', 'update')->name('products.update');
        Route::get('/products/duplicate/{id}', 'duplicate')->name('products.duplicate');
        Route::post('/products/sku_combination', 'sku_combination')->name('products.sku_combination');
        Route::post('/products/sku_combination_edit', 'sku_combination_edit')->name('products.sku_combination_edit');
        Route::post('/products/add-more-choice-option', 'add_more_choice_option')->name('products.add-more-choice-option');
        Route::post('/products/seller/featured', 'updateFeatured')->name('products.featured');
        Route::post('/products/published', 'updatePublished')->name('products.published');
        Route::get('/products/destroy/{id}', 'destroy')->name('products.destroy');
        Route::post('/products/bulk-delete', 'bulk_product_delete')->name('products.bulk-delete');
        Route::post('/product-search', 'product_search')->name('product.search');
        Route::post('/get-selected-products', 'get_selected_products')->name('get-selected-products');

        // category-wise discount set
        Route::get('/categories-wise-product-discount', 'categoriesWiseProductDiscount')->name('categories_wise_product_discount');
        Route::post('/set-product-discount', 'setProductDiscount')->name('set_product_discount');
      
      	// update unit_price
      	Route::post('/products/{id}/update-price', 'updatePrice')->name('products.updatePrice');
      
        //Fabrics
    	Route::get('/fabrics', 'fabrics')->name('fabrics');

    });

    // Product Bulk Upload
    Route::controller(ProductBulkUploadController::class)->group(function () {
        Route::get('/product-bulk-upload/index', 'index')->name('product_bulk_upload.index');
        Route::post('/product-bulk-upload/store', 'bulk_upload')->name('bulk_product_upload');
        Route::group(['prefix' => 'bulk-upload/download'], function() {
            Route::get('/category', 'pdf_download_category')->name('pdf.download_category');
            Route::get('/brand', 'pdf_download_brand')->name('pdf.download_brand');
        });
    });

    // Digital Product
    Route::controller(DigitalProductController::class)->group(function () {
        Route::get('/digitalproducts', 'index')->name('digitalproducts');
        Route::get('/digitalproducts/create', 'create')->name('digitalproducts.create');
        Route::post('/digitalproducts/store', 'store')->name('digitalproducts.store');
        Route::get('/digitalproducts/{id}/edit', 'edit')->name('digitalproducts.edit');
        Route::post('/digitalproducts/update/{product}', 'update')->name('digitalproducts.update');
        Route::get('/digitalproducts/destroy/{id}', 'destroy')->name('digitalproducts.destroy');
        Route::get('/digitalproducts/download/{id}', 'download')->name('digitalproducts.download');
    });

    //Coupon
    Route::resource('coupon', CouponController::class);
    Route::controller(CouponController::class)->group(function () {
        Route::post('/coupon/get_form', 'get_coupon_form')->name('coupon.get_coupon_form');
        Route::post('/coupon/get_form_edit', 'get_coupon_form_edit')->name('coupon.get_coupon_form_edit');
        Route::get('/coupon/destroy/{id}', 'destroy')->name('coupon.destroy');
    });

    //Order
    Route::resource('orders', OrderController::class);
    Route::controller(OrderController::class)->group(function () {
        Route::post('/orders/update_delivery_status', 'update_delivery_status')->name('orders.update_delivery_status');
        Route::post('/orders/update_payment_status', 'update_payment_status')->name('orders.update_payment_status');

        // Order bulk export
        Route::get('/order-bulk-export', 'orderBulkExport')->name('order-bulk-export');
    });

    Route::controller(InvoiceController::class)->group(function () {
        Route::get('/invoice/{order_id}', 'invoice_download')->name('invoice.download');
    });
    // Route::get('invoice/{order_id}',[InvoiceController::class, 'invoice_download'])->name('invoice.download');
    //Review
    Route::controller(ReviewController::class)->group(function () {
        Route::get('/reviews', 'index')->name('reviews');
    });
    // Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews');

    //Shop
    Route::controller(ShopController::class)->group(function () {
        Route::get('/shop', 'index')->name('shop.index');
        Route::post('/shop/update', 'update')->name('shop.update');
        Route::get('/shop/apply-for-verification', 'verify_form')->name('shop.verify');
        Route::post('/shop/verification_info_store', 'verify_form_store')->name('shop.verify.store');
    });

    //Payments
    Route::resource('payments', PaymentController::class);

    // Profile Settings
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'index')->name('profile.index');
        Route::post('/profile/update/{id}', 'update')->name('profile.update');
    });

    // Address
    Route::resource('addresses', AddressController::class);
    Route::controller(AddressController::class)->group(function () {
        Route::post('/get-states', 'getStates')->name('get-state');
        Route::post('/get-cities', 'getCities')->name('get-city');
        Route::post('/address/update/{id}', 'update')->name('addresses.update');
        Route::get('/addresses/destroy/{id}', 'destroy')->name('addresses.destroy');
        Route::get('/addresses/set_default/{id}', 'set_default')->name('addresses.set_default');
    });

    // Money Withdraw Requests
    Route::controller(SellerWithdrawRequestController::class)->group(function () {
        Route::get('/money-withdraw-requests', 'index')->name('money_withdraw_requests.index');
        Route::post('/money-withdraw-request/store', 'store')->name('money_withdraw_request.store');
    });

    // Commission History
    Route::controller(CommissionHistoryController::class)->group(function () {
        Route::get('/commission-history', 'index')->name('commission-history.index');
    });

    //Conversations
    Route::controller(ConversationController::class)->group(function () {
        Route::get('/conversations', 'index')->name('conversations.index');
        Route::get('/conversations/show/{id}', 'show')->name('conversations.show');
        Route::post('conversations/refresh', 'refresh')->name('conversations.refresh');
        Route::post('conversations/message/store', 'message_store')->name('conversations.message_store');
    });

    // product query (comments) show on seller panel
    Route::controller(ProductQueryController::class)->group(function () {
        Route::get('/product-queries', 'index')->name('product_query.index');
        Route::get('/product-queries/{id}', 'show')->name('product_query.show');
        Route::put('/product-queries/{id}', 'reply')->name('product_query.reply');
    });

    // Support Ticket
    Route::controller(SupportTicketController::class)->group(function () {
        Route::get('/support_ticket', 'index')->name('support_ticket.index');
        Route::post('/support_ticket/store', 'store')->name('support_ticket.store');
        Route::get('/support_ticket/show/{id}', 'show')->name('support_ticket.show');
        Route::post('/support_ticket/reply', 'ticket_reply_store')->name('support_ticket.reply_store');
    });

    // Notifications
    Route::controller(NotificationController::class)->group(function () {
        Route::get('/all-notification', 'index')->name('all-notification');
        Route::post('/notifications/bulk-delete', 'bulkDelete')->name('notifications.bulk_delete');
        Route::get('/notification/read-and-redirect/{id}', 'readAndRedirect')->name('notification.read-and-redirect');

    });

});

