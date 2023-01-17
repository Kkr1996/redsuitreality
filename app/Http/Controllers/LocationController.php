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
use App\Location;
use Illuminate\Support\Facades\DB;
use Litepie\Settings\Models\Setting;
use Log;
use Session;
class LocationController extends BaseController
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
    	$location = Location::orderBy('order_by','asc')->get();
        
        $menus_title = DB::table('menus')->select('name')->where('slug','location')->first();
        $menus_title = $menus_title->name;
        
        return $this->response->setMetaTitle($menus_title)
            ->view('location.index')
            ->data(compact('user', 'location','menus_title'))
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
        $menus_title = DB::table('menus')->select('name')->where('slug','location')->first();
        $menus_title = $menus_title->name;
        return $this->response->setMetaTitle($menus_title)
        ->view('location.create', true)
        ->data(compact('menus_title'))
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
        try {
            
            
             $cnt_location = DB::table('location')->get();     
             $cntall =  count($cnt_location);
             $locationname        = $request->location_name;
             $home_service           = $request->home;
             $traditional_realstate           = $request->traditional_realstate;
             $status           = $request->status;
             $created_at          = date('Y-m-d H:i:s');
             $slug                = $this->create_slug($request->location_name);
             $values = array(
                'location_name'     =>$locationname,
                'home'              =>$home_service,
                'traditional_realstate_firm' =>$traditional_realstate,
                'created_at'        =>$created_at,
                'slug'              =>$slug,
                'status'           =>$status,
                'order_by'        =>$cntall
             
             );
             DB::table('location')->insert($values);
            
             return $this->response->message(trans('messages.success.created', ['Module' => trans('user::location.name')]))
             ->code(204)
             ->status('success')
             ->url(guard_url('redsuitstack'))
             ->redirect();
            
         
           }  catch (Exception $e) {
              return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('redsuitstack'))
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
        $location = Location::find($id);
        return $this->response->setMetaTitle(trans('app.edit') . ' ' . trans('user::location.name'))
        ->view('location.edit')
        ->data(compact('location'))
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
             $locationname        = $request->location_name;
             $home_service           = $request->home;
             $traditional_realstate           = $request->traditional_realstate;
             $created_at          = date('Y-m-d H:i:s');
             $slug                = $this->create_slug($request->location_name);
             $status           = $request->status;

            
            $values = array(
                'location_name'     =>$locationname,
                'home'              =>$home_service,
                'traditional_realstate_firm' =>$traditional_realstate,
                'created_at'        =>$created_at,
                'slug'                  =>$slug,
                'status'           =>$status
            );
            
             DB::table('location')
             ->where('id',  $request->id)
             ->update($values);
            return redirect()->back();
            
            
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('redsuitstack/'))
                ->redirect();
        }

    }
    /**
     * Remove the team.
     *
     * @param Model   $team
     *
     * @return Response
     */
    public function destroy($id)
    {
        
        try {
            
            $location = Location::find($id);
            $location->delete();
            return $this->response->message(trans('messages.success.deleted', ['Module' => trans('user::location.location_name')]))
                ->code(202)
                ->status('success')
                ->url(guard_url('location/'))
                ->redirect();

        } catch (Exception $e) {

            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('location'))
                ->redirect();
        }

    }

    /**
     * Restore deleted teams.
     *
     * @param Model   $team
     *
     * @return Response
     */
    public function restore(Request $request)
    {
        try {
            $ids = hashids_decode($request->input('ids'));
            $this->repository->restore($ids);

            return $this->response->message(trans('messages.success.restore', ['Module' => trans('user::team.name')]))
                ->status("success")
                ->code(202)
                ->url(guard_url('/teams/team'))
                ->redirect();

        } catch (Exception $e) {

            return $this->response->message($e->getMessage())
                ->status("error")
                ->code(400)
                ->url(guard_url('/user/team/'))
                ->redirect();
        }

    }

    /**
     * Attach a user to a team.
     *
     * @param Request $request
     * @param Model   $team
     *
     * @return Response
     */
    public function attach(Request $request)
    {
        try {
            $attributes = $request->all();

            $team = $this->repository->attach($attributes);
            return $this->response->message(trans('messages.success.attached', ['Module' => trans('user::team.name')]))
                ->code(204)
                ->status('success')
                ->url(guard_url('user/team/' . $team->getRouteKey()))
                ->redirect();
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('user/team/' . $team->getRouteKey()))
                ->redirect();
        }

    }
    /**
     * Detach a user from a team.
     *
     * @param Request $request
     * @param Model   $team
     *
     * @return Response
     */
    public function detach(Request $request)
    {
        try {
            $attributes = $request->all();
            $team = $this->repository->detach($attributes);
            return $this->response->message(trans('messages.success.detached', ['Module' => trans('user::team.name')]))
                ->code(204)
                ->status('success')
                ->url(guard_url('user/team/' . $team->getRouteKey()))
                ->redirect();
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('user/team/' . $team->getRouteKey()))
                ->redirect();
        }

    }
    
    /**
     * Create slug for title of service
    */
    public function create_slug($string){
       $slug=preg_replace('/[^A-Za-z0-9-]+/', '-', $string);
       return strtolower($slug);
    }


    public function testing()
    {

        echo "string";
    }
    public function deleteimage() 
    {


        $imageindex =  $_POST['imageid'];
        $id =  $_POST['locationid'];
 

        $currentlocation =  DB::table('location')
        ->where('id',  $id)
        ->first();
        $ourgallerypathnew = array();
        $ourgallerypath = array();
        if($currentlocation->ourgallery)
        {
            foreach(unserialize($currentlocation->ourgallery) as $keyc=>$pathval) 
            {
                if($keyc != $imageindex)
                {
                   $ourgallerypath[] = $pathval;
                }

            }
        }
        
        $values = array(
        'ourgallery'=>serialize($ourgallerypath)
        ); 
        DB::table('location')
        ->where('id',$id)
        ->update($values);
   
    
    }
    public function deleteimagemap() 
    {
    
         $imageindex =  $_POST['imageid'];
       


         $currentservices =  DB::table('location')
         ->where('id',  $imageindex)
         ->first();
        
       //  echo '<pre>',var_dump($currentservices); echo '</pre>';
        
            $values = array(
                'location_image'=>""
            ); 
        
            DB::table('location')
            ->where('id',$imageindex)
            ->update($values);
            
    }
    public function orderby()
    {
         $sectionid =  $_POST['sectionid'];
          foreach ($sectionid as $key => $getkey) {
            $values = array(
                'order_by'=>$key
            ); 
            $update =  DB::table('location')
            ->where('id',$getkey)
            ->update($values);
         }
    } 

}
