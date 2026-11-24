<?php
declare(strict_types=1);
$titulo = 'Inicio';
require_once __DIR__.'/../includes/header.php';

$niveles = [
    ['🌱','Preescolar','3 a 5 años','Jugar, explorar y descubrir el mundo con curiosidad y alegría.'],
    ['📚','Básica primaria','6 a 10 años','Leer, crear, resolver preguntas y aprender en comunidad.'],
    ['🎨','Talleres complementarios','Todas las edades','Arte, música, movimiento y experiencias para descubrir talentos.'],
];
$cifras = [[18,'años de trayectoria'],[240,'familias acompañadas'],[12,'docentes comprometidos']];
?>
<section class="portada"><div class="contenedor portada__grid">
  <div>
    <p class="etiqueta">Preescolar y básica primaria · Neiva</p>
    <h1>Pequeños vuelos, <span class="resaltado">grandes sueños</span></h1>
    <p class="portada__lead">En <?= escapar(NOMBRE_COLEGIO) ?> acompañamos a cada niño y niña con afecto, creatividad y curiosidad para aprender cada día.</p>
    <div class="botones">
      <a class="boton boton--coral" href="prematricula.php">Pre-matrícula 2027</a>
      <a class="boton" href="agenda.php">Agenda una visita</a>
    </div>
  </div>
  <div class="portada__arte"><img src="img/ccolibri.png" alt="Identidad ilustrada del Colegio Colibrí" width="560" height="560"></div>
</div></section>
<section class="banner"><div class="contenedor banner__caja">
  <p><strong>¡Estamos listos para conocerte!</strong> Agenda una visita y descubre nuestra propuesta educativa.</p>
  <a class="boton boton--claro" href="agenda.php">Conócenos</a>
</div></section>
<section class="seccion"><div class="contenedor">
  <h2 class="seccion__titulo">Una comunidad para crecer</h2>
  <p class="seccion__sub">Acompañamos cada etapa con experiencias significativas y aprendizaje activo.</p>
  <div class="rejilla rejilla--3">
    <?php foreach ($niveles as [$icono,$nombre,$edades,$texto]): ?>
      <article class="tarjeta reveal"><span class="ico" aria-hidden="true"><?= $icono ?></span><h3><?= escapar($nombre) ?></h3><p class="tarjeta__dato"><?= escapar($edades) ?></p><p><?= escapar($texto) ?></p></article>
    <?php endforeach; ?>
  </div>
</div></section>
<section class="cifras"><div class="contenedor rejilla rejilla--3">
  <?php foreach ($cifras as [$numero,$texto]): ?>
    <div><strong data-contador="<?= (int)$numero ?>">0</strong><span><?= escapar($texto) ?></span></div>
  <?php endforeach; ?>
</div></section>
<section class="seccion"><div class="contenedor llamado">
  <div><h2>¿Quieres conocer el colegio?</h2><p>Agenda una visita guiada y conversemos sobre el proceso de admisión.</p></div>
  <a class="boton boton--coral" href="agenda.php">Agenda tu cita</a>
</div></section>
<?php require_once __DIR__.'/../includes/footer.php'; ?>
