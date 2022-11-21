<?php

/**
 * © Copyright 2013-2021 Codemarket - Todos os direitos reservados.
 * Class ModelModuleCodeActivemodule
 */
class ModelModuleCodeActivemodule extends Model
{
    public function index()
    {
        //eventActiveModule

                $log524 = new Log('Code-MelhorEnvio-' . date('m-Y') . '.log');
                $log524->write("eventActiveModule() - Melhor Envio");

                $this->db->query('CREATE TABLE IF NOT EXISTS `' . DB_PREFIX . 'code_melhorenvio` (
                  `oc_code_melhorenvio_id` int(11) NOT NULL AUTO_INCREMENT,
                  `order_id` int(11) DEFAULT NULL,
                  `packages` longtext DEFAULT NULL,
                  `data` longtext DEFAULT NULL,
                  `status` varchar(255) DEFAULT NULL,
                  `date_created` datetime DEFAULT NULL,
                  PRIMARY KEY (`oc_code_melhorenvio_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
                ');

                $this->db->query('CREATE TABLE IF NOT EXISTS `' . DB_PREFIX . 'code_melhorenvio_tracking` (
                  `code_melhorenvio_tracking_id` int(11) NOT NULL AUTO_INCREMENT,
                  `order_id` int(11) DEFAULT NULL,
                  `tracking` longtext DEFAULT NULL,
                  `id` varchar(65) DEFAULT NULL,
                  `order_status_id` varchar(255) DEFAULT NULL,
                  `date_created` datetime DEFAULT NULL,
                  PRIMARY KEY (`code_melhorenvio_tracking_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
                ');

                $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "code_melhorenvio` LIKE 'packages'");
                if (!$query->num_rows) {
                    $this->db->query("ALTER TABLE `" . DB_PREFIX . "code_melhorenvio` ADD (`packages` longtext DEFAULT NULL)");
                }

                $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "code_melhorenvio` LIKE 'status'");
                if (!$query->num_rows) {
                    $this->db->query("ALTER TABLE `" . DB_PREFIX . "code_melhorenvio` ADD (`status` varchar(255) DEFAULT NULL)");
                }

                $this->load->model('module/codemarket_module');
                $c524 = $this->model_module_codemarket_module->getModulo('524');

                $c524_status = (!empty($c524->status)) ? 1 : 0;

                $this->model_module_codemarket_module->addExtensionStore('shipping', 'code_melhorenvio');
                $this->model_module_codemarket_module->editSettingStore('shipping', 'code_melhorenvio', 'code_melhorenvio_status', $c524_status);

                $log524->write("eventActiveModule() - Melhor Envio rodado com sucesso");
            
    }

    public function versionApp()
    {
        $data = [];
        //eventVersionApp

                $data['524'] = [
                    'version' => '5.0',
                    'date' => '15/03/2022'
                ];
            

                $data['1'] = [
                    'version' => '1.1',
                    'date' => '11/03/2021'
                ];
            

        return $data;
    }
}
