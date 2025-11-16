#!/usr/bin/env bash
set -euo pipefail

TARGET_DIR="${1:-contacts-app}"
LARAVEL_VERSION="11.*"
REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

if ! command -v composer >/dev/null 2>&1; then
  echo "[setup] composer is required" >&2
  exit 1
fi

if ! command -v php >/dev/null 2>&1; then
  echo "[setup] php is required" >&2
  exit 1
fi

if [ -e "$TARGET_DIR" ]; then
  echo "[setup] target directory '$TARGET_DIR' already exists" >&2
  exit 1
fi

composer create-project laravel/laravel "$TARGET_DIR" "$LARAVEL_VERSION"

copy_dir() {
  local source_dir="$1"
  local dest_dir="$2"
  if [ ! -d "$source_dir" ]; then
    return
  fi

  if command -v rsync >/dev/null 2>&1; then
    rsync -a --delete "$source_dir"/ "$dest_dir"/
  else
    mkdir -p "$dest_dir"
    cp -R "$source_dir"/. "$dest_dir"/
  fi
}

copy_dir "$REPO_ROOT/app" "$TARGET_DIR/app"
copy_dir "$REPO_ROOT/database" "$TARGET_DIR/database"
copy_dir "$REPO_ROOT/resources" "$TARGET_DIR/resources"
copy_dir "$REPO_ROOT/routes" "$TARGET_DIR/routes"

cp "$REPO_ROOT/.env.example" "$TARGET_DIR/.env.example"
cp "$REPO_ROOT/.env.example" "$TARGET_DIR/.env"

touch "$TARGET_DIR/database/database.sqlite"

pushd "$TARGET_DIR" >/dev/null
php artisan key:generate --ansi
php artisan migrate --force
php artisan db:seed --force
popd >/dev/null

echo "[setup] All done. Run 'cd $TARGET_DIR && php artisan serve' to start the app."
