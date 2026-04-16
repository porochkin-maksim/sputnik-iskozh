#!/usr/bin/env bash
set -euo pipefail

docker run --rm -v "$PWD":/app -w /app php:8.4-cli php "$@"
