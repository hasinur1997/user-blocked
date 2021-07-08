<?php 
/**
* Plugin Name:       User Blocked
* Plugin URI:        https://example.com/plugins/the-basics/
* Description:       Handle the basics with this plugin.
* Version:           1.10.3
* Requires at least: 5.2
* Requires PHP:      7.2
* Author:            Hasinur Rahman
* Author URI:        https://author.example.com/
* License:           GPL v2 or later
* License URI:       https://www.gnu.org/licenses/gpl-2.0.html
* Text Domain:       user-blocked
* Domain Path:       /languages
*/

/**
 * Class User Blocked 
 */
class User_Blocked {
    /**
     * Initialize
     */
    public function __construct() {
        register_activation_hook( __FILE__, [ $this, 'activate' ] );

        $this->init_hooks();
        
    }

    /**
     * Get instance for the class
     *
     * @return object | bool
     */
    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * User role create when activating the plugin
     *
     * @return void
     */
    public function activate() {
        add_role( 'blocked', __( 'Blocked', 'user-blocked' ), ['blocked' => true] );
    }

    /**
     * Init all required hooks
     *
     * @return void
     */
    public function init_hooks() {
        add_action( 'init', [ $this, 'set_up'] );

        add_filter('query_vars', [ $this, 'set_query_vars'] );

        add_action( 'template_redirect', [ $this, 'redirect_user'] );
    }

    /**
     * Setup initial things
     *
     * @return void
     */
    public function set_up() {
        add_rewrite_rule('blocked?$', 'index.php?blocked=1', 'top');

        $this->handle_user();
    }

    /**
     * Return the user in blocked url if user is blocked role
     *
     * @return void
     */
    public function handle_user() {
        if ( is_admin() && current_user_can('blocked') ) {
            wp_redirect(get_home_url() . '/blocked');
            die();
        }
    }

    /**
     * Redirect user if user is blocked
     *
     * @return void
     */
    public function redirect_user() {
        $is_blocked = intval(get_query_var('blocked'));

        if ( $is_blocked ) {
            ?>
                <h2><?php echo esc_html__( 'You are blocked !', 'user-blocked' ) ?></h2>
            <?php
            die();
        }
    }

    /**
     * Set query var for the custom url
     *
     * @param [type] $query_vars
     * @return void
     */
    public function set_query_vars($query_vars) {
        $query_vars[] = 'blocked';
    
        return  $query_vars;
    }
}

// Kick Off the plugin
User_Blocked::init();