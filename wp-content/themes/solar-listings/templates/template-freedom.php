<?php
/**
* Template Name: Freedom
**/
?>
<html lang="en-AU">

<head>
	<meta charset="utf-8">
	
	<title><?php wp_title(); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
	
	<style type="text/css">
		.social-media, .stb-container{
			display: none;
			visibility: hidden;
		}

		.page-id-20064.page-template-template-freedom .container{
			width: 100%;
		}
	</style>
</head>

<body <?php body_class(); ?>>
	
	<div id="main-wrapper" class="container">
		<article id="content">
			<?php the_content(); ?>
		</article>
	</div>
	<?php wp_footer(); ?>
</body>
</html>