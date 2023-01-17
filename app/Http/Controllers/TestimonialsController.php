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
use Illuminate\Support\Facades\DB;
use Litepie\Settings\Models\Setting;
use Log;


class TestimonialsController extends BaseController
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
    	$user =  $request->user()->toArray();
        $data =  DB::table('testimonials')->orderBy('order_by', 'asc')->get();
        
        $menus_title = DB::table('menus')->select('name')->where('slug','what-makes-us-different')->first();
        $menus_title = $menus_title->name;
        
        
        return $this->response->setMetaTitle($menus_title)
            ->view('testimonials.index')
            ->data(compact('data','menus_title'))
            ->output();
    }

    public function create(Request $request)
    {
             $menus_title = DB::table('menus')->select('name')->where('slug','what-makes-us-different')->first();
             $menus_title = $menus_title->name;
              return $this->response->setMetaTitle($menus_title)  
              ->view('testimonials.create')
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
            $cnt_testimonials = DB::table('testimonials')->get();     
            $cntall =  count($cnt_testimonials);

            $title = $request->title;
            $image = $request->featured_img;
            $description = $request->description;
            $status = $request->status;
            $filePath = '';
            if (!empty($image)) {
                $name = config('app.name').'-'.str_slug(Input::get('name')).'-facility';
                $folder = '/uploads/images/facility/';
                $image = $request->file('featured_img');
                $name  = time().'.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('/storage/uploads/images/facility/');
                $filePath = $folder . $name;
                $image->move($destinationPath, $name);
            }
            $values = array( 
                'title' =>$title,
                'icons'=>$filePath,
                'status'=>$status,
                'description'=>$description,
                'order_by'=>$cntall
            );
            DB::table('testimonials')->insert($values);
            
            return $this->response->message(trans('messages.success.created', ['Module' => trans('user::facility.name')]))
            ->code(204)
            ->status('success')
            ->url(guard_url('testimonials'))
            ->redirect();
                
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('testimonials'))
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
                 
        $data = DB::table('testimonials')
           ->where('id',$id)->first();
        return $this->response->setMetaTitle(trans('app.edit') . ' ' . trans('user::makediffent.title'))
        ->view('testimonials.edit')
        ->data(compact('data'))
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
               
            $title       = $request->title;
            $image       = $request->featured_img;
            $description = $request->description;
            $status      = $request->status;
            if (!empty($image)) {

                $folder = '/uploads/images/facility/';
                $image = $request->file('featured_img');
                $name  = time().'.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('/storage/uploads/images/facility/');
                $filePath = $folder . $name;
                $image->move($destinationPath, $name);

            }
            else
            {
                $filePath = $request->is_icons;
            }

            $values = array('title'=>$title,'icons'=>$filePath,'status'=>$status,'description'=>$description);
            DB::table('testimonials')
            ->where('id',  $request->id)
            ->update($values);
            return redirect()->back();
                
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('testimonials/'))
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
            
            DB::delete('delete from testimonials where id = ?',[$id]);
            
           
            return $this->response->message(trans('messages.success.deleted', ['Module' => trans('user::success')]))
                ->code(202)
                ->status('success')
                ->url(guard_url('makeusdifferent/'))
                ->redirect();

        } catch (Exception $e) {

            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('makeusdifferent'))
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


    public function orderby()
    {
         $sectionid =  $_POST['sectionid'];
          foreach ($sectionid as $key => $getkey) {
            $values = array(
                'order_by'=>$key
            ); 
            $update =  DB::table('testimonials')
            ->where('id',$getkey)
            ->update($values);
         }
    } 

}
