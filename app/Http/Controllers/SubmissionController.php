<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as BaseController;
use App\Http\Response\ResourceResponse;
use Litepie\Theme\ThemeAndViews;
use Litepie\User\Traits\RoutesAndGuards;
use Litepie\User\Traits\UserPages;
use Illuminate\Support\Facades\Input;
use App\Service;
use App\Submission;
use Illuminate\Support\Facades\DB;
use Log;

class SubmissionController extends BaseController
{
	use RoutesAndGuards, ThemeAndViews, UserPages;
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
    	$submissions = Submission::orderBy('id', 'desc')->get();
        $menus_title = DB::table('menus')->select('name')->where('slug','contact-us')->first();
        $menus_title = $menus_title->name;
        
        return $this->response->setMetaTitle($menus_title)
            ->view('submission.index')
            ->data(compact('user', 'submissions','menus_title'))
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

        $submission = Submission::find($id);
        $values = array(
        'status'     =>"inactive",
        );

        DB::table('submissions')
        ->where('id',  $id)
        ->update($values);
        $url = url('admin');
        if(empty($_GET['status'])){
        header("Refresh: 0.001; url=$url/submission/$id?status=1");
        }  
        //
        return $this->response->setMetaTitle(trans('app.view') . ' ' . trans('user::service.name'))
            ->data(compact('submission'))
            ->view('submission.show')
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
        $menus_title = DB::table('menus')->select('name')->where('slug','contact-us')->first();
        $menus_title = $menus_title->name;
        return $this->response->setMetaTitle($menus_title)
            ->view('submission.create', true)
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
    public function store()
    {
        try {

            $submission = Submission::create();

            $submission->name = Input::get('name');
            $submission->info = Input::get('info');
            $submission->download_link = Input::get('download_link');
            $submission->pdf = Input::get('pdf');
            $submission->icon = Input::get('icon');

            $submission->save();

            return $this->response->message(trans('messages.success.created', ['Module' => trans('user::submission.name')]))
                ->code(204)
                ->status('success')
                ->url(guard_url('submission'))
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
        $submission = Submission::find($id);

       // die();
  
        
        return $this->response->setMetaTitle(trans('app.edit') . ' ' . trans('user::submission.name'))
            ->view('submission.edit')
            ->data(compact('submission'))
            ->output();
    }


    public function destroy($id)
    {
        
        try {
            
            $service = Submission::find($id);
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
    public function form_type($slug)
    {

    	$submissions = Submission::where('slug',$slug)->orderBy('id', 'desc')->get();
        
        $menus_title = DB::table('menus')->select('name')->where('slug',$slug)->first();
        
        $submissions_cnt = Submission::where('slug',$slug)->orderBy('id', 'desc')->get()->count();
        
        
        $menus_title = $menus_title->name;
        
        return $this->response->setMetaTitle($menus_title)
            ->view('submission.index')
            ->data(compact('submissions','menus_title','submissions_cnt'))
            ->output();
        
    }
    public function deletemail(Request $request)
    {
        $getid = $request->getsel;
        $delete =  DB::table('submissions')->whereIn('id', $getid)->delete(); 
        if($delete)
        {
            return redirect()->back();
        }
        else
        {
            echo "false";
        }
        

    }
    
    public function readmail(Request $request)
    {
        $getid = $request->getsel;
        $delete =  DB::table('submissions')->whereIn('id', $getid)->update(['status' => 'inactive']);
        if($delete)
        {
            return redirect()->back();
        }
        else
        {
            echo "false";
        }
    }

    public function unreadmail(Request $request)
    {
        $getid = $request->getsel;
        // echo '<pre>',var_dump($getid); echo '</pre>';

        $delete =  DB::table('submissions')->whereIn('id', $getid)->update(['status' => 'active']);
        if($delete)
        {
            return redirect()->back();
        }
        else
        {
            echo "false";
        }
    }

}
