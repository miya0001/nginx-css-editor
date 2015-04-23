<?php
/**
 * Plugin Name: Nginx CSS Editor
 * Plugin URI:  https://wpist.me/
 * Description: This is a awesome cool plugin.
 * Version:     0.1.0
 * Author:      Takayuki Miyauchi
 * Author URI:  https://wpist.me/
 * License:     GPLv2
 * Text Domain: nginx_css_editor
 * Domain Path: /languages
 */

/**
 * Copyright (c) 2015 Takayuki Miyauchi ( https://wpist.me/ )
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */


define( 'NGINX_CSS_EDITOR_URL',  plugins_url( '', __FILE__ ) );
define( 'NGINX_CSS_EDITOR_PATH', dirname( __FILE__ ) );

$nginx_css_editor = new Nginx_CSS_Editor();
$nginx_css_editor->register();

class Nginx_CSS_Editor {

private $version = '';
private $langs   = '';

function __construct()
{
    $data = get_file_data(
        __FILE__,
        array( 'ver' => 'Version', 'langs' => 'Domain Path' )
    );
    $this->version = $data['ver'];
    $this->langs   = $data['langs'];
}

public function register()
{
    add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
}

public function plugins_loaded()
{
    // load_plugin_textdomain(
    //     'nginx_css_editor',
    //     false,
    //     dirname( plugin_basename( __FILE__ ) ).$this->langs
    // );

    add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
    add_action( 'admin_menu', array( $this, 'admin_menu' ) );
    add_action( 'admin_init', array( $this, 'admin_init' ) );

    add_action( 'wp_head', array( $this, 'wp_head' ) );
}

public function wp_head()
{
    echo "<style>";
    if ( $this->is_mobile() ) {
        echo get_option( 'nginx-css-editor-sp-style' );
    } else {
        echo get_option( 'nginx-css-editor-pc-style' );
    }
    echo "</style>";
}

public function admin_menu()
{
    // See http://codex.wordpress.org/Administration_Menus
    add_theme_page(
        __( 'Nginx CSS Editor', 'nginx_css_editor' ),
        __( 'Nginx CSS Editor', 'nginx_css_editor' ),
        'switch_themes', // http://codex.wordpress.org/Roles_and_Capabilities
        'nginx_css_editor',
        array( $this, 'options_page' )
    );
}

public function admin_init()
{
    if ( current_user_can( 'switch_themes' ) ) {
        if ( isset( $_POST['_wpnonce_nginx_css_editor'] ) && $_POST['_wpnonce_nginx_css_editor'] ){
            if ( check_admin_referer( 'nig5mycgoz2gldiign9qdue443ivn29', '_wpnonce_nginx_css_editor' ) ){

                if ( isset( $_POST['nginx-css-editor-pc-style'] ) ) {
                    update_option( 'nginx-css-editor-pc-style', trim( $_POST['nginx-css-editor-pc-style'] ) );
                }

                if ( isset( $_POST['nginx-css-editor-sp-style'] ) ) {
                    update_option( 'nginx-css-editor-sp-style', trim( $_POST['nginx-css-editor-sp-style'] ) );
                }

                wp_safe_redirect( menu_page_url( 'nginx_css_editor', false ) );
            }
        }
    }
}

public function options_page()
{
?>
<div id="nginx-css-editor" class="wrap">
<h2><?php _e( 'Nginx CSS Editor', 'nginx_css_editor' ); ?></h2>

<form method="post" action="<?php echo esc_attr( $_SERVER['REQUEST_URI'] ); ?>">
<?php wp_nonce_field( 'nig5mycgoz2gldiign9qdue443ivn29', '_wpnonce_nginx_css_editor' ); ?>

<h3>PC Style</h3>

<textarea name="nginx-css-editor-pc-style" class="style-editor"><?php echo esc_textarea( get_option( 'nginx-css-editor-pc-style' ) ); ?></textarea>

<h3>SP Style</h3>

<textarea name="nginx-css-editor-sp-style" class="style-editor"><?php echo esc_textarea( get_option( 'nginx-css-editor-sp-style' ) ); ?></textarea>

<p style="margin-top: 3em;">
    <input type="submit" name="submit" id="submit" class="button button-primary"
            value="<?php _e( "Save Changes", "nginx_css_editor" ); ?>"></p>
</form>
</div><!-- #nginx_css_editor -->
<?php
}

public function admin_enqueue_scripts( $hook )
{
    if ( 'appearance_page_nginx_css_editor' === $hook ) {
        wp_enqueue_style(
            'admin-nginx_css_editor-style',
            plugins_url( 'css/admin-nginx-css-editor.min.css', __FILE__ ),
            array(),
            $this->version,
            'all'
        );

        wp_enqueue_script(
            'admin-nginx_css_editor-script',
            plugins_url( 'js/admin-nginx-css-editor.min.js', __FILE__ ),
            array( 'jquery' ),
            $this->version,
            true
        );
    }
}

public function wp_enqueue_scripts()
{
    wp_enqueue_style(
        'nginx-css-editor-style',
        plugins_url( 'css/nginx-css-editor.min.css', __FILE__ ),
        array(),
        $this->version,
        'all'
    );

    wp_enqueue_script(
        'nginx-css-editor-script',
        plugins_url( 'js/nginx-css-editor.min.js', __FILE__ ),
        array( 'jquery' ),
        $this->version,
        true
    );
}

public function is_mobile()
{
    if ( in_array( $this->mobile_detect(), array( '@smartphone', '@ktai' ) ) ) {
        return true;
    } else {
        return false;
    }
}

public function mobile_detect()
{
    $mobile_detect = '';
    if ( isset( $_SERVER['HTTP_X_UA_DETECT'] ) && $_SERVER['HTTP_X_UA_DETECT'] ) {
        $mobile_detect = $_SERVER['HTTP_X_UA_DETECT'];
    }

    return apply_filters( "nginxmobile_mobile_detect", $mobile_detect );
}

} // end class Nginx_CSS_Editor

// EOF
