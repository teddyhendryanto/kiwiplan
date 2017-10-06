<!doctype html>
<html lang="en">

<head>
	<title> @yield('title') | Kiwiplan </title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
	<!-- VENDOR CSS -->
  <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">
	<link rel="stylesheet" href="{{ asset('vendor/font-awesome/css/font-awesome.min.css') }}">
	<!-- MAIN CSS -->
	<link rel="stylesheet" href="{{ asset('css/main.css') }}">

	@yield('pluginscss')

	<!-- GOOGLE FONTS -->
	{{-- <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700" rel="stylesheet"> --}}

	<!-- ICONS -->
  <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('img/'.env('SITE_ASSET').'/apple-icon.png') }}">
	<link rel="icon" type="image/png" sizes="96x96" href="{{ asset('img/'.env('SITE_ASSET').'/favicon.png') }}">
</head>

<body class="layout-fullwidth">
	<!-- WRAPPER -->
	<div id="wrapper">
		<!-- NAVBAR -->
		@include('components.navbar')
		<!-- END NAVBAR -->

		<!-- LEFT SIDEBAR -->
		@include('components.sidebar')
		<!-- END LEFT SIDEBAR -->

		<!-- MAIN -->
		<div class="main">
			<!-- MAIN CONTENT -->
			<div class="main-content">
				<div class="container-fluid">
					@yield('breadcrumb')

					@yield('content')
				</div>
			</div>
			<!-- END MAIN CONTENT -->
		</div>
		<!-- END MAIN -->
		<div class="clearfix"></div>

		<!-- FOOTER -->
		@include('components.footer')
		<!-- END FOOTER -->
	</div>
	<!-- END WRAPPER -->

	<!-- Jquery-1.12-4 -->
  <script src="{{ asset('vendor/jquery/jquery-2.1.4.min.js')}}"></script>
	<!-- Javascript -->
	<script src="{{ asset('vendor/bootstrap/js/bootstrap.min.js')}}"></script>
	<script src="{{ asset('vendor/jquery-slimscroll/jquery.slimscroll.min.js')}}"></script>
	<script src="{{ asset('js/common.js')}}"></script>

	@yield('pluginsjs')

	@yield('script')
</body>

</html>
