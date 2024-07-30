@if(Auth::check() && Auth::user()->isRM)
  @section('title', __('Berkas'))
  @php $isRM = true; $isHR = false; @endphp
@elseif(Auth::check() && Auth::user()->isHR)
  @section('title', __('Berkas'))
  @php $isRM = false;$isHR = true; @endphp
@else
  @section('title', __('e-Book'))
  @php $isRM = false;$isHR = false; @endphp
@endif
<!DOCTYPE html>
<html lang="en">

<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta charset="utf-8" />
	<title>Masaksih | {{ config('app.name', 'Demo') }} - @yield('title')</title>
	<meta name="description" content="overview &amp; stats" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
	<meta name="csrf-token" content="{{ csrf_token() }}" />
	<link rel="stylesheet" href="{{ url('/assets/css/bootstrap.min.css') }}" />
	<link rel="stylesheet" href="{{ url('/assets/font-awesome/4.2.0/css/font-awesome.min.css') }}" />
	<link rel="stylesheet" href="{{ url('/assets/fonts/fonts.googleapis.com.css') }}" />
	<link rel="stylesheet" href="{{ url('/assets/css/ace.min.css') }}" class="ace-main-stylesheet" id="main-ace-style" />
	<link rel="stylesheet" href="{{ url('/plugins/toastr/toastr.min.css') }}">
	<link rel="stylesheet" href="{{ url('/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}" />
	<link rel="stylesheet" href="{{ url('/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}" />
	<link rel="stylesheet" href="{{ url('/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}" />
	@hasSection('custom-css')
	@yield('custom-css')
	@endif
	<script src="{{ url('/assets/js/ace-extra.min.js') }}"></script>
</head>

