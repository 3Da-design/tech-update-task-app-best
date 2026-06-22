# BEFORE（変更前ベースライン）

更新シナリオを始める前の手順です。本リポジトリは **Web `8003` / DB 公開 `5434`** で動作します。

## 1. ブランチ作成

```bash
git fetch --tags
git checkout experiment-baseline-v1
git checkout -b exp/best-api-spec-change-status-int
```

## 1-1. 品質ゲート確認

```bash
./scripts/check-quality.sh
```

- 接続先: `http://localhost:8003`
- コンテナ: `tech-update-task-app-best-*`

## 1-2. ベースライン計測

```bash
composer experiment:metrics -- --phase baseline --diff-ref experiment-baseline-v1
```

## 1-3. 記録

[metrics-record-template.md](./metrics-record-template.md) の列定義に従い記録する。

## シナリオ ID

| ID | ブランチ例 |
|----|-----------|
| `api-spec-change-status-int` | `exp/best-api-spec-change-status-int` |
| `api-spec-change-priority` | `exp/best-api-spec-change-priority` |
| `db-schema-change` | `exp/best-db-schema-change` |
