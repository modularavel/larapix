<?php

use Modularavel\Larapix\Larapix;
use Modularavel\Larapix\Facades\Larapix as LarapixFacade;

it('pode criar uma instancia do Larapix', function () {
    $larapix = new Larapix();
    expect($larapix)->toBeInstanceOf(Larapix::class);
});

it('pode definir chave pix via interface fluente', function () {
    $larapix = new Larapix();
    $result = $larapix->chavePix('12345678909');
    expect($result)->toBe($larapix);
});

it('pode definir nome do titular via interface fluente', function () {
    $larapix = new Larapix();
    $result = $larapix->nomeDoTitularDaConta('João da Silva');
    expect($result)->toBe($larapix);
});

it('pode definir cidade do titular via interface fluente', function () {
    $larapix = new Larapix();
    $result = $larapix->cidadeDoTitularDaConta('SAO PAULO');
    expect($result)->toBe($larapix);
});

it('pode definir valor via interface fluente', function () {
    $larapix = new Larapix();
    $result = $larapix->valor(100.50);
    expect($result)->toBe($larapix);
});

it('pode definir descricao via interface fluente', function () {
    $larapix = new Larapix();
    $result = $larapix->descricao('Pagamento de exemplo');
    expect($result)->toBe($larapix);
});

it('pode definir txid via interface fluente', function () {
    $larapix = new Larapix();
    $result = $larapix->txid('TEST123');
    expect($result)->toBe($larapix);
});

it('pode gerar um codigo pix', function () {
    $larapix = (new Larapix())
        ->chavePix('12345678909')
        ->nomeDoTitularDaConta('João da Silva')
        ->cidadeDoTitularDaConta('SAO PAULO')
        ->valor(100.00)
        ->descricao('Teste')
        ->txid('TEST123');

    $code = $larapix->gerarCodigoDePagamento();
    expect($code)->toBeString()
        ->and(strlen($code))->toBeGreaterThan(0);
});

it('pode gerar uma imagem de qr code', function () {
    $larapix = (new Larapix())
        ->chavePix('12345678909')
        ->nomeDoTitularDaConta('João da Silva')
        ->cidadeDoTitularDaConta('SAO PAULO')
        ->valor(100.00);

    $code = $larapix->gerarCodigoDePagamento();
    $qrCode = $larapix->gerarQRCodeDePagamento($code);
    expect($qrCode)->toBeString()
        ->and(strlen($qrCode))->toBeGreaterThan(0);

    // Verifica se e uma imagem PNG valida
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    expect($finfo->buffer($qrCode))->toBe('image/png');
});

it('pode gerar e salvar uma imagem de qr code em arquivo', function () {
    $larapix = (new Larapix())
        ->chavePix('98765432100')
        ->nomeDoTitularDaConta('Maria Souza')
        ->cidadeDoTitularDaConta('RIO DE JANEIRO')
        ->valor(50.00);

    $code = $larapix->gerarCodigoDePagamento();
    $qrCodeData = $larapix->gerarQRCodeDePagamento($code);

    // Cria um arquivo temporario para salvar o QR code
    $tempDir = sys_get_temp_dir();
    $filePath = $tempDir . '/larapix-test-qrcode.png';
    file_put_contents($filePath, $qrCodeData);

    // Verifica se o arquivo existe e e um PNG valido
    expect(file_exists($filePath))->toBeTrue();
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    expect($finfo->file($filePath))->toBe('image/png');

    // Limpeza
    unlink($filePath);
});

it('pode usar o metodo cobrar para criar uma nova instancia', function () {
    $larapix = Larapix::cobrar(50.00, '98765432100', 'Maria Souza', 'RIO DE JANEIRO', 'Outro teste', 'TXID456');
    expect($larapix)->toBeInstanceOf(Larapix::class);
});

it('pode usar a facade', function () {
    $larapix = LarapixFacade::cobrar(75.00);
    expect($larapix)->toBeInstanceOf(Larapix::class);
    $code = $larapix
        ->chavePix('11122233344')
        ->nomeDoTitularDaConta('Carlos Pereira')
        ->cidadeDoTitularDaConta('BELO HORIZONTE')
        ->gerarCodigoDePagamento();
    expect($code)->toBeString();
});

