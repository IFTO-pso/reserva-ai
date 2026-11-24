# Reserva AI

Sistema interno para a equipe responsável **cadastrar, consultar, alterar e remover equipamentos** e **reservá-los em datas e horários específicos**, localizando rapidamente as informações básicas e a situação atual de cada item.

## Funcionalidades

**Equipamentos**

- Listagem com nome, número de patrimônio, categoria e situação (com aviso de lista vazia), busca por nome/patrimônio e filtro por situação.
- Cadastro, consulta, edição e exclusão (com página de confirmação).
- Categorias fixas: Informática, Audiovisual, Ferramentas e Outros.
- Situações permitidas: **Disponível**, **Em manutenção** e **Fora de uso**. No cadastro, a situação inicial é Disponível, salvo se outra opção válida for escolhida.
- Número de patrimônio obrigatório e único (validação no servidor e índice `UNIQUE` no banco). Aceita letras, números, ponto, hífen e barra; zeros à esquerda são preservados. Na edição, o patrimônio do próprio equipamento não é considerado duplicado.
- A consulta mostra a ocupação atual e as próximas reservas.
- Um equipamento com reservas futuras ou em andamento não pode mudar para manutenção/fora de uso até que elas sejam canceladas.
- Um equipamento com qualquer histórico de reservas (inclusive canceladas) não pode ser excluído; use a situação "Fora de uso".

**Reservas**

- Cada reserva tem equipamento, nome do responsável, início e fim.
- Somente equipamentos com situação Disponível podem ser reservados.
- O início não pode estar no passado e o fim deve ser posterior ao início.
- Períodos sobrepostos para o mesmo equipamento são recusados; reservas consecutivas são permitidas (o fim é exclusivo: uma reserva até 10:00 não conflita com outra a partir de 10:00).
- Reservas futuras ou em andamento podem ser canceladas (com confirmação). O registro é mantido no histórico com a data do cancelamento e o período é liberado. Reservas não podem ser editadas.
- Estados exibidos: Futura, Em andamento, Encerrada e Cancelada.
- Todos os horários usam o fuso `America/Sao_Paulo` (entrada, armazenamento e exibição).

**Segurança**

- Todos os formulários usam proteção CSRF do Laravel (`@csrf`) e todas as entradas são validadas no servidor.
- Valores informados pelos usuários são exibidos com escape padrão do Blade (`{{ }}`).
- Reservas, mudanças de situação, exclusões e cancelamentos bloqueiam a linha do equipamento (`SELECT ... FOR UPDATE`) dentro de uma transação, evitando reservas sobrepostas em requisições simultâneas.
- **Não há login.** O sistema é destinado ao uso interno: qualquer pessoa com acesso à aplicação pode executar todas as operações. Publique-o apenas em rede interna/restrita.

## Pré-requisitos

- PHP 8.0 ou superior, com as extensões `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json` e `fileinfo`
- Composer 2
- Node.js e npm (para compilar o CSS com Laravel Mix + Tailwind CSS 3)
- **MySQL** 5.7 ou superior (recomendado 8.x), com motor InnoDB

## Instalação

No diretório do projeto (exemplos para PowerShell no Windows; no PowerShell use `npm.cmd` caso a política de execução bloqueie `npm`):

```powershell
composer install
npm install
copy .env.example .env
php artisan key:generate
```

## Configuração

1. Crie o banco no MySQL (ajuste nome e usuário conforme seu ambiente):

   ```sql
   CREATE DATABASE reserva_ai CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

2. Edite o `.env` com os dados da conexão:

   ```dotenv
   APP_NAME="Reserva AI"
   APP_URL=http://localhost:8000

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=reserva_ai
   DB_USERNAME=seu_usuario
   DB_PASSWORD=sua_senha
   ```

3. Crie as tabelas:

   ```powershell
   php artisan migrate
   ```

4. Compile os assets (CSS com Tailwind e fontes locais):

   ```powershell
   npm run production
   ```

   Durante o desenvolvimento, use `npm run watch` para recompilar automaticamente.

## Execução

```powershell
php artisan serve
```

Acesse <http://localhost:8000>. A página inicial redireciona para a lista de equipamentos.

## Testes

Os testes que usam o banco **apagam e recriam as tabelas** (`RefreshDatabase`). Use sempre um banco MySQL exclusivo para testes, nunca o banco de uso.

1. Crie um banco exclusivo, por exemplo `reserva_ai_test`, com um usuário restrito a ele.
2. Copie o exemplo e preencha a conexão (o arquivo `.env.testing` é ignorado pelo Git):

   ```powershell
   copy .env.testing.example .env.testing
   php artisan key:generate --env=testing
   ```

3. Execute:

   ```powershell
   php artisan test
   ```

   Testes que não usam banco (regras do model, proteção CSRF e campos obrigatórios) podem ser executados isoladamente:

   ```powershell
   php artisan test --testsuite=Unit
   php artisan test tests/Feature/SegurancaFormulariosTest.php
   ```

## Estrutura

| Camada | Arquivos |
| --- | --- |
| Models | `app/Models/Equipamento.php`, `app/Models/Reserva.php` |
| Migrations | `database/migrations/*_criar_tabela_equipamentos.php`, `*_criar_tabela_reservas.php` |
| Controllers | `app/Http/Controllers/EquipamentoController.php`, `ReservaController.php` |
| Rotas | `routes/web.php` |
| Views | `resources/views/layouts`, `equipamentos`, `reservas`, `parciais` |
| Estilos | `resources/css/app.css`, `tailwind.config.js`, `webpack.mix.js` |
| Traduções | `lang/pt_BR` |

## Identidade visual

- Paleta: verde escuro `#00523D`, verde-claro pastel `#D6F2C2`, verde-limão `#8DE55D`, amarelo-esverdeado `#D3F452`, verde intenso `#038E39`, branco `#FFFFFF` e roxo complementar `#2E1E3D`.
- Tipografia: Baloo 2 (títulos), Montserrat (subtítulos) e Nunito Sans (textos), servidas localmente em `public/fonts` sob a licença SIL Open Font License (arquivos `OFL-*.txt`).
- Ícones e ilustrações são SVGs próprios, sem bibliotecas externas.
- A situação dos equipamentos e o estado das reservas sempre aparecem com texto e ícone, não apenas com cor.

## Observação sobre dependências de front-end

O `package.json` fixa o `webpack` em `5.103.0` (campo `overrides`), pois versões mais recentes removeram um módulo usado pelo Laravel Mix 6 e quebram o build.
