<?php
 /**
 * Plugin Name: Cálculo de Frete BRASPRESS
 * Plugin URI: https://driftweb.com.br
 * Description: Cálculo de Frete BRASPRESS
 * Author: Ronaldo Raycik
 * Author URI: https://linkedin.com/in/rraycik
 * Version: 1.0.0
 */


if (!defined('ABSPATH')):
    exit();
endif;


define('BRASPRESS_FRETE_PATH', plugin_dir_path(__FILE__));
define('BRASPRESS_FRETE_URL', plugin_dir_url(__FILE__));

define('BRASPRESS_FRETE_INCLUDES_PATH', plugin_dir_path(__FILE__) . 'includes/');
define('BRASPRESS_FRETE_INCLUDES_URL', plugin_dir_url(__FILE__) . 'includes/');

define('BRASPRESS_FRETE_VIEWS_PATH', plugin_dir_path(__FILE__) . 'views/');
define('BRASPRESS_FRETE_VIEWS_URL', plugin_dir_url(__FILE__) . 'views/');

define('BRASPRESS_FRETE_ASSETS_PATH', plugin_dir_path(__FILE__) . 'assets/');
define('BRASPRESS_FRETE_ASSETS_URL', plugin_dir_url(__FILE__) . 'assets/');


if (!class_exists('braspressFrete')):

    class braspressFrete{

        /**
         * Instance of this class
         *
         * @var object
         */



        protected static $braspressFrete = null;


        private function __construct(){
            /**
             * Include plugin files
             */

            $this->enqueue_includes();

            // $this->enqueue_views();



        }





        public static function braspress_frete_start(){

            if (self::$braspressFrete == null):

                self::$braspressFrete = new self();

            endif;



            return self::$braspressFrete;

        }


        private function enqueue_includes(){

            include_once BRASPRESS_FRETE_INCLUDES_PATH . 'class-braspress-frete.php';

        }



        // private function enqueue_views(){

        //     include_once BRASPRESS_FRETE_VIEWS_PATH . 'view-braspress-frete.php';

        // }



    }





    //Start's when plugins are loaded plugin

    // add_action('plugins_loaded', array('braspressFrete', 'braspress_frete_start'));



     
    

    add_action('init', array('braspressFrete', 'braspress_frete_start'));


endif;