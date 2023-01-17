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
use App\Footer;
use App\Facility;
use App\Category;
use app\Location;
use App\Auction;
use app\Simplyaccelerate;
use Litecms\Contact\Models\Contact;
use Session;
use App\Faq;
use App\Faqcategories;

use App\Blog;
use Illuminate\Support\Facades\DB;
use Log;
use App\Client;
use Illuminate\Support\Facades\Hash;

class PublicController extends Controller
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
        $footer = Footer::all();   
    }
    /**
     * show homepage
     *
     * @return \Illuminate\Http\Response
     */
    
    public function home()
    {
        

        $page  = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('home');
    
        if( strtolower($page_status)== strtolower("Draft"))
        {
            return redirect('404');
        }
        $sections     = $page->sections;
        $facility     = Facility::all();
        $testimonials = DB::table('testimonials')->where('status','1')->get();  
        $banner       = DB::table('notification')->where('slug',"banner-style")->get();
        $message_us   = DB::table('quick_forms')->where('slug',"message-us")->get();
        $services     = Service::where('status','Published')->get();
        
        return  $this->response
                ->setMetaKeyword(strip_tags($page->meta_keyword))
                ->setMetaDescription(strip_tags($page->meta_description))
                ->setMetaTitle(strip_tags($page->meta_title))
                ->layout('home')
                ->view('home')
                ->data(compact('page', 'sections','facility','testimonials','message_us','banner','services'))
                ->output();
                
    }
    
    public function testimonial()
    {
        
        $page_status       = $page->status;
        if( strtolower($page_status)== strtolower("Draft"))
        {
            return redirect('404');
        }
        $page     = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('testimonials');
        $sections = $page->sections;
        $testimonial = DB::table('testimonials')->where('status','1')->orderBy('order_by','asc')->get();  
        return  $this->response
                ->setMetaKeyword(strip_tags($page->meta_keyword))
                ->setMetaDescription(strip_tags($page->meta_description))
                ->setMetaTitle(strip_tags($page->meta_title))
                ->layout('home')
                ->view('testimonials')
                ->data(compact('page','sections','testimonial'))
                ->output();   
    }
    
    public function results()
    {
        $page = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('home');
        $sections = $page->sections;
        return  $this->response
                ->setMetaKeyword(strip_tags($page->meta_keyword))
                ->setMetaDescription(strip_tags($page->meta_description))
                ->setMetaTitle(strip_tags($page->meta_title))
                ->layout('home')
                ->view('results')
                ->data(compact('page'))
                ->output();   
          
          
          
    }
    
    public function blog()
    {

        $page     = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('Blog');
        $sections = $page->sections;
        return  $this->response
                ->setMetaKeyword(strip_tags($page->meta_keyword))
                ->setMetaDescription(strip_tags($page->meta_description))
                ->setMetaTitle(strip_tags($page->meta_title))
                ->layout('home')
                ->view('blog')
                ->data(compact('page','sections'))
                ->output();  
    } 
    
    public function notaryforms()
    {

        $page     = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('notary-form');
        $sections = $page->sections;
        
        return  $this->response
                ->setMetaKeyword(strip_tags($page->meta_keyword))
                ->setMetaDescription(strip_tags($page->meta_description))
                ->setMetaTitle(strip_tags($page->meta_title))
                ->layout('home')
                ->view('notaryform')
                ->data(compact('page','sections'))
                ->output();  
    }
    
    
    public function signupmarkets()
    {
        

        $page     = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('sign-up-for-market-reports');
        $sections = $page->sections;
        
        return  $this->response
                ->setMetaKeyword(strip_tags($page->meta_keyword))
                ->setMetaDescription(strip_tags($page->meta_description))
                ->setMetaTitle(strip_tags($page->meta_title))
                ->layout('home')
                ->view('signupmarkets')
                ->data(compact('page','sections'))
                ->output();   
          
          
          
    }
    public function comparative_markets_analysis()
    {
        

        $page     = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('comparative_markets_analysis');
        $sections = $page->sections;
        
        return  $this->response
                ->setMetaKeyword(strip_tags($page->meta_keyword))
                ->setMetaDescription(strip_tags($page->meta_description))
                ->setMetaTitle(strip_tags($page->meta_title))
                ->layout('home')
                ->view('comparative_markets_analysis')
                ->data(compact('page','sections'))
                ->output();      
    }
    public function home_bots()
    {
        $page     = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('home_bots');
        $sections = $page->sections;
        return  $this->response
                ->setMetaKeyword(strip_tags($page->meta_keyword))
                ->setMetaDescription(strip_tags($page->meta_description))
                ->setMetaTitle(strip_tags($page->meta_title))
                ->layout('home')
                ->view('homeevaluationpage')
                ->data(compact('page','sections'))
                ->output();       
    }

    
    public function details()
    {
        $page = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('home');
        $sections = $page->sections;
        return  $this->response
                ->setMetaKeyword(strip_tags($page->meta_keyword))
                ->setMetaDescription(strip_tags($page->meta_description))
                ->setMetaTitle(strip_tags($page->meta_title))
                ->layout('home')
                ->view('details')
                ->data(compact('page'))
                ->output();   
    }
    public function about()
    {
        $page = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('about');
        $page_status       = $page->status;
        if( strtolower($page_status)== strtolower("Draft"))
        {
            return redirect('404');
        }
        $message_us = DB::table('quick_forms')->where('slug',"message-us")->get();
        $sections = $page->sections;
        $services = Service::where('status','Published')->get();
        return $this->response
            ->setMetaKeyword(strip_tags($page->meta_keyword))
            ->setMetaDescription(strip_tags($page->meta_description))
            ->setMetaTitle(strip_tags($page->meta_title))
            ->layout('home')
            ->view('about')
            ->data(compact('page','sections','services','message_us'))
            ->output();
    }
    
    public function helpful_links()
    {
        $page              = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('helpful');
        $page_status       = $page->status;
        
        if( strtolower($page_status)== strtolower("Draft"))
        {
            return redirect('404');
        }
        
        $message_us = DB::table('quick_forms')->where('slug',"message-us")->get();
        $sections = $page->sections;
        $services = Service::where('status','Published')->get();
        return $this->response
            ->setMetaKeyword(strip_tags($page->meta_keyword))
            ->setMetaDescription(strip_tags($page->meta_description))
            ->setMetaTitle(strip_tags($page->meta_title))
            ->layout('home')
            ->view('helpful')
            ->data(compact('page','sections','services','message_us'))
            ->output();
    }
    public function auctions()
    {
        $page              = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('Auction');
        $page_status       = $page->status;
        
        if(strtolower($page_status) == strtolower("Draft"))
        {
            return redirect('404');
        }
        
        $message_us = DB::table('quick_forms')->where('slug',"message-us")->get();
        $sections   = $page->sections;
        $services   = Service::where('status','Published')->get();
        
        return $this->response
            ->setMetaKeyword(strip_tags($page->meta_keyword))
            ->setMetaDescription(strip_tags($page->meta_description))
            ->setMetaTitle(strip_tags($page->meta_title))
            ->layout('home')
            ->view('auction')
            ->data(compact('page','sections','services','message_us'))
            ->output();
    }
    public function logins()
    {
        $page = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('contact');
        $sections = $page->sections;
        if(!empty(user_id()))
        {
          return redirect('client/home');
        }
        return $this->response
            ->setMetaKeyword(strip_tags($page->meta_keyword))
            ->setMetaDescription(strip_tags($page->meta_description))
            ->setMetaTitle(strip_tags($page->meta_title))
            ->layout('home')
            ->view('login')
            ->data(compact('page','sections'))
            ->output();
    }
    
    public function registers()
    {

        if(!empty(user_id()))
        {
          return redirect('client/home');
        }
        return $this->response
            ->setMetaKeyword(strip_tags("Register"))
            ->setMetaDescription(strip_tags("Register"))
            ->setMetaTitle(strip_tags("Register"))
            ->layout('home')
            ->view('register')
            ->output();
    }
    
    public function registers_action(Request $request)
    {
        
         $username =  $request->name;
         $password =  $request->password;
         $useremail    =  $request->email;
         $verifyid =  strtolower(substr($request->name,0,2)).time();
        
            $data = [
                'name'      => $username,
                'email'     => $useremail,
                'password'  => $password,
                'api_token' => str_random(60),
                'status'    =>'Locked',
                'user_id'   =>4,
                'verify_id' =>$verifyid 
            ];

            $user  = Client::create($data);
            $values = array(
            'verify_id'=>$verifyid
            ); 

            DB::table('clients')
            ->where('email',$request->email)
            ->update($values);
        
            $subject                =  "RedSuit Reality";
            $from                   =  "admin@redsuitrealty.com";
            $fromName               =  "RedSuit Reality";
            $emailSubject           =  "RedSuit Reality";
            $email                  =  "developer.owengraffix@gmail.com";
            $img                    =  url('public/themes/admin/assets/img/logo/redsuit-logo.png');
            $url                    =  url('/verifyemail/').'/'.$verifyid;
                  //admin  
            $htmlContent = "<html>
            <head>
            <title>Please Follow the Instruction</title>
              <style type='text/css'>
              .content-fixed
              {
                width:600px;         
              }
              .wrap-image
              {
                  background:#344E5C;
                  text-align:center;
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
              .link_verify
              {
                background:#EA0029;
                padding:10px 15px;
                border-radius:50px;
                color:#fff!important;
                font-size:14px;
                font-weight:bold;
                margin-top:30px;
              }
              </style>
            </head>
            <body>
            <div class='content-fixed'>
                 <div class='wrap-image'>
                   <img src='$img'>
                 </div>
                 <div class='content'>
                    <p>Username: <strong>$username</strong></p>
                    <p style='font-size:13px;'>Note: Please verify to login.</p>
                    <p><a href='$url' class='link_verify'>Verify email address</a></p>
                </div>
                <div class='footer'>
                    <p>From,</p>
                    <p>RedSuit Teams</p>
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
            $returnpath = "-f" . $email;
            $send =  mail($useremail, $emailSubject, $message, $headers, $returnpath);       
            return redirect('login')->with("msg",""); 
        
    }
    
    public function forgot_password()
    {
        $page = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('contact');
        $sections = $page->sections;
        if(!empty(user_id()))
        {
          return redirect('client/home');
        }
        return $this->response
            ->setMetaKeyword(strip_tags($page->meta_keyword))
            ->setMetaDescription(strip_tags($page->meta_description))
            ->setMetaTitle(strip_tags($page->meta_title))
            ->layout('home')
            ->view('forgot')
            ->data(compact('page','sections'))
            ->output();
    }
    public function forgotemails()
    {
        $email = $_POST['email'];
        $data  = DB::table('clients')->where('email',$email)->first();
        
 
        $values = array(
            'session_date'=>date('Y-m-d H:i:s'),
        );
        if(count($data))
        {
            
            $verify_id =  $data->verify_id;
            $uname     =  $data->name;
            $useremail = $email;
            DB::table('clients')
            ->where('email',$email)
            ->update($values);
            
          
            
            
            
            $subject                =  "RedSuit Reality - Update Password";
            $from                   =  "admin@redsuitrealty.com";
            $fromName               =  "RedSuit Reality";
            $emailSubject           =  "RedSuit Reality";
            $email                  =  "developer.owengraffix@gmail.com";
            $img                    =  url('public/themes/admin/assets/img/logo/redsuit-logo.png');
            $url                    =  url('/update_password/').'/'.$verify_id.'?id='.uniqid();
                  //admin  
            $htmlContent = "<html>
            <head>
            <title>Please Follow the Instruction</title>
              <style type='text/css'>
              body p
              {
                color:#000;
              }
              .content-fixed
              {
                width:600px;         
              }
              .wrap-image
              {
                  background:#344E5C;
                  text-align:center;
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
              .link_verify
              {
                margin-top:30px;
                color:#000!important;
                font-size:15px;
                font-weight:bold;
              }
              </style>
            </head>
            <body>
            <div class='content-fixed'>
                 <div class='wrap-image'>
                   <img src='$img'>
                 </div>
                 <div class='content'>
                    <p>Username:<strong>$uname</strong></p>
                
                    <p>Note:Please click to below link to update password.</p>
                    <p><a href='$url' class='link_verify'>Update Password</a></p>
                    
                    <p>Redsuit Team.</p>
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
            $returnpath = "-f" . $email;
            $send =  mail($useremail, $emailSubject, $message, $headers, $returnpath);   
            echo "true";
        }
        else
        {
            echo "false";
        }

       
    }
    public function updatepasswords($slugid="")
    { 
        
        $page = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('contact');
        $sections = $page->sections;
        if(!empty(user_id()))
        {
          return redirect('client/home');
        }
        return $this->response
            ->setMetaKeyword(strip_tags($page->meta_keyword))
            ->setMetaDescription(strip_tags($page->meta_description))
            ->setMetaTitle(strip_tags($page->meta_title))
            ->layout('home')
            ->view('updatepassword')
            ->data(compact('page','sections','slugid'))
            ->output();
    }
    public function customer_updatepaswords()
    {
      
        
         $password  = $_POST['password'];
         $verify_id = $_POST['verifyid'];
         $data  = DB::table('clients')->where('verify_id',$verify_id)->first();
        
         $session_date =  strtotime($data->session_date);
    
         $current_date =  strtotime(date('Y-m-d H:i:s'));


         $values = array(
            'password'=>Hash::make($password),
         );
        
         $update =     DB::table('clients')
         ->where('verify_id',$verify_id)
         ->update($values);
         if($update)
         {
            echo "true";
         }
         else
         {
           echo "false"; 
         }  
    }
    /**
     * Contact Page
     *
     * @return \Illuminate\Http\Response
     */
    public function contact()
    {
        $page = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('contact');
        $page_status       = $page->status;
        if( strtolower($page_status)== strtolower("Draft"))
        {
            return redirect('404');
        }
        $sections = $page->sections;
        $contact_form = DB::table('quick_forms')->where('slug',"contact-us")->get();
        return $this->response
            ->setMetaKeyword(strip_tags($page->meta_keyword))
            ->setMetaDescription(strip_tags($page->meta_description))
            ->setMetaTitle(strip_tags($page->meta_title))
            ->layout('home')
            ->view('contact')
            ->data(compact('page','sections','contact_form'))
            ->output();
    }
    /**
     * show service.
     *
     * @return \Illuminate\Http\Response
     */
    public function services()
    {
        $page              = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('services');
        $page_status       = $page->status;
        if( strtolower($page_status) == strtolower("Draft"))
        {
            return redirect('404');
        }
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
    
    public function submitSubmission(Request $request)
    {
        
        $submission               = Submission::create();
        $submission->name         = $request->full_name;
        $submission->email        = $request->email;
        $submission->mail_address = $request->mail_address;
        $submission->phone        = $request->phone;
        $submission->message      = $request->message;
        $submission->refer_by     = $request->refer_by;
        $submission->save();
        return redirect()->back()->with('message', 'IT WORKS!');
        
    }

    public function selling()
    {
        
        $page = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('selling');
        $page_status       = $page->status;
        
        if( strtolower($page_status)== strtolower("Draft"))
        {
            return redirect('404');
        }
        $page_home = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('home');
        $sectionss = $page_home->sections;
        $sections = $page->sections;
        $locations = DB::table('location')->where('status','Published')->orderBy('order_by','asc')->get();  
        $facilitys =  DB::table('auctions')->where('categories','tailored-to-you')->get();
        $reddiff   =  DB::table('members')->orderBy('order_by','asc')->get();
        return $this->response
            ->setMetaKeyword(strip_tags($page->meta_keyword))
            ->setMetaDescription(strip_tags($page->meta_description))
            ->setMetaTitle(strip_tags($page->meta_title))
            ->layout('home')
            ->view('selling')
            ->data(compact('page', 'sections','locations','facilitys','reddiff','sectionss'))
            ->output();
         
         
    }
    public function selling_serivice($slugs)
    {
        $page      = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('selling');
        $sections  = $page->sections;
        $facilitys = DB::table('auctions')->where('slug',$slugs)->where('status','publish')->get();
        
        
        $page_status       = $page->status;
        if( strtolower($page_status)== strtolower("Draft"))
        {
            return redirect('404');
        }

        
        return $this->response
            ->setMetaKeyword(strip_tags($page->meta_keyword))
            ->setMetaDescription(strip_tags($page->meta_description))
            ->setMetaTitle(strip_tags($page->meta_title))
            ->layout('home')
            ->view('selling_services')
            ->data(compact('page', 'sections','facilitys'))
            ->output();
    }
    public function buying()
    {
        $page = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('buying');
        $page_status       = $page->status;
        if( strtolower($page_status)== strtolower("Draft"))
        {
            return redirect('404');
        }
        $sections     = $page->sections;
        $testimonials = DB::table('testimonials')->get();  
        $banner       = DB::table('notification')->where('slug',"banner-style")->get();
        $auction      = DB::table('auctions')->where('categories','fsbo')->where('status','publish')->get();  
        $message_us   = DB::table('quick_forms')->where('slug',"message-us")->get();
        $page_home    = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('home');
        $sectionss    = $page_home->sections;
 
        $slugs_name = str_replace( array('[',']') , ''  ,  $sections[4]->body );

        $buying_property    = Auction::where('slug',$slugs_name)->where('status','publish')->get();
        
        
        return $this->response
            ->setMetaKeyword(strip_tags($page->meta_keyword))
            ->setMetaDescription(strip_tags($page->meta_description))
            ->setMetaTitle(strip_tags($page->meta_title))
            ->layout('home')
            ->view('buying')
            ->data(compact('page', 'sections','message_us', 'auction','sectionss','buying_property'))
            ->output();
        
        
    }
    public function thankyou()
    {
      
        $page = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('thank-you');
        $sections = $page->sections;
        return $this->response
            ->setMetaKeyword(strip_tags($page->meta_keyword))
            ->setMetaDescription(strip_tags($page->meta_description))
            ->setMetaTitle(strip_tags($page->meta_title))
            ->layout('home')
            ->view('thankyou')
            ->data(compact('page', 'sections'))
            ->output();
    }
    public function ordersubmit()
    {
      
        $page     = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('order-success');
        $sections = $page->sections;
        return $this->response
            ->setMetaKeyword(strip_tags($page->meta_keyword))
            ->setMetaDescription(strip_tags($page->meta_description))
            ->setMetaTitle(strip_tags($page->meta_title))
            ->layout('home')
            ->view('ordersubmit')
            ->data(compact('page', 'sections'))
            ->output();
    }
    public function faq()
    {
            $page     = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('faq');
            $page_status       = $page->status;
            if( strtolower($page_status)== strtolower("Draft"))
            {
                return redirect('404');
            }

           $sections = $page->sections;
           $faqs = Faq::all();
        
            $faqs_selling_cnt = Faq::where('faqs','selling')->get()->count();
            $faqs_buying_cnt  = Faq::where('faqs','buying')->get()->count();
            $faqs_general_questions_cnt  = Faq::where('faqs','general_questions')->get()->count();
        
            $faq_categories              = Faqcategories::where('status','publish')->orderBy('order_by','asc')->get();
            $faqs_auctions_cnt           = Faq::where('faqs','auctions')->get()->count();
        
            foreach($faq_categories as $fkeys=>$fvalues)
            {
                 $faqs_by_cat[] = Faq::select('question','answer')->where('faqs',$fvalues->slug)->orderBy('order_by','asc')->get(); 
                 $faqs_title[]  = $fvalues->title;
            }
        
           return $this->response
            ->setMetaKeyword(strip_tags($page->meta_keyword))
            ->setMetaDescription(strip_tags($page->meta_description))
            ->setMetaTitle(strip_tags($page->meta_title))
            ->layout('home')
            ->view('faq')
            ->data(compact('page', 'sections','faqs','faqs_selling','faqs_buying','faqs_selling_cnt','faqs_buying_cnt','faqs_general_questions_cnt','faqs_auctions_cnt','faqs_by_cat','faqs_title'))
            ->output();
     
    }
    public function rebate()
    {
         return $this->response
            ->layout('home')
            ->view('rebate')
            ->output();  
    }
    public function bestdeal()
    {
        
        $page                = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('get-the-best-deals-a-rebate');
        $page_status         = $page->status;
        
        if( strtolower($page_status) == strtolower("Draft"))
        {
            return redirect('404');
        }
        
        $sections            = $page->sections;
        $page_buying         = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('making-buying-fast-and-easy');
        $sections_buying     = $page_buying->sections;
        
        $page_agents         = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('agents-you-can-trust');
        $sections_agents     = $page_agents->sections; 
        
        $testimonials_db_name = str_replace( array('[',']') , ''  ,  $sections[4]->body );
        
        if($testimonials_db_name)
        {
            $testimonials = DB::table($testimonials_db_name)->get();  
        }
        $message_us          = DB::table('quick_forms')->where('slug',"message-us")->get();
        
        $slugs_name          = str_replace( array('[',']') , ''  ,  $sections[3]->body );

        $buying_property     = Auction::where('slug',$slugs_name)->where('status','publish')->get();
        
        
        return  $this->response
                ->setMetaKeyword(strip_tags($page->meta_keyword))
                ->setMetaDescription(strip_tags($page->meta_description))
                ->setMetaTitle(strip_tags($page->meta_title))
                ->layout('home')
                ->view('bestdeal')
                ->data(compact('page', 'sections','message_us','testimonials','sections_buying','sections_agents','sections_agents','buying_property'))
                ->output();
        
        

    }
    public function buyingfast()
    {
        

        
        $page           = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('making-buying-fast-and-easy');
        $page_status    = $page->status;
        if( strtolower($page_status)== strtolower("Draft"))
        {
            return redirect('404');
        } 
        
        $sections     = $page->sections;
        
        $page_bestdeal         = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('get-the-best-deals-a-rebate');
        $sections_bestdeal     = $page_bestdeal->sections;
        
        $page_agents         = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('agents-you-can-trust');
        $sections_agents     = $page_agents->sections; 
        
        
        $db_name = str_replace( array('[',']') , ''  ,  $sections[4]->body );
        
        if($db_name)
        {
            $testimonials = DB::table($db_name)->get();  
        }
        
        
        
        $message_us   = DB::table('quick_forms')->where('slug',"message-us")->get();
        
        
         $slugs_name          = str_replace( array('[',']') , ''  ,  $sections[1]->body );

         $buying_property     = Auction::where('slug',$slugs_name)->where('status','publish')->get();

        
         return  $this->response
                 ->setMetaKeyword(strip_tags($page->meta_keyword))
                 ->setMetaDescription(strip_tags($page->meta_description))
                 ->setMetaTitle(strip_tags($page->meta_title))
                 ->layout('home')
                 ->view('buyingfast')
                 ->data(compact('page', 'sections','message_us','testimonials','sections_agents','sections_bestdeal','buying_property'))
                 ->output(); 
    }
    public function agent_trust()
    {
        

        
        $page              = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('agents-you-can-trust');
        $page_status       = $page->status;
        if( strtolower($page_status)== strtolower("Draft"))
        {
            return redirect('404');
        } 
        
        
        $sections            = $page->sections;
        $page_buying         = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('making-buying-fast-and-easy');
        $sections_buying     = $page_buying->sections;
        $page_bestdeal         = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('get-the-best-deals-a-rebate');
        $sections_bestdeal     = $page_bestdeal->sections;
        
        $testimonials_db_name = str_replace( array('[',']') , ''  ,  $sections[3]->body );
        
        if($testimonials_db_name)
        {
            $testimonials = DB::table($testimonials_db_name)->get();  
        }
        
        
        $message_us   = DB::table('quick_forms')->where('slug',"message-us")->get();
        
        return  $this->response
                ->setMetaKeyword(strip_tags($page->meta_keyword))
                ->setMetaDescription(strip_tags($page->meta_description))
                ->setMetaTitle(strip_tags($page->meta_title))
                ->layout('home')
                ->view('agents')
                ->data(compact('page', 'sections','message_us','testimonials','sections_buying','sections_bestdeal'))
                ->output(); 
    }
    public function mortage_calculator()
    {

        $page          = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('mortgage-calculator');
        $page_status       = $page->status;
        if( strtolower($page_status)== strtolower("Draft"))
        {
            return redirect('404');
        }
        $sections      = $page->sections;
        $testimonials  = DB::table('testimonials')->get();  
        $requestform   = DB::table('quick_forms')->where('slug',"request-form")->get();
        $message_us    = DB::table('quick_forms')->where('slug',"message-us")->get();
        return  $this->response
                ->setMetaKeyword(strip_tags($page->meta_keyword))
                ->setMetaDescription(strip_tags($page->meta_description))
                ->setMetaTitle(strip_tags($page->meta_title))
                ->layout('home')
                ->view('mortage_calculator')
                ->data(compact('page', 'sections','requestform','testimonials','message_us'))
                ->output();
        

    }
    public function listing()
    {
        
        $page                = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('listing');
        $page_status       = $page->status;
        if( strtolower($page_status)== strtolower("Draft"))
        {
            return redirect('404');
        }
        $sections            = $page->sections;
      
        
        
        return  $this->response
                ->setMetaKeyword(strip_tags($page->meta_keyword))
                ->setMetaDescription(strip_tags($page->meta_description))
                ->setMetaTitle(strip_tags($page->meta_title))
                ->layout('home')
                ->view('listing')
                ->data(compact('page', 'sections'))
                ->output();
    } 
    public function buying_at_auctions()
    {
        
        $page                = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('buying-at-auction');
        $page_status       = $page->status;
        if( strtolower($page_status)== strtolower("Draft"))
        {
            return redirect('404');
        }
        $sections            = $page->sections;
      
        
        
        return  $this->response
                ->setMetaKeyword(strip_tags($page->meta_keyword))
                ->setMetaDescription(strip_tags($page->meta_description))
                ->setMetaTitle(strip_tags($page->meta_title))
                ->layout('home')
                ->view('buying_at_auction')
                ->data(compact('page', 'sections'))
                ->output();
    }
    public function selling_at_auctions()
    {
        
        $page                = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('selling-at-auction');
        $page_status       = $page->status;
        if( strtolower($page_status)== strtolower("Draft"))
        {
            return redirect('404');
        }
        $sections            = $page->sections;
      
        
        
        return  $this->response
                ->setMetaKeyword(strip_tags($page->meta_keyword))
                ->setMetaDescription(strip_tags($page->meta_description))
                ->setMetaTitle(strip_tags($page->meta_title))
                ->layout('home')
                ->view('selling_at_auction')
                ->data(compact('page', 'sections'))
                ->output();
    }
    
    public function tests()
    {
         return $this->response
         ->layout('home')
         ->view('test')
         ->output();
    }
    
    public function single_service($slug)
    {
        

        $meta_title       ="";
        $meta_keyword     ="";
        $meta_description ="";
        
        
        $services     = DB::table('services')->where('slug',$slug)->first();
        $testimonials = DB::table('testimonials')->get();  
        $page         = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('services');
        $sections     = $page->sections;
        $servicesall  = Service::where('status','Published')->orderBy('order_by','asc')->get();
        
       
        $meta_title       =  $services->meta_title;
        $meta_keyword     =  $services->meta_keyword;
        $meta_description =  $services->meta_description;
        $status           =  $services->status;

        if($status == "Draft" )
        {
            return redirect('404');
        }
        if($services->product_categories)
        {
            $buying_property    = Auction::where('slug',$services->product_categories)->where('status','publish')->get();
        }
        
        
        return $this->response
        ->setMetaKeyword(strip_tags($meta_keyword))
        ->setMetaDescription(strip_tags($meta_description))
        ->setMetaTitle(strip_tags($meta_title))
        ->layout('home')
        ->view('single-services')
        ->data(compact('page', 'sections','services','testimonials','servicesall','buying_property'))
        ->output();

    }
    public function facility($slug)
    {
        $facility     =  DB::table('facility')->where('slug', $slug)->first();
        $facility_all =  DB::table('facility')->get();
        return $this->response
        ->setMetaKeyword(strip_tags($page->meta_keyword))
        ->setMetaDescription(strip_tags($page->meta_description))
        ->setMetaTitle(strip_tags($page->meta_title))
        ->layout('home')
        ->view('facility')
        ->data(compact('facility','facility_all'))
        ->output();
    }

    public function simply_accelerating_marketing(Request $request){
        $page = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('simply-accelerating-marketing');
        if( strtolower($page_status)== strtolower("Draft"))
        {
            return redirect('404');
        }
        $sections = $page->sections;
        $simply_accelerated = DB::table('auctions')->where('categories','simply_accelerates')->where('status','publish')->orderBy('order_by','asc')->get();
        $simply_accelerated_package = DB::table('auctions')->where('categories','allincluded')->where('status','publish')->get();
        return $this->response
        ->setMetaKeyword(strip_tags($page->meta_keyword))
        ->setMetaDescription(strip_tags($page->meta_description))
        ->setMetaTitle(strip_tags($page->meta_title))
        ->layout('home')
        ->view('simply-accelerating-marketing')
        ->data(compact('page','sections','simply_accelerated','simply_accelerated_package'))
        ->output();
    }
    public function services_designed(Request $request){
        $page               = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('service-designed-to-meet-your-needs');
        if( strtolower($page_status)== strtolower("Draft"))
        {
            return redirect('404');
        }  
        $sections           = $page->sections; 
        $servicesdesigneds  = DB::table('servicesdesigneds')->orderBy('order_by','asc')->get();
        return $this->response
        ->setMetaKeyword(strip_tags($page->meta_keyword))
        ->setMetaDescription(strip_tags($page->meta_description))
        ->setMetaTitle(strip_tags($page->meta_title))
        ->layout('home')
        ->view('services-designed')
        ->data(compact('page','sections','servicesdesigneds'))
        ->output();

    }
    
    public function plans(Request $request){
        
        
        $page               = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('plans');
        $sections           = $page->sections;  
        $auction            = DB::table('auctions')->where('categories','fsbo')->where('status','publish')->get();  
        
        
        return $this->response
        ->setMetaKeyword(strip_tags($page->meta_keyword))
        ->setMetaDescription(strip_tags($page->meta_description))
        ->setMetaTitle(strip_tags($page->meta_title))
        ->layout('home')
        ->view('plans')
        ->data(compact('page','sections','auction'))
        ->output();
    
    }    
    public function shops(Request $request){
        
        $page                    = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('shop');
        $sections                = $page->sections;
        $category                = Category::where('status','publish')->orderBy('order_by','asc')->get();
        
        $simply_acc              = Auction::where('categories','simply_accelerates')->where('status','publish')->get();
        
        
        foreach($category as $key=>$val)
        {
            $category_title[] = $val->name;
            $category_slug[] = $val->slug;
            $auction[]  = DB::table('auctions')->select('name','price','slug','id')->where('slug',$val->slug)->where('status','publish')->get(); 
        }
        $servicesall  = Service::where('status','Published')->get();
        return $this->response
        ->setMetaKeyword(strip_tags($page->meta_keyword))
        ->setMetaDescription(strip_tags($page->meta_description))
        ->setMetaTitle(strip_tags($page->meta_title))
        ->layout('home')
        ->view('shop')
        ->data(compact('page','sections','auction','category_title','simply_acc','servicesall','category_slug'))
        ->output();
        
        
    
    }
    

    public function contactform(Request $request)
    {
        $contact_form = DB::table('quick_forms')->where('slug',"contact-us")->get();
        if(count($contact_form) > 0)
        {
            $subject                =  $contact_form[0]->subject;
            $from                   =  $contact_form[0]->sender;
            $fromName               =  $contact_form[0]->subject;
            $emailSubject           =  $contact_form[0]->subject;
            $email                  =  $contact_form[0]->recipient;
            $mail_sent_ok           =  $contact_form[0]->mail_sent_ok;
            $toEmail                =  $contact_form[0]->recipient;
            $img                    =  url('public/themes/admin/assets/img/logo/redsuit-logo.png');
            $name            =  $request->username;
            $useremail       =  $request->useremail;
            $phone           =  $request->userphone;
            $city            =  $request->usercity;
            $zipcode         =  $request->userzipcode;
            $usermessage     =  $request->usermessage;
            $phoneval = $phone;
            if(strlen($phoneval) === 16) {
            
            }
            else
            {
                Session::flash('phoneerror',"Phone no is invalid try again!!");
                return redirect()->back()->with('message', 'IT WORKS!');   
            }
            if(strlen($name) <= 20) {
            
            }
            else
            {
                Session::flash('phoneerror',"username no is invalid try again!!");
                return redirect()->back()->with('message', 'IT WORKS!');   
            }
            
            
            $submission = Submission::create();
            $submission->name = $request->username;
            $submission->email  = $request->useremail;
            $submission->phone = $request->userphone;
            $submission->city  = $request->usercity;
            $submission->zipcode  = $request->userzipcode;
            $submission->message  = $request->usermessage;
            
            date_default_timezone_set('Europe/London');
            $submission->mail_time =  date("M-d-Y h:i:s A") . "\n"; 
            $submission->slug = "contact-us";
            $submission->status   = "active";
            $submission->save();
      
   
      //admin  
      $htmlContent = "<html>
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
              background-color:#344E5C;
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
                <p>Username: <strong>$name</strong></p>
                <p>Email:    <strong>$useremail</strong></p>
                <p>Phone:    <strong>$phone</strong></p>
                <p>City:     <strong>$city</strong></p>
                <p>Zipcode:  <strong>$zipcode</strong></p>
                <p>Message:  <strong>$usermessage</strong></p>
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
        $returnpath = "-f" . $email;
        $send =  mail($toEmail, $emailSubject, $message, $headers, $returnpath);

            unset($message);
            $htmlContentuser = '<html>
            <head>
            <title>Please Follow the Instruction</title>
            <style type="text/css">';
            $htmlContentuser.= $contact_form[0]->template_css;  
            $htmlContentuser.='</style></head>';
            $htmlContentuser.= $contact_form[0]->header_content;  
            $htmlContentuser.= $name;
            $htmlContentuser.= $contact_form[0]->thanku_template;
            $htmlContentuser.= '</html>';
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=utf-8" . "\r\n";
            // More headers
            $headers .= 'From: '.$emailSubject.' <'.$from.'>' . "\r\n";
            $headers .= 'Cc:website@redsuitrealty.com' . "\r\n";
            $usersend = mail($useremail,$emailSubject,$htmlContentuser,$headers);  
            if($usersend)
            { 
                Session::flash('messagess', $mail_sent_ok);
                
                return redirect('thankyou')->with('message', 'IT WORKS!');   
            } 
            
            
         }
           
    }
    

    public function message_us(Request $request)
    {

        $contact_form = DB::table('quick_forms')->where('slug',"message-us")->get();
        

        if(count($contact_form) > 0)
        {
            $subject                =  $contact_form[0]->subject;
            $from                   =  $contact_form[0]->sender;
            $fromName               =  $contact_form[0]->subject;
            $emailSubject           =  $contact_form[0]->subject;
            $email                  =  $contact_form[0]->recipient;
            $mail_sent_ok           =  $contact_form[0]->mail_sent_ok;
            $toEmail                  =  $contact_form[0]->recipient;
            $replaymsg                =  $contact_form[0]->message_body;
            
             $img                    =  url('public/themes/admin/assets/img/logo/redsuit-logo-red.png');
            $name            =  $request->username;
            $useremail       =  $request->email;
            $phone           =  $request->phone;
            $usermessage     =  $request->message;
            
           
            $phoneval = $phone;
            if(strlen($phoneval) === 16) {
            
            }
            else
            {
                Session::flash('phoneerror',"Phone no is invalid try again!!");
                return redirect()->back()->with('message', 'IT WORKS!');   
            }

          
            
            
        $submission = Submission::create();
        $submission->name = $request->username;
        $submission->email = $request->email;
        $submission->phone = $request->phone;
        date_default_timezone_set('Europe/London');
        $submission->mail_time =  date("M-d-Y h:i:s A") . "\n";       
            
            
        $submission->message = $request->message;
        $submission->slug = "message-us";
        $submission->status   = "active";
        $submission->save();

      //admin  
        $htmlContent = "<html>
        <head>
        <title>Please Follow the Instruction</title>
          <style type='text/css'>
          body
          {
            font-family: 'Roboto', sans-serif;
          }
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
                <p>Username:  <strong>$name</strong></p>
                <p>Email:     <strong>$useremail</strong></p>
                <p>Phone:     <strong>$phone</strong></p>
                <p>Message:   <strong>$usermessage</strong></p>  
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
        $returnpath = "-f" . $email;
        $send =  mail($toEmail, $emailSubject, $message, $headers, $returnpath);
        //User Get Booking Msg
        
        unset($message);
 
        $htmlContentuser = '<html>
        <head>
        <title>Please Follow the Instruction</title>
          <style type="text/css">';
            
        $htmlContentuser.= $contact_form[0]->template_css;  
            
        $htmlContentuser.='</style></head>';
            
        $htmlContentuser.= $contact_form[0]->header_content;  
        $htmlContentuser.= $name;
        $htmlContentuser.= $contact_form[0]->thanku_template;
        $htmlContentuser.= '</html>';

           // die();
            $headers = "From: $fromName"." <".$from.">";
            $semi_rand = md5(time()); 
            $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 
            $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\""; 
            $message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"UTF-8\"\n" .
            "Content-Transfer-Encoding: 7bit\n\n" . $htmlContentuser . "\n\n"; 
            $message .= "--{$mime_boundary}--";
            $returnpath = "-f" . $from;
            $usersend =  mail($useremail, $emailSubject, $message, $headers, $returnpath);
            if($usersend)
            { 
                Session::flash('messagess',$mail_sent_ok);
                return redirect('thankyou')->with('message', 'IT WORKS!');   
            }
            
        }
           
    }
    public function verifyemail($verifyid)
    { 
        $values = array(
            'status'=>"Active",
            'email_verified_at'=>date('Y-m-d'),
        ); 

        $update =  DB::table('clients')
        ->where('verify_id',$verifyid)
        ->update($values);
        
        $url = url('login');
        
        if($update)
        {
            return redirect('login')->with('msg','Please login email is verified');
        }   
        
    }
    public function notfound()
    {
       
        $page              = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('404');
        $sections          = $page->sections;  
        return $this->response
               ->setMetaKeyword(strip_tags($page->meta_keyword))
               ->setMetaDescription(strip_tags($page->meta_description))
               ->setMetaTitle(strip_tags($page->meta_title))
               ->layout('home')
               ->view('404')
               ->data(compact('page','sections'))
               ->output();
    }
    public function checkemail()
    {
        $email_id = $_POST['email_id'];

        $data = DB::table('clients')->where('email',$email_id)->first();
        

        if($data)
        {
            echo true;
        }
        else
        {
            echo false;
        }
            
    }

}


