<?php

namespace Code\MelhorEnvio;

use DB\MySQLi;
use GuzzleHttp\Client;

use function _\findIndex;

/**
 * Class MelhorEnvio
 * © Copyright 2013-2022 Codemarket - Todos os direitos reservados.
 *
 * @package Code\MelhorEnvio
 */
class MelhorEnvio
{
    /**
     * @var object
     */
    public $conf;
    /**
     * @var \Loader
     */
    private $load;
    /**
     * @var \Registry
     */
    private $registry;
    /**
     * @var MySQLi
     */
    private $db;
    /**
     * @var \ModelModuleCodemarketModule
     */
    private $codeModel;
    /**
     * @var \Config
     */
    private $config;
    /**
     * @var \Cart\Weight
     */
    private $weight;
    /**
     * @var \Cart\Length
     */
    private $length;
    /**
     * @var object
     */
    private $quote;
    /**
     * @var \GuzzleHttp\Client
     */
    private $client;
    
    /**
     * MelhorEnvio constructor.
     *
     * @param \Registry $registry
     *
     * @throws \Exception
     */
    public function __construct(\Registry $registry)
    {
        $this->registry = $registry;
        $this->load = $registry->get('load');
        $this->db = $registry->get('db');
        
        $this->load->model('module/codemarket_module');
        $codeModel = $registry->get('model_module_codemarket_module');
        $this->conf = $codeModel->getModulo('524');
        
        $this->conf->baseUri = 'https://melhorenvio.com.br';
        if ((int)$this->conf->env === 0) {
            $this->conf->apiToken = $this->conf->apiTokenSandbox;
            $this->conf->baseUri = 'https://sandbox.melhorenvio.com.br';
        }
        
        $this->client = new Client([
            'base_uri' => $this->conf->baseUri,
            'headers' => [
                'accept' => 'application/json',
                'content-type' => 'application/json',
                'authorization' => 'Bearer '.$this->conf->apiToken,
            ],
        ]);
    }
    
    /**
     * cart.product.options
     *
     * @param $haystack
     * @param $path
     * @param $value
     *
     * @return int|mixed
     */
    public static function findIndex($haystack, $path, $value)
    {
        $getValue = function ($haystack, $path) {
            $segments = explode('.', $path);
            
            foreach ($segments as $segment) {
                if (!is_array($haystack) || !isset($haystack[$segment])) {
                    return null;
                }
                
                $haystack = $haystack[$segment];
            }
            
            return $haystack;
        };
        
        foreach ($haystack as $i => $item) {
            if ($getValue($item, $path) == $value) {
                return $i;
            }
        }
        
        return -1;
    }
    
    /**
     * @param      $orderId
     * @param bool $assoc
     *
     * @return mixed|null
     * @throws \Exception
     */
    public function getQuote($orderId, $assoc = false)
    {
        $query = $this->db->query(
            'SELECT * FROM '.DB_PREFIX.'code_melhorenvio WHERE order_id = '
            .(int)$orderId
        );
        
        if ($query->row) {
            $quote = json_decode($query->row['data'], $assoc);
            $this->quote = json_decode($query->row['data']);
            
            return $quote;
        }
        
        return null;
    }
    
    public function setDataCart($order_id, $package = [])
    {
        $this->load->model('checkout/order');
        $this->load->model('catalog/product');
        
        $quote = $this->getQuote($order_id);
        
        if (empty($quote)) {
            return false;
        }
        
        $modelOrder = $this->registry->get('model_checkout_order');
        $order = $modelOrder->getOrder($order_id);
        
        $data['data'] = [
            'quote' => $quote,
            'package' => $package,
            'order' => $order,
        ];
        
        $data['config'] = $this->conf;
        
        /*
        foreach ($data['config']->agencies as $key => $ag) {
            if (empty($ag)) {
                unset($data['config']->agencies->key);
                continue;
            }
           
             // $data['config']->agencies->$key = new stdClass();
            
        }
        */
        
        if (!empty($data['config']->agencies[$quote->company->id])) {
            $data['data']['agency']
                = $data['config']->agencies[$quote->company->id]->id;
        }
        
        // Removendo alguns dados parra ficar mais leve
        unset($data['config']->servicos);
        unset($data['config']->apiTokenSandbox);
        unset($data['config']->rules);
        unset($data['config']->agencies);
        unset($data['config']->code_token);
        
        return json_encode($data);
    }
    
