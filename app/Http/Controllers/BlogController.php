<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Response\PublicResponse;
use Litepie\Theme\ThemeAndViews;
use Litepie\User\Traits\RoutesAndGuards;
use App\Service;
use App\FrontSection;
use App\TeamMember;
use App\Submission;
use App\MySetting;
use App\Property;
use Litecms\Contact\Models\Contact;
use Session;
use App\Faq;
use App\Blog;
use Illuminate\Support\Facades\DB;
use Log;

class BlogController extends Controller
{
    use ThemeAndViews, RoutesAndGuards;

    /**
     * Initialize public controller.
     *
     * @return null
     */
    public function __construct()
    {
        $contact = Contact::first();
        Session::put('phone', $contact->phone);
        $this->response = app(PublicResponse::class);
        $this->setTheme('public');
    }

    /**
     * show homepage
     *
     * @return \Illuminate\Http\Response
     */


    /**
     * Show team member for each user.
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * Meet team member for each user.
     *
     * @return \Illuminate\Http\Response
     */
    public function about()
    {
        $page = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('about');
        $sections = $page->sections;
        $team_members = TeamMember::all();

        return $this->response
            ->setMetaKeyword(strip_tags($page->meta_keyword))
            ->setMetaDescription(strip_tags($page->meta_description))
            ->setMetaTitle(strip_tags($page->meta_title))
            ->layout('home')
            ->view('about')
            ->data(compact('page', 'sections', 'team_members'))
            ->output();
    }
    
    /**
     * Contact Page
     *
     * @return \Illuminate\Http\Response
     */
    public function contact()
    {
        $page = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('contact');
        $sections = $page->sections;
        $team_members = TeamMember::all();
        $contact = Contact::first();

        return $this->response
            ->setMetaKeyword(strip_tags($page->meta_keyword))
            ->setMetaDescription(strip_tags($page->meta_description))
            ->setMetaTitle(strip_tags($page->meta_title))
            ->layout('home')
            ->view('contact')
            ->data(compact('page', 'sections', 'team_members', 'contact'))
            ->output();
    }
    /**
     * show service.
     *
     * @return \Illuminate\Http\Response
     */
    public function services()
    {
        $page = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('services');

        $services = Service::all();
        $sections = $page->sections;

        return $this->response
            ->setMetaKeyword(strip_tags($page->meta_keyword))
            ->setMetaDescription(strip_tags($page->meta_description))
            ->setMetaTitle(strip_tags($page->meta_title))
            ->layout('home')
            ->view('services')
            ->data(compact('page','services', 'sections'))
            ->output();
    }
    /**
     * Properties
     *
     * @return \Illuminate\Http\Response
     */
    public function properties()
    {
        $page = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('properties');
        $properties = Property::all();

        return $this->response
            ->setMetaKeyword(strip_tags($page->meta_keyword))
            ->setMetaDescription(strip_tags($page->meta_description))
            ->setMetaTitle(strip_tags($page->meta_title))
            ->layout('home')
            ->view('properties')
            ->data(compact('page', 'properties'))
            ->output();
    }
    /**
     * Properties
     *
     * @return \Illuminate\Http\Response
     */
    public function single_property($id)
    {
        $page = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('properties');
        $property = Property::find($id);

        return $this->response
            ->setMetaKeyword(strip_tags($page->meta_keyword))
            ->setMetaDescription(strip_tags($page->meta_description))
            ->setMetaTitle(strip_tags($page->meta_title))
            ->layout('home')
            ->view('property')
            ->data(compact('page', 'property'))
            ->output();
    }

    /**
     * Filter properties
     * @param creteria
     * @return Response
     */

