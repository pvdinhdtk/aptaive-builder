#!/usr/bin/env bash
set -e

PLUGIN=aptaive-builder
OUT="$HOME/Desktop/${PLUGIN}-release"

echo "📦 Building plugin to: $OUT"

rm -rf "$OUT"
mkdir -p "$OUT/$PLUGIN"

rsync -av \
  --exclude='.git' \
  --exclude='bin' \
  --exclude='stubs' \
  --exclude='vendor' \
  --exclude='node_modules' \
  --exclude='README-DEV.md' \
  --exclude='admin/dev' \
  --exclude='composer.*' \
  --exclude='phpstan*' \
  ./ \
  "$OUT/$PLUGIN/"

cd "$OUT"
zip -r ${PLUGIN}.zip ${PLUGIN}

echo "✅ Done!"
echo "➡️  File: $OUT/${PLUGIN}.zip"
