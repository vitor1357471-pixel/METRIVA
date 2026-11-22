<?php require __DIR__.'/../inc/layout.php'; require_admin(); $pdo=db();
if($_SERVER['REQUEST_METHOD']==='POST'){ csrf_check(); $id=(int)$_POST['id'];
 if(($_POST['act']??'')==='status' && isset(STATUS[$_POST['status']??''])){ $pdo->prepare("UPDATE orders SET status=? WHERE id=?")->execute([$_POST['status'],$id]);audit("Pedido #$id → {$_POST['status']}");flash('Status atualizado.'); }
 if(($_POST['act']??'')==='pay'){ $v=(float)str_replace(',','.',str_replace('.','',(string)$_POST['amount']));
  if($v>0){ $pdo->prepare("INSERT INTO payments(order_id,amount,method,reference,paid_at,user_id) VALUES(?,?,?,?,NOW(),?)")->execute([$id,$v,substr(trim((string)$_POST['method']),0,30)?:'outro',substr(trim((string)$_POST['ref']),0,80),$_SESSION['uid']]);
   $pdo->prepare("UPDATE orders SET status='pagamento_confirmado' WHERE id=? AND status='aguardando_pagamento'")->execute([$id]);audit("Pagamento R$ $v no pedido #$id");flash('Pagamento registrado.'); } else flash('Informe um valor maior que zero.'); }
 header('Location: pedidos.php');exit; }
$f=$_GET['s']??'';$rows=$pdo->prepare("SELECT o.*,c.name cname,c.email,c.phone,(SELECT COALESCE(SUM(amount),0) FROM payments WHERE order_id=o.id) paid FROM orders o JOIN customers c ON c.id=o.customer_id WHERE (?='' OR o.status=?) ORDER BY o.id DESC LIMIT 200");$rows->execute([$f,$f]);
head('Pedidos',true); ?>
<h1>Pedidos</h1><form class="row"><select name="s" onchange="this.form.submit()"><option value="">Todos os status</option><?php foreach(STATUS as $k=>$v): ?><option value="<?=$k?>" <?=$f===$k?'selected':''?>><?=e($v)?></option><?php endforeach; ?></select></form>
<?php foreach($rows as $o): ?><div class="card" style="margin-top:14px"><b><?=e($o['code'])?></b> — <?=e($o['service_name'])?> — <?=e(money($o['price']))?> <span class="muted">(recebido: <?=e(money($o['paid']))?>)</span>
<p class="muted"><?=e($o['cname'])?> · <?=e($o['email'])?> · <?=e($o['phone'])?> · <?=e(date('d/m/Y H:i',strtotime($o['created_at'])))?></p><?php if($o['notes']): ?><p><?=nl2br(e($o['notes']))?></p><?php endif; ?>
<form method="post" class="row"><?=csrf_field()?><input type="hidden" name="id" value="<?=$o['id']?>"><input type="hidden" name="act" value="status"><select name="status"><?php foreach(STATUS as $k=>$v): ?><option value="<?=$k?>" <?=$o['status']===$k?'selected':''?>><?=e($v)?></option><?php endforeach; ?></select><button class="btn sm">Atualizar status</button></form>
<form method="post" class="row" style="margin-top:8px"><?=csrf_field()?><input type="hidden" name="id" value="<?=$o['id']?>"><input type="hidden" name="act" value="pay"><input name="amount" placeholder="Valor pago" required><input name="method" placeholder="Pix, cartão…"><input name="ref" placeholder="Referência"><button class="btn sm">Registrar pagamento</button></form></div><?php endforeach; ?>
<?php foot(true); ?>
