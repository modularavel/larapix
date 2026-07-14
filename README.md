<p align="center">
  <img src="https://github.com/modularavel/larapix/blob/main/screens/banner.png?raw=true" width="100%" alt="Larapix - Pagamentos PIX para Laravel" />
</p>

<h1 align="center">modu<strong style="color: #F05340">laravel</strong><strong style="color: #72ee59ff">/larapix</strong></h1>

<p align="center">
  <strong>Gere QR Codes e códigos PIX copia e cola para pagamentos instantâneos no Laravel.</strong>
</p>

<p align="center">
  <a href="https://packagist.org/packages/modularavel/larapix"><img src="https://img.shields.io/packagist/v/modularavel/larapix.svg?style=flat-square&label=vers%C3%A3o" alt="Versão no Packagist"></a>
  <a href="https://github.com/modularavel/larapix/actions?query=workflow%3Arun-tests+branch%3Amain"><img src="https://img.shields.io/github/actions/workflow/status/modularavel/larapix/run-tests.yml?branch=main&label=testes&style=flat-square" alt="Status dos Testes"></a>
  <a href="https://github.com/modularavel/larapix/actions?query=workflow%3A%22Fix+PHP+code+style+issues%22+branch%3Amain"><img src="https://img.shields.io/github/actions/workflow/status/modularavel/larapix/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square" alt="Code Style"></a>
  <a href="https://packagist.org/packages/modularavel/larapix"><img src="https://img.shields.io/packagist/dt/modularavel/larapix.svg?style=flat-square&label=downloads" alt="Total de Downloads"></a>
  <a href="https://github.com/modularavel/larapix/blob/main/LICENSE.md"><img src="https://img.shields.io/badge/licen%C3%A7a-MIT-green.svg?style=flat-square" alt="Licença MIT"></a>
  <a href="https://www.php.net/"><img src="https://img.shields.io/badge/PHP-^8.1+-8892BF.svg?style=flat-square" alt="PHP ^8.1"></a>
  <a href="https://laravel.com/"><img src="https://img.shields.io/badge/Laravel-10.x%20|%2011.x%20|%2012.x-FF2D20.svg?style=flat-square" alt="Laravel 10|11|12"></a>
</p>

---

## 📖 Sobre

**Larapix** é um pacote Laravel elegante e minimalista para gerar pagamentos PIX de forma programática. Com uma interface fluente e intuitiva, você pode criar códigos copia e cola e QR Codes prontos para uso em segundos.

### ⚡ Principais Funcionalidades

| Funcionalidade | Descrição |
|---|---|
| 🔑 **Código Copia e Cola** | Gera o payload PIX completo conforme especificação BACEN |
| 📱 **QR Code PNG** | Gera imagem QR Code pronta para exibição |
| 🧹 **Sanitização Automática** | Remove acentos e caracteres especiais da cidade automaticamente |
| ⛓️ **Interface Fluente** | API encadeável e expressiva |
| 🏭 **Facade** | Acesso simplificado via `Larapix::cobrar()` |
| ⚙️ **Configurável** | Valores padrão via `.env` ou config |
| 🖼️ **View Blade** | Template responsivo pronto para uso |
| ✅ **CRC16-CCITT** | Checksum conforme padrão do Banco Central |

---

## 📦 Instalação

Instale o pacote via Composer:

```bash
composer require modularavel/larapix
```

Publique o arquivo de configuração:

```bash
php artisan vendor:publish --tag="larapix-config"
```

Opcionalmente, publique as views:

```bash
php artisan vendor:publish --tag="larapix-views"
```

---

## ⚙️ Configuração

Após publicar o config, edite `config/larapix.php`:

```php
return [
    // Chave PIX (CPF, CNPJ, e-mail, telefone ou aleatória)
    'chave_pix' => env('LARAPIX_CHAVE_PIX'),

    // Nome completo do titular da conta
    'nome_do_titular' => env('LARAPIX_NOME_DO_TITULAR'),

    // Cidade do titular (acentos são removidos automaticamente)
    'cidade_do_titular' => env('LARAPIX_CIDADE_DO_TITULAR'),

    // Valor padrão da transação em reais
    'valor' => env('LARAPIX_VALOR', 0),

    // Descrição padrão do pagamento
    'descricao' => env('LARAPIX_DESCRICAO'),

    // Identificador único da transação (TXID)
    'id_transacao' => env('LARAPIX_ID_TRANSACAO'),
];
```

