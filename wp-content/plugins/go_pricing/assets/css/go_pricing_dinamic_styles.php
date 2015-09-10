<?php
/* Block direct access */
if ( ! defined( 'WPINC' ) ) { die; }

$table_settings = get_option( GW_GO_PREFIX . 'table_settings' );

/* default settings if not set */
if ( empty( $table_settings ) ) {
	$table_settings['primary-font']='Arial, Helvetica, sans-serif';
	$table_settings['primary-font-css']='';
	$table_settings['secondary-font']='Verdana, Geneva, sans-serif';
	$table_settings['secondary-font-css']='';
	$table_settings['colw-min']='130px';
	$table_settings['colw-max']='';
	$table_settings['transitions-chk']='on';
	$table_settings['transitions']='1';
	$table_settings['responsivity-chk']='on';
	$table_settings['responsivity']='1';
	$table_settings['size1-min']='768px';
	$table_settings['size1-max']='959px';
	$table_settings['size2-min']='480px';
	$table_settings['size2-max']='767px';
	$table_settings['size3-min']='';
	$table_settings['size3-max']='479px';
	print_r($table_settings['responsivity']);
}
if ( !empty( $table_settings['primary-font-css'] ) ) { '@import url(' . $table_settings['primary-font-css'] . ');'; }
?>
/* -------------------------------------------------------------------------------- /

	Plugin Name: Go - Responsive Pricing & Compare Tables
	Plugin URI: http://codecanyon.net/item/go-responsive-pricing-compare-tables-for-wp/3725820
	Description: The New Generation Pricing Tables. If you like traditional Pricing Tables, but you would like get much more out of it, then this rodded product is a useful tool for you.
	Author: Granth
	Version: 2.4.3
	Author URI: http://themeforest.net/user/Granth

	+----------------------------------------------------+
		TABLE OF CONTENTS
	+----------------------------------------------------+

    [0] IMPORT FONTS
    [1] RESET
    [2] SETUP
    [3] LAYOUT
    [3.1] HEADER
    [3.2] BODY
    [3.3] FOOTER & BUTTONS
    [4] ICONS
    [5] RIBBONS
    [6] COLUMN SHADOWS<?php
    if ( isset( $table_settings['responsivity'] ) && $table_settings['responsivity'] == '1' ) : ?>    		
    [7] MEDIA QUERIES
	[8] CUSTOM CSS
	<?php else: ?> 
	[7] CUSTOM CSS	
	<?php endif; ?> 

/ -------------------------------------------------------------------------------- */

/* -------------------------------------------------------------------------------- /
	[0]	IMPORT FONT
/ -------------------------------------------------------------------------------- */

<?php if ( !empty( $table_settings['primary-font-css'] ) ) { echo "\t" . '@import url(' . $table_settings['primary-font-css'] . ');' . "\n"; } ?>
<?php if ( !empty( $table_settings['secondary-font-css'] ) &&  $table_settings['primary-font-css'] != $table_settings['secondary-font-css'] ) { echo "\t" . '@import url(' . $table_settings['secondary-font-css'] . ');' . "\n"; } ?>

/* -------------------------------------------------------------------------------- /
	[1]	RESET
/ -------------------------------------------------------------------------------- */

	.gw-go { -webkit-tap-highlight-color: rgba(0,0,0,0); }
	.gw-go * {
		background:none;
		border:none;
        -moz-box-sizing:content-box !important;
		-webkit-box-sizing:content-box !important;
		box-sizing:content-box !important;
		margin:0;
		outline:none;		
		padding:0;
		letter-spacing:normal !important;
		text-transform:none;
		text-decoration:none !important;
		-webkit-tap-highlight-color: rgba(0,0,0,0);
        <?php if ( isset( $table_settings['transitions'] ) && $table_settings['transitions'] == '0' ) : ?>
		-moz-transition:all 0s linear !important;
		-ms-transition:all 0s linear !important;
		-o-transition:all 0s linear !important;
		-webkit-transition:all 0s linear !important;
		transition:all 0s linear !important;        
        <?php endif; ?>  		
	}

