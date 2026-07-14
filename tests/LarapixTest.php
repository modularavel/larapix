<?php

use Modularavel\Larapix\Larapix;
use Modularavel\Larapix\Facades\Larapix as LarapixFacade;
use Mpdf\QrCode\Output\Png;

it('can create an instance of Larapix', function () {
    $larapix = new Larapix();
    expect($larapix)->toBeInstanceOf(Larapix::class);
});

it('can set and get chave pix via fluent interface', function () {
    $larapix = new Larapix();
    $result = $larapix->chavePix('12345678909');
    expect($result)->toBe($larapix);
});

it('can set and get nome do titular via fluent interface', function () {
    $larapix = new Larapix();
    $result = $larapix->nomeDoTitularDaConta('João da Silva');
    expect($result)->toBe($larapix);
});

it('can set and get cidade do titular via fluent interface', function () {
    $larapix = new Larapix();
    $result = $larapix->cidadeDoTitularDaConta('SAO PAULO');
    expect($result)->toBe($larapix);
});

it('can set and get valor via fluent interface', function () {
    $larapix = new Larapix();
    $result = $larapix->valor(100.50);
    expect($result)->toBe($larapix);
});

it('can set and get descricao via fluent interface', function () {
    $larapix = new Larapix();
    $result = $larapix->descricao('Pagamento de exemplo');
    expect($result)->toBe($larapix);
});

it('can set and get txid via fluent interface', function () {
    $larapix = new Larapix();
    $result = $larapix->txid('TEST123');
    expect($result)->toBe($larapix);
});

it('can generate a pix code', function () {
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

it('can generate a qr code image', function () {
    $larapix = (new Larapix())
        ->chavePix('12345678909')
        ->nomeDoTitularDaConta('João da Silva')
        ->cidadeDoTitularDaConta('SAO PAULO')
        ->valor(100.00);

    $code = $larapix->gerarCodigoDePagamento();
    $qrCode = $larapix->gerarQRCodeDePagamento($code);
    expect($qrCode)->toBeString()
        ->and(strlen($qrCode))->toBeGreaterThan(0);

    // Check if it's a valid PNG image
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    expect($finfo->buffer($qrCode))->toBe('image/png');
});

it('can generate and save a qr code image to file', function () {
    $larapix = (new Larapix())
        ->chavePix('98765432100')
        ->nomeDoTitularDaConta('Maria Souza')
        ->cidadeDoTitularDaConta('RIO DE JANEIRO')
        ->valor(50.00);

    $code = $larapix->gerarCodigoDePagamento();
    $qrCodeData = $larapix->gerarQRCodeDePagamento($code);

    // Create a temporary file to save the QR code
    $tempDir = sys_get_temp_dir();
    $filePath = $tempDir . '/larapix-test-qrcode.png';
    file_put_contents($filePath, $qrCodeData);

    // Verify the file exists and is a valid PNG
    expect(file_exists($filePath))->toBeTrue();
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    expect($finfo->file($filePath))->toBe('image/png');

    // Clean up
    unlink($filePath);
});

it('can use the cobrar method to create a new instance', function () {
    $larapix = Larapix::cobrar(50.00, '98765432100', 'Maria Souza', 'RIO DE JANEIRO', 'Outro teste', 'TXID456');
    expect($larapix)->toBeInstanceOf(Larapix::class);
});

it('can use the facade', function () {
    $larapix = LarapixFacade::cobrar(75.00);
    expect($larapix)->toBeInstanceOf(Larapix::class);
    $code = $larapix
        ->chavePix('11122233344')
        ->nomeDoTitularDaConta('Carlos Pereira')
        ->cidadeDoTitularDaConta('BELO HORIZONTE')
        ->gerarCodigoDePagamento();
    expect($code)->toBeString();
});

it('uses config values when parameters are not provided', function () {
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
