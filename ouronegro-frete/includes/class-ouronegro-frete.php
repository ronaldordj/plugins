<?php
if (!defined('ABSPATH')):
    exit();
endif;


add_filter( 'woocommerce_shipping_methods', 'register_ouronegro_frete' );

function register_ouronegro_frete( $methods ) {

	// $method contains available shipping methods
	$methods[ 'ouronegro_frete' ] = 'WC_OuroNegro_Frete';

	return $methods;
}


/**
 * WC_OuroNegro_Frete class.
 *
 * @class 		WC_OuroNegro_Frete
 * @version		1.0.0
 * @package		Shipping-for-WooCommerce/Classes
 * @category	Class
 * @author 		Ronaldo Raycik
 */
class WC_OuroNegro_Frete extends WC_Shipping_Method {

	/**
	 * Constructor. The instance ID is passed to this.
	 */
	public function __construct( $instance_id = 0 ) {
		$this->id                    = 'ouronegro_frete';
		$this->instance_id           = absint( $instance_id );
		$this->method_title          = 'Ouro Negro';
		$this->method_description    = 'Ouro Negro - Cálculo de frete Transportadora Ouro Negro.';
		$this->supports              = array(
			'shipping-zones',
			'instance-settings',
		);
		$this->instance_form_fields = array(
			'enabled' => array(
				'title' 		=> __( 'Enable/Disable' ),
				'type' 			=> 'checkbox',
				'label' 		=> __( 'Ativar método de entrega?' ),
				'default' 		=> 'no',
			),
			'title' => array(
				'title' 		=> __( 'Título' ),
				'type' 			=> 'text',
				'description' 	=> __( 'Nome do Frete/Transportadora.' ),
                'placeholder'		=> __( 'Nome da Transportadora' ),
                'default'		=> 'Ouro Negro Transportes',
				'desc_tip'		=> true
            ),
            'dominio' => array(
				'title' 		=> 'URL Api',
				'type' 			=> 'text',
				'description' 	=> __( 'URL API RTE' ),
                'placeholder'	=> __( 'https://01wapi.ouronegro.com.br' ),
                'default'       => 'https://01wapi.ouronegro.com.br/api/v1/simula-cotacao',
				'desc_tip'		=> true
			),'login' => array(
				'title' 		=> __( 'Token Bearer' ),
				'type' 			=> 'text',
				'description' 	=> __( '' ),
				'placeholder'		=> __( 'token' ),
				'desc_tip'		=> true
            ),'cnpjRemetente' => array(
				'title' 		=> __( 'CNPJ Remetente' ),
				'type' 			=> 'text',
				'description' 	=> __( 'Informar apenas Números!' ),
				'placeholder'		=> __( '12345678912345' ),
				'desc_tip'		=> true
            ),'cepOrigem' => array(
				'title' 		=> __( 'Cep Origem' ),
				'type' 			=> 'text',
                'description' 	=> __( 'Cep de Origem. Informar apenas Números.' ),
				'placeholder'		=> __( '01234567' ),
				'desc_tip'		=> true
            ),
              'idOrigem' => array(
				'title' 		=> __( 'Código da cidade de origem' ),
				'type' 			=> 'text',
                'description' 	=> __( 'Código da cidade de origem. Informar apenas Números.' ),
				'placeholder'		=> __( '0123' ),
				'desc_tip'		=> true
            )
        );
        
		$this->enabled                      = $this->get_option( 'enabled' );
        $this->title                        = $this->get_option( 'title' );
        $this->dominio                      = $this->get_option( 'dominio' );
        $this->login                        = $this->get_option( 'login' );        
        $this->cnpjRemetente                = $this->get_option( 'cnpjRemetente' );        
        $this->cepOrigem                    = $this->get_option( 'cepOrigem' );
        $this->idOrigem                     = $this->get_option( 'idOrigem' );
        $this->ouronegro_frete_option             = [
            'enabled' => $this->enabled,
            'title' => $this->title,
            'dominio' => $this->dominio,
            'login' => $this->login,            
            'cnpjRemetente' => $this->cnpjRemetente,            
            'cepOrigem' => $this->cepOrigem,
            'idOrigem' => $this->idOrigem
        ];
        

		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
    }
    
    /**
     * calculate_shipping function.
     * @param array $package (default: array())
     */
    public function calculate_shipping( $package = array() ) {

        $ouronegro_frete_calc = calcRTEFrete($package, $this->ouronegro_frete_option);       
        
        if($this->enabled == 'yes'){

            $shipping_name = $this->title;
            $cost = $ouronegro_frete_calc;

            $rate = array(
                'id' => $this->id,
                'label' => $shipping_name,
                'cost' => $cost
            );           

            $this->add_rate( $rate );            
            
        }
    }



}

