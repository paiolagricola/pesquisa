<?php
/**
 * Recebe uma resposta da pesquisa e grava em dados/respostas.json
 */
header('Content-Type: application/json; charset=utf-8');

$raw = file_get_contents('php://input');
if (strlen($raw) > 60000) { http_response_code(413); echo '{"ok":false}'; exit; }

$d = json_decode($raw, true);
if (!is_array($d)) { http_response_code(400); echo '{"ok":false}'; exit; }

// monta a linha só com as chaves esperadas (1..23)
$linha = array('data' => date('d/m/Y'));
for ($i = 1; $i <= 23; $i++) {
    $k = (string)$i;
    if (!array_key_exists($k, $d)) continue;
    $v = $d[$k];
    if (is_string($v)) $v = mb_substr(trim($v), 0, 2000);
    elseif (!is_int($v) && !is_null($v)) continue;
    $linha[$k] = $v;
}
if (count($linha) < 2) { http_response_code(400); echo '{"ok":false}'; exit; }

$dir = __DIR__ . '/dados';
if (!is_dir($dir)) mkdir($dir, 0755, true);
$arq = $dir . '/respostas.json';

$fp = fopen($arq, 'c+');
if (!$fp) { http_response_code(500); echo '{"ok":false}'; exit; }
flock($fp, LOCK_EX);

$txt = stream_get_contents($fp);
$todas = $txt ? json_decode($txt, true) : array();
if (!is_array($todas)) $todas = array();

// Insere em posição ALEATÓRIA, nunca no fim.
// Ordem de chegada é identificação disfarçada: quem sabe quem respondeu
// primeiro consegue amarrar a resposta à pessoa.
$pos = count($todas) ? random_int(0, count($todas)) : 0;
array_splice($todas, $pos, 0, array($linha));

ftruncate($fp, 0);
rewind($fp);
fwrite($fp, json_encode($todas, JSON_UNESCAPED_UNICODE));
fflush($fp);
flock($fp, LOCK_UN);
fclose($fp);

echo '{"ok":true}';
