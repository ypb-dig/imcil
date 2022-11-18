<?php
// HTTP
define('HTTP_SERVER', 'https://homolog.ypb.com.br/imcil/');

// HTTPS
define('HTTPS_SERVER', 'https://homolog.ypb.com.br/imcil/');

// DIR
define('DIR_APPLICATION', '/home/ypbcom/public_html/homolog/imcil/catalog/');
define('DIR_SYSTEM', '/home/ypbcom/public_html/homolog/imcil/system/');
define('DIR_IMAGE', '/home/ypbcom/public_html/homolog/imcil/image/');
define('DIR_WEBHOOK', '/home/ypbcom/public_html/homolog/imcil/webhook/');
define('DIR_STORAGE', '/home/ypbcom/public_html/storage/');
define('DIR_LANGUAGE', DIR_APPLICATION . 'language/');
define('DIR_TEMPLATE', DIR_APPLICATION . 'view/theme/');
define('DIR_CONFIG', DIR_SYSTEM . 'config/');
define('DIR_CACHE', DIR_STORAGE . 'cache/');
define('DIR_DOWNLOAD', DIR_STORAGE . 'download/');
define('DIR_LOGS', DIR_STORAGE . 'logs/');
define('DIR_MODIFICATION', DIR_STORAGE . 'modification/');
define('DIR_SESSION', DIR_STORAGE . 'session/');
define('DIR_UPLOAD', DIR_STORAGE . 'upload/');

// DB
define('DB_DRIVER', 'mysqli');
define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', 'ypbcom_imcil');
define('DB_PASSWORD', 'Y?BSiuuNY+aA');
define('DB_DATABASE', 'ypbcom_imcil');
define('DB_PORT', '3306');
define('DB_PREFIX', 'imc_');