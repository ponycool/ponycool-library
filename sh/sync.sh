#!/usr/bin/env bash

DIRNAME=$0
if [ "${DIRNAME:0:1}" = "/" ]; then
  CUR_DIR=$(dirname "$DIRNAME")
else
  CUR_DIR="$(pwd)"/"$(dirname "$DIRNAME")"
fi
. "$CUR_DIR"/message.sh

SOURCE_PROJECT_PATH=~/Documents/Dev/webroot/ponycool-core/app/ThirdParty/PonyCool
PROJECT_PATH=~/Documents/Dev/webroot/ponycool-library/PonyCool

info "开始同步......"

rm -rf $PROJECT_PATH
cp -a $SOURCE_PROJECT_PATH $PROJECT_PATH && rm -f $PROJECT_PATH/.DS_Store

# shellcheck disable=SC2181
if [ "$?" != 0 ]; then
  error "Copy FAILED"
else
  success "Copy SUCCESS"
fi
