#!/usr/bin/env bash
set -e

PLUGIN=aptaive-builder
VERSION=$(
  sed -n 's/^Version:[[:space:]]*//p' "${PLUGIN}.php" | head -n 1
)

if [ -z "$VERSION" ]; then
  echo "❌ Could not determine plugin version from ${PLUGIN}.php"
  exit 1
fi

PACKAGE_NAME="${PLUGIN}-${VERSION}"
OUT="$HOME/Desktop/${PACKAGE_NAME}-release"

echo "📦 Building plugin to: $OUT"

rm -rf "$OUT"
mkdir -p "$OUT/$PLUGIN"

rsync -av \
  --exclude='.git' \
  --exclude='.github' \
  --exclude='.vscode' \
  --exclude='.gitignore' \
  --exclude='.DS_Store' \
  --exclude='bin' \
  --exclude='stubs' \
  --exclude='vendor' \
  --exclude='node_modules' \
  --exclude='README-DEV.md' \
  --exclude='README.md' \
  --exclude='*.log' \
  --exclude='admin/dev' \
  --exclude='composer.*' \
  --exclude='phpstan*' \
  ./ \
  "$OUT/$PLUGIN/"

cd "$OUT"
zip -r "${PACKAGE_NAME}.zip" "${PLUGIN}"

echo "✅ Done!"
echo "➡️  File: $OUT/${PACKAGE_NAME}.zip"
