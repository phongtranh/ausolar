<?php
/**
 * List of all fonts used in theme
 *
 * @return array
 */
function sl_get_fonts()
{
	// Use static variable to cache the return value of this function
	static $fonts = null;
	if ( ! empty( $fonts ) )
		return $fonts;

	$fonts = array();

	// Custom fonts
	$base_url         = sl_get_font_base_url();
	$fonts['Raleway'] = array(
		'type'        => 'web',
		'font-family' => '"Raleway", sans-serif',
		'css'         => '
@font-face {
	font-family: "Raleway";
	src: url("' . $base_url . 'raleway/thin.eot");
	src: url("' . $base_url . 'raleway/thin.eot?iefix") format("eot"),
		 url("' . $base_url . 'raleway/thin.woff") format("woff"),
		 url("' . $base_url . 'raleway/thin.ttf") format("truetype"),
		 url("' . $base_url . 'raleway/thin.svg#webfontraleway") format("svg");
	font-weight: normal;
	font-style: normal;
}',
	);

	// Ostrich Font Family
	$fonts['Ostrich Black']   = array(
		'type'        => 'web',
		'font-family' => '"Ostrich Black", sans-serif',
		'css'         => '
@font-face {
	font-family: "Ostrich Black";
	src: url("' . $base_url . 'ostrich/black.eot");
	src: url("' . $base_url . 'ostrich/black.eot?iefix") format("eot"),
		 url("' . $base_url . 'ostrich/black.woff") format("woff"),
		 url("' . $base_url . 'ostrich/black.ttf") format("truetype"),
		 url("' . $base_url . 'ostrich/black.svg#webfontostrich") format("svg");
	font-weight: normal;
	font-style: normal;
}',
	);
	$fonts['Ostrich Bold']    = array(
		'type'        => 'web',
		'font-family' => '"Ostrich Bold", sans-serif',
		'css'         => '
@font-face {
	font-family: "Ostrich Bold";
	src: url("' . $base_url . 'ostrich/bold.eot");
	src: url("' . $base_url . 'ostrich/bold.eot?iefix") format("eot"),
		 url("' . $base_url . 'ostrich/bold.woff") format("woff"),
		 url("' . $base_url . 'ostrich/bold.ttf") format("truetype"),
		 url("' . $base_url . 'ostrich/bold.svg#webfontostrich") format("svg");
	font-weight: normal;
	font-style: normal;
}',
	);
	$fonts['Ostrich Dashed']  = array(
		'type'        => 'web',
		'font-family' => '"Ostrich Dashed", sans-serif',
		'css'         => '
@font-face {
	font-family: "Ostrich Dashed";
	src: url("' . $base_url . 'ostrich/dashed.eot");
	src: url("' . $base_url . 'ostrich/dashed.eot?iefix") format("eot"),
		 url("' . $base_url . 'ostrich/dashed.woff") format("woff"),
		 url("' . $base_url . 'ostrich/dashed.ttf") format("truetype"),
		 url("' . $base_url . 'ostrich/dashed.svg#webfontostrich") format("svg");
	font-weight: normal;
	font-style: normal;
}',
	);
	$fonts['Ostrich Rounded'] = array(
		'type'        => 'web',
		'font-family' => '"Ostrich Rounded", sans-serif',
		'css'         => '
@font-face {
	font-family: "Ostrich Rounded";
	src: url("' . $base_url . 'ostrich/rounded.eot");
	src: url("' . $base_url . 'ostrich/rounded.eot?iefix") format("eot"),
		 url("' . $base_url . 'ostrich/rounded.woff") format("woff"),
		 url("' . $base_url . 'ostrich/rounded.ttf") format("truetype"),
		 url("' . $base_url . 'ostrich/rounded.svg#webfontostrich") format("svg");
	font-weight: normal;
	font-style: normal;
}',
	);
	$fonts['Ostrich Regular'] = array(
		'type'        => 'web',
		'font-family' => '"Ostrich Regular", sans-serif',
		'css'         => '
@font-face {
	font-family: "Ostrich Regular";
	src: url("' . $base_url . 'ostrich/normal.eot");
	src: url("' . $base_url . 'ostrich/normal.eot?iefix") format("eot"),
		 url("' . $base_url . 'ostrich/normal.woff") format("woff"),
		 url("' . $base_url . 'ostrich/normal.ttf") format("truetype"),
		 url("' . $base_url . 'ostrich/normal.svg#webfontostrich") format("svg");
	font-weight: normal;
	font-style: normal;
}',
	);
	$fonts['Ostrich Light']   = array(
		'type'        => 'web',
		'font-family' => '"Ostrich Light", sans-serif',
		'css'         => '
@font-face {
	font-family: "Ostrich Light";
	src: url("' . $base_url . 'ostrich/light.eot");
	src: url("' . $base_url . 'ostrich/light.eot?iefix") format("eot"),
		 url("' . $base_url . 'ostrich/light.woff") format("woff"),
		 url("' . $base_url . 'ostrich/light.ttf") format("truetype"),
		 url("' . $base_url . 'ostrich/light.svg#webfontostrich") format("svg");
	font-weight: normal;
	font-style: normal;
}',
	);

	// League Gothic
	$fonts['League Gothic'] = array(
		'type'        => 'web',
		'font-family' => '"League Gothic", sans-serif',
		'css'         => '
@font-face {
	font-family: "League Gothic";
	src: url("' . $base_url . 'league-gothic/bold.eot");
	src: url("' . $base_url . 'league-gothic/bold.eot?iefix") format("eot"),
		 url("' . $base_url . 'league-gothic/bold.woff") format("woff"),
		 url("' . $base_url . 'league-gothic/bold.ttf") format("truetype"),
		 url("' . $base_url . 'league-gothic/bold.svg#webfontLeague_Gothic") format("svg");
	font-weight: normal;
	font-style: normal;
}',
	);

	// DIN Cond Font Family
	$fonts['DIN Cond Bold']   = array(
		'type'        => 'web',
		'font-family' => '"DINCondBold", sans-serif',
		'css'         => '
@font-face {
	font-family: "DINCondBold";
	src: url("' . $base_url . 'din-cond/bold.eot");
	src: url("' . $base_url . 'din-cond/bold.eot?iefix") format("eot"),
		 url("' . $base_url . 'din-cond/bold.woff") format("woff"),
		 url("' . $base_url . 'din-cond/bold.ttf") format("truetype"),
		 url("' . $base_url . 'din-cond/bold.svg#webfontAXxr213b") format("svg");
	font-weight: normal;
	font-style: normal;
}',
	);
	$fonts['DIN Cond Normal'] = array(
		'type'        => 'web',
		'font-family' => '"DINCondRegular", sans-serif',
		'css'         => '
@font-face {
	font-family: "DINCondRegular";
	src: url("' . $base_url . 'din-cond/normal.eot");
	src: url("' . $base_url . 'din-cond/normal.eot?iefix") format("eot"),
		 url("' . $base_url . 'din-cond/normal.woff") format("woff"),
		 url("' . $base_url . 'din-cond/normal.ttf") format("truetype"),
		 url("' . $base_url . 'din-cond/normal.svg#webfontEcPvUcPM") format("svg");
	font-weight: normal;
	font-style: normal;
}',
	);
	$fonts['DIN Cond Light']  = array(
		'type'        => 'web',
		'font-family' => '"DINCondLight", sans-serif',
		'css'         => '
@font-face {
	font-family: "DINCondLight";
	src: url("' . $base_url . 'din-cond/light.eot");
	src: url("' . $base_url . 'din-cond/light.eot?iefix") format("eot"),
		 url("' . $base_url . 'din-cond/light.woff") format("woff"),
		 url("' . $base_url . 'din-cond/light.ttf") format("truetype"),
		 url("' . $base_url . 'din-cond/light.svg#webfontEcPvUcPM") format("svg");
	font-weight: normal;
	font-style: normal;
}',
	);

	// Barata Display
	$fonts['Barata Display'] = array(
		'type'        => 'web',
		'font-family' => '"Barata-Display", sans-serif',
		'css'         => '
@font-face {
	font-family: "Barata-Display";
	src: url("' . $base_url . 'barata-display/normal.eot");
	src: url("' . $base_url . 'barata-display/normal.eot?iefix") format("eot"),
		 url("' . $base_url . 'barata-display/normal.woff") format("woff"),
		 url("' . $base_url . 'barata-display/normal.ttf") format("truetype"),
		 url("' . $base_url . 'barata-display/normal.svg#webfontEcPvUcPM") format("svg");
	font-weight: normal;
	font-style: normal;
}',
	);

	// Bell Gothic Family
	$fonts['Bell Gothic Bold']  = array(
		'type'        => 'web',
		'font-family' => '"Bell-Gothic-Bold", sans-serif',
		'css'         => '
@font-face {
	font-family: "Bell-Gothic-Bold";
	src: url("' . $base_url . 'bell-gothic/bold.eot");
	src: url("' . $base_url . 'bell-gothic/bold.eot?iefix") format("eot"),
		 url("' . $base_url . 'bell-gothic/bold.woff") format("woff"),
		 url("' . $base_url . 'bell-gothic/bold.ttf") format("truetype"),
		 url("' . $base_url . 'bell-gothic/bold.svg#webfontEcPvUcPM") format("svg");
	font-weight: normal;
	font-style: normal;
}',
	);
	$fonts['Bell Gothic Black'] = array(
		'type'        => 'web',
		'font-family' => '"Bell-Gothic-Black", sans-serif',
		'css'         => '
@font-face {
	font-family: "Bell-Gothic-Black";
	src: url("' . $base_url . 'bell-gothic/black.eot");
	src: url("' . $base_url . 'bell-gothic/black.eot?iefix") format("eot"),
		 url("' . $base_url . 'bell-gothic/black.woff") format("woff"),
		 url("' . $base_url . 'bell-gothic/black.ttf") format("truetype"),
		 url("' . $base_url . 'bell-gothic/black.svg#webfontEcPvUcPM") format("svg");
	font-weight: normal;
	font-style: normal;
}',
	);

	// Snickles
	$fonts['Snickles'] = array(
		'type'        => 'web',
		'font-family' => '"Snickles", sans-serif',
		'css'         => '
@font-face {
	font-family: "Snickles";
	src: url("' . $base_url . 'snickles/normal.eot");
	src: url("' . $base_url . 'snickles/normal.eot?iefix") format("eot"),
		 url("' . $base_url . 'snickles/normal.woff") format("woff"),
		 url("' . $base_url . 'snickles/normal.ttf") format("truetype"),
		 url("' . $base_url . 'snickles/normal.svg#webfontEcPvUcPM") format("svg");
	font-weight: normal;
	font-style: normal;
}',
	);

	// Honey Script
	$fonts['Honey Script'] = array(
		'type'        => 'web',
		'font-family' => '"Honey-Script", sans-serif',
		'css'         => '
@font-face {
	font-family: "Honey-Script";
	src: url("' . $base_url . 'honey-script/light.eot");
	src: url("' . $base_url . 'honey-script/light.eot?iefix") format("eot"),
		 url("' . $base_url . 'honey-script/light.woff") format("woff"),
		 url("' . $base_url . 'honey-script/light.ttf") format("truetype"),
		 url("' . $base_url . 'honey-script/light.svg#webfontEcPvUcPM") format("svg");
	font-weight: normal;
	font-style: normal;
}',
	);

	// Avant Garde
	$fonts['Avant Garde Black'] = array(
		'type'        => 'web',
		'font-family' => '"Avant-Garde-Black", sans-serif',
		'css'         => '
@font-face {
	font-family: "Avant-Garde-Black";
	src: url("' . $base_url . 'avant-garde/black.eot");
	src: url("' . $base_url . 'avant-garde/black.eot?iefix") format("eot"),
		 url("' . $base_url . 'avant-garde/black.woff") format("woff"),
		 url("' . $base_url . 'avant-garde/black.ttf") format("truetype"),
		 url("' . $base_url . 'avant-garde/black.svg#webfontEcPvUcPM") format("svg");
	font-weight: normal;
	font-style: normal;
}',
	);
	$fonts['Avant Garde Bold']  = array(
		'type'        => 'web',
		'font-family' => '"Avant-Garde-Bold", sans-serif',
		'css'         => '
@font-face {
	font-family: "Avant-Garde-Bold";
	src: url("' . $base_url . 'avant-garde/bold.eot");
	src: url("' . $base_url . 'avant-garde/bold.eot?iefix") format("eot"),
		 url("' . $base_url . 'avant-garde/bold.woff") format("woff"),
		 url("' . $base_url . 'avant-garde/bold.ttf") format("truetype"),
		 url("' . $base_url . 'avant-garde/bold.svg#webfontEcPvUcPM") format("svg");
	font-weight: normal;
	font-style: normal;
}',
	);
	$fonts['Avant Garde']       = array(
		'type'        => 'web',
		'font-family' => '"Avant-Garde", sans-serif',
		'css'         => '
@font-face {
	font-family: "Avant-Garde";
	src: url("' . $base_url . 'avant-garde/normal.eot");
	src: url("' . $base_url . 'avant-garde/normal.eot?iefix") format("eot"),
		 url("' . $base_url . 'avant-garde/normal.woff") format("woff"),
		 url("' . $base_url . 'avant-garde/normal.ttf") format("truetype"),
		 url("' . $base_url . 'avant-garde/normal.svg#webfontEcPvUcPM") format("svg");
	font-weight: normal;
	font-style: normal;
}',
	);

	// Gill Sans
	$fonts['Gill Sans']      = array(
		'type'        => 'web',
		'font-family' => '"Gill-Sans", sans-serif',
		'css'         => '
@font-face {
	font-family: "Gill-Sans";
	src: url("' . $base_url . 'gill-sans/normal.eot");
	src: url("' . $base_url . 'gill-sans/normal.eot?iefix") format("eot"),
		 url("' . $base_url . 'gill-sans/normal.woff") format("woff"),
		 url("' . $base_url . 'gill-sans/normal.ttf") format("truetype"),
		 url("' . $base_url . 'gill-sans/normal.svg#webfontEcPvUcPM") format("svg");
	font-weight: normal;
	font-style: normal;
}',
	);
	$fonts['Gill Sans Bold'] = array(
		'type'        => 'web',
		'font-family' => '"Gill-Sans-Bold", sans-serif',
		'css'         => '
@font-face {
	font-family: "Gill-Sans-Bold";
	src: url("' . $base_url . 'gill-sans/bold.eot");
	src: url("' . $base_url . 'gill-sans/bold.eot?iefix") format("eot"),
		 url("' . $base_url . 'gill-sans/bold.woff") format("woff"),
		 url("' . $base_url . 'gill-sans/bold.ttf") format("truetype"),
		 url("' . $base_url . 'gill-sans/bold.svg#webfontEcPvUcPM") format("svg");
	font-weight: normal;
	font-style: normal;
}',
	);

	// Rockwell
	$fonts['Rockwell']      = array(
		'type'        => 'web',
		'font-family' => '"Rockwell", sans-serif',
		'css'         => '
@font-face {
	font-family: "Rockwell";
	src: url("' . $base_url . 'rockwell/normal.eot");
	src: url("' . $base_url . 'rockwell/normal.eot?iefix") format("eot"),
		 url("' . $base_url . 'rockwell/normal.woff") format("woff"),
		 url("' . $base_url . 'rockwell/normal.ttf") format("truetype"),
		 url("' . $base_url . 'rockwell/normal.svg#webfontEcPvUcPM") format("svg");
	font-weight: normal;
	font-style: normal;
}',
	);
	$fonts['Rockwell Bold'] = array(
		'type'        => 'web',
		'font-family' => '"Rockwell-Bold", sans-serif',
		'css'         => '
@font-face {
	font-family: "Rockwell-Bold";
	src: url("' . $base_url . 'rockwell/bold.eot");
	src: url("' . $base_url . 'rockwell/bold.eot?iefix") format("eot"),
		 url("' . $base_url . 'rockwell/bold.woff") format("woff"),
		 url("' . $base_url . 'rockwell/bold.ttf") format("truetype"),
		 url("' . $base_url . 'rockwell/bold.svg#webfontEcPvUcPM") format("svg");
	font-weight: normal;
	font-style: normal;
}',
	);

	// VAG Rounded
	$fonts['VAG Rounded']      = array(
		'type'        => 'web',
		'font-family' => '"VAG-Rounded", sans-serif',
		'css'         => '
@font-face {
	font-family: "VAG-Rounded";
	src: url("' . $base_url . 'vag-rounded/light.eot");
	src: url("' . $base_url . 'vag-rounded/light.eot?iefix") format("eot"),
		 url("' . $base_url . 'vag-rounded/light.woff") format("woff"),
		 url("' . $base_url . 'vag-rounded/light.ttf") format("truetype"),
		 url("' . $base_url . 'vag-rounded/light.svg#webfontEcPvUcPM") format("svg");
	font-weight: normal;
	font-style: normal;
}',
	);
	$fonts['VAG Rounded Bold'] = array(
		'type'        => 'web',
		'font-family' => '"Vag-Rounded-Bold", sans-serif',
		'css'         => '
@font-face {
	font-family: "Vag-Rounded-Bold";
	src: url("' . $base_url . 'vag-rounded/bold.eot");
	src: url("' . $base_url . 'vag-rounded/bold.eot?iefix") format("eot"),
		 url("' . $base_url . 'vag-rounded/bold.woff") format("woff"),
		 url("' . $base_url . 'vag-rounded/bold.ttf") format("truetype"),
		 url("' . $base_url . 'vag-rounded/bold.svg#webfontEcPvUcPM") format("svg");
	font-weight: normal;
	font-style: normal;
}',
	);

	$fonts['Lucida']            = array(
		'font-family' => '"Lucida Sans", "Lucida Grande", "Lucida Sans Unicode", sans-serif',
		'css'         => '',
	);
	$fonts['Georgia, Palatino'] = array(
		'font-family' => 'Georgia, Palatino, "Palatino Linotype", Times, "Times New Roman", serif',
		'css'         => '',
	);
	/**
	 * Added 'label' param to keep the value saved and the text displayed different
	 * in case when we want to change the output of old fonts but keep the old value
	 *
	 * @since 5.2.1
	 */
	$fonts['Arial, Helvetica']  = array(
		'font-family' => 'Arial, Helvetica, sans-serif',
		'css'         => '',
		'label'       => 'Arial',
	);
	$fonts['Helvetica']  = array(
		'font-family' => 'Helvetica, Arial, sans-serif',
		'css'         => '',
		'label'       => 'Helvetica',
	);

	$fonts = array_reverse( $fonts, true );

	return $fonts;
}

