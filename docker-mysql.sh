#!/bin/bash

# ContractLabour Application - MySQL CLI Access Script
# Open MySQL command-line client

set -e

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd "$SCRIPT_DIR"

echo "🗄️  Opening MySQL client..."
echo ""
echo "Database: anahaw"
echo "User:     anahaw"
echo ""
echo "Useful commands:"
echo "  SHOW DATABASES;     - List all databases"
echo "  USE anahaw;         - Switch to anahaw database"
echo "  SHOW TABLES;        - List tables"
echo "  DESC table_name;    - Show table structure"
echo ""
echo "Type 'exit' or 'quit' to return"
echo ""

docker-compose exec mysql mysql -u anahaw -panahaw
