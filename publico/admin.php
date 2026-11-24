<?php
declare(strict_types=1);

/**
 * Panel administrativo de demostración para Colegio Colibrí.
 * Ubicación: publico/admin.php
 * Configure ADMIN_PASSWORD como variable de entorno en el servidor.
 */

$https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
session_name('colibri_admin');
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => $https,
    'httponly' => true,
    'samesite' => 'Strict',
]);
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
session_start();

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: no-referrer');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header("Content-Security-Policy: default-src 'self'; style-src 'self' 'unsafe-inline'; form-action 'self'; base-uri 'self'; frame-ancestors 'none'");

const ARCHIVOS = [
    'citas' => [
        'archivo' => 'citas.csv',
        'titulo' => 'Citas agendadas',
        'columnas' => ['creado_en' => 'Registro', 'radicado' => 'Radicado', 'acudiente' => 'Acudiente', 'telefono' => 'Teléfono', 'correo' => 'Correo', 'tipo' => 'Tipo', 'fecha' => 'Fecha', 'franja' => 'Horario', 'ninos' => 'Niños', 'comentarios' => 'Comentarios'],
    ],
    'prematriculas' => [
        'archivo' => 'prematriculas.csv',
        'titulo' => 'Pre-matrículas',
        'columnas' => ['creado_en' => 'Registro', 'radicado' => 'Radicado', 'nombres' => 'Nombres', 'apellidos' => 'Apellidos', 'nacimiento' => 'Nacimiento', 'grado' => 'Grado', 'acudiente' => 'Acudiente', 'parentesco' => 'Parentesco', 'telefono' => 'Teléfono', 'correo' => 'Correo', 'observaciones' => 'Observaciones'],
    ],
];