    public function getProperties(Request $request)
    {
        $query = Property::query();

        if ($request->category_lease) {
            $query = $query->where('category', 'lease');
        }
        if($request->category_sale)
        {
            $query = $query->where('category', 'sale');
        }
        if($request->category_land_sale)
        {
            $query = $query->where('category', 'land_sale');
        }
        if($request->type_office)
        {
            $query = $query->where('type', 'office');
        }
        if($request->type_industrial)
        {
            $query = $query->where('type', 'industrial');
        }
        if($request->type_retail)
        {
            $query = $query->where('type', 'retail');
        }
        if( $request->property_name != '' )
        {
            $query = $query->where('name', 'like', '%'.$request->property_name.'%');
        }

        if ($request->price) {
            $query = $query->where('price', '<=', ($request->price)[1])->where('price', '>', ($request->price)[0]);
        }

        if ($request->square_feet) {
            $query = $query->where('square', '<=', ($request->square_feet)[1])->where('square', '>', ($request->square_feet)[0]);
        }
        $properties = $query->get();
        return $properties;
    }
    /**
     * show individual service.
     *
     * @return \Illuminate\Http\Response
     */
    public function service_show($slug)
    {
        $page = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('blog');
        $services = Service::find($slug);
        return $this->response
            ->layout('home')
            ->view('service_brief')
            ->data(compact('page','services'))
            ->output();
    }
    /**
     * submit submission
    */
    public function submitSubmission(Request $request)
    {
        $submission = Submission::create();
        $submission->name = $request->full_name;
        $submission->email = $request->email;
        $submission->mail_address = $request->mail_address;
        $submission->phone = $request->phone;
        $submission->message = $request->message;
        $submission->refer_by = $request->refer_by;
        $submission->save();
        return redirect()->back()->with('message', 'IT WORKS!');
    }
    public function blog()
    {

        $page = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('blog');
        $sections = $page->sections;
        
        $blogpost = Service::paginate(5);
        
        
        $blogpost_archive = Service::all();
        $categroy_id = '';
        $current_archive_date='';
        //Get  Blog Category
        $menus = DB::table('menus')->where('key', 'category')->first();
        $blogcategory = DB::table('menus')->where('parent_id',$menus->id)->get();
        //Get  Blog Category
        return $this->response
        ->setMetaKeyword(strip_tags($page->meta_keyword))
        ->setMetaDescription(strip_tags($page->meta_description))
        ->setMetaTitle(strip_tags($page->meta_title))
        ->layout('home')
        ->view('blog')
        ->data(compact('page', 'sections','blogpost','blogcategory','categroy_id','blogpost_archive','current_archive_date'))
        ->output();
        
    }
    public function blog_category($slug)
    {
        $page = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('blog');
        $sections = $page->sections;
      //   $blogpost = Service::paginate(5);
        $blogpost    = Service::where('category_id',$slug)->paginate(5);
        $categroy_id = $slug;
        $menus = DB::table('menus')->where('key', 'category')->first();
        $blogcategory = DB::table('menus')->where('parent_id',$menus->id)->get();
        $blogpost_archive = Service::all();
        $current_archive_date = '';
        //Get  Blog Category
        return $this->response
        ->setMetaKeyword(strip_tags($page->meta_keyword))
        ->setMetaDescription(strip_tags($page->meta_description))
        ->setMetaTitle(strip_tags($page->meta_title))
        ->layout('home')
        ->view('blog')
        ->data(compact('page', 'sections','blogpost','blogcategory','categroy_id','blogpost_archive','current_archive_date'))
        ->output();
    }
    
    public function blog_archive($slug)
    {
        $page = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('blog');
        $sections = $page->sections;
        $date = date_create($slug);
        $year =  date_format($date,"Y"); 
        $month = date_format($date,"m"); 
        $blogpost    = Service::whereMonth('publish_date',$month)->whereYear('publish_date', '=', $year)->paginate(5);
        $categroy_id = '';
        $menus = DB::table('menus')->where('key', 'category')->first();
        $blogcategory = DB::table('menus')->where('parent_id',$menus->id)->get();
        $blogpost_archive = Service::all();
        
        $current_archive_date = $slug;
        //Get  Blog Category
        return $this->response
        ->setMetaKeyword(strip_tags($page->meta_keyword))
        ->setMetaDescription(strip_tags($page->meta_description))
        ->setMetaTitle(strip_tags($page->meta_title))
        ->layout('home')
        ->view('blog')
        ->data(compact('page', 'sections','blogpost','blogcategory','categroy_id','blogpost_archive','current_archive_date'))
        ->output();
    }
    
