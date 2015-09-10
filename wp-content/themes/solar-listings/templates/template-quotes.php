<?php
/**
 * Template Name: Quotes
 */
?>
<!DOCTYPE html>
<html lang="en-AU">
<head>
	<title><?php the_title(); ?></title>
	<meta charset="utf-8">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<style type="text/css">
		body{
			padding-top: 60px;
		}
		#header img{
			width: 250px;
			max-width: 80%;
			margin: auto;
			display: block;
			margin: auto;
		}
		#heading{
			background: #F99F21;
			margin-top: 40px;
			border-bottom: 5px solid #ddd;
			padding: 20px 0 30px 0;
		}
		h1{
			font-size: 24px;
		}
		h2{
			font-size: 18px;
			font-weight: normal;
		}
		#main{
			background: url('/wp-content/uploads/backgroundimage.jpg') no-repeat;
		}

		#content{
			padding: 20px 0;
		}

		#footer{
			padding: 30px 0;
		}

		.thumbnail{
			padding: 0 10px;
			background: none;
			border: none;
		}

		.thumbnail > .caption{
			text-align: center;
		}
		
		.thumbnail a .image{
			display: block;
			width: 100%;
			min-height: 180px;
			background-repeat: no-repeat !important;
			background-size: 100% 100%;
		}

		@media (max-width: 767px){
			.thumbnail a .image{
				min-height: 250px;
			}
		}

		.thumbnail a h3{
			font-size: 18px;
			text-align: center;
		}

		#thumbnail-home a .image{
			background-image: url('/wp-content/uploads/quotes/home_offstate.svg');
		}

		#thumbnail-home a:hover .image{
			background-image: url('/wp-content/uploads/quotes/home_onstate.svg');
		}
		#thumbnail-batteries a .image{
			background-image: url('/wp-content/uploads/quotes/batteries_offstate.svg')
		}
		#thumbnail-batteries a:hover .image{
			background-image: url('/wp-content/uploads/quotes/batteries_onstate.svg')
		}

		#thumbnail-electric-hw a .image, #thumbnail-hw a .image{
			background-image: url('/wp-content/uploads/quotes/hotwater_offstate.svg');
		}
		
		#thumbnail-electric-hw a:hover .image, #thumbnail-hw a:hover .image{
			background-image: url('/wp-content/uploads/quotes/hotwater_onstate.svg');
		}

		#thumbnail-business a .image{
			background-image: url('/wp-content/uploads/quotes/business_offstate.svg')
		}
		#thumbnail-business a:hover .image{
			background-image: url('/wp-content/uploads/quotes/business_onstate.svg')
		}

		#thumbnail-maintenance a .image{
			background-image: url('/wp-content/uploads/quotes/maintenance_offstate.svg')
		}
		#thumbnail-maintenance a:hover .image{
			background-image: url('/wp-content/uploads/quotes/maintenance_onstate.svg')
		}

	</style>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body <?php body_class(); ?>>
	<header id="header">
		<div class="container">
			<a class="logo" href="https://www.australiansolarquotes.com.au/">
				<img src="https://www.australiansolarquotes.com.au/wp-content/uploads/logo.svg" alt="Australian Solar Quotes">
			</a>
		</div>
	</header>

	<div id="heading">
		<div class="container">
			<div class="row">
				<div class="col-md-12 text-center">
					<h1>Compare Quotes and Find a Great Installer</h1>
					<h2>Compare the market with ASQ and find trusted &amp; reviewed solar installers</h2>	
				</div>
			</div>
		</div>
	</div>

	<div id="main">
		<div class="container">
			<div class="row" id="content">
				<div class="col-md-12">
					<h3 class="text-center">What kind of solar are you interested in?</h3>
				</div>
			</div>
			
			<div class="row" id="cards">
				<div class="thumbnail col-md-2 col-sm-4 col-xs-6" id="thumbnail-home">
					<a href="https://www.australiansolarquotes.com.au/quotes/form?type=solar">
						<div class="image"></div>
						<div class="caption">
							<h3>Solar Electricity for your Home</h3>
						</div>
					</a>
				</div>

				<div class="thumbnail col-md-2 col-sm-4 col-xs-6" id="thumbnail-batteries">
					<a href="https://www.australiansolarquotes.com.au/quotes/form?type=batteries">
						<div class="image"></div>
						<div class="caption">
							<h3>Solar + Batteries for your Home</h3>
						</div>
					</a>
				</div>

				<div class="thumbnail col-md-2 col-sm-4 col-xs-6" id="thumbnail-hw">
					<a href="https://www.australiansolarquotes.com.au/quotes/form?type=hotwater">
						<div class="image"></div>
						<div class="caption">
							<h3>Solar Hot Water for your Home</h3>
						</div>
					</a>
				</div>

				<div class="thumbnail col-md-2 col-sm-4 col-xs-6" id="thumbnail-electric-hw">
					<a href="https://www.australiansolarquotes.com.au/quotes/form?type=solarandhotwater">
						<div class="image"></div>
						<div class="caption">
							<h3>Solar Electricity &amp; Solar Hot Water for your Home</h3>
						</div>
					</a>
				</div>

				<div class="thumbnail col-md-2 col-sm-4 col-xs-6" id="thumbnail-business">
					<a href="https://www.australiansolarquotes.com.au/quotes/form?type=commercial">
						<div class="image"></div>
						<div class="caption">
							<h3>Solar for your Business</h3>
						</div>
					</a>
				</div>

				<div class="thumbnail col-md-2 col-sm-4 col-xs-6" id="thumbnail-maintenance">
					<a href="https://www.australiansolarquotes.com.au/quotes/form?type=maintenance">
						<div class="image"></div>
						<div class="caption">
							<h3>Maintenance &amp; Repairs of an existing system</h3>
						</div>
					</a>
				</div>
			</div>


			<footer id="footer" class="row">
				<div class="col-md-12 text-center">
					<p>&copy; 2015 <a href="https://www.australiansolarquotes.com.au">Australian Solar Quotes</a> - good advice guarantee</p>
					<p><strong>Ph: 1300 303 864 | Privacy Policy | Terms &amp; Conditions | Feedback</strong></p>
				</div>
			</footer>
		</div>
	</div>
	
	<?php wp_footer(); ?>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</body>
</html>