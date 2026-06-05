#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")/.."

BACKEND_ALLOWLIST_FILE="scripts/architecture-allowlist.txt"
FRONTEND_ALLOWLIST_FILE="scripts/frontend-architecture-allowlist.txt"

fail() {
    printf 'Architecture check failed: %s\n' "$1" >&2
    exit 1
}

filter_allowlisted() {
    local allowlist_file="$1"

    if [[ -f "$allowlist_file" ]]; then
        grep -Ev -f <(grep -Ev '^[[:space:]]*(#|$)' "$allowlist_file") || true
    else
        cat
    fi
}

check_absent() {
    local pattern="$1"
    local scope="$2"
    local message="$3"
    local allowlist_file="${4:-$BACKEND_ALLOWLIST_FILE}"

    if rg -n "$pattern" $scope >/tmp/architecture-check.out 2>/dev/null; then
        local filtered
        filtered="$(filter_allowlisted "$allowlist_file" < /tmp/architecture-check.out)"

        if [[ -n "$filtered" ]]; then
            printf '%s\n' "$filtered" >&2
            fail "$message"
        fi
    fi
}

check_vue_component_size() {
    local max_lines=320
    local output
    local filtered

    output="$(
        find resources/js/components -name '*.vue' -print0 \
            | xargs -0 wc -l \
            | awk -v max_lines="$max_lines" '$1 > max_lines && $2 != "total" { printf "%s:%s: Vue component exceeds %s lines\n", $2, $1, max_lines }'
    )"

    filtered="$(printf '%s\n' "$output" | filter_allowlisted "$FRONTEND_ALLOWLIST_FILE")"

    if [[ -n "$filtered" ]]; then
        printf '%s\n' "$filtered" >&2
        fail "Vue components over ${max_lines} lines must be split or explicitly baselined"
    fi
}

check_absent 'app\(' 'core/Domains' '`app(...)` is forbidden in core/Domains'
check_absent 'Locator' 'core/App' 'Locator usage is forbidden in core/App'
check_absent 'use App\\Models' 'core/Domains' 'Eloquent models are forbidden in core/Domains'
check_absent 'use Illuminate' 'core/Domains' 'Illuminate dependencies are forbidden in core/Domains'
check_absent 'dispatch_sync\(|dispatch\(new .*Job' 'core' 'Core must publish events, not dispatch jobs directly'
check_absent 'class .*Job' 'core/Domains' 'Domain jobs are forbidden in core/Domains'
check_absent 'Locator|Facade' 'core/Domains' 'Locator/facade style is forbidden in core/Domains'
check_absent 'class .*Handler' 'core/App' 'Application use cases in core/App must be Commands with execute(), not Handlers'
check_absent 'public function handle\(' 'core/App' 'Application use cases in core/App must expose execute(), not handle()'
check_absent 'class .*Dto|class .*DTO|class .*Request|class .*Bag' 'core/App' 'Application input objects in core/App must be named *Input'
check_absent '->validate\(|validate\(' 'app/Http/Controllers' 'Controller validation must live in core/App validators'
check_absent 'Validator' 'app/Http/Controllers' 'Controllers must not depend on validators directly'
check_absent 'DB::transaction|beginTransaction|commit\(|rollBack\(' 'app/Http/Controllers' 'Transactions are forbidden in controllers'
check_absent 'makeDtoFromObject|makeDtoFromObjects|makeModelFromDto' 'app core' 'Legacy mapping methods are forbidden'

check_absent 'window\.axios|\baxios\.' 'resources/js/components' 'Direct axios usage is forbidden in Vue components' "$FRONTEND_ALLOWLIST_FILE"
check_absent 'routes-functions|Url\.Routes|Url\.Generator|utils/Url|/utils/Url' 'resources/js/components' 'Legacy route helpers are forbidden in Vue components' "$FRONTEND_ALLOWLIST_FILE"
check_absent '\$\(|jQuery' 'resources/js/components' 'jQuery usage is forbidden in Vue components' "$FRONTEND_ALLOWLIST_FILE"
check_absent 'export default' 'resources/js/components' 'New Vue components must use <script setup>' "$FRONTEND_ALLOWLIST_FILE"

check_absent '^' 'resources/views/auth' 'Legacy Blade path resources/views/auth is forbidden; use resources/views/pages/auth'
check_absent '^' 'resources/views/admin/pages' 'Legacy Blade path resources/views/admin/pages is forbidden; use resources/views/pages/admin'
check_absent '^' 'resources/views/admin/error-logs' 'Legacy Blade path resources/views/admin/error-logs is forbidden; use resources/views/pages/admin/system'
check_absent '^' 'resources/views/admin/emails' 'Legacy Blade path resources/views/admin/emails is forbidden; use resources/views/pages/admin/system'
check_vue_component_size

printf 'Architecture check passed.\n'
