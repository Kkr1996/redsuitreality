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
use App\Property;
use Illuminate\Routing\UrlGenerator;
use Log;

class PropertyController extends BaseController
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
    	$properties = Property::all();

        return $this->response->setMetaTitle('Property')
            ->view('property.index')
            ->data(compact('user', 'properties'))
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

        return $this->response->setMetaTitle(trans('app.new') . ' Property')
            ->view('property.create', true)
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

            $property = Property::create();

            $property->name = $request->name;
            $property->info = $request->info;
            $property->category = $request->category;
            $property->type = $request->type;
            $property->price = $request->price;
            $property->square = $request->square;
            $property->size = $request->size;
            $property->condition = $request->condition;
            $property->construction = $request->construction;
            $property->zoning = $request->zoning;
            $property->dimension = $request->dimension;

            $property->meta_title = $request->meta_title;
            $property->meta_keyword = $request->meta_keyword;
            $property->meta_description = $request->meta_description;

            $image = $request->image;
            if (!empty($image)) {

                // Make a image name based on user name and current timestamp
                $name = config('app.name').'-'.str_slug(Input::get('name'));
                // Define folder path
                $folder = '/uploads/images/property/';
                // Make a file path where image will be stored [ folder path + file name + file extension]
                $filePath = url('/').'/storage'.$folder . $name. '.' . $image->getClientOriginalExtension();
                // Upload image
                $this->uploadOne($image, $folder, 'public', $name);

                // Set user profile image path in database to filePath
                $property->file = $filePath;
            }

            $property->save();

            return $this->response->message(trans('messages.success.created', ['Module' => trans('user::property.name')]))
                ->code(204)
                ->status('success')
                ->url(guard_url('property'))
                ->redirect();
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('property'))
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
        $property = Property::find($id);

        return $this->response->setMetaTitle(trans('app.edit') . ' ' . trans('user::property.name'))
            ->view('property.edit')
            ->data(compact('property'))
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
            $property = Property::find($request->id);
            
            $property->name = $request->name;
            $property->info = $request->info;
            $property->category = $request->category;
            $property->type = $request->type;
            $property->price = $request->price;
            $property->square = $request->square;
            $property->size = $request->size;
            $property->condition = $request->condition;
            $property->construction = $request->construction;
            $property->zoning = $request->zoning;
            $property->dimension = $request->dimension;

            $property->meta_title = $request->meta_title;
            $property->meta_keyword = $request->meta_keyword;
            $property->meta_description = $request->meta_description;
            
            
            $image = $request->image;
            $folder = '/uploads/images/property/';
            if (!empty($image)) {

                $name = config('app.name').'-'.str_slug($request->name);
                
                $filePath = url('/').'/storage'.$folder . $name. '.' . $image->getClientOriginalExtension();
                
                $this->uploadOne($image, $folder, 'public', $name);

                $property->image = $filePath;
            }

            $property->save();

            return $this->response->message(trans('messages.success.deleted', ['Module' => trans('user::property.name')]))
                ->code(204)
                ->status('success')
                ->url(guard_url('property'))
                ->redirect();
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('property/'))
                ->redirect();
        }

    }

    /**
     * Remove the property.
     *
     * @param Model   $property
     *
     * @return Response
     */
    public function destroy($id)
    {
        try {
            
            $property = Property::find($id);
            $name = $property->name;
            $property->delete();
            return $this->response->message(trans('messages.success.deleted', ['Module' => $name]))
                ->code(202)
                ->status('success')
                ->url(guard_url('property/'))
                ->redirect();

        } catch (Exception $e) {

            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('property'))
                ->redirect();
        }

    }

}