function e(mixed $valor): string {
    return htmlspecialchars((string)$valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function tokenCsrf(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return (string)$_SESSION['csrf'];
}
function csrfValido(): bool {
    return isset($_POST['csrf'], $_SESSION['csrf'])
        && is_string($_POST['csrf'])
        && hash_equals((string)$_SESSION['csrf'], $_POST['csrf']);
}
function leerCsvAdmin(string $nombre): array {
    $ruta = dirname(__DIR__) . '/datos/' . $nombre;
    if (!is_file($ruta) || !is_readable($ruta)) return [];
    $h = fopen($ruta, 'rb');
    if ($h === false) return [];
    $filas = [];
    try {
        if (!flock($h, LOCK_SH)) return [];
        $cabecera = fgetcsv($h, 0, ',', '"', '\\');
        if (!is_array($cabecera)) return [];
        while (($valores = fgetcsv($h, 0, ',', '"', '\\')) !== false) {
            if ($valores === [null] || $valores === false) continue;
            $fila = [];
            foreach ($cabecera as $i => $campo) $fila[(string)$campo] = (string)($valores[$i] ?? '');
            $filas[] = $fila;
        }
    } finally {
        flock($h, LOCK_UN);
        fclose($h);
    }
    return array_reverse($filas);
}

$claveConfigurada = getenv('ADMIN_PASSWORD');
$error = '';

if (isset($_GET['salir'])) {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'] ?? '', $p['secure'], $p['httponly']);
    }
    session_destroy();
    header('Location: admin.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_SESSION['admin_ok'])) {
    $intentos = (int)($_SESSION['intentos'] ?? 0);
    $ultimo = (int)($_SESSION['ultimo_intento'] ?? 0);
    if ($intentos >= 8 && time() - $ultimo < 900) {
        $error = 'Demasiados intentos. Espera 15 minutos antes de volver a intentar.';
    } elseif (!csrfValido()) {
        $error = 'La sesión del formulario expiró. Recarga la página e inténtalo nuevamente.';
    } elseif (!is_string($claveConfigurada) || $claveConfigurada === '') {
        $error = 'El panel aún no está configurado. Define la variable ADMIN_PASSWORD en el hosting.';
    } else {
        $ingresada = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
        if (hash_equals($claveConfigurada, $ingresada)) {
            session_regenerate_id(true);
            $_SESSION['admin_ok'] = true;
            $_SESSION['intentos'] = 0;
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
            header('Location: admin.php');
            exit;
        }
        $_SESSION['intentos'] = $intentos + 1;
        $_SESSION['ultimo_intento'] = time();
        $error = 'Contraseña incorrecta.';
    }
}

$autenticado = !empty($_SESSION['admin_ok']);
$seccion = is_string($_GET['ver'] ?? null) && isset(ARCHIVOS[$_GET['ver']]) ? $_GET['ver'] : 'citas';
$registros = $autenticado ? leerCsvAdmin(ARCHIVOS[$seccion]['archivo']) : [];

if ($autenticado && isset($_GET['descargar']) && $_GET['descargar'] === '1') {
    $ruta = dirname(__DIR__) . '/datos/' . ARCHIVOS[$seccion]['archivo'];
    if (!is_file($ruta) || !is_readable($ruta)) {
        http_response_code(404);
        exit('Todavía no hay registros para descargar.');
    }
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . ARCHIVOS[$seccion]['archivo'] . '"');
    header('X-Content-Type-Options: nosniff');
    readfile($ruta);
    exit;
}
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Administración | Colegio Colibrí</title>
<style>
:root{--azul:#173b55;--turquesa:#168f91;--coral:#e97865;--fondo:#f3f7f7;--texto:#263746;--borde:#dce6e8;--blanco:#fff}
*{box-sizing:border-box}body{margin:0;background:var(--fondo);color:var(--texto);font:15px/1.55 system-ui,-apple-system,"Segoe UI",sans-serif}
a{color:var(--turquesa)}.barra{background:var(--azul);color:#fff;padding:18px 24px}.barra-in{max-width:1200px;margin:auto;display:flex;align-items:center;justify-content:space-between;gap:16px}.marca{font-weight:750;font-size:19px}.marca small{display:block;font-size:12px;font-weight:400;opacity:.8}.contenedor{max-width:1200px;margin:34px auto;padding:0 20px}.panel{background:#fff;border:1px solid var(--borde);border-radius:15px;padding:24px;box-shadow:0 8px 28px #173b550c}.login{max-width:440px;margin:8vh auto}.muted{color:#667985}.campo{display:grid;gap:7px;margin:18px 0}.campo label{font-weight:650}.campo input{width:100%;padding:12px 13px;border:1px solid #cbd9dd;border-radius:8px;font:inherit}.campo input:focus{outline:3px solid #168f912c;border-color:var(--turquesa)}button,.btn{display:inline-block;border:0;border-radius:8px;background:var(--turquesa);color:white;padding:11px 17px;font:inherit;font-weight:650;text-decoration:none;cursor:pointer}.btn-sec{background:#eaf3f3;color:var(--azul)}.error{padding:12px 14px;background:#fff0ed;border:1px solid #f1c4bb;color:#8b3023;border-radius:8px}.tabs{display:flex;gap:10px;flex-wrap:wrap;margin:20px 0}.tabs a[aria-current=page]{background:var(--azul);color:#fff}.resumen{display:flex;align-items:center;justify-content:space-between;gap:15px;flex-wrap:wrap;margin:20px 0}.tabla-wrap{overflow:auto;border:1px solid var(--borde);border-radius:10px}table{border-collapse:collapse;width:100%;min-width:850px;background:white}th,td{text-align:left;padding:12px 14px;border-bottom:1px solid var(--borde);vertical-align:top;max-width:280px;overflow-wrap:anywhere}th{background:#edf5f5;color:var(--azul);font-size:13px}tr:last-child td{border-bottom:0}.vacio{text-align:center;padding:35px;color:#667985}.pie{text-align:center;color:#71818a;font-size:12px;margin:24px 0}@media(max-width:600px){.barra-in{align-items:flex-start;flex-direction:column}.contenedor{margin:20px auto;padding:0 12px}.panel{padding:17px}}
</style>
</head>
<body>
<header class="barra"><div class="barra-in"><div class="marca">Colegio Colibrí <small>Panel administrativo · demostración académica</small></div><?php if ($autenticado): ?><a class="btn btn-sec" href="?salir=1">Cerrar sesión</a><?php endif; ?></div></header>
<main class="contenedor">
<?php if (!$autenticado): ?>
<section class="panel login">
<h1>Acceso administrativo</h1>
<p class="muted">Ingresa la contraseña administrativa para consultar los registros de demostración.</p>
<?php if ($error !== ''): ?><p class="error" role="alert"><?= e($error) ?></p><?php endif; ?>
<form method="post" action="admin.php" autocomplete="on">
<input type="hidden" name="csrf" value="<?= e(tokenCsrf()) ?>">
<div class="campo"><label for="password">Contraseña</label><input id="password" name="password" type="password" required autocomplete="current-password" autofocus></div>
<button type="submit">Ingresar</button>
</form>
<?php if (!is_string($claveConfigurada) || $claveConfigurada === ''): ?><p class="muted"><strong>Configuración pendiente:</strong> agrega la variable de entorno <code>ADMIN_PASSWORD</code> en Render. No escribas la contraseña dentro de este archivo.</p><?php endif; ?>
</section>
<?php else: ?>
<section class="panel">
<h1>Registros del colegio</h1>
<p class="muted">Consulta interna de datos ficticios registrados mediante los formularios públicos.</p>
<nav class="tabs" aria-label="Tipos de registro">
<a class="btn btn-sec" href="?ver=citas" <?= $seccion === 'citas' ? 'aria-current="page"' : '' ?>>Citas</a>
<a class="btn btn-sec" href="?ver=prematriculas" <?= $seccion === 'prematriculas' ? 'aria-current="page"' : '' ?>>Pre-matrículas</a>
</nav>
<div class="resumen"><div><h2><?= e(ARCHIVOS[$seccion]['titulo']) ?></h2><span class="muted"><?= count($registros) ?> registro(s), más recientes primero</span></div><a class="btn" href="?ver=<?= e($seccion) ?>&descargar=1">Descargar CSV</a></div>
<div class="tabla-wrap">
<table>
<thead><tr><?php foreach (ARCHIVOS[$seccion]['columnas'] as $etiqueta): ?><th scope="col"><?= e($etiqueta) ?></th><?php endforeach; ?></tr></thead>
<tbody>
<?php if ($registros === []): ?><tr><td class="vacio" colspan="<?= count(ARCHIVOS[$seccion]['columnas']) ?>">Todavía no hay registros. Envía una solicitud ficticia desde el formulario público para probar el panel.</td></tr>
<?php else: foreach ($registros as $fila): ?><tr><?php foreach (ARCHIVOS[$seccion]['columnas'] as $campo => $_): ?><td><?= e($fila[$campo] ?? '') ?></td><?php endforeach; ?></tr><?php endforeach; endif; ?>
</tbody></table>
</div>
</section>
<?php endif; ?>
<p class="pie">Colegio Colibrí · Uso académico con información ficticia. Mantén este panel protegido.</p>
</main>
</body>
</html>
