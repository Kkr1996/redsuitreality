<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as BaseController;
use Litepie\User\Traits\RoutesAndGuards;
use App\Http\Response\ResourceResponse;
use Litepie\Theme\ThemeAndViews;
use App\Traits\UploadTrait;
use App\FrontSection;
use Log;
class FrontSectionController extends BaseController
{
	use RoutesAndGuards, ThemeAndViews, UploadTrait;
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
    	$sections = FrontSection::all();
        $menus_title = DB::table('menus')->select('name')->where('slug','we-are-ready')->first();
        $menus_title = $menus_title->name;
        return $this->response->setMetaTitle($menus_title)
            ->view('frontsection.index')
            ->data(compact('user', 'sections','menus_title'))
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

        $section = FrontSection::find($id);

        return $this->response->setMetaTitle(trans('app.view') . ' ' . trans('user::frontsection.name'))
            ->data(compact('section'))
            ->view('frontsection.show')
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

        return $this->response->setMetaTitle(trans('app.new') . ' ' . trans('user::frontsection.name'))
            ->view('frontsection.create', true)
            ->output();
    }

    /**
     * Create new section.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function store(Request $request)
    {
        try {

            $section = FrontSection::create();

            $section->name = $request->name;
            $section->heading = $request->heading;
            $section->body = $request->body;
            
            $image = $request->image;
            
            if (!empty($image)) {

                // Make a image name based on user name and current timestamp
                $name = str_slug($request->name).'_'.time();
                // Define folder path
                $folder = '/uploads/images/frontsections/';
                // Make a file path where image will be stored [ folder path + file name + file extension]
                $filePath = $folder . $name. '.' . $image->getClientOriginalExtension();
                // Upload image
                $this->uploadOne($image, $folder, 'public', $name);

                // Set user profile image path in database to filePath
                $section->image = $filePath;
            }

            $section->save();

            return $this->response->message(trans('messages.success.created', ['Module' => trans('user::frontsection.name')]))
                ->code(204)
                ->status('success')
                ->url(guard_url('section'))
                ->redirect();
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('section'))
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
        $section = FrontSection::find($id);

        return $this->response->setMetaTitle(trans('app.edit') . ' HomepageSection')
            ->view('frontsection.edit')
            ->data(compact('section'))
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

            $section = FrontSection::find($request->id);

            $section->name = $request->name;
            $section->heading = $request->heading;
            $section->body = $request->body;
            
            $image = $request->image;
            
            if (!empty($image)) {

                // Make a image name based on user name and current timestamp
                $name = str_slug($request->name).'_'.time();
                // Define folder path
                $folder = '/uploads/images/frontsections/';
                // Make a file path where image will be stored [ folder path + file name + file extension]
                $filePath = $folder . $name. '.' . $image->getClientOriginalExtension();
                
                Log::info($filePath);
                // Upload image
                $this->uploadOne($image, $folder, 'public', $name);

                // Set user profile image path in database to filePath
                $section->image = $filePath;
            }

            $section->save();

            return $this->response->message(trans('messages.success.deleted', ['Module' => trans('user::frontsection.name')]))
                ->code(204)
                ->status('success')
                ->url(guard_url('section'))
                ->redirect();
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('section'))
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
            
            $section = FrontSection::find($id);
            $section->delete();
            return $this->response->message(trans('messages.success.deleted', ['Module' => trans('user::frontsection.name')]))
                ->code(202)
                ->status('success')
                ->url(guard_url('section'))
                ->redirect();

        } catch (Exception $e) {

            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('section'))
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
                ->url(guard_url('/section'))
                ->redirect();

        } catch (Exception $e) {

            return $this->response->message($e->getMessage())
                ->status("error")
                ->code(400)
                ->url(guard_url('/section'))
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
}
