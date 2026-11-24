# Histórico de desenvolvimento — Reserva AI

Registro do prompt original e das respostas da IA (assistente usando Copilot SDK no VS Code), em ordem cronológica e de forma resumida.

- Projeto: `reserva-ai` (Laravel 9.52.23, PHP 8.0.30, Node 24, Windows)
- Data de conclusão: 23/11/2026

---

## 1. Prompt original do usuário

### Princípios

Objetivo: **Permitir que a equipe responsável cadastre, consulte, altere e remova equipamentos e também reserve em datas e horarios específicos, localizando informações básicas e sua situação atual.**

#### Como construir

Mantenha a mesma arquitetura usada no framework Laravel (MVC). Construa os elementos em sequência: 1° Model; 2° Migrate; 3° Controller; 4° View. Apenas vá para a próxima etapa de construção, se não tiver erros ou incossitências. Erros de sintaxe ou possíves bugs.

A nomeclatura dos arquivos e funções devem ser em português.

Sempre use como parametro de segurança o "CSRF Protection". Sempre valide todos os dados de entrada para manter a segurança.

#### O que não fazer

Não adote quaisquer biblioteca ou tecnologia não especificado sem minha permissão. Não mude de arquitetura ou adote novos padrões de código sem a minha permissão.

### Especificações

- Linguagem: PHP e Blade/HTML, seguindo a configuração existente.
- Framework: Laravel, usando a versão do projeto.
- UI e UX: tailwind css
- Persistência: MySQL

**Requisitos**

- RF000 — Reservar equipamento: o sistema deve ser capaz de reservar um equipamento em um período específico.
- RF001 — Listar equipamentos: exibir nome, número de patrimônio, categoria e situação; informar quando a lista estiver vazia.
- RF002 — Cadastrar equipamento: nome, número de patrimônio, categoria e situação.
- RF003 — Consultar equipamento: visualizar os dados de um equipamento específico.
- RF004 — Editar equipamento: alterar dados sem considerar o patrimônio do próprio equipamento como duplicado.
- RF005 — Excluir equipamento: com confirmação antes de concluir.
- RF006 — Validar os dados: impedir campos obrigatórios ausentes ou inválidos.
- RF007 — Impedir patrimônio duplicado: patrimônio único, com aviso quando já cadastrado.
- RF008 — Controlar a situação: somente disponível, em manutenção ou fora de uso; padrão disponível no cadastro.
- RNF001 — Compatibilidade: navegadores atuais, desktop e celular.
- RNF002 — Usabilidade: mensagens de validação claras por campo; situação não identificada apenas por cor.
- RNF003 — Segurança dos formulários: proteção CSRF e validação no servidor.
- RNF004 — Segurança na apresentação: valores escapados (padrão do Blade).
- RNF005 — Integridade dos dados: restrição de unicidade no banco para patrimônio.
- RNF006 — Manutenibilidade: convenções e versão do Laravel do projeto; responsabilidades organizadas.
- RNF007 — Instalação e execução: README com objetivo, pré-requisitos, configuração, instalação e execução.

**Identidade visual**

- Estilo moderno, descontraído e contemporâneo; predominância de verdes; composição assimétrica com blocos contrastantes; ilustrações vetoriais e ícones minimalistas; design limpo com boa hierarquia.
- Paleta: verde escuro `#00523D`, verde-claro pastel `#D6F2C2`, verde-limão `#8DE55D`, amarelo-esverdeado `#D3F452`, verde intenso `#038E39`, branco `#FFFFFF`, roxo complementar `#2E1E3D`.
- Tipografia: títulos Baloo 2 (700/800); subtítulos Montserrat (600/700); textos Nunito Sans (400/500); títulos grandes e arredondados.
- Elementos: cards arredondados; ícones em caixas quadradas com cantos arredondados; botões verde-limão com texto verde escuro; ilustrações flat com formas orgânicas; linhas geométricas discretas.
- Layout: desktop em duas colunas (~55% / 45%); alternância de fundos verde escuro e verde-claro; blocos com espaçamento generoso; mobile vertical sem perda de legibilidade.
- Regras gerais: consistência; sem gradientes excessivos nem sombras pesadas; acessibilidade, contraste e responsividade; sem cara de template genérico.

> "Organize tudo para desenvolver."

---

## 2. Linha cronológica das respostas da IA

### Etapa 1 — Análise do projeto e planejamento (modo plano)

- Levantou o estado do projeto: Laravel 9 base, Blade, Laravel Mix 6, MySQL como driver padrão, idioma inglês e fuso UTC, sem Tailwind, README padrão, pasta sem Git.
- Fez perguntas de esclarecimento ao usuário. Decisões confirmadas:
  - Categorias fixas: Informática, Audiovisual, Ferramentas e Outros.
  - Sem login; o nome do responsável é registrado na reserva.
  - Reservas não podem ser editadas, apenas canceladas (com histórico).
  - Fuso `America/Sao_Paulo`.
  - Tailwind CSS 3 integrado ao Laravel Mix existente (sem Vite).
  - Fontes baixadas de origem oficial e SVGs próprios autorizados.
- Escreveu o plano completo (escopo, convenções, sequência Model → Migration → Controller → View, critérios de aceite) e obteve aprovação.

### Etapa 2 — Preparação do ambiente de testes

