<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'PublicController@home');
Route::any('team/{id}', 'PublicController@team_member');
Route::get('about', 'PublicController@about');
Route::get('contact', 'PublicController@contact');
Route::get('shop', 'PublicController@shops');
Route::get('blog', 'PublicController@blog');

Route::get('notaryform', 'PublicController@notaryforms');


Route::get('buying_at_auction', 'PublicController@buying_at_auctions');
Route::get('selling_at_auction', 'PublicController@selling_at_auctions');
Route::get('signupmarkets', 'PublicController@signupmarkets');
Route::get('comparative_markets_analysis', 'PublicController@comparative_markets_analysis');
Route::get('home_evaluation', 'PublicController@home_bots');


Route::get('helpful-link', 'PublicController@helpful_links');

Route::get('auction', 'PublicController@auctions');

Route::get('properties', 'PublicController@properties');
Route::get('buying', 'PublicController@buying');
Route::get('selling', 'PublicController@selling');
Route::get('selling/{slug}', 'PublicController@selling_serivice');
Route::get('faqs', 'PublicController@faq');
Route::get('agent_trust', 'PublicController@agent_trust');
Route::post('comment', 'BlogController@storecomment');
Route::get('verifyemail/{id}', 'PublicController@verifyemail');
Route::get('results', 'PublicController@results');
Route::get('details', 'PublicController@details');
Route::get('login', 'PublicController@logins');
Route::get('register', 'PublicController@registers');
Route::post('register_action', 'PublicController@registers_action');

Route::get('forgot-password', 'PublicController@forgot_password');
Route::post('forgotemail', 'PublicController@forgotemails');
Route::get('update_password/{slug}', 'PublicController@updatepasswords');
Route::post('customer_updatepasword', 'PublicController@customer_updatepaswords');
Route::get('test', 'PublicController@tests');
/* Submission */
Route::get('paypal', 'PaymentController@index');
Route::post('paypal', 'PaymentController@payWithpaypal');
Route::get('status', 'PaymentController@getPaymentStatus');
Route::post('payment', 'PayPalController@payWithpaypal');
Route::get('status', 'PayPalController@getPaymentStatus');
Route::get('testimonials', 'PublicController@testimonial');
Route::post('submission/contact', 'PublicController@submitSubmission');
Route::get('blog', 'PublicController@blog');
Route::get('category/{slug}', 'PublicController@blog_category');
Route::get('archive/{slug}', 'PublicController@blog_archive');
Route::post('submitform','PublicController@contactform');
Route::post('booknow','PublicController@book_now');
Route::post('messageus','PublicController@message_us');
Route::get('post/{slug}', 'PublicController@blogss');
Route::get('contact-us', 'PublicController@contact');
Route::get('seal-certified', 'PublicController@seal_certified');
Route::get('location/{slug}', 'PublicController@location');
Route::get('faq', 'PublicController@faq');
Route::get('bestdeal', 'PublicController@bestdeal');
Route::get('rebate', 'PublicController@rebate');
Route::get('buyingfast', 'PublicController@buyingfast');
Route::get('listing', 'PublicController@listing');
Route::post('request', 'PublicController@requestform');
Route::get('services/{slug}', 'PublicController@single_service');

Route::get('mortage_calculator', 'PublicController@mortage_calculator');
Route::get('single-services', 'PublicController@single_service');
//cropper
Route::post('image-cropper/upload','ImageCropperController@upload');
//Email Post
Route::post('get-quote','PublicController@get_quote');
Route::post('gettouch','PublicController@getintouch');
Route::get('/prices', 'CheckoutController@home');
Route::post('/charge', 'CheckoutController@charge')->name('product.payment');;
Route::get('/thankyou', 'PublicController@thankyou');
Route::get('/ordersubmit', 'PublicController@ordersubmit');
Route::get('addProduct','CheckoutController@callAddProduct');
Route::post('addProduct','CheckoutController@addProduct');
Route::get('addToCart/{id}','CheckoutController@addToCart');
Route::get('cart', 'CheckoutController@cart');
Route::get('cart/{slug}', 'CheckoutController@cart');
Route::get('checkout', 'CheckoutController@checkout');
Route::post('simply_accelerated','CheckoutController@alcarte');
//FSBO




Route::post('userprofile','CheckoutController@clientprofile');
Route::get('services-designed', 'PublicController@services_designed');
Route::get('simply-accelerating-marketing', 'PublicController@simply_accelerating_marketing');
Route::get('plans', 'PublicController@plans');
Route::post('checkemail', 'PublicController@checkemail');



//Auth

Route::post('password/email', 'PasswordController@getemails');
Route::get('forgotpasswords/{slug}', 'PasswordController@usersupdatepasswords');
Route::post('admin_updatepasword', 'PasswordController@admin_updatepasword');






