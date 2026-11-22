<?php // ESBOÇO de webhook de pagamento. Adapte ao formato e à assinatura do provedor escolhido.
require __DIR__.'/inc/bootstrap.php';
$secret=$GLOBALS['cfg']['webhook_secret']; $raw=file_get_contents('php://input');
if($secret===''||!hash_equals(hash_hmac('sha256',$raw,$secret),(string)($_SERVER['HTTP_X_SIGNATURE']??''))){ http_response_code(401); exit; }
$d=json_decode($raw,true); // esperado: {"order_code":"MTV-...","amount":100.0,"method":"pix","reference":"...","status":"paid"}
if(($d['status']??'')!=='paid'||empty($d['order_code'])){ http_response_code(200); exit; }
$o=db()->prepare("SELECT id FROM orders WHERE code=?");$o->execute([$d['order_code']]);$id=$o->fetchColumn(); if(!$id){http_response_code(404);exit;}
$dup=db()->prepare("SELECT 1 FROM payments WHERE reference=?");$dup->execute([(string)$d['reference']]); if($dup->fetchColumn()){exit('ok');}
db()->prepare("INSERT INTO payments(order_id,amount,method,reference,paid_at) VALUES(?,?,?,?,NOW())")->execute([$id,(float)$d['amount'],(string)($d['method']??'webhook'),(string)$d['reference']]);
db()->prepare("UPDATE orders SET status='pagamento_confirmado' WHERE id=? AND status='aguardando_pagamento'")->execute([$id]); echo 'ok';
