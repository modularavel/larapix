<?php

// config for Modularavel/Larapix
return [
    'chave_pix' => env('LARAPIX_CHAVE_PIX', '05542201300'), // Pode ser chave CPF, CNPJ, Aleatória, Telefone, Email....
    'nome_do_titular' => env('LARAPIX_NOME_DO_TITULAR', 'Casimiro Carvalho Rocha'),
    'cidadeDoTitularDaConta_do_titular' => env('LARAPIX_CIDADE_DO_TITULAR', 'SAO LUIS'), // Sem caracteres especiais como acentos, traços etc....
    'valor' => 59, // Em Reais Brasileiro BRL - R$
    'descricao' => 'Premium Video from SPITFANS' // Descrição do pagamento
];
