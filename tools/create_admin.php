<?php // Uso: php tools/create_admin.php "Nome" email@dominio.com
if(PHP_SAPI!=='cli') exit('Apenas via linha de comando.');
require __DIR__.'/../inc/bootstrap.php';
[$_,$n,$em]=$argv+[null,null,null]; if(!$n||!filter_var($em,FILTER_VALIDATE_EMAIL)) exit("Uso: php tools/create_admin.php \"Nome\" email\n");
echo "Senha (mín. 10 caracteres): "; $p=trim(fgets(STDIN)); if(strlen($p)<10) exit("Senha curta.\n");
db()->prepare("INSERT INTO users(name,email,pass_hash) VALUES(?,?,?)")->execute([$n,strtolower($em),password_hash($p,PASSWORD_DEFAULT)]); echo "Admin criado.\n";
