<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as BaseController;
use App\Http\Response\ResourceResponse;
use Litepie\Theme\ThemeAndViews;
use Litepie\User\Traits\RoutesAndGuards;
use Litepie\User\Traits\UserPages;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\File; 
use App\Traits\UploadTrait;
use App\MySetting;
use App\Service;
use App\Facility;
use App\Location;
use App\Library;
use Illuminate\Support\Facades\DB;
use Litepie\Settings\Models\Setting;
use Log;
use Session;

class LibraryController extends BaseController
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
    	$user = $request->user()->toArray();
    	$library = Library::orderBy('id', 'desc')->get();
   
        $menus_title = DB::table('menus')->select('name')->where('slug','footer-setting')->first();
        $menus_title = $menus_title->name;

        return $this->response->setMetaTitle($menus_title)
            ->view('library.index')
            ->data(compact('user', 'library','menus_title'))
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
            $menus_title = DB::table('menus')->select('name')->where('slug','footer-setting')->first();
            $menus_title = $menus_title->name;

              return $this->response->setMetaTitle($menus_title)
              ->view('location.create', true)
              ->data(compact('user','page_setting','menus_title'))
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
        //Log::info("store");
        try {


            $ourgallerypath=array();
            if($files=$request->file('ourlibrary')){
                
                foreach($files as $key=>$file){
                        $folder   = 'storage/uploads/';
                        $name     = uniqid().rand(0,1000).$file->getClientOriginalName();
                    
                    
                        $fileSize = $file->getClientSize();
                        $ext = $file->getClientOriginalExtension();
                    
                        $original_name = $file->getClientOriginalName();
                    
                  
                            
                            
                        $destinationPathGallery = public_path('storage/uploads/');
                        $ourgallerypath = $folder . $name;
                        $file->move($destinationPathGallery,$name);
                        $created_at = date('Y-m-d H:i:s');
                    
                    
                        $values = array('filename'=>$ourgallerypath,'updated_date' => $created_at,'created_date'=>$created_at,'filesize'=>$fileSize,'extension'=>$ext,'originalname'=>$original_name);
                        DB::table('library')->insert($values);
                    
                    } 
            }
    
            return $this->response->message(trans('messages.success.created', ['Module' => trans('user::location.name')]))
            ->code(204)
            ->status('success')
            ->url(guard_url('library'))
            ->redirect();
               


                
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('media'))
                ->redirect();
        }

    }
    public function getlibrary()
    {
      $library = Library::orderBy('id', 'desc')->get();
      $data = '';
       foreach ($library as $key => $value) {
         $data .='<div class="col-xl-4 col-lg-4 col-12">
                 <div class="wrap-image">
                        <img src="'.url('public').'/'.$value->filename.'">
                  </div>
             </div>';
       }
       return $data;
    }
    public function deleteimage() 
    {
        $image_id  = $_POST['alldata']['imageid'];
        $imagepath = $_POST['alldata']['imagepath'];
        
        $image_path_d = public_path($imagepath);
        File::delete($image_path_d);


        $values = array(
            'filename'=>""
        ); 
        DB::table('library')
        ->where('id',$image_id)
        ->delete();
    }
    

}
