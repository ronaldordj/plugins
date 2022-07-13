<?php
 /**
 * Plugin Name: Cálculo de Frete BRASPRESS
 * Plugin URI: https://driftweb.com.br
 * Description: Mostra o valor em quantidade de vezes configurado
 * Author: Ronaldo Raycik
 * Author URI: https://linkedin.com/in/rraycik
 * Version: 1.0.0
 */


function action_woocommerce_after_shop_loop_item_title( ) {
echo "<span class='price_list'>".do_shortcode("[parcela parcelas='3' juros='0']")."</span>";
};

// add the action
add_action( 'woocommerce_after_shop_loop_item_title', 'action_woocommerce_after_shop_loop_item_title', 0, 0 );



function parcelas($parcelas){
	global $product;

	if ($product->get_type() == 'variable') {
		$product_variations = $product->get_available_variations();
		$variation_product_id = $product_variations [0]['variation_id'];
		$variation_product = new WC_Product_Variation( $variation_product_id );
		$preco_numero = $variation_product->price;
		$qtd_parcelas = $parcelas['parcelas'];
		$juros = $parcelas['juros'];
		$parcela_minima = 50.00;
		$valor_total = ($preco_numero * pow(1+($juros/100), $qtd_parcelas));
		return "".$parcelas['parcelas']."x de R$".number_format($valor_total/$qtd_parcelas,2,",",".")."";
	}
	else {		
		$preco_numero = $product->get_regular_price();		
		$qtd_parcelas = $parcelas['parcelas'];
		$juros = $parcelas['juros'];
		$parcela_minima = 50.00;
		$valor_total = ($preco_numero * pow(1+($juros/100), $qtd_parcelas));		
		return "".$parcelas['parcelas']."x de R$".number_format($valor_total/$qtd_parcelas,2,",",".")."";			
	}
	
}
add_shortcode( 'parcela', 'parcelas' );


function action_woocommerce_after_shop_loop_item_title2( ) {

    global $product;
	if ($product->get_type() == 'variable') {
		$variations = $product->get_available_variations();
		foreach($variations as $variation){
			$variation_id = $variation['variation_id'];
			$variation_obj = new WC_Product_variation($variation_id);
			$stock = $variation_obj->get_stock_quantity();
		}
	}
	else {
			$stock = $product->get_stock_quantity();
	}	


};

add_action( 'woocommerce_after_shop_loop_item', 'action_woocommerce_after_shop_loop_item_title2', 0, 0 );
