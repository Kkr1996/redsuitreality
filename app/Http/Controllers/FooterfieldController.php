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
use App\Footerfield;
use Illuminate\Support\Facades\DB;
use Litepie\Settings\Models\Setting;
use Log;

class FooterfieldController extends BaseController
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
    	$facility = Footerfield::find(1);

        $menus_title = DB::table('menus')->select('name')->where('slug','we-are-ready')->first();
        $menus_title = $menus_title->name;
        return $this->response->setMetaTitle($menus_title)
            ->view('footerfield.edit')
            ->data(compact('user', 'facility','menus_title'))
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

              return $this->response->setMetaTitle(trans('app.new') . ' ' . trans('user::service.name'))
              ->view('footerfield.create', true)
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
            
            $title = $request->title;
            $created_at = date('Y-m-d H:i:s');
            $slug     = $this->create_slug($request->title);
            $info              = $request->info;
            $subtitle          = $request->subtitle;
            $text              = $request->text;
            $values = array(
                'title' =>  $title,
                'subheading'=>$subtitle,
                'descriptions' => $info
            );
            
            DB::table('footerfields')->insert($values);
            
            return $this->response->message(trans('messages.success.created', ['Module' => trans('user::weareready.name')]))
            ->code(204)
            ->status('success')
            ->url(guard_url('weareready'))
            ->redirect();
               


            return $this->response->message(trans('messages.success.created', ['Module' => trans('user::facility.name')]))
                ->code(204)
                ->status('success')
                ->url(guard_url('facility'))
                ->redirect();
            
            
                
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('facility'))
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
            $facility = Footerfield::find($id);
            return $this->response->setMetaTitle(trans('app.edit') . ' ' . trans('user::weareready.name'))
            ->view('footerfield.edit')
            ->data(compact('facility'))
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
            
            $title             = $request->title;
            $info              = $request->descriptions;
            $subtitle          = $request->subheading;
            $text              = $request->text;
            $valuess = array(
                'title'       =>$title,
                'subheading'  =>$subtitle,
                'descriptions'=>$info,
                'text'        =>$text
            );
            DB::table('footerfields')->where('id',$request->id)->update($valuess);
            return redirect()->back();  
            } catch (Exception $e) {
              return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('weareready/'))
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

    public function deletefacilityimage() 
    {


         $imageindex =  $_POST['imageid'];
         $currentservices =  DB::table('facility')
         ->where('id',  $imageindex)
         ->first();
       //  echo '<pre>',var_dump($currentservices); echo '</pre>';
        
            $values = array(
                'image'=>""
            ); 
        
            DB::table('facility')
            ->where('id',$imageindex)
            ->update($values);
    }
    
    public function deleteiconsimage() 
    {


         $imageindex =  $_POST['imageid'];
         $currentservices =  DB::table('facility')
         ->where('id',  $imageindex)
         ->first();
       //  echo '<pre>',var_dump($currentservices); echo '</pre>';
        
            $values = array(
                'icons'=>""
            ); 
        
            DB::table('facility')
            ->where('id',$imageindex)
            ->update($values);
    }

}
