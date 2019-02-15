<?php
/**
 * Plugin Name: Sitewide Notice TWDH
 * Description: Adds a message bar and animated button to your webpage
 * Plugin URI: https://www.thewebdesignhub.com
 * Version: 1.0.1
 * Author: The Web Design Hub
 * Author URI: http://www.thewebdesignhub.com
 * License: GPL2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: sitewide-notice-twdh
 *
 * Sitewide Notice TWDH is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Sitewide Notice TWDH is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Sitewide Notice TWDH. If not, see http://www.gnu.org/licenses/gpl.html
 *
**/

defined( 'ABSPATH' ) or exit;

/**
 * INCLUDES
 */
include 'sitewide-notice-twdh-settings.php'; //all admin code can be found in here.

class SiteWide_Notice_TWDH {

    /** Refers to a single instance of this class. */
    private static $instance = null;

    /**
    * Creates or returns an instance of this class.
    *
    * @return  Sitewide_Notice_TWDH A single instance of this class.
    */
    public static function get_instance() {
        if ( null == self::$instance ) {
            self::$instance = new self;
            self::$instance->hooks();
        }
        return self::$instance;
    } // end get_instance;

    /**
    * Initializes the plugin by setting localization, filters, and administration functions.
    */
    private function __construct() {

    } //end of construct


    private static function hooks() {
        global $pagenow;

        //run this code regardless if the actual banner is activated or not.
        add_action( 'init', array( 'SiteWide_Notice_TWDH', 'init' ) );

        $swnza_options = get_option( 'swnza_options' );

        if( $swnza_options['active'] && !is_admin() && ( $pagenow !== 'wp-login.php' ) ) {
            add_action( 'wp_footer', array( 'SiteWide_Notice_TWDH', 'display_sitewide_notice_banner' ) );
            add_action( 'wp_enqueue_scripts', array( 'SiteWide_Notice_TWDH', 'enqueue_scripts' ) );
        }
    }

    public static function init() {

        if( isset( $_REQUEST['remove_swnza_settings'] ) || !empty( $_REQUEST['remove_swnza_settings'] ) ) {
            delete_option( 'swnza_options' );
        }
    }

    public static function enqueue_scripts() {
        wp_enqueue_style( 'swnza_css', plugins_url( '/css/swnza.css', __FILE__ ) );
        wp_enqueue_script( 'swnza_css', plugins_url( '/js/jquery_cookie.js', __FILE__ ), array( 'jquery' ), '2.1.4', true );
    }

