<?php

// Because GF use 'wp' hook to retrieve data and it doesn't work on admin, so we'll do a little hack

if( ! empty ( $_POST['gform_submit'] ) && $_POST["gform_submit"] == 36 )
{
	$_POST['input_13'] = date( 'd/m/Y' );

	RGForms::maybe_process_form();
	
	$query_url = add_query_arg( 'process', 'true' );

	header( "location: {$query_url}" );
}

if( ! empty ( $_GET['process'] ) )
	add_action( 'admin_notices', 'solar_gf_rejected_notice' );

function solar_gf_rejected_notice()
{
    ?>
    <div class="updated">
        <p><?php _e( 'Rejection was done!', '7listings' ); ?></p>
    </div>
    <?php
}


function solar_fix_gf_field_markup( $field_content, $field, $value, $zero, $form_id )
{
	$event = solar_gf_get_logic_event( $field );

	$field_content = str_replace( 'select ', 'select ' . $event, $field_content );
	$field_content = str_replace( 'input ', 'input ' . $event, $field_content );

	return $field_content;
}

function solar_gf_get_logic_event( $field )
{

	$event_match = array(
		'text' 		=> 'keyup',
		'textarea' 	=> 'keyup',
		'select'	=> 'change',
		'button'	=> 'click',
		'submit'	=> 'click'
	);

	$event = isset( $event_match[$field['type']] ) ? $event_match[$field['type']] : null;
	
    switch ( $event )
    {
        case "keyup" :
            return "onchange='gf_apply_rules(" . $field["formId"] . "," . GFCommon::json_encode($field["conditionalLogicFields"]) . ");' onkeyup='clearTimeout(__gf_timeout_handle); __gf_timeout_handle = setTimeout(\"gf_apply_rules(" . $field["formId"] . "," . GFCommon::json_encode($field["conditionalLogicFields"]) . ")\", 300);'";
        break;

        case "click" :
            return "onclick='gf_apply_rules(" . $field["formId"] . "," . GFCommon::json_encode($field["conditionalLogicFields"]) . ");'";
        break;

        case "change" :
            return "onchange='gf_apply_rules(" . $field["formId"] . "," . GFCommon::json_encode($field["conditionalLogicFields"]) . ");'";
        break;

        default:
        	return '';
    }
}

function solar_fix_gf_container_markup( $field_container, $field, $form, $css_class, $style, $field_content )
{
	$field_id = "field_" . $form["id"] . "_" . $field['id'];
	$field_container = "<li id='{$field_id}' class='$css_class' $style>{FIELD_CONTENT}</li>";
	return $field_container;
}