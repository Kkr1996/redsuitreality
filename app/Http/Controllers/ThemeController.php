<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as BaseController;
use App\Http\Response\ResourceResponse;
use Litepie\Theme\ThemeAndViews;
use Litepie\User\Traits\RoutesAndGuards;
use Litepie\User\Traits\UserPages;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;

use App\Traits\UploadTrait;
use App\MySetting;

use Litepie\Settings\Models\Setting;

use File;
use Log;

class ThemeController extends BaseController
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
    	
    	
    	$customize_style = MySetting::where('key', 'customize_style')->first();
    	$data = json_decode($customize_style->value);

        return $this->response->setMetaTitle('Customize')
            ->view('theme.index')
            ->data(compact('user', 'data'))
            ->output();
    }
    
    public function update(Request $request)
    {
       try {
           
           $data = "";
           $data .= ".button{background-color:".$request->button_color." !important;}";
           
           $data .= "a.link-customize{color:".$request->link_color.";}";
           $data .= "section span{color:".$request->font_color." !important;}";
           $data .= ".body{background-color: ".$request->background_color." !important;}";
           $data .= ".main-header{background-color: ".$request->menu_color." !important;}";
           
    	   $fileName = 'customize.css';
    	   
    	   $json_data = json_encode([$request->button_color, $request->link_color, $request->font_color, $request->background_color, $request->menu_color ]);
    	   
    	   $customize_style = MySetting::where('key', 'customize_style')->first();
    	   $customize_style->value = $json_data;
    	   $customize_style->save();
    	   
    	   $folder = 'storage/uploads/css/';
    	   File::put($folder.$fileName, $data);
           
           return $this->response->message(trans('messages.success.created', ['Module' => trans('user::service.name')]))
                ->code(204)
                ->status('success')
                ->url(guard_url('theme'))
                ->redirect();
       }
       catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('service'))
                ->redirect();
        }
    }

}

