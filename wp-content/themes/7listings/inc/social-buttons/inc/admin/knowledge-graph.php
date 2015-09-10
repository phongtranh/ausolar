<?php

/**
 * Create settings page in 7listings menu and hold all settings for knowledge graph
 */
class Sl_Knowledge_Graph extends Sl_Settings_Page
{
	/**
	 * Display page settings for knowledge graph
	 *
	 * @return void
	 */
	public function page_content()
	{
		include SSB_DIR . 'inc/admin/knowledge-settings.php';
	}

	/**
	 * Enqueue scripts and styles for this page settings
	 *
	 * @return void
	 */
	public function enqueue()
	{
		wp_enqueue_media();
		wp_enqueue_style( 'select2' );
		wp_enqueue_script( 'sl-knowledge-graph', sl_locate_url( 'js/admin/knowledge-graph.js' ), array( 'sl-choose-image', 'select2' ), '', true );
	}
}
