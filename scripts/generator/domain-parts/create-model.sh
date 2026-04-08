#!/usr/bin/env bash

# Создаёт файл модели Eloquent для домена
# Использование:
#   create-model.sh -n SomeObject -p /path/to/Domains

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
OUTPUT_FILE="${DOMAIN_DIR}/Models/${ENTITY_NAME}.php"

if [ -f "$OUTPUT_FILE" ]; then
    echo -e "${YELLOW}Skipped Model (exists): ${OUTPUT_FILE}${NC}"
    exit 0
fi

# Преобразование имени в snake_case для имени таблицы
ENTITY_SNAKE=$(echo "$ENTITY_NAME" | sed 's/\([A-Z]\)/_\L\1/g' | sed 's/^_//')
TABLE_NAME="${ENTITY_SNAKE}"

cat > "$OUTPUT_FILE" <<EOF
<?php declare(strict_types=1);

namespace Core\Domains\\${ENTITY_NAME}\Models;

use App\Models\AbstractModel;
use Carbon\Carbon;

/**
 * @property int         \$id
 * @property string      \$name
 * @property Carbon|null \$created_at
 * @property Carbon|null \$updated_at
 */
class ${ENTITY_NAME} extends AbstractModel
{
    public const string TABLE = '${TABLE_NAME}';

    public const string ID   = 'id';
    public const string NAME = 'name';
    
    public const array PROPERTIES_TO_TITLES = [];

    protected \$guarded = [];
}
EOF

echo -e "${GREEN}Created Model: ${OUTPUT_FILE}${NC}"