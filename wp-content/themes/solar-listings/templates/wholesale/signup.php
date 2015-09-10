<?php
if ( is_user_logged_in() )
{
    $company = get_posts( array(
        'post_type'      => 'wholesale',
        'post_status'    => 'any',
        'posts_per_page' => 1,
        'meta_key'       => 'user',
        'meta_value'     => get_current_user_id(),
    ) );

    if ( empty( $company ) )
    {
        echo '<div class="alert">' . __( 'You do not own a Wholesale company, please list your Wholesale company now!.',
                '7listings' ) . '</div>';
    }
    else
    {
        printf( '<div class="alert alert-error">' . __( 'You already own a wholesale company, consider <a href="%s">edit wholesale details here</a>.', '7listings' ) . '</div>', get_permalink( sl_setting( 'company_page_edit' ) ) );

        return;
    }
}
?>

<form action="" method="post" enctype="multipart/form-data" class="company-form signup" id="wholesale-signup-form">

<div id="ajax-message"></div>

<?php if ( ! is_user_logged_in() ) : ?>

    <h2><?php _e( 'User Details', '7listings' ); ?></h2>
    <div class="user-details">
        <div class="row-fluid">
            <div class="span6">
                <label><?php _e( 'Username', '7listings' ); ?> <span class="required">*</span></label>
                <input type="text" name="username" required class="span12">
            </div>
            <div class="span6">
                <label><?php _e( 'Password', '7listings' ); ?> <span class="required">*</span></label>
                <input type="password" name="password" required class="span12">
            </div>
        </div>

        <div class="row-fluid">
            <div class="span6">
                <label><?php _e( 'Name', '7listings' ); ?> <span class="required">*</span></label>
                <input required type="text" name="first_name" class="span12">
                <span class="help-block"><?php _e( 'First', '7listings' ); ?></span>
            </div>
            <div class="span6">
                <label>&nbsp;</label> <input required type="text" name="last_name" class="span12">
                <span class="help-block"><?php _e( 'Last', '7listings' ); ?></span>
            </div>
        </div>

        <div class="row-fluid">
            <div class="span6">
                <label><?php _e( 'Email', '7listings' ); ?> <span class="required">*</span></label>
                <input type="email" name="user_email" required class="span12">
                <span class="help-block"><?php _e( 'Enter Email', '7listings' ); ?></span>
            </div>
            <div class="span6">
                <label>&nbsp;</label> <input type="email" name="user_email_confirm" required class="span12">
                <span class="help-block"><?php _e( 'Confirm Email', '7listings' ); ?></span>
            </div>
        </div>
    </div>

<?php endif; ?>

<h2><?php _e( 'Company Details', '7listings' ); ?></h2>

<div class="row-fluid">
    <div class="span9">
        <label><?php _e( 'Company Trading Name', '7listings' ); ?> <span class="required">*</span></label>
        <input type="text" name="post_title" required class="span8 title">
        <br>
        <label class="description-label"><?php _e( 'Description', '7listings' ); ?>
            <span class="required">*</span></label>
        <textarea name="post_content" class="span12 description-input"></textarea>
    </div>

    <div class="span3">
        <label><?php _e( 'Logo', '7listings' ); ?></label>
        <input type="file" name="<?php echo sl_meta_key( 'logo', 'company' ); ?>" onchange="preview();" id="logo-upload">
        <img id="logo-preview" class="photo" src="">
        <script>
            function preview()
            {
                var reader = new FileReader();
                reader.readAsDataURL( document.getElementById( 'logo-upload' ).files[0] );
                reader.onload = function ( e )
                {
                    document.getElementById( 'logo-preview' ).src = e.target.result;
                };
            }
        </script>
    </div>
</div>

<br>

<div class="row-fluid">

    <div class="span4 contact-inputs">
        <div class="control-group">
            <label class="control-label"><?php _e( 'Website', '7listings' ); ?></label>
            <div class="controls">
                <div class="input-prepend">
                    <span class="add-on"><i class="icon-globe"></i></span>
                    <input type="text" name="website" class="span12 no-highlight">
                </div>
            </div>
        </div>
    </div><!-- .span4 -->

    <div class="span4">
        <div class="control-group">
            <label class="control-label"><?php _e( 'Company Email', '7listings' ); ?></label>
            <div class="controls">
                <div class="input-prepend">
                    <span class="add-on"><i class="icon-envelope-alt"></i></span>
                    <input type="email" name="email" class="span12 no-highlight">
                </div>
            </div>
        </div>
    </div>

    <div class="span4">
        <div class="control-group">
            <label class="control-label"><?php _e( 'Phone', '7listings' ); ?></label>
            <div class="controls">
                <div class="input-prepend">
                    <span class="add-on"><i class="icon-phone"></i></span>
                    <input type="text" name="phone" class="span12 no-highlight">
                </div>
            </div>
        </div>
    </div>
</div><!-- .row-fluid -->

<?php do_action( 'wholesale_signup_form_after' ); ?>

<div class="submit">
    <input type="submit" name="submit" class="button booking large" value="<?php _e( 'Sign Up', '7listings' ); ?>">
</div>
</form>
