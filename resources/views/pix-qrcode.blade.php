<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title ?? config('larapix.descricao') }}</title>

    <link rel="icon" type="image/png" href="data:image/png;base64, <?=base64_encode($image)?>">

    <link rel="preconnect"  href="https://cdn.jsdelivr.net">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <main class="d-flex align-items-center justify-content-center vh-100">
        <div class="card shadow bg-body-secondary" style="max-width: 95%; width: 990px;">
            <div class="row g-0">
                <div class="col-md-4">
                    <img src="data:image/png;base64, <?=base64_encode($image)?>" class="img-fluid rounded-start" width="400px" height="400px" alt="QRCODE de Pagamento">
                </div>
                <div class="col-md-8">
                    <div class="card-body">
                        <h3 class="card-title fw-bold mb-3">Código PIX gerado com sucesso!</h3>
                        <p class="mb-3">Copie a chave PIX abaixo e cole na opção <strong>CHAVE COPIA E COLA</strong> no seu internet banking ou pague scaneando o qrcode.</p>
                        <div class="alert alert-info mb-3" role="alert">
                            {{ $codigo }}
                        </div>
                        <div class="text-end">
                            <button onclick="copyToClipboard('{{ $codigo }}')" class="btn btn-outline-secondary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-copy" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M4 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM2 5a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1h1v1a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h1v1z"/>
                                </svg> Copiar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script defer>
        // Copy text to clipboard
        async function copyToClipboard(text) {
          await navigator.clipboard.writeText(text)
            .then(() => {
                alert('Chave PIX copiada com sucesso!')
                console.log("Text copied to clipboard:", text);
            })
            .catch(err => {
                alert('Erro! Tente novamente....');
                console.error("Failed to copy text:", err);
            });
        }
    </script>
</body>
</html>
