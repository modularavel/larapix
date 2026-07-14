<?php

namespace Modularavel\Larapix;

use Illuminate\Support\Str;
use Modularavel\Larapix\Constants\Constants;
use Modularavel\Larapix\Contracts\LarapixInterface;
use Mpdf\QrCode\Output\Png;
use Mpdf\QrCode\QrCode;
use Mpdf\QrCode\QrCodeException;
use Normalizer;

/**
 * Classe principal para geração de pagamentos PIX (QR Code e código copia e cola)
 */
class Larapix implements LarapixInterface
{
    /**
     * Chave PIX do recebedor
     */
    protected string $chavePix = '';

    /**
     * Nome completo do titular da conta
     */
    protected string $nomeDoTitularDaConta = '';

    /**
     * Cidade do titular da conta (sem caracteres especiais)
     */
    protected string $cidadeDoTitularDaConta = '';

    /**
     * Valor da transação em reais (formato decimal com duas casas)
     */
    protected string $valor = '0.00';

    /**
     * Descrição opcional do pagamento
     */
    protected string $descricao = '';

    /**
     * Identificador único da transação (TXID)
     */
    protected string $txid = '';

    /**
     * Construtor da classe Larapix
     *
     * @param float|int $valor Valor da transação
     * @param string|null $chavePix Chave PIX do recebedor
     * @param string|null $nomeDoTitularDaConta Nome do titular da conta
     * @param string|null $cidadeDoTitularDaConta Cidade do titular
     * @param string|null $descricao Descrição do pagamento
     * @param string|null $txid Identificador único da transação
     */
    public function __construct(
        $valor = 0,
        $chavePix = '',
        $nomeDoTitularDaConta = '',
        $cidadeDoTitularDaConta = '',
        $descricao = '',
        $txid = '',
    ) {
        // Inicializa os atributos usando configurações padrão ou valores passados
        $this->chavePix(config('larapix.chave_pix', $chavePix))
            ->nomeDoTitularDaConta(config('larapix.nome_do_titular', $nomeDoTitularDaConta))
            ->cidadeDoTitularDaConta(config('larapix.cidade_do_titular', $cidadeDoTitularDaConta))
            ->valor(config('larapix.valor', $valor))
            ->descricao(config('larapix.descricao', $descricao))
            ->txid($txid ?: config('larapix.id_transacao', ''));
    }

    /**
     * Cria uma nova instância para cobrança
     *
     * @param float $valor Valor da transação
     * @param string|int|null $chavePix Chave PIX opcional
     * @param string|null $nomeDoTitularDaConta Nome do titular opcional
     * @param string|null $cidadeDoTitularDaConta Cidade do titular opcional
     * @param string|null $descricao Descrição opcional
     * @param string|int|null $txid TXID opcional
     * @return static
     */
    public static function cobrar(float $valor, string|int|null $chavePix = null, ?string $nomeDoTitularDaConta = null, ?string $cidadeDoTitularDaConta = null, ?string $descricao = null, string|int|null $txid = null): static
    {
        return new static($valor, $chavePix, $nomeDoTitularDaConta, $cidadeDoTitularDaConta, $descricao, $txid);
    }

    /**
     * Define o nome do titular da conta
     *
     * @param string|null $nomeDoTitularDaConta Nome completo do titular
     * @return $this
     */
    public function nomeDoTitularDaConta(?string $nomeDoTitularDaConta): static
    {
        $this->nomeDoTitularDaConta = $nomeDoTitularDaConta ?? '';

        return $this;
    }

    /**
     * Define a cidade do titular da conta, sanitizando para remover acentos e caracteres especiais
     *
     * @param string|null $cidadeDoTitularDaConta Cidade (sem acentos ou caracteres especiais)
     * @return $this
     */
    public function cidadeDoTitularDaConta(?string $cidadeDoTitularDaConta): static
    {
        $this->cidadeDoTitularDaConta = $this->sanitizeCity($cidadeDoTitularDaConta);

        return $this;
    }

    /**
     * Sanitiza o nome da cidade: remove acentos e mantém apenas letras, números e espaços
     *
     * @param string|null $city
     * @return string
     */
    private function sanitizeCity(?string $city): string
    {
        if (null === $city) {
            return '';
        }

        // Remove acentos
        $city = Normalizer::normalize($city, Normalizer::FORM_D);
        $city = preg_replace('/[\x{0300}-\x{036F}]/u', '', $city);

        // Mantém apenas letras, números e espaços
        $city = preg_replace('/[^A-Za-z0-9\s]/', '', $city);

        // Remove espaços extras
        $city = trim(preg_replace('/\s+/', ' ', $city));

        return $city;
    }

    /**
     * Define o valor da transação
     *
     * @param float $valor Valor em reais
     * @return $this
     */
    public function valor(float $valor): static
    {
        // Formata o valor para duas casas decimais separadas por ponto
        $this->valor = number_format($valor, 2, '.', '');

        return $this;
    }

    /**
     * Define a descrição do pagamento
     *
     * @param string|null $descricao Descrição opcional
     * @return $this
     */
    public function descricao(?string $descricao): static
    {
        $this->descricao = $descricao ?? '';

        return $this;
    }