function sl_get_google_fonts()
{
	// Use static variable to cache the return value of this function
	static $fonts = null;
	if ( ! empty( $fonts ) )
		return $fonts;

	$fonts = array(
		'Dosis'                    => array(
			'css'         => '@import url(http://fonts.googleapis.com/css?family=Dosis);',
			'font-family' => '"Dosis", sans-serif',
			'type'        => 'google',
		),
		'Lato'                     => array(
			'css'         => '@import url(http://fonts.googleapis.com/css?family=Lato);',
			'font-family' => '"Lato", sans-serif',
			'type'        => 'google',
		),
		'Arvo'                     => array(
			'css'         => '@import url(http://fonts.googleapis.com/css?family=Arvo);',
			'font-family' => '"Arvo", serif',
			'type'        => 'google',
		),
		'Cabin'                    => array(
			'css'         => '@import url(http://fonts.googleapis.com/css?family=Cabin);',
			'font-family' => '"Cabin", sans-serif',
			'type'        => 'google',
		),
		'Playfair Display'         => array(
			'css'         => '@import url(http://fonts.googleapis.com/css?family=Playfair+Display);',
			'font-family' => '"Playfair Display", serif',
			'type'        => 'google',
		),
		'PT Sans'                  => array(
			'css'         => '@import url(http://fonts.googleapis.com/css?family=PT+Sans);',
			'font-family' => '"PT Sans", sans-serif',
			'type'        => 'google',
		),
		'Poiret One'               => array(
			'css'         => '@import url(http://fonts.googleapis.com/css?family=Poiret+One);',
			'font-family' => '"Poiret One", cursive',
			'type'        => 'google',
		),
		'Cabin Sketch'             => array(
			'css'         => '@import url(http://fonts.googleapis.com/css?family=Cabin+Sketch);',
			'font-family' => '"Cabin Sketch", cursive',
			'type'        => 'google',
		),
		'Belgrano'                 => array(
			'css'         => '@import url(http://fonts.googleapis.com/css?family=Belgrano);',
			'font-family' => '"Belgrano", serif',
			'type'        => 'google',
		),
		'PT Sans Narrow'           => array(
			'css'         => '@import url(http://fonts.googleapis.com/css?family=PT+Sans+Narrow);',
			'font-family' => '"PT Sans Narrow", sans-serif',
			'type'        => 'google',
		),
		'Cabin Condensed'          => array(
			'css'         => '@import url(http://fonts.googleapis.com/css?family=Cabin+Condensed);',
			'font-family' => '"Cabin Condensed", sans-serif',
			'type'        => 'google',
		),
		'Tinos'                    => array(
			'css'         => '@import url(http://fonts.googleapis.com/css?family=Tinos);',
			'font-family' => '"Tinos", serif',
			'type'        => 'google',
		),
		'Sofia'                    => array(
			'css'         => '@import url(http://fonts.googleapis.com/css?family=Sofia);',
			'font-family' => '"Sofia", cursive',
			'type'        => 'google',
		),
		'Oleo Script'              => array(
			'css'         => '@import url(http://fonts.googleapis.com/css?family=Oleo+Script);',
			'font-family' => '"Oleo Script", cursive',
			'type'        => 'google',
		),
		'Wire One'                 => array(
			'css'         => '@import url(http://fonts.googleapis.com/css?family=Wire+One);',
			'font-family' => '"Wire One", sans-serif',
			'type'        => 'google',
		),
		'Josefin Sans'             => array(
			'css'         => '@import url(http://fonts.googleapis.com/css?family=Josefin+Sans);',
			'font-family' => '"Josefin Sans", sans-serif',
			'type'        => 'google',
		),
		'Lobster'                  => array(
			'css'         => '@import url(http://fonts.googleapis.com/css?family=Lobster);',
			'font-family' => '"Lobster", cursive',
			'type'        => 'google',
		),
		'Mate SC'                  => array(
			'css'         => '@import url(http://fonts.googleapis.com/css?family=Mate+SC);',
			'font-family' => '"Mate SC", serif',
			'type'        => 'google',
		),
		'Kreon'                    => array(
			'css'         => '@import url(http://fonts.googleapis.com/css?family=Kreon);',
			'font-family' => '"Kreon", serif',
			'type'        => 'google',
		),
		'Fugaz One'                => array(
			'css'         => '@import url(http://fonts.googleapis.com/css?family=Fugaz+One);',
			'font-family' => '"Fugaz One", cursive',
			'type'        => 'google',
		),
		'Kameron'                  => array(
			'css'         => '@import url(http://fonts.googleapis.com/css?family=Kameron);',
			'font-family' => '"Kameron", serif',
			'type'        => 'google',
		),
		'Josefin Slab'             => array(
			'css'         => '@import url(http://fonts.googleapis.com/css?family=Josefin+Slab);',
			'font-family' => '"Josefin Slab", serif',
			'type'        => 'google',
		),
		'Graduate'                 => array(
			'css'         => '@import url(http://fonts.googleapis.com/css?family=Graduate);',
			'font-family' => '"Graduate", cursive',
			'type'        => 'google',
		),
		'Just Me Again Down Here'  => array(
			'css'         => '@import url(http://fonts.googleapis.com/css?family=Just+Me+Again+Down+Here);',
			'font-family' => '"Just Me Again Down Here", cursive',
			'type'        => 'google',
		),
		'Ubuntu Condensed'         => array(
			'css'         => '@import url(http://fonts.googleapis.com/css?family=Ubuntu+Condensed);',
			'font-family' => '"Ubuntu Condensed", sans-serif',
			'type'        => 'google',
		),
		'Trocchi'                  => array(
			'css'         => '@import url(http://fonts.googleapis.com/css?family=Trocchi);',
			'font-family' => '"Trocchi", serif',
			'type'        => 'google',
		),
		'Oxygen'                   => array(
			'css'         => '@import url(http://fonts.googleapis.com/css?family=Oxygen);',
			'font-family' => '"Oxygen", sans-serif',
			'type'        => 'google',
		),
		'Berkshire Swash'          => array(
			'css'         => '@import url(http://fonts.googleapis.com/css?family=Berkshire+Swash);',
			'font-family' => '"Berkshire Swash", cursive',
			'type'        => 'google',
		),
		'Marmelad'                 => array(
			'css'         => '@import url(http://fonts.googleapis.com/css?family=Marmelad);',
			'font-family' => '"Marmelad", sans-serif',
			'type'        => 'google',
		),
		'Shadows Into Light Two'   => array(
			'css'         => '@import url(http://fonts.googleapis.com/css?family=Shadows+Into+Light+Two);',
			'font-family' => '"Shadows Into Light Two", cursive',
			'type'        => 'google',
		),
		'Six Caps'                 => array(
			'css'         => '@import url(http://fonts.googleapis.com/css?family=Six+Caps);',
			'font-family' => '"Six Caps", sans-serif',
			'type'        => 'google',
		),
		'Yanone Kaffeesatz Normal' => array(
			'css'         => '@import url(http://fonts.googleapis.com/css?family=Yanone+Kaffeesatz:400);',
			'font-family' => '"Yanone Kaffeesatz", sans-serif',
			'type'        => 'google',
		),
		'Abril Fatface'            => array(
			'css'         => '@import url(http://fonts.googleapis.com/css?family=Abril+Fatface);',
			'font-family' => '"Abril Fatface", cursive',
			'type'        => 'google',
		),
		'Open Sans Condensed:700'  => array(
			'css'         => '@import url(http://fonts.googleapis.com/css?family=Open+Sans+Condensed:700);',
			'font-family' => '"Open Sans Condensed", sans-serif',
			'type'        => 'google',
		),
		'Playball'                 => array(
			'css'         => '@import url(http://fonts.googleapis.com/css?family=Playball);',
			'font-family' => '"Playball", cursive',
			'type'        => 'google',
		),
		'Jura'                     => array(
			'css'         => '@import url(http://fonts.googleapis.com/css?family=Jura);',
			'font-family' => '"Jura", sans-serif',
			'type'        => 'google',
		),
	);

	krsort( $fonts );

	return $fonts;
}

/**
 * Get base font URL
 *
 * @return string
 */
function sl_get_font_base_url()
{
	return trailingslashit( 'https://www.australiansolarquotes.com.au/fonts' );
}