- Criou `.env.testing.example` e incluiu `.env.testing` no `.gitignore`; o usuário preencheu o `.env.testing` com um banco exclusivo de testes.
- Ao verificar a conexão, identificou que o servidor local é **MariaDB 10.4.32**, não MySQL.
- Perguntou ao usuário como proceder. Decisão: **manter MySQL estritamente**; o usuário configurará um servidor MySQL. Por isso, nenhuma migration ou teste com banco foi executado no MariaDB.

### Etapa 3 — Models

- `app/Models/Equipamento.php`: constantes `SITUACOES` e `CATEGORIAS`, situação padrão `disponivel`, métodos `rotuloSituacao`, `rotuloCategoria`, `estaDisponivel`, `reservaEmAndamento`.
- `app/Models/Reserva.php`: escopos `naoCanceladas`, `conflitantes` (fim exclusivo, permitindo reservas consecutivas), `vigentes`, `emAndamento`; estado derivado (futura, em andamento, encerrada, cancelada); `podeSerCancelada`.
- Verificação: `php -l` e testes unitários (`tests/Unit/ReservaTest.php`) passando.

### Etapa 4 — Migrations

- `criar_tabela_equipamentos`: InnoDB, patrimônio `varchar(100)` com índice `UNIQUE`, colunas enum, situação com default `disponivel`.
- `criar_tabela_reservas`: chave estrangeira com exclusão restrita (preserva histórico), colunas `datetime` para início, fim e cancelamento, índices para consulta de conflito.
- Verificação: SQL gerado offline (`--pretend`) conferido; execução real pendente de MySQL.

### Etapa 5 — Controllers e rotas

- `EquipamentoController`: listar (busca, filtro por situação, paginação), criar, cadastrar, consultar, editar, atualizar, confirmar exclusão e excluir.
  - Validação no servidor com mensagens em português por campo.
  - Patrimônio único ignorando o próprio registro na edição; erro de duplicidade do MySQL (1062) convertido em mensagem do campo.
  - Bloqueia exclusão com histórico de reservas e mudança de situação com reservas vigentes.
- `ReservaController`: listar, criar, cadastrar, consultar, confirmar cancelamento e cancelar.
  - Início não pode estar no passado; fim posterior ao início; somente equipamentos disponíveis.
  - Transação com `lockForUpdate` na linha do equipamento e revalidação de conflitos, evitando reservas sobrepostas em pedidos simultâneos.
- `routes/web.php`: 16 rotas nomeadas em português, ids numéricos, `/` redireciona para `/equipamentos`.
- Traduções em `lang/pt_BR`; `config/app.php` com fuso `America/Sao_Paulo` e locale `pt_BR`.
- Verificação: `php -l`, `route:list` e testes de segurança (`SegurancaFormulariosTest`): requisições sem CSRF ou com token inválido retornam 419; campos obrigatórios exigidos.

### Etapa 6 — Views e identidade visual

- Tailwind CSS 3 adicionado ao Laravel Mix (`tailwind.config.js`, `webpack.mix.js`, `resources/css/app.css`) com a paleta e as fontes do usuário.
- Fontes Baloo 2, Montserrat e Nunito Sans servidas localmente em `public/fonts`, com licenças OFL.
- Layout `layouts/principal`, parciais (ícones SVG próprios, situação, estado da reserva, ilustração, erros, paginação) e telas de equipamentos e reservas, incluindo páginas de confirmação de exclusão e cancelamento.
- Situação e estado sempre exibidos com ícone + texto; todos os formulários com `@csrf`; dados exibidos com escape do Blade.
- Problema encontrado: o Laravel Mix 6 quebra com webpack ≥ 5.104. Solução: fixar `webpack` em `5.103.0` via `overrides` no `package.json`.
- Verificação: `view:cache`, `npm run production`, renderização das 16 telas com dados de exemplo, escape de HTML confirmado e conferência no navegador em desktop e em 375px (sem rolagem horizontal).

### Etapa 7 — Documentação e verificação final

- Servidor de pré-visualização encerrado e arquivos temporários removidos.
- `.env.example` atualizado com `APP_NAME="Reserva AI"` e `DB_DATABASE=reserva_ai`.
- `README.md` reescrito com objetivo, funcionalidades, regras, segurança, pré-requisitos, instalação, configuração, execução, testes, estrutura e identidade visual.
- Testes finais: 4 testes unitários e 5 de segurança passando.

---

## 3. Resultado entregue

| Requisito | Situação |
| --- | --- |
| RF000–RF008 | Implementados |
| RNF001–RNF007 | Implementados |
| Testes sem banco (unitários e CSRF) | Passando (9 testes) |
| Testes com banco (`EquipamentoTest`, `ReservaFluxoTest`) | Escritos, aguardando MySQL |
| Teste de reservas simultâneas em MySQL | Não escrito |

### Pendências

1. Configurar um servidor **MySQL** no `.env.testing` e executar:

   ```powershell
   php artisan migrate --env=testing
   php artisan test
   ```

2. Ajustar `APP_NAME` no `.env` local (ainda "Laravel").

### Observações

- Única alteração fora do especificado: `webpack` fixado em `5.103.0` para o build do Laravel Mix funcionar.
- Não há login: qualquer pessoa com acesso ao sistema executa todas as operações. Publicar somente em rede interna.

---

## 4. Solicitação posterior

> "preciso que exporte todos os detalhes desse prompt e da resposta da IA para um arquivo markdown. Contenha toda a resposta na linha cronológica de forma resumida. O arquivo deve estar na raiz ao lado do README"

Resposta: criação deste arquivo, `HISTORICO-DESENVOLVIMENTO.md`, na raiz do projeto.
