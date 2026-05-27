#!/usr/bin/env bash
# 実行前: PHPStan + ESLint / 実行後: PHPUnit + Newman（Docker 前提）
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"
# shellcheck source=lib/app-base-url.sh
source "${ROOT}/scripts/lib/app-base-url.sh"

echo "== Ensure Docker services (postgres) =="
docker compose up -d postgres
POSTGRES_ID="$(docker compose ps -q postgres)"
if [[ -z "${POSTGRES_ID}" ]]; then
  echo "ERROR: postgres container id not found. docker compose ps で状態を確認してください。"
  exit 1
fi
for _ in $(seq 1 30); do
  health="$(docker inspect -f '{{.State.Health.Status}}' "${POSTGRES_ID}" 2>/dev/null || true)"
  if [[ "${health}" == "healthy" ]]; then
    break
  fi
  sleep 1
done
health="$(docker inspect -f '{{.State.Health.Status}}' "${POSTGRES_ID}" 2>/dev/null || true)"
if [[ "${health}" != "healthy" ]]; then
  echo "ERROR: postgres is not healthy (status=${health}). docker compose logs postgres で確認してください。"
  exit 1
fi

if ! grep -q 'tech-update-task-app-legacy' "${ROOT}/docker-compose.yml" 2>/dev/null; then
  echo "ERROR: legacy 用の docker-compose.yml が見つかりません（実行ディレクトリを確認してください）。"
  exit 1
fi

# shellcheck source=lib/app-base-url.sh
source "${ROOT}/scripts/lib/app-base-url.sh"

echo "== Ensure Docker services (postgres) =="
docker compose up -d postgres
POSTGRES_ID="$(docker compose ps -q postgres)"
if [[ -z "${POSTGRES_ID}" ]]; then
  echo "ERROR: postgres container id not found. docker compose ps で状態を確認してください。"
  exit 1
fi
for _ in $(seq 1 30); do
  health="$(docker inspect -f '{{.State.Health.Status}}' "${POSTGRES_ID}" 2>/dev/null || true)"
  if [[ "${health}" == "healthy" ]]; then
    break
  fi
  sleep 1
done
health="$(docker inspect -f '{{.State.Health.Status}}' "${POSTGRES_ID}" 2>/dev/null || true)"
if [[ "${health}" != "healthy" ]]; then
  echo "ERROR: postgres is not healthy (status=${health}). docker compose logs postgres で確認してください。"
  exit 1
fi

echo "== PHPStan (実行前・型・構造) =="
docker compose exec -T app composer phpstan

echo ""
echo "== Frontend dependencies (Docker / npm ci) =="
docker compose --profile node run --rm node sh -c "rm -rf node_modules/* node_modules/.[!.]* node_modules/..?* && npm ci"

echo ""
echo "== ESLint (実行前・構文・規約) =="
docker compose --profile node run --rm node npm run lint

echo ""
echo "== Frontend build (PHPUnit / Newman 用) =="
docker compose --profile node run --rm node npm run build

echo ""
echo "== PHPUnit (実行後・ロジック) =="
docker compose exec -T app composer test

echo ""
echo "== Newman (実行後・API・セッション) =="
if ! curl -sf "${APP_BASE_URL}/up" > /dev/null 2>&1; then
  echo "${APP_BASE_URL} に接続できないため docker compose up -d を実行します"
  docker compose up -d
  for _ in $(seq 1 30); do
    if curl -sf "${APP_BASE_URL}/up" > /dev/null 2>&1; then
      break
    fi
    sleep 1
  done
  if ! curl -sf "${APP_BASE_URL}/up" > /dev/null 2>&1; then
    echo "ERROR: ${APP_BASE_URL}/up に接続できません。docker compose ps で状態を確認してください。"
    exit 1
  fi
fi

echo "DB migrate --seed（test@example.com / password）"
docker compose exec -T app php artisan migrate --force --seed

docker compose --profile node run --rm node npm run test:api:docker

echo ""
echo "All checks passed (PHPStan, ESLint, PHPUnit, Newman)."
