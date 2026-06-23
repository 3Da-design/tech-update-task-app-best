# 実験設計

本リポジトリ（`tech-update-task-app-best`）で、技術更新シナリオの影響をメトリクスとして記録するための手順です。

## 更新シナリオ（3 件）

| ID | ドキュメント |
|----|--------------|
| `api-spec-change-status-int` | [api-spec-change-status-int.md](./scenarios/api-spec-change-status-int.md) |
| `api-spec-change-priority` | [api-spec-change-priority.md](./scenarios/api-spec-change-priority.md) |
| `db-schema-change` | [db-schema-change.md](./scenarios/db-schema-change.md) |

## アーキテクチャ（計測対象）

- **Controller:** 薄い層。`TaskService` へ委譲。認可は `TaskPolicy` + `FindsAuthorizedTask`
- **Service:** `TaskService` がユースケース境界
- **協調クラス:** `TaskListQuery`、`TaskInputNormalizer`、`TaskListFilters`
- **入出力:** `TaskPayloadRequest`、`TaskResource`

## 評価指標

| 優先 | 指標 | 取得方法 |
|------|------|----------|
| 1 | 修正工数（ファイル数・行数） | `composer experiment:metrics` の `git.*`（**after_fix**） |
| 2 | 更新直後のテスト失敗数 | `phpunit.fail` / `newman.fail`（**after_update**） |
| 3 | 作業時間（分） | [メトリクス記録テンプレート](#メトリクス記録テンプレート) に手動記録 |

## 実験フェーズ

| フェーズ | 説明 |
|----------|------|
| `baseline` | 更新前・CI 緑 |
| `after_update` | 更新適用直後・テスト未修正 |
| `after_fix` | 修正完了・CI 緑 |

```bash
composer experiment:metrics -- --phase baseline --diff-ref experiment-baseline-v1
composer experiment:metrics -- --phase after_update --diff-ref experiment-baseline-v1
composer experiment:metrics -- --phase after_fix --diff-ref experiment-baseline-v1
```

## ベースライン

```bash
./scripts/check-quality.sh
git tag -a experiment-baseline-v1 -m "Experiment baseline: best architecture"
```

詳細手順: [ベースライン手順（BEFORE）](#ベースライン手順before)

## ベースライン手順（BEFORE）

更新シナリオを始める前の手順です。本リポジトリは **Web `8003` / DB 公開 `5434`** で動作します。

### 1. ブランチ作成

```bash
git fetch --tags
git checkout experiment-baseline-v1
git checkout -b exp/best-api-spec-change-status-int
```

### 1-1. 品質ゲート確認

```bash
./scripts/check-quality.sh
```

- 接続先: `http://localhost:8003`
- コンテナ: `tech-update-task-app-best-*`

### 1-2. ベースライン計測

```bash
composer experiment:metrics -- --phase baseline --diff-ref experiment-baseline-v1
```

### 1-3. 記録

[メトリクス記録テンプレート](#メトリクス記録テンプレート) の列定義に従い記録する。

### シナリオ ID

| ID | ブランチ例 |
|----|-----------|
| `api-spec-change-status-int` | `exp/best-api-spec-change-status-int` |
| `api-spec-change-priority` | `exp/best-api-spec-change-priority` |
| `db-schema-change` | `exp/best-db-schema-change` |

## 結果の公開

```bash
composer experiment:record -- --scenario api-spec-change-status-int --write
./scripts/publish-experiment-results.sh --scenario api-spec-change-status-int
```

公開先: `experiment/results/<scenario>/`（各シナリオの JSON・RECORD.md）

## メトリクス記録テンプレート

### 列定義

| 列名 | 説明 | 例 |
|------|------|-----|
| `repository` | 固定値 | `best` |
| `scenario` | シナリオ ID | `api-spec-change-status-int` |
| `phase` | フェーズ | `baseline` / `after_update` / `after_fix` |
| `phpunit_pass` / `phpunit_total` | PHPUnit | `38` / `38` |
| `newman_pass` / `newman_total` | Newman | `13` / `13` |
| `phpstan_errors` | PHPStan 件数 | `0` |
| `files_changed` | 変更ファイル数 | `7` |
| `lines_added` / `lines_deleted` | 行数 | `45` / `10` |
| `work_minutes` | 作業時間（分） | `30` |
| `notes` | メモ | `TaskInputNormalizer のみ` |

### 記録例

| repository | scenario | phase | files_changed | notes |
|------------|----------|-------|---------------|-------|
| best | api-spec-change-status-int | after_fix | 5 | [api-spec-change-status-int/](../../experiment/results/api-spec-change-status-int/) |
| best | api-spec-change-priority | after_fix | 7 | [api-spec-change-priority/](../../experiment/results/api-spec-change-priority/) |
| best | db-schema-change | after_fix | 2 | [db-schema-change/](../../experiment/results/db-schema-change/) |

## 関連ドキュメント

| ドキュメント | 内容 |
|--------------|------|
| [README.md](../README.md) | プロジェクト概要 |
