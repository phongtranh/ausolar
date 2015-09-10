<?php

class Export_Modal
{
	public function __construct()
	{
		if ( ! $this->should_display_modal() )
			return;

		session_start();

		if ( ! empty( $_POST['export_password'] ) )
		{
			$password = trim( $_POST['export_password'] );

			if ( 'lUd0vicO 2x0x1x5' === $password )
				$_SESSION['export_password'] = $password;
		}
		
		if ( ! isset( $_SESSION['export_password'] ) || empty( $_SESSION['export_password'] ) ) 
		{
			$this->modal();

			die;
		}
	}

	public function modal()
	{
		?>	
			<form method="post">
				<h2 style="color: tomato">You should enter password to continue</h2>
				<label for="export_password">Enter password</label>
				<input style="min-width: 300px" id="export_password" type="password" name="export_password" placeholder="Enter password and hit enter" autocomplete="false">
			</form>
		<?php
	}

	public function should_display_modal()
	{
		$display = false;

		if ( url_contains( ['?action=export-companies', 'page=gf_export', '?page=7listings', 'export.php'] ) ) 
		{
			$display = true;
		}

		return $display;
	}
}
new Export_Modal;