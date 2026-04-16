#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")/.."

ALLOWLIST_FILE="scripts/architecture-allowlist.txt"

fail() {
    printf 'Architecture check failed: %s\n' "$1" >&2
    exit 1
}

filter_allowlisted() {
    if [[ -f "$ALLOWLIST_FILE" ]]; then
        grep -Ev -f "$ALLOWLIST_FILE" || true
    else
        cat
    fi
}

check_absent() {
    local pattern="$1"
    local scope="$2"
    local message="$3"

    if rg -n "$pattern" $scope >/tmp/architecture-check.out 2>/dev/null; then
        local filtered
        filtered="$(filter_allowlisted < /tmp/architecture-check.out)"

        if [[ -n "$filtered" ]]; then
            printf '%s\n' "$filtered" >&2
            fail "$message"
        fi
    fi
}

check_absent 'app\(' 'core/Domains' '`app(...)` is forbidden in core/Domains'
check_absent 'Locator' 'core/App' 'Locator usage is forbidden in core/App'
check_absent '->validate\(|validate\(' 'app/Http/Controllers' 'Controller validation must live in core/App validators'
check_absent 'Validator' 'app/Http/Controllers' 'Controllers must not depend on validators directly'
check_absent 'DB::transaction|beginTransaction|commit\(|rollBack\(' 'app/Http/Controllers' 'Transactions are forbidden in controllers'
check_absent 'makeDtoFromObject|makeDtoFromObjects|makeModelFromDto' 'app core' 'Legacy mapping methods are forbidden'

printf 'Architecture check passed.\n'
