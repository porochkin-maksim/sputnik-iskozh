#!/usr/bin/env bash

# Создаёт DTO для домена
# Использование:
#   create-dto.sh -n SomeObject -p /path/to/Domains

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
OUTPUT_FILE="${DOMAIN_DIR}/Models/${ENTITY_NAME}DTO.php"

if [ -f "$OUTPUT_FILE" ]; then
    echo -e "${YELLOW}Skipped DTO (exists): ${OUTPUT_FILE}${NC}"
    exit 0
fi

cat > "$OUTPUT_FILE" <<EOF
<?php declare(strict_types=1);

namespace Core\Domains\\${ENTITY_NAME}\Models;

use Core\Domains\Common\Traits\TimestampsTrait;
use Carbon\Carbon;

class ${ENTITY_NAME}DTO
{
    use TimestampsTrait;

    private ?int    \$id   = null;
    private ?string \$name = null;

    public function getId(): ?int
    {
        return \$this->id;
    }

    public function setId(?int \$id): static
    {
        \$this->id = \$id;
        return \$this;
    }

    public function getName(): ?string
    {
        return \$this->name;
    }

    public function setName(?string \$name): static
    {
        \$this->name = \$name;
        return \$this;
    }
}
EOF

echo -e "${GREEN}Created DTO: ${OUTPUT_FILE}${NC}"