<?php require __DIR__.'/../inc/layout.php'; require_admin(); $pdo=db();
if($_SERVER['REQUEST_METHOD']==='POST'){ csrf_check(); $act=$_POST['act']??''; $id=(int)($_POST['id']??0);
 try{
  if($act==='toggle'){ $pdo->prepare("UPDATE services SET active=1-active WHERE id=?")->execute([$id]);audit("Alternou serviço #$id");}
  elseif($act==='delete'){ $pdo->prepare("DELETE FROM services WHERE id=?")->execute([$id]);audit("Excluiu serviço #$id");flash('Serviço excluído. Pedidos antigos continuam no histórico.');}
  elseif($act==='save'){
   $n=trim((string)$_POST['name']);$d=trim((string)$_POST['description']);$cid=(int)$_POST['category_id'];
   $pr=trim((string)$_POST['price']);$pr=$pr===''?null:(float)str_replace(',','.',str_replace('.','',$pr));
   if($n===''||$d===''||$cid<1||($pr!==null&&$pr<0)) throw new RuntimeException('Preencha nome, descrição e categoria; preço não pode ser negativo.');
   $img=upload_image();$f=[$cid,$n,$d,trim((string)$_POST['features']),trim((string)$_POST['info']),$pr,trim((string)$_POST['deadline']),isset($_POST['active'])?1:0];
   if($id){ $pdo->prepare("UPDATE services SET category_id=?,name=?,description=?,features=?,info=?,price=?,deadline=?,active=?".($img?",image=?":"")." WHERE id=?")->execute(array_merge($f,$img?[$img]:[],[$id]));audit("Editou serviço #$id");}
   else{ $pdo->prepare("INSERT INTO services(category_id,name,description,features,info,price,deadline,active,image) VALUES(?,?,?,?,?,?,?,?,?)")->execute(array_merge($f,[$img]));audit("Criou serviço $n");}
   flash('Serviço salvo.'); }
 }catch(RuntimeException $x){flash($x->getMessage());}
 header('Location: servicos.php');exit; }
$edit=null;if(isset($_GET['edit'])){$s=$pdo->prepare("SELECT * FROM services WHERE id=?");$s->execute([(int)$_GET['edit']]);$edit=$s->fetch();}
$cats=$pdo->query("SELECT id,name FROM categories ORDER BY sort")->fetchAll();
$rows=$pdo->query("SELECT s.*,c.name cat FROM services s JOIN categories c ON c.id=s.category_id ORDER BY s.id DESC")->fetchAll();
head('Serviços',true); ?>
<h1><?=$edit?'Editar serviço':'Novo serviço'?></h1>
<form method="post" enctype="multipart/form-data" class="card"><?=csrf_field()?><input type="hidden" name="act" value="save"><input type="hidden" name="id" value="<?=(int)($edit['id']??0)?>">
<label>Categoria<select name="category_id"><?php foreach($cats as $c): ?><option value="<?=$c['id']?>" <?=($edit['category_id']??0)==$c['id']?'selected':''?>><?=e($c['name'])?></option><?php endforeach; ?></select></label>
<label>Nome<input name="name" required value="<?=e($edit['name']??'')?>"></label><label>Descrição<textarea name="description" rows="3" required><?=e($edit['description']??'')?></textarea></label>
<label>Recursos incluídos (um por linha)<textarea name="features" rows="3"><?=e($edit['features']??'')?></textarea></label><label>Informações importantes<textarea name="info" rows="2"><?=e($edit['info']??'')?></textarea></label>
<div class="row"><label>Preço (vazio = sob orçamento)<input name="price" value="<?=e(isset($edit['price'])?number_format((float)$edit['price'],2,',','.'):'')?>"></label><label>Prazo<input name="deadline" value="<?=e($edit['deadline']??'')?>"></label><label>Imagem (JPG/PNG/WebP, até 2 MB)<input type="file" name="img" accept="image/*"></label></div>
<label><input type="checkbox" name="active" style="width:auto" <?=($edit['active']??1)?'checked':''?>> Ativo no site</label><p><button class="btn">Salvar</button> <?php if($edit): ?><a class="btn ghost" href="servicos.php">Cancelar</a><?php endif; ?></p></form>
<h2>Cadastrados</h2><div class="tw"><table><tr><th>Serviço</th><th>Categoria</th><th>Preço</th><th>Status</th><th></th></tr>
<?php foreach($rows as $r): ?><tr><td><?=e($r['name'])?></td><td><?=e($r['cat'])?></td><td><?=e(money($r['price']))?></td><td><?=$r['active']?'Ativo':'Inativo'?></td><td>
<a href="?edit=<?=$r['id']?>">Editar</a>
<form method="post" style="display:inline"><?=csrf_field()?><input type="hidden" name="id" value="<?=$r['id']?>"><button class="lnk" name="act" value="toggle"><?=$r['active']?'Desativar':'Ativar'?></button>
<button class="lnk" name="act" value="delete" onclick="return confirm('Excluir este serviço?')">Excluir</button></form></td></tr><?php endforeach; ?></table></div>
<?php foot(true); ?>
