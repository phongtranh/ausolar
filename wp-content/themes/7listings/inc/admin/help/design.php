<h3>Tools</h3>

<p><a href="https://kuler.adobe.com/" target="_blank">Adobe Kuler</a> - tool for creating colour palettes.</p>


<br>

<h3>Custom CSS & .less</h3>
<p class="input-hint"><?php _e( 'You can press <code>ctrl + space</code> to activate autocompletion', '7listings' ); ?></p>

<p class="input-hint"><?php _e( 'You can use LESS with <code>@import</code> to import LESS file.', '7listings' ); ?></p>

<h4><?php _e( 'Available variables', '7listings' ); ?></h4>

<p class="input-hint">
	<code>@themeDir:</code> <?php _e( 'Path to parent theme directory (with trailing slash)', '7listings' ); ?>
</p>

<p class="input-hint">
	<code>@childDir:</code> <?php _e( 'Path to child theme directory (with trailing slash)', '7listings' ); ?>
</p>

<p class="input-hint">
	<code>@themeUrl:</code> <?php _e( 'URL to parent theme directory (with trailing slash)', '7listings' ); ?>
</p>

<p class="input-hint">
	<code>@childUrl:</code> <?php _e( 'URL to child theme directory (with trailing slash)', '7listings' ); ?>
</p>

<p class="input-hint">
	<code>@imagePath:</code> <?php _e( 'URL to parent theme\'s images directory (with trailing slash)', '7listings' ); ?>
</p>

<h5><?php _e( 'Examples', '7listings' ); ?></h5>

<p class="input-hint"><code>@import '@{childDir}css/my-child-theme-css.less';</code></p>

<p class="input-hint"><code>#featured { background: url(@{childUrl}images/bg.png); }</code></p>

<br>

<h4><?php _e( 'Extra plugin support', '7listings' ); ?></h4>

<p class="input-hint">
	<code>@import '@{themeDir}css/less/components/plugins/gravityforms.less';</code> <?php _e( 'Gravityforms', '7listings' ); ?>
</p>

<p class="input-hint">
	<code>@import '@{themeDir}css/less/components/plugins/datepicker/style.less';</code> <?php _e( 'jQuery date & timepicker', '7listings' ); ?>
</p>

<p class="input-hint">
	<code>@import '@{themeDir}css/less/components/plugins/woo-testimonials.less';</code> <?php _e( 'WOO Testimonials', '7listings' ); ?>
</p>

<br>

<h4><?php _e( '.less Reference', '7listings' ); ?></h4>

<p class="input-hint">
	<a href="http://lesscss.org/features/" target="_blank"><?php _e( 'See official .less website', '7listings' ); ?></a>
</p>