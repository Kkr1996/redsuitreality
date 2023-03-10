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
use App\Faqcategories;
use Illuminate\Support\Facades\DB;
use Litepie\Settings\Models\Setting;
use Log;

class FaqcategoriesController extends BaseController
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
    	$allcategories     = Faqcategories::orderBy('order_by','asc')->get();
        $menus_title       = DB::table('menus')->select('name')->where('slug','categories')->first();
        $menus_title       = $menus_title->name;
        return $this->response->setMetaTitle($menus_title)
            ->view('faqcategories.index')
            ->data(compact('menus_title','allcategories'))
            ->output();
    }

    public function create(Request $request)
    {
        
              $menus = DB::table('menus')->where('key', 'category')->first();
              $category = DB::table('menus')->where('parent_id',$menus->id)->get();
              $menus_title = DB::table('menus')->select('name')->where('slug','categories')->first();
              $menus_title = $menus_title->name;
              return $this->response->setMetaTitle($menus_title)
              ->view('faqcategories.create', true)
              ->data(compact('category','menus_title'))
              ->output();
    }

    public function store(Request $request)
    {

      try {

              $allfaqcategories             = Faqcategories::all();
              $faqcategories                = Faqcategories::create();
              $faqcategories->title         = $request->title;
              $faqcategories->slug          = $this->create_slug($request->title);
              $faqcategories->status        = $request->status;
              $faqcategories->order_by      = count($allfaqcategories);
              $faqcategories->save();

              return $this->response->message(trans('messages.success.created', ['Module' => trans('user::facility.name')]))
                   ->code(204)
                   ->status('success')
                   ->url(guard_url('faqcategories'))
                   ->redirect();
            
           } 
           catch (Exception $e) {
                return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('categories'))
                ->redirect();
           }

    }

    public function edit($id)
    {
          $data = Faqcategories::find($id);
          return $this->response->setMetaTitle('Categories')
          ->view('faqcategories.edit')
          ->data(compact('data'))
          ->output();
    }

    public function update(Request $request)
    {

        try {    
                $faqcategories                = Faqcategories::find($request->id);
                $faqcategories->title         = $request->title;
                $faqcategories->status        = $request->status;
                $faqcategories->save();  
                return redirect()->back();
            } 
            catch (Exception $e) {
                return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('categories/'))
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
            
            $facility = Faqcategories::find($id);
            $facility->delete();
            return $this->response->message(trans('messages.success.deleted', ['Module' => trans('user::facility.title')]))
                   ->code(202)
                   ->status('success')
                   ->url(guard_url('categorys/'))
                   ->redirect();

        } catch (Exception $e) {

            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('auction'))
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
            $update =  DB::table('faqcategories')
            ->where('id',$getkey)
            ->update($values);
         }
    }

}
