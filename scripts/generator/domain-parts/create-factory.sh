#!/usr/bin/env bash

# Создаёт Factory для домена
# Использование:
#   create-factory.sh -n SomeObject -p /path/to/Domains

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
OUTPUT_FILE="${DOMAIN_DIR}/Factories/${ENTITY_NAME}Factory.php"

if [ -f "$OUTPUT_FILE" ]; then
    echo -e "${YELLOW}Skipped Factory (exists): ${OUTPUT_FILE}${NC}"
    exit 0
fi

cat > "$OUTPUT_FILE" <<EOF
<?php declare(strict_types=1);

namespace Core\Domains\\${ENTITY_NAME}\Factories;

use Core\Domains\\${ENTITY_NAME}\Models\\${ENTITY_NAME};
use Core\Domains\\${ENTITY_NAME}\Models\\${ENTITY_NAME}DTO;
use Illuminate\Database\Eloquent\Collection;

class ${ENTITY_NAME}Factory
{
    public function makeDefault(): ${ENTITY_NAME}DTO
    {
        return (new ${ENTITY_NAME}DTO());
    }

    public function makeDtoFromObject(${ENTITY_NAME} \$model): ${ENTITY_NAME}DTO
    {
        return (new ${ENTITY_NAME}DTO())
            ->setId(\$model->{${ENTITY_NAME}::ID})
            ->setName(\$model->{${ENTITY_NAME}::NAME})
            ->setCreatedAt(\$model->{${ENTITY_NAME}::CREATED_AT})
            ->setUpdatedAt(\$model->{${ENTITY_NAME}::UPDATED_AT})
        ;
    }

    public function makeDtoFromObjects(array|Collection \$models): Collection
    {
        \$result = new Collection();
        foreach (\$models as \$model) {
            \$result->add(\$this->makeDtoFromObject(\$model));
        }
        return \$result;
    }

    public function makeModelFromDto(${ENTITY_NAME}DTO \$dto, ?${ENTITY_NAME} \$model): ${ENTITY_NAME}
    {
        if (\$model) {
            \$result = \$model;
        } else {
            \$result = new ${ENTITY_NAME}();
        }

        \$result->fill([
            ${ENTITY_NAME}::NAME => \$dto->getName(),
        ]);

        return \$result;
    }
}
EOF

echo -e "${GREEN}Created Factory: ${OUTPUT_FILE}${NC}"