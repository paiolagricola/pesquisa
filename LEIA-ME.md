# Publicar em zeiagro.com.br — Hostinger

## Arquivos

```
index.html      → a pesquisa e o painel
salvar.php      → recebe cada resposta
dados.php       → devolve as respostas pro painel (protegido por chave)
dados/.htaccess → bloqueia leitura direta do arquivo de respostas
```

O arquivo `dados/respostas.json` é criado sozinho no primeiro envio.

---

## 1. Antes de subir

Abra `dados.php` e troque a chave da primeira linha:

```php
$CHAVE = 'troque-isto-por-algo-longo-2026';
```

Por algo longo e sem sentido. É ela que abre o painel.

---

## 2. Subir

**Pelo GitHub:** commit dos 4 arquivos + a pasta `dados/` num repositório, e no hPanel aponte o deploy Git pra ele. A Hostinger sincroniza a cada push.

> Atenção: se a Hostinger fizer deploy limpo a cada push, ela pode apagar `dados/respostas.json`. Adicione `dados/respostas.json` ao `.gitignore` e **não faça deploy durante o período da pesquisa.**

**Sem GitHub (mais seguro nesse caso):** hPanel > Gerenciador de Arquivos > `public_html`, e arraste os arquivos. Não tem risco de deploy sobrescrever resposta.

Recomendo subir numa subpasta, tipo `public_html/pesquisa/`, pra não mexer no site principal.

---

## 3. Testar (obrigatório antes de divulgar)

1. Abra `zeiagro.com.br/pesquisa/` e responda até o fim.
2. Abra `zeiagro.com.br/pesquisa/?admin=SUA_CHAVE` — sua resposta tem que aparecer somada às 8 de demonstração.
3. Abra `zeiagro.com.br/pesquisa/dados/respostas.json` direto no navegador. **Tem que dar erro 403.** Se abrir o conteúdo, o `.htaccess` não pegou — me avisa antes de divulgar qualquer coisa.
4. Depois do teste, apague `dados/respostas.json` pelo Gerenciador de Arquivos pra zerar.

---

## 4. Usar

**Pro time:** `zeiagro.com.br/pesquisa/`

**Pra você:** `zeiagro.com.br/pesquisa/?admin=SUA_CHAVE`

Sem a chave, quem abrir cai no formulário. O time não vê o painel.

No painel, o botão "remover demo" tira as 8 respostas fictícias e deixa só as reais.

---

## Anonimato — o que o código já faz

- **PHP não grava IP.** Só a data, sem hora.
- **Cada resposta entra em posição aleatória** no arquivo. Ordem de chegada denuncia quem respondeu logo depois de você mandar o link.
- **Nenhum campo de identificação.** Não adicione área nem cargo depois — com áreas de 1 ou 2 pessoas dentro do CSC, área é nome.

O que o código não resolve: o **log de acesso do servidor**, que registra IP de quem abriu a página. Não liga resposta a pessoa, mas registra quem entrou. Se quiser fechar isso também, dá pra limpar os logs de acesso no hPanel depois da pesquisa.

---

## Trocar por banco depois

`salvar.php` recebe um JSON `{"1":4,"2":3,...,"18":"texto"}`. Pra migrar pra MySQL da Hostinger, muda só esse arquivo — o `index.html` continua igual.