### 🔐 Variáveis de Ambiente (`.env`)

```env
LARAPIX_CHAVE_PIX=seu-cpf-cnpj-email-ou-chave
LARAPIX_NOME_DO_TITULAR=Seu Nome Completo
LARAPIX_CIDADE_DO_TITULAR=SAO PAULO
LARAPIX_VALOR=0
LARAPIX_DESCRICAO=
LARAPIX_ID_TRANSACAO=
```

---

## 🚀 Uso

### Uso Básico — Interface Fluente

```php
use Modularavel\Larapix\Larapix;

$pix = (new Larapix())
    ->chavePix('12345678909')
    ->nomeDoTitularDaConta('João da Silva')
    ->cidadeDoTitularDaConta('São Paulo') // acentos removidos automaticamente
    ->valor(150.00)
    ->descricao('Pagamento de serviço')
    ->txid('PEDIDO-001');

// Gerar código copia e cola
$codigo = $pix->gerarCodigoDePagamento();

// Gerar imagem QR Code (PNG)
$qrCodePng = $pix->gerarQRCodeDePagamento($codigo);
```

### Uso com Facade

```php
use Modularavel\Larapix\Facades\Larapix;

$pix = Larapix::cobrar(
    valor: 99.90,
    chavePix: '11999998888',
    nomeDoTitularDaConta: 'Maria Souza',
    cidadeDoTitularDaConta: 'Uberlândia',
    descricao: 'Assinatura mensal',
    txid: 'SUB-2025-001'
);

$codigo = $pix->gerarCodigoDePagamento();
$qrCode = $pix->gerarQRCodeDePagamento($codigo);
```

### Uso com Valores do Config

```php
// Usa os valores definidos em config/larapix.php
$pix = new Larapix();
$codigo = $pix->valor(50.00)->txid('TX-123')->gerarCodigoDePagamento();
```

### Exibir QR Code em uma View Blade

```php
// No controller
$pix = Larapix::cobrar(valor: 75.00, descricao: 'Produto X');
$codigo = $pix->gerarCodigoDePagamento();
$qrCode = $pix->gerarQRCodeDePagamento($codigo);

return view('larapix::pix-qrcode', [
    'image' => $qrCode,
    'codigo' => $codigo,
]);
```

### Salvar QR Code em Arquivo

```php
$pix = (new Larapix())
    ->chavePix('98765432100')
    ->nomeDoTitularDaConta('Empresa LTDA')
    ->cidadeDoTitularDaConta('Curitiba')
    ->valor(500.00);

$codigo = $pix->gerarCodigoDePagamento();
$imagemPng = $pix->gerarQRCodeDePagamento($codigo);

file_put_contents(storage_path('app/qrcode-pix.png'), $imagemPng);
```

### QR Code com Largura Personalizada

```php
use Mpdf\QrCode\Output\Png;

$qrCode = $pix->gerarQRCodeDePagamento($codigo, Png::class, 800); // 800px
```

---

## 🧹 Sanitização da Cidade

O campo `cidadeDoTitularDaConta` é sanitizado automaticamente:

| Entrada | Resultado |
|---------|-----------|
| `São Paulo` | `Sao Paulo` |
| `Florianópolis` | `Florianopolis` |
| `Açailândia` | `Acailandia` |
| `Rio de Janeiro!@#` | `Rio de Janeiro` |
| `   Goiânia   ` | `Goiania` |
| `null` | `''` (vazio) |

Não é necessário tratar manualmente — o pacote cuida disso para você.

---

## 🧪 Testes

O pacote conta com **57 testes** e **79 assertions** usando Pest PHP:

```bash
composer test
```

