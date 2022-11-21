<?php
class ControllerCommonDashboard extends Controller {
	public function index() {
		$this->load->language('common/dashboard');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['user_token'] = $this->session->data['user_token'];

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		// Check install directory exists
		if (is_dir(DIR_CATALOG . '../install')) {
			$data['error_install'] = $this->language->get('error_install');
		} else {
			$data['error_install'] = '';
		}

		// Dashboard Extensions
		$dashboards = array();

		$this->load->model('setting/extension');

		// Get a list of installed modules
		$extensions = $this->model_setting_extension->getInstalled('dashboard');

		// Add all the modules which have multiple settings for each module
		foreach ($extensions as $code) {
			if ($this->config->get('dashboard_' . $code . '_status') && $this->user->hasPermission('access', 'extension/dashboard/' . $code)) {
				$output = $this->load->controller('extension/dashboard/' . $code . '/dashboard');

				if ($output) {
					$dashboards[] = array(
						'code'       => $code,
						'width'      => $this->config->get('dashboard_' . $code . '_width'),
						'sort_order' => $this->config->get('dashboard_' . $code . '_sort_order'),
						'output'     => $output
					);
				}
			}
		}

		$sort_order = array();

		foreach ($dashboards as $key => $value) {
			$sort_order[$key] = $value['sort_order'];
		}

		array_multisort($sort_order, SORT_ASC, $dashboards);

		// Split the array so the columns width is not more than 12 on each row.
		$width = 0;
		$column = array();
		$data['rows'] = array();

		foreach ($dashboards as $dashboard) {
			$column[] = $dashboard;

			$width = ($width + $dashboard['width']);

			if ($width >= 12) {
				$data['rows'][] = $column;

				$width = 0;
				$column = array();
			}
		}

		if (!empty($column)) {
			$data['rows'][] = $column;
		}

		if (DIR_STORAGE == DIR_SYSTEM . 'storage/') {
			$data['security'] = $this->load->controller('common/security');
		} else {
			$data['security'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');


                $this->load->model('module/codemarket_module');
                $conf524 = $this->model_module_codemarket_module->getModulo('524');

                if(!empty($conf524) && !empty($conf524->status) && $conf524->status == 1) {
                    if(!empty($conf524->apiToken)) {
                        $tokenParts = explode('.', trim($conf524->apiToken));
                        if(!empty($tokenParts[1])){
                            $tokenPayload = json_decode(base64_decode($tokenParts[1]));
                            $tokenExpirationDate = date('d/m/Y H:i:s', $tokenPayload->exp);

                            $dateNow = new DateTime("now", new DateTimeZone('America/Sao_Paulo'));
                            $dateNow = $dateNow->format('d/m/Y H:i:s');

                            if(strtotime($dateNow) > $tokenPayload->exp){
                                $tokenExpirationDate = $tokenExpirationDate." Expirado!";
                            }
                        }else{
                            $tokenExpirationDate = 'Token inválido! Crie um novo Token no Melhor Envio';
                        }
                    }else{
                        $tokenExpirationDate = 'Sem Token! Crie um novo Token no Melhor Envio';
                    }

                    $data['code524'] = $tokenExpirationDate;
                }
            
		$this->response->setOutput($this->load->view('common/dashboard', $data));
	}
}
