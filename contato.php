<?php require __DIR__.'/inc/layout.php'; head('Contato'); $w=preg_replace('/\D/','',setting('whatsapp')); ?><h1>Contato</h1>
<?php if($w): ?><p><a class="btn" href="https://wa.me/<?=e($w)?>" target="_blank" rel="noopener">Falar no WhatsApp</a></p><?php endif; ?>
<?php if(setting('email')): ?><p>E-mail: <?=e(setting('email'))?></p><?php endif; ?>
<?php if(!$w&&!setting('email')): ?><p class="muted">Contatos ainda não configurados.</p><?php endif; ?><?php foot(); ?>