<body class="no-skin">
	<div id="navbar" class="navbar navbar-default navbar-fixed-top">
		<script type="text/javascript">
			try {
				ace.settings.check('navbar', 'fixed')
			} catch (e) {}
		</script>

		<div class="navbar-container" id="navbar-container">
			<button type="button" class="navbar-toggle menu-toggler pull-left" id="menu-toggler" data-target="#sidebar">
				<span class="sr-only">Toggle sidebar</span>

				<span class="icon-bar"></span>

				<span class="icon-bar"></span>

				<span class="icon-bar"></span>
			</button>

			<!--<div class="navbar-header pull-left">
				<a href="/" class="navbar-brand">
					<img class="logo" src="/assets/images/logo.png" />
				</a>
			</div>-->
			<div class="navbar-buttons navbar-header pull-right" role="navigation">
				<ul class="nav ace-nav">
					@if(Auth::check())
					<li>
						<form id="logout" action="{{ route('logout') }}" method="post">
							@csrf
						</form>
						<a href="#" onclick="$('#logout').submit();">
							<i class="ace-icon fa fa-sign-out"></i>
							Logout
						</a>
					</li>
					@else
					<li>
						<a href="{{ route('login') }}">
							<i class="ace-icon fa fa-sign-in"></i>
							Login
						</a>
					</li>
					@endif
				</ul>
			</div>
		</div>
	</div>

	<div class="main-container" id="main-container">
		<script type="text/javascript">
			try {
				ace.settings.check('main-container', 'fixed')
			} catch (e) {}
		</script>

		<div id="sidebar" class="sidebar responsive sidebar-fixed sidebar-scroll">
			<script type="text/javascript">
				try {
					ace.settings.check('sidebar', 'fixed')
				} catch (e) {}
			</script>
			<ul class="nav nav-list" style="padding-top:20px">
				<li class="{{ (Route::current()->getName() == 'home')? 'active highlight':''}}">
					<a href="{{ route('home') }}">
						<i class="menu-icon fa fa-home"></i>
						<span class="menu-text">Beranda</span>
					</a>

					<b class="arrow"></b>
				</li>
				@if(Auth::check())
				@if(Auth::user()->role == 0 || Auth::user()->role == 1)
				<li class="{{ (Route::current()->getName() == 'master.pengeluaran.kategori') || (Route::current()->getName() == 'master.pengeluaran.kategori') || (Route::current()->getName() == 'master.pemasukan.kategori') || (Route::current()->getName() == 'master.santri') || (Route::current()->getName() == 'master.staff') || (Route::current()->getName() == 'master.guru') || (Route::current()->getName() == 'master.users') || (Route::current()->getName() == 'master.assets')? 'active highlight':''}}">
					<a href="#" class="dropdown-toggle">
						<i class="menu-icon fa fa-folder"></i>
						<span class="menu-text">
							Master
						</span>

						<b class="arrow fa fa-angle-down"></b>
					</a>

					<b class="arrow"></b>

					<ul class="submenu">
						<li class="{{ (Route::current()->getName() == 'master.assets')? 'active highlight':''}}">
							<a href="{{ route('master.assets') }}">
								<i class="menu-icon fa fa-caret-right"></i>
								Assets
							</a>

							<b class="arrow"></b>
						</li>
						<li class="{{ (Route::current()->getName() == 'master.guru')? 'active highlight':''}}">
							<a href="{{ route('master.guru') }}">
								<i class="menu-icon fa fa-caret-right"></i>
								Guru
							</a>

							<b class="arrow"></b>
						</li>
						<li class="{{ (Route::current()->getName() == 'master.pemasukan.kategori')? 'active highlight':''}}">
							<a href="{{ route('master.pemasukan.kategori') }}">
								<i class="menu-icon fa fa-caret-right"></i>
								Kategori Pemasukan
							</a>

							<b class="arrow"></b>
						</li>
						<li class="{{ (Route::current()->getName() == 'master.pengeluaran.kategori')? 'active highlight':''}}">
							<a href="{{ route('master.pengeluaran.kategori') }}">
								<i class="menu-icon fa fa-caret-right"></i>
								Kategori Pengeluaran
							</a>

							<b class="arrow"></b>
						</li>
						<li class="{{ (Route::current()->getName() == 'master.santri')? 'active highlight':''}}">
							<a href="{{ route('master.santri') }}">
								<i class="menu-icon fa fa-caret-right"></i>
								Santri / Santri Wati
							</a>

							<b class="arrow"></b>
						</li>
						<li class="{{ (Route::current()->getName() == 'master.staff')? 'active highlight':''}}">
							<a href="{{ route('master.staff') }}">
								<i class="menu-icon fa fa-caret-right"></i>
								Staff
							</a>

							<b class="arrow"></b>
						</li>
						<li class="{{ (Route::current()->getName() == 'master.users')? 'active highlight':''}}">
							<a href="{{ route('master.users') }}">
								<i class="menu-icon fa fa-caret-right"></i>
								Users
							</a>

							<b class="arrow"></b>
						</li>
					</ul>
				</li>
				<li class="{{ (Route::current()->getName() == 'pemasukan')? 'active highlight':''}}">
					<a href="{{ route('pemasukan') }}">
						<i class="menu-icon fa fa-download"></i>
						<span class="menu-text">Pemasukan</span>
					</a>

					<b class="arrow"></b>
				</li>
				<li class="{{ (Route::current()->getName() == 'pengeluaran')? 'active highlight':''}}">
					<a href="{{ route('pengeluaran') }}">
						<i class="menu-icon fa fa-upload"></i>
						<span class="menu-text">Pengeluaran</span>
					</a>

					<b class="arrow"></b>
				</li>
				<li class="{{ (Route::current()->getName() == 'settings')? 'active highlight':''}}">
					<a href="{{ route('settings') }}">
						<i class="menu-icon fa fa-gears"></i>
						<span class="menu-text">Pengaturan</span>
					</a>

					<b class="arrow"></b>
				</li>
				@endif
				@endif
			</ul>

			<div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
				<i class="ace-icon fa fa-angle-double-left" data-icon1="ace-icon fa fa-angle-double-left" data-icon2="ace-icon fa fa-angle-double-right"></i>
			</div>

			<div class="space-12"></div>
			<div class="center" style="color:#fff;">
				<small>Version {{ config('app.version', '1.0') }}</small>
			</div>

			<script type="text/javascript">
				try {
					ace.settings.check('sidebar', 'collapsed')
				} catch (e) {}
			</script>

		</div>

		<div class="main-content">
			<div class="main-content-inner">
				<div class="breadcrumbs breadcrumbs-fixed" id="breadcrumbs">
					<script type="text/javascript">
						try {
							ace.settings.check('breadcrumbs', 'fixed')
						} catch (e) {}
					</script>

					<ul class="breadcrumb">
						<li>
							<i class="ace-icon fa fa-home home-icon"></i>
							<a href="/">{{ config('app.name', 'Demo') }}</a>
						</li>
						<li class="active">@yield('title')</li>
					</ul>
					@if(Route::current()->getName() == 'master.ebook' || Route::current()->getName() == 'master.category')
					<div class="nav-search" id="nav-search">
						<form class="form-search">
							<span class="input-icon">
								<input type="text" id="search" name="search" placeholder="Search ..." class="nav-search-input" id="nav-search-input" autocomplete="off" />
								<i class="ace-icon fa fa-search nav-search-icon"></i>
							</span>
						</form>
					</div>
					@endif
				</div>

				<div class="page-content">
					<div class="page-header">
						<h1>@yield('title')</h1>
					</div>

					<div class="row">
						<div class="col-xs-12">
							@yield('content')
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="footer">
			<div class="footer-inner">
				<div class="footer-content">
					@if(Auth::check())
					<span class="block">Anda login sebagai <b>{{ Auth::user()->username }}</b> (<a href="#" onclick="$('#logout').submit();">Logout</a>)</span>
					@endif
					<span class="block">
						Copyright &copy; {{ date('Y') }} <a href="https://www.dhp.co.id">PT. DHP DIGITAL TEKNOLOGI</a>.
					</span>
				</div>
			</div>
		</div>

		<a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
			<i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i>
		</a>
	</div>

	<script src="/assets/js/jquery.2.1.1.min.js"></script>

	<script type="text/javascript">
		window.jQuery || document.write("<script src='/assets/js/jquery.min.js'>" + "<" + "/script>");
	</script>

	<script type="text/javascript">
		if ('ontouchstart' in document.documentElement) document.write("<script src='/assets/js/jquery.mobile.custom.min.js'>" + "<" + "/script>");
	</script>
	<script src="/assets/js/bootstrap.min.js"></script>

	<script src="/assets/js/jquery-ui.custom.min.js"></script>
	<script src="/assets/js/jquery.ui.touch-punch.min.js"></script>

	<script src="/assets/js/ace-elements.min.js"></script>
	<script src="/assets/js/ace.min.js"></script>
	<script src="/plugins/toastr/toastr.min.js"></script>
	@hasSection('custom-js')
	@yield('custom-js')
	@endif
	@if(Session::has('success'))
	<script>
		toastr.success('{!! Session::get("success") !!}');
	</script>
	@endif
	@if(Session::has('error'))
	<script>
		toastr.error('{!! Session::get("error") !!}');
	</script>
	@endif
	@if(!empty($errors->all()))
	<script>
		toastr.error('{!! implode("", $errors->all("<li>:message</li>")) !!}');
	</script>
	@endif
</body>

</html>
