<?php
declare(strict_types=1);
require_once __DIR__.'/../includes/config.php';
$titulo = 'Experiencias';
require_once __DIR__.'/../includes/header.php';
$cats = ['todas'=>'Todas','arte'=>'Arte','ciencia'=>'Ciencia','deporte'=>'Deporte'];
$exp = [['arte','🎭','Teatro de títeres','Creamos personajes y contamos historias.'],['arte','🎶','Taller de ritmos','Tambores, flautas y canciones del Huila.'],['ciencia','🌿','Huerta viva','Sembramos, regamos y cosechamos.'],['ciencia','🔬','Laboratorio pequeño','Experimentos seguros con agua y colores.'],['deporte','⚽','Juegos cooperativos','Movernos sin que nadie quede por fuera.'],['deporte','🧘','Yoga para niños','Respirar, estirar y calmar la mente.']];
?>
<section class="encabezado-pagina"><div class="contenedor"><h1>Experiencias</h1><p>Aprender también es vivir aventuras.</p></div></section>
<section class="seccion"><div class="contenedor">
  <div class="filtros" role="group" aria-label="Filtrar"><?php foreach ($cats as $k => $t): ?><button type="button" data-filtro="<?= $k ?>"<?= $k==='todas'?' class="activo"':'' ?>><?= escapar($t) ?></button><?php endforeach; ?></div>
  <div class="rejilla"><?php foreach ($exp as [$c,$i,$t,$d]): ?>
    <article class="tarjeta reveal" data-cat="<?= $c ?>"><span class="ico"><?= $i ?></span><h3><?= escapar($t) ?></h3><p><?= escapar($d) ?></p></article>
  <?php endforeach; ?></div></div></section>
<?php require_once __DIR__.'/../includes/footer.php'; ?>
