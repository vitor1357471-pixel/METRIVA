<?php
$local = __DIR__.'/config.local.php';
return array_merge([
 'db_host'=>getenv('DB_HOST')?:'localhost','db_name'=>getenv('DB_NAME')?:'metriva',
 'db_user'=>getenv('DB_USER')?:'root','db_pass'=>getenv('DB_PASS')?:'',
 'webhook_secret'=>getenv('WEBHOOK_SECRET')?:'',
], is_file($local) ? require $local : []);
