#!/usr/bin/env bash

# Скрипт для генерации классов сущности в Core\Domains
# Использование:
#   ./create-domain.sh -n SomeObject

set -e

# Цвета
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

usage() {
    echo "Usage: $0 -n NAME [-p DOMAINS_BASE]"
    echo "  -n NAME        Entity name (e.g., SomeObject, TicketCategory)"
    echo "  -p DOMAINS_BASE Path to 'Domains' folder (default: ../core/Domains relative to script)"
    exit 1
}

# Парсинг параметров
while getopts "n:p:" opt; do
    case $opt in
        n) ENTITY_NAME="$OPTARG" ;;
        p) DOMAINS_BASE="$OPTARG" ;;
        *) usage ;;
    esac
done

if [ -z "$ENTITY_NAME" ]; then
    echo -e "${YELLOW}Error: Entity name is required${NC}"
    usage
fi

# Определяем базовый путь к Domains, если не задан
if [ -z "$DOMAINS_BASE" ]; then
    SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
    # Скрипт лежит в web/scripts, поднимаемся на уровень вверх, затем core/Domains
    DOMAINS_BASE="$(cd "$SCRIPT_DIR/../core/Domains" && pwd)"
fi

DOMAIN_DIR="${DOMAINS_BASE}/${ENTITY_NAME}"
mkdir -p "${DOMAIN_DIR}"

# Преобразование имени в snake_case для таблицы
ENTITY_SNAKE=$(echo "$ENTITY_NAME" | sed 's/\([A-Z]\)/_\L\1/g' | sed 's/^_//')
TABLE_NAME="${ENTITY_SNAKE}"

# Создаём подпапки
mkdir -p "${DOMAIN_DIR}/Models"
mkdir -p "${DOMAIN_DIR}/Factories"
mkdir -p "${DOMAIN_DIR}/Repositories"
mkdir -p "${DOMAIN_DIR}/Services"
mkdir -p "${DOMAIN_DIR}/Collection"
mkdir -p "${DOMAIN_DIR}/Responses"
mkdir -p "${DOMAIN_DIR}/Searchers"

# -------------------------------------------------------------------
# 1. Модель (Eloquent)
# -------------------------------------------------------------------
MODEL_FILE="${DOMAIN_DIR}/Models/${ENTITY_NAME}.php"
if [ ! -f "$MODEL_FILE" ]; then
    cat > "$MODEL_FILE" <<EOF
<?php declare(strict_types=1);

namespace Core\Domains\\${ENTITY_NAME}\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int \$id
 * @property string \$name
 * @property Carbon|null \$created_at
 * @property Carbon|null \$updated_at
 */
class ${ENTITY_NAME} extends Model
{
    public const string TABLE = '${TABLE_NAME}';

    protected \$table = self::TABLE;

    public const string ID = 'id';

    protected \$guarded = [];
}
EOF
    echo -e "${GREEN}Created Model: ${MODEL_FILE}${NC}"
else
    echo -e "${YELLOW}Skipped Model (exists): ${MODEL_FILE}${NC}"
fi

# -------------------------------------------------------------------
# 2. DTO
# -------------------------------------------------------------------
DTO_FILE="${DOMAIN_DIR}/Models/${ENTITY_NAME}DTO.php"
if [ ! -f "$DTO_FILE" ]; then
    cat > "$DTO_FILE" <<EOF
<?php declare(strict_types=1);

namespace Core\Domains\\${ENTITY_NAME}\Models;

use Core\Domains\Common\Traits\TimestampsTrait;

use Carbon\Carbon;

class ${ENTITY_NAME}DTO
{
    use TimestampsTrait;

    private ?int \$id = null;

    public function getId(): ?int
    {
        return \$this->id;
    }

    public function setId(?int \$id): static
    {
        \$this->id = \$id;
        return \$this;
    }
}
EOF
    echo -e "${GREEN}Created DTO: ${DTO_FILE}${NC}"
else
    echo -e "${YELLOW}Skipped DTO (exists): ${DTO_FILE}${NC}"
fi

# -------------------------------------------------------------------
# 3. Factory
# -------------------------------------------------------------------
FACTORY_FILE="${DOMAIN_DIR}/Factories/${ENTITY_NAME}Factory.php"
if [ ! -f "$FACTORY_FILE" ]; then
    cat > "$FACTORY_FILE" <<EOF
<?php declare(strict_types=1);

namespace Core\Domains\\${ENTITY_NAME}\Factories;

