<?php

// Configurações padrão do pacote Modularavel/Larapix
return [
    // Chave PIX do recebedor (CPF, CNPJ, e-mail, telefone ou chave aleatória)
    'chave_pix' => env('LARAPIX_CHAVE_PIX'),
    // Nome completo do titular da conta
    'nome_do_titular' => env('LARAPIX_NOME_DO_TITULAR'),
    // Cidade do titular (sem acentos ou caracteres especiais)
    'cidade_do_titular' => env('LARAPIX_CIDADE_DO_TITULAR'),
    // Valor padrão da transação em reais (BRL)
    'valor' => env('LARAPIX_VALOR', 0),
    // Descrição padrão do pagamento
    'descricao' => env('LARAPIX_DESCRICAO'),
    // Identificador único da transação (UUID)
    'id_transacao' => env('LARAPIX_ID_TRANSACAO'),
];
