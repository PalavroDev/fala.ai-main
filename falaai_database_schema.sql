
CREATE DATABASE 'falaai';
-- =====================================================
-- TABELA: users (Usuários)
-- =====================================================
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL COMMENT 'Nome completo (8-60 chars, só letras)',
  `cpf` varchar(14) NOT NULL UNIQUE COMMENT 'CPF formatado 000.000.000-00',
  `email` varchar(255) NOT NULL UNIQUE COMMENT 'Email único',
  `phone` varchar(20) NOT NULL COMMENT 'Telefone (+55)XX-XXXXXXXX',
  `cep` varchar(9) NOT NULL COMMENT 'CEP 00000-000',
  `street` varchar(255) NOT NULL COMMENT 'Nome da rua',
  `number` varchar(10) NOT NULL COMMENT 'Número da residência',
  `complement` varchar(255) NULL COMMENT 'Complemento (apto, bloco)',
  `district` varchar(255) NOT NULL COMMENT 'Bairro',
  `city` varchar(255) NOT NULL COMMENT 'Cidade',
  `state` varchar(2) NOT NULL COMMENT 'Estado (UF)',
  `login` varchar(6) NOT NULL UNIQUE COMMENT 'Login exatamente 6 chars alfabéticos',
  `password` varchar(255) NOT NULL COMMENT 'Senha criptografada',
  `profile` enum('master','comum') NOT NULL DEFAULT 'comum' COMMENT 'Perfil: master ou comum',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Status ativo/inativo',
  `two_factor_secret` varchar(255) NULL COMMENT 'Chave secreta 2FA',
  `two_factor_confirmed_at` timestamp NULL COMMENT 'Confirmação 2FA',
  `email_verified_at` timestamp NULL COMMENT 'Verificação de email',
  `remember_token` varchar(100) NULL COMMENT 'Token remember me',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  KEY `idx_profile_active` (`profile`, `is_active`),
  KEY `idx_cpf` (`cpf`),
  KEY `idx_login` (`login`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELA: plans (Planos)
-- =====================================================
CREATE TABLE `plans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT 'Nome do plano (Basic, Smart, Black)',
  `description` text NOT NULL COMMENT 'Descrição do plano',
  `price` decimal(10,2) NOT NULL COMMENT 'Preço mensal',
  `features` json NOT NULL COMMENT 'Lista de funcionalidades em JSON',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Plano ativo/inativo',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  KEY `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELA: subscriptions (Assinaturas)
-- =====================================================
CREATE TABLE `subscriptions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL COMMENT 'ID do usuário',
  `plan_id` bigint unsigned NOT NULL COMMENT 'ID do plano',
  `status` enum('active','cancelled','expired') NOT NULL DEFAULT 'active' COMMENT 'Status da assinatura',
  `starts_at` timestamp NOT NULL COMMENT 'Data de início',
  `ends_at` timestamp NULL COMMENT 'Data de término',
  `cancelled_at` timestamp NULL COMMENT 'Data de cancelamento',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  CONSTRAINT `fk_subscriptions_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_subscriptions_plan` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELA: payments (Pagamentos)
-- =====================================================
CREATE TABLE `payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL COMMENT 'ID do usuário',
  `subscription_id` bigint unsigned NOT NULL COMMENT 'ID da assinatura',
  `amount` decimal(10,2) NOT NULL COMMENT 'Valor do pagamento',
  `status` enum('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending' COMMENT 'Status do pagamento',
  `payment_method` enum('credit_card','debit_card','pix','boleto') NOT NULL COMMENT 'Método de pagamento',
  `transaction_id` varchar(255) NULL UNIQUE COMMENT 'ID da transação',
  `payment_data` json NULL COMMENT 'Dados adicionais do pagamento',
  `paid_at` timestamp NULL COMMENT 'Data do pagamento',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  CONSTRAINT `fk_payments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_payments_subscription` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELA: checkouts (Checkouts)
-- =====================================================
CREATE TABLE `checkouts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `plan_id` bigint unsigned NOT NULL COMMENT 'ID do plano',
  `email` varchar(255) NOT NULL COMMENT 'Email do cliente',
  `password` varchar(255) NOT NULL COMMENT 'Hash da senha',
  `payment_method` varchar(255) NOT NULL COMMENT 'Método de pagamento',
  `card_name` varchar(255) NULL COMMENT 'Nome no cartão',
  `card_number` varchar(255) NULL COMMENT 'Número do cartão',
  `expiry_date` varchar(255) NULL COMMENT 'Data de expiração',
  `cvc` varchar(255) NULL COMMENT 'CVC do cartão',
  `zip_code` varchar(255) NOT NULL COMMENT 'CEP',
  `subtotal` decimal(10,2) NOT NULL COMMENT 'Subtotal',
  `taxes` decimal(10,2) NOT NULL COMMENT 'Impostos',
  `total` decimal(10,2) NOT NULL COMMENT 'Total',
  `status` varchar(255) NOT NULL DEFAULT 'pending' COMMENT 'Status do checkout',
  `transaction_id` varchar(255) NULL COMMENT 'ID da transação',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  KEY `idx_email_status` (`email`, `status`),
  KEY `idx_plan_status` (`plan_id`, `status`),
  KEY `idx_transaction` (`transaction_id`),
  CONSTRAINT `fk_checkouts_plan` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELA: access_logs (Logs de Acesso)
-- =====================================================
CREATE TABLE `access_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL COMMENT 'ID do usuário',
  `user_name` varchar(60) NOT NULL COMMENT 'Nome do usuário',
  `user_cpf` varchar(14) NOT NULL COMMENT 'CPF do usuário',
  `user_login` varchar(6) NOT NULL COMMENT 'Login do usuário',
  `ip_address` varchar(45) NOT NULL COMMENT 'Endereço IP',
  `user_agent` varchar(255) NULL COMMENT 'User Agent do navegador',
  `two_factor_used` tinyint(1) NOT NULL DEFAULT 0 COMMENT '2FA foi usado',
  `login_successful` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Login foi bem-sucedido',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  KEY `idx_user_created` (`user_id`, `created_at`),
  KEY `idx_cpf_created` (`user_cpf`, `created_at`),
  KEY `idx_created_success` (`created_at`, `login_successful`),
  CONSTRAINT `fk_access_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELA: student_workouts (Treinos do Estudante)
-- =====================================================
CREATE TABLE `student_workouts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL COMMENT 'ID do usuário',
  `workout_name` varchar(255) NOT NULL COMMENT 'Nome do treino (ex: Série A - Pernas)',
  `duration_minutes` int NOT NULL COMMENT 'Duração em minutos',
  `exercises` json NOT NULL COMMENT 'Array de exercícios com séries e repetições',
  `completed` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Treino foi concluído',
  `started_at` timestamp NULL COMMENT 'Quando começou',
  `completed_at` timestamp NULL COMMENT 'Quando terminou',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  KEY `idx_user_created` (`user_id`, `created_at`),
  KEY `idx_completed_created` (`completed`, `created_at`),
  CONSTRAINT `fk_student_workouts_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELA: student_goals (Metas do Estudante)
-- =====================================================
CREATE TABLE `student_goals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL COMMENT 'ID do usuário',
  `title` varchar(255) NOT NULL COMMENT 'Título da meta (ex: Perder 5kg)',
  `description` text NULL COMMENT 'Descrição da meta',
  `type` varchar(255) NOT NULL COMMENT 'Tipo da meta (peso, frequência, força)',
  `target_value` decimal(10,2) NOT NULL COMMENT 'Valor objetivo',
  `target_unit` varchar(10) NOT NULL COMMENT 'Unidade (kg, dias, reps)',
  `current_value` decimal(10,2) NOT NULL DEFAULT 0 COMMENT 'Valor atual',
  `target_date` date NOT NULL COMMENT 'Data objetivo',
  `is_achieved` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Meta foi alcançada',
  `achieved_at` timestamp NULL COMMENT 'Quando foi alcançada',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  KEY `idx_user_target_date` (`user_id`, `target_date`),
  KEY `idx_achieved_created` (`is_achieved`, `created_at`),
  CONSTRAINT `fk_student_goals_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELA: password_reset_tokens (Tokens de Reset)
-- =====================================================
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- RELACIONAMENTOS DO DIAGRAMA ER
-- =====================================================

/*
RELACIONAMENTOS PRINCIPAIS:

1. users (1) -----> (N) subscriptions
   - Um usuário pode ter múltiplas assinaturas
   - Chave estrangeira: subscriptions.user_id -> users.id

2. plans (1) -----> (N) subscriptions  
   - Um plano pode ter múltiplas assinaturas
   - Chave estrangeira: subscriptions.plan_id -> plans.id

3. users (1) -----> (N) payments
   - Um usuário pode ter múltiplos pagamentos
   - Chave estrangeira: payments.user_id -> users.id

4. subscriptions (1) -----> (N) payments
   - Uma assinatura pode ter múltiplos pagamentos
   - Chave estrangeira: payments.subscription_id -> subscriptions.id

5. plans (1) -----> (N) checkouts
   - Um plano pode ter múltiplos checkouts
   - Chave estrangeira: checkouts.plan_id -> plans.id

6. users (1) -----> (N) access_logs
   - Um usuário pode ter múltiplos logs de acesso
   - Chave estrangeira: access_logs.user_id -> users.id

7. users (1) -----> (N) student_workouts
   - Um usuário pode ter múltiplos treinos
   - Chave estrangeira: student_workouts.user_id -> users.id

8. users (1) -----> (N) student_goals
   - Um usuário pode ter múltiplas metas
   - Chave estrangeira: student_goals.user_id -> users.id

CARDINALIDADES:
- users: 1:N (um para muitos)
- plans: 1:N (um para muitos)  
- subscriptions: 1:N (um para muitos)
- payments: N:1 (muitos para um)
- checkouts: N:1 (muitos para um)
- access_logs: N:1 (muitos para um)
- student_workouts: N:1 (muitos para um)
- student_goals: N:1 (muitos para um)
*/

-- =====================================================
-- INSERÇÃO DE DADOS DEMO
-- =====================================================

-- Inserir planos demo
INSERT INTO `plans` (`name`, `description`, `price`, `features`, `is_active`) VALUES
('Basic', 'Ideal para iniciantes', 79.90, '["Acesso a equipamentos básicos", "Treino livre", "Vestiário com armários", "Horário: 6h às 22h", "Água e toalhas inclusas", "Suporte da equipe"]', 1),
('Smart', 'Mais popular entre nossos alunos', 129.90, '["Todos os benefícios do Basic", "Aulas coletivas incluídas", "Avaliação física trimestral", "App de treinos personalizado", "Horário: 5h às 23h", "Acesso a área de alongamento premium", "Suporte nutricional básico"]', 1),
('Black', 'Premium e completo', 199.90, '["Todos os benefícios do Smart", "Personal trainer 2x por mês", "Nutricionista incluso", "Acesso a todas as unidades", "Acesso 24 horas", "Sala VIP", "Convidados ilimitados", "Massagem terapêutica mensal", "Suplementação básica inclusa"]', 1);

-- Inserir usuário master demo
INSERT INTO `users` (`name`, `cpf`, `email`, `phone`, `cep`, `street`, `number`, `district`, `city`, `state`, `login`, `password`, `profile`, `is_active`) VALUES
('Administrador Master', '000.000.000-00', 'master@fitplan.com.br', '(+55)11-99999-9999', '00000-000', 'Rua Administrativa', '1', 'Centro', 'São Paulo', 'SP', 'MASTER', '$2y$10$92VIlKDHYy1T3pLvXpXUjOWu8jQaGHxMOP7OOGqj4yHvjqKdR2K0K', 'master', 1);

-- Inserir usuário comum demo
INSERT INTO `users` (`name`, `cpf`, `email`, `phone`, `cep`, `street`, `number`, `district`, `city`, `state`, `login`, `password`, `profile`, `is_active`) VALUES
('Sofia Maria Silva', '123.456.789-00', 'sofia@fitplan.com.br', '(+55)11-99999-8888', '01234-567', 'Rua das Flores', '123', 'Centro', 'São Paulo', 'SP', 'SOPHIA', '$2y$10$92VIlKDHYy1T3pLvXpXUjOWu8jQaGHxMOP7OOGqj4yHvjqKdR2K0K', 'comum', 1);

-- =====================================================
-- COMENTÁRIOS FINAIS
-- =====================================================

/*
ESTRUTURA DO BANCO DE DADOS FITPLAN ACADEMY:

📊 TABELAS PRINCIPAIS:
- users: Usuários do sistema (master/comum)
- plans: Planos de academia (Basic/Smart/Black)
- subscriptions: Assinaturas dos usuários
- payments: Pagamentos das assinaturas
- checkouts: Processo de checkout
- access_logs: Logs de acesso para auditoria
- student_workouts: Treinos dos alunos
- student_goals: Metas dos alunos
- password_reset_tokens: Tokens de recuperação

🔗 RELACIONAMENTOS:
- Sistema hierárquico com usuários como centro
- Relacionamentos 1:N entre entidades principais
- Foreign keys com CASCADE para integridade
- Índices otimizados para consultas frequentes

🎯 FUNCIONALIDADES SUPORTADAS:
- Sistema de usuários com perfis diferenciados
- Gestão de planos e assinaturas
- Processamento de pagamentos
- Auditoria de acessos
- Acompanhamento de treinos e metas
- Sistema de checkout completo

📈 PERFORMANCE:
- Índices estratégicos em campos de busca
- Chaves estrangeiras otimizadas
- Campos JSON para flexibilidade
- Timestamps automáticos
- Soft deletes onde necessário
*/
