<?php

namespace Modularavel\Larapix\Contracts;

use Mpdf\QrCode\Output\Png;

/**
 * Interface para classes de geração de pagamentos PIX
 */
interface LarapixInterface
{
    /**
     * Define a chave PIX
     *
     * @param string|null $chavePix Chave PIX (CPF, CNPJ, e-mail, telefone ou aleatória)
     * @return static
     */
    public function chavePix(?string $chavePix): static;

    /**
     * Define o nome do titular da conta
     *
     * @param string|null $nomeDoTitularDaConta Nome completo do titular
     * @return static
     */
    public function nomeDoTitularDaConta(?string $nomeDoTitularDaConta): static;

    /**
     * Define a cidade do titular da conta
     *
     * @param string|null $cidadeDoTitularDaConta Cidade (sem acentos ou caracteres especiais)
     * @return static
     */
    public function cidadeDoTitularDaConta(?string $cidadeDoTitularDaConta): static;

    /**
     * Define o valor da transação
     *
     * @param float $valor Valor em reais
     * @return static
     */
    public function valor(float $valor): static;

    /**
     * Define a descrição do pagamento
     *
     * @param string|null $descricao Descrição opcional
     * @return static
     */
    public function descricao(?string $descricao): static;

    /**
     * Define o TXID (identificador único da transação)
     *
     * @param string|null $txid Identificador único
     * @return static
     */
    public function txid(?string $txid): static;

    /**
     * Gera o código completo de pagamento PIX (cópia e cola)
     *
     * @return string Código PIX completo com CRC16
     */
    public function gerarCodigoDePagamento(): string;

    /**
     * Gera a imagem QR Code a partir do código de pagamento PIX
     *
     * @param string $codigoPagamento Código PIX gerado por gerarCodigoDePagamento()
     * @param string $imageType Tipo de imagem (padrão: PNG)
     * @param int $w Largura da imagem em pixels
     * @return string Dados binários da imagem
     */
    public function gerarQRCodeDePagamento(string $codigoPagamento, string $imageType = Png::class, int $w = 600): string;
}