Route::prefix('{guard}')->name('guard.')->group(function () {
    
    Auth::routes(['verify' => true]);
    Route::get('/', 'ResourceController@home');
    Route::resource('/redsuitdifference', 'TeamMemberController');
    Route::post('/redsuitdifference/update', 'TeamMemberController@update');
    Route::post('/redsuitdiff_orderby', 'TeamMemberController@orderby');
    
    Route::resource('/section', 'FrontSectionController');
    Route::post('/section/update', 'FrontSectionController@update');
    Route::post('/section/create', 'FrontSectionController@store');
    Route::resource('/quickform', 'QuickFormController');
    Route::post('/quickform/update', 'QuickFormController@update');
    Route::post('/quickform/sendemail', 'QuickFormController@mailtouser');
    Route::resource('/property', 'PropertyController');
    Route::post('/property/update', 'PropertyController@update');
    Route::resource('/submission', 'SubmissionController');
    Route::get('/submission/form/{slug}', 'SubmissionController@form_type');  
    Route::get('/theme', 'ThemeController@index');
    Route::post('/theme/update', 'ThemeController@update');    
    Route::resource('/service', 'ServiceController');
    Route::post('/service/update', 'ServiceController@update');
    Route::post('/mysetting/update/{key}', 'MySettingController@update');
    
    Route::get('login/{provider}', 'Auth\SocialAuthController@redirectToProvider');
    
    Route::resource('/faq', 'FaqController');
    Route::post('/faq/update', 'FaqController@update');
    Route::post('/faq_orderby', 'FaqController@orderby');
    
    
    Route::resource('/pagesetting', 'PagesettingController');
    Route::post('/pagesetting/update', 'PagesettingController@update'); 
    Route::resource('/comment', 'BlogmanageController');
    Route::post('/comment/update', 'BlogmanageController@update'); 
    Route::resource('/pagesetting', 'PagesettingController'); 
    Route::resource('/product', 'FacilityController');
    Route::post('/product/update', 'FacilityController@update'); 
    Route::post('/product_orderby', 'FacilityController@orderby'); 
    
    
    Route::resource('/emailtemplate', 'EmailtemplateController');
    Route::post('/emailtemplate/update', 'EmailtemplateController@update');
    Route::resource('/categories', 'AuctionController');
    Route::post('/categories/update', 'AuctionController@update');
    Route::post('/product_categories_orderby', 'AuctionController@orderby');
    
    
    Route::resource('/redsuitstack', 'LocationController');
    Route::post('/redsuitstack_orderby', 'LocationController@orderby');
    
    
    Route::resource('/simplyaccelerate', 'SimplyacceleratedController');
    Route::post('/simplyaccelerate_orderby', 'SimplyacceleratedController@orderby');
    
    
    Route::resource('/servicesdesigned', 'ServicesdesignedController');
    Route::post('/servicesdesigned/update', 'ServicesdesignedController@update');
    Route::post('/servicesdesigned_orderby', 'ServicesdesignedController@orderby');
    
    Route::resource('/notification', 'NotificationController');
    Route::post('/notification/update', 'NotificationController@update');
    Route::post('/notification/removeimage', 'NotificationController@removeimage');
    Route::post('/submission/deletemail', 'SubmissionController@deletemail');
    /* read or unread submissions */
    Route::post('/submission/readmail', 'SubmissionController@readmail');
    Route::post('/submission/unreadmail', 'SubmissionController@unreadmail');
    Route::resource('/library', 'LibraryController');
    Route::post('/library/deleteimage', 'LibraryController@deleteimage');
    Route::post('/library/deleteimagemap', 'LibraryController@deleteimagemap');
    Route::post('/library/update', 'LibraryController@update');
    Route::any('/library/getlibrary', 'LibraryController@getlibrary');
    Route::post('/facility/getmedia', 'FacilityController@getmedia');
    //cropper
    Route::post('image-cropper/upload','ImageCropperController@upload');
    Route::post('/location/deleteimage', 'LocationController@deleteimage');
    Route::post('/location/deleteimagemap', 'LocationController@deleteimagemap');
    
    Route::post('/service/deleteimage', 'ServiceController@deleteimage');
    Route::post('/service_orderby', 'ServiceController@orderby');
    
    Route::post('/facility/deletefacilityimage', 'FacilityController@deletefacilityimage');
    Route::post('/facility/deleteiconsimage', 'FacilityController@deleteiconsimage');
    Route::post('/facility/deletetailor', 'FacilityController@deletetailor');
    Route::post('/service/upload', 'ServiceController@uploadprofile');
    Route::post('/redsuitstack/update', 'LocationController@update');
    Route::post('/simplyaccelerate/update', 'SimplyacceleratedController@update');
    Route::resource('/footer', 'FooterSettingController');
    Route::post('/footer/update', 'FooterSettingController@update');
    
    Route::resource('/testimonials', 'TestimonialsController');
    Route::post('/testimonials/update', 'TestimonialsController@update');  
    Route::post('/testimonials_orderby', 'TestimonialsController@orderby');
    
    
    Route::resource('/faqcategories', 'FaqcategoriesController');
    Route::post('/faqcategories/update', 'FaqcategoriesController@update');
    Route::post('/faqcategories_orderby', 'FaqcategoriesController@orderby');
    
    
    
    Route::post('/serviceuser/uploaduser', 'ServiceController@uploaduserprofile'); 
    Route::post('/serviceuser/uploadicons', 'ServiceController@uploadicons'); 
    Route::resource('/footersetting','FooterfieldController');  
    Route::post('/footersetting/update','FooterfieldController@update');
    Route::resource('/customer', 'ShopController');
    Route::post('/customer/customerupdate', 'ShopController@customerupdate');
    Route::resource('/woo_settings', 'PaymentsettingController');
    Route::post('/woo_settings/update','PaymentsettingController@update');
    Route::resource('/woocommerce_emails_settings','WooCommerceController');
    Route::post('/woocommerce_emails_settings/update','WooCommerceController@update');
    Route::any('/wooguests', 'ShopController@guest');
    Route::any('/guestedit/{slug}', 'ShopController@guestedit');
    
    
    Route::post('/wooguests_delete', 'ShopController@wooguests_deletes');
    Route::post('/customer_delete', 'ShopController@customer_deletes');
    Route::get('/shop_order', 'ShopController@orders');
    
    Route::get('/order_status/{slug}', 'ShopController@order_status');
    Route::post('/order_statuss/update', 'ShopController@update');
    Route::post('/orders_delete', 'ShopController@orders_deletes');
    Route::post('/medialibrary/deleteimage', 'LibraryController@deleteimage');
    
});

Route::any('/{page?}','PublicController@notfound');