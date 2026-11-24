<?php
declare(strict_types=1);
$titulo = 'Experiencias';
require_once __DIR__.'/../includes/header.php';

$categorias = [
    'todas' => 'Todas',
    'arte' => 'Arte',
    'ciencia' => 'Ciencia',
    'huerta' => 'Huerta',
    'musica' => 'Música',
];
$experiencias = [
    ['arte','🎨','Mural de los sueños','Creamos una obra colectiva para expresar ideas, emociones y sueños.'],
    ['ciencia','🔬','Laboratorio de burbujas','Exploramos el agua, el jabón y la luz mediante experiencias seguras.'],
    ['huerta','🌱','Nuestra huerta escolar','Sembramos, cuidamos y observamos los ciclos de la naturaleza.'],
    ['musica','🎵','Festival de la canción','Descubrimos ritmos, sonidos y canciones de nuestra región.'],
    ['ciencia','🚀','Feria de inventos','Construimos prototipos sencillos con materiales reutilizables.'],
    ['arte','🎭','Teatro de títeres','Inventamos personajes y narramos historias en grupo.'],
    ['huerta','🐝','Pequeños guardianes','Aprendemos por qué los polinizadores son importantes para el ambiente.'],
    ['musica','🥁','Taller de percusión','Creamos ritmos con instrumentos y objetos cotidianos.'],
];
?>
<section class="encabezado-pagina"><div class="contenedor">
  <p class="etiqueta">Aprender haciendo</p><h1>Experiencias</h1>
  <p>Proyectos, exploración y momentos para descubrir talentos.</p>
</div></section>
<section class="seccion"><div class="contenedor">
  <h2>Un mundo de descubrimientos</h2>
  <p>Explora algunas de las actividades que enriquecen nuestra propuesta pedagógica.</p>
  <div class="filtros" role="group" aria-label="Filtrar experiencias por categoría">
    <?php foreach ($categorias as $clave => $nombre): ?>
      <button type="button" class="filtro<?= $clave === 'todas' ? ' filtro--activo' : '' ?>"
        data-filtro="<?= escapar($clave) ?>" aria-pressed="<?= $clave === 'todas' ? 'true' : 'false' ?>">
        <?= escapar($nombre) ?>
      </button>
    <?php endforeach; ?>
  </div>
  <div class="rejilla rejilla--4" id="listaExperiencias">
    <?php foreach ($experiencias as [$categoria,$icono,$tituloExp,$texto]): ?>
      <article class="tarjeta tarjeta--experiencia" data-categoria="<?= escapar($categoria) ?>">
        <span class="ico" aria-hidden="true"><?= $icono ?></span>
        <span class="insignia"><?= escapar($categorias[$categoria]) ?></span>
        <h3><?= escapar($tituloExp) ?></h3><p><?= escapar($texto) ?></p>
      </article>
    <?php endforeach; ?>
  </div>
  <p class="vacio" id="mensajeVacio" hidden>No hay experiencias en esta categoría todavía.</p>
</div></section>
<?php require_once __DIR__.'/../includes/footer.php'; ?>
