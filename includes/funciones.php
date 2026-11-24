<?php
declare(strict_types=1);
require_once __DIR__.'/db.php';
function escapar(string $t): string { return htmlspecialchars($t, ENT_QUOTES, 'UTF-8'); }
function leerTexto(array $o, string $k): string { $v = $o[$k] ?? ''; return is_string($v) ? trim($v) : ''; }
function longitudValida(string $t, int $min, int $max): bool { $l = mb_strlen($t); return $l >= $min && $l <= $max; }
function correoValido(string $c): bool { return filter_var($c, FILTER_VALIDATE_EMAIL) !== false; }
function telefonoValido(string $t): bool { return preg_match('/^[0-9]{7,10}$/', $t) === 1; }
function leerFecha(string $t): ?DateTimeImmutable {
    $f = DateTimeImmutable::createFromFormat('!Y-m-d', $t);
    $e = DateTimeImmutable::getLastErrors();
    if ($f === false || ($e !== false && ($e['warning_count'] > 0 || $e['error_count'] > 0))) return null;
    return $f;
}
function calcularEdad(DateTimeImmutable $n): int { return $n->diff(new DateTimeImmutable('today'))->y; }
function esPaginaActual(string $a): bool { return basename($_SERVER['SCRIPT_NAME'] ?? '') === $a; }
function errorCampo(array $e, string $c): string {
    return isset($e[$c]) ? '<p class="error" id="error-'.escapar($c).'">'.escapar($e[$c]).'</p>' : '';
}
/** Ejecuta un INSERT con los valores de la fila; devuelve false (y registra el error) si falla. */
function insertarFila(string $sql, array $fila): bool {
    try {
        return db()->prepare($sql)->execute(array_map(fn($c) => (string)$c, array_values($fila)));
    } catch (Throwable $e) {
        error_log('BD: '.$e->getMessage());
        return false;
    }
}
/** Fila: [fecha_hora, radicado, nombres, apellidos, nacimiento, grado, acudiente, parentesco, telefono, correo, observaciones] */
function guardarPrematricula(array $fila): bool {
    return insertarFila('INSERT INTO prematriculas (creado_en, radicado, nombres, apellidos, nacimiento, grado, acudiente, parentesco, telefono, correo, observaciones) VALUES (?,?,?,?,?,?,?,?,?,?,?)', $fila);
}
/** Fila: [fecha_hora, radicado, acudiente, telefono, correo, tipo, fecha, franja, ninos, comentarios] */
function guardarCita(array $fila): bool {
    return insertarFila('INSERT INTO citas (creado_en, radicado, acudiente, telefono, correo, tipo, fecha, franja, ninos, comentarios) VALUES (?,?,?,?,?,?,?,?,?,?)', $fila);
}
function citasEnFranja(string $fecha, string $franja): int {
    try {
        $s = db()->prepare('SELECT COUNT(*) FROM citas WHERE fecha = ? AND franja = ?');
        $s->execute([$fecha, $franja]);
        return (int)$s->fetchColumn();
    } catch (Throwable $e) {
        error_log('BD: '.$e->getMessage());
        return 0;
    }
}
// ---- Ayudas de formulario (evitan repetir HTML en cada campo) ----
function ariaError(array $e, string $id): string {
    return isset($e[$id]) ? ' aria-invalid="true" aria-describedby="error-'.escapar($id).'"' : '';
}
function campo(string $tipo, string $id, string $etq, array $d, array $e, string $extra = ''): string {
    return '<div class="campo"><label for="'.$id.'">'.escapar($etq).'</label><input type="'.$tipo.'" id="'.$id.'" name="'.$id.'" value="'.escapar($d[$id] ?? '').'" '.$extra.ariaError($e,$id).'>'.errorCampo($e,$id).'</div>';
}
function selector(string $id, string $etq, array $ops, array $d, array $e): string {
    $h = '<div class="campo"><label for="'.$id.'">'.escapar($etq).'</label><select id="'.$id.'" name="'.$id.'"'.ariaError($e,$id).'><option value="">Selecciona…</option>';
    foreach ($ops as $k => $v) {
        $val = array_is_list($ops) ? (string)$v : (string)$k;
        $h .= '<option value="'.escapar($val).'"'.($val === ($d[$id] ?? '') ? ' selected' : '').'>'.escapar((string)$v).'</option>';
    }
    return $h.'</select>'.errorCampo($e,$id).'</div>';
}
function areaTexto(string $id, string $etq, array $d, array $e): string {
    return '<div class="campo"><label for="'.$id.'">'.escapar($etq).' (opcional)</label><textarea id="'.$id.'" name="'.$id.'" rows="3" maxlength="300"'.ariaError($e,$id).'>'.escapar($d[$id] ?? '').'</textarea>'.errorCampo($e,$id).'</div>';
}
function casilla(bool $on, array $e): string {
    return '<div class="campo campo-consentimiento"><label class="check" for="autorizo">'
        . '<input type="checkbox" id="autorizo" name="autorizo" value="1"'
        . ($on ? ' checked' : '') . ' required>'
        . '<span>Confirmo que utilizo datos ficticios y autorizo su tratamiento exclusivamente para este ejercicio académico.</span>'
        . '</label>' . errorCampo($e, 'autorizo') . '</div>';
}
function avisos(array $e): string {
    if (isset($e['general'])) return '<div class="aviso aviso--error" role="alert">'.escapar($e['general']).'</div>';
    return $e ? '<div class="aviso aviso--error" role="alert">Revisa los campos marcados: '.count($e).' dato(s) por corregir.</div>' : '';
}
