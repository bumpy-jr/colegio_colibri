<?php
declare(strict_types=1);
require_once __DIR__.'/../includes/config.php';
require_once __DIR__.'/../includes/funciones.php';
$titulo = 'Agenda tu cita'; $errores = []; $enviado = false; $radicado = '';
$datos = ['acudiente'=>'','telefono'=>'','correo'=>'','tipo'=>'','fecha'=>'','franja'=>'','ninos'=>'1','comentarios'=>''];
$autoriza = false;
$manana = new DateTimeImmutable('tomorrow'); $limite = $manana->modify('+60 days');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($datos as $c => $_) $datos[$c] = leerTexto($_POST, $c);
    $autoriza = isset($_POST['autorizo']);
    $tel = preg_replace('/[\s-]+/', '', $datos['telefono']) ?? '';
    if (!longitudValida($datos['acudiente'],3,80)) $errores['acudiente'] = 'Escribe tu nombre completo.';
    if (!telefonoValido($tel)) $errores['telefono'] = 'Teléfono de 7 a 10 dígitos, solo números.';
    if (!correoValido($datos['correo'])) $errores['correo'] = 'Escribe un correo válido.';
    if (!array_key_exists($datos['tipo'], TIPOS_CITA)) $errores['tipo'] = 'Selecciona el tipo de cita.';
    $f = leerFecha($datos['fecha']);
    if ($f === null) $errores['fecha'] = 'Selecciona una fecha válida.';
    elseif ($f < $manana || $f > $limite) $errores['fecha'] = 'Elige entre '.$manana->format('d/m/Y').' y '.$limite->format('d/m/Y').'.';
    elseif ((int)$f->format('N') > 5) $errores['fecha'] = 'Atendemos de lunes a viernes.';
    if (!array_key_exists($datos['franja'], FRANJAS)) $errores['franja'] = 'Selecciona una hora.';
    $ninos = filter_var($datos['ninos'], FILTER_VALIDATE_INT, ['options'=>['min_range'=>1,'max_range'=>5]]);
    if ($ninos === false) $errores['ninos'] = 'Indica entre 1 y 5 niños.';
    if (mb_strlen($datos['comentarios']) > 300) $errores['comentarios'] = 'Máximo 300 caracteres.';
    if (!$autoriza) $errores['autorizo'] = 'Debes autorizar el tratamiento de datos.';
    if ($errores === []) {
        $radicado = 'CT-'.date('Ymd').'-'.strtoupper(bin2hex(random_bytes(2)));
        $resultado = reservarCita([date('Y-m-d H:i:s'),$radicado,$datos['acudiente'],$tel,$datos['correo'],$datos['tipo'],$datos['fecha'],$datos['franja'],$ninos,$datos['comentarios']]);
        if ($resultado === 'guardada') $enviado = true;
        elseif ($resultado === 'llena') $errores['franja'] = 'Esa hora ya completó sus '.CITAS_POR_FRANJA.' citas. Elige otra.';
        else $errores['general'] = 'No pudimos guardar la cita. Inténtalo de nuevo.';
    }
}
require_once __DIR__.'/../includes/header.php';
?>
<section class="encabezado-pagina"><div class="contenedor"><h1>Agenda tu cita</h1><p>Ven a conocer el nido. De lunes a viernes.</p></div></section>
<section class="seccion"><div class="contenedor contenedor--estrecho">
<?php if ($enviado): ?>
  <div class="aviso aviso--exito" role="status"><h2>¡Cita confirmada!</h2>
  <p><?= escapar(TIPOS_CITA[$datos['tipo']]) ?> el <strong><?= escapar($datos['fecha']) ?></strong> a las <strong><?= escapar(FRANJAS[$datos['franja']]) ?></strong>.</p>
  <p>Radicado: <strong class="codigo"><?= escapar($radicado) ?></strong></p><a class="boton" href="index.php">Volver al inicio</a></div>
<?php else: ?>
  <aside class="aviso aviso--info aviso--institucional" aria-label="Información importante"><span class="aviso__icono" aria-hidden="true">i</span><div><strong>Formulario de demostración</strong><p>Este sitio se utiliza con fines académicos. Registra únicamente información ficticia. La agenda permite <?= CITAS_POR_FRANJA ?> reservas por horario.</p></div></aside>
  <?= avisos($errores) ?>
  <form class="formulario" method="POST" action="agenda.php" novalidate>
    <fieldset><legend>1. Tus datos</legend>
      <?= campo('text','acudiente','Nombre completo',$datos,$errores,'maxlength="80" required') ?>
      <div class="campo-doble"><?= campo('tel','telefono','Teléfono',$datos,$errores,'inputmode="numeric" required') ?><?= campo('email','correo','Correo',$datos,$errores,'required') ?></div></fieldset>
    <fieldset><legend>2. Tu cita</legend>
      <div class="campo-doble"><?= selector('tipo','Tipo de cita',TIPOS_CITA,$datos,$errores) ?><?= campo('number','ninos','Niños',$datos,$errores,'min="1" max="5"') ?></div>
      <div class="campo-doble"><?= campo('date','fecha','Fecha',$datos,$errores,'min="'.$manana->format('Y-m-d').'" max="'.$limite->format('Y-m-d').'" required') ?><?= selector('franja','Hora',FRANJAS,$datos,$errores) ?></div>
      <?= areaTexto('comentarios','Comentarios',$datos,$errores) ?></fieldset>
    <?= casilla($autoriza,$errores) ?>
    <button class="boton boton--coral" type="submit">Reservar cita</button>
  </form>
<?php endif; ?></div></section>
<?php require_once __DIR__.'/../includes/footer.php'; ?>
