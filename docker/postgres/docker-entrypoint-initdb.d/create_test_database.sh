#!/bin/bash
set -e

# Create test database dynamically based on environment variables
psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "$POSTGRES_DB" <<-EOSQL
    CREATE DATABASE ${POSTGRES_DB}_test WITH OWNER $POSTGRES_USER;
EOSQL

echo "Created test database: ${POSTGRES_DB}_test with owner: $POSTGRES_USER" 