/* -------------------------------------------------------------------------------- /
	[2]	SETUP - general settings, clearfix, common classes
/ -------------------------------------------------------------------------------- */

	/* clearfix */
	.gw-go-clearfix:after {
		content:".";
		display:block;
		height:0;
		clear:both;
		visibility:hidden;
	}
	.gw-go-clearfix { display:inline-block; } /* Hide from IE Mac \*/
	.gw-go-clearfix { display:block; } /* End hide from IE Mac */
	.gw-go-clearfix:after {
		content:".";
		display:block;
		height:0;
		clear:both;
		visibility:hidden;
	}
	
	/* clearfix class */
	.gw-go-clear {
		clear:both;
		display:block;
		font-size:0;
		height:0;
		line-height:0;
		width:100%;
	}
	
	/* text aligns */
	.gw-go-tcenter { text-align:center; }
	.gw-go-tleft { text-align:center; }
	.gw-go-tright { text-align:right; }

	/* video iframe */
	.gw-go-video-wrapper {
		padding-bottom:56.25% !important;
		position:relative;	
		height:0; 	
	}
    .gw-go-header-bottom .gw-go-video-wrapper { margin-bottom:-1px; }
	.gw-go-video-wrapper iframe {
        height:100%;
        left:0;
        margin:0;
        border:none;
        outline:none;
        position:absolute;
        top:0;
        width:100%;
	}
	
	/* image settings */
	.gw-go-img-wrapper { position:relative; }
	.gw-go img {
		display:inline-block;
		vertical-align:top;
	}
	img.gw-go-responsive-img {
	    border:none !important;
		height:auto !important;
    	margin:0 !important;
        paddig:0 !important;        
        max-width:100% !important;
		width:100% !important;
	}
    .gw-go audio, 
    .gw-go video {
    	margin:0;
        paddig:0;
		/*height:auto !important;*/
        max-width:100% !important;
		width:100% !important;        
    }
    /* fix for google map popup & mediaelementjs styling bug in some themes */
    .gw-go-gmap img { max-width: none !important; }
    .gw-go .mejs-container img {
		height: auto !important;    
    	max-width: none !important;
		width:100% !important;        
	}
	.gw-go-ie8 .me-plugin, 
	.gw-go-ie8 .mejs-mediaelement { position: static !important; }
	
	/* table & input settings for paypal */
	.gw-go-btn-wrap table {
		border:none;
		margin:0 auto;
		width:auto;
		text-align:center;
	}
	.gw-go-btn-wrap td {
		border:none;	
		margin:0;
		padding:0 0 10px 0;
	}
	.gw-go-btn-wrap input[type="text"] {
		background:#FFF !important;
		border:solid 1px #b8b8b8;
		-moz-border-radius:3px;
		-webkit-border-radius:3px;
		border-radius:3px;
		-moz-box-shadow:1px 1px 3px rgba(0,0,0,0.15) inset;
		-webkit-box-shadow:1px 1px 3px rgba(0,0,0,0.15) inset;
		box-shadow:1px 1px 3px rgba(0,0,0,0.15) inset;
		font-family: Verdana, Geneva, sans-serif;	
		font-size:12px;
		height:20px;
		line-height:20px;
		padding:3px 5px;	
		-moz-transition:all 0.15s linear;
		-o-transition:all 0.15s linear;
		-webkit-transition:all 0.15s linear;
		transition:all 0.15s linear;
	}
	.gw-go-btn-wrap input[type="text"]:focus {
		border:solid 1px #9d9d9d;
		-moz-box-shadow:1px 1px 3px rgba(0,0,0,0.15) inset;
		-webkit-box-shadow:1px 1px 3px rgba(0,0,0,0.15) inset;
		box-shadow:1px 1px 3px rgba(0,0,0,0.15) inset;	
	}
		
