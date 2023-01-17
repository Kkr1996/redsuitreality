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
use App\Library;
use Illuminate\Support\Facades\DB;
use Litepie\Settings\Models\Setting;
use Log;
use Session;
class ImageCropperController extends Controller
{

    public function index()
    {
        return view('cropper');
    }

    public function upload(Request $request)
    {
       // $folderPath = public_path('upload/');

   
        $folderPath = public_path('storage/images/library/');
        $folder  = 'storage/images/library/';
        $image_parts = explode(";base64,", $request->image);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);
        $filename  =uniqid() . '.png';
        $file = $folderPath .$filename;
        $created_at = date('Y-m-d H:i:s');
        $ourgallerypath = $folder.$filename;

        $values = array('filename' =>  $ourgallerypath,'updated_date' => $created_at,'created_date'=>$created_at);
        DB::table('library')->insert($values);
        file_put_contents($file, $image_base64);

        return response()->json(['success'=>'success']);
    }
}