it('usa valores do config quando parametros nao sao fornecidos', function () {
    config()->set('larapix.chave_pix', 'config-chave-123');
    config()->set('larapix.nome_do_titular', 'Config User');
    config()->set('larapix.cidade_do_titular', 'CIDADE CONFIG');
    config()->set('larapix.valor', 99.99);
    config()->set('larapix.descricao', 'Config Desc');
    config()->set('larapix.id_transacao', 'CONFIG-TXID');

    $larapix = new Larapix();
    $code = $larapix->gerarCodigoDePagamento();
    expect($code)->toBeString();
});

it('sanitiza cidade sem caracteres especiais', function () {
    $larapix = new Larapix();
    $larapix->cidadeDoTitularDaConta('SAO PAULO');

    $reflection = new ReflectionClass($larapix);
    $property = $reflection->getProperty('cidadeDoTitularDaConta');
    $property->setAccessible(true);

    expect($property->getValue($larapix))->toBe('SAO PAULO');
});

it('sanitiza cidade com caracteres especiais e acentos', function () {
    $larapix = new Larapix();
    $larapix->cidadeDoTitularDaConta('São Paulo!@#$%^&*()');

    $reflection = new ReflectionClass($larapix);
    $property = $reflection->getProperty('cidadeDoTitularDaConta');
    $property->setAccessible(true);

    expect($property->getValue($larapix))->toBe('Sao Paulo');
});

it('sanitiza cidade com multiplos acentos e espacos', function () {
    $larapix = new Larapix();
    $larapix->cidadeDoTitularDaConta('Rio de Janeiro!  Carioca 123');

    $reflection = new ReflectionClass($larapix);
    $property = $reflection->getProperty('cidadeDoTitularDaConta');
    $property->setAccessible(true);

    expect($property->getValue($larapix))->toBe('Rio de Janeiro Carioca 123');
});

it('sanitiza cidade com cedilha', function () {
    $larapix = new Larapix();
    $larapix->cidadeDoTitularDaConta('Açailândia');

    $reflection = new ReflectionClass($larapix);
    $property = $reflection->getProperty('cidadeDoTitularDaConta');
    $property->setAccessible(true);

    expect($property->getValue($larapix))->toBe('Acailandia');
});

it('sanitiza cidade com til', function () {
    $larapix = new Larapix();
    $larapix->cidadeDoTitularDaConta('São João del-Rei');

    $reflection = new ReflectionClass($larapix);
    $property = $reflection->getProperty('cidadeDoTitularDaConta');
    $property->setAccessible(true);

    expect($property->getValue($larapix))->toBe('Sao Joao delRei');
});

it('sanitiza cidade com circunflexo e acento agudo', function () {
    $larapix = new Larapix();
    $larapix->cidadeDoTitularDaConta('Ângelo José');

    $reflection = new ReflectionClass($larapix);
    $property = $reflection->getProperty('cidadeDoTitularDaConta');
    $property->setAccessible(true);

    expect($property->getValue($larapix))->toBe('Angelo Jose');
});

it('sanitiza cidade com acento grave e trema', function () {
    $larapix = new Larapix();
    $larapix->cidadeDoTitularDaConta('Über Città');

    $reflection = new ReflectionClass($larapix);
    $property = $reflection->getProperty('cidadeDoTitularDaConta');
    $property->setAccessible(true);

    expect($property->getValue($larapix))->toBe('Uber Citta');
});

it('sanitiza cidade com numeros preservando-os', function () {
    $larapix = new Larapix();
    $larapix->cidadeDoTitularDaConta('Região 42');

    $reflection = new ReflectionClass($larapix);
    $property = $reflection->getProperty('cidadeDoTitularDaConta');
    $property->setAccessible(true);

    expect($property->getValue($larapix))->toBe('Regiao 42');
});

