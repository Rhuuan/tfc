<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conta Excluída - {{ config('app.name') }}</title>
    
    @livewireStyles
    @filamentStyles
    
    <style>
        body { 
            font-family: 'Inter', system-ui, sans-serif; 
            text-align: center; 
            padding: 50px; 
            background: rgb(249, 250, 251);
            margin: 0;
            color: rgb(17, 24, 39);
        }
        .container {
            max-width: 500px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
            border: 1px solid rgb(229, 231, 235);
        }
        .success { 
            color: rgb(34, 197, 94); 
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .subtitle {
            color: rgb(107, 114, 128);
            font-size: 14px;
            margin-bottom: 24px;
        }
        .message {
            color: rgb(75, 85, 99);
            line-height: 1.6;
            margin-bottom: 32px;
            font-size: 14px;
        }
        .message p {
            margin: 12px 0;
            display: flex;
            align-items: center;
            gap: 8px;
            text-align: left;
        }
        .message svg {
            flex-shrink: 0;
            width: 16px;
            height: 16px;
        }
        .btn { 
            display: inline-block; 
            padding: 10px 24px; 
            color: white; 
            text-decoration: none; 
            border-radius: 8px; 
            margin: 6px;
            transition: all 0.2s;
            font-size: 14px;
            font-weight: 500;
        }
        .btn-primary {
            background: rgb(251, 191, 36);
            border: 1px solid rgb(245, 158, 11);
        }
        .btn-primary:hover {
            background: rgb(245, 158, 11);
        }
        .btn-secondary {
            background: rgb(107, 114, 128);
            border: 1px solid rgb(75, 85, 99);
        }
        .btn-secondary:hover {
            background: rgb(75, 85, 99);
        }
        .footer {
            margin-top: 24px;
            color: rgb(156, 163, 175);
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="success">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            Conta Excluída
        </h1>
        
        <p class="subtitle">Sua conta foi removida com sucesso</p>
        
        <div class="message">
            <p>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: rgb(59, 130, 246);">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Sua conta foi completamente removida do sistema
            </p>
            <p>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: rgb(239, 68, 68);">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Todos os seus dados foram excluídos permanentemente
            </p>
            <p>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: rgb(245, 158, 11);">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                Para usar nossos serviços novamente, será necessário criar uma nova conta
            </p>
        </div>
        
        <div>
            <a href="/admin/register" class="btn btn-primary">Criar Nova Conta</a>
            <a href="/" class="btn btn-secondary">Página Inicial</a>
        </div>
        
        <div class="footer">
            Obrigado por ter usado nossos serviços.
        </div>
    </div>

    @livewireScripts
    @filamentScripts
</body>
</html>