/* -------------------------------------------------------------------------------- /
	[3] LAYOUT
/ -------------------------------------------------------------------------------- */

	.gw-go {
		font-size:12px;
		line-height:16px;
		margin:auto;
		width:100%;
	}
	
	/* default colum widths */
	.gw-go-1col .gw-go-col-wrap { width:100%; }
	.gw-go-2cols .gw-go-col-wrap { width:50%; }
	.gw-go-3cols .gw-go-col-wrap { width:33.33%; }
	.gw-go-4cols .gw-go-col-wrap { width:25%; }
	.gw-go-5cols .gw-go-col-wrap { width:20%; }
	.gw-go-6cols .gw-go-col-wrap { width:16.66%; }
	.gw-go-7cols .gw-go-col-wrap { width:14.285%; }	

	/* 1% left space */
	.gw-go-space-1p.gw-go-2cols .gw-go-col-wrap { width:49.5%; }
	.gw-go-space-1p.gw-go-3cols .gw-go-col-wrap { width:32.66%; }
	.gw-go-space-1p.gw-go-4cols .gw-go-col-wrap { width:24.25%; }
	.gw-go-space-1p.gw-go-5cols .gw-go-col-wrap { width:19.2%; }
	.gw-go-space-1p.gw-go-6cols .gw-go-col-wrap { width:15.83%; }
	.gw-go-space-1p.gw-go-7cols .gw-go-col-wrap { width:13.428%; }		
	.gw-go-space-1p .gw-go-col-wrap { margin-left:1%; }

	/* 2% left space */
	.gw-go-space-2p.gw-go-2cols .gw-go-col-wrap { width:49%; }
	.gw-go-space-2p.gw-go-3cols .gw-go-col-wrap { width:32%; }
	.gw-go-space-2p.gw-go-4cols .gw-go-col-wrap { width:23.5%; }
	.gw-go-space-2p.gw-go-5cols .gw-go-col-wrap { width:18.4%; }
	.gw-go-space-2p.gw-go-6cols .gw-go-col-wrap { width:15%; }
	.gw-go-space-2p.gw-go-7cols .gw-go-col-wrap { width:12.571%; }
	.gw-go-space-2p .gw-go-col-wrap { margin-left:2%; }	

	/* 3% left space */
	.gw-go-space-3p.gw-go-2cols .gw-go-col-wrap { width:48.5%; }
	.gw-go-space-3p.gw-go-3cols .gw-go-col-wrap { width:31.33%; }
	.gw-go-space-3p.gw-go-4cols .gw-go-col-wrap { width:22.75%; }
	.gw-go-space-3p.gw-go-5cols .gw-go-col-wrap { width:17.6%; }
	.gw-go-space-3p.gw-go-6cols .gw-go-col-wrap { width:14.16%; }
	.gw-go-space-3p.gw-go-7cols .gw-go-col-wrap { width:11.714%; }
	.gw-go-space-3p .gw-go-col-wrap { margin-left:3%; }	

	/* 4% left space */
	.gw-go-space-4p.gw-go-2cols .gw-go-col-wrap { width:48%; }
	.gw-go-space-4p.gw-go-3cols .gw-go-col-wrap { width:30.66%; }
	.gw-go-space-4p.gw-go-4cols .gw-go-col-wrap { width:22%; }
	.gw-go-space-4p.gw-go-5cols .gw-go-col-wrap { width:16.8%; }
	.gw-go-space-4p.gw-go-6cols .gw-go-col-wrap { width:13.33%; }
	.gw-go-space-4p.gw-go-7cols .gw-go-col-wrap { width:10.857%; }	
	.gw-go-space-4p .gw-go-col-wrap { margin-left:4%; }	

	/* 5% left space */
	.gw-go-space-5p.gw-go-2cols .gw-go-col-wrap { width:47.5%; }
	.gw-go-space-5p.gw-go-3cols .gw-go-col-wrap { width:30%; }
	.gw-go-space-5p.gw-go-4cols .gw-go-col-wrap { width:21.25%; }
	.gw-go-space-5p.gw-go-5cols .gw-go-col-wrap { width:16%; }
	.gw-go-space-5p.gw-go-6cols .gw-go-col-wrap { width:12.5%; }
	.gw-go-space-5p.gw-go-7cols .gw-go-col-wrap { width:10%; }	
	.gw-go-space-5p .gw-go-col-wrap { margin-left:5%; }	
	
	.gw-go-col-wrap {
		float:left;
		margin-left:-1px;
		<?php if ( !empty( $table_settings['colw-min'] ) ) : ?>
        min-width:<?php echo $table_settings['colw-min']; ?>;
        <?php endif; ?>
		<?php if ( !empty( $table_settings['colw-max'] ) ) : ?>
        max-width:<?php echo $table_settings['colw-max']; ?>;
        <?php endif; ?>
		padding:20px 0 40px;
		padding-left:0 !important;
		padding-right:0 !important;		
		position:relative;
		-moz-transition:all 0.2s linear;
		-ms-transition:all 0.2s linear;
		-o-transition:all 0.2s linear;
		-webkit-transition:all 0.2s linear;
		transition:all 0.2s linear;
	}
	.gw-go-col-wrap:first-child { margin-left:0; }
	
	.gw-go-col {
		border:solid 1px #EBEBEB;
		border-bottom:solid 2px #D3D3D3;
        border-top-width:2px;
		margin-bottom:-20px;		
		position:relative;
		text-align:center;				
		top:0;
		-moz-transition:margin-top 0.2s linear, top 0.2s linear, -moz-box-shadow 0.2s linear;
		-ms-transition:margin-top 0.2s linear, top 0.2s linear, box-shadow 0.2s linear;
		-o-transition:margin-top 0.2s linear, top 0.2s linear, box-shadow 0.2s linear;
		-webkit-transition:margin-top 0.2s linear, top 0.2s linear, -webkit-box-shadow 0.2s linear;
		transition:margin-top 0.2s linear, top 0.2s linear, box-shadow 0.2s linear;
	}
	.gw-go-col:before {
		content:'';
		height:23px;		
		margin-top:2px;
       	filter:alpha(opacity=40);
		-khtml-opacity:0.4;
		-moz-opacity:0.4;
		-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=40)";  
		opacity:0.4;		
		position:absolute;
		left:0;
		top:100%;
		width:100%;
	}
	
	/* enlarge highlighted column / column on hover option is enabled */
	.gw-go.gw-go-enlarge-current.gw-go-hover .gw-go-col-wrap.gw-go-current { padding:20px 0 40px; }	
	.gw-go.gw-go-hover .gw-go-col-wrap.gw-go-current .gw-go-col,
	.gw-go.gw-go-enlarge-current.gw-go-hover .gw-go-col-wrap.gw-go-current .gw-go-col {	
		-moz-box-shadow:0 0 20px -2px rgba(0,0,0,0);
		-webkit-box-shadow:0 0 20px -2px rgba(0,0,0,0);
		box-shadow:0 0 20px -2px rgba(0,0,0,0);		
		margin-top:0;
		top:0;
	}
	.gw-go.gw-go-enlarge-current .gw-go-col-wrap.gw-go-current, 
	.gw-go.gw-go-enlarge-current .gw-go-col-wrap.gw-go-hover, 
	.gw-go.gw-go-enlarge-current.gw-go-hover .gw-go-col-wrap.gw-go-current.gw-go-hover {
		padding:0;
		margin-bottom:0;
	}

	.gw-go.gw-go-enlarge-current.gw-go-no-footer.gw-go-hover .gw-go-col-wrap.gw-go-current {
		margin-bottom:0;
	}	
	.gw-go.gw-go-enlarge-current.gw-go-no-footer .gw-go-col-wrap.gw-go-current,
	.gw-go.gw-go-enlarge-current.gw-go-no-footer .gw-go-col-wrap.gw-go-hover,
	.gw-go.gw-go-enlarge-current.gw-go-no-footer.gw-go-hover .gw-go-col-wrap.gw-go-current.gw-go-hover  {
		margin-bottom:40px;
	}
		
	
	.gw-go.gw-go-enlarge-current .gw-go-col-wrap.gw-go-current .gw-go-col,
	.gw-go.gw-go-enlarge-current .gw-go-col-wrap.gw-go-hover .gw-go-col,
	.gw-go.gw-go-enlarge-current.gw-go-hover .gw-go-col-wrap.gw-go-current.gw-go-hover .gw-go-col {
		-moz-box-shadow:0 0 20px -2px rgba(0,0,0,0.25);
		-webkit-box-shadow:0 0 20px -2px rgba(0,0,0,0.25);
		box-shadow:0 0 20px -2px rgba(0,0,0,0.25);		
		margin-top:20px;
		top:-20px;
	}
	.gw-go.gw-go-enlarge-current .gw-go-col-wrap.gw-go-current { z-index:2; }
	.gw-go.gw-go-enlarge-current .gw-go-col-wrap.gw-go-hover { z-index:3; }
	
	/* disable enlarge */
	.gw-go-col-wrap.gw-go-disable-enlarge { padding:20px 0 20px !important; }
	.gw-go-col-wrap.gw-go-disable-enlarge .gw-go-footer { height:67px !important; }
	.gw-go-col-wrap.gw-go-disable-enlarge .gw-go-btn-wrap { bottom:20px !important; }
	
	.gw-go-col-wrap.gw-go-disable-enlarge .gw-go-col {
		 -moz-box-shadow:none !important;
		-webkit-box-shadow:none !important;
		box-shadow:none !important;
	}	
        