it('sanitiza cidade tratando valor nulo', function () {
    $larapix = new Larapix();
    $larapix->cidadeDoTitularDaConta(null);

    $reflection = new ReflectionClass($larapix);
    $property = $reflection->getProperty('cidadeDoTitularDaConta');
    $property->setAccessible(true);

    expect($property->getValue($larapix))->toBe('');
});

it('sanitiza cidade com apenas caracteres especiais retornando vazio', function () {
    $larapix = new Larapix();
    $larapix->cidadeDoTitularDaConta('!@#$%^&*()-+=[]{}');

    $reflection = new ReflectionClass($larapix);
    $property = $reflection->getProperty('cidadeDoTitularDaConta');
    $property->setAccessible(true);

    expect($property->getValue($larapix))->toBe('');
});

it('sanitiza cidade com espacos no inicio e fim', function () {
    $larapix = new Larapix();
    $larapix->cidadeDoTitularDaConta('   Goiânia   ');

    $reflection = new ReflectionClass($larapix);
    $property = $reflection->getProperty('cidadeDoTitularDaConta');
    $property->setAccessible(true);

    expect($property->getValue($larapix))->toBe('Goiania');
});

it('sanitiza cidade usada no metodo cobrar via config', function () {
    config()->set('larapix.cidade_do_titular', 'Florianópolis');

    $larapix = Larapix::cobrar(100.00, '12345678909', 'Fulano', null, 'Desc', 'TX1');

    $reflection = new ReflectionClass($larapix);
    $property = $reflection->getProperty('cidadeDoTitularDaConta');
    $property->setAccessible(true);

    expect($property->getValue($larapix))->toBe('Florianopolis');
});

it('sanitiza cidade definida via metodo fluente apos cobrar', function () {
    $larapix = Larapix::cobrar(100.00, '12345678909', 'Fulano')
        ->cidadeDoTitularDaConta('Florianópolis');

    $reflection = new ReflectionClass($larapix);
    $property = $reflection->getProperty('cidadeDoTitularDaConta');
    $property->setAccessible(true);

    expect($property->getValue($larapix))->toBe('Florianopolis');
});

it('gera codigo pix valido com cidade acentuada', function () {
    $larapix = (new Larapix())
        ->chavePix('12345678909')
        ->nomeDoTitularDaConta('Joao da Silva')
        ->cidadeDoTitularDaConta('Uberlândia')
        ->valor(50.00)
        ->txid('TX001');

    $code = $larapix->gerarCodigoDePagamento();

    // O codigo gerado nao deve conter caracteres acentuados
    expect($code)->toBeString()
        ->and($code)->not->toContain('â')
        ->and($code)->toContain('Uberlandia');
});

// --- Testes de formatacao do valor ---

it('formata valor com duas casas decimais', function () {
    $larapix = new Larapix();
    $larapix->valor(100.5);

    $reflection = new ReflectionClass($larapix);
    $property = $reflection->getProperty('valor');
    $property->setAccessible(true);

    expect($property->getValue($larapix))->toBe('100.50');
});

it('formata valor zero corretamente', function () {
    $larapix = new Larapix();
    $larapix->valor(0);

    $reflection = new ReflectionClass($larapix);
    $property = $reflection->getProperty('valor');
    $property->setAccessible(true);

    expect($property->getValue($larapix))->toBe('0.00');
});

it('formata valor com muitas casas decimais arredondando para duas', function () {
    $larapix = new Larapix();
    $larapix->valor(19.999);

    $reflection = new ReflectionClass($larapix);
    $property = $reflection->getProperty('valor');
    $property->setAccessible(true);

    expect($property->getValue($larapix))->toBe('20.00');
});

it('formata valor grande sem separador de milhares', function () {
    $larapix = new Larapix();
    $larapix->valor(1234567.89);

    $reflection = new ReflectionClass($larapix);
    $property = $reflection->getProperty('valor');
    $property->setAccessible(true);

    expect($property->getValue($larapix))->toBe('1234567.89');
});

// --- Testes de tratamento de nulo nos setters ---

