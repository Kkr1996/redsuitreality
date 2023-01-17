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
use App\Faqcategories;
use App\Service;
use App\Faq;
use Illuminate\Support\Facades\DB;
use Litepie\Settings\Models\Setting;
use Log;

class FaqController extends BaseController
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
    	$faq         = Faq::orderBy('order_by','asc')->get();
        foreach($faq as $fkeys=>$fval)
        {
          $categories[$fval->id] =   DB::table('faqcategories')->select('title')->where('slug',$fval->faqs)->first()->title;
        }
        $menus_title = DB::table('menus')->select('name')->where('slug','frequently-asked-questions')->first();
        $menus_title = $menus_title->name;
        return $this->response->setMetaTitle($menus_title)
               ->view('faq.index')
               ->data(compact('faq','menus_title','categories'))
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
            $menus = DB::table('menus')->where('key', 'category')->first();
            $category = DB::table('menus')->where('parent_id',$menus->id)->get();
            $allcategories     = Faqcategories::where('status','publish')->get();
            $menus_title = DB::table('menus')->select('name')->where('slug','frequently-asked-questions')->first();
            $menus_title = $menus_title->name;
            return $this->response->setMetaTitle($menus_title)
            ->view('faq.create', true)
            ->data(compact('category','menus_title','allcategories'))
            ->output();
    }
    public function store(Request $request)
    {
        try { 
            $cnt_testimonials = DB::table('faq_db')->get();     
            $cntall =  count($cnt_testimonials);
            $question = $request->name;
            $answer   = $request->info;
            $faqs     = $request->faqs;
            $created_at = date('Y-m-d H:i:s');
            $values     = array(
                                'question' =>  $question,
                                'answer' => $answer,
                                'created_at'=>$created_at,
                                'faqs'=>$faqs,
                                'order_by'=>$cntall
                           );
            DB::table('faq_db')->insert($values);
            return $this->response->message(trans('messages.success.created', ['Module' => trans('user::service.name')]))
                ->code(204)
                ->status('success')
                ->url(guard_url('faq'))
                ->redirect();
            } 
            catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('faq'))
                ->redirect();
           }
    }
    public function edit($id)
    {
        $faq               = Faq::find($id);
        $allcategories     = Faqcategories::where('status','publish')->get();
        return $this->response->setMetaTitle(trans('app.edit') . ' ' . trans('user::faq.name'))
        ->view('faq.edit')
        ->data(compact('faq','allcategories'))
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
            
             $request->id; 
             $question = $request->question;
             $answer   = $request->answer;
             $faqs   = $request->faqs;
             $values = array('question' =>  $question,'answer' => $answer,'faqs'=>$faqs);
             DB::table('faq_db')
             ->where('id',  $request->id)
             ->update($values);
             return $this->response->message(trans('messages.success.deleted', ['Module' => trans('user::service.name')]))
                ->code(204)
                ->status('success')
                ->url(guard_url('faq'))
                ->redirect();
          }    catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('faq/'))
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
            
            $faq = Faq::find($id);
            $faq->delete();
            return $this->response->message(trans('messages.success.deleted', ['Module' => trans('user::faq.name')]))
                ->code(202)
                ->status('success')
                ->url(guard_url('faq/'))
                ->redirect();

        } catch (Exception $e) {

            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('faq'))
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
            $update =  DB::table('faq_db')
            ->where('id',$getkey)
            ->update($values);
         }
    }


}
