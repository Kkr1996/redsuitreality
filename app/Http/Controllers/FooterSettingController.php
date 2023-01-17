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
use App\Footer;
use App\Blog;
use App\Faq;
use Illuminate\Support\Facades\DB;
use Litepie\Settings\Models\Setting;
use Log;

class FooterSettingController extends BaseController
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
        $footer = Footer::all();
        $menus_title = DB::table('menus')->select('name')->where('slug','we-are-ready')->first();
        $menus_title = $menus_title->name;
        return $this->response->setMetaTitle($menus_title)
            ->view('footer.index')
            ->data(compact('user', 'footer','menus_title'))
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
              $menus = DB::table('menus')->where('key', 'category')->first();
              $category = DB::table('menus')->where('parent_id',$menus->id)->get();
              $menus_title = DB::table('menus')->select('name')->where('slug','we-are-ready')->first();
              $menus_title = $menus_title->name;
        
              return $this->response->setMetaTitle($menus_title)
              ->view('footer.create', true)
              ->data(compact('category','menus_title'))
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
            $heading               = $request->heading;
            $message               = $request->message;
            $mobile                = $request->mobile;
            $email                 = $request->email;
            $location_we_serving   = $request->location_we_serving;
            $header_mobile_no      = $request->header_mobile_no;
            $postal_email          = $request->postal_email;
            $location_desc          = $request->location_desc;
            $created_at            = date('Y-m-d H:i:s');
   	         
            
            
            
            
            
            
            $values = array(
                'heading'                        =>$heading,
                'message'                        =>$message,
                'mobile'                         =>$mobile,
                'email'                          =>$email,
                'location_we_serving'            =>$location_we_serving,
                'header_mobile_no'               =>$header_mobile_no,
                'postal_email'                   =>$postal_email,
                'location_desc'                  =>$location_desc,
                'created_at'                     =>$created_at,
               );
            DB::table('footersetting')->insert($values);
            return $this->response->message(trans('messages.success.created', ['Module' => trans('user::footer.name')]))
                ->code(204)
                ->status('success')
                ->url(guard_url('footer'))
                ->redirect();
                
            } 
            catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('footer'))
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
        
        $blog = Footer::find($id);
        return $this->response->setMetaTitle(trans('app.edit') . ' ' . trans('user::blog.name'))
        ->view('footer.edit')
        ->data(compact('blog'))
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
            
            $heading               = $request->heading;
            $message               = $request->message;
            $mobile                = $request->mobile;
            $email                 = $request->email;
            $location_we_serving   = $request->location_we_serving;
            
            

            $header_mobile_no      = $request->header_mobile_no;
            $postal_email          = $request->postal_email;
            $location_desc          = $request->location_desc;
            $created_at            = date('Y-m-d H:i:s');
            $values = array(
                'heading'                        =>$heading,
                'message'                        =>$message,
                'mobile'                         =>$mobile,
                'email'                          =>$email,
                'location_we_serving'            =>$location_we_serving,
                'header_mobile_no'               =>$header_mobile_no,
                'postal_email'                   =>$postal_email,
                'location_desc'                  =>$location_desc,
                'created_at'                     =>$created_at,
               );
             DB::table('footersetting')
             ->where('id',  $request->id)
             ->update($values);
            
   
            
            
             return $this->response->message(trans('messages.success.deleted', ['Module' => trans('user::service.name')]))
                ->code(204)
                ->status('success')
                ->url(guard_url('footer'))
                ->redirect();
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('footer/'))
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
            
            $blog = Footer::find($id);
            $blog->delete();
            return $this->response->message(trans('messages.success.deleted', ['Module' => trans('user::blog.id')]))
                ->code(202)
                ->status('success')
                ->url(guard_url('footer/'))
                ->redirect();

        } catch (Exception $e) {

            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('footer'))
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
}
