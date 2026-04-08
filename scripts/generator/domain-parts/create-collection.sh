#!/usr/bin/env bash

# Создаёт Collection для домена
# Использование:
#   create-collection.sh -n SomeObject -p /path/to/Domains

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
OUTPUT_FILE="${DOMAIN_DIR}/Collection/${ENTITY_NAME}Collection.php"

if [ -f "$OUTPUT_FILE" ]; then
    echo -e "${YELLOW}Skipped Collection (exists): ${OUTPUT_FILE}${NC}"
    exit 0
fi

cat > "$OUTPUT_FILE" <<EOF
<?php declare(strict_types=1);

namespace Core\Domains\\${ENTITY_NAME}\Collection;

use Core\Collections\CollectionInterface;
use Core\Collections\CollectionTrait;
use Core\Domains\\${ENTITY_NAME}\Models\\${ENTITY_NAME}DTO;
use Illuminate\Support\Collection;

/**
 * @method ${ENTITY_NAME}DTO|null first()
 * @method ${ENTITY_NAME}DTO[]    toArray()
 * @template-extends Collection<int, ${ENTITY_NAME}DTO>
 */
class ${ENTITY_NAME}Collection extends Collection implements CollectionInterface
{
    use CollectionTrait;

    public function checkItemInstance(mixed \$item): bool
    {
        return \$item instanceof ${ENTITY_NAME}DTO;
    }
}
EOF

echo -e "${GREEN}Created Collection: ${OUTPUT_FILE}${NC}"