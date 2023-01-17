<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as BaseController;
use App\Http\Response\ResourceResponse;
use Litepie\Theme\ThemeAndViews;
use Litepie\User\Traits\RoutesAndGuards;
use Litepie\User\Traits\UserPages;
use Illuminate\Support\Facades\Input;
use App\Traits\UploadTrait;
use App\MySetting;
use App\Service;
use App\Facility;
use App\Auction;
use App\Category;
use Illuminate\Support\Facades\DB;
use Litepie\Settings\Models\Setting;
use Log;

class FacilityController extends BaseController
{
	use RoutesAndGuards, ThemeAndViews, UserPages, UploadTrait;
    /**
     * Initialize public controller.
     *
     * @return null
     */
    public function __construct()
    {
        Log::info('helo');
        guard(request()->guard . '.web');
        $this->middleware('auth:' . guard());
        $this->middleware('verified:guard.verification.notice');
        $this->middleware('role:' . $this->getGuardRoute());
        $this->response = app(ResourceResponse::class);
        $this->setTheme();
    }
    public function index(Request $request)
    {
    	$user        = $request->user()->toArray();
    	$facility    = Auction::where('categories','!=','simply_accelerates')->orderBy('order_by','asc')->get();
        $menus_title = DB::table('menus')->select('name')->where('slug','all-products')->first();
        $menus_title = $menus_title->name;
        $category    = Category::where('status','publish')->get();
        foreach($facility as $key=>$val){
            $category      = $val->slug;  
            $categ_array   = DB::table('categorys')->select('name')->where('slug',$category)->first(); 
            $categ__name[] = $categ_array->name;   
        }
        return $this->response->setMetaTitle($menus_title)
            ->view('facility.index')
            ->data(compact('user', 'facility','slug','menus_title','categ__name'))
            ->output();
    }
    public function show($id)
    {
        $service = Service::find($id);
        
        return $this->response->setMetaTitle(trans('app.view') . ' ' . trans('user::service.name'))
            ->data(compact('service'))
            ->view('service.show')
            ->output();
    }
    public function create(Request $request)
    {
          $menus = DB::table('menus')->where('key', 'category')->first();
          $category = Category::where('status','publish')->get();
          $menus_title = DB::table('menus')->select('name')->where('slug','all-products')->first();
          $menus_title = $menus_title->name;
          return $this->response->setMetaTitle($menus_title)
          ->view('facility.create', true)
          ->data(compact('category','menus_title'))
          ->output();
    }
    public function store(Request $request)
    {
        try {
            
            $allauctions    = Auction::where('categories','!=','simply_accelerates')->get();
            $cntall         = count($allauctions);
            
            
            $title               = $request->title;
            $created_at          = date('Y-m-d H:i:s');
            $info                = $request->info;
            $subtitle            = $request->subtitle;
            $text                = $request->text;
            $slug                = $request->categories;
            $categories          = $request->categories;
            $price               = $request->price;
            $status              = $request->status;
            $heading             = $request->heading;
            $legal_disclaimer         = $request->legal_disclaimer;
            $values = array(
                'name'      =>$title,
                'title'     =>$subtitle,
                'text'      =>$text,
                'info'      =>$info,
                'created_at'=>$created_at,
                'slug'      =>$slug,
                'categories'=>$categories,
                'price'     =>$price,
                'status'    =>$status,
                'heading'   =>$heading,
                'legal_disclaimer'=>$legal_disclaimer,
                'order_by'        =>$cntall
            
            );
            DB::table('auctions')->insert($values);
            return redirect('admin/product');    
                
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('facility'))
                ->redirect();
        }

    }
    public function edit($id)
    {
            $facility = Auction::find($id);
        	$category = Category::where('status','publish')->get();
            return $this->response->setMetaTitle('Edit Products')
            ->view('facility.edit')
            ->data(compact('facility','category'))
            ->output();
    }
    
    public function other(Request $request)
    {
        try {
            $hover_setting = Setting::where('key', 'service.hover.color')->get();
            $hover_setting->value = $request->color;
            Log::info($hover_setting);
            $hover_setting->save();
            return $this->response->message(trans('messages.success.deleted', ['Module' => trans('user::service.name')]))
                ->code(204)
                ->status('success')
                ->url(guard_url('service'))
                ->redirect();
            
            } catch (Exception $e) {
             return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('service/'))
                ->redirect();
            } 
    }
    public function update(Request $request)
    {
        try {                           
            $title              = $request->name;
            $info               = $request->info;
            $subtitle           = $request->title;
            $text               = $request->text;
            $description        = $request->description;
            $price              = $request->price;
            $status             = $request->status;
            $heading            = $request->heading;
            $categories         = $request->categories;
            $legal_disclaimer         = $request->legal_disclaimer;
            $values = array(
                            'name'      =>$title,
                            'info'      =>$info,
                            'title'     =>$subtitle,
                            'text'      =>$text,
                            'price'     =>$price,
                            'status'    =>$status,
                            'heading'   =>$heading,
                            'slug'      =>$categories,
                            'legal_disclaimer'=>$legal_disclaimer
                           );

           DB::table('auctions')
           ->where('id',  $request->id)
          ->update($values);
          return redirect()->back();
            
            
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('facility/'))
                ->redirect();
        }

    }
    public function destroy($id)
    {
        try{
            $facility = Facility::find($id);
            $facility->delete();
            return $this->response->message(trans('messages.success.deleted', ['Module' => trans('user::facility.title')]))
                ->code(202)
                ->status('success')
                ->url(guard_url('facility/'))
                ->redirect();
          } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('facility'))
                ->redirect();
        }

    }
    public function create_slug($string){
       $slug=preg_replace('/[^A-Za-z0-9-]+/', '-', $string);
       return strtolower($slug);
    }

    public function deletefacilityimage() 
    {
         $imageindex =  $_POST['imageid'];
         $currentservices =  DB::table('facility')
         ->where('id',  $imageindex)
         ->first();
        $values = array(
            'image'=>""
        ); 

        DB::table('facility')
        ->where('id',$imageindex)
        ->update($values);
    }
    public function deleteiconsimage() 
    {
        $imageindex      =  $_POST['imageid'];
        $currentservices =  DB::table('facility')
        ->where('id',  $imageindex)
        ->first();
        $values = array(
            'icons'=>""
        ); 
        DB::table('facility')
        ->where('id',$imageindex)
        ->update($values);
    }
    public function deletetailor()
    {
        $dataid        =  $_POST['dataid'];
        DB::table('auctions')
        ->where('id',$dataid)
        ->delete();
    }
    public function orderby()
    {
         $sectionid =  $_POST['sectionid'];
          foreach ($sectionid as $key => $getkey) {
            $values = array(
                'order_by'=>$key
            ); 
            $update =  DB::table('auctions')
            ->where('id',$getkey)
            ->update($values);
         }
    }

}
