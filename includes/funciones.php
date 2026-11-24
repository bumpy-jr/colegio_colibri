<?php
declare(strict_types=1);

/** Utilidades comunes, validación y almacenamiento CSV fuera de la carpeta pública. */
function escapar(string $texto): string {
    return htmlspecialchars($texto, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function leerTexto(array $origen, string $clave): string {
    $valor = $origen[$clave] ?? '';
    return is_string($valor) ? trim($valor) : '';
}
function longitudValida(string $texto, int $minimo, int $maximo): bool {
    $longitud = function_exists('mb_strlen') ? mb_strlen($texto, 'UTF-8') : strlen($texto);
    return $longitud >= $minimo && $longitud <= $maximo;
}
function correoValido(string $correo): bool {
    return filter_var($correo, FILTER_VALIDATE_EMAIL) !== false && strlen($correo) <= 254;
}
function telefonoValido(string $telefono): bool {
    return preg_match('/^[0-9]{7,10}$/', $telefono) === 1;
}
function leerFecha(string $texto): ?DateTimeImmutable {
    if ($texto === '') return null;
    $fecha = DateTimeImmutable::createFromFormat('!Y-m-d', $texto);
    $errores = DateTimeImmutable::getLastErrors();
    if ($fecha === false || ($errores !== false && ($errores['warning_count'] > 0 || $errores['error_count'] > 0))) return null;
    return $fecha->format('Y-m-d') === $texto ? $fecha : null;
}
function calcularEdad(DateTimeImmutable $nacimiento): int {
    return $nacimiento->diff(new DateTimeImmutable('today'))->y;
}
function esPaginaActual(string $archivo): bool {
    return basename($_SERVER['SCRIPT_NAME'] ?? '') === $archivo;
}
function errorCampo(array $errores, string $campo): string {
    return isset($errores[$campo])
        ? '<p class="error" id="error-'.escapar($campo).'">'.escapar($errores[$campo]).'</p>'
        : '';
}
function ariaError(array $errores, string $id): string {
    return isset($errores[$id]) ? ' aria-invalid="true" aria-describedby="error-'.escapar($id).'"' : '';
}
function campo(string $tipo, string $id, string $etiqueta, array $datos, array $errores, string $extra = ''): string {
    return '<div class="campo"><label for="'.escapar($id).'">'.escapar($etiqueta).'</label>'
        .'<input type="'.escapar($tipo).'" id="'.escapar($id).'" name="'.escapar($id).'" value="'.escapar($datos[$id] ?? '').'" '.$extra.ariaError($errores,$id).'>'
        .errorCampo($errores,$id).'</div>';
}
function selector(string $id, string $etiqueta, array $opciones, array $datos, array $errores): string {
    $html = '<div class="campo"><label for="'.escapar($id).'">'.escapar($etiqueta).'</label><select id="'.escapar($id).'" name="'.escapar($id).'"'.ariaError($errores,$id).'><option value="">Selecciona…</option>';
    foreach ($opciones as $clave => $nombre) {
        $valor = array_is_list($opciones) ? (string)$nombre : (string)$clave;
        $html .= '<option value="'.escapar($valor).'"'.($valor === ($datos[$id] ?? '') ? ' selected' : '').'>'.escapar((string)$nombre).'</option>';
    }
    return $html.'</select>'.errorCampo($errores,$id).'</div>';
}
function areaTexto(string $id, string $etiqueta, array $datos, array $errores): string {
    return '<div class="campo"><label for="'.escapar($id).'">'.escapar($etiqueta).' (opcional)</label><textarea id="'.escapar($id).'" name="'.escapar($id).'" rows="3" maxlength="300"'.ariaError($errores,$id).'>'.escapar($datos[$id] ?? '').'</textarea>'.errorCampo($errores,$id).'</div>';
}
function casilla(bool $marcada, array $errores): string {
    return '<div class="campo campo-consentimiento"><label class="check" for="autorizo">'
        .'<input type="checkbox" id="autorizo" name="autorizo" value="1"'.($marcada ? ' checked' : '').' required'.ariaError($errores,'autorizo').'>'
        .'<span>Confirmo que utilizo datos ficticios y autorizo su tratamiento exclusivamente para este ejercicio académico.</span>'
        .'</label>'.errorCampo($errores,'autorizo').'</div>';
}
function avisos(array $errores): string {
    if (!$errores) return '';
    if (isset($errores['general'])) return '<div class="aviso aviso--error" role="alert">'.escapar($errores['general']).'</div>';
    return '<div class="aviso aviso--error" role="alert" tabindex="-1">Revisa los campos marcados: '.count($errores).' dato(s) por corregir.</div>';
}

/** Protege CSV frente a fórmulas interpretadas por hojas de cálculo. */
function celdaSegura(string|int $valor): string {
    $texto = (string)$valor;
    if ($texto !== '' && preg_match('/^[\s\x00-\x1F]*[=+\-@]/u', $texto)) return "'".$texto;
    return $texto;
}
function rutaDatos(string $archivo): string {
    $directorio = dirname(__DIR__).'/datos';
    if (!is_dir($directorio) && !mkdir($directorio, 0770, true) && !is_dir($directorio)) {
        throw new RuntimeException('No fue posible crear el directorio de datos.');
    }
    return $directorio.'/'.$archivo;
}
/** Agrega una fila con bloqueo exclusivo y cabecera; nunca expone el CSV desde /publico. */
function agregarCsv(string $archivo, array $cabecera, array $fila, ?callable $antesDeGuardar = null): bool {
    $ruta = rutaDatos($archivo);
    $handle = @fopen($ruta, 'c+');
    if ($handle === false) return false;
    try {
        if (!flock($handle, LOCK_EX)) return false;
        $stat = fstat($handle);
        if ($stat === false) return false;
        if ($antesDeGuardar !== null) {
            rewind($handle);
            $filas = 0;
            while (($actual = fgetcsv($handle, 0, ',', '"', '\\')) !== false) {
                if ($filas > 0 && ($actual[6] ?? '') === (string)($fila[6] ?? '') && ($actual[7] ?? '') === (string)($fila[7] ?? '')) {
                    $filas++;
                } elseif ($filas === 0) {
                    $filas = 1; // cabecera leída
                }
            }
            if (!$antesDeGuardar($filas)) return false;
        }
        fseek($handle, 0, SEEK_END);
        if ((int)$stat['size'] === 0) fputcsv($handle, $cabecera, ',', '"', '\\');
        $segura = array_map(static fn($v) => celdaSegura(is_scalar($v) ? $v : ''), $fila);
        $ok = fputcsv($handle, $segura, ',', '"', '\\') !== false;
        fflush($handle);
        return $ok;
    } finally {
        flock($handle, LOCK_UN);
        fclose($handle);
    }
}
function guardarPrematricula(array $fila): bool {
    return agregarCsv('prematriculas.csv',
        ['creado_en','radicado','nombres','apellidos','nacimiento','grado','acudiente','parentesco','telefono','correo','observaciones'],
        $fila);
}
/** Retorna guardada, llena o error. El conteo y la escritura ocurren bajo el mismo bloqueo. */
function reservarCita(array $fila): string {
    $ruta = rutaDatos('citas.csv');
    $handle = @fopen($ruta, 'c+');
    if ($handle === false) return 'error';
    try {
        if (!flock($handle, LOCK_EX)) return 'error';
        $stat = fstat($handle);
        $cantidad = 0;
        rewind($handle);
        $primera = true;
        while (($actual = fgetcsv($handle, 0, ',', '"', '\\')) !== false) {
            if ($primera) { $primera = false; continue; }
            if (($actual[6] ?? '') === (string)$fila[6] && ($actual[7] ?? '') === (string)$fila[7]) $cantidad++;
        }
        if ($cantidad >= CITAS_POR_FRANJA) return 'llena';
        fseek($handle, 0, SEEK_END);
        if ((int)($stat['size'] ?? 0) === 0) {
            fputcsv($handle, ['creado_en','radicado','acudiente','telefono','correo','tipo','fecha','franja','ninos','comentarios'], ',', '"', '\\');
        }
        $segura = array_map(static fn($v) => celdaSegura(is_scalar($v) ? $v : ''), $fila);
        if (fputcsv($handle, $segura, ',', '"', '\\') === false) return 'error';
        fflush($handle);
        return 'guardada';
    } finally {
        flock($handle, LOCK_UN);
        fclose($handle);
    }
}
function citasEnFranja(string $fecha, string $franja): int {
    $ruta = rutaDatos('citas.csv');
    if (!is_file($ruta) || ($h = @fopen($ruta, 'r')) === false) return 0;
    $cantidad = 0;
    try {
        if (!flock($h, LOCK_SH)) return 0;
        fgetcsv($h, 0, ',', '"', '\\');
        while (($fila = fgetcsv($h, 0, ',', '"', '\\')) !== false) {
            if (($fila[6] ?? '') === $fecha && ($fila[7] ?? '') === $franja) $cantidad++;
        }
    } finally {
        flock($h, LOCK_UN);
        fclose($h);
    }
    return $cantidad;
}
