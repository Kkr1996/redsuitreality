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
use App\TeamMember;
use Log;
use Illuminate\Support\Facades\DB;
class TeamMemberController extends BaseController
{
	use RoutesAndGuards, ThemeAndViews, UserPages, UploadTrait;
    /**
     * Initialize public controller.
     *
     * @return null
     */
    public function __construct()
    {
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
    	$team = TeamMember::orderBy('order_by','asc')->get();

        $menus_title = DB::table('menus')->select('name')->where('slug','team-member')->first();
        $menus_title = $menus_title->name;
        
        return $this->response->setMetaTitle($menus_title)
            ->view('team.index')
            ->data(compact('user', 'team','menus_title'))
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

        $team_member = TeamMember::find($id);

        return $this->response->setMetaTitle(trans('app.view') . ' ' . trans('user::team.name'))
            ->data(compact('team_member'))
            ->view('team.show')
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

        $menus_title = DB::table('menus')->select('name')->where('slug','team-member')->first();
        $menus_title = $menus_title->name;
        return $this->response->setMetaTitle($menus_title)
            ->view('team.create', true)
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

             $team_member = TeamMember::create();
             $team_member->title = $request->title;
             $team_member->descriptions = $request->descriptions;
             $image = $request->featured_img;
             if (!empty($image)) {

              
                $folder = '/uploads/images/redsuitdiff/';
                $image = $request->file('featured_img');
                $name  = time().'.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('/storage/uploads/images/redsuitdiff/');
                $filePath = $folder . $name;
                $image->move($destinationPath, $name);
                $team_member->icon = $filePath;
             }
             $team_member->save();

            return $this->response->message(trans('messages.success.created', ['Module' => trans('user::team.name')]))
                ->code(204)
                ->status('success')
                ->url(guard_url('redsuitdifference'))
                ->redirect();
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('redsuitdifference'))
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
        $team_member = TeamMember::find($id);

        return $this->response->setMetaTitle(trans('app.edit') . ' ' . trans('user::team.name'))
            ->view('team.edit')
            ->data(compact('team_member'))
            ->output();
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
   
   
             $team_member = TeamMember::find($request->id);

             $team_member->title = $request->title;
             $team_member->descriptions = $request->descriptions;
        
      
            $image = $request->featured_img;
            
            if (!empty($image)) {

               
                $folder = '/uploads/images/redsuitdiff/';
                $image = $request->file('featured_img');
                $name  = time().'.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('/storage/uploads/images/redsuitdiff/');
                $filePath = $folder . $name;
                $image->move($destinationPath, $name);
                
                $team_member->icon = $filePath;

            }

            $team_member->save();
            

            return $this->response->message(trans('messages.success.deleted', ['Module' => trans('user::team.name')]))
                ->code(204)
                ->status('success')
                ->url(guard_url('redsuitdifference'))
                ->redirect();
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('redsuitdifference/'))
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
            $team_member = TeamMember::find($id);
            $team_member->delete();
            return $this->response->message(trans('messages.success.deleted', ['Module' => trans('user::team.name')]))
                ->code(202)
                ->status('success')
                ->url(guard_url('team/'))
                ->redirect();

        } catch (Exception $e) {

            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('team'))
                ->redirect();
        }

    }
    public function orderby()
    {
         $sectionid =  $_POST['sectionid'];
          foreach ($sectionid as $key => $getkey) {
            $values = array(
                'order_by'=>$key
            ); 
            $update =  DB::table('members')
            ->where('id',$getkey)
            ->update($values);
         }
    }
}
