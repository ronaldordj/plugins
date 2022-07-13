<?php
if (!defined('ABSPATH')):
    exit();
endif;


add_filter( 'woocommerce_shipping_methods', 'register_braspress_frete' );

function register_braspress_frete( $methods ) {

	// $method contains available shipping methods
	$methods[ 'braspress_frete' ] = 'WC_BRASPRESS_Frete';

	return $methods;
}


/**
 * WC_BRASPRESS_Frete class.
 *
 * @class 		WC_BRASPRESS_Frete
 * @version		1.0.0
 * @package		Shipping-for-WooCommerce/Classes
 * @category	Class
 * @author 		Ronaldo Raycik
 */
class WC_BRASPRESS_Frete extends WC_Shipping_Method {

	/**
	 * Constructor. The instance ID is passed to this.
	 */
	public function __construct( $instance_id = 0 ) {
		$this->id                    = 'braspress_frete';
		$this->instance_id           = absint( $instance_id );
		$this->method_title          = 'BRASPRESS Frete';
		$this->method_description    = 'BRASPRESS Frete - Cálculo de frete Transportadora BRASPRESS.';
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
                'default'		=> 'Transportadora BRASPRESS',
				'desc_tip'		=> true
            ),
            'dominio' => array(
				'title' 		=> 'URL Api',
				'type' 			=> 'text',
				'description' 	=> __( 'URL API BRASPRESS' ),
                'placeholder'	=> __( 'https://api.braspress.com' ),
                'default'       => 'https://hml-api.braspress.com/v1/cotacao/calcular/json',
				'desc_tip'		=> true
			),'login' => array(
				'title' 		=> __( 'Usuário' ),
				'type' 			=> 'text',
				'description' 	=> __( '' ),
				'placeholder'		=> __( 'Usuário Braspress' ),
				'desc_tip'		=> true
            ),'senha' => array(
				'title' 		=> __( 'Senha' ),
				'type' 			=> 'text',
				'description' 	=> __( '' ),
				'placeholder'		=> __( 'Senha Braspress' ),
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
            )
        );
        
		$this->enabled                      = $this->get_option( 'enabled' );
        $this->title                        = $this->get_option( 'title' );
        $this->dominio                      = $this->get_option( 'dominio' );
        $this->login                        = $this->get_option( 'login' );        
        $this->senha                        = $this->get_option( 'senha' );        
        $this->cnpjRemetente                = $this->get_option( 'cnpjRemetente' );        
        $this->cepOrigem                    = $this->get_option( 'cepOrigem' );        
        $this->braspress_frete_option             = [
            'enabled'       => $this->enabled,
            'title'         => $this->title,
            'dominio'       => $this->dominio,
            'login'         => $this->login,
            'senha'         => $this->login,            
            'cnpjRemetente' => $this->cnpjRemetente,            
            'cepOrigem'     => $this->cepOrigem            
        ];
        

		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
    }
    
    /**
     * calculate_shipping function.
     * @param array $package (default: array())
     */
    public function calculate_shipping( $package = array() ) {

        $braspress_frete_calc = calcBRASPRESSFrete($package, $this->braspress_frete_option);       
        
        if($this->enabled == 'yes'){

            $shipping_name = $this->title;
            $cost = $braspress_frete_calc;

            $rate = array(
                'id' => $this->id,
                'label' => $shipping_name,
                'cost' => $cost
            );           

            $this->add_rate( $rate );            
            
        }
    }



}

function calcBRASPRESSFrete($package = null, $braspress_frete_option = null){
    if(!$package || !$braspress_frete_option){
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
        $volume = $comprimento*$largura*$altura;
        $volume_total += $volume*$qtd;       

        $cubagem[] = ['altura' => $altrura, 'largura'=>$largura, 'comprimento' => $comprimento, 'volume' => $volume_total];
        
    }  
    
    //print_r($peso_total);    

    $ceporigem = $braspress_frete_option['cepOrigem'];
    $idorigem  = $braspress_frete_option['idOrigem'];
    $cnpj      = $braspress_frete_option['cnpjRemetente'];
    $URL       = $braspress_frete_option['dominio'];
    $token     = $braspress_frete_option['login'];

    $data = [
        "cnpjRemetente" => $cnpj,
        "cnpjDestinatario"  => "",
        "modal" => "R",
        "tipoFrete" => "1",
        "cepOrigem" => $ceporigem,
        "cepDestino" => $cep,
        "vlrMercadoria" => $valorNF,
        "peso" => $peso_total,
        "volumes => $volume_total",
        "cubagem" => $cubagem
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
    CURLOPT_HTTPHEADER => array(
        "Authorization: Basic {usuario e senha base64}",
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

    return $arraysaida['totalFrete'];

    print_r($arraysaida['totalFrete']);

}
