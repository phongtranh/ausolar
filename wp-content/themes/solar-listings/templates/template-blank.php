<?php
/**
 * Template Name: Empty
 */
?>
<html>
	<head>
		<title><?php the_title(); ?></title>
		<?php wp_head(); ?>

		<style type="text/css">
			html{margin: 0 !important; padding: 0 15px; overflow: hidden;}
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
			.error-message-overlay{
				position: absolute;
				z-index: 999;
				width: 66%;
				height: 336px;
				margin-left: -15%;
				background: rgba( 0, 0, 0, .8 );
				color: #fff;
				font-size: 18px;
				padding: 120px 60px 0 60px;
				text-align: center;
			}
		</style>

	</head>
	<body>
	<div id="sidebar">
		<?php echo do_shortcode('[solar-quote_widget_short]'); ?>
	</div>
	</body>
</html>