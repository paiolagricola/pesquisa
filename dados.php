<?php
/**
 * Devolve as respostas para o painel. Protegido por chave.
 * A chave fica em dados/chave.txt, criado direto no servidor
 * (nunca no Git — o repositório é público).
 */
header('Content-Type: application/json; charset=utf-8');

$arqChave = __DIR__ . '/dados/chave.txt';
$CHAVE = is_file($arqChave) ? trim(file_get_contents($arqChave)) : '';

$k = isset($_GET['k']) ? $_GET['k'] : '';
if ($CHAVE === '' || strlen($CHAVE) < 20 || !hash_equals($CHAVE, $k)) {
    http_response_code(403);
    echo '{"erro":"chave invalida"}';
    exit;
}

$arq = __DIR__ . '/dados/respostas.json';
$todas = is_file($arq) ? json_decode(file_get_contents($arq), true) : array();
if (!is_array($todas)) $todas = array();

shuffle($todas);

echo json_encode(array('rows' => $todas), JSON_UNESCAPED_UNICODE);