/* -------------------------------------------------------------------------------- /
	[3.1] HEADER
/ -------------------------------------------------------------------------------- */

	.gw-go-header {
	    overflow:hidden; 
    	position:relative;
        font-family:<?php echo trim( $table_settings['primary-font'],';').";\n"; ?>
        text-align:center;	
     }
	.gw-go-header img { margin:0 !important; }
	.gw-go-header p { margin-bottom:4px; }
    /** 1. regular pricing header **/
	
	/* header containers */
	.gw-go-header-top {
		height:95px;	
		position:relative;
	}
	.gw-go-header h1 {
    	color:inherit;
 		font-size:32px !important;
        line-height:32px !important;
		font-family:<?php echo trim( $table_settings['primary-font'],';').";\n"; ?>
		font-weight:normal !important;
	    letter-spacing:normal !important;           
        margin-bottom:15px !important;
        padding:0 !important;         
		text-transform:none	!important;
        top:15px; 
    }
    .gw-go-header h1 small { font-size:26px; }
	.gw-go-header h2 {
       	color:inherit;
 		font-size:26px !important;
        line-height:32px !important;
		font-family:<?php echo trim( $table_settings['primary-font'],';').";\n"; ?>
		font-weight:normal !important;
	    letter-spacing:normal !important;        
        margin-bottom:15px !important;
        padding:0 !important;         
		text-transform:none	!important;
        top:15px;  
    }    
    .gw-go-header h3 {
    	color:inherit;    
		font-size:18px !important;
		line-height:16px !important;
		font-family:<?php echo trim( $table_settings['primary-font'],';').";\n"; ?>
		font-weight:normal !important;
		left:0;
	    letter-spacing:normal !important;
        margin:0 !important;
        padding:0 !important;
		position:absolute;
		text-align:center;	
		text-transform:none	!important;   
        top:19px;
		width:100%;
	}
    .gw-go-header h3 small {
	    display:block;
        font-size:12px;
    }
	.gw-go-header-bottom {
		border-top:solid 1px transparent;	
		height:50px;
	}

	/* pricing coins */
	.gw-go-coin-wrap {		
		font-family:<?php echo trim( $table_settings['primary-font'],';').";\n"; ?>
		font-size:32px;
		height:78px;
		left:50%;
		margin:0 0 0 -40px;
		position:absolute;			
		top:54px;			
		width:78px;
        z-index:1;
	}
	.gw-go-coinf, 
	.gw-go-coinb {
		-webkit-border-radius:50px;
		-moz-border-radius:50px;
		border-radius:50px;
		height:76px;		
		left:0;		
		position:absolute;		
		width:76px;
	}

	.gw-go-coinf div, 
	.gw-go-coinb div {
		background:#FFF;		
		-moz-background-clip: padding; 
        -webkit-background-clip: padding-box; 
        background-clip: padding-box;
		-moz-box-shadow:1px 1px 3px rgba(0,0,0,0.38) inset;
		-webkit-box-shadow:1px 1px 3px rgba(0,0,0,0.38) inset;
		box-shadow:1px 1px 3px rgba(0,0,0,0.38) inset;
        -moz-box-sizing:content-box !important;
		-webkit-box-sizing:content-box !important;
		box-sizing:content-box !important;		
		-webkit-border-radius:50px;
		-moz-border-radius:50px;
		border-radius:50px;
		font-size:32px;
		height:56px;			
		margin:0 auto;
		margin-top:-1px;
		margin-left:-1px;
		padding-top:22px;		
		width:78px;
		top:0;
	}
	.gw-go-coinf small, 
	.gw-go-coinb small {
		display:block;
		font-size:12px;
		margin-top:9px;	
	}
	.gw-go-coinb,
	.gw-go-col-wrap.gw-go-hover .gw-go-coinf,
	.gw-go-col-wrap.gw-go-current .gw-go-coinf { visibility:hidden; }

	.gw-go-coinf,
	.gw-go-col-wrap.gw-go-hover .gw-go-coinb,
	.gw-go-col-wrap.gw-go-current .gw-go-coinb { visibility:visible; }	
	