// function get_string_between($string, $start, $end){
//     $string = ' ' . $string;
//     $ini = strpos($string, $start);
//     if ($ini == 0) return '';
//     $ini += strlen($start);
//     $len = strpos($string, $end, $ini) - $ini;
//     return substr($string, $ini, $len);
// }

function calcRTEFrete($package = null, $ouronegro_frete_option = null){
    if(!$package || !$ouronegro_frete_option){
        return;
    }
    
    $cep = str_replace('-', '', $package['destination']['postcode']);

    $valorNF = $package['contents_cost'];
    settype($valorNF, "float");
    $qtd = 0;
    $peso_total = 0;
    $volume_total = 0;
    $test_dimensao = [];
    foreach ($package['contents'] as $key => $value) {
        $qtd += $value['quantity'];
        $peso = 0;
        $prod_id = ($value['variation_id'] ? $value['variation_id'] : $value['product_id']);
        // $prod_id = $value['product_id'];
        $product = wc_get_product( $prod_id );
        $peso = $product->get_weight();
        $peso_total += $peso*$qtd;

        $comprimento = $product->get_length();
        $largura = $product->get_width();
        $altura = $product->get_height();
        $test_dimensao[] = ['qtd' => $qtd, 'peso'=>$peso, 'c' => $comprimento, 'l' => $largura, 'a' => $altura];
        $volume = $comprimento*$largura*$altura;

        $volume_total += $volume*$qtd;       
        
    }  
    
    //print_r($peso_total);

    $ci = curl_init();

    curl_setopt_array($ci, array(
      CURLOPT_URL => "https://01wapi.ouronegro.com.br/api/v1/busca-por-cep?zipCode=".$cep,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        "Authorization: Bearer 9mnDzK96OChOE4QXmhLIb8xruDz0BSouJtcHdilITNUzMhHkmPtEwLaFCMedVcOpVQ-Xci99ZtZqjPhKI9EgaOpg2YN4gMI-39xtukmtJPPYqb5c_WC9vigVpSU_D4Cfky4Bn4sdOibToYugUpdhPyQ-nZOkzT_IV7tXLnpIaHY9YPr1fXfTfxurFLueIkYoeTMIsxFsQfzrmh3BXk51jhx2Ag3FjhAzZmtV4riz_NZ1XjXgGwmnC58FvETR1YXR_9sKz3AAGvvDA_Iw4OgCf5w1J6pQDP3QENJ4Vmxt_akrn-3u14-vgEeWGZ3HHzVqO5CDxVUq0UFN9QU6Vb2ctbERv80qrozCyENt1NPs_0m8UZsPuKAkieVpvWJQTQQtcm9oxkT-J84qjBCC9sMBhNiLLZbiC0aApaNyUoi9gwjL62NzV7U6aM7BBoUwaHi9zCbsrJXxRSLXXWlOtwibm4F789J459jPTJBIE1Aozsk"
      ),
    ));
    
    $result = curl_exec($ci);
    
    curl_close($ci);
    

    $inf   = json_decode($result);     
    $array = json_decode(json_encode($inf), true);

    $idcidade = $array['CityId'];

    $ceporigem = $ouronegro_frete_option['cepOrigem'];
    $idorigem  = $ouronegro_frete_option['idOrigem'];
    $cnpj      = $ouronegro_frete_option['cnpjRemetente'];
    $URL       = $ouronegro_frete_option['dominio'];
    $token     = $ouronegro_frete_option['login'];

    $data = [
        "OriginZipCode" => $ceporigem,
        "OriginCityId"  => $idorigem,
        "DestinationZipCode" => $cep,
        "DestinationCityId" => $idcidade,
        "TotalWeight" => $peso_total,
        "EletronicInvoiceValue" => $valorNF,
        "CustomerTaxIdRegistration" => $cnpj,
        "Packs" => [

        ]
    ];


    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => $URL,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => json_encode($data),
    //CURLOPT_POSTFIELDS =>"{\r\n  \"OriginZipCode\": \"$ceporigem\",\r\n  \"OriginCityId\": 7566,\r\n  \"DestinationZipCode\": \"$cep\",\r\n  \"DestinationCityId\": $idcidade,\r\n  \"TotalWeight\": $peso_total,\r\n  \"EletronicInvoiceValue\": $valorNF,\r\n  \"CustomerTaxIdRegistration\": \"$cnpj\",\r\n  \"Packs\": []\r\n}",
    CURLOPT_HTTPHEADER => array(
        "Authorization: Bearer $token",
        "Content-Type: application/json"
    ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    if ($err) {
        return;
    }  
    
    $info       = json_decode($response);     
    $arraysaida = json_decode(json_encode($info), true);
    // $frete = $arraysaida['Value'];

    return $arraysaida['Value'];

    //print_r($arraysaida['Value']);

}