    public function blogss($slug)
    {
         $page = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('blog');
         $services = Service::find($slug);
         return $this->response
               ->layout('home')
               ->view('single_blog')
               ->data(compact('page','services'))
               ->output();
    }

    public function faq()
    {
        
        
       $page = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('faq');
       $sections = $page->sections;
       $faq_question = Faq::all();
       return $this->response
        ->setMetaKeyword(strip_tags($page->meta_keyword))
        ->setMetaDescription(strip_tags($page->meta_description))
        ->setMetaTitle(strip_tags($page->meta_title))
        ->layout('home')
        ->view('faq')
        ->data(compact('page', 'sections','faq_question'))
        ->output();
        
        
    }

    public function storecomment(Request $request)
    {
        
       
        $comment_time        = date("h:i a"); 
        $publish_date        = date("Y-m-d");
        $status              = "inactive";
        $blog_id             = $request->blog_id;
        $getblog             = Service::where('slug',$blog_id)->first(); 
        $blogtitle           = $getblog['name'];
        $notification        = 0; 

        DB::table('blog_comment')->insert(
            [
             'user_name'     =>$request->username,
             'user_email'    =>$request->useremail,
             'user_website'  =>$request->url,
             'message'       =>$request->message,
             'blog_id'       =>$request->blog_id,
             'publish_date'  =>$publish_date,
             'approvel_date' =>$publish_date,
             'status'        =>$status,
             'comment_time'  =>$comment_time,
             'blogTitle'     =>$blogtitle,
             'notification'  =>$notification
            ]
        );
        

        $img      =  url('public/themes/admin/assets/img/logo/logo-big-white.png');
        $subject  =  "A Greener Clean";
        $toEmail  =  "info@agreenerclean.net";
        $from     = 'website@agreenerclean.net';
        $fromName = 'A Greener Clean';
        $emailSubject = 'A Greener Clean';
        $htmlContent  = "<html>
        <head>
        <title>Please Follow the Instruction</title>
          <style type='text/css'>
          .content-fixed
          {
          width:600px;         
          }
          .wrap-image
          {
          text-align:center;
          background-color:#fff;
          padding:15px;
          }
          img
          {
          max-width:150px;
          max-height:150px;
          }
          .content{           
            padding:10px;
          }
          </style>
        </head>
        <body>
        <div class='content-fixed'>
            <div class='wrap-image'>
             <img src='$img'>
            </div>
            <div class='content'>
                <p>Blog Title: <strong>$blogtitle</strong></p>
                <p>Username: <strong>$request->username</strong></p>
                <p>Email:  <strong>$request->useremail</strong></p>         
                <p>Url:  <strong>$request->url</strong></p> 
                <p>Comment:  <strong>$request->message</strong></p>        
            </div>
        </div>
        </body>
        </html>";
        $headers = "From: $fromName"." <".$from.">";
        $semi_rand = md5(time()); 
        $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 
        $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\""; 
        $message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"UTF-8\"\n" .
        "Content-Transfer-Encoding: 7bit\n\n" . $htmlContent . "\n\n"; 
        $message .= "--{$mime_boundary}--";
        $returnpath = "-f" . $toEmail;
        $send =  mail($toEmail, $emailSubject, $message, $headers, $returnpath);
        if($send)
        { 
            Session::flash('messagess', "Thanks for your comment. it will publish ASAP.");
            return redirect()->back()->with('message', 'IT WORKS!');   
        }
        //return redirect()->back()->with('message', 'IT WORKS!');
    }
}
