<?php
/* 
Version: 2.3.0.2
Author: Denise (rei7092@gmail.com)
Page: http://www.opencart.com/index.php?route=extension/extension/info&token=862f82b6be28a025c788dfff38c7a550&extension_id=26240
*/

class ControllerExtensionModuleLiveOptions extends Controller {
	/* ====================================================================================

	SETTINGS

	Below you can find five variables that relate to DOM the structure of the template product/product.twig. 
	The default values correspond to a default OpenCart theme. 
	If you use customized theme, these containers might have other class or id. In this case you need to clarify their value.

	==================================================================================== */
	public $options_container 			= '#content';			// in default them it is ".product-info"
	public $special_container 	        = '.price-new-live';	// in default them it is ".price-new"
	public $price_container				= '.price-old-live';	// in default them it is ".price-old"
	public $tax_container 		        = '.price-tax-live';	// in default them it is ".price-tax'"
	public $points_container 			= '.spend-points-live';	// in default them it is ".price-points'"
	public $reward_container 			= '.get-reward-live';	// in default them it is ".price-reward'"

	private $error = array();

	public function index() {
		$this->load->language('extension/module/live_options');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_live_options', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

 		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		if (isset($this->error['options_container'])) {
			$data['error_options_container'] = $this->error['options_container'];
		} else {
			$data['error_options_container'] = '';
		}
		if (isset($this->error['special_container'])) {
			$data['error_special_container'] = $this->error['special_container'];
		} else {
			$data['error_special_container'] = '';
		}
		if (isset($this->error['price_container'])) {
			$data['error_price_container'] = $this->error['price_container'];
		} else {
			$data['error_price_container'] = '';
		}
		if (isset($this->error['tax_container'])) {
			$data['error_tax_container'] = $this->error['tax_container'];
		} else {
			$data['error_tax_container'] = '';
		}
		if (isset($this->error['points_container'])) {
			$data['error_points_container'] = $this->error['points_container'];
		} else {
			$data['error_points_container'] = '';
		}
		if (isset($this->error['reward_container'])) {
			$data['error_reward_container'] = $this->error['reward_container'];
		} else {
			$data['error_reward_container'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
		    unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/live_options', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/live_options', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		// 商品區塊
		if (isset($this->request->post['module_live_options_container'])) {
			$data['module_live_options_container'] = $this->request->post['module_live_options_container'];
		} elseif( null !== $this->config->get('module_live_options_container') ) {
			$data['module_live_options_container'] = $this->config->get('module_live_options_container');
		} else {
			$data['module_live_options_container'] = $this->options_container;
		}

		// 特價
		if (isset($this->request->post['module_live_options_special_container'])) {
			$data['module_live_options_special_container'] = $this->request->post['module_live_options_special_container'];
		} elseif( null !== $this->config->get('module_live_options_special_container') ) {
			$data['module_live_options_special_container'] = $this->config->get('module_live_options_special_container');
		} else {
			$data['module_live_options_special_container'] = $this->special_container;
		}

		// 售價
		if (isset($this->request->post['module_live_options_price_container'])) {
			$data['module_live_options_price_container'] = $this->request->post['module_live_options_price_container'];
		} elseif( null !== $this->config->get('module_live_options_price_container') ) {
			$data['module_live_options_price_container'] = $this->config->get('module_live_options_price_container');
		} else {
			$data['module_live_options_price_container'] = $this->price_container;
		}

		// 稅率
		if (isset($this->request->post['module_live_options_tax_container'])) {
			$data['module_live_options_tax_container'] = $this->request->post['module_live_options_tax_container'];
		} elseif( null !== $this->config->get('module_live_options_tax_container') ) {
			$data['module_live_options_tax_container'] = $this->config->get('module_live_options_tax_container');
		} else {
			$data['module_live_options_tax_container'] = $this->tax_container;
		}

		// 紅利
		if (isset($this->request->post['module_live_options_points_container'])) {
			$data['module_live_options_points_container'] = $this->request->post['module_live_options_points_container'];
		} elseif( null !== $this->config->get('module_live_options_points_container') ) {
			$data['module_live_options_points_container'] = $this->config->get('module_live_options_points_container');
		} else {
			$data['module_live_options_points_container'] = $this->points_container;
		}

		// 折扣點數
		if (isset($this->request->post['module_live_options_reward_container'])) {
			$data['module_live_options_reward_container'] = $this->request->post['module_live_options_reward_container'];
		} elseif( null !== $this->config->get('module_live_options_reward_container') ) {
			$data['module_live_options_reward_container'] = $this->config->get('module_live_options_reward_container');
		} else {
			$data['module_live_options_reward_container'] = $this->reward_container;
		}

		// 下拉選單：售價顯示方式
		if (isset($this->request->post['module_live_options_show_type'])) {
			$data['module_live_options_show_type'] = $this->request->post['module_live_options_show_type'];
		} else {
			$data['module_live_options_show_type'] = $this->config->get('module_live_options_show_type');
		}

		// 下拉選單：選項顯示方式
		if (isset($this->request->post['module_live_options_show_options_type'])) {
			$data['module_live_options_show_options_type'] = $this->request->post['module_live_options_show_options_type'];
		} else {
			$data['module_live_options_show_options_type'] = $this->config->get('module_live_options_show_options_type');
		}

		// 下拉選單：使用快取
		if (isset($this->request->post['module_live_options_use_cache'])) {
			$data['module_live_options_use_cache'] = $this->request->post['module_live_options_use_cache'];
		} else {
			$data['module_live_options_use_cache'] = $this->config->get('module_live_options_use_cache');
		}

		// 下拉選單：統計數量
		if (isset($this->request->post['module_live_options_calculate_quantity'])) {
			$data['module_live_options_calculate_quantity'] = $this->request->post['module_live_options_calculate_quantity'];
		} else {
			$data['module_live_options_calculate_quantity'] = $this->config->get('module_live_options_calculate_quantity');
		}

		// 下拉選單：啟用狀態
		if (isset($this->request->post['module_live_options_status'])) {
			$data['module_live_options_status'] = $this->request->post['module_live_options_status'];
		} else {
			$data['module_live_options_status'] = $this->config->get('module_live_options_status');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
        $data['current_lang_id'] = $this->config->get('config_language_id');

		$this->response->setOutput($this->load->view('extension/module/live_options', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/live_options')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		if (!$this->request->post['module_live_options_container']) {
			$this->error['options_container'] = $this->language->get('error_options_container');
		}
		if (!$this->request->post['module_live_options_special_container']) {
			$this->error['special_container'] = $this->language->get('error_special_container');
		}
		if (!$this->request->post['module_live_options_price_container']) {
			$this->error['price_container'] = $this->language->get('error_price_container');
		}
		if (!$this->request->post['module_live_options_tax_container']) {
			$this->error['tax_container'] = $this->language->get('error_tax_container');
		}
		if (!$this->request->post['module_live_options_points_container']) {
			$this->error['points_container'] = $this->language->get('error_points_container');
		}
		if (!$this->request->post['module_live_options_reward_container']) {
			$this->error['reward_container'] = $this->language->get('error_reward_container');
		}

		return !$this->error;
	}

	public function install() {
		if ($this->install_validate()) {
			$sql = "ALTER TABLE `" . DB_PREFIX . "product_option_value` CHANGE `price_prefix` `price_prefix` VARCHAR(2) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL";
			$this->db->query($sql);
		}
	}

	public function uninstall() {
		if ($this->install_validate()) {
			$sql = "ALTER TABLE `" . DB_PREFIX . "product_option_value` CHANGE `price_prefix` `price_prefix` VARCHAR(1) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL";
			$this->db->query($sql);
		}
	}

	protected function install_validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/live_options')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
?>