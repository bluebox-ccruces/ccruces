CREATE TABLE IF NOT EXISTS companies (
  id VARCHAR(64) PRIMARY KEY,
  name VARCHAR(160) NOT NULL,
  legal_name VARCHAR(190) NULL,
  tax_id VARCHAR(64) NULL,
  primary_domain VARCHAR(190) NOT NULL,
  status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_companies_primary_domain (primary_domain),
  INDEX idx_companies_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS sites (
  id VARCHAR(64) PRIMARY KEY,
  company_id VARCHAR(64) NOT NULL,
  service_id VARCHAR(64) NULL,
  name VARCHAR(160) NOT NULL,
  slug VARCHAR(120) NOT NULL,
  domain VARCHAR(190) NOT NULL,
  site_type ENUM('holding', 'service', 'admin', 'api') NOT NULL DEFAULT 'service',
  status ENUM('active', 'inactive', 'draft') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_sites_domain (domain),
  UNIQUE KEY uq_sites_company_slug (company_id, slug),
  INDEX idx_sites_company_type (company_id, site_type),
  INDEX idx_sites_service (service_id),
  CONSTRAINT fk_sites_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  company_id VARCHAR(64) NOT NULL DEFAULT 'ccruces',
  username VARCHAR(64) NOT NULL UNIQUE,
  email VARCHAR(190) NOT NULL UNIQUE,
  name VARCHAR(120) NOT NULL,
  role ENUM('admin', 'client') NOT NULL DEFAULT 'client',
  password_hash VARCHAR(255) NOT NULL,
  status TINYINT(1) NOT NULL DEFAULT 1,
  last_login_at DATETIME NULL,
  failed_login_attempts SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  locked_until DATETIME NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_users_company (company_id),
  INDEX idx_users_status_role (status, role),
  INDEX idx_users_locked_until (locked_until),
  CONSTRAINT fk_users_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS services (
  id VARCHAR(64) PRIMARY KEY,
  company_id VARCHAR(64) NOT NULL DEFAULT 'ccruces',
  site_id VARCHAR(64) NULL,
  slug VARCHAR(120) NULL,
  category VARCHAR(120) NULL,
  name VARCHAR(120) NOT NULL,
  tagline VARCHAR(180) NOT NULL DEFAULT '',
  description TEXT NOT NULL,
  summary TEXT NULL,
  content TEXT NULL,
  benefits TEXT NULL,
  financial_benefits TEXT NULL,
  roi_note TEXT NULL,
  video_url VARCHAR(255) NULL,
  logo VARCHAR(255) NOT NULL,
  demo_url VARCHAR(255) NOT NULL,
  private_url VARCHAR(255) NOT NULL,
  status VARCHAR(120) NOT NULL DEFAULT 'Disponible',
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  sort_order INT UNSIGNED NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_services_company_slug (company_id, slug),
  INDEX idx_services_company (company_id),
  INDEX idx_services_site (site_id),
  INDEX idx_services_sort_order_name (sort_order, name),
  INDEX idx_services_is_active (is_active),
  INDEX idx_services_status (status),
  CONSTRAINT fk_services_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE RESTRICT,
  CONSTRAINT fk_services_site FOREIGN KEY (site_id) REFERENCES sites(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS service_modules (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  company_id VARCHAR(64) NOT NULL DEFAULT 'ccruces',
  service_id VARCHAR(64) NOT NULL,
  code VARCHAR(80) NOT NULL,
  name VARCHAR(160) NOT NULL,
  base_url VARCHAR(255) NULL,
  api_scope VARCHAR(120) NOT NULL,
  status ENUM('active', 'inactive', 'development') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_service_modules_code (code),
  UNIQUE KEY uq_service_modules_service_scope (service_id, api_scope),
  INDEX idx_service_modules_company (company_id),
  INDEX idx_service_modules_status (status),
  CONSTRAINT fk_service_modules_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE RESTRICT,
  CONSTRAINT fk_service_modules_service FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS permissions (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  service_id VARCHAR(64) NULL,
  code VARCHAR(120) NOT NULL,
  name VARCHAR(160) NOT NULL,
  description TEXT NULL,
  permission_scope ENUM('global', 'service') NOT NULL DEFAULT 'service',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_permissions_code (code),
  INDEX idx_permissions_service (service_id),
  INDEX idx_permissions_scope (permission_scope),
  CONSTRAINT fk_permissions_service FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS roles (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  company_id VARCHAR(64) NOT NULL DEFAULT 'ccruces',
  service_id VARCHAR(64) NULL,
  code VARCHAR(120) NOT NULL,
  name VARCHAR(160) NOT NULL,
  description TEXT NULL,
  role_scope ENUM('global', 'service') NOT NULL DEFAULT 'service',
  is_system TINYINT(1) NOT NULL DEFAULT 0,
  status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_roles_code (code),
  INDEX idx_roles_company_scope (company_id, role_scope),
  INDEX idx_roles_service_status (service_id, status),
  CONSTRAINT fk_roles_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE RESTRICT,
  CONSTRAINT fk_roles_service FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS role_permissions (
  role_id BIGINT UNSIGNED NOT NULL,
  permission_id BIGINT UNSIGNED NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (role_id, permission_id),
  INDEX idx_role_permissions_permission (permission_id),
  CONSTRAINT fk_role_permissions_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
  CONSTRAINT fk_role_permissions_permission FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS user_service_access (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  company_id VARCHAR(64) NOT NULL DEFAULT 'ccruces',
  service_id VARCHAR(64) NOT NULL,
  access_status ENUM('active', 'suspended', 'revoked') NOT NULL DEFAULT 'active',
  granted_by INT UNSIGNED NULL,
  granted_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  expires_at DATETIME NULL,
  UNIQUE KEY uq_user_service_access (user_id, service_id),
  INDEX idx_user_service_access_company (company_id, access_status),
  INDEX idx_user_service_access_service (service_id, access_status),
  INDEX idx_user_service_access_granted_by (granted_by),
  CONSTRAINT fk_user_service_access_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_user_service_access_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE RESTRICT,
  CONSTRAINT fk_user_service_access_service FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE,
  CONSTRAINT fk_user_service_access_granted_by FOREIGN KEY (granted_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS user_role_assignments (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  role_id BIGINT UNSIGNED NOT NULL,
  assignment_status ENUM('active', 'suspended', 'revoked') NOT NULL DEFAULT 'active',
  assigned_by INT UNSIGNED NULL,
  assigned_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  expires_at DATETIME NULL,
  UNIQUE KEY uq_user_role_assignment (user_id, role_id),
  INDEX idx_user_role_assignments_role (role_id, assignment_status),
  INDEX idx_user_role_assignments_assigned_by (assigned_by),
  CONSTRAINT fk_user_role_assignments_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_user_role_assignments_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
  CONSTRAINT fk_user_role_assignments_assigned_by FOREIGN KEY (assigned_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS api_clients (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  company_id VARCHAR(64) NOT NULL DEFAULT 'ccruces',
  service_id VARCHAR(64) NULL,
  name VARCHAR(160) NOT NULL,
  client_key VARCHAR(120) NOT NULL,
  client_secret_hash VARCHAR(255) NULL,
  allowed_origins TEXT NULL,
  scopes TEXT NULL,
  status ENUM('active', 'inactive', 'revoked') NOT NULL DEFAULT 'active',
  last_used_at DATETIME NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_api_clients_key (client_key),
  INDEX idx_api_clients_company_status (company_id, status),
  INDEX idx_api_clients_service (service_id),
  CONSTRAINT fk_api_clients_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE RESTRICT,
  CONSTRAINT fk_api_clients_service FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS customers (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  company_id VARCHAR(64) NOT NULL DEFAULT 'ccruces',
  external_code VARCHAR(80) NULL,
  name VARCHAR(190) NOT NULL,
  tax_id VARCHAR(64) NULL,
  email VARCHAR(190) NULL,
  phone VARCHAR(80) NULL,
  status ENUM('active', 'inactive', 'prospect') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_customers_company_external (company_id, external_code),
  INDEX idx_customers_company_name (company_id, name),
  INDEX idx_customers_tax_id (tax_id),
  CONSTRAINT fk_customers_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS leads (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  company_id VARCHAR(64) NOT NULL DEFAULT 'ccruces',
  site_id VARCHAR(64) NULL,
  service_id VARCHAR(64) NULL,
  source VARCHAR(120) NOT NULL DEFAULT 'web',
  name VARCHAR(160) NOT NULL,
  email VARCHAR(190) NULL,
  phone VARCHAR(80) NULL,
  message TEXT NULL,
  status ENUM('new', 'contacted', 'qualified', 'won', 'lost') NOT NULL DEFAULT 'new',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_leads_company_status (company_id, status),
  INDEX idx_leads_service_status (service_id, status),
  INDEX idx_leads_created_at (created_at),
  CONSTRAINT fk_leads_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE RESTRICT,
  CONSTRAINT fk_leads_site FOREIGN KEY (site_id) REFERENCES sites(id) ON DELETE SET NULL,
  CONSTRAINT fk_leads_service FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS posts (
  id VARCHAR(64) PRIMARY KEY,
  company_id VARCHAR(64) NOT NULL DEFAULT 'ccruces',
  site_id VARCHAR(64) NULL,
  service_id VARCHAR(64) NULL,
  title VARCHAR(180) NOT NULL,
  slug VARCHAR(191) NULL,
  excerpt TEXT NULL,
  content MEDIUMTEXT NOT NULL,
  author VARCHAR(120) NOT NULL,
  is_published TINYINT(1) NOT NULL DEFAULT 1,
  published_at DATE NOT NULL,
  deleted_at DATETIME NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_posts_slug (slug),
  INDEX idx_posts_company (company_id),
  INDEX idx_posts_site (site_id),
  INDEX idx_posts_service (service_id),
  INDEX idx_posts_published_at (published_at),
  INDEX idx_posts_visibility_date (is_published, published_at),
  INDEX idx_posts_author_date (author, published_at),
  INDEX idx_posts_deleted_at (deleted_at),
  FULLTEXT KEY idx_posts_fulltext (title, excerpt, content),
  CONSTRAINT fk_posts_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE RESTRICT,
  CONSTRAINT fk_posts_site FOREIGN KEY (site_id) REFERENCES sites(id) ON DELETE SET NULL,
  CONSTRAINT fk_posts_service FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS post_likes (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  post_id VARCHAR(64) NOT NULL,
  username VARCHAR(64) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_post_likes_post_user (post_id, username),
  INDEX idx_post_likes_post_id (post_id),
  INDEX idx_post_likes_username (username),
  CONSTRAINT fk_post_likes_post FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
  CONSTRAINT fk_post_likes_user FOREIGN KEY (username) REFERENCES users(username) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS post_comments (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  post_id VARCHAR(64) NOT NULL,
  username VARCHAR(64) NOT NULL,
  content TEXT NOT NULL,
  status ENUM('visible', 'hidden') NOT NULL DEFAULT 'visible',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_post_comments_post_created (post_id, created_at),
  INDEX idx_post_comments_user (username),
  INDEX idx_post_comments_status (status),
  CONSTRAINT fk_post_comments_post FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
  CONSTRAINT fk_post_comments_user FOREIGN KEY (username) REFERENCES users(username) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS post_images (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  post_id VARCHAR(64) NOT NULL,
  image_path VARCHAR(255) NOT NULL,
  alt_text VARCHAR(255) NOT NULL DEFAULT '',
  is_primary TINYINT(1) NOT NULL DEFAULT 0,
  sort_order INT UNSIGNED NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_post_images_post_sort (post_id, is_primary, sort_order),
  INDEX idx_post_images_path (image_path),
  CONSTRAINT fk_post_images_post FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS audit_logs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  company_id VARCHAR(64) NOT NULL DEFAULT 'ccruces',
  service_id VARCHAR(64) NULL,
  actor_username VARCHAR(64) NULL,
  action VARCHAR(120) NOT NULL,
  entity_type VARCHAR(120) NOT NULL,
  entity_id VARCHAR(120) NULL,
  metadata JSON NULL,
  ip_address VARCHAR(64) NULL,
  user_agent VARCHAR(255) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_audit_company_created (company_id, created_at),
  INDEX idx_audit_service_created (service_id, created_at),
  INDEX idx_audit_actor_created (actor_username, created_at),
  CONSTRAINT fk_audit_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE RESTRICT,
  CONSTRAINT fk_audit_service FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL,
  CONSTRAINT fk_audit_actor FOREIGN KEY (actor_username) REFERENCES users(username) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS bocado_locations (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  company_id VARCHAR(64) NOT NULL DEFAULT 'ccruces',
  code VARCHAR(80) NOT NULL,
  name VARCHAR(160) NOT NULL,
  address VARCHAR(255) NULL,
  status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_bocado_locations_code (company_id, code),
  INDEX idx_bocado_locations_company_status (company_id, status),
  CONSTRAINT fk_bocado_locations_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS bocado_cost_centers (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  company_id VARCHAR(64) NOT NULL DEFAULT 'ccruces',
  code VARCHAR(80) NOT NULL,
  name VARCHAR(160) NOT NULL,
  status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_bocado_cost_centers_code (company_id, code),
  INDEX idx_bocado_cost_centers_status (company_id, status),
  CONSTRAINT fk_bocado_cost_centers_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS bocado_diners (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  company_id VARCHAR(64) NOT NULL DEFAULT 'ccruces',
  cost_center_id BIGINT UNSIGNED NULL,
  employee_code VARCHAR(80) NOT NULL,
  document_number VARCHAR(80) NULL,
  full_name VARCHAR(190) NOT NULL,
  diner_type ENUM('employee', 'contractor', 'visitor') NOT NULL DEFAULT 'employee',
  status ENUM('active', 'inactive', 'blocked') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_bocado_diners_employee (company_id, employee_code),
  INDEX idx_bocado_diners_document (document_number),
  INDEX idx_bocado_diners_status (company_id, status),
  CONSTRAINT fk_bocado_diners_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE RESTRICT,
  CONSTRAINT fk_bocado_diners_cost_center FOREIGN KEY (cost_center_id) REFERENCES bocado_cost_centers(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS bocado_meal_types (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  company_id VARCHAR(64) NOT NULL DEFAULT 'ccruces',
  code VARCHAR(80) NOT NULL,
  name VARCHAR(120) NOT NULL,
  unit_price DECIMAL(12,2) NOT NULL DEFAULT 0,
  status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_bocado_meal_types_code (company_id, code),
  INDEX idx_bocado_meal_types_status (company_id, status),
  CONSTRAINT fk_bocado_meal_types_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS bocado_meal_events (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  company_id VARCHAR(64) NOT NULL DEFAULT 'ccruces',
  location_id BIGINT UNSIGNED NULL,
  diner_id BIGINT UNSIGNED NOT NULL,
  meal_type_id BIGINT UNSIGNED NOT NULL,
  served_at DATETIME NOT NULL,
  quantity DECIMAL(10,2) NOT NULL DEFAULT 1,
  unit_price DECIMAL(12,2) NOT NULL DEFAULT 0,
  subsidy_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
  total_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
  validation_method ENUM('manual', 'qr', 'card', 'biometric', 'api') NOT NULL DEFAULT 'manual',
  status ENUM('valid', 'void', 'pending') NOT NULL DEFAULT 'valid',
  created_by VARCHAR(64) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_bocado_events_company_date (company_id, served_at),
  INDEX idx_bocado_events_diner_date (diner_id, served_at),
  INDEX idx_bocado_events_location_date (location_id, served_at),
  CONSTRAINT fk_bocado_events_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE RESTRICT,
  CONSTRAINT fk_bocado_events_location FOREIGN KEY (location_id) REFERENCES bocado_locations(id) ON DELETE SET NULL,
  CONSTRAINT fk_bocado_events_diner FOREIGN KEY (diner_id) REFERENCES bocado_diners(id) ON DELETE RESTRICT,
  CONSTRAINT fk_bocado_events_meal_type FOREIGN KEY (meal_type_id) REFERENCES bocado_meal_types(id) ON DELETE RESTRICT,
  CONSTRAINT fk_bocado_events_created_by FOREIGN KEY (created_by) REFERENCES users(username) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS bluesales_accounts (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  company_id VARCHAR(64) NOT NULL DEFAULT 'ccruces',
  customer_id BIGINT UNSIGNED NULL,
  account_code VARCHAR(80) NULL,
  name VARCHAR(190) NOT NULL,
  tax_id VARCHAR(64) NULL,
  country VARCHAR(80) NULL,
  segment VARCHAR(120) NULL,
  status ENUM('active', 'inactive', 'prospect') NOT NULL DEFAULT 'prospect',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_bluesales_accounts_code (company_id, account_code),
  INDEX idx_bluesales_accounts_company_name (company_id, name),
  INDEX idx_bluesales_accounts_customer (customer_id),
  CONSTRAINT fk_bluesales_accounts_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE RESTRICT,
  CONSTRAINT fk_bluesales_accounts_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS bluesales_contacts (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  account_id BIGINT UNSIGNED NOT NULL,
  full_name VARCHAR(190) NOT NULL,
  email VARCHAR(190) NULL,
  phone VARCHAR(80) NULL,
  position_title VARCHAR(120) NULL,
  is_primary TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_bluesales_contacts_account (account_id, is_primary),
  INDEX idx_bluesales_contacts_email (email),
  CONSTRAINT fk_bluesales_contacts_account FOREIGN KEY (account_id) REFERENCES bluesales_accounts(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS bluesales_products (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  company_id VARCHAR(64) NOT NULL DEFAULT 'ccruces',
  sku VARCHAR(80) NOT NULL,
  name VARCHAR(160) NOT NULL,
  category VARCHAR(120) NULL,
  unit VARCHAR(40) NOT NULL DEFAULT 'kg',
  status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_bluesales_products_sku (company_id, sku),
  INDEX idx_bluesales_products_status (company_id, status),
  CONSTRAINT fk_bluesales_products_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS bluesales_opportunities (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  company_id VARCHAR(64) NOT NULL DEFAULT 'ccruces',
  account_id BIGINT UNSIGNED NOT NULL,
  owner_username VARCHAR(64) NULL,
  title VARCHAR(190) NOT NULL,
  stage ENUM('lead', 'qualified', 'proposal', 'negotiation', 'won', 'lost') NOT NULL DEFAULT 'lead',
  expected_close_date DATE NULL,
  currency CHAR(3) NOT NULL DEFAULT 'USD',
  estimated_amount DECIMAL(14,2) NOT NULL DEFAULT 0,
  probability_percent TINYINT UNSIGNED NOT NULL DEFAULT 0,
  notes TEXT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_bluesales_opp_company_stage (company_id, stage),
  INDEX idx_bluesales_opp_account (account_id),
  INDEX idx_bluesales_opp_owner (owner_username),
  CONSTRAINT fk_bluesales_opp_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE RESTRICT,
  CONSTRAINT fk_bluesales_opp_account FOREIGN KEY (account_id) REFERENCES bluesales_accounts(id) ON DELETE RESTRICT,
  CONSTRAINT fk_bluesales_opp_owner FOREIGN KEY (owner_username) REFERENCES users(username) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS bluesales_opportunity_items (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  opportunity_id BIGINT UNSIGNED NOT NULL,
  product_id BIGINT UNSIGNED NOT NULL,
  quantity DECIMAL(14,2) NOT NULL DEFAULT 0,
  unit_price DECIMAL(14,2) NOT NULL DEFAULT 0,
  total_amount DECIMAL(14,2) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_bluesales_opp_items_opp (opportunity_id),
  INDEX idx_bluesales_opp_items_product (product_id),
  CONSTRAINT fk_bluesales_opp_items_opp FOREIGN KEY (opportunity_id) REFERENCES bluesales_opportunities(id) ON DELETE CASCADE,
  CONSTRAINT fk_bluesales_opp_items_product FOREIGN KEY (product_id) REFERENCES bluesales_products(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS bluesales_orders (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  company_id VARCHAR(64) NOT NULL DEFAULT 'ccruces',
  opportunity_id BIGINT UNSIGNED NULL,
  account_id BIGINT UNSIGNED NOT NULL,
  order_number VARCHAR(80) NOT NULL,
  order_date DATE NOT NULL,
  currency CHAR(3) NOT NULL DEFAULT 'USD',
  total_amount DECIMAL(14,2) NOT NULL DEFAULT 0,
  status ENUM('draft', 'confirmed', 'shipped', 'invoiced', 'paid', 'cancelled') NOT NULL DEFAULT 'draft',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_bluesales_orders_number (company_id, order_number),
  INDEX idx_bluesales_orders_account_date (account_id, order_date),
  INDEX idx_bluesales_orders_status (company_id, status),
  CONSTRAINT fk_bluesales_orders_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE RESTRICT,
  CONSTRAINT fk_bluesales_orders_opportunity FOREIGN KEY (opportunity_id) REFERENCES bluesales_opportunities(id) ON DELETE SET NULL,
  CONSTRAINT fk_bluesales_orders_account FOREIGN KEY (account_id) REFERENCES bluesales_accounts(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS bluesales_order_items (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id BIGINT UNSIGNED NOT NULL,
  product_id BIGINT UNSIGNED NOT NULL,
  quantity DECIMAL(14,2) NOT NULL DEFAULT 0,
  unit_price DECIMAL(14,2) NOT NULL DEFAULT 0,
  total_amount DECIMAL(14,2) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_bluesales_order_items_order (order_id),
  INDEX idx_bluesales_order_items_product (product_id),
  CONSTRAINT fk_bluesales_order_items_order FOREIGN KEY (order_id) REFERENCES bluesales_orders(id) ON DELETE CASCADE,
  CONSTRAINT fk_bluesales_order_items_product FOREIGN KEY (product_id) REFERENCES bluesales_products(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
