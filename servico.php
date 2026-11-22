<?php require __DIR__.'/inc/layout.php';
$st=db()->prepare("SELECT s.*,c.name cat FROM services s JOIN categories c ON c.id=s.category_id WHERE s.id=? AND s.active=1");$st->execute([(int)($_GET['id']??0)]);$s=$st->fetch();
if(!$s){http_response_code(404);head('Não encontrado');echo '<p>Serviço indisponível.</p>';foot();exit;}
$err=null;
if($_SERVER['REQUEST_METHOD']==='POST'){ csrf_check();
 $n=trim((string)$_POST['name']);$em=strtolower(trim((string)$_POST['email']));$ph=trim((string)$_POST['phone']);$nt=trim((string)$_POST['notes']);
 if(mb_strlen($n)<3||!filter_var($em,FILTER_VALIDATE_EMAIL)||strlen(preg_replace('/\D/','',$ph))<10||mb_strlen($nt)>2000) $err='Confira nome, e-mail e telefone com DDD.';
 else{ $pdo=db();$pdo->beginTransaction();
  $pdo->prepare("INSERT INTO customers(name,email,phone) VALUES(?,?,?) ON DUPLICATE KEY UPDATE name=VALUES(name),phone=VALUES(phone)")->execute([$n,$em,$ph]);
  $cid=$pdo->prepare("SELECT id FROM customers WHERE email=?");$cid->execute([$em]);$cid=$cid->fetchColumn();
  $code='MTV-'.strtoupper(bin2hex(random_bytes(4)));
  $pdo->prepare("INSERT INTO orders(code,customer_id,service_id,category_id,service_name,price,notes) VALUES(?,?,?,?,?,?,?)")->execute([$code,$cid,$s['id'],$s['category_id'],$s['name'],$s['price'],$nt]);
  $pdo->commit();$_SESSION['novo']=$code;header('Location: pedido.php');exit;}}
head($s['name']); ?>
<span class="tag"><?=e($s['cat'])?></span><h1><?=e($s['name'])?></h1>
<div class="grid"><div><?php if($s['image']): ?><img src="uploads/<?=e($s['image'])?>" alt="" style="width:100%;border-radius:14px"><?php endif; ?>
<p><?=nl2br(e($s['description']))?></p>
<?php if($s['features']): ?><h3>Inclui</h3><ul><?php foreach(array_filter(array_map('trim',explode("\n",$s['features']))) as $f): ?><li><?=e($f)?></li><?php endforeach; ?></ul><?php endif; ?>
<?php if($s['info']): ?><h3>Informações importantes</h3><p class="muted"><?=nl2br(e($s['info']))?></p><?php endif; ?></div>
<form method="post" class="card"><p class="price"><?=e(money($s['price']))?></p><?php if($s['deadline']): ?><p class="muted">Prazo: <?=e($s['deadline'])?></p><?php endif; ?>
<?php if($err): ?><p class="err"><?=e($err)?></p><?php endif; ?><?=csrf_field()?>
<label>Nome<input name="name" required maxlength="120"></label><label>E-mail<input type="email" name="email" required maxlength="120"></label>
<label>Telefone com DDD<input name="phone" required maxlength="30"></label><label>Detalhes do pedido<textarea name="notes" rows="4" maxlength="2000"></textarea></label>
<p><button class="btn">Enviar pedido</button></p></form></div>
<?php foot(); ?>
