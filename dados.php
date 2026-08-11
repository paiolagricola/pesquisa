<?php
/**
 * Devolve as respostas para o painel. Protegido por chave.
 * TROQUE A CHAVE ABAIXO antes de publicar.
 */
$CHAVE = 'mruk9pff6sfj6qx1biul7s2w9vmksoihe7uq9b8e';

header('Content-Type: application/json; charset=utf-8');

$k = isset($_GET['k']) ? $_GET['k'] : '';
if (!hash_equals($CHAVE, $k)) {
    http_response_code(403);
    echo '{"erro":"chave invalida"}';
    exit;
}

$arq = __DIR__ . '/dados/respostas.json';
$todas = is_file($arq) ? json_decode(file_get_contents($arq), true) : array();
if (!is_array($todas)) $todas = array();

shuffle($todas);

echo json_encode(array('rows' => $todas), JSON_UNESCAPED_UNICODE);
