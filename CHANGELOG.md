# 📋 Changelog

Todas as alterações notáveis deste projeto serão documentadas neste arquivo.

O formato é baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/),
e este projeto adere ao [Versionamento Semântico](https://semver.org/lang/pt-BR/).

---

## [1.2.0] - 2025-07-14

### 🔀 Compatibilidade Expandida

- **PHP**: suporte expandido para `^8.1` (anteriormente `^8.2`).
- **Laravel**: suporte explícito para Laravel **10.x**, **11.x** e **12.x**.
- `illuminate/contracts` e `illuminate/support` agora declarados com `^10.0|^11.0|^12.0`.
- `orchestra/testbench` compatível com `^8.0|^9.0`.
- `nunomaduro/collision` compatível com `^7.0|^8.0`.

### 📦 Dependências

- Adicionado `ext-intl` como requisito explícito (necessário para `Normalizer`).
- Adicionado `ext-mbstring` como requisito explícito.
- Adicionado `illuminate/support` como dependência direta (usa `Str::length`).
- Removido `spatie/laravel-ray` do `require-dev` (ferramenta de debug pessoal).
- `minimum-stability` alterado de `dev` para `stable`.

### 🧹 Limpeza do Projeto

- Removido diretório `workbench/` e referências no autoload.
- Removido diretório `database/factories/` (placeholder vazio).
- Removido `src/Abstraction/LarapixAbstraction.php` (arquivo vazio sem uso).
- Removido `phpstan-baseline.neon` (vazio).
- Removido `composer.lock` do versionamento (library package).
- Removidas imagens não utilizadas no README (`screens/021.jpeg`, `1.jpeg`, `31.jpeg`).
- Removido `.gitkeep` em `resources/views/` (desnecessário).
- Removidos scripts de `workbench` do `composer.json`.
- Atualizado `.gitignore` com categorias organizadas e proteção para `.env`.
- Atualizado `.gitattributes` removendo referências a arquivos inexistentes.
- Atualizado `phpstan.neon.dist` removendo baseline e path `database`.

---

## [1.1.0] - 2025-07-14

### ✨ Adicionado

- **Sanitização automática da cidade do titular**: o campo `cidadeDoTitularDaConta` agora remove automaticamente acentos (á, é, í, ó, ú, ã, õ, â, ê, ô, ç) e caracteres especiais, mantendo apenas letras, números e espaços.
- Método privado `sanitizeCity()` utilizando `Normalizer::FORM_D` para decomposição Unicode e remoção de marcas diacríticas.
- Tratamento de espaços múltiplos e trim automático no nome da cidade.
- Suporte a valores `null` em todos os setters (`chavePix`, `nomeDoTitularDaConta`, `cidadeDoTitularDaConta`, `descricao`, `txid`) retornando string vazia.

### 🧪 Testes

- Adicionados 57 testes automatizados com Pest PHP cobrindo:
  - Interface fluente (todos os setters)
  - Sanitização de cidade (13 cenários: sem acentos, com acentos, cedilha, til, circunflexo, trema, números, null, apenas especiais, espaços, via config, via fluent, end-to-end)
  - Formatação do valor (duas casas decimais, zero, arredondamento, valores grandes)
  - Tratamento de null em todos os campos
  - Estrutura do payload PIX (formato indicador, GUI BACEN, chave, nome, valor, TXID, país, moeda, CRC16)
  - Consistência e unicidade do CRC16
  - Tipos inteiros no `cobrar()` (chave e txid)
  - QR Code com largura personalizada
  - Encadeamento completo de métodos
  - Prioridade TXID (parâmetro vs config)
  - Geração completa via config

### 📝 Documentação

- Documentação PHPDoc completa em todas as classes, métodos e propriedades.
- Nomenclatura dos testes traduzida para português brasileiro.
- README.md profissional com exemplos de uso, badges e documentação completa.

### 🔧 Melhorado

- Configuração do pacote atualizada com todas as variáveis de ambiente documentadas.
- Comentários em português nos arquivos de configuração.

---

## [1.0.0] - 2025-04-29

### 🚀 Lançamento Inicial

- Geração de código PIX copia e cola conforme especificação BACEN.
- Geração de QR Code em formato PNG via biblioteca `mpdf/qrcode`.
- Interface fluente para construção do payload PIX.
- Método estático `cobrar()` para criação rápida de cobranças.
- Facade `Larapix` para uso simplificado no Laravel.
- View Blade com template responsivo (Bootstrap 5) para exibição do QR Code.
- Rota de exemplo para demonstração.
- Suporte a configuração via arquivo `config/larapix.php` e variáveis de ambiente.
- Cálculo de CRC16-CCITT conforme padrão do Banco Central do Brasil.
- Campos do payload: formato, conta do recebedor (GUI + chave + descrição), categoria, moeda (BRL/986), valor, país (BR), nome, cidade, dados adicionais (TXID).
- Service Provider com auto-discovery para Laravel.
- Comando de instalação com publicação de config.

### 🔗 Dependências

- PHP ^8.2
- Laravel (illuminate/contracts)
- mpdf/qrcode ^1.2
- spatie/laravel-package-tools ^1.16

### 👥 Novos Contribuidores

- @dependabot fez sua primeira contribuição em https://github.com/modularavel/larapix/pull/1

**Full Changelog**: https://github.com/modularavel/larapix/commits/1.0.0

---

[1.2.0]: https://github.com/modularavel/larapix/compare/1.1.0...1.2.0
[1.1.0]: https://github.com/modularavel/larapix/compare/1.0.0...1.1.0
[1.0.0]: https://github.com/modularavel/larapix/commits/1.0.0
