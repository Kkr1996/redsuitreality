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
use App\Submission;
use Illuminate\Support\Facades\DB;
use Litepie\Settings\Models\Setting;
use Log;
use Session;
use App\Auction;


class SimplyacceleratedController extends BaseController
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
    	$simplyaccelerates = Auction::where("categories","simply_accelerates")->orderBy('order_by','asc')->get();
        $menus_title = DB::table('menus')->select('name')->where('slug','simple-accelerated-marketing')->first();
        $menus_title = $menus_title->name;
        
        return $this->response->setMetaTitle($menus_title)
            ->view('simplyaccelerate.index')
            ->data(compact('user', 'simplyaccelerates','menus_title'))
            ->output();
    }
    public function show($id)
    {
        $service = Auction::find($id);
        return $this->response->setMetaTitle(trans('app.view') . ' ' . trans('user::service.name'))
            ->data(compact('service'))
            ->view('service.show')
            ->output();
    }
    public function create(Request $request)
    {
        $menus_title = DB::table('menus')->select('name')->where('slug','simple-accelerated-marketing')->first();
        $menus_title = $menus_title->name;
        return $this->response->setMetaTitle($menus_title)
        ->view('simplyaccelerate.create', true)
        ->data(compact('menus_title'))
        ->output();
    }
    public function store(Request $request)
    {
        try {
            
             $cntsimplyaccelerates       = Auction::where("categories","simply_accelerates")->get();
             $cntall                     = count($cntsimplyaccelerates);

             $title                  = $request->title;
             $all_included           = $request->all_included;
             $alacarte               = $request->alacarte;
             $price                  = $request->price;
             $status                 = $request->status;
             $created_at             = date('Y-m-d H:i:s');
            
             $values = array(
                'name'               =>$title,
                'all_included'       =>$all_included,
                'price'              =>$price,
                'categories'         =>"simply_accelerates",
                'status'             =>$status,
                'created_at'         =>$created_at,
                'order_by'           =>$cntall
             );
             DB::table('auctions')->insert($values);

             return $this->response->message(trans('messages.success.created', ['Module' => trans('user::location.name')]))
             ->code(204)
             ->status('success')
             ->url(guard_url('simplyaccelerate'))
             ->redirect();
             return $this->response->message(trans('messages.success.created', ['Module' => trans('user::facility.name')]))
                ->code(204)
                ->status('success')
                ->url(guard_url('simplyaccelerate'))
                ->redirect();
           }  catch (Exception $e) {
              return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('simplyaccelerate'))
                ->redirect();
        }

    }
    public function edit($id)
    {
        $simplyaccelerate = Auction::find($id);

        return $this->response->setMetaTitle(trans('app.edit') . ' ' . trans('user::simplyaccelerates.name'))
        ->view('simplyaccelerate.edit')
        ->data(compact('simplyaccelerate'))
        ->output();

    }
    public function update(Request $request)
    {
        try {   
             $title                  = $request->name;
             $all_included           = $request->all_included;
             $alacarte               = $request->alacarte;
             $price                  = $request->price;
             $updated_at             = date('Y-m-d H:i:s');
             $status                 = $request->status;
             $values = array(
                'name'               =>$title,
                'all_included'       =>$all_included,
                'price'              =>$price,
                'categories'         =>"simply_accelerates",
                'status'             =>$status,
                'updated_at'         =>$updated_at
             );
             DB::table('auctions')
             ->where('id',$request->id)
             ->update($values);
            return $this->response->message(trans('messages.success.deleted', ['Module' => trans('user::service.name')]))
                ->code(204)
                ->status('success')
                ->url(guard_url('simplyaccelerate'))
                ->redirect();
           } catch (Exception $e) {
             return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('simplyaccelerate/'))
                ->redirect();
        }
    }
    public function destroy($id)
    { 
        try {
            $location = Auction::find($id);
            $location->delete();
            return $this->response->message(trans('messages.success.deleted', ['Module' => trans('user::location.location_name')]))
                ->code(202)
                ->status('success')
                ->url(guard_url('simplyaccelerate/'))
                ->redirect();

          } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('simplyaccelerate'))
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
            $update =  DB::table('auctions')
            ->where('id',$getkey)
            ->update($values);
         }
    } 

}
