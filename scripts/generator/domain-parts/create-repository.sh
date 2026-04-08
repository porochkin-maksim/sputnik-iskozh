#!/usr/bin/env bash

# Создаёт Repository для домена
# Использование:
#   create-repository.sh -n SomeObject -p /path/to/Domains

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
OUTPUT_FILE="${DOMAIN_DIR}/Repositories/${ENTITY_NAME}Repository.php"

if [ -f "$OUTPUT_FILE" ]; then
    echo -e "${YELLOW}Skipped Repository (exists): ${OUTPUT_FILE}${NC}"
    exit 0
fi

cat > "$OUTPUT_FILE" <<EOF
<?php declare(strict_types=1);

namespace Core\Domains\\${ENTITY_NAME}\Repositories;

use Core\Domains\\${ENTITY_NAME}\Models\\${ENTITY_NAME};
use Core\Db\RepositoryTrait;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\\${ENTITY_NAME}\Collection\\${ENTITY_NAME}Collection;
use Core\Domains\\${ENTITY_NAME}\Models\\${ENTITY_NAME}DTO;
use Core\Domains\\${ENTITY_NAME}\Factories\\${ENTITY_NAME}Factory;
use Core\Domains\\${ENTITY_NAME}\Responses\\${ENTITY_NAME}SearchResponse;

class ${ENTITY_NAME}Repository
{
    use RepositoryTrait;

    private const string TABLE = ${ENTITY_NAME}::TABLE;

    public function __construct(
        private ${ENTITY_NAME}Factory \$${ENTITY_NAME,,}Factory,
    ) {}

    protected function modelClass(): string
    {
        return ${ENTITY_NAME}::class;
    }

    public function search(SearcherInterface \$searcher): ${ENTITY_NAME}SearchResponse
    {
        \$response   = \$this->searchModels(\$searcher);
        \$collection = new ${ENTITY_NAME}Collection();
        foreach (\$response->getItems() as \$model) {
            \$collection->add(\$this->${ENTITY_NAME,,}Factory->makeDtoFromObject(\$model));
        }

        \$result = new ${ENTITY_NAME}SearchResponse();
        \$result->setTotal(\$response->getTotal())
            ->setItems(\$collection);

        return \$result;
    }

    public function save(${ENTITY_NAME}DTO \$dto): ${ENTITY_NAME}DTO
    {
        \$model = \$this->getModelById(\$dto->getId());
        \$model = \$this->${ENTITY_NAME,,}Factory->makeModelFromDto(\$dto, \$model);
        \$model->save();

        return \$this->${ENTITY_NAME,,}Factory->makeDtoFromObject(\$model);
    }

    public function getById(?int \$id): ?${ENTITY_NAME}DTO
    {
        /** @var ${ENTITY_NAME} \$model */
        \$model = \$this->getModelById(\$id);

        return \$model ? \$this->${ENTITY_NAME,,}Factory->makeDtoFromObject(\$model) : null;
    }
}
EOF

echo -e "${GREEN}Created Repository: ${OUTPUT_FILE}${NC}"