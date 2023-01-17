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
use App\Category;
use Illuminate\Support\Facades\DB;
use Litepie\Settings\Models\Setting;
use Log;
use File;
class ServiceController extends BaseController
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

    	$services    = Service::orderBy('order_by','asc')->get();
    	$category    = Category::where('status','publish')->get();
        $menus_title = DB::table('menus')->select('name')->where('slug','blog')->first();
        $menus_title = $menus_title->name;
        
        return $this->response->setMetaTitle($menus_title)
            ->view('service.index')
            ->data(compact('services','menus_title','category'))
            ->output();
    }


    /**
     * Display team.
     *
     * @param Request $request
     * @param Model   $team
     *
     * @return Response
     */
    public function show($id)
    {
        $service = Service::find($id);
        
        return $this->response->setMetaTitle(trans('app.view') . ' ' . trans('user::service.name'))
            ->data(compact('service'))
            ->view('service.show')
            ->output();
    }

    /**
     * Show the form for creating a new team.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function create(Request $request)
    {
              $menus       = DB::table('menus')->where('key', 'category')->first();
              $category    = DB::table('menus')->where('parent_id',$menus->id)->get();
              $menus_title = DB::table('menus')->select('name')->where('slug','blog')->first();
              $menus_title = $menus_title->name;
        
              $product_category    = Category::where('status','publish')->get();
        
              return $this->response->setMetaTitle(trans('app.new') . ' ' . trans('user::service.name'))
              ->view('service.create', true)
              ->data(compact('category','menus_title','product_category'))
              ->output();
    }

    /**
     * Create new team.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function store(Request $request)
    {
        Log::info("store");
        try {
            
            
            $allservice = Service::all();
            
            
            $service = Service::create();
            $service->name = $request->name;
            $service->info = $request->info;
            $service->slug  = $this->create_slug($request->name);
            $slug           = $this->create_slug($request->name);
            $service->hover_color  = $request->hover_color;
            $service->meta_title   = $request->meta_title;
            $service->meta_keyword = $request->meta_keyword;
            $service->meta_description = $request->meta_description;
            $service->publish_date = $request->publish_date;
            $service->category_id  = $request->category;
            $service->status       = $request->status;
            $service->product_categories       = $request->product_categories;
            
            $image                  = $request->featured_img;
            $slug_menus             = $slug.rand(99,999);
         
            
            
            $service->slug_menus   = $slug_menus;
            $service->order_by     = count($allservice);
         
            
            //Menu Create By Custom Approach 
            
            $parent_id_menu  = 111;
            $totalcount =   DB::table('menus')
            ->where('parent_id',$parent_id_menu)->get();
            
            if($request->status == "Published")
            {
                $status_menus    = 1;  
            }
            else
            {
                $status_menus    = 0;  
            }
            $name_menu       = $request->name;
           
            $url_menu        = "services/".$slug;
            $order_menu      = count($totalcount);
            
         
            
            $values_menus    = array(
                
                                     'name'=>$name_menu,
                                     'url'=>$url_menu,
                                     'parent_id'=>$parent_id_menu,
                                     'order'=>$order_menu,
                                     'status'=>$status_menus,
                                     'slug'=>$slug_menus,
                                    );
            DB::table('menus')->insert($values_menus);
            
           //Menu Create By Custom Approach  
            
            
            
            
            
            
            
            
            if (!empty($image)) {
                $folder = '/uploads/images/services/';
                $image = $request->file('featured_img');
                $name  = time().'.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('/storage/uploads/images/services/');
                $filePath = $folder . $name;
                $image->move($destinationPath, $name);
                $service->icon = $filePath;      
            }
            
            Log::info(print_r($service, true));
            $service->save();
            return $this->response->message(trans('messages.success.created', ['Module' => trans('user::service.name')]))
                ->code(204)
                ->status('success')
                ->url(guard_url('service'))
                ->redirect();
                
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('service'))
                ->redirect();
        }

    }

    /**
     * Show team for editing.
     *
     * @param Request $request
     * @param Model   $team
     *
     * @return Response
     */
    public function edit($id)
    {
          $service = Service::find($id);
          $menus = DB::table('menus')->where('key', 'category')->first();
          $category = DB::table('menus')->where('parent_id',$menus->id)->get();
          $product_category    = Category::where('status','publish')->get();
          return $this->response->setMetaTitle(trans('app.edit') . ' ' . trans('user::service.name'))
          ->view('service.edit')
          ->data(compact('service','category','product_category'))
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

    /**
     * Update the team.
     *
     * @param Request $request
     * @param Model   $team
     *
     * @return Response
     */
    public function update(Request $request)
    {
        try {
            $service = Service::find($request->id);
            $service->name = $request->name;
            $service->info = $request->info;
            $service->hover_color = $request->hover_color;
            $service->meta_title = $request->meta_title;
            $service->meta_keyword = $request->meta_keyword;
            $service->meta_description = $request->meta_description;
            $service->publish_date = $request->publish_date;
            $service->category_id = $request->category;
            $service->status = $request->status;
            $service->product_categories = $request->product_categories;
            
            $status = $request->status;
            $image  = $request->featured_img;

            //Menus Update
            $slugmenus  = $request->slugmenus;
            if($status == "Published")
            {
                $status_menus    = 1;  
            }
            else
            {
                $status_menus    = 0;  
            }
            $values = array('status'=>$status_menus);
            if($slugmenus)
            {
                DB::table('menus')
                ->where('slug',  $slugmenus)
                ->update($values);  
            }
            
            if (!empty($image)) {
                $folder = '/uploads/images/services/';
                $image = $request->file('featured_img');
                $name  = time().'.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('/storage/uploads/images/services/');
                $filePath = $folder . $name;
                $image->move($destinationPath, $name);
                $service->icon = $filePath;
            }
            $service->save();
            return redirect()->back();
            return $this->response->message(trans('messages.success.deleted', ['Module' => trans('user::service.name')]))
                ->code(204)
                ->status('success')
                ->url(guard_url('service'))
                ->redirect();
        } 
        catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('service/'))
                ->redirect();
        }

    }
    public function destroy($id)
    {  
        try {
            $service = Service::find($id);
            $service->delete();
            return $this->response->message(trans('messages.success.deleted', ['Module' => trans('user::service.name')]))
            ->code(202)
            ->status('success')
            ->url(guard_url('service/'))
            ->redirect();

          } catch (Exception $e) {
            return $this->response->message($e->getMessage())
            ->code(400)
            ->status('error')
            ->url(guard_url('service'))
            ->redirect();
        }
    }
    public function create_slug($string){
       $slug=preg_replace('/[^A-Za-z0-9-]+/', '-', $string);
       return strtolower($slug);
    }
    public function deleteimage() 
    {

        $filepath      =  $_POST['filepath'];
        $imageindex      =  $_POST['imageid'];
        $currentservices =  DB::table('services')
        ->where('id',  $imageindex)
        ->first();
        $image_path = public_path("/storage".$filepath."");
        File::delete($image_path);
        $values = array(
        'icon'=>""
        ); 
        DB::table('services')
        ->where('id',$imageindex)
        ->update($values); 
    }
    
    public function uploaduserprofile(Request $request)
    {
            $username   =  $request->username;
            $users      =   DB::table('users')->where('name',$username)->get();
            $user_id = $users[0]->id;
      

            $image = $request->image;
            if (!empty($image)) {
                $folder = '/uploads/images/services/';
                $image = $request->file('image');
                $name  = time().'.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('/storage/uploads/images/services/');
                $filePath = $folder . $name;
                $image->move($destinationPath, $name);
                $url    = url('admin/user/user');
                $values = array(
                    'image'=>$name
                 ); 
                DB::table('users')
                ->where('id',$user_id)
                ->update($values);
                return redirect()->away($url);
                
            }

    }
    public function uploadicons(Request $request)
    {
         $image = $request->image;
         $un_id = $request->un_id;
         $par_id = $request->par_id;
         if (!empty($image)) {
            $folder = '/uploads/images/services/';
            $image = $request->file('image');
            $name  = time().'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('/storage/uploads/images/services/');
            $filePath = $folder . $name;
            $image->move($destinationPath, $name);
            
             $values = array(
                'upload_folder'=>$filePath
             );
            
             DB::table('menus')
             ->where('id', $un_id)->where('parent_id',$par_id)
             ->update($values);
             return redirect()->back();
        }
    }
    public function uploadprofile(Request $request)
    {   
        
         $username   =  user()->name;
         $users      =  DB::table('users')->where('name',$username)->get();
         $user_id    =  $users[0]->id;
         $image      =  $request->image;
        
         if (!empty($image)) {  
            $folder  = '/uploads/images/services/';
            $image   = $request->file('image');
            $name    = time().'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('/storage/uploads/images/services/');
            $filePath = $folder . $name;
            $image->move($destinationPath, $name);
            $url    = url('admin/profile');
            $values = array(
                'image'=>$name
            ); 
            DB::table('users')
            ->where('id',$user_id)
            ->update($values);
            return redirect()->away($url);
        }  
    }
    public function orderby()
    {
          $sectionid = $_POST['sectionid'];
   
          foreach ($sectionid as $key => $getkey) {
            $values = array(
                'order_by'=>$key
            ); 
            $update =  DB::table('services')
            ->where('id',$getkey)
            ->update($values);
         }
    }
    

}
