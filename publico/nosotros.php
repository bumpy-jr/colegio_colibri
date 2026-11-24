<?php
declare(strict_types=1);
require_once __DIR__.'/../includes/config.php';
$titulo = 'Nosotros';
require_once __DIR__.'/../includes/header.php';
$valores = ['Curiosidad','Respeto','Alegría','Cuidado del entorno','Responsabilidad'];
$ruta = ['Explorar con los sentidos','Jugar para descubrir','Crear y compartir ideas','Reflexionar y celebrar'];
?>
<section class="encabezado-pagina"><div class="contenedor"><h1>Nosotros</h1><p>Conoce a la comunidad detrás del <?= escapar(NOMBRE_COLEGIO) ?>.</p></div></section>
<section class="seccion"><div class="contenedor rejilla">
  <article class="tarjeta"><h2>Misión</h2><p>Acompañar a niños y niñas en sus primeros años escolares con juego, afecto y rigor.</p></article>
  <article class="tarjeta"><h2>Visión</h2><p>Ser en 2030 un colegio referente del Huila en educación inicial creativa.</p></article>
</div></section>
<section class="seccion seccion--alt"><div class="contenedor rejilla">
  <div><h2>Nuestros valores</h2><ul class="chips"><?php foreach ($valores as $v): ?><li><?= escapar($v) ?></li><?php endforeach; ?></ul></div>
  <div><h2>Ruta pedagógica</h2><ol class="pasos"><?php foreach ($ruta as $r): ?><li><?= escapar($r) ?></li><?php endforeach; ?></ol></div>
</div></section>
<?php require_once __DIR__.'/../includes/footer.php'; ?>
