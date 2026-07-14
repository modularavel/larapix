<?php

namespace Modularavel\Larapix\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Facade para a classe Larapix (geração de pagamentos PIX)
 *
 * @see \Modularavel\Larapix\Larapix
 *
 * @method static self cobrar(float $valor, string|int|null $chavePix = null, ?string $nomeDoTitularDaConta = null, ?string $cidadeDoTitularDaConta = null, ?string $descricao = null, string|int|null $txid = null) Cria uma nova instância para cobrança
 * @method static self chavePix(string $string) Define a chave PIX
 * @method static self nomeDoTitularDaConta(string $merchantName) Define o nome do titular
 * @method static self cidadeDoTitularDaConta(string $merchantCity) Define a cidade do titular
 * @method static self valor(float $setAmount) Define o valor da transação
 * @method static self descricao(string $string) Define a descrição do pagamento
 * @method static self txid(string $txid) Define o TXID
 * @method string gerarCodigoDePagamento() Gera o código PIX copia e cola
 * @method string gerarQRCodeDePagamento(string $codigoPagamento) Gera a imagem QR Code
 */
class Larapix extends Facade
{
    /**
     * Obtém o nome registrado do componente no container
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return \Modularavel\Larapix\Larapix::class;
    }
}
