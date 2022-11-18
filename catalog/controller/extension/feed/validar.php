<?php
class ControllerExtensionFeedValidar extends Controller {
	public function index() {
		$this->load->language('extension/feed/validar');
		if ($this->config->get('module_validar_status')) {
			$check = $this->checkForUpdate();
		    if ($check) {
			        $mail = new Mail($this->config->get('config_mail_engine'));
					$mail->parameter = $this->config->get('config_mail_parameter');
					$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
					$mail->smtp_username = $this->config->get('config_mail_smtp_username');
					$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
					$mail->smtp_port = $this->config->get('config_mail_smtp_port');
					$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

					$mail->setTo($this->config->get('config_email'));
					$mail->setFrom($this->config->get('config_email'));
					$mail->setReplyTo($this->language->get('text_email'));
					$mail->setSender(html_entity_decode($this->language->get('heading_title'), ENT_QUOTES, 'UTF-8'));
					$mail->setSubject(html_entity_decode(sprintf($this->language->get('text_subject'), $this->language->get('heading_title')), ENT_QUOTES, 'UTF-8'));
					$mail->setText($this->language->get('text_info'));
					$mail->send();
		    } else {
				echo $this->language->get('text_update');
			}
		
		} else {
		  echo $this->language->get('text_desabled');
		}

	}

	public function checkForUpdate() {
	    if ($this->config->get('module_validar_status')) {
		$ver = 0;
		$url = base64_decode('aHR0cHM6Ly93d3cub3BlbmNhcnRtYXN0ZXIuY29tLmJyL21vZHVsZS92ZXJzaW9uLw==');
        $json_convert  = array('module' => 'validar');

        $soap_do = curl_init();
        curl_setopt($soap_do, CURLOPT_URL, $url);
        curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($soap_do, CURLOPT_TIMEOUT,        10);
        curl_setopt($soap_do, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($soap_do, CURLOPT_POST,           true );
        curl_setopt($soap_do, CURLOPT_POSTFIELDS,     $json_convert);

        $response = curl_exec($soap_do); 
        curl_close($soap_do);
        $resposta = json_decode($response, true);
		
		if (version_compare($resposta['mensagem'], $this->ver(), '>')) {
        $ver = 1;
        }
		return $ver;
		}
	}
	
	public function ver() {
		$ver = '1.0.8.0';
		return $ver;
	}
}
