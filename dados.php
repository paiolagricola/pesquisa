<?php
/**
 * Devolve as respostas para o painel. Protegido por login ou chave.
 * As credenciais ficam em arquivos criados direto no servidor,
 * nunca no Git (o repositório é público):
 *   dados/login.txt → linha 1: usuário · linha 2: senha
 *   dados/chave.txt → chave para o atalho ?admin=CHAVE (opcional)
 */
header('Content-Type: application/json; charset=utf-8');

$ok = false;

// 1) Login por usuário e senha — POST {"u":"...","p":"..."}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $arqLogin = __DIR__ . '/dados/login.txt';
    $linhas = is_file($arqLogin)
        ? array_values(array_filter(array_map('trim', file($arqLogin)), 'strlen'))
        : array();
    $d = json_decode(file_get_contents('php://input'), true);
    if (count($linhas) >= 2 && is_array($d)
        && hash_equals(mb_strtolower($linhas[0]), mb_strtolower(isset($d['u']) ? (string)$d['u'] : ''))
        && hash_equals($linhas[1], isset($d['p']) ? (string)$d['p'] : '')) {
        $ok = true;
    }
    if (!$ok) usleep(400000); // freia tentativa de adivinhação em massa
}

// 2) Chave direto na URL (?k=...), lida de dados/chave.txt
if (!$ok) {
    $arqChave = __DIR__ . '/dados/chave.txt';
    $CHAVE = is_file($arqChave) ? trim(file_get_contents($arqChave)) : '';
    $k = isset($_GET['k']) ? $_GET['k'] : '';
    if ($CHAVE !== '' && strlen($CHAVE) >= 20 && hash_equals($CHAVE, $k)) $ok = true;
}

if (!$ok) {
    http_response_code(403);
    echo '{"erro":"acesso negado"}';
    exit;
}

$arq = __DIR__ . '/dados/respostas.json';
$todas = is_file($arq) ? json_decode(file_get_contents($arq), true) : array();
if (!is_array($todas)) $todas = array();

shuffle($todas);

echo json_encode(array('rows' => $todas), JSON_UNESCAPED_UNICODE);