    public static function display_sitewide_notice_banner() {
       $swnza_options = get_option( 'swnza_options' );

        if( ( isset( $swnza_options[ 'hide_for_logged_in' ] ) && ! empty( $swnza_options['hide_for_logged_in'] ) ) && is_user_logged_in() ) {
            return;
        }

        if( $swnza_options[ 'active' ] ) {

            // If show for PMPro members setting is enabled and user doesn't have membership level, return.
            if( isset( $swnza_options['show_for_members'] ) && ! empty( $swnza_options['show_for_members'] ) && !pmpro_hasMembershipLevel() ) {
                return;
            } ?>

            <!-- SiteWide Notice WP Cookies -->
            <script type="text/javascript">
            jQuery(document).ready(function($){

                if( Cookies.get('swnza_hide_banner_cookie') != undefined ) {
                    $('.swnza_banner').hide();
                }

                $('#swnza_close_button_link').click(function(){
                  Cookies.set('swnza_hide_banner_cookie', 1, { expires: 1, path: '/' }); //expire the cookie after 24 hours.

                  $('.swnza_banner').hide();
                });
            });
            </script>

            <!-- SiteWide Notice WP Custom CSS -->
                <style type="text/css">
                    .swnza_banner{
                        position:fixed;
                        height:2.5em;
                        width:100%;
                        background-color:<?php echo $swnza_options['background_color'] ?>;
                        padding-top:10px;
                        z-index:999;
                        display:block;
                        animation: drop-in 0.5s ease-out 1s 1 normal forwards;
                    }

                    <?php if( isset( $swnza_options['show_on_top'] ) && ! empty( $swnza_options['show_on_top'] ) ) { ?>
                        .admin-bar .swnza_banner { margin-top:32px; }
                        .swnza_banner { top:-2.5em; }
                        .swnza_close_button { bottom:-10px; }
                    <?php } else { ?>
                        .swnza_banner{ bottom:-2.5em; }
                        .swnza_close_button { top:-10px;}
                    <?php } ?>

                    .swnza_banner span {
                        text-align: center;
                        margin: auto;
                        font-family: "Open Sans", sans-serif;
                    }

                    .swnza_banner a {
                      text-decoration: none;
                      color: <?php echo $swnza_options['background_color'] ?>;
                    }

                    .swnza_banner p {
                        color: <?php echo $swnza_options['font_color'] ?>;
                        text-align:center;
                        z-index:1000;
                        font-size:1.25em;
                        display:block;
                        position: fixed;
                        padding-right: 3em;
                        animation: drop-in-message 0.5s ease-out 1s 1 normal forwards;
                        <?php if( isset( $swnza_options['show_on_top'] ) && ! empty( $swnza_options['show_on_top'] ) ) { ?>
                          top: -2,5em;
                        <?php } else { ?>
                          bottom: -2.5em;
                        <?php } ?>
                        <?php if( isset( $swnza_options['button'] ) && ! empty( $swnza_options['button'] ) ) { ?>
                          left: 25%;
                          float: left;
                          width: auto;
                        <?php }else{ ?>
                          width: 100%;
                        <?php } ?>
                        <?php if( !is_user_logged_in() ) { ?>
                          margin-top: 0;
                        <?php } ?>
                    }

                    .swnza_banner div {
                        background-color: <?php echo $swnza_options['font_color'] ?>;
                        color: <?php echo $swnza_options['background_color'] ?>;
                        text-align: center;
                        height: 1.5em;
                        margin-top: .1em;
                        display: block;
                        font-size: 1em;
                        padding-left: 0.75em;
                        padding-right: 0.75em;
                        width: auto;
                        position: fixed;
                        right: 25%;
                        font-weight: 400;
                        z-index: 1;
                        <?php if( isset( $swnza_options['button_wiggle'] ) && ! empty( $swnza_options['button_wiggle'] ) ) { ?>
                          animation-name: wiggle;
                          animation-duration: 2.5s;
                          animation-iteration-count: infinite;
                          animation-delay: 2s;
                        <?php } ?>
                    }

                    .swnza_close_button{
                        display:block;
                        position:absolute;
                        right:5px;
                        width:20px;
                        height:20px;
                        background:url("<?php echo plugins_url( 'images/close-button.svg', __FILE__ ); ?>") no-repeat center center;
                        background-color:white;
                        border-radius:100px;
                        border: 3px solid <?php echo $swnza_options['background_color'] ?>;
                        fill: <?php echo $swnza_options['font_color'] ?>;
                    }
                    .swnza_close_button:hover{
                        cursor: pointer;
                    }

                    .swnza_close_button_link {
                      fill: <?php echo $swnza_options['font_color'] ?>;
                      color: <?php echo $swnza_options['font_color'] ?>;
                    }

                    @keyframes wiggle {
                      0%, 26%, 100% {
                        transform: rotate(0deg);
                      }
                      4%, 12%, 20% {
                        transform: rotate(-5deg);
                      }
                      8%, 16%, 24% {
                        transform: rotate(5deg);
                      }
                    }
                    @keyframes drop-in {
                      <?php if( isset( $swnza_options['show_on_top'] ) && ! empty( $swnza_options['show_on_top'] ) ) { ?>
                          0% {
                            top: -2.5em;
                          }
                          100% {
                            top: 0;
                          }
                      <?php } else { ?>
                          0% {
                            bottom: -2.5em;
                          }
                          100% {
                            bottom: 0;
                          }
                      <?php } ?>
                    }
                    @keyframes drop-in-message {
                      <?php if( isset( $swnza_options['show_on_top'] ) && ! empty( $swnza_options['show_on_top'] ) ) { ?>
                          0% {
                            top: -2.5em;
                          }
                          100% {
                            top: 5px;
                          }
                      <?php } else { ?>
                          0% {
                            bottom: -2.5em;
                          }
                          100% {
                            bottom: 5px;
                          }
                      <?php } ?>
                    }

                <?php if( $swnza_options[ 'show_on_mobile' ] != 1 ) { ?>
                    @media all and (max-width: 500px){
                    .swnza_banner{
                        display: none;
                    }
                }
                <?php } ?>
                </style>
                <?php } ?>

        <div class="swnza_banner" id="swnza_banner_id">
          <span>
            <p id="swnza_banner_text"><?php echo htmlspecialchars_decode( stripslashes( $swnza_options['message'] ) ); ?></p>
            <?php if( isset( $swnza_options['button'] ) && ! empty( $swnza_options['button'] ) ) { ?>
            <div id="swnza_button_text">
              <a href="<?php echo htmlspecialchars_decode( stripslashes( $swnza_options['button_link'] ) ); ?>">
              <?php echo htmlspecialchars_decode( stripslashes( $swnza_options['button_message'] ) ); ?>
              </a>
            </div>
          <?php } ?>
          </span>
          <a id="swnza_close_button_link" class="swnza_close_button"></a>
        </div>
    <?php
    }
} //end of class

Sitewide_Notice_TWDH::get_instance();
