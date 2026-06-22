# メトリクス記録テンプレート

## 列定義

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

## 記録例

| repository | scenario | phase | files_changed | notes |
|------------|----------|-------|---------------|-------|
| best | api-spec-change-status-int | after_fix | 5 | [api-spec-change-status-int/](./results/api-spec-change-status-int/) |
| best | api-spec-change-priority | after_fix | 7 | [api-spec-change-priority/](./results/api-spec-change-priority/) |
| best | db-schema-change | after_fix | 2 | [db-schema-change/](./results/db-schema-change/) |

結果一覧: [results/RESULTS.md](./results/RESULTS.md)
