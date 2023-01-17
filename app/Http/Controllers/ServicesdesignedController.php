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
use App\Simplyaccelerate;
use App\Servicesdesigned;
use App\Submission;
use Illuminate\Support\Facades\DB;
use Litepie\Settings\Models\Setting;
use Log;
use Session;


class ServicesdesignedController extends BaseController
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
    	$user              = $request->user()->toArray();
    	$simplyaccelerates = Servicesdesigned::orderBy('order_by','asc')->get();
        $menus_title = DB::table('menus')->select('name')->where('slug','services-designed-marketing')->first();
        $menus_title = $menus_title->name;
        
        return $this->response->setMetaTitle($menus_title)
            ->view('servicesdesigned.index')
            ->data(compact('user', 'simplyaccelerates','menus_title'))
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
        $menus_title = DB::table('menus')->select('name')->where('slug','services-designed-marketing')->first();
        $menus_title = $menus_title->name;
        return $this->response->setMetaTitle($menus_title)
        ->view('servicesdesigned.create', true)
        ->data(compact('menus_title'))
        ->output();
    }

    public function store(Request $request)
    {
        try {
            
             $allcnt   = Servicesdesigned::orderBy('order_by','asc')->get();
            
            
             $title                  = $request->title;
             $fsbo                   = $request->fsbo;
             $assisted_fsbo          = $request->assisted_fsbo;
             $agent_fsbo             = $request->agent_fsbo;
             $full_agent             = $request->full_agent;
             $values = array(
                'title'              =>$title,
                'fsbo'               =>$fsbo,
                'assisted_fsbo'      =>$assisted_fsbo,
                'agent_fsbo'         =>$agent_fsbo,
                'full_agent'         =>$full_agent,
                'order_by'           =>count($allcnt)
             );
             DB::table('servicesdesigneds')->insert($values);
             return $this->response->message(trans('messages.success.created', ['Module' => trans('user::servicesdesigned.title')]))
             ->code(204)
             ->status('success')
             ->url(guard_url('servicesdesigned'))
             ->redirect();
             return $this->response->message(trans('messages.success.created', ['Module' => trans('user::servicesdesigne.title')]))
                ->code(204)
                ->status('success')
                ->url(guard_url('servicesdesigned'))
                ->redirect();
           }  catch (Exception $e) {
              return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('servicesdesigned'))
                ->redirect();
        }

    }
    public function edit($id)
    {
        $simplyaccelerate = Servicesdesigned::find($id);

        return $this->response->setMetaTitle(trans('app.edit') . ' ' . trans('user::simplyaccelerates.name'))
        ->view('servicesdesigned.edit')
        ->data(compact('simplyaccelerate'))
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
             $title                  = $request->title;
             $fsbo                   = $request->fsbo;
             $assisted_fsbo          = $request->assisted_fsbo;
             $agent_fsbo             = $request->agent_fsbo;
             $full_agent             = $request->full_agent;
             $values = array(
                'title'              =>$title,
                'fsbo'               =>$fsbo,
                'assisted_fsbo'      =>$assisted_fsbo,
                'agent_fsbo'         =>$agent_fsbo,
                'full_agent'         =>$full_agent
             );
             DB::table('servicesdesigneds')
             ->where('id',  $request->id)
             ->update($values);
            return $this->response->message(trans('messages.success.deleted', ['Module' => trans('user::service.name')]))
                ->code(204)
                ->status('success')
                ->url(guard_url('servicesdesigned'))
                ->redirect();
            }  catch (Exception $e) {
               return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('servicesdesigned/'))
                ->redirect();
        }

    }
    public function destroy($id)
    {
        try {
            
            $location = Servicesdesigned::find($id);
            $location->delete();
            return $this->response->message(trans('messages.success.deleted', ['Module' => trans('user::location.location_name')]))
                ->code(202)
                ->status('success')
                ->url(guard_url('servicesdesigned/'))
                ->redirect();

        } catch (Exception $e) {

            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('servicesdesigned'))
                ->redirect();
        }
    }
    public function create_slug($string){
       $slug=preg_replace('/[^A-Za-z0-9-]+/', '-', $string);
       return strtolower($slug);
    }

    public function orderby()
    {
          $sectionid =  $_POST['sectionid'];
          foreach ($sectionid as $key => $getkey) {
            $values = array(
                'order_by'=>$key
            ); 
            $update =  DB::table('servicesdesigneds')
            ->where('id',$getkey)
            ->update($values);
         }
    } 

}
