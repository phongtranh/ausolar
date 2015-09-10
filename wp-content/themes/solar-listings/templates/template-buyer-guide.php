<?php
/**
 * Template Name: Buyer guide
 */
?>
<head>
	<meta charset="utf-8">
	
	<title><?php wp_title(); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
	<div id="main-wrapper" class="container">
		<article id="content">
			<?php echo get_the_content(); ?>
		</article>
	</div>
</body>
</html>