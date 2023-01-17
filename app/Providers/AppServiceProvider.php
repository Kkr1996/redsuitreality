<?php

namespace App\Providers;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use app\Footer;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
  
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        error_reporting(0);

        if(!isset($_COOKIE['myuserid_2'])) {
              setcookie("myuserid_2", uniqid() , time()+60*60*24); 
        }


         view()->composer('*', function($view) {
            if (Auth::check()) {

                $view->with('cartitems', DB::table('carts')->where('user_id',auth()->user()->id)->get());
            } 
            else
            {
                 $view->with('cartitems', DB::table('sessioncarts')->where('user_id', $_COOKIE['myuserid_2'])->get());
            }  
         });
   
         View::share('key', 'value');

         Schema::defaultStringLength(191);
         
         $footervalue        = DB::table('contacts')->get();
         $googleanalytics    = DB::table('page_setting')->get();
         $book_now           = DB::table('quick_forms')->where('slug',"book-now")->get();
         $message_us         = DB::table('quick_forms')->where('slug',"message-us")->get();
        
        
        
        
        
         $footerfields         = DB::table('footerfields')->where('id',"1")->get();
        
        
        
         $blognotification   = DB::table('blog_comment')->where("notification",0)->get();

         $pop_up             = DB::table('notification')->where("slug","pop-up-style-")->where('status','show')->get(); 
         $location           = DB::table('location')->get();
         $social_menus       = DB::table('menus')->where('parent_id',6)->orderBy('order','asc')->get();
        
        
         //Get current mail notification
        
         $request_form_notify   = DB::table('submissions')->where('slug',"request-form")->where('status',"active")->get();
         $contact_us_notify     = DB::table('submissions')->where('slug',"contact-us")->where('status',"active")->get();
         $message_us_notify     = DB::table('submissions')->where('slug',"message-us")->where('status',"active")->get();
        
         $userpayments_notify   = DB::table('userpayments')->where('new_notification',0)->get();
        
         $total_submission    = DB::table('submissions')->where('status',"active")->get();
         $adminlogoo          = DB::table('admin_logo')->get();
         $pagess              = DB::table('pages')->get();
         $users               = DB::table('users')->get();
        
        
         $services_global     = DB::table('services')->where('status','Published')->get();
        
         //Get current mail notification
        
         $values = array(
             'footervalue'        =>$footervalue,
             'location'           =>$location,
             'blognotification'   =>$blognotification,
             'googleanalytics'    =>$googleanalytics,
             'book_now'           =>$book_now,
             'message_us'         =>$message_us,
             'bannernotification' =>$bannernotification,
             'pop_up'             =>$pop_up,
             'request_form_notify'=>$request_form_notify,
             'contact_us_notify'  =>$contact_us_notify,
             'message_us_notify'  =>$message_us_notify,
             'total_submission'   =>$total_submission,
             'adminlogo'          =>$adminlogoo,
             'pages'              =>$pagess,
             'users'              =>$users,
             'social_menus'       =>$social_menus,
             'footerfields'       =>$footerfields,
             'services_global'    =>$services_global,
             'userpayments_notifys'=>$userpayments_notify
          );
        
         View::share($values);  
    }
}