it('trata chavePix nula sem erro', function () {
    $larapix = new Larapix();
    $larapix->chavePix(null);

    $reflection = new ReflectionClass($larapix);
    $property = $reflection->getProperty('chavePix');
    $property->setAccessible(true);

    expect($property->getValue($larapix))->toBe('');
});

it('trata nomeDoTitularDaConta nulo sem erro', function () {
    $larapix = new Larapix();
    $larapix->nomeDoTitularDaConta(null);

    $reflection = new ReflectionClass($larapix);
    $property = $reflection->getProperty('nomeDoTitularDaConta');
    $property->setAccessible(true);

    expect($property->getValue($larapix))->toBe('');
});

it('trata descricao nula sem erro', function () {
    $larapix = new Larapix();
    $larapix->descricao(null);

    $reflection = new ReflectionClass($larapix);
    $property = $reflection->getProperty('descricao');
    $property->setAccessible(true);

    expect($property->getValue($larapix))->toBe('');
});

it('trata txid nulo sem erro', function () {
    $larapix = new Larapix();
    $larapix->txid(null);

    $reflection = new ReflectionClass($larapix);
    $property = $reflection->getProperty('txid');
    $property->setAccessible(true);

    expect($property->getValue($larapix))->toBe('');
});

// --- Testes de estrutura do payload PIX ---

it('gera codigo pix iniciando com indicador de formato 000201', function () {
    $larapix = (new Larapix())
        ->chavePix('12345678909')
        ->nomeDoTitularDaConta('Fulano')
        ->cidadeDoTitularDaConta('SAO PAULO')
        ->valor(10.00)
        ->txid('ABC');

    $code = $larapix->gerarCodigoDePagamento();

    // Indicador de formato do payload: ID=00, tamanho=02, valor=01
    expect($code)->toStartWith('000201');
});

it('gera codigo pix contendo informacoes da conta do recebedor com gui', function () {
    $larapix = (new Larapix())
        ->chavePix('teste@email.com')
        ->nomeDoTitularDaConta('Fulano')
        ->cidadeDoTitularDaConta('CURITIBA')
        ->valor(25.00)
        ->txid('XYZ');

    $code = $larapix->gerarCodigoDePagamento();

    // Deve conter o identificador GUI do BACEN
    expect($code)->toContain('br.gov.bcb.pix');
});

it('gera codigo pix contendo o valor da chave pix', function () {
    $chave = 'minha-chave-aleatoria-uuid';
    $larapix = (new Larapix())
        ->chavePix($chave)
        ->nomeDoTitularDaConta('Test')
        ->cidadeDoTitularDaConta('BRASILIA')
        ->valor(1.00)
        ->txid('T1');

    $code = $larapix->gerarCodigoDePagamento();

    expect($code)->toContain($chave);
});

it('gera codigo pix contendo o nome do recebedor', function () {
    $larapix = (new Larapix())
        ->chavePix('123')
        ->nomeDoTitularDaConta('Nome Do Recebedor')
        ->cidadeDoTitularDaConta('RECIFE')
        ->valor(5.00)
        ->txid('T2');

    $code = $larapix->gerarCodigoDePagamento();

    expect($code)->toContain('Nome Do Recebedor');
});

it('gera codigo pix contendo o valor da transacao', function () {
    $larapix = (new Larapix())
        ->chavePix('123')
        ->nomeDoTitularDaConta('Test')
        ->cidadeDoTitularDaConta('SP')
        ->valor(149.99)
        ->txid('T3');

    $code = $larapix->gerarCodigoDePagamento();

    expect($code)->toContain('149.99');
});

it('gera codigo pix contendo o txid', function () {
    $larapix = (new Larapix())
        ->chavePix('123')
        ->nomeDoTitularDaConta('Test')
        ->cidadeDoTitularDaConta('SP')
        ->valor(10.00)
        ->txid('MEU-TXID-UNICO');

    $code = $larapix->gerarCodigoDePagamento();

    expect($code)->toContain('MEU-TXID-UNICO');
});

