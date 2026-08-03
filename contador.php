<?php
// Contador de visitas simple. Guarda el total en contador.txt
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$archivo = __DIR__ . '/contador.txt';

// Abrimos en modo lectura/escritura, creando el archivo si no existe.
$fp = fopen($archivo, 'c+');
if ($fp === false) {
    echo json_encode(['visitas' => null]);
    exit;
}

// El lock evita que dos visitas simultaneas se pisen y pierdan la cuenta.
if (flock($fp, LOCK_EX)) {
    $visitas = (int) trim(fread($fp, 32));
    $visitas++;

    ftruncate($fp, 0);
    rewind($fp);
    fwrite($fp, (string) $visitas);
    fflush($fp);
    flock($fp, LOCK_UN);
} else {
    $visitas = null;
}
fclose($fp);

echo json_encode(['visitas' => $visitas]);