    /**
     * Envia automaticamente o pedido ao carrinho
     * Sem uso no momento 10/03/2022 08:30
     *
     * @param $order_id
     *
     * @return mixed
     * @throws \Exception
     */
    public function autoPost(
        $order_id
    ) {
        $quote = $this->getQuote($order_id);
        
        $package = new \stdClass();
        $package->weight = $quote->packages[0]->weight;
        $package->width = $quote->packages[0]->dimensions->width;
        $package->length = $quote->packages[0]->dimensions->length;
        $package->height = $quote->packages[0]->dimensions->height;
        
        $post = $this->setDataCart($order_id, $package);
        
        if (empty($post) || empty($this->conf->code_app_token)) {
            return false;
        }
        
        $url = "https://api.cloud.codemarket.com.br/v1/melhorenvio/cart";
        
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_MAXREDIRS => 1,
            CURLOPT_TIMEOUT => 8,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => json_encode($post),
            CURLOPT_HTTPHEADER => [
                'content-type: application/json',
                'accept: application/json',
                'Cache-Control: no-cache',
                'Accept-Encoding: gzip, deflate, br',
                'Authorization: '.$this->conf->code_app_token,
                'Key: 03eacb5b-7582-430e-b442-a8ce48fe1495',
                'App: 524',
            ],
        ]);
        
        $response = curl_exec($curl);
        curl_close($curl);
        
        if (!empty($response) && $response->success === 'true'
            && isset($response->data)
        ) {
            return $response->data;
        } else {
            return false;
        }
    }
    
    public function comparEtiqueta(
        $order_id,
        $package
    ) {
        $post = $this->setDataCart($order_id, $package);
        
        if (empty($post) || empty($this->conf->code_app_token)) {
            return false;
        }
        
        //return $post;
        $url = "https://api.cloud.codemarket.com.br/v1/melhorenvio/cart";
        
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_MAXREDIRS => 1,
            CURLOPT_TIMEOUT => 8,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $post,
            CURLOPT_HTTPHEADER => [
                'content-type: application/json',
                'accept: application/json',
                'Cache-Control: no-cache',
                'Accept-Encoding: gzip, deflate, br',
                'Authorization: '.$this->conf->code_app_token,
                'Key: 03eacb5b-7582-430e-b442-a8ce48fe1495',
                'App: 524',
            ],
        ]);
        $response = curl_exec($curl);
        //print_r($response); exit();
        //$err = curl_error($curl);
        curl_close($curl);
        
        $response = json_decode($response);
        
        //print_r($response);
        //exit();
        
        if (!empty($response) && $response->success === 'true'
            && isset($response->data)
        ) {
            return $response->data;
        } else {
            return false;
        }
    }
    
    public function checkCart(
        $package
    ) {
        if (empty($package->cart->id)) {
            return false;
        }
        
        $response = $this->client->get('/api/v2/me/cart');
        
        $cart = json_decode($response->getBody()->getContents());
        
        if (empty($cart->data)) {
            return false;
        }
        
        $index = -1;
        foreach ($cart->data as $i => $cart) {
            if ($cart->id == $package->cart->id) {
                $index = $i;
                break;
            }
        }
        
        return $index >= 0;
    }
    
    /**
     * Rastreio Postagem
     *
     * @param $package
     *
     * @return false
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function trackPackage(
        $package,
        $log,
        $debugUrl
    ) {
        if (empty($package->cart->id)) {
            $log->write(
                "Lib-trackPackage() - Falha no Rastreio sem o ID do Carrinho"
            );
            
            return false;
        }
        
        $response = $this->client->post(
            '/api/v2/me/shipment/tracking',
            [
                'form_params' => [
                    'orders' => [$package->cart->id],
                ],
            ]
        );
        
        $response = json_decode($response->getBody()->getContents());
        
        if (!empty($response)
            && !empty($response->{$package->cart->id})
        ) {
            // Usando o Melhor Rastreio para Consultar
            
            if ($debugUrl) {
                echo "<h3>Lib-trackPackage() - Retorno Melhor Envio Rastreamento</h3>";
                print_r($response->{$package->cart->id});
            }
            
            // Atualizando o Status com base no Melhor Rastreio
            if (!empty($response->{$package->cart->id}->tracking)) {
                try {
                    $response2 = $this->client->get(
                        'https://api.melhorrastreio.com.br/api/v1/trackings/'
                        .$response->{$package->cart->id}->tracking
                    );
                    $response2 = json_decode(
                        $response2->getBody()->getContents()
                    );
                    
                    if ($debugUrl) {
                        echo "<h3>Lib-trackPackage() - Retorno Auxiliar Melhor Rastreio Rastreamento</h3>";
                        print_r($response2);
                    }
                    
                    if (!empty($response2->data)
                        && !empty($response2->data->status)
                        && !empty($response2->data->tracking)
                        && $response2->data->tracking
                        == $response->{$package->cart->id}->tracking
                    ) {
                        $response->{$package->cart->id}->status
                            = $response2->data->status;
                    }
                    
                    if (!empty($response2->data)
                        && !empty($response2->data->company->name)
                        && !empty($response2->data->tracking)
                        && $response2->data->tracking
                        == $response->{$package->cart->id}->tracking
                    ) {
                        $response->{$package->cart->id}->transportadora
                            = $response2->data->company->name;
                    }
                } catch (Exception $e) {
                    $log->write(
                        "Lib-trackPackage() - Erro na Rastreio do Frete!, erro: "
                        .print_r($e, true)
                    );
                    //print_r($e);
                    //exit();
                    // Não faz nada
                }
            }
            
            $log->write(
                "Lib-trackPackage() - Rastreio do Frete realizada com sucesso!"
            );
            
            return $response->{$package->cart->id};
        }
        
        if ($debugUrl) {
            echo "<h3>Lib-trackPackage() - Falha na Rastreio, retorno: </h3>";
            print_r($response);
        }
        $log->write(
            "Lib-trackPackage() - Falha na Rastreio do Frete!, retorno: "
            .print_r($response, true)
        );
        
        return false;
    }
    
    public function removeFromCart(
        $package
    ) {
        if (empty($package->cart->id)) {
            return false;
        }
        
        $response = $this->client->delete(
            '/api/v2/me/cart/'.$package->cart->id
        );
        
        return $response->getStatusCode() === 200;
    }
    
    public function removeCartFromPackage(
        $order_id,
        $package
    ) {
        if (empty($package->cart->id)) {
            return false;
        }
        
        $query = $this->db->query(
            'SELECT * FROM '.DB_PREFIX
            .'code_melhorenvio WHERE order_id = '
            .(int)$order_id
        );
        
        if (!$query->row) {
            return false;
        }
        
        $packages = json_decode($query->row['packages'], true);
        
        foreach ($packages as $i => $pkg) {
            if (empty($pkg->cart->id)) {
                unset($packages[$i]);
            }
        }
        
        $index = self::findIndex(
            $packages,
            'cart.id',
            $package->cart->id
        );
        
        if ($index >= 0) {
            unset($packages[$index]['cart']);
            
            $packages = json_encode($packages, JSON_PRETTY_PRINT);
            
            $this->db->query(
                'UPDATE '.DB_PREFIX."code_melhorenvio SET packages = '"
                .$this->db->escape($packages)."' WHERE order_id = "
                .$order_id
            );
        }
        
        return true;
    }
}
