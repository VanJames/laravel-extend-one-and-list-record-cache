<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
	<meta name="description" content="">
	<meta name="author" content="">
	<link rel="shortcut icon" href="{{ asset('/common/images/favicon.png')}}" type="image/png">

	<title>后台管理</title>

	<link href="{{ asset('/common/css/style.default.css')}}" rel="stylesheet">

	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
	<script src="{{ asset('/common/js/html5shiv.js')}}"></script>
	<script src="{{ asset('/common/js/respond.min.js')}}"></script>
	<![endif]-->
</head>

<body>

{{var_dump(1)}}

<!-- Preloader -->
<div id="preloader">
	<div id="status"><i class="fa fa-spinner fa-spin"></i></div>
</div>

<section>

	@extends('admin.common.left')

	<div class="mainpanel">

			@include('admin/common/header')

			@include('admin/common/position')

		@yield('content')

	</div><!-- mainpanel -->

	@extends('admin.common.right')


</section>


<script src="{{ asset('/common/js/jquery-1.11.1.min.js')}}"></script>
<script src="{{ asset('/common/js/jquery-migrate-1.2.1.min.js')}}"></script>
<script src="{{ asset('/common/js/jquery-ui-1.10.3.min.js')}}"></script>
<script src="{{ asset('/common/js/bootstrap.min.js')}}"></script>
<script src="{{ asset('/common/js/modernizr.min.js')}}"></script>
<script src="{{ asset('/common/js/jquery.sparkline.min.js')}}"></script>
<script src="{{ asset('/common/js/toggles.min.js')}}"></script>
<script src="{{ asset('/common/js/retina.min.js')}}"></script>
<script src="{{ asset('/common/js/jquery.cookies.js')}}"></script>

<script src="{{ asset('/common/js/flot/jquery.flot.min.js')}}"></script>
<script src="{{ asset('/common/js/flot/jquery.flot.resize.min.js')}}"></script>
<script src="{{ asset('/common/js/flot/jquery.flot.spline.min.js')}}"></script>
<script src="{{ asset('/common/js/morris.min.js')}}"></script>
<script src="{{ asset('/common/js/raphael-2.1.0.min.js')}}"></script>

<script src="{{ asset('/common/js/custom.js')}}"></script>
<script src="{{ asset('/common/js/chosen.jquery.js')}}"></script>
<script src="{{ asset('/common/js/dashboard.js')}}"></script>
<div style="text-align:center;">
	<p>后台：<a href="javascript:void(0)" target="_blank">后台</a></p>
</div>
</body>
</html>