<?php
declare(strict_types=1);
require_once __DIR__.'/../includes/config.php';
$titulo = 'Inicio';
require_once __DIR__.'/../includes/header.php';
$cifras = [[18,'años enseñando'],[240,'familias felices'],[12,'docentes'],[8,'grados']];
$pilares = [['🎨','Juego creativo','Arte, música y movimiento como puerta al aprendizaje.'],['🌱','Huerta escolar','Cuidamos plantas y aprendemos ciencias con las manos.'],['🤝','Convivencia','Aprendemos a escuchar, compartir y resolver juntos.']];
?>
<section class="portada"><div class="contenedor portada__grid">
  <div><p class="etiqueta">Preescolar y básica primaria · Neiva</p>
  <h1><?= escapar(LEMA) ?></h1>
  <p>En <?= escapar(NOMBRE_COLEGIO) ?> cada niño es un colibrí: curioso, veloz y lleno de color.</p>
  <p><a class="boton boton--coral" href="prematricula.php">Pre-matrícula 2027</a> <a class="boton" href="agenda.php">Agenda una visita</a></p></div>
  <div class="portada__arte" aria-hidden="true"><img src="img/ccolibri.png" alt="" width="560" height="560"></div>
</div></section>
<section class="seccion"><div class="contenedor"><h2>Lo que nos hace distintos</h2>
  <div class="rejilla"><?php foreach ($pilares as [$ico,$t,$d]): ?>
    <article class="tarjeta reveal"><span class="ico"><?= $ico ?></span><h3><?= escapar($t) ?></h3><p><?= escapar($d) ?></p></article>
  <?php endforeach; ?></div></div></section>
<section class="cifras"><div class="contenedor rejilla">
  <?php foreach ($cifras as [$n,$t]): ?><div><strong data-contar="<?= $n ?>">0</strong><span><?= escapar($t) ?></span></div><?php endforeach; ?>
</div></section>
<?php require_once __DIR__.'/../includes/footer.php'; ?>
