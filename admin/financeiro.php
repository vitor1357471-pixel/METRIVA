<?php require __DIR__.'/../inc/layout.php'; require_admin(); $pdo=db();
if($_SERVER['REQUEST_METHOD']==='POST'){ csrf_check();
 if(($_POST['act']??'')==='del'){ $pdo->prepare("DELETE FROM expenses WHERE id=?")->execute([(int)$_POST['id']]);audit('Excluiu despesa #'.(int)$_POST['id']); }
 else{ $v=(float)str_replace(',','.',str_replace('.','',(string)$_POST['amount']));$d=trim((string)$_POST['description']);$dt=(string)$_POST['spent_at'];
  if($v>0&&$d!==''&&strtotime($dt)){ $pdo->prepare("INSERT INTO expenses(description,amount,spent_at,user_id) VALUES(?,?,?,?)")->execute([$d,$v,$dt,$_SESSION['uid']]);audit("Despesa $d R$ $v");flash('Despesa cadastrada.'); } else flash('Preencha descrição, valor e data.'); }
 header('Location: financeiro.php');exit; }
[$a,$b]=period();$s=$pdo->prepare("SELECT COALESCE(SUM(amount),0) FROM payments WHERE paid_at BETWEEN ? AND ?");$s->execute([$a,$b]);$in=(float)$s->fetchColumn();
$s=$pdo->prepare("SELECT * FROM expenses WHERE spent_at BETWEEN ? AND ? ORDER BY spent_at DESC");$s->execute([$a,$b]);$ex=$s->fetchAll();$out=array_sum(array_column($ex,'amount'));$net=$in-$out;
head('Financeiro',true); ?>
<h1>Financeiro</h1><form class="row"><select name="p" onchange="this.form.submit()"><?php foreach(['hoje'=>'Hoje','7'=>'Últimos 7 dias','30'=>'Últimos 30 dias','mes'=>'Este mês','anterior'=>'Mês anterior'] as $k=>$v): ?><option value="<?=$k?>" <?=($_GET['p']??'30')==(string)$k?'selected':''?>><?=$v?></option><?php endforeach; ?></select></form>
<div class="kpis" style="margin-top:14px"><div class="card kpi"><span class="muted">Entradas</span><b><?=e(money($in))?></b></div><div class="card kpi"><span class="muted">Despesas</span><b><?=e(money($out))?></b></div><div class="card kpi"><span class="muted">Resultado líquido</span><b><?=e(money($net))?></b></div></div>
<h2>Distribuição de lucros</h2><div class="kpis"><?php foreach([1,2] as $i): $pc=(float)setting("p{$i}_pct"); ?><div class="card kpi"><span class="muted"><?=e(setting("p{$i}_name"))?> (<?=e($pc)?>%)</span><b><?=e(money(max(0,$net)*$pc/100))?></b></div><?php endforeach; ?></div>
<p class="muted">Percentuais configuráveis em Configurações. Sem lucro no período, a distribuição é zero.</p>
<h2>Nova despesa</h2><form method="post" class="card row"><?=csrf_field()?><input name="description" placeholder="Descrição" required><input name="amount" placeholder="Valor" required><input type="date" name="spent_at" value="<?=date('Y-m-d')?>" required><button class="btn">Cadastrar</button></form>
<h2>Despesas do período</h2><div class="tw"><table><?php foreach($ex as $x): ?><tr><td><?=e(date('d/m/Y',strtotime($x['spent_at'])))?></td><td><?=e($x['description'])?></td><td><?=e(money($x['amount']))?></td><td><form method="post"><?=csrf_field()?><input type="hidden" name="act" value="del"><input type="hidden" name="id" value="<?=$x['id']?>"><button class="lnk" onclick="return confirm('Excluir?')">Excluir</button></form></td></tr><?php endforeach; ?></table></div>
<?php foot(true); ?>