/* -------------------------------------------------------------------------------- /
	[3.2] BODY
/ -------------------------------------------------------------------------------- */

	ul.gw-go-body {
		border-bottom:solid 1px transparent;
        list-style:none !important;		
		margin:0 !important;
		padding:0 !important;
		position:relative;
	}
	ul.gw-go-body li {
	    background:none;
		border-top:solid 1px #FFF;
        -moz-box-sizing:border-box !important;
		-webkit-box-sizing:border-box !important;
		box-sizing:border-box !important;
		display:table;		
		font-family:<?php echo trim( $table_settings['secondary-font'],';').";\n"; ?>;
        min-height:17px;
        line-height:16px !important;
		list-style:none !important;	
		margin:0 !important;
		padding:10px 5px !important;
		width:100%;
	}
	ul.gw-go-body li .gw-go-body-cell {
		display:table-cell;
		vertical-align:middle;
	}
	
	ul.gw-go-body li:before, ul.gw-go-body li:after { display:none !important; }
	ul.gw-go-body li.gw-go-has-tooltip { position:relative; }
	ul.gw-go-body li.gw-go-has-tooltip span.gw-go-tooltip { 
		background:#9D9D9D;
        border-color:#9D9D9D;
		-moz-border-radius:4px;
		-webkit-border-radius:4px;
		border-radius:4px;
		-moz-box-shadow:0 0 5px rgba(0,0,0,0.15);
		-webkit-box-shadow:0 0 5px rgba(0,0,0,0.15);
		box-shadow:0 0 5px rgba(0,0,0,0.15);			
		bottom:100%;
		color:#333333;
		left:50%;
        margin-bottom:-17px;        
		margin-left:-15px;
		opacity:0;
		padding:6px 10px 6px 10px;	
		position:absolute;		
		text-align:left;
		text-decoration:none !important;
		-moz-transition:all 0.0s linear;
		-ms-transition:all 0.0s linear;
		-o-transition:all 0.0s linear;
		-webkit-transition:all 0.0s linear;
		transition:all 0.0s linear;		
		visibility:hidden;
		width:130px;
		z-index:4;
	}
	ul.gw-go-body li.gw-go-has-tooltip:hover span.gw-go-tooltip {
		opacity:1;
		visibility:visible;
		bottom:100%;
        margin-bottom:-3px;
		-moz-transition:opacity 0.2s linear, visibility 0s linear, margin-bottom 0.2s linear;
		-ms-transition:opacity 0.2s linear, visibility 0s linear, margin-bottom 0.2s linear;
		-o-transition:opacity 0.2s linear, visibility 0s linear, margin-bottom 0.2s linear;
		-webkit-transition:opacity 0.2s linear, visibility 0s linear, margin-bottom 0.2s linear;
		transition:opacity 0.2s linear, visibility 0s linear, margin-bottom 0.2s linear;
	
	}	
	ul.gw-go-body li.gw-go-has-tooltip span.gw-go-tooltip:before {
		border-right:6px solid transparent;	
		border-left:6px solid transparent;		
		border-top:6px solid #9D9D9D;
        border-top-color:inherit;
		content:'';
		left:10px;
		position:absolute;
		top:100%;
	}
    ul.gw-go-body li a {
      	color:inherit;
    	text-decoration:none !important; 
     }
	ul.gw-go-body li a:hover { text-decoration:underline !important; }    
	
/* -------------------------------------------------------------------------------- /
	[3.3] FOOTER & BUTTONS
/ -------------------------------------------------------------------------------- */

	/* colum footer */
	.gw-go-footer,
	.gw-go.gw-go-hover .gw-go-footer,
	.gw-go.gw-go-hover .gw-go-col-wrap.gw-go-current .gw-go-footer,
	.gw-go.gw-go-enlarge-current.gw-go-hover .gw-go-col-wrap.gw-go-current .gw-go-footer { 
		height:67px; 
		-moz-transition:all 0.2s linear;
		-ms-transition:all 0.2s linear; 
		-o-transition:all 0.2s linear; 
		-webkit-transition:all 0.2s linear; 
		transition:all 0.2s linear; 
	}

	.gw-go.gw-go-enlarge-current .gw-go-col-wrap.gw-go-current .gw-go-footer, 
	.gw-go.gw-go-enlarge-current.gw-go-hover .gw-go-col-wrap.gw-go-hover .gw-go-footer, 
	.gw-go.gw-go-enlarge-current.gw-go-hover .gw-go-col-wrap.gw-go-current.gw-go-hover .gw-go-footer { height:107px; }

	/* button general settings */
	.gw-go-btn {
		-moz-border-radius:4px;
		-webkit-border-radius:4px;
		border-radius:4px;
        cursor:pointer;		
		display:inline-block;
		font-family:<?php echo trim( $table_settings['primary-font'],';').";\n"; ?>
		margin:0 5px !important;
		text-decoration:none;		
	}
    span.gw-go-btn form { display:none !important; }
    .gw-go-btn:hover { text-decoration:none !important; }

	/* button hover & active states */
	.gw-go-col-wrap.gw-go-hover .gw-go-btn:active, .gw-go.gw-go-hover .gw-go-col-wrap.gw-go-current .gw-go-btn:active {	
		-moz-box-shadow:0 0 0 rgba(0,0,0,0.15) inset;
		-webkit-box-shadow:0 0 0 rgba(0,0,0,0.15) inset;
		box-shadow:0 0 0 rgba(0,0,0,0.15) inset;
		-moz-transition:all 0s linear;
		-ms-transition:all 0s linear; 
		-o-transition:all 0s linear; 
		-webkit-transition:all 0s linear; 
		transition:all 0s linear; 		
	}
	
	/* button sizes - small, medium, large */
	.gw-go-btn-small {
		font-size:11px;
		height:18px;
		line-height:18px;
		padding:0 5px;
	}
	.gw-go-btn-medium {
		font-size:12px;
		height:27px;
		line-height:27px;
		padding:0 8px;
	}
	.gw-go-btn-large {
		font-size:16px;
		height:42px;
		line-height:42px;
		padding:0 15px;
	}
	.gw-go-btn-wrap {
		bottom:20px;		
		display:table;				
		height:87px;
		margin-top:20px;
		position:relative;
		-moz-transition:bottom 0.2s linear;	
		-o-transition:bottom 0.2s linear;	
		-webkit-transition:bottom 0.2s linear;
		transition:bottom 0.2s linear;
		width:100%;		
	}
	.gw-go-btn-wrap-inner {
		display:table-cell;
		vertical-align:middle;
	}
	
	/* button - column hover event */
	.gw-go.gw-go-enlarge-current .gw-go-col-wrap.gw-go-hover .gw-go-btn-wrap,
	.gw-go.gw-go-enlarge-current .gw-go-col-wrap.gw-go-current .gw-go-btn-wrap,
	.gw-go.gw-go-enlarge-current.gw-go-hover .gw-go-col-wrap.gw-go-current.gw-go-hover .gw-go-btn-wrap { bottom:0px; }
	.gw-go.gw-go-enlarge-current.gw-go-hover .gw-go-col-wrap.gw-go-current .gw-go-btn-wrap { bottom:20px; } 	

