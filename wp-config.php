<?php
// SERVERS USE LOAD BALANCER. DO NOT REMOVE THESE LINES
$_SERVER['HTTPS'] = 'on';

define( 'DISALLOW_FILE_EDIT', true );

/** Enable W3 Total Cache */
define('WP_CACHE', true); // Added by W3 Total Cache

/** Enable W3 Total Cache Edge Mode */
define('W3TC_EDGE_MODE', true); // Added by W3 Total Cache

/** Enable W3 Total Cache */
 //Added by WP-Cache Manager

// Optimization
define( 'WP_POST_REVISIONS', false );
define( 'WP_MAX_MEMORY_LIMIT', '512M' );
define( 'WP_MEMORY_LIMIT', '512M' );
define( 'EMPTY_TRASH_DAYS', 0 );
# define( 'WP_ALLOW_REPAIR', true ); // Disable it for security reason

define( 'POSTCODE_DB_USER', '625132_postcode' );
define( 'POSTCODE_DB_PASSWORD', 'WIV6vWS581Wj' );
define( 'POSTCODE_DB_NAME', '625132_postcode' );
define( 'POSTCODE_DB_HOST', 'mysql51-054.wc2.dfw1.stabletransit.com' );

define( 'DB_NAME', '625132_7solar' );
define( 'DB_USER', 'root' );
define( 'DB_PASSWORD', '' );
define( 'DB_HOST', 'localhost' );
// define( 'DB_HOST', 'mysql51-066.wc2.dfw1.stabletransit.com' );
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');
$table_prefix = 'asq_';

define('AUTH_KEY',         'G1`S2.~d8_8._}-hl5Z?g,o8n7!G$l0;VMC`de|+:a-CT>+J#6J?6P~,3j3&Un?!');
define('SECURE_AUTH_KEY',  '^|VwlJ W._o5dpinkn:/%ZVcxBaQ&(fk&JJZE5hN3$4|;[m!-wdODsi?wSUy6J:G');
define('LOGGED_IN_KEY',    'D7F`S(sl)A1^Jf$(rsjGu2LQBH ;C|-Xq_6#BOxLT-GLyxa3|3+sV7D]d1`lG>o8');
define('NONCE_KEY',        'NyPZA03iO;&Z$XS#aS;-|&N/3hZ-cf!l<<ed)}Uk~s$}B1dmMFkmY!ydA,+RJEV=');
define('AUTH_SALT',        '%,0UnV|+g%mu:>Z:u>dZP-CfR^n46nKS9z& 4L).^ig@g=MLzq~A(X%4K4v6K[JC');
define('SECURE_AUTH_SALT', '7FG]F-tZ)k@>bR5z;Gu|=9p9{fZ!Uze)H.ye,l8aA63[VAvLo?3e%-Ac@@S?R9nG');
define('LOGGED_IN_SALT',   'oD-*0Ya[KUO21vF1!cYAq9[325m+1b45=B+>Tbx`k]VzJhKw24vqEn^)5+tNsm V');
define('NONCE_SALT',       '9@VkFIYC4e1i7K^VB4|rg+B&: APOh!]k-z^;lFN~cLX}[1@k|lpJ6A$!2dXQ@nd');


define('WPLANG', '');
define('WP_DEBUG', isset($_GET['db']));

if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');
require_once(ABSPATH . 'wp-settings.php');
