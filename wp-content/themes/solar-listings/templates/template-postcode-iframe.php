<?php
/**
 * Template Name: Postcode Iframe
 */


?>
<html>
	<head>
		<title><?php the_title(); ?></title>
		<?php wp_head(); ?>

		<style type="text/css">
			html{ margin: 0 !important; padding: 0 15px; overflow: hidden; }
			html:hover{overflow-y: scroll; padding-right: 9px;}
			html::-webkit-scrollbar-track
			{
				-webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
				background-color: #F5F5F5;
			}
			html::-webkit-scrollbar
			{
				width: 6px;
				background-color: #F5F5F5;
			}

			html::-webkit-scrollbar-thumb
			{
				background-color: #333;
			}
			.social-buttons, #livechat-compact-container{ display: none !important; visibility: hidden }
			
			#sidebar{
				padding: 0;
			}
		</style>

	</head>
	<body <?php body_class(); ?>>
	<div id="sidebar">
		<?php echo do_shortcode('[solar-postcode_quote]'); ?>
	</div>
	</body>
</html>