/* -------------------------------------------------------------------------------- /
	[4]	ICONS
/ -------------------------------------------------------------------------------- */	

	.gw-go-icon-left { margin-left:0 !important; }
	.gw-go-icon-right { margin-right:0 !important; }
 	
	/* body icons */
	span[class*="gw-go-icon"] {
    	background-position: 50% 50% no-repeat;
		display:inline-block;
		height:16px;
		margin:0 3px -4px;
		width:16px;
	}
    /* team icons */
	.gw-go-icon-light-skype { background:url(../images/signs/icon_team_light_skype.png) 50% 50% no-repeat; }
	.gw-go-icon-light-facebook { background:url(../images/signs/icon_team_light_facebook.png) 50% 50% no-repeat; }
	.gw-go-icon-light-twitter { background:url(../images/signs/icon_team_light_twitter.png) 50% 50% no-repeat; }
	.gw-go-icon-light-email { background:url(../images/signs/icon_team_light_email.png) 50% 50% no-repeat; }
	.gw-go-icon-dark-skype { background:url(../images/signs/icon_team_dark_skype.png) 50% 50% no-repeat; }
	.gw-go-icon-dark-facebook { background:url(../images/signs/icon_team_dark_facebook.png) 50% 50% no-repeat; }
	.gw-go-icon-dark-twitter { background:url(../images/signs/icon_team_dark_twitter.png) 50% 50% no-repeat; }
	.gw-go-icon-dark-email { background:url(../images/signs/icon_team_dark_email.png) 50% 50% no-repeat; }    

	/* light icons */
	.gw-go-icon-light-arrow { background:url(../images/signs/icon_light_arrow.png) 50% 50% no-repeat; }
	.gw-go-icon-light-arrow2 { background:url(../images/signs/icon_light_arrow2.png) 50% 50% no-repeat; }
	.gw-go-icon-light-circle { background:url(../images/signs/icon_light_circle.png) 50% 50% no-repeat; }
	.gw-go-icon-light-cross { background:url(../images/signs/icon_light_cross.png) 50% 50% no-repeat; }
	.gw-go-icon-light-dot { background:url(../images/signs/icon_light_dot.png) 50% 50% no-repeat; }
	.gw-go-icon-light-minus { background:url(../images/signs/icon_light_minus.png) 50% 50% no-repeat; }
	.gw-go-icon-light-ok { background:url(../images/signs/icon_light_ok.png) 50% 50% no-repeat; }
	.gw-go-icon-light-plus { background:url(../images/signs/icon_light_plus.png) 50% 50% no-repeat; }
	.gw-go-icon-light-star { background:url(../images/signs/icon_light_star.png) 50% 50% no-repeat; }
	
	/* dark icons */
	.gw-go-icon-dark-arrow { background:url(../images/signs/icon_dark_arrow.png) 50% 50% no-repeat; }
	.gw-go-icon-dark-arrow2 { background:url(../images/signs/icon_dark_arrow2.png) 50% 50% no-repeat; }
	.gw-go-icon-dark-circle { background:url(../images/signs/icon_dark_circle.png) 50% 50% no-repeat; }
	.gw-go-icon-dark-cross { background:url(../images/signs/icon_dark_cross.png) 50% 50% no-repeat; }
	.gw-go-icon-dark-dot { background:url(../images/signs/icon_dark_dot.png) 50% 50% no-repeat; }
	.gw-go-icon-dark-minus { background:url(../images/signs/icon_dark_minus.png) 50% 50% no-repeat; }
	.gw-go-icon-dark-ok { background:url(../images/signs/icon_dark_ok.png) 50% 50% no-repeat; }
	.gw-go-icon-dark-plus { background:url(../images/signs/icon_dark_plus.png) 50% 50% no-repeat; }
	.gw-go-icon-dark-star { background:url(../images/signs/icon_dark_star.png) 50% 50% no-repeat; }
	
	/* red icons */
	.gw-go-icon-red-arrow { background:url(../images/signs/icon_red_arrow.png) 50% 50% no-repeat; }
	.gw-go-icon-red-arrow2 { background:url(../images/signs/icon_red_arrow2.png) 50% 50% no-repeat; }
	.gw-go-icon-red-circle { background:url(../images/signs/icon_red_circle.png) 50% 50% no-repeat; }
	.gw-go-icon-red-cross { background:url(../images/signs/icon_red_cross.png) 50% 50% no-repeat; }
	.gw-go-icon-red-dot { background:url(../images/signs/icon_red_dot.png) 50% 50% no-repeat; }
	.gw-go-icon-red-minus { background:url(../images/signs/icon_red_minus.png) 50% 50% no-repeat; }
	.gw-go-icon-red-ok { background:url(../images/signs/icon_red_ok.png) 50% 50% no-repeat; }
	.gw-go-icon-red-plus { background:url(../images/signs/icon_red_plus.png) 50% 50% no-repeat; }
	.gw-go-icon-red-star { background:url(../images/signs/icon_red_star.png) 50% 50% no-repeat; }
	
	/* green icons */
	.gw-go-icon-green-arrow { background:url(../images/signs/icon_green_arrow.png) 50% 50% no-repeat; }
	.gw-go-icon-green-arrow2 { background:url(../images/signs/icon_green_arrow2.png) 50% 50% no-repeat; }
	.gw-go-icon-green-circle { background:url(../images/signs/icon_green_circle.png) 50% 50% no-repeat; }
	.gw-go-icon-green-cross { background:url(../images/signs/icon_green_cross.png) 50% 50% no-repeat; }
	.gw-go-icon-green-dot { background:url(../images/signs/icon_green_dot.png) 50% 50% no-repeat; }
	.gw-go-icon-green-minus { background:url(../images/signs/icon_green_minus.png) 50% 50% no-repeat; }
	.gw-go-icon-green-ok { background:url(../images/signs/icon_green_ok.png) 50% 50% no-repeat; }
	.gw-go-icon-green-plus { background:url(../images/signs/icon_green_plus.png) 50% 50% no-repeat; }
	.gw-go-icon-green-star { background:url(../images/signs/icon_green_star.png) 50% 50% no-repeat; }		

	/* button icons */
 	span[class*="gw-go-btn-icon"] {
		display:inline-block;
		height:20px;
		margin:0 5px -5px;
		width:20px;
	}
	span[class*="gw-go-btn-icon"][class*="gw-go-btn-icon-large"] {
		height:24px;
		margin:0 5px -6px;
		width:24px;	
	}
	.gw-go-btn-icon-medium-white-basket { background:url(../images/signs/icon_white_basket_medium.png) 50% 50% no-repeat; }
	.gw-go-btn-icon-medium-white-download { background:url(../images/signs/icon_white_download_medium.png) 50% 50% no-repeat; }
	.gw-go-btn-icon-large-white-basket { background:url(../images/signs/icon_white_basket_large.png) 50% 50% no-repeat; }	

