<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>{{ Theme::getMetaTitle() }}</title>
    <meta name="description" content="{{ Theme::getMetaDesctiption() }}">
    <meta name="keyword" content="{{ Theme::getMetaKeyword() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="{{url('public/themes/public/assets/css/bootstrap.min.css')}}">
    <link type="text/css" rel="stylesheet" href="{{url('public/storage/uploads/css/customize.css')}}">
    <link type="text/css" rel="stylesheet" href="{{url('public/themes/public/assets/css/style.css')}}">
    <link type="text/css" rel="stylesheet" href="{{url('public/themes/public/assets/css/custom.css')}}">

    {!! Theme::asset()->scripts() !!}
    {!! Theme::asset()->styles() !!}

</head> 
<body>
<header>
  <div class="custom_header">
    <div class="container width-90-l">
      <div class="row">
          <div class="navbar">
              <div class="toggle-outer">
                 <div class="toggle">
                <div class="line1"></div>
                <div class="line2"></div>
                <div class="line3"></div>
            </div>
            </div>
            <div class="logo">
                <a href="{{ url('/') }}"><img src="{{url('public/themes/public/assets/images/logo.svg')}}"></a>
            </div>
              <div class="mobile-search">
            <li class="search"> 
                   <i class="fa fa-search" aria-hidden="true"></i>
                   <div class="search-bar">
                <form action="" method="" id="search-bar">
               <input type="text" name="search" class="header-search">
               <button type="submit" class="search-submit">
                 <span><i class="fa fa-search" aria-hidden="true"></i></span>
                 </button>
               </form>
            </div>
               </li>
               </div>
            <ul class="links">
                <?php
                  echo  Menu::menu('main');
                ?>
               <li class="search"> 
                   <i class="fa fa-search" aria-hidden="true"></i>
                   <div class="search-bar">
                <form action="" method="" id="search-bar">
               <input type="text" name="search" class="header-search">
               <button type="submit" class="search-submit">
                 <span><i class="fa fa-search" aria-hidden="true"></i></span>
                 </button>
               </form>
            </div>
               </li>
               <div class="mobile-social">
                   <a href=""><img src="{{url('public/themes/public/assets/images/fb.png')}}"></a>
                   <a href=""><img src="{{url('public/themes/public/assets/images/icon-social.png')}}"></a>
                   <a href=""><img src="{{url('public/themes/public/assets/images/instagram.png')}}"></a>
                   
               </div>
               <div class="collpase-bottom-logo-close">
                   <a href="nav-bottom-logo"><img src="{{url('public/themes/public/assets/images/mob-footer-icon.png')}}" alt="logo"></a>
                   <div class="close-btn">
                       <span class="close-text"> Close </span>
                       <span class="close-icon"><i class="fa fa-times" aria-hidden="true"></i></span>
                   </div>
               </div>
            </ul>
          
            
         
          </div>
        </div>
    </div>
    <div class="search-overlay-color"></div>
  </div>
    <div class="container width-90-l">
      <div class="custom_social">
          <div class="left">
            <a href="tel:7042533502">704.253.3502</a>
          </div>
          <div class="right">
            <ul>
                <li><a href=""><img src="{{url('public/themes/public/assets/images/fb.png')}}"></a></li>
                <li><a href=""><img src="{{url('public/themes/public/assets/images/icon-social.png')}}"></a></li>
                <li><a href=""><img src="{{url('public/themes/public/assets/images/instagram.png')}}"></a></li>
            </ul>
         </div>
      </div>
    </div>
</header>
    
<div class="container">
  <div class="row text-center">
    <div class="col-lg-6 offset-lg-3 col-sm-6 offset-sm-3 col-12 p-3 error-main">
      <div class="row">
        <div class="col-lg-8 col-12 col-sm-10 offset-lg-2 offset-sm-1">
          <h1 class="m-0">404</h1>
          <h6>Page not found</h6>

<!--                 <img src="public/themes/public/assets/images/best-deal/morning-brew-SQ5Lx-pCvDI-unsplash.jpg" alt="l-image">-->
        </div>
      </div>
    </div>
  </div>
</div>
    
<footer class="custom-footer">
	<div class="container width-90-l">
		<div class="row">
			<div class="col-xl-2 col-lg-2 col-md-3 col-sm-3 col-12">
				<h1>Company</h1>
				<ul>
					<li><a href="#">Home</a></li>
					<li><a href="#">About Us</a></li>
					<li><a href="#">Contact</a></li>
					<li><a href="#">FAQ's</a></li>
					<li><a href="#">Search Listings</a></li>
					<li><a href="#">View Online Auctions</a></li>
					<li><a href="#">Upcoming Auctions</a></li>
				</ul>
			</div>
			<div class="col-xl-2 col-lg-2 col-md-3 col-sm-3 col-12">
				<h1>Services</h1>
				<ul>
					<li><a href="">Listing Agency</a></li>
					<li><a href="{{url('about')}}">About Us</a></li>
					<li><a href="{{url('contact')}}">Contact</a></li>
					<li><a href="{{url('faqs')}}">FAQ's</a></li>
					<li><a href="">Search Listings</a></li>
					<li><a href="">View Online Auctions</a></li>
					<li><a href="">Upcoming Auctions</a></li>
					<li><a href="{{url('agent_trust')}}">Agents You Can Trust</a></li>
				</ul>
			</div>
			<div class="col-xl-4 col-lg-4 col-md-3 col-sm-3 col-12">
				<div class="buy-action">
					<ul>
						<li><a href="">Buying at Auction</a></li>
						<li><a href="">Selling at Auction</a></li>
					</ul>
				</div>
			</div>
			<div class="col-xl-2 col-lg-2 col-md-3 col-sm-3 col-12">
				<h1>More</h1>
				<ul>
					<li><a href="">Mortgage Calculator </a></li>
					<li><a href="">Testimonials </a></li>
					<li><a href="">Helpful Links</a></li>
				</ul>
			</div>
			<div class="col-xl-2 col-lg-2 col-md-3 col-sm-3 col-12">
				<div class="wrap-footer-icons">
					<div class="wrap-logo">
	  					<img src="{{url('public/themes/public/assets/images/redsuite-footerlogo.png')}}">
					</div>
					<ul>
						<li><a href=""><img src="{{url('public/themes/public/assets/images/fb.png')}}"></a></li>
						<li><a href=""><img src="{{url('public/themes/public/assets/images/icon-social.png')}}"></a></li>
						<li><a href=""><img src="{{url('public/themes/public/assets/images/instagram.png')}}"></a></li>
					</ul>
				</div>
			</div>
		</div>	
	</div>
</footer>
<footer class="last_footer">
		<div class="container width-90-l">
			<div class="wrap-content">
				<div class="left">
					<p>License Numbers: #123456</p>
					<p>©2020 Red Suit Realty & Auction. all rights reserved.</p>
					<a href="#">Designed and Developed by Owen Graffix</a>
				</div>
				<div class="right">
					<img src="{{url('public/themes/public/assets/images/copyrightbar-logo.png')}}">
				</div>
			</div>
	</div>
</footer>

</body>
</html>