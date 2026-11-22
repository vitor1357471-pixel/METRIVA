<?php require __DIR__.'/inc/layout.php';
$cats=db()->query("SELECT c.*,(SELECT COUNT(*) FROM services s WHERE s.category_id=c.id AND s.active=1) n FROM categories c ORDER BY sort")->fetchAll();
head('Início'); ?>
<section class="hero"><h1>Seu próximo passo no digital começa aqui.</h1>
<p>Encontre soluções digitais para impulsionar seus projetos, negócios e presença online.</p>
<a class="btn" href="#categorias">Conhecer os serviços</a></section>
<h2 id="categorias">O que a Metriva oferece</h2><div class="grid">
<?php foreach($cats as $c): ?><div class="card"><h3><?=e($c['name'])?></h3><p class="muted"><?=e($c['description'])?></p>
<p class="muted"><?=(int)$c['n']?> serviço(s) disponível(is)</p><a class="btn" href="catalogo.php?c=<?=e($c['slug'])?>">Ver serviços</a></div><?php endforeach; ?></div>
<h2>Por que contratar a Metriva</h2><div class="grid">
<div class="card"><h3>Preço claro</h3><p class="muted">Você vê valores e prazos antes de enviar o pedido.</p></div>
<div class="card"><h3>Acompanhamento</h3><p class="muted">Cada pedido tem um código para consultar o andamento.</p></div>
<div class="card"><h3>Serviços dentro das regras</h3><p class="muted">Trabalhamos com configuração, consultoria e gestão, em conformidade com os termos das plataformas.</p></div></div>
<h2>Como funciona</h2><div class="grid steps">
<div class="card"><h3>Escolha</h3><p class="muted">Selecione a categoria e o serviço.</p></div>
<div class="card"><h3>Envie o pedido</h3><p class="muted">Preencha seus dados e receba o código.</p></div>
<div class="card"><h3>Pague</h3><p class="muted">Nossa equipe envia as instruções de pagamento.</p></div>
<div class="card"><h3>Acompanhe</h3><p class="muted">Consulte o status até a conclusão.</p></div></div>
<h2>Perguntas frequentes</h2>
<details><summary>Como acompanho meu pedido?</summary><p class="muted">Use o código recebido e o e-mail informado em “Acompanhar pedido”.</p></details>
<details><summary>Como é feito o pagamento?</summary><p class="muted"><?=e(setting('payment_info'))?></p></details>
<details><summary>Vocês vendem contas de Instagram ou BMs?</summary><p class="muted">Não. Oferecemos configuração, consultoria, gestão e suporte.</p></details>
<?php foot(); ?>
