#!/usr/bin/env bash

DIRNAME=$0
if [ "${DIRNAME:0:1}" = "/" ]; then
  CUR_DIR=$(dirname "$DIRNAME")
else
  CUR_DIR="$(pwd)"/"$(dirname "$DIRNAME")"
fi
. "$CUR_DIR"/message.sh

info "开始测试......"

vendor/bin/phpunit --configuration phpunit.xml --coverage-text --colors=never

# shellcheck disable=SC2181
if [ "$?" != 0 ]; then
  error "TEST FAILED"
else
  success "TEST SUCCESS"
fi