/* -------------------------------------------------------------------------------- /
	[5]	RIBBONS
/ -------------------------------------------------------------------------------- */
	
	.gw-go-ribbon-left, .gw-go-ribbon-right {
		height:75px;
		left:-1px;		
		position:absolute;
		width:75px;
        top:-1px;
		z-index:1;		
	}
	.gw-go-ribbon-right {
	    background-position:100% 0 !important;
		left:auto;
		right:-1px;
	}
	/* blue left */
	.gw-go-ribbon-left-blue-50percent { background:url(../images/ribbons/ribbon_blue_left_50percent.png) 0 0 no-repeat; }
	.gw-go-ribbon-left-blue-new { background:url(../images/ribbons/ribbon_blue_left_new.png) 0 0 no-repeat; }
	.gw-go-ribbon-left-blue-top { background:url(../images/ribbons/ribbon_blue_left_top.png) 0 0 no-repeat; }
	.gw-go-ribbon-left-blue-save { background:url(../images/ribbons/ribbon_blue_left_save.png) 0 0 no-repeat; }			

	/* blue right */
	.gw-go-ribbon-right-blue-50percent { background:url(../images/ribbons/ribbon_blue_right_50percent.png) 0 0 no-repeat; }
	.gw-go-ribbon-right-blue-new { background:url(../images/ribbons/ribbon_blue_right_new.png) 0 0 no-repeat; }
	.gw-go-ribbon-right-blue-top { background:url(../images/ribbons/ribbon_blue_right_top.png) 0 0 no-repeat; }
	.gw-go-ribbon-right-blue-save { background:url(../images/ribbons/ribbon_blue_right_save.png) 0 0 no-repeat; }

	/* green left */
	.gw-go-ribbon-left-green-50percent { background:url(../images/ribbons/ribbon_green_left_50percent.png) 0 0 no-repeat; }
	.gw-go-ribbon-left-green-new { background:url(../images/ribbons/ribbon_green_left_new.png) 0 0 no-repeat; }
	.gw-go-ribbon-left-green-top { background:url(../images/ribbons/ribbon_green_left_top.png) 0 0 no-repeat; }
	.gw-go-ribbon-left-green-save { background:url(../images/ribbons/ribbon_green_left_save.png) 0 0 no-repeat; }			

	/* green right */
	.gw-go-ribbon-right-green-50percent { background:url(../images/ribbons/ribbon_green_right_50percent.png) 0 0 no-repeat; }
	.gw-go-ribbon-right-green-new { background:url(../images/ribbons/ribbon_green_right_new.png) 0 0 no-repeat; }
	.gw-go-ribbon-right-green-top { background:url(../images/ribbons/ribbon_green_right_top.png) 0 0 no-repeat; }
	.gw-go-ribbon-right-green-save { background:url(../images/ribbons/ribbon_green_right_save.png) 0 0 no-repeat; }

	/* red left */
	.gw-go-ribbon-left-red-50percent { background:url(../images/ribbons/ribbon_red_left_50percent.png) 0 0 no-repeat; }
	.gw-go-ribbon-left-red-new { background:url(../images/ribbons/ribbon_red_left_new.png) 0 0 no-repeat; }
	.gw-go-ribbon-left-red-top { background:url(../images/ribbons/ribbon_red_left_top.png) 0 0 no-repeat; }
	.gw-go-ribbon-left-red-save { background:url(../images/ribbons/ribbon_red_left_save.png) 0 0 no-repeat; }			

	/* red right */
	.gw-go-ribbon-right-red-50percent { background:url(../images/ribbons/ribbon_red_right_50percent.png) 0 0 no-repeat; }
	.gw-go-ribbon-right-red-new { background:url(../images/ribbons/ribbon_red_right_new.png) 0 0 no-repeat; }
	.gw-go-ribbon-right-red-top { background:url(../images/ribbons/ribbon_red_right_top.png) 0 0 no-repeat; }
	.gw-go-ribbon-right-red-save { background:url(../images/ribbons/ribbon_red_right_save.png) 0 0 no-repeat; }

	/* yellow left */
	.gw-go-ribbon-left-yellow-50percent { background:url(../images/ribbons/ribbon_yellow_left_50percent.png) 0 0 no-repeat; }
	.gw-go-ribbon-left-yellow-new { background:url(../images/ribbons/ribbon_yellow_left_new.png) 0 0 no-repeat; }
	.gw-go-ribbon-left-yellow-top { background:url(../images/ribbons/ribbon_yellow_left_top.png) 0 0 no-repeat; }
	.gw-go-ribbon-left-yellow-save { background:url(../images/ribbons/ribbon_yellow_left_save.png) 0 0 no-repeat; }			

	/* yellow right */
	.gw-go-ribbon-right-yellow-50percent { background:url(../images/ribbons/ribbon_yellow_right_50percent.png) 0 0 no-repeat; }
	.gw-go-ribbon-right-yellow-new { background:url(../images/ribbons/ribbon_yellow_right_new.png) 0 0 no-repeat; }
	.gw-go-ribbon-right-yellow-top { background:url(../images/ribbons/ribbon_yellow_right_top.png) 0 0 no-repeat; }
	.gw-go-ribbon-right-yellow-save { background:url(../images/ribbons/ribbon_yellow_right_save.png) 0 0 no-repeat; }

