<?php
declare(strict_types=1);
$GLOBALS['cfg'] = require __DIR__.'/../config.php';
ini_set('session.use_strict_mode','1');
session_set_cookie_params(['httponly'=>true,'samesite'=>'Lax','secure'=>!empty($_SERVER['HTTPS'])]);
session_start();
function db(): PDO { static $p; if(!$p){ $c=$GLOBALS['cfg'];
 $p=new PDO("mysql:host={$c['db_host']};dbname={$c['db_name']};charset=utf8mb4",$c['db_user'],$c['db_pass'],[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false]);} return $p; }
function e($s): string { return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8'); }
function csrf(): string { return $_SESSION['t'] ??= bin2hex(random_bytes(32)); }
function csrf_field(): string { return '<input type="hidden" name="_t" value="'.csrf().'">'; }
function csrf_check(): void { if(!hash_equals($_SESSION['t']??'',(string)($_POST['_t']??''))){ http_response_code(419); exit('Sessão expirada. Volte e tente novamente.'); } }
function money($v): string { return $v===null ? 'Sob orçamento' : 'R$ '.number_format((float)$v,2,',','.'); }
function setting(string $k,string $d=''): string { static $s; $s ??= db()->query("SELECT k,v FROM settings")->fetchAll(PDO::FETCH_KEY_PAIR); return $s[$k]??$d; }
function flash(?string $m=null): ?string { if($m!==null){$_SESSION['f']=$m;return null;} $x=$_SESSION['f']??null; unset($_SESSION['f']); return $x; }
function require_admin(): void { if(empty($_SESSION['uid'])){ header('Location: login.php'); exit; } }
function audit(string $a): void { db()->prepare("INSERT INTO audit_log(user_id,action) VALUES(?,?)")->execute([$_SESSION['uid']??null,$a]); }
const STATUS=['aguardando_pagamento'=>'Aguardando pagamento','pagamento_confirmado'=>'Pagamento confirmado','em_andamento'=>'Em andamento','aguardando_informacoes'=>'Aguardando informações','concluido'=>'Concluído','cancelado'=>'Cancelado'];
function period(): array { $p=$_GET['p']??'30'; $t=new DateTime('today');
 switch($p){
  case 'hoje': $a=clone $t; $b=clone $t; break;
  case '7': $a=(clone $t)->modify('-6 days'); $b=clone $t; break;
  case 'mes': $a=new DateTime('first day of this month'); $b=clone $t; break;
  case 'anterior': $a=new DateTime('first day of last month'); $b=new DateTime('last day of last month'); break;
  case 'custom': try{ $a=new DateTime((string)($_GET['de']??'')); $b=new DateTime((string)($_GET['ate']??'')); }catch(Exception $x){ $a=(clone $t)->modify('-29 days'); $b=clone $t; } break;
  default: $a=(clone $t)->modify('-29 days'); $b=clone $t; }
 return [$a->format('Y-m-d').' 00:00:00',$b->format('Y-m-d').' 23:59:59']; }
function upload_image(): ?string { $f=$_FILES['img']??null; if(!$f||$f['error']===UPLOAD_ERR_NO_FILE) return null;
 if($f['error']!==0||$f['size']>2*1024*1024) throw new RuntimeException('Imagem inválida ou acima de 2 MB.');
 $ext=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'][(new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name'])]??null;
 if(!$ext) throw new RuntimeException('Use JPG, PNG ou WebP.');
 $n=bin2hex(random_bytes(8)).'.'.$ext; move_uploaded_file($f['tmp_name'],__DIR__.'/../uploads/'.$n); return $n; }