Cobertura dos testes:
- ✅ Interface fluente (todos os setters)
- ✅ Sanitização de cidade (13 cenários)
- ✅ Formatação de valor
- ✅ Tratamento de null
- ✅ Estrutura do payload PIX
- ✅ CRC16 (consistência e unicidade)
- ✅ Geração de QR Code
- ✅ Prioridade config vs parâmetro
- ✅ Tipos inteiros no `cobrar()`
- ✅ Encadeamento completo

---

## 📐 Estrutura do Payload PIX

O código gerado segue a especificação EMVCo / BACEN:

```
┌─────────────────────────────────────────────────────────────┐
│ 00 │ Payload Format Indicator (01)                          │
│ 26 │ Merchant Account Information                           │
│    ├── 00 │ GUI (br.gov.bcb.pix)                           │
│    ├── 01 │ Chave PIX                                      │
│    └── 02 │ Descrição (opcional)                           │
│ 52 │ Merchant Category Code (0000)                          │
│ 53 │ Transaction Currency (986 = BRL)                       │
│ 54 │ Transaction Amount                                     │
│ 58 │ Country Code (BR)                                      │
│ 59 │ Merchant Name                                          │
│ 60 │ Merchant City                                          │
│ 62 │ Additional Data Field                                  │
│    └── 05 │ TXID                                           │
│ 63 │ CRC16 (checksum)                                       │
└─────────────────────────────────────────────────────────────┘
```

---

## 📁 Estrutura do Pacote

```
larapix/
├── config/
│   └── larapix.php              # Configurações do pacote
├── resources/views/
│   └── pix-qrcode.blade.php     # View com QR Code (Bootstrap 5)
├── routes/
│   └── web.php                  # Rota de exemplo
├── src/
│   ├── Constants/
│   │   └── Constants.php        # IDs dos campos do payload
│   ├── Contracts/
│   │   └── LarapixInterface.php # Interface do contrato
│   ├── Facades/
│   │   └── Larapix.php          # Facade Laravel
│   ├── Larapix.php              # Classe principal
│   └── LarapixServiceProvider.php
└── tests/
    ├── LarapixTest.php          # 56 testes funcionais
    ├── ArchTest.php             # Testes de arquitetura
    └── ...
```

---

## 🔗 Requisitos

| Requisito | Versão |
|-----------|--------|
| PHP | ^8.1 |
| Laravel | 10.x / 11.x / 12.x |
| ext-intl | Requerido (para `Normalizer`) |
| ext-mbstring | Requerido |
| mpdf/qrcode | ^1.2 |

---

## 📋 Changelog

Veja [CHANGELOG.md](CHANGELOG.md) para detalhes sobre as mudanças em cada versão.

---

## 🤝 Contribuindo

Contribuições são bem-vindas! Por favor, veja [CONTRIBUTING.md](CONTRIBUTING.md) para detalhes.

```bash
# Clonar o repositório
git clone https://github.com/modularavel/larapix.git
cd larapix

# Instalar dependências
composer install

# Rodar testes
composer test

# Formatar código
composer format

# Análise estática
composer analyse
```

---

## 🔒 Vulnerabilidades de Segurança

Para reportar vulnerabilidades de segurança, consulte nossa [política de segurança](../../security/policy).

---

## 👨‍💻 Autor

<table>
  <tr>
    <td align="center">
      <a href="https://github.com/casimirorocha">
        <img src="https://github.com/casimirorocha.png" width="100px;" alt="Casimiro Rocha"/><br />
        <sub><b>Casimiro Rocha</b></sub>
      </a><br />
      <a href="mailto:casimiroaf@gmail.com">📧 casimiroaf@gmail.com</a>
    </td>
  </tr>
</table>

---

## 📜 Licença

Este projeto é licenciado sob a **Licença MIT** — veja o arquivo [LICENSE.md](LICENSE.md) para detalhes.

---

## 🌟 Apoie o Projeto

Se este pacote foi útil para você, considere dar uma ⭐ no [repositório](https://github.com/modularavel/larapix)!

---

<p align="center">
  Feito com ❤️ por <a href="https://github.com/modularavel">Modularavel</a>
</p>

<p align="center">
  <strong>Versão atual: 1.2.0</strong>
</p>