    /**
     * Define a chave PIX
     *
     * @param string|null $chavePix Chave PIX (CPF, CNPJ, e-mail, telefone ou aleatória)
     * @return $this
     */
    public function chavePix(?string $chavePix): static
    {
        $this->chavePix = $chavePix ?? '';

        return $this;
    }

    /**
     * Define o TXID (identificador único da transação)
     *
     * @param string|null $txid Identificador único
     * @return $this
     */
    public function txid(?string $txid): static
    {
        $this->txid = $txid ?? '';

        return $this;
    }

    /**
     * Gera um campo do payload PIX no formato ID + TAMANHO + VALOR
     *
     * @param string $id Identificador do campo conforme especificação BACEN
     * @param string $value Conteúdo do campo
     * @return string Campo formatado
     */
    private function getValue(string $id, string $value): string
    {
        // Calcula o tamanho do valor e formata com dois dígitos
        $size = str_pad(strlen($value), 2, '0', STR_PAD_LEFT);

        // Retorna o campo completo no formato ID + TAMANHO + VALOR
        return $id.$size.$value;
    }

    /**
     * Obtém os dados do titular da conta formatados para o payload
     *
     * @return string|null Dados do titular formatados
     */
    private function getDadosDoTitularDaConta(): ?string
    {
        // GUI: Identificador do domínio do BACEN para PIX
        $gui = $this->getValue(Constants::ID_MERCHANT_ACCOUNT_INFORMATION_GUI, 'br.gov.bcb.pix');

        // Chave PIX do recebedor
        $key = $this->getValue(Constants::ID_MERCHANT_ACCOUNT_INFORMATION_KEY, $this->chavePix);

        // Descrição do pagamento (opcional)
        $descricao = strlen($this->descricao) ? $this->getValue(Constants::ID_MERCHANT_ACCOUNT_INFORMATION_DESCRIPTION, $this->descricao) : '';

        // Combina todos os dados da conta em um único campo
        return $this->getValue(Constants::ID_MERCHANT_ACCOUNT_INFORMATION, $gui.$key.$descricao);
    }

    /**
     * Gera o código completo de pagamento PIX (cópia e cola)
     *
     * @return string Código PIX completo com CRC16
     */
    public function gerarCodigoDePagamento(): string
    {
        // Monta o payload PIX com todos os campos obrigatórios e opcionais
        $payload = $this->getValue(Constants::ID_PAYLOAD_FORMAT_INDICATOR, '01')
                    .$this->getDadosDoTitularDaConta()
                    .$this->getValue(Constants::ID_MERCHANT_CATEGORY_CODE, '0000')
                    .$this->getValue(Constants::ID_TRANSACTION_CURRENCY, '986') // Moeda: BRL (986)
                    .$this->getValue(Constants::ID_TRANSACTION_AMOUNT, $this->valor)
                    .$this->getValue(Constants::ID_COUNTRY_CODE, 'BR')
                    .$this->getValue(Constants::ID_MERCHANT_NAME, $this->nomeDoTitularDaConta)
                    .$this->getValue(Constants::ID_MERCHANT_CITY, $this->cidadeDoTitularDaConta)
                    .$this->obterModeloDeDadosAdicionais();

        // Adiciona o CRC16 para validação e retorna o código completo
        return $payload.$this->getCRC16($payload);
    }

    /**
     * Gera a imagem QR Code a partir do código de pagamento PIX
     *
     * @param string $codigoPagamento Código PIX gerado por gerarCodigoDePagamento()
     * @param string $imageType Tipo de imagem (padrão: PNG)
     * @param int $w Largura da imagem em pixels
     * @return string Dados binários da imagem
     *
     * @throws QrCodeException
     */
    public function gerarQRCodeDePagamento(string $codigoPagamento, string $imageType = Png::class, int $w = 600): string
    {
        // Cria o objeto QR Code com o código de pagamento
        $objetoQrCode = new QrCode($codigoPagamento);

        // Gera a imagem no formato especificado e retorna os dados binários
        return (new $imageType)->output($objetoQrCode, $w);
    }

    /**
     * Obtém o campo de dados adicionais (incluindo o TXID)
     *
     * @return string Campo de dados adicionais formatado
     */
    private function obterModeloDeDadosAdicionais(): string
    {
        // TXID: Identificador único da transação
        $txid = $this->getValue(Constants::ID_ADDITIONAL_DATA_FIELD_TEMPLATE_TXID, $this->txid);

        // Combina o TXID no campo de dados adicionais
        return $this->getValue(Constants::ID_ADDITIONAL_DATA_FIELD_TEMPLATE, $txid);
    }

    /**
     * Calcula o CRC16 (checksum) para validação do código PIX
     *
     * @param string $payload Payload PIX sem o CRC16
     * @return string CRC16 formatado
     */
    private function getCRC16(string $payload): string
    {
        // Adiciona o marcador do CRC16 ao payload para cálculo
        $payload .= Constants::ID_CRC16.'04';

        // Parâmetros do algoritmo CRC16-CCITT definidos pelo BACEN
        $polinomio = 0x1021;
        $resultado = 0xFFFF;

        // Obtém o comprimento do payload usando Str::length para suporte a UTF-8
        $length = Str::length($payload);

        // Calcula o checksum iterando sobre cada byte do payload
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

        // Retorna o CRC16 em hexadecimal maiúsculo com 4 caracteres
        return Constants::ID_CRC16.'04'.strtoupper(dechex($resultado));
    }
}
