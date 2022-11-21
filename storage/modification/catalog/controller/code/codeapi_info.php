<?php

/**
 * © Copyright 2013-2021 Codemarket - Todos os direitos reservados.
 * Class ControllerCodeCodeApiInfo
 */
class ControllerCodeCodeApiInfo extends Controller
{

                // Verifica o Status do Melhor Envio - index.php?route=code/codeapi_info/verifyStatusMelhorEnvio&token=
                public function verifyStatusMelhorEnvio(){
                    $this->load->model('module/codemarket_module');
                    $c524 = $this->model_module_codemarket_module->getModulo('524');

                    if (empty($c524->code_token) || $c524->code_token != $this->request->get['token']) {
                        exit("Informe um Token válido!");
                    }

                    $c524_status = (!empty($c524->status)) ? 1 : 0;

                    $this->model_module_codemarket_module->addExtensionStore('shipping', 'code_melhorenvio');
                    $this->model_module_codemarket_module->editSettingStore('shipping', 'code_melhorenvio', 'code_melhorenvio_status', $c524_status);
                    echo "<h1>Verificado com sucesso o Status do Melhor Envio, Status: ".$c524_status ."</h1>";
                }
            
    public function index()
    {
        $opencartVersion = VERSION;
        $phpVersion = PHP_VERSION;
        $curlVersion = curl_version();

        header('Content-Type: application/json');

        if (ob_get_level() > 0) {
            ob_flush();
        }

        echo json_encode(
            [
                'opencartVersion' => $opencartVersion,
                'phpVersion'      => $phpVersion,
                'curlVersion'     => $curlVersion['version'],
                'pdo'             => !empty(class_exists('PDO')) ? true : false,
            ]
        );

        exit();
    }

    public function verifyPDO()
    {
        if (class_exists('PDO') != 1) {
            echo "Sem extensão PDO no PHP! - Instale a extensão PDO e php_mysqli pelo Cpanel ou verifique com sua hospedagem";
        } else {
            echo "Extensão PDO instalada";
        }
    }
}
