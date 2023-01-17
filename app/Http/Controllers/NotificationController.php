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
class NotificationController extends BaseController
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
         $notification = DB::table('notification')->get();
        
         $menus_title = DB::table('menus')->select('name')->where('slug','notification')->first();
         $menus_title = $menus_title->name;

         return $this->response->setMetaTitle($menus_title)
            ->view('notification.index')
            ->data(compact('user', 'notification','menus_title'))
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

                $menus_title = DB::table('menus')->select('name')->where('slug','notification')->first();
                $menus_title = $menus_title->name;
                return $this->response->setMetaTitle($menus_title)
                ->view('notification.create', true)
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
            
            
             $title                     = $request->name;
             $image                     = $request->image;
             $heading                   = $request->heading;
             $text_color                = $request->text_color;
             $background_color          = $request->background_color;
             $text                      = $request->text;
             $created_at                = date('Y-m-d H:i:s');
             $slug                      = $this->create_slug($request->name);
             $duration             = $request->duration;

             $active                    = $request->status;
             $position             = $request->position;
             $border_color         = $request->border_color;

            if (!empty($image)) {
                $folder   = '/uploads/images/notification/';
                $image    = $request->file('image');
                $filename = $image->getClientOriginalName();
                $name     = $filename.'-'.time().'.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('/storage/uploads/images/notification/');
                $filePathmap = $folder . $name;
                $image->move($destinationPath, $name);
            }
            else
            {
               $filePathmap =''; 
            }
            
            $values = array(
                'name'              =>$title,
                'text'              =>$text,
                'heading'           =>$heading,
                'image'             =>$filePathmap,
                'text_color'        =>$text_color,
                'background_color'  =>$background_color,
                'created_at'        =>$created_at,
                'slug'              =>$slug,
                'status'            =>$active,
                'position'          =>$position,
                'border_color'      =>$border_color,
                'duration'          =>$duration
            );

            DB::table('notification')->insert($values);
            
            return $this->response->message(trans('messages.success.created', ['Module' => trans('user::location.name')]))
            ->code(204)
            ->status('success')
            ->url(guard_url('notification'))
            ->redirect();
               


            return $this->response->message(trans('messages.success.created', ['Module' => trans('user::facility.name')]))
                ->code(204)
                ->status('success')
                ->url(guard_url('notification'))
                ->redirect();
                
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('notification'))
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
        $notification = DB::table('notification')->where('id',$id)->first();
        return $this->response->setMetaTitle(trans('app.edit') . ' ' . trans('user::notification.name'))
        ->view('notification.edit')
        ->data(compact('notification'))
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


              $title                = $request->name;
              $heading              = $request->heading;
              $text_color           = $request->text_color;
              $background_color     = $request->background_color;
              $image                = $request->image;
              $text                 = $request->text;
              $active               = $request->status;
              $position             = $request->position;
              $border_color         = $request->border_color;
              $duration             = $request->duration;
            
            
            
 
             if (!empty($image)) {
                $folder          = '/uploads/images/notification/';
                $image           = $request->file('image');
                $filename        = $image->getClientOriginalName();
                $name            = $filename.'-'.time().'.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('/storage/uploads/images/notification/');
                $filePathmap     = $folder . $name;
                $image->move($destinationPath, $name);
            }
            else
            {
                 $filePathmap = $request->imagepath;
            }
                
             $values = array(
                'name'              =>$title,
                'text'              =>$text,
                'heading'           =>$heading,
                'image'             =>$filePathmap,
                'text_color'        =>$text_color,
                'background_color'  =>$background_color,
                'status'            =>$active,
                'position'          =>$position,
                'border_color'      =>$border_color,
                'duration'          =>$duration  
            ); 
            
             DB::table('notification')
             ->where('id',  $request->id)
             ->update($values);
           
         
            return $this->response->message(trans('messages.success.deleted', ['Module' => trans('user::service.name')]))
                ->code(204)
                ->status('success')
                ->url(guard_url('notification'))
                ->redirect();
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('notification/'))
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

    public function removeimage() 
    {
    
    
        $row_id =  $_POST['row_id'];
        $values = array(
            'image'=>""
        ); 
        DB::table('notification')
        ->where('id',$row_id)
        ->update($values);
            
    }
    

}
