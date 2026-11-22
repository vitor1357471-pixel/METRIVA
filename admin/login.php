<?php require __DIR__.'/../inc/layout.php';
$err=null;
if($_SERVER['REQUEST_METHOD']==='POST'){ csrf_check();
 if(($_SESSION['tries']??0)>=5 && time()-($_SESSION['last']??0)<300){ $err='Muitas tentativas. Aguarde 5 minutos.'; }
 else{ $st=db()->prepare("SELECT * FROM users WHERE email=?");$st->execute([strtolower(trim((string)$_POST['email']))]);$u=$st->fetch();
  if($u && password_verify((string)$_POST['pass'],$u['pass_hash'])){ session_regenerate_id(true);$_SESSION=['uid'=>$u['id'],'uname'=>$u['name']];audit('Login');header('Location: index.php');exit; }
  $_SESSION['tries']=($_SESSION['tries']??0)+1;$_SESSION['last']=time();$err='E-mail ou senha incorretos.'; } }
head('Entrar',true); ?>
<h1>Painel administrativo</h1><form method="post" class="card" style="max-width:400px"><?=csrf_field()?>
<label>E-mail<input type="email" name="email" required></label><label>Senha<input type="password" name="pass" required></label>
<?php if($err): ?><p class="err"><?=e($err)?></p><?php endif; ?><p><button class="btn">Entrar</button></p></form><?php foot(true); ?>
