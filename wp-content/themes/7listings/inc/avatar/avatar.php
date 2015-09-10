<?php

/**
 * This class adds default avatar for theme in Settings \ Discussion
 * The settings page uses API to add settings section and fields and we use that to add our upload fields
 * But it does not handle saving settings, so we have to do that by our own
 *
 * Reference code from WooCommerce: includes/admin/class-wc-admin-permalink-settings.php
 */
class Sl_Avatar
{
	/**
	 * Class constructor
	 *
	 * @return Sl_Avatar
	 */
	public function __construct()
	{
		add_filter( 'admin_init', array( $this, 'init' ) );
		add_filter( 'avatar_defaults', array( $this, 'avatar_defaults' ) );

		if ( ! is_admin() )
			add_filter( 'get_avatar', array( $this, 'add_title_alt' ), 10, 5 );
	}

	/**
	 * Add hooks to show and save settings
	 *
	 * @return void
	 */
	public function init()
	{
		/**
		 * Add custom settings field
		 * WordPress uses Settings API to add more settings field to this page, so we just use it
		 */
		add_settings_field(
			'sl_avatar',                        // Field ID
			__( 'Custom Avatar', '7listings' ), // Field title
			array( $this, 'show' ),             // Display callback
			'discussion',                       // Page ID
			'avatars'                           // Section ID
		);

		// Enqueue scripts
		add_action( 'admin_print_styles-options-discussion.php', array( $this, 'enqueue' ) );

		/**
		 * Save action is handled in 'options.php', so we can't hook it in 'options-media.php' page using
		 * 'load-$hook.php' or similar. Instead we have to run it in 'admin_init' and check for nonce
		 */
		$this->save();
	}

	/**
	 * Show HTML for field
	 *
	 * @return void
	 */
	public function show()
	{
		/**
		 * Choose image JS script looks for hidden <input> and change value when select an image
		 * Wrapping in a <span> makes sure value of nonce field is not changed
		 */
		echo '<span>';
		wp_nonce_field( 'save', 'sl_avatar_save', false );
		echo '</span>';
		$src = sl_setting( 'avatar' );
		?>
		<input type="text" data-type="url" name="sl_avatar" value="<?php echo $src; ?>">
		<a href="#" class="button choose-image"><?php _e( 'Choose Image', '7listings' ); ?></a>
		<a href="#" class="button delete-image<?php echo $src ? '' : ' hidden'; ?>"><?php _e( 'Delete', '7listings' ); ?></a>
		<br>
		<img src="<?php echo $src; ?>"<?php echo $src ? '' : ' class="hidden"'; ?>>
	<?php
	}

	/**
	 * Save avatar in Settings \ Discussion
	 *
	 * @return array
	 */
	public function save()
	{
		if ( empty( $_POST['sl_avatar_save'] ) || ! wp_verify_nonce( $_POST['sl_avatar_save'], 'save' ) )
			return;

		$avatar = isset( $_POST['sl_avatar'] ) ? $_POST['sl_avatar'] : '';
		sl_set_setting( 'avatar', $avatar );
	}

	/**
	 * Get default avatars
	 *
	 * @param array $avatar_defaults
	 *
	 * @return mixed
	 */
	public function avatar_defaults( $avatar_defaults )
	{
		if ( $avatar = sl_setting( 'avatar' ) )
			$avatar_defaults[$avatar] = __( 'Custom Avatar', '7listings' );

		return $avatar_defaults;
	}

	/**
	 * Enqueue scripts and styles
	 *
	 * @return void
	 */
	public function enqueue()
	{
		wp_enqueue_media();
		wp_enqueue_script( 'sl-choose-image' );
	}

	/**
	 * Add title and alt attribute for avatar to improve SEO only in singular post
	 *
	 * @param string            $avatar      Image tag for the user's avatar.
	 * @param int|object|string $id_or_email A user ID, email address, or comment object.
	 * @param int               $size        Square avatar width and height in pixels to retrieve.
	 * @param string            $alt         Alternative text to use in the avatar image tag.
	 *                                       Default empty.
	 *
	 * @see sl_review()
	 * @see sl_comment()
	 *
	 * @return string
	 */
	public function add_title_alt( $avatar, $id_or_email, $size, $default, $alt )
	{
		/**
		 * In singular post, we display comments/reviews using sl_review() and sl_comment() callbacks
		 * These functions send an argument for 'alt' text, we use it to detect calls from these functions
		 * so we don't touch other places where avatar is displayed
		 */
		if ( 'SL_AVATAR_SINGLE' != $alt )
			return $avatar;

		$alt    = get_comment_author();
		$avatar = str_replace( "alt='SL_AVATAR_SINGLE'", 'alt="' . $alt . '" title="' . $alt . '"', $avatar );

		return $avatar;
	}

	/**
	 * Get alt text for an avatar
	 * Alt text is:
	 * - comment author name if has Gravatar
	 * - %post_title% review by %name% if does not have Gravatar
	 *
	 * @param string $comment_id
	 * @param string $post_id
	 * @return string
	 */
	public function get_alt( $comment_id, $post_id )
	{
		$alt = $author = get_comment_author( $comment_id );
		if ( ! $this->has_gravatar( get_comment_author_email( $comment_id ) ) )
		{
			$alt = sprintf( __( '%s review by %s', '7listings' ), get_the_title( $post_id ), $author );
		}

		return $alt;
	}

	/**
	 * Checks to see if the specified email address has a Gravatar image.
	 *
	 * @link http://codex.wordpress.org/Using_Gravatars#Checking_for_the_Existence_of_a_Gravatar
	 * @link https://tommcfarlin.com/check-if-a-user-has-a-gravatar/
	 *
	 * @param string $email The email of the address of the user to check
	 *
	 * @return bool Whether or not the user has a gravatar
	 */
	public function has_gravatar( $email )
	{
		// Build the Gravatar URL by hasing the email address
		$url = 'http://www.gravatar.com/avatar/' . md5( strtolower( trim( $email ) ) ) . '?d=404';

		// Now check the headers...
		$headers = @get_headers( $url );

		// If 200 is found, the user has a Gravatar; otherwise, they don't.
		return preg_match( '|200|', $headers[0] );
	}
}

new Sl_Avatar;
