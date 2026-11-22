# METRIVA — instalação

Requisitos: PHP 8.1+ (extensões pdo_mysql, fileinfo, mbstring), MySQL 5.7+/MariaDB 10.4+, Apache com `.htaccess` (ou regras equivalentes no Nginx).

1. Crie o banco (utf8mb4) e importe `sql/schema.sql`.
2. Copie `config.sample.php` para `config.local.php` e preencha os dados do banco. Use `chmod 640`.
3. Dê permissão de escrita à pasta `uploads/`.
4. Crie cada sócio (a senha é pedida no terminal, nunca fica em arquivo):
   `php tools/create_admin.php "Nome" email@dominio.com`
5. Acesse `/admin/login.php`. Em **Configurações** informe WhatsApp, e-mail, texto "Sobre" e percentuais de lucro (devem somar 100).
6. Em **Serviços**, cadastre os serviços de cada categoria. O site público não tem nenhum serviço até você cadastrar.
7. Em produção, use HTTPS (o cookie de sessão fica `Secure` automaticamente).

## Pagamentos
Pagamentos são registrados manualmente em Admin → Pedidos. `webhook.php` é um **esboço**: valida assinatura HMAC com `WEBHOOK_SECRET` (variável de ambiente ou `config.local.php`) e registra o pagamento. Para Pix/cartão reais é preciso: conta em um provedor, criação da cobrança via API dele, e adaptar o formato/assinatura do webhook ao provedor.

## Não incluído nesta versão
Conta/login de cliente (o cliente acompanha por código + e-mail), carrinho com vários serviços (1 serviço por pedido), formulário de suporte, portfólio de sites, depoimentos e FAQ editáveis pelo painel, perfis de permissão entre sócios (ambos são administradores; ações importantes ficam em `audit_log`).
