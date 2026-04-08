#!/usr/bin/env bash

# Создаёт Service для домена
# Использование:
#   create-service.sh -n SomeObject -p /path/to/Domains

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
OUTPUT_FILE="${DOMAIN_DIR}/Services/${ENTITY_NAME}Service.php"

if [ -f "$OUTPUT_FILE" ]; then
    echo -e "${YELLOW}Skipped Service (exists): ${OUTPUT_FILE}${NC}"
    exit 0
fi

cat > "$OUTPUT_FILE" <<EOF
<?php declare(strict_types=1);

namespace Core\Domains\\${ENTITY_NAME}\Services;

use Core\Domains\\${ENTITY_NAME}\Models\\${ENTITY_NAME}DTO;
use Core\Domains\\${ENTITY_NAME}\Repositories\\${ENTITY_NAME}Repository;
use Core\Domains\\${ENTITY_NAME}\Responses\\${ENTITY_NAME}SearchResponse;
use Core\Domains\\${ENTITY_NAME}\Searchers\\${ENTITY_NAME}Searcher;

readonly class ${ENTITY_NAME}Service
{
    public function __construct(
        private ${ENTITY_NAME}Repository \$${ENTITY_NAME,,}Repository,
    ) {}

    public function search(${ENTITY_NAME}Searcher \$searcher): ${ENTITY_NAME}SearchResponse
    {
        return \$this->${ENTITY_NAME,,}Repository->search(\$searcher);
    }

    public function getById(?int \$id): ?${ENTITY_NAME}DTO
    {
        return \$this->${ENTITY_NAME,,}Repository->getById(\$id);
    }

    public function save(${ENTITY_NAME}DTO \$dto): ${ENTITY_NAME}DTO
    {
        return \$this->${ENTITY_NAME,,}Repository->save(\$dto);
    }

    public function deleteById(?int \$id): bool
    {
        return \$this->${ENTITY_NAME,,}Repository->deleteById(\$id);
    }
}
EOF

echo -e "${GREEN}Created Service: ${OUTPUT_FILE}${NC}"