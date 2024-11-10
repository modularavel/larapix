<?php

namespace Modularavel\Larapix\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Modularavel\Larapix\Larapix
 *
 * @method static self cobrar(float $valor, string|int|null $chavePix = null, ?string $nomeDoTitularDaConta = null, ?string $cidadeDoTitularDaConta = null, ?string $descricao = null, string|int|null $txid = null)
 * @method static self chavePix(string $string)
 * @method static self nomeDoTitularDaConta(string $merchantName)
 * @method static self cidadeDoTitularDaConta(string $merchantCity)
 * @method static self valor(float $setAmount)
 * @method static self descricao(string $string)
 * @method static self txid(string $txid)
 * @method string gerarCodigoDePagamento()
 * @method string gerarQRCodeDePagamento(string $codigoPagamento)
 */
class Larapix extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Modularavel\Larapix\Larapix::class;
    }
}
