<?php require_once __DIR__.'/bootstrap.php';
function head(string $t,bool $adm=false): void { $B=$adm?'../':''; $cats=db()->query("SELECT slug,name FROM categories ORDER BY sort")->fetchAll(); ?>
<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?=e($t)?> · METRIVA</title><link rel="stylesheet" href="<?=$B?>assets/style.css"></head><body>
<header class="top"><a class="logo" href="<?=$B?>index.php">METRIVA<?=$adm?' <small>admin</small>':''?></a>
<input type="checkbox" id="mn" class="mn"><label for="mn" class="burger" aria-label="Menu">☰</label>
<nav><?php if($adm): ?>
<a href="index.php">Painel</a><a href="servicos.php">Serviços</a><a href="pedidos.php">Pedidos</a><a href="financeiro.php">Financeiro</a><a href="config.php">Configurações</a>
<form method="post" action="logout.php"><?=csrf_field()?><button class="lnk">Sair</button></form>
<?php else: ?><a href="index.php">Início</a><?php foreach($cats as $c): ?><a href="catalogo.php?c=<?=e($c['slug'])?>"><?=e($c['name'])?></a><?php endforeach; ?>
<a href="sobre.php">Sobre</a><a href="contato.php">Contato</a><a href="pedido.php" class="btn sm">Acompanhar pedido</a><?php endif; ?></nav></header><main>
<?php if($m=flash()): ?><div class="flash"><?=e($m)?></div><?php endif;
}
function foot(bool $adm=false): void { $w=preg_replace('/\D/','',setting('whatsapp')); ?></main>
<footer class="foot"><b>METRIVA</b> — Soluções digitais. <?php if(!$adm): ?><a href="contato.php">Contato</a><?php endif; ?></footer>
<?php if(!$adm && $w): ?><a class="wpp" href="https://wa.me/<?=e($w)?>" target="_blank" rel="noopener">WhatsApp</a><?php endif; ?></body></html><?php }
