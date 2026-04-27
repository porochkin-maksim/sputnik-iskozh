#!/usr/bin/env bash

# Создаёт SearchResponse для домена
# Использование:
#   create-search-response.sh -n SomeObject -p /path/to/Domains

set -e

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

usage() {
    echo "Usage: $0 -n NAME -p DOMAINS_BASE"
    exit 1
}

while getopts "n:p:" opt; do
    case $opt in
        n) ENTITY_NAME="$OPTARG" ;;
        p) DOMAINS_BASE="$OPTARG" ;;
        *) usage ;;
    esac
done

if [ -z "$ENTITY_NAME" ] || [ -z "$DOMAINS_BASE" ]; then
    echo -e "${YELLOW}Error: both -n and -p are required${NC}"
    usage
fi

DOMAIN_DIR="${DOMAINS_BASE}/${ENTITY_NAME}"
OUTPUT_FILE="${DOMAIN_DIR}/Responses/${ENTITY_NAME}SearchResponse.php"

if [ -f "$OUTPUT_FILE" ]; then
    echo -e "${YELLOW}Skipped SearchResponse (exists): ${OUTPUT_FILE}${NC}"
    exit 0
fi

cat > "$OUTPUT_FILE" <<EOF
<?php declare(strict_types=1);

namespace Core\Domains\\${ENTITY_NAME}\Responses;

use Core\Db\Searcher\Models\BaseSearchResponse;
use Core\Domains\\${ENTITY_NAME}\Collection\\${ENTITY_NAME}Collection;

/**
 * @method ${ENTITY_NAME}Collection getItems()
 */
class ${ENTITY_NAME}SearchResponse extends BaseSearchResponse
{
}
EOF

echo -e "${GREEN}Created SearchResponse: ${OUTPUT_FILE}${NC}"