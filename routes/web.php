<?php

use Illuminate\Support\Facades\Route;
use Modularavel\Larapix\Facades\Larapix;

Route::get('/', function () {
    // INSTÂNCIA PRINCIPAL DO PAYLOAD PIX
    $pixPayload = Larapix::cobrar(
        valor: 50,
        descricao: 'Furar fila',
        txid: 'casimirorocha'
    );

    // CÓDIGO DE PAGAMENTO PIX
    $codigo = $pixPayload->gerarCodigoDePagamento();

    $qrCode = $pixPayload->gerarQRCodeDePagamento($codigo);

    return view('larapix::pix-qrcode', [
        'image' => $qrCode,
        'codigo' => $codigo,
    ]);
});
