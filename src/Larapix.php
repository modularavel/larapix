<?php

namespace Modularavel\Larapix;

use Illuminate\Support\Str;
use Modularavel\Larapix\Constants\Constants;
use Modularavel\Larapix\Contracts\LarapixInterface;
use Mpdf\QrCode\Output\Png;
use Mpdf\QrCode\QrCode;
use Mpdf\QrCode\QrCodeException;

class Larapix implements LarapixInterface
{
    protected string $chavePix;

    protected string $nomeDoTitularDaConta;

    protected string $cidadeDoTitularDaConta;

    protected string $valor;

    protected string $descricao;

    protected string $txid;

    public function __construct(
        $valor = 0,
        $chavePix = '',
        $nomeDoTitularDaConta = '',
        $cidadeDoTitularDaConta = '',
        $descricao = '',
        $txid = '',
    ) {
        $this->chavePix(config('larapix.chave_pix', $chavePix))
            ->nomeDoTitularDaConta(config('larapix.nome_do_titular', $nomeDoTitularDaConta))
            ->cidadeDoTitularDaConta(config('larapix.cidadeDoTitularDaConta_do_titular', $cidadeDoTitularDaConta))
            ->valor($valor)
            ->descricao($descricao)
            ->txid($txid);
    }

    public function cobrar(float $valor, string|int|null $chavePix = null, ?string $nomeDoTitularDaConta = null, ?string $cidadeDoTitularDaConta = null, ?string $descricao = null, string|int|null $txid = null): static
    {
        return new static($valor, $chavePix, $nomeDoTitularDaConta, $cidadeDoTitularDaConta, $descricao, $txid);
    }

    /**
     * Método responsável por definir o valor de $nomeDoTitularDaConta
     *
     * @return $this
     */
    public function nomeDoTitularDaConta(string $nomeDoTitularDaConta): static
    {
        $this->nomeDoTitularDaConta = $nomeDoTitularDaConta;

        return $this;
    }

    /**
     * Método responsável por definir o valor de $cidadeDoTitularDaConta
     *
     * @return $this
     */
    public function cidadeDoTitularDaConta(string $cidadeDoTitularDaConta): static
    {
        $this->cidadeDoTitularDaConta = $cidadeDoTitularDaConta;

        return $this;
    }

    /**
     * Método responsável por definir o valor de $valor
     *
     * @return $this
     */
    public function valor(float $valor): static
    {
        $this->valor = number_format($valor, 2, '.', '');

        return $this;
    }

    /**
     * Método responsável por definir o valor de $descricao
     *
     * @return $this
     */
    public function descricao(string $descricao): static
    {
        $this->descricao = $descricao;

        return $this;
    }

    /**
     * Método responsável por definir o valor de $chavePix
     *
     * @return $this
     */
    public function chavePix(string $chavePix): static
    {
        $this->chavePix = $chavePix;

        return $this;
    }

    /**
     * Método responsável por definir o valor de $txid
     *
     * @return $this
     */
    public function txid(string $txid): static
    {
        $this->txid = $txid;

        return $this;
    }

    /**
     * Responsável por retornar o valor completo de um objeto do payload
     *
     * @return string $id.$size.$value
     */
    private function getValue(string $id, string $value): string
    {
        $size = str_pad(strlen($value), 2, '0', STR_PAD_LEFT);

        return $id.$size.$value;
    }

    /**
     * Método responsável por retornar os valores completos da informação da conta
     */
    private function getDadosDoTitularDaConta(): ?string
    {
        // DOMÍNIO DO BANCO
        $gui = $this->getValue(Constants::ID_MERCHANT_ACCOUNT_INFORMATION_GUI, 'br.gov.bcb.pix');

        // CHAVE PIX
        $key = $this->getValue(Constants::ID_MERCHANT_ACCOUNT_INFORMATION_KEY, $this->chavePix);

        // DESCRIÇÃO DO PAGAMENTO
        $descricao = strlen($this->descricao) ? $this->getValue(Constants::ID_MERCHANT_ACCOUNT_INFORMATION_DESCRIPTION, $this->descricao) : '';

        // VALOR COMPLETO DA CONTA
        return $this->getValue(Constants::ID_MERCHANT_ACCOUNT_INFORMATION, $gui.$key.$descricao);
    }

    /**
     * Método responsável por gerar o código completo do payload Pix
     */
    public function gerarCodigoDePagamento(): string
    {
        // CRIA O PAYLOAD
        $payload = $this->getValue(Constants::ID_PAYLOAD_FORMAT_INDICATOR, '01')
                    .$this->getDadosDoTitularDaConta()
                    .$this->getValue(Constants::ID_MERCHANT_CATEGORY_CODE, '0000')
                    .$this->getValue(Constants::ID_TRANSACTION_CURRENCY, '986')
                    .$this->getValue(Constants::ID_TRANSACTION_AMOUNT, $this->valor)
                    .$this->getValue(Constants::ID_COUNTRY_CODE, 'BR')
                    .$this->getValue(Constants::ID_MERCHANT_NAME, $this->nomeDoTitularDaConta)
                    .$this->getValue(Constants::ID_MERCHANT_CITY, $this->cidadeDoTitularDaConta)
                    .$this->obterModeloDeDadosAdicionais();

        // RETORNA O PAYLOAD + CRC16
        return $payload.$this->getCRC16($payload);
    }

    /**
     * Método responsável por gerar a imagem qrcode através do código pix gerado em self::gerarCodigoDePagamento()
     *
     * @throws QrCodeException
     */
    public function gerarQRCodeDePagamento(string $codigoPagamento, string $imageType = Png::class, int $w = 600): string
    {
        // QRCODE
        $objetoQrCode = new QrCode($codigoPagamento);

        return (new $imageType)->output($objetoQrCode, $w);
    }

    /**
     * Método responsável por retornar os valores completos do campo adicional do pix (TXID)
     */
    private function obterModeloDeDadosAdicionais(): string
    {
        // TXID
        $txid = $this->getValue(Constants::ID_ADDITIONAL_DATA_FIELD_TEMPLATE_TXID, $this->txid);

        // RETORNA O VALOR COMPLETO
        return $this->getValue(Constants::ID_ADDITIONAL_DATA_FIELD_TEMPLATE, $txid);
    }

    /**
     * Método responsável por calcular o valor da hash de validação do código pix
     */
    private function getCRC16(string $payload): string
    {
        // ADICIONA DADOS GERAIS NO PAYLOAD
        $payload .= Constants::ID_CRC16.'04';

        // DADOS DEFINIDOS PELO BACEN
        $polinomio = 0x1021;
        $resultado = 0xFFFF;

        $length = Str::length($payload);

        // CHECKSUM
        if ($length > 0) {
            for ($offset = 0; $offset < $length; $offset++) {
                $resultado ^= (ord($payload[$offset]) << 8);
                for ($bitwise = 0; $bitwise < 8; $bitwise++) {
                    if (($resultado <<= 1) & 0x10000) {
                        $resultado ^= $polinomio;
                    }
                    $resultado &= 0xFFFF;
                }
            }
        }

        // RETORNA CÓDIGO CRC16 DE 4 CARACTERES
        return Constants::ID_CRC16.'04'.strtoupper(dechex($resultado));
    }
}
