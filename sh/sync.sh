#!/usr/bin/env bash

# 提示信息
function info() {
  echo -e "\033[32m提示信息：$1\033[0m"
}

# 成功信息
function success() {
  echo -e "\033[36m成功信息：$1\033[0m"
}

# 错误信息
function error() {
  echo -e "\033[31m错误信息：$1\033[0m"
  exit
}

SOURCE_PROJECT_PATH=~/Documents/Dev/webroot/ponycool-core/app/ThirdParty/PonyCool
PROJECT_PATH=~/Documents/Dev/webroot/ponycool-library/PonyCool

info "开始同步......"

rm -rf $PROJECT_PATH
cp -a $SOURCE_PROJECT_PATH $PROJECT_PATH && rm -f $PROJECT_PATH/.DS_Store

# shellcheck disable=SC2181
if [ "$?" != 0 ]; then
  error "Copy failed"
else
  success "Copy SUCCESS"
fi
