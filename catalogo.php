<?php require __DIR__.'/inc/layout.php';
$st=db()->prepare("SELECT * FROM categories WHERE slug=?");$st->execute([(string)($_GET['c']??'')]);$cat=$st->fetch();
if(!$cat){http_response_code(404);head('Não encontrado');echo '<p>Categoria não encontrada.</p>';foot();exit;}
$q=trim((string)($_GET['q']??''));
$st=db()->prepare("SELECT * FROM services WHERE category_id=? AND active=1 AND (name LIKE ? OR description LIKE ?) ORDER BY id DESC");
$st->execute([$cat['id'],"%$q%","%$q%"]);$rows=$st->fetchAll();
head($cat['name']); ?>
<h1><?=e($cat['name'])?></h1><p class="muted"><?=e($cat['description'])?></p>
<form class="row" style="margin:18px 0"><input type="hidden" name="c" value="<?=e($cat['slug'])?>"><input name="q" value="<?=e($q)?>" placeholder="Buscar serviço"><button class="btn">Buscar</button></form>
<?php if(!$rows): ?><p class="muted">Nenhum serviço disponível no momento.</p><?php endif; ?><div class="grid">
<?php foreach($rows as $s): ?><div class="card"><?php if($s['image']): ?><img src="uploads/<?=e($s['image'])?>" alt="<?=e($s['name'])?>"><?php endif; ?>
<h3><?=e($s['name'])?></h3><p class="muted"><?=e(mb_strimwidth($s['description'],0,140,'…'))?></p>
<p class="price"><?=e(money($s['price']))?></p><?php if($s['deadline']): ?><p class="muted">Prazo: <?=e($s['deadline'])?></p><?php endif; ?>
<a class="btn" href="servico.php?id=<?=(int)$s['id']?>"><?=$s['price']===null?'Solicitar orçamento':'Contratar'?></a></div><?php endforeach; ?></div>
<?php foot(); ?>
