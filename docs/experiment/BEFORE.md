# BEFORE（変更前ベースライン）

legacy / improved とも **同一手順**。本リポジトリ（best）は **Web `8003` / DB 公開 `5434`**（他構成と衝突しないよう分離済み）。

## 前提

1. `git pull origin main` で最新を取得してから `git fetch --tags` する（`experiment-baseline-v1` が best Docker 分離済みであること）。
2. 他構成と **同時に動かす場合**、ポート・コンテナ名が重複しないことを `.env` で確認する。

## 1. ブランチ作成

```bash
git fetch --tags
git checkout experiment-baseline-v1
git checkout -b exp/api-spec-change-status-int
```

## 1-1. 品質ゲート確認

```bash
./scripts/check-quality.sh
```

**期待:** すべて成功（PHPStan / ESLint / Vite build / PHPUnit / Newman 13/13）。

- 接続先は `http://localhost:8003`（`scripts/lib/app-base-url.sh`）。
- 起動コンテナは `tech-update-task-app-best-*`（`scripts/lib/ensure-docker-stack.sh` が検証）。

## 1-2. ベースライン計測

```bash
composer experiment:metrics -- --phase baseline --diff-ref experiment-baseline-v1
```

## 1-3. 記録

[metrics-record-template.md](./metrics-record-template.md) の列定義に従い記録する。

## タグの更新について

`experiment-baseline-v1` は **CI 緑かつ best Docker（8003）が入った `main` の先端** を指す。古いタグ（8000 / `tech-update-task-app-php` のみ）のままでは 1-1 / 1-2 でコンテナ名衝突が起きる。

タグを更新したあと、手元で確認:

```bash
git fetch --tags
git show experiment-baseline-v1:docker-compose.yml | head -5
# → tech-update-task-app-best / 8003 系であること
```
