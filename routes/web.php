<?php

use Illuminate\Support\Facades\Route;
use Modularavel\Larapix\Facades\Larapix;

// Rota de exemplo para exibição do QR Code e código copia e cola
Route::get('/', function () {
    // Cria uma nova instância de pagamento PIX com valores de exemplo
    $pixPayload = Larapix::cobrar(
        valor: 50,
        descricao: 'Furar fila',
        txid: 'casimirocha'
    );

    // Gera o código de pagamento PIX (cópia e cola)
    $codigo = $pixPayload->gerarCodigoDePagamento();

    // Gera a imagem QR Code
    $qrCode = $pixPayload->gerarQRCodeDePagamento($codigo);

    // Retorna a view com os dados do QR Code e código de pagamento
    return view('larapix::pix-qrcode', [
        'image' => $qrCode,
        'codigo' => $codigo,
    ]);
});