it('gera codigo pix contendo codigo do pais BR', function () {
    $larapix = (new Larapix())
        ->chavePix('123')
        ->nomeDoTitularDaConta('Test')
        ->cidadeDoTitularDaConta('SP')
        ->valor(10.00)
        ->txid('T4');

    $code = $larapix->gerarCodigoDePagamento();

    // Campo do pais: ID=58, tamanho=02, valor=BR
    expect($code)->toContain('5802BR');
});

it('gera codigo pix contendo codigo da moeda 986 para BRL', function () {
    $larapix = (new Larapix())
        ->chavePix('123')
        ->nomeDoTitularDaConta('Test')
        ->cidadeDoTitularDaConta('SP')
        ->valor(10.00)
        ->txid('T5');

    $code = $larapix->gerarCodigoDePagamento();

    // Campo da moeda: ID=53, tamanho=03, valor=986
    expect($code)->toContain('5303986');
});

it('gera codigo pix terminando com campo CRC16 (ID 63)', function () {
    $larapix = (new Larapix())
        ->chavePix('12345678909')
        ->nomeDoTitularDaConta('Fulano')
        ->cidadeDoTitularDaConta('SAO PAULO')
        ->valor(100.00)
        ->txid('CRC-TEST');

    $code = $larapix->gerarCodigoDePagamento();

    // CRC16 sao 4 caracteres hexadecimais no final, precedidos por ID=63 e tamanho=04
    expect($code)->toMatch('/6304[A-F0-9]{4}$/');
});

it('gera codigo pix sem descricao quando descricao esta vazia', function () {
    $larapix = (new Larapix())
        ->chavePix('12345678909')
        ->nomeDoTitularDaConta('Fulano')
        ->cidadeDoTitularDaConta('SAO PAULO')
        ->valor(50.00)
        ->descricao('')
        ->txid('NO-DESC');

    $code = $larapix->gerarCodigoDePagamento();

    // O codigo deve ser valido mesmo assim
    expect($code)->toBeString()
        ->and($code)->toMatch('/6304[A-F0-9]{4}$/');
});

it('gera codigo pix incluindo descricao quando fornecida', function () {
    $larapix = (new Larapix())
        ->chavePix('12345678909')
        ->nomeDoTitularDaConta('Fulano')
        ->cidadeDoTitularDaConta('SAO PAULO')
        ->valor(50.00)
        ->descricao('Pagamento mensal')
        ->txid('DESC-TEST');

    $code = $larapix->gerarCodigoDePagamento();

    expect($code)->toContain('Pagamento mensal');
});

// --- Testes de consistencia do CRC16 ---

it('gera CRC16 consistente para a mesma entrada', function () {
    $buildLarapix = fn () => (new Larapix())
        ->chavePix('12345678909')
        ->nomeDoTitularDaConta('Joao')
        ->cidadeDoTitularDaConta('SAO PAULO')
        ->valor(100.00)
        ->txid('CONSISTENCY');

    $code1 = $buildLarapix()->gerarCodigoDePagamento();
    $code2 = $buildLarapix()->gerarCodigoDePagamento();

    expect($code1)->toBe($code2);
});

it('gera CRC16 diferente para entradas diferentes', function () {
    $larapix1 = (new Larapix())
        ->chavePix('12345678909')
        ->nomeDoTitularDaConta('Joao')
        ->cidadeDoTitularDaConta('SAO PAULO')
        ->valor(100.00)
        ->txid('TX-A');

    $larapix2 = (new Larapix())
        ->chavePix('12345678909')
        ->nomeDoTitularDaConta('Joao')
        ->cidadeDoTitularDaConta('SAO PAULO')
        ->valor(200.00)
        ->txid('TX-B');

    $code1 = $larapix1->gerarCodigoDePagamento();
    $code2 = $larapix2->gerarCodigoDePagamento();

    expect($code1)->not->toBe($code2);
});

// --- Testes com tipos inteiros no cobrar ---

it('aceita chave pix inteira no cobrar', function () {
    $larapix = Larapix::cobrar(10.00, 12345678909);
    expect($larapix)->toBeInstanceOf(Larapix::class);
});

it('aceita txid inteiro no cobrar', function () {
    $larapix = Larapix::cobrar(10.00, '12345678909', 'Test', 'SP', 'Desc', 99999);
    expect($larapix)->toBeInstanceOf(Larapix::class);
});

