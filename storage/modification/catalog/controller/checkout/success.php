<?php
class ControllerCheckoutSuccess extends Controller {
	public function index() {
		$this->load->language('checkout/success');

		if (isset($this->session->data['order_id'])) {
			$this->cart->clear();

                $log = new Log('Code-MelhorEnvio-' . date('m-Y') . '.log');
                $log->write("SalvarFrete - Passo 1 Pagina do Sucesso");

                $this->load->model('module/codemarket_module');
                $c524 = $this->model_module_codemarket_module->getModulo('524');

                if (!empty($this->session->data['melhor_envio']) && !empty($this->session->data['shipping_method']['code']) &&
                    strpos($this->session->data['shipping_method']['code'],'code_melhorenvio') >= 0 &&
                    !empty($this->session->data['melhor_envio']['post']) &&
                    !empty($this->session->data['shipping_method']['melhorenvio_id']) &&
                    !empty($c524) && ((!empty($c524->salvarFrete) && $c524->salvarFrete == 1) || (empty($c524->salvarFrete)))
                ) {
                    $log->write("SalvarFrete - Passo 2 Passou na verificação");
                    //$log->write("SalvarFrete - Shipping Method ".print_r($this->session->data['shipping_method'], true));

                    $id =  (int) $this->session->data['shipping_method']['melhorenvio_id'];

                    foreach((array) $this->session->data['melhor_envio']['post'] as $key_menvio => $code_menvio){
                        $code_menvio = (array) $code_menvio;
                        if(!empty($code_menvio['id']) && $id == $code_menvio['id']){
                            $key = $key_menvio;
                            break;
                        }
                    }

                    if(!empty($key) && !empty($this->session->data['melhor_envio']['post'][$key]) && !empty($this->session->data['order_id'])){
                        $this->db->query('INSERT INTO ' . DB_PREFIX . 'code_melhorenvio
                            SET
                            order_id = ' . (int) $this->session->data['order_id'] . ',
                            data = "' . $this->db->escape(json_encode($this->session->data['melhor_envio']['post'][$key])) . '",
                            date_created = NOW()
                        ');

                        $log->write("SalvarFrete - Passo Final salvo Melhor Envio com sucesso ".$this->session->data['order_id']);
                    }else{
                        $log->write("SalvarFrete - Passo Final não passou na condição");
                    }

                    unset($this->session->data['melhor_envio']);
                }else{
                     $log->write("SalvarFrete - Passo 2 não passou na verificação");
                }
            

			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['guest']);
			unset($this->session->data['comment']);
			unset($this->session->data['order_id']);
			unset($this->session->data['coupon']);
			unset($this->session->data['reward']);
			unset($this->session->data['voucher']);
			unset($this->session->data['vouchers']);
			unset($this->session->data['totals']);
		}

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_basket'),
			'href' => $this->url->link('checkout/cart')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_checkout'),
			'href' => $this->url->link('checkout/checkout', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_success'),
			'href' => $this->url->link('checkout/success')
		);

		if ($this->customer->isLogged()) {
			$data['text_message'] = sprintf($this->language->get('text_customer'), $this->url->link('account/account', '', true), $this->url->link('account/order', '', true), $this->url->link('account/download', '', true), $this->url->link('information/contact'));
		} else {
			$data['text_message'] = sprintf($this->language->get('text_guest'), $this->url->link('information/contact'));
		}

		$data['continue'] = $this->url->link('common/home');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('common/success', $data));
	}
}
