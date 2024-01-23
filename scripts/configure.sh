#!/bin/sh -

set -e

################################################################################

SCRIPT_DIR=$(cd -- "$(dirname -- "$0")" && pwd -P)
WORK_DIR="${SCRIPT_DIR}/.."

usage() {
    printf './configure [reconfigure]\n'
}

if [ "$(id -u)" = 0 ] ; then
    printf '\033[31mdo not run this script as root\033[36m\n'
    exit 1
fi

if [ $# -gt 1 ] ; then
    usage
    exit 2
fi

if [ $# -eq 1 ] && [ ! "$1" = "reconfigure" ] ; then
    printf '\033[31minvalid first argument\033[36m\n'
    exit 3
fi

if [ -f "${WORK_DIR}/.env" ] ; then
    mv "${WORK_DIR}/.env" "${WORK_DIR}/.env.old"
fi

if [ $# -eq 0 ] ; then
    cp "${WORK_DIR}/.env.dist" "${WORK_DIR}/.env"
else
    cp "${WORK_DIR}/.env.old" "${WORK_DIR}/.env"
fi

if [ ! -w "${WORK_DIR}/.env" ] ; then
    printf '\033[31mwrong file permissions\033[36m\n'
    exit 4
fi

sed -e "s|\(^DOCKER_UID=\)\(.\{1,\}\)|\1$(id -u)|g" "${WORK_DIR}/.env" > "${WORK_DIR}/.env.tmp"
mv "${WORK_DIR}/.env.tmp" "${WORK_DIR}/.env"
sed -e "s|\(^DOCKER_GID=\)\(.\{1,\}\)|\1$(id -g)|g" "${WORK_DIR}/.env" > "${WORK_DIR}/.env.tmp"
mv "${WORK_DIR}/.env.tmp" "${WORK_DIR}/.env"

printf '\033[36mвведите новое значение\033[0m LOCAL_BASE_DOMAIN\t\t(default: %s)\n' $(sed -n -e "s|\(^LOCAL_BASE_DOMAIN=\)\(.\{1,\}\)|\2|p" "${WORK_DIR}/.env")
IFS= read -r answer
if [ -n "$answer" ] ; then
    sed -e "s|\(^LOCAL_BASE_DOMAIN=\)\(.\{1,\}\)|\1$answer|g" "${WORK_DIR}/.env" > "${WORK_DIR}/.env.tmp"
    mv "${WORK_DIR}/.env.tmp" "${WORK_DIR}/.env"
    slug=$(echo "$answer" | sed -e "s|\.|-|g")
    sed -e "s|\(^LOCAL_BASE_DOMAIN_SLUG=\)\(.\{1,\}\)|\1$slug|g" "${WORK_DIR}/.env" > "${WORK_DIR}/.env.tmp"
    mv "${WORK_DIR}/.env.tmp" "${WORK_DIR}/.env"
    service_name=$(sed -n -e "s|\(^SERVICE_NAME=\)\(.\{1,\}\)|\2|p" "${WORK_DIR}/.env")
    sed -e "s|\(^COMPOSE_PROJECT_NAME=\)\(.\{0,\}\)|\1$service_name-$slug|g" "${WORK_DIR}/.env" > "${WORK_DIR}/.env.tmp"
    mv "${WORK_DIR}/.env.tmp" "${WORK_DIR}/.env"
fi


printf '\033[36mВведите свой логин от почты\033[0m \t\t\n' $(sed -n -e "s|\(^SERVER_NAME=\)\(.\{1,\}\)|\2|p" "${WORK_DIR}/.env")
IFS= read -r answer
if [ -n "$answer" ] ; then
    service_name=$(sed -n -e "s|\(^SERVICE_NAME=\)\(.\{1,\}\)|\2|p" "${WORK_DIR}/.env")
    sed -e "s|\(^SERVER_NAME=\)\(.\{0,\}\)|\1$service_name-${answer%%@*}|g" "${WORK_DIR}/.env" > "${WORK_DIR}/.env.tmp"
    mv "${WORK_DIR}/.env.tmp" "${WORK_DIR}/.env"
    sed -e "s|\(^DEBUG_EMAIL=\)\(.\{1,\}\)|\1$answer|g" "${WORK_DIR}/.env" > "${WORK_DIR}/.env.tmp"
    mv "${WORK_DIR}/.env.tmp" "${WORK_DIR}/.env"
    sed -e "s|\(^CATCH_ALL_EMAIL=\)\(.\{1,\}\)|\1$answer|g" "${WORK_DIR}/.env" > "${WORK_DIR}/.env.tmp"
    mv "${WORK_DIR}/.env.tmp" "${WORK_DIR}/.env"
fi

printf '\033[36mSERVER_NAME:\033[0m %s\n' "$(sed -n -e "s|\(^SERVER_NAME=\)\(.\{1,\}\)|\2|p" "${WORK_DIR}/.env")"
printf '\033[36mDOCKER_UID:\033[0m %s\n' "$(id -u)"
printf '\033[36mDOCKER_GID:\033[0m %s\n' "$(id -g)"