// --- Testes de geracao do QR Code ---

it('gera qr code com largura personalizada', function () {
    $larapix = (new Larapix())
        ->chavePix('12345678909')
        ->nomeDoTitularDaConta('Test')
        ->cidadeDoTitularDaConta('SP')
        ->valor(10.00);

    $code = $larapix->gerarCodigoDePagamento();
    $qrSmall = $larapix->gerarQRCodeDePagamento($code, \Mpdf\QrCode\Output\Png::class, 200);
    $qrLarge = $larapix->gerarQRCodeDePagamento($code, \Mpdf\QrCode\Output\Png::class, 800);

    // Ambos devem ser PNGs validos
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    expect($finfo->buffer($qrSmall))->toBe('image/png')
        ->and($finfo->buffer($qrLarge))->toBe('image/png');

    // Largura maior deve produzir imagem maior
    expect(strlen($qrLarge))->toBeGreaterThan(strlen($qrSmall));
});

// --- Teste de encadeamento completo da interface fluente ---

it('suporta encadeamento completo de metodos', function () {
    $code = (new Larapix())
        ->chavePix('12345678909')
        ->nomeDoTitularDaConta('Fulano de Tal')
        ->cidadeDoTitularDaConta('SALVADOR')
        ->valor(75.50)
        ->descricao('Servico prestado')
        ->txid('CHAIN-TEST')
        ->gerarCodigoDePagamento();

    expect($code)->toBeString()
        ->and($code)->toStartWith('000201')
        ->and($code)->toMatch('/6304[A-F0-9]{4}$/');
});

// --- Testes de prioridade do TXID: parametro vs config ---

it('usa txid do parametro quando fornecido, ignorando config', function () {
    config()->set('larapix.id_transacao', 'CONFIG-TXID');

    $larapix = new Larapix(10, '123', 'Test', 'SP', '', 'PARAM-TXID');

    $reflection = new ReflectionClass($larapix);
    $property = $reflection->getProperty('txid');
    $property->setAccessible(true);

    expect($property->getValue($larapix))->toBe('PARAM-TXID');
});

it('usa txid do config quando parametro esta vazio', function () {
    config()->set('larapix.id_transacao', 'CONFIG-TXID');

    $larapix = new Larapix(10, '123', 'Test', 'SP', '', '');

    $reflection = new ReflectionClass($larapix);
    $property = $reflection->getProperty('txid');
    $property->setAccessible(true);

    expect($property->getValue($larapix))->toBe('CONFIG-TXID');
});

// --- Testes com valores do config ---

it('usa todos os valores do config para gerar um codigo pix valido', function () {
    config()->set('larapix.chave_pix', '11122233344');
    config()->set('larapix.nome_do_titular', 'Empresa LTDA');
    config()->set('larapix.cidade_do_titular', 'MANAUS');
    config()->set('larapix.valor', 250.00);
    config()->set('larapix.descricao', 'Assinatura');
    config()->set('larapix.id_transacao', 'SUB-001');

    $larapix = new Larapix();
    $code = $larapix->gerarCodigoDePagamento();

    expect($code)->toBeString()
        ->and($code)->toContain('11122233344')
        ->and($code)->toContain('Empresa LTDA')
        ->and($code)->toContain('MANAUS')
        ->and($code)->toContain('250.00')
        ->and($code)->toContain('Assinatura')
        ->and($code)->toContain('SUB-001')
        ->and($code)->toMatch('/6304[A-F0-9]{4}$/');
});

// --- Caso limite: txid vazio gera codigo valido ---

it('gera codigo pix valido com txid vazio', function () {
    $larapix = (new Larapix())
        ->chavePix('12345678909')
        ->nomeDoTitularDaConta('Test')
        ->cidadeDoTitularDaConta('SP')
        ->valor(10.00)
        ->txid('');

    $code = $larapix->gerarCodigoDePagamento();

    expect($code)->toBeString()
        ->and($code)->toMatch('/6304[A-F0-9]{4}$/');
});
