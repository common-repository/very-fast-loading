<?php
/**
 * Plugin Name: Very Fast Loading Free
 * Plugin URI: http://www.cartaddon.com/products/Fast-Loading-Wordpress.html
 * Description: This plugin will make your wordpress website loads faster. The only and true lightweight plugin that will minify your html codes and inline javascript and css codes. All pages are affected with this amazing plugin, either on your frontend or backend areas. Once activated, your html codes and inline js and css codes are in 1 line of codes only.
 * Version: 1.1.0
 * Author: CartAddon
 * Author URI: http://www.cartaddon.com
 * License: GPL2
 */


add_action('admin_menu', 'fastloadingmenu');

function fastloadingmenu() {
	add_menu_page('Fast Loading', 'Fast Loading', 'administrator', 'fastloadingsettings', 'fastloadingsettingspage', 'dashicons-admin-generic');
	wp_enqueue_style( 'fastloadingbootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css' );	
	wp_enqueue_style( 'fastloadingfontawesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css' );
	wp_enqueue_script( 'fastloadingbootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js' );
}

function fastloadingsettingspage() {
  // 
  ?>


<form method="post" action="options.php">
    
    <h1>Thank you for using "Fast Loading Plugin" by <a href="http://www.cartaddon.com/categories/Wordpress/" title="cartaddon wordpress plugins">CartAddon.com</a></h1>
    <p>To donate, please click <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=F33HE9J3WVC5Y" target="_blank" title="Donate Fast Loading Development">Donate Fast Loading Development</a></p>
    <p>Support link: <a href="http://www.cartaddon.com/forums/forumdisplay.php?fid=2" target="_blank" title="Fast Loading Wordpress Support">http://www.cartaddon.com/forums/forumdisplay.php?fid=2</a></p>
    <p>Please rate: <a href="https://wordpress.org/support/view/plugin-reviews/very-fast-loading" target="_blank" title="Please rate fast loading plugin">https://wordpress.org/support/view/plugin-reviews/very-fast-loading</a></p>
    <div class="col-xs-12"> 
    <div class="row">
        <div class="col-xs-12 col-sm-3">
            <div class="panel panel-success">
                <div class="panel-heading">
                    <h3 class="panel-title">Free</h3>
                </div>
                <div class="panel-body">
                    
                        <ul>
                            <li><i class="fa fa-check fa-1x text-success"></i> Minified HTML</li>
                            <li><i class="fa fa-check fa-1x text-success"></i> Minified inline CSS</li>
                            <li><i class="fa fa-check fa-1x text-success"></i> Minified in JS</li>
                            <li><i class="fa fa-times fa-1x text-danger"></i> Cache</li>
                        </ul>
                        
                    
                </div>
            </div>
        </div>
        
        <div class="col-xs-12 col-sm-3">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">Premium ( Extremely Fast )</h3>
                </div>
                <div class="panel-body">
                    
                        <ul>
                            <li><i class="fa fa-check fa-1x text-success"></i> Minified HTML</li>
                            <li><i class="fa fa-check fa-1x text-success"></i> Minified inline CSS</li>
                            <li><i class="fa fa-check fa-1x text-success"></i> Minified in JS</li>
                            <li><i class="fa fa-check fa-1x text-success"></i> Cache ( TWIG )</li>
                        </ul>
                        
                    
                </div>
            </div>
        </div>
    
    </div>
    
    <div class="row">
    	<h4>You can upgrade to premium for only USD $10</h4>
        <p>Upgrading to premium edition will reduce database calls.</p>
        <p>E.g. you have 100,000 visitors, this plugin will send query to database only 1 time, and use the cache to serve other 99,000 visitors in number of seconds/minutes/hours/days/months before send another query to the database.</p>
        <p>Basically it reduce database calls at 1 time only regardless of how many visitors you have. </p>
        <a class="btn btn-lg btn-success" href="http://www.cartaddon.com/products/Fast-Loading-Wordpress-with-Cache.html" role="button" target="_blank">Upgrade Now</a>
        
    </div>
    </div>
    

</form>
</div>
  <?php
}
add_action( 'admin_init', 'fastloadingsettings1' );

function fastloadingsettings1() {
	register_setting( 'my-plugin-settings-group', 'accountant_name' );
	register_setting( 'my-plugin-settings-group', 'accountant_phone' );
	register_setting( 'my-plugin-settings-group', 'accountant_email' );
}

function veryfastloading_ob($buffer){
	$initial = strlen($buffer);
	$min = plugin_dir_path( __FILE__ ) . 'lib/min'; #minify 2.1.7	
	ini_set('include_path', ini_get('include_path').":$min/lib");	
	require_once($min."/lib/Minify/Loader.php");
	require_once($min."/lib/Minify/HTML.php");
	require_once($min."/lib/JSMin.php");
	require_once($min."/lib/Minify/CSS.php");
	Minify_Loader::register();	
	$buffer = Minify_HTML::minify($buffer, array('htmlMinifier' => array('Minify_HTML', 'minify'), 
												 'jsMinifier' => array('JSMin', 'minify'),
												 'cssMinifier' => array('Minify_CSS', 'minify'),
												 ));	
	$search = array('/\>[^\S ]+/s', '/[^\S ]+\</s', '~>\s+<~',       
	   				 #end default minification 
					'~//<!\[CDATA\[\s*|\s*//\]\]>~',
					);
    $replace2 = array('>', '<', '><',
						#end default minification
						' ', 
						);
 	$buffer = preg_replace($search, $replace2, $buffer);
 	$findarr = array( '/(\s)+/s', '/\r|\n/');
 	$reparr = array( '\\1', ' ',);	 
 	$buffer = trim(preg_replace($findarr, $reparr, $buffer));	
	
	$final = strlen($buffer);
	$savings = round((($initial-$final)/$initial*100), 3);
	$pluginfooter = '<!-- This site runs Very Fast Loading plugin v1.1.0 Total size saved: ' . $savings . '% | Size before minify: ' . $initial . ' bytes | Size after minified: ' . $final . ' bytes. Please visit http://www.cartaddon.com/products/Fast-Loading-Wordpress.html -->';
	$buffer = str_replace ('</body>', $pluginfooter.'</body>', $buffer);
	return $buffer;
}
ob_start("veryfastloading_ob");



?>