use Core\Domains\\${ENTITY_NAME}\Models\\${ENTITY_NAME};
use Core\Domains\\${ENTITY_NAME}\Models\\${ENTITY_NAME}DTO;
use Illuminate\Database\Eloquent\Collection;

class ${ENTITY_NAME}Factory
{
    public function makeDtoFromObject(${ENTITY_NAME} \$model): ${ENTITY_NAME}DTO
    {
        return new ${ENTITY_NAME}DTO()
            ->setId(\$model->{${ENTITY_NAME}::ID})
            ->setCreatedAt(\$model->{${ENTITY_NAME}::CREATED_AT})
            ->setUpdatedAt(\$model->{${ENTITY_NAME}::UPDATED_AT});
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
    echo -e "${GREEN}Created Factory: ${FACTORY_FILE}${NC}"
else
    echo -e "${YELLOW}Skipped Factory (exists): ${FACTORY_FILE}${NC}"
fi

# -------------------------------------------------------------------
# 4. Repository
# -------------------------------------------------------------------
REPOSITORY_FILE="${DOMAIN_DIR}/Repositories/${ENTITY_NAME}Repository.php"
if [ ! -f "$REPOSITORY_FILE" ]; then
    cat > "$REPOSITORY_FILE" <<EOF
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
    echo -e "${GREEN}Created Repository: ${REPOSITORY_FILE}${NC}"
else
    echo -e "${YELLOW}Skipped Repository (exists): ${REPOSITORY_FILE}${NC}"
fi

# -------------------------------------------------------------------
# 5. Service
# -------------------------------------------------------------------
SERVICE_FILE="${DOMAIN_DIR}/Services/${ENTITY_NAME}Service.php"
if [ ! -f "$SERVICE_FILE" ]; then
    cat > "$SERVICE_FILE" <<EOF
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
    echo -e "${GREEN}Created Service: ${SERVICE_FILE}${NC}"
else
    echo -e "${YELLOW}Skipped Service (exists): ${SERVICE_FILE}${NC}"
fi

# -------------------------------------------------------------------
# 6. Collection
# -------------------------------------------------------------------
COLLECTION_FILE="${DOMAIN_DIR}/Collection/${ENTITY_NAME}Collection.php"
if [ ! -f "$COLLECTION_FILE" ]; then
    cat > "$COLLECTION_FILE" <<EOF
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
    echo -e "${GREEN}Created Collection: ${COLLECTION_FILE}${NC}"
else
    echo -e "${YELLOW}Skipped Collection (exists): ${COLLECTION_FILE}${NC}"
fi

# -------------------------------------------------------------------
# 7. SearchResponse
# -------------------------------------------------------------------
RESPONSE_FILE="${DOMAIN_DIR}/Responses/${ENTITY_NAME}SearchResponse.php"
if [ ! -f "$RESPONSE_FILE" ]; then
    cat > "$RESPONSE_FILE" <<EOF
<?php declare(strict_types=1);

namespace Core\Domains\\${ENTITY_NAME}\Responses;

use Core\Db\Searcher\Models\BaseSearchResponse;
use Core\Domains\\${ENTITY_NAME}\Collection\\${ENTITY_NAME}Collection;

/**
 * @method \${ENTITY_NAME}Collection getItems()
 */
class ${ENTITY_NAME}SearchResponse extends BaseSearchResponse
{
}
EOF
    echo -e "${GREEN}Created SearchResponse: ${RESPONSE_FILE}${NC}"
else
    echo -e "${YELLOW}Skipped SearchResponse (exists): ${RESPONSE_FILE}${NC}"
fi

# -------------------------------------------------------------------
# 8. Searcher (базовый)
# -------------------------------------------------------------------
SEARCHER_FILE="${DOMAIN_DIR}/Searchers/${ENTITY_NAME}Searcher.php"
if [ ! -f "$SEARCHER_FILE" ]; then
    cat > "$SEARCHER_FILE" <<EOF
<?php declare(strict_types=1);

namespace Core\Domains\\${ENTITY_NAME}\Searchers;

use Core\Db\Searcher\SearcherInterface;
use Core\Db\Searcher\SearcherTrait;

class ${ENTITY_NAME}Searcher implements SearcherInterface
{
    use SearcherTrait;
}
EOF
    echo -e "${GREEN}Created Searcher: ${SEARCHER_FILE}${NC}"
else
    echo -e "${YELLOW}Skipped Searcher (exists): ${SEARCHER_FILE}${NC}"
fi

echo -e "${GREEN}Done. Domain '${ENTITY_NAME}' created in ${DOMAIN_DIR}${NC}"