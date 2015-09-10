<h2><?php _e( 'Background', '7listings' ); ?></h2>

<?php Sl_Form::background( 'body' ); ?>

<h2><?php _e( 'Layout', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Select basic page layout:<br>fluid, box, boxed', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></h2>

<?php include THEME_TABS . 'design/layout.php'; ?>