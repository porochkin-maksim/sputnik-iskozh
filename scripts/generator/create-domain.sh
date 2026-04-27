#!/usr/bin/env bash

# Основной оркестратор для генерации всех классов домена
# Использование:
#   ./create-domain.sh -n SomeObject

set -e

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

usage() {
    echo "Usage: $0 -n NAME [-p DOMAINS_BASE]"
    echo "  -n NAME        Entity name (e.g., SomeObject)"
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
    DOMAINS_BASE="$(cd "$SCRIPT_DIR/../../core/Domains" && pwd)"
fi

DOMAIN_DIR="${DOMAINS_BASE}/${ENTITY_NAME}"

# Создаём корневую папку домена и все стандартные подпапки
mkdir -p "${DOMAIN_DIR}/Models"
mkdir -p "${DOMAIN_DIR}/Factories"
mkdir -p "${DOMAIN_DIR}/Repositories"
mkdir -p "${DOMAIN_DIR}/Services"
mkdir -p "${DOMAIN_DIR}/Collection"
mkdir -p "${DOMAIN_DIR}/Responses"
mkdir -p "${DOMAIN_DIR}/Searchers"
mkdir -p "${DOMAIN_DIR}/Validators"
mkdir -p "${DOMAIN_DIR}/UseCases"
mkdir -p "${DOMAIN_DIR}/UseCases/${ENTITY_NAME}"

# Определяем путь к папке с подскриптами (рядом с текущим скриптом)
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
PARTS_DIR="${SCRIPT_DIR}/domain-parts"

# Вызываем каждый подскрипт, передавая ему имя сущности и базовый путь
for part_script in \
    "$PARTS_DIR/create-collection.sh" \
    "$PARTS_DIR/create-dto.sh" \
    "$PARTS_DIR/create-factory.sh" \
    "$PARTS_DIR/create-repository.sh" \
    "$PARTS_DIR/create-search-response.sh" \
    "$PARTS_DIR/create-searcher.sh" \
    "$PARTS_DIR/create-service.sh" \
    "$PARTS_DIR/create-validator.sh" \
    "$PARTS_DIR/create-use-cases.sh" \
    "$PARTS_DIR/create-model.sh"
do
    if [ -f "$part_script" ]; then
        chmod +x "$part_script"
        bash "$part_script" -n "$ENTITY_NAME" -p "$DOMAINS_BASE"
    else
        echo -e "${YELLOW}Warning: $part_script not found, skipping${NC}"
    fi
done

echo -e "${GREEN}Done. Domain '${ENTITY_NAME}' created in ${DOMAIN_DIR}${NC}"