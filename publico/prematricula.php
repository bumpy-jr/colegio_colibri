<?php
declare(strict_types=1);
require_once __DIR__.'/../includes/config.php';
require_once __DIR__.'/../includes/funciones.php';
$titulo = 'Pre-matrícula'; $errores = []; $enviado = false; $radicado = '';
$datos = ['nombres'=>'','apellidos'=>'','nacimiento'=>'','grado'=>'','acudiente'=>'','parentesco'=>'','telefono'=>'','correo'=>'','observaciones'=>''];
$autoriza = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($datos as $c => $_) $datos[$c] = leerTexto($_POST, $c);
    $autoriza = isset($_POST['autorizo']);
    $tel = preg_replace('/[\s-]+/', '', $datos['telefono']) ?? '';
    if (!longitudValida($datos['nombres'],2,60)) $errores['nombres'] = 'Escribe los nombres (2 a 60 letras).';
    if (!longitudValida($datos['apellidos'],2,60)) $errores['apellidos'] = 'Escribe los apellidos (2 a 60 letras).';
    $nac = leerFecha($datos['nacimiento']);
    if ($nac === null) $errores['nacimiento'] = 'Selecciona una fecha válida.';
    elseif ($nac > new DateTimeImmutable('today')) $errores['nacimiento'] = 'La fecha no puede ser futura.';
    elseif (($e = calcularEdad($nac)) < 2 || $e > 11) $errores['nacimiento'] = 'Atendemos niños entre 2 y 11 años.';
    if (!array_key_exists($datos['grado'], GRADOS)) $errores['grado'] = 'Selecciona el grado.';
    if (!longitudValida($datos['acudiente'],3,80)) $errores['acudiente'] = 'Escribe el nombre completo del acudiente.';
    if (!in_array($datos['parentesco'], PARENTESCOS, true)) $errores['parentesco'] = 'Selecciona el parentesco.';
    if (!telefonoValido($tel)) $errores['telefono'] = 'Teléfono de 7 a 10 dígitos, solo números.';
    if (!correoValido($datos['correo'])) $errores['correo'] = 'Escribe un correo válido.';
    if (mb_strlen($datos['observaciones']) > 300) $errores['observaciones'] = 'Máximo 300 caracteres.';
    if (!$autoriza) $errores['autorizo'] = 'Debes autorizar el tratamiento de datos.';
    if ($errores === []) {
        $radicado = 'PM-'.date('Ymd').'-'.strtoupper(bin2hex(random_bytes(2)));
        if (guardarPrematricula([date('Y-m-d H:i:s'),$radicado,$datos['nombres'],$datos['apellidos'],$datos['nacimiento'],$datos['grado'],$datos['acudiente'],$datos['parentesco'],$tel,$datos['correo'],$datos['observaciones']])) $enviado = true;
        else $errores['general'] = 'No pudimos guardar la solicitud. Inténtalo de nuevo.';
    }
}
require_once __DIR__.'/../includes/header.php';
?>
<section class="encabezado-pagina"><div class="contenedor"><h1>Pre-matrícula 2027</h1><p>Cuéntanos de tu pequeño colibrí.</p></div></section>
<section class="seccion"><div class="contenedor contenedor--estrecho">
<?php if ($enviado): ?>
  <div class="aviso aviso--exito" role="status"><h2>¡Solicitud recibida!</h2>
  <p>Gracias, <strong><?= escapar($datos['acudiente']) ?></strong>. Registramos a <strong><?= escapar($datos['nombres'].' '.$datos['apellidos']) ?></strong>.</p>
  <p>Radicado: <strong class="codigo"><?= escapar($radicado) ?></strong></p><a class="boton" href="index.php">Volver al inicio</a></div>
<?php else: ?>
  <aside class="aviso aviso--info aviso--institucional" aria-label="Información importante"><span class="aviso__icono" aria-hidden="true">i</span><div><strong>Formulario de demostración</strong><p>Este sitio se utiliza con fines académicos. Para realizar pruebas, ingresa únicamente información ficticia.</p></div></aside>
  <?= avisos($errores) ?>
  <form class="formulario" method="POST" action="prematricula.php" novalidate>
    <fieldset><legend>1. El niño o la niña</legend>
      <?= campo('text','nombres','Nombres',$datos,$errores,'maxlength="60" required') ?>
      <?= campo('text','apellidos','Apellidos',$datos,$errores,'maxlength="60" required') ?>
      <div class="campo-doble"><?= campo('date','nacimiento','Fecha de nacimiento',$datos,$errores,'required') ?><?= selector('grado','Grado al que aspira',GRADOS,$datos,$errores) ?></div></fieldset>
    <fieldset><legend>2. El acudiente</legend>
      <?= campo('text','acudiente','Nombre completo',$datos,$errores,'maxlength="80" required') ?>
      <div class="campo-doble"><?= selector('parentesco','Parentesco',PARENTESCOS,$datos,$errores) ?><?= campo('tel','telefono','Teléfono',$datos,$errores,'inputmode="numeric" placeholder="3001234567" required') ?></div>
      <?= campo('email','correo','Correo electrónico',$datos,$errores,'placeholder="nombre@correo.com" required') ?>
      <?= areaTexto('observaciones','Observaciones',$datos,$errores) ?></fieldset>
    <?= casilla($autoriza,$errores) ?>
    <button class="boton boton--coral" type="submit">Enviar pre-matrícula</button>
  </form>
<?php endif; ?></div></section>
<?php require_once __DIR__.'/../includes/footer.php'; ?>
