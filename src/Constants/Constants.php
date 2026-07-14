<?php

namespace Modularavel\Larapix\Constants;

/**
 * Constantes para identificação dos campos do payload PIX conforme especificação BACEN
 */
class Constants
{
    /**
     * Indicador de formato do payload (obrigatório, valor fixo "01")
     */
    const ID_PAYLOAD_FORMAT_INDICATOR = '00';

    /**
     * Informações da conta do recebedor
     */
    const ID_MERCHANT_ACCOUNT_INFORMATION = '26';

    /**
     * GUI (Globally Unique Identifier) - domínio do BACEN para PIX
     */
    const ID_MERCHANT_ACCOUNT_INFORMATION_GUI = '00';

    /**
     * Chave PIX do recebedor
     */
    const ID_MERCHANT_ACCOUNT_INFORMATION_KEY = '01';

    /**
     * Descrição do pagamento (opcional)
     */
    const ID_MERCHANT_ACCOUNT_INFORMATION_DESCRIPTION = '02';

    /**
     * Categoria do estabelecimento (padrão "0000" se não informado)
     */
    const ID_MERCHANT_CATEGORY_CODE = '52';

    /**
     * Moeda da transação (986 = BRL - Real Brasileiro)
     */
    const ID_TRANSACTION_CURRENCY = '53';

    /**
     * Valor da transação
     */
    const ID_TRANSACTION_AMOUNT = '54';

    /**
     * Código do país (BR = Brasil)
     */
    const ID_COUNTRY_CODE = '58';

    /**
     * Nome do titular da conta/recebedor
     */
    const ID_MERCHANT_NAME = '59';

    /**
     * Cidade do titular da conta/recebedor
     */
    const ID_MERCHANT_CITY = '60';

    /**
     * Campo de dados adicionais (inclui TXID)
     */
    const ID_ADDITIONAL_DATA_FIELD_TEMPLATE = '62';

    /**
     * TXID - Identificador único da transação
     */
    const ID_ADDITIONAL_DATA_FIELD_TEMPLATE_TXID = '05';

    /**
     * CRC16 - Checksum para validação do payload
     */
    const ID_CRC16 = '63';
}
