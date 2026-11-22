<?php require __DIR__.'/../inc/layout.php'; require_admin(); $pdo=db();
$keys=['whatsapp','email','about','payment_info','p1_name','p1_pct','p2_name','p2_pct'];
if($_SERVER['REQUEST_METHOD']==='POST'){ csrf_check();
 if((float)$_POST['p1_pct']+(float)$_POST['p2_pct']!=100){ flash('Os percentuais dos sócios devem somar 100.'); }
 else{ foreach($keys as $k) $pdo->prepare("INSERT INTO settings(k,v) VALUES(?,?) ON DUPLICATE KEY UPDATE v=VALUES(v)")->execute([$k,trim((string)$_POST[$k])]);audit('Alterou configurações');flash('Configurações salvas.'); }
 header('Location: config.php');exit; }
head('Configurações',true); ?>
<h1>Configurações</h1><form method="post" class="card"><?=csrf_field()?>
<label>WhatsApp (com DDI/DDD, só números)<input name="whatsapp" value="<?=e(setting('whatsapp'))?>"></label><label>E-mail de contato<input name="email" value="<?=e(setting('email'))?>"></label>
<label>Texto “Sobre”<textarea name="about" rows="4"><?=e(setting('about'))?></textarea></label><label>Instruções de pagamento mostradas ao cliente<textarea name="payment_info" rows="3"><?=e(setting('payment_info'))?></textarea></label>
<div class="row"><label>Sócio 1<input name="p1_name" value="<?=e(setting('p1_name'))?>"></label><label>% lucro<input name="p1_pct" value="<?=e(setting('p1_pct'))?>"></label><label>Sócio 2<input name="p2_name" value="<?=e(setting('p2_name'))?>"></label><label>% lucro<input name="p2_pct" value="<?=e(setting('p2_pct'))?>"></label></div>
<p><button class="btn">Salvar</button></p></form><?php foot(true); ?>
