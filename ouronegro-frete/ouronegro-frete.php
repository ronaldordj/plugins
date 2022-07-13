<?php
 /**
 * Plugin Name: Cálculo de Frete Ouro Negro Woocommece
 * Plugin URI: https://r1sistemas.tec.br
 * Description: Cálculo de Frete Ouro Negro Woocommece - Conforme Tabela
 * Author: Ronaldo Raycik
 * Author URI: https://linkedin.com/in/rraycik
 * Version: 1.0.0
 */


if (!defined('ABSPATH')):
    exit();
endif;


define('OURONEGRO_FRETE_PATH', plugin_dir_path(__FILE__));
define('OURONEGRO_FRETE_URL', plugin_dir_url(__FILE__));

define('OURONEGRO_FRETE_INCLUDES_PATH', plugin_dir_path(__FILE__) . 'includes/');
define('OURONEGRO_FRETE_INCLUDES_URL', plugin_dir_url(__FILE__) . 'includes/');

define('OURONEGRO_FRETE_VIEWS_PATH', plugin_dir_path(__FILE__) . 'views/');
define('OURONEGRO_FRETE_VIEWS_URL', plugin_dir_url(__FILE__) . 'views/');

define('OURONEGRO_FRETE_ASSETS_PATH', plugin_dir_path(__FILE__) . 'assets/');
define('OURONEGRO_FRETE_ASSETS_URL', plugin_dir_url(__FILE__) . 'assets/');


if (!class_exists('ouronegroFrete')):

    class ouronegroFrete{

        /**
         * Instance of this class
         *
         * @var object
         */



        protected static $ouronegroFrete = null;


        private function __construct(){
            /**
             * Include plugin files
             */

            $this->enqueue_includes();

            // $this->enqueue_views();



        }





        public static function ouronegro_frete_start(){

            if (self::$ouronegroFrete == null):

                self::$ouronegroFrete = new self();

            endif;



            return self::$ouronegroFrete;

        }


        private function enqueue_includes(){

            include_once OURONEGRO_FRETE_INCLUDES_PATH . 'class-ouronegro-frete.php';

        }



        private function enqueue_views(){

            include_once OURONEGRO_FRETE_VIEWS_PATH . 'view-ouronegro-frete.php';

        }



    }


    //Start's when plugins are loaded plugin

    add_action('plugins_loaded', array('ouronegroFrete', 'ouronegro_frete_start'));     
    

    add_action('init', array('ouronegroFrete', 'ouronegro_frete_start'));


endif;