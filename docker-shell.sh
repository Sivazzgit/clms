#!/bin/bash

# ContractLabour Application - Docker Shell Access Script
# Open a bash shell in the web container

set -e

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd "$SCRIPT_DIR"

echo "🐚 Opening bash shell in web container..."
echo ""
echo "Type 'exit' to return to the host shell"
echo ""

docker-compose exec web bash
