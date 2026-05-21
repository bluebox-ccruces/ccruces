ALTER TABLE users
  ADD COLUMN email VARCHAR(190) NOT NULL DEFAULT '' AFTER username,
  ADD UNIQUE INDEX uq_users_email (email);
