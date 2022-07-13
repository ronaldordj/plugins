<?php
 /**
 * Plugin Name: Cálculo de Frete RTE/Rodonaves Woocommece
 * Plugin URI: https://driftweb.com.br
 * Description: Cálculo de Frete RTE/Rodonaves Woocommece
 * Author: Ronaldo Raycik
 * Author URI: https://linkedin.com/in/rraycik
 * Version: 1.0.0
 */


if (!defined('ABSPATH')):
    exit();
endif;


define('RTE_FRETE_PATH', plugin_dir_path(__FILE__));
define('RTE_FRETE_URL', plugin_dir_url(__FILE__));

define('RTE_FRETE_INCLUDES_PATH', plugin_dir_path(__FILE__) . 'includes/');
define('RTE_FRETE_INCLUDES_URL', plugin_dir_url(__FILE__) . 'includes/');

define('RTE_FRETE_VIEWS_PATH', plugin_dir_path(__FILE__) . 'views/');
define('RTE_FRETE_VIEWS_URL', plugin_dir_url(__FILE__) . 'views/');

define('RTE_FRETE_ASSETS_PATH', plugin_dir_path(__FILE__) . 'assets/');
define('RTE_FRETE_ASSETS_URL', plugin_dir_url(__FILE__) . 'assets/');


if (!class_exists('rteFrete')):

    class rteFrete{

        /**
         * Instance of this class
         *
         * @var object
         */



        protected static $rteFrete = null;


        private function __construct(){
            /**
             * Include plugin files
             */

            $this->enqueue_includes();

            // $this->enqueue_views();



        }





        public static function rte_frete_start(){

            if (self::$rteFrete == null):

                self::$rteFrete = new self();

            endif;



            return self::$rteFrete;

        }


        private function enqueue_includes(){

            include_once RTE_FRETE_INCLUDES_PATH . 'class-rte-frete.php';

        }



        // private function enqueue_views(){

        //     include_once RTE_FRETE_VIEWS_PATH . 'view-rte-frete.php';

        // }



    }





    //Start's when plugins are loaded plugin

    // add_action('plugins_loaded', array('rteFrete', 'rte_frete_start'));



     
    

    add_action('init', array('rteFrete', 'rte_frete_start'));


endif;