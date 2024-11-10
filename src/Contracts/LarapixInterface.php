<?php

namespace Modularavel\Larapix\Contracts;

use Mpdf\QrCode\Output\Png;

interface LarapixInterface
{
    public function chavePix(string $chavePix): static;

    public function nomeDoTitularDaConta(string $nomeDoTitularDaConta): static;

    public function cidadeDoTitularDaConta(string $cidadeDoTitularDaConta): static;

    public function valor(float $valor): static;

    public function descricao(string $descricao): static;

    public function txid(string $txid): static;

    public function gerarCodigoDePagamento(): string;

    public function gerarQRCodeDePagamento(string $codigoPagamento, string $imageType = Png::class, int $w = 600): string;
}