/* -------------------------------------------------------------------------------- /
	[6] COLUMN SHADOWS
/ -------------------------------------------------------------------------------- */

	.gw-go-col.gw-go-shadow1:before { background:url(../images/shadows/shadow_1.png) 50% 100% no-repeat; }
	.gw-go-col.gw-go-shadow2:before { background:url(../images/shadows/shadow_2.png) 50% 100% no-repeat; }
	.gw-go-col.gw-go-shadow3:before { background:url(../images/shadows/shadow_3.png) 50% 100% no-repeat; } 
	.gw-go-col.gw-go-shadow4:before { background:url(../images/shadows/shadow_4.png) 50% 100% no-repeat; }
	.gw-go-col.gw-go-shadow5:before { background:url(../images/shadows/shadow_5.png) 50% 100% no-repeat; }
	.gw-go-col.gw-go-shadow1:before,
	.gw-go-col.gw-go-shadow2:before,
	.gw-go-col.gw-go-shadow3:before,
	.gw-go-col.gw-go-shadow4:before,
	.gw-go-col.gw-go-shadow5:before { background-size:100% 23px; }

<?php if ( isset( $table_settings['responsivity'] ) && $table_settings['responsivity'] == '1' )  : ?>

/* -------------------------------------------------------------------------------- /
	[7]	MEDIA QUERIES
/ -------------------------------------------------------------------------------- */

/* -------------------------------------------------------------------------------- /
	[7.1] TABLET (PORTRAIT)
/ -------------------------------------------------------------------------------- */

	@media only screen<?php 
		echo isset( $table_settings['size1-min'] ) && $table_settings['size1-min'] != '' ? ' and (min-width: ' . $table_settings['size1-min'] . ')' : '' ;
		echo isset( $table_settings['size1-max'] ) && $table_settings['size1-max'] != '' ? ' and (max-width: ' . $table_settings['size1-max'] . ')' : '' 		
		?> {
	}

/* -------------------------------------------------------------------------------- /
	[7.2] MOBILE (PORTRAIT)
/ -------------------------------------------------------------------------------- */

	@media only screen<?php 
		echo isset( $table_settings['size2-min'] ) && $table_settings['size2-min'] != '' ? ' and (min-width: ' . $table_settings['size2-min'] . ')' : '' ;
		echo isset( $table_settings['size2-max'] ) && $table_settings['size2-max'] != '' ? ' and (max-width: ' . $table_settings['size2-max'] . ')' : '' 		
		?> {
   		.gw-go-1col .gw-go-col-wrap { width:100% !important; }

		.gw-go-2cols .gw-go-col-wrap,
		.gw-go-3cols .gw-go-col-wrap,
		.gw-go-4cols .gw-go-col-wrap,
		.gw-go-5cols .gw-go-col-wrap,
		.gw-go-6cols .gw-go-col-wrap,
		.gw-go-7cols .gw-go-col-wrap { width:50% !important; }
		
        .gw-go-space-1p .gw-go-col-wrap { width:49.5% !important; }
        .gw-go-space-2p .gw-go-col-wrap { width:49% !important; }
        .gw-go-space-3p .gw-go-col-wrap { width:48.5% !important; }
        .gw-go-space-4p .gw-go-col-wrap { width:48% !important; }
        .gw-go-space-5p .gw-go-col-wrap { width:47.5% !important; }                                
		.gw-go-col-wrap:nth-of-type(2n-1) { margin-left:0 !important; }               
	}

/* -------------------------------------------------------------------------------- /
	[7.3] MOBILE (LANDSCAPE)
/ -------------------------------------------------------------------------------- */

	@media only screen<?php 
		echo isset( $table_settings['size3-min'] ) && $table_settings['size3-min'] != '' ? ' and (min-width: ' . $table_settings['size3-min'] . ')' : '' ;
		echo isset( $table_settings['size3-max'] ) && $table_settings['size3-max'] != '' ? ' and (max-width: ' . $table_settings['size3-max'] . ')' : '' 		
		?> {
   		.gw-go-1col .gw-go-col-wrap,
		.gw-go-2cols .gw-go-col-wrap,
		.gw-go-3cols .gw-go-col-wrap,
		.gw-go-4cols .gw-go-col-wrap,
		.gw-go-5cols .gw-go-col-wrap,
		.gw-go-6cols .gw-go-col-wrap,
		.gw-go-7cols .gw-go-col-wrap {
        	margin-left:0 !important;
        	float:left !important;
        	width:100% !important;
         } 
	}
<?php endif; ?>

<?php if ( isset( $table_settings['responsivity'] ) && $table_settings['responsivity'] == '1' )  : ?>
/* -------------------------------------------------------------------------------- /
	[8]	CUSTOM CSS
/ -------------------------------------------------------------------------------- */

<?php else: ?>
/* -------------------------------------------------------------------------------- /
	[7]	CUSTOM CSS
/ -------------------------------------------------------------------------------- */
<?php endif; ?>

<?php 
	if( isset( $table_settings['custom-css'] ) && !empty( $table_settings['custom-css'] ) ) {
		echo $table_settings['custom-css'];
	}
?>


