<?php
/**
 * Template Name: Company Rating Widget
 */
?>

<?php
$post_id = empty( $_GET['id'] ) ? 0 : absint( $_GET['id'] );
if ( ! $post_id )
{
	echo 'Invalid company';
	die;
}
$class = isset( $_GET['theme'] ) && 'dark' == $_GET['theme'] ? 'dark' : 'white';
?>

<!DOCTYPE html>
<html>
<head>
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:700,400' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
	<style>
		html {
			margin: 0 !important;
		}

		body {
			font: 400 14px/1 'Open Sans', Arial, sans-serif;
			text-align: center;
			margin: 0;
			padding: 10px;
			box-sizing: border-box;
		}

		a {
			text-decoration: none;
			color: #7f7f7f;
		}

		a img {
			border: 0;
		}

		strong {
			font-weight: 700;
		}

		h1,
		h2 {
			margin: 0;
			line-height: 1;
		}

		h1 {
			color: #000;
			display: block;
			font-size: 17px;
			font-weight: 700;
		}

		h2 {
			font-weight: 400;
			font-size: 13px;
		}

		.header {
			display: inline-block;
		}

		.logo {
			padding: 0;
			border-radius: 0;
			margin-right: 2px;
			width: 30px;
			height: 30px;
		}

		.main {
			margin: 30px 0 20px;
		}

		.fa {
			font-size: 50px;
			line-height: 54px;
			width: 50px;
			height: 50px;
		}

		.star {
			display: inline-block;
			margin: 0 3px;
			border-radius: 3px;
			color: #fff;
		}
		.star-red .star {
			background: #FC1E1E;
		}
		.star-yellow .star {
			background: #fcf33c;
		}
		.star-orange .star {
			background: #FCCA1E;
		}
		.star-green .star {
			background: #0ed011;
		}

		.star.no {
			background: #ccc;
		}

		.dark a {
			color: #aaa;
		}

		.dark h1 {
			color: #fff;
		}

		.tiny {
			font-size: 11px;
		}

		.tiny h1 {
			font-size: 10px;
			margin-bottom: 5px;
		}

		.tiny h2 {
			font-size: 9px;
		}

		.tiny .logo {
			display: none;
		}

		.tiny main {
			margin: 20px 0 10px;
		}

		.tiny .fa {
			font-size: 24px;
			line-height: 24px;
			width: 24px;
			height: 24px;
		}

		.small {
			font-size: 12px;
		}

		.small h1 {
			font-size: 13px;
			margin-bottom: 7px;
		}

		.small h2 {
			font-size: 11px;
		}

		.small main {
			margin: 20px 0 10px;
		}

		.small .fa {
			font-size: 38px;
			line-height: 40px;
			width: 38px;
			height: 38px;
		}
	</style>
</head>
<body class="<?= $class; ?>">
<a href="<?= get_permalink( $post_id ); ?>" target="_blank">
	<div>
		<img src="<?= CHILD_URL; ?>images/logo-mini.png" class="logo">
		<div class="header">
			<h1>AUSTRALIAN SOLAR QUOTES</h1>
			<h2>RATINGS YOU CAN TRUST</h2>
		</div>
	</div>
	<div class="main">
		<?php
		$average = Sl_Company_Helper::get_average_rating( $post_id );
		$count   = Sl_Company_Helper::get_no_reviews( $post_id );
		$stars   = floatval( $average );

		if ( $stars < 1.1 )
		{
			$class = 'red';
			$stars = 0;
		}
		elseif ( $stars < 2.1 )
		{
			$class = 'orange';
			$stars = 2;
		}
		elseif ( $stars < 3.5 )
		{
			$class = 'yellow';
			$stars = 3;
		}
		elseif ( $stars < 4.3 )
		{
			$class = 'green';
			$stars = 4;
		}
		else
		{
			$class = 'green';
			$stars = 5;
		}

		echo '<span class="star-', $class, '">';
		for ( $i = 0; $i < $stars; $i ++ )
		{
			echo '<span class="yes star"><i class="fa fa-star"></i></span>';
		}
		for ( $i = $stars; $i < 5; $i ++ )
		{
			echo '<span class="no star"><i class="fa fa-star"></i></span>';
		}
		echo '</span>';
		?>
	</div>
	<div>
		ASQSCORE <strong><?= $average; ?></strong>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<strong><?= $count; ?></strong> REVIEWS
	</div>
</a>
<script>
	(function ()
	{
		var width = self.innerWidth,
			body = document.querySelector( 'body' );
		if ( width < 200 )
		{
			body.classList.add( 'tiny' );
		}
		else if ( width < 300 )
		{
			body.classList.add( 'small' );
		}
	})();
</script>
</body>
</html>
