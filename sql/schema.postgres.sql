CREATE TABLE IF NOT EXISTS users (
  id BIGSERIAL PRIMARY KEY,
  name VARCHAR(80) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS items (
  id BIGSERIAL PRIMARY KEY,
  user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  kind VARCHAR(10) NOT NULL CHECK (kind IN ('Lost', 'Found')),
  title VARCHAR(120) NOT NULL,
  category VARCHAR(24) NOT NULL CHECK (category IN ('Electronics', 'Books', 'Clothing', 'Keys & Cards', 'Bags', 'Documents', 'Other')),
  description TEXT NOT NULL,
  location VARCHAR(160) NOT NULL,
  event_date DATE NOT NULL,
  status VARCHAR(10) NOT NULL DEFAULT 'Open' CHECK (status IN ('Open', 'Claimed', 'Returned')),
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS claims (
  id BIGSERIAL PRIMARY KEY,
  item_id BIGINT NOT NULL REFERENCES items(id) ON DELETE CASCADE,
  claimant_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  message VARCHAR(700) NOT NULL,
  status VARCHAR(10) NOT NULL DEFAULT 'Pending' CHECK (status IN ('Pending', 'Accepted', 'Declined')),
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT uq_claim_item_user UNIQUE (item_id, claimant_id)
);

CREATE INDEX IF NOT EXISTS idx_items_browse ON items(status, kind, category, created_at);
CREATE INDEX IF NOT EXISTS idx_items_owner ON items(user_id, created_at);
CREATE INDEX IF NOT EXISTS idx_claims_item_status ON claims(item_id, status);
