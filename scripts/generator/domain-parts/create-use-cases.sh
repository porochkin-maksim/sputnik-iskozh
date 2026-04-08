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
OUTPUT_FILE="${DOMAIN_DIR}/UseCases/Create${ENTITY_NAME}UseCase.php"

if [ -f "$OUTPUT_FILE" ]; then
    echo -e "${YELLOW}Skipped UseCase (exists): ${OUTPUT_FILE}${NC}"
    exit 0
fi

cat > "$OUTPUT_FILE" <<EOF
<?php declare(strict_types=1);

namespace Core\Domains\\${ENTITY_NAME}\UseCases;

use Core\Domains\\${ENTITY_NAME}\Models\\${ENTITY_NAME}DTO;
use JsonException;

class Create${ENTITY_NAME}UseCase
{
    /**
     * @throws JsonException
     */
    public function validate(${ENTITY_NAME}DTO $dto): void
    {
        $errors = [];

        if ( ! empty($errors)) {
            throw new \InvalidArgumentException(json_encode($errors, JSON_THROW_ON_ERROR));
        }
    }
}
EOF

echo -e "${GREEN}Created Service: ${OUTPUT_FILE}${NC}"