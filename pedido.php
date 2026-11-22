<?php require __DIR__.'/inc/layout.php';
$o=null;$err=null;$novo=$_SESSION['novo']??null;unset($_SESSION['novo']);
if($_SERVER['REQUEST_METHOD']==='POST'){ csrf_check();
 $st=db()->prepare("SELECT o.*,c.name cname FROM orders o JOIN customers c ON c.id=o.customer_id WHERE o.code=? AND c.email=?");
 $st->execute([strtoupper(trim((string)$_POST['code'])),strtolower(trim((string)$_POST['email']))]);$o=$st->fetch();
 if(!$o)$err='Pedido não encontrado. Confira o código e o e-mail.';}
head('Acompanhar pedido'); ?>
<h1>Acompanhar pedido</h1>
<?php if($novo): ?><div class="flash">Pedido enviado! Seu código é <b><?=e($novo)?></b>. Guarde-o para acompanhar o andamento.<br><?=e(setting('payment_info'))?></div><?php endif; ?>
<form method="post" class="card" style="max-width:460px"><?=csrf_field()?><label>Código do pedido<input name="code" value="<?=e($novo??'')?>" required></label>
<label>E-mail usado no pedido<input type="email" name="email" required></label><?php if($err): ?><p class="err"><?=e($err)?></p><?php endif; ?><p><button class="btn">Consultar</button></p></form>
<?php if($o): $pg=db()->prepare("SELECT amount,method,paid_at FROM payments WHERE order_id=?");$pg->execute([$o['id']]); ?>
<div class="card" style="margin-top:18px"><h3><?=e($o['service_name'])?> — <?=e($o['code'])?></h3>
<p>Status: <span class="tag"><?=e(STATUS[$o['status']])?></span></p><p>Valor: <?=e(money($o['price']))?></p>
<?php foreach($pg as $p): ?><p class="muted">Pagamento de <?=e(money($p['amount']))?> (<?=e($p['method'])?>) em <?=e(date('d/m/Y',strtotime($p['paid_at'])))?></p><?php endforeach; ?></div><?php endif; ?>
<?php foot(); ?>
