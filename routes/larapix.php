<?php

use Illuminate\Support\Facades\Route;
use Modularavel\Larapix\Facades\Larapix;

Route::get('/', function () {
    // INSTÂNCIA PRINCIPAL DO PAYLOAD PIX
    $pix = Larapix::cobrar(
        valor: 50,
        descricao: 'Furar fila',
        txid: 'casimirorocha'
    );

    // CÓDIGO DE PAGAMENTO PIX
    $codigo = $pix->gerarCodigoDePagamento();

    $qrCode = $pix->gerarQRCodeDePagamento($codigo);

    return view('larapix::pix-qrcode', [
        'image' => $qrCode,
        'codigo' => $codigo,
    ]);
});
