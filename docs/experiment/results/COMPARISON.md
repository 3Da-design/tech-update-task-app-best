# 改良構成 vs 従来構成 — 実験比較表

本ドキュメントは、4 つの更新シナリオについて **改良構成（improved）** と **従来構成（legacy）** で実施した 3 フェーズ計測の要約です。詳細 JSON は各サブディレクトリを参照してください。

## シナリオ 1: バックエンド API 仕様変更（`priority` 追加）

| 構成 | フェーズ | PHPUnit | Newman | PHPStan | Vite build |
|------|----------|---------|--------|---------|------------|
| improved | baseline | 38/38 (100%) | 13/13 (100%) | 0 | OK |
| improved | after_update | 36/38 (94.74%) | 10/13 (76.92%) | 0 | OK |
| improved | after_fix | 38/38 (100%) | 13/13 (100%) | 0 | OK |
| legacy | baseline | 38/38 (100%) | 13/13 (100%) | 0 | OK |
| legacy | after_update | 36/38 (94.74%) | 10/13 (76.92%) | 0 | OK |
| legacy | after_fix | 38/38 (100%) | 13/13 (100%) | 0 | OK |

**主な修正ファイル（改良）:** `TaskResource`, FormRequest×2, `TaskService`, migration, テスト, Postman（Controller / Repository は未変更）

**主な修正ファイル（従来）:** 上記に加え **`Web\TaskController` と `API\TaskController` の `normalizeTaskPayload` を両方更新**（Service 層なしのため重複修正）

| run_id | 構成 | 結果ディレクトリ |
|--------|------|------------------|
| `run-20260521T060318Z` | improved | [api-spec-change/](./api-spec-change/) |
| `run-20260521T061416Z` | legacy | [legacy/api-spec-change/](./legacy/api-spec-change/) |

**所見:** 更新直後の失敗数は同一。従来構成では修正対象が Controller 2 ファイルに分散し、改良構成ではタスク層（Service 周辺）に集約される。

---

## シナリオ 2: Laravel バージョン更新（13.8.0 → 13.11.2）

| フェーズ | PHPUnit | Newman | PHPStan | 備考 |
|----------|---------|--------|---------|------|
| baseline | 38/38 (100%) | 13/13 (100%) | 0 | |
| after_update | 38/38 (100%) | 13/13 (100%) | 0 | 同一メジャー内マイナー更新 |
| after_fix | 38/38 (100%) | 13/13 (100%) | 0 | コード修正不要 |

結果: [laravel-upgrade/](./laravel-upgrade/)（`run-20260521T060830Z`）

---

## シナリオ 3: テストツール更新（PHPUnit / PHPStan / Larastan / Newman）

| フェーズ | PHPUnit | Newman | PHPStan | 備考 |
|----------|---------|--------|---------|------|
| baseline | 38/38 (100%) | 13/13 (100%) | 0 | |
| after_update | 38/38 (100%) | 13/13 (100%) | 0 | lock 更新のみ |
| after_fix | 38/38 (100%) | 13/13 (100%) | 0 | テストコード修正不要 |

結果: [test-tool-upgrade/](./test-tool-upgrade/)（`run-20260521T060939Z`）

---

## シナリオ 4: JavaScript ライブラリ変更（Alpine / Vite / Tailwind 4）

| フェーズ | PHPUnit | Newman | ESLint | Vite build |
|----------|---------|--------|--------|------------|
| baseline | 38/38 (100%) | 13/13 (100%) | OK | OK |
| after_update | 38/38 (100%) | 13/13 (100%) | OK | **失敗**（Tailwind 4 PostCSS 非互換） |
| after_fix | 38/38 (100%) | 13/13 (100%) | OK | OK（`@tailwindcss/vite` 移行後） |

結果: [js-library-change/](./js-library-change/)（`run-20260521T061059Z`）

**after_fix で修正したファイル:** `vite.config.js`, `resources/css/app.css`, `postcss.config.js`

---

## ブランチ一覧

| ブランチ | 内容 |
|----------|------|
| `main` | 改良構成 + シナリオ1（priority）+ 全結果ドキュメント |
| `experiment-baseline-v1` | 改良構成ベースライン（メトリクス整備後） |
| `exp/api-spec-change` | シナリオ1（3フェーズ実施済み） |
| `exp/laravel-upgrade` | シナリオ2 |
| `exp/test-tool-upgrade` | シナリオ3 |
| `exp/js-library-change` | シナリオ4 |
| `legacy-architecture` | 従来構成（Controller 直 DB） |
| `exp/legacy-api-spec-change` | 従来構成でのシナリオ1 |

---

## 評価指標の達成

| 指標 | 状態 |
|------|------|
| テスト通過率（3フェーズ） | 全シナリオで `baseline` / `after_update` / `after_fix` JSON あり |
| エラー発生率 | シナリオ1・4で `after_update` の失敗数を記録 |
| 修正工数 | `git diff --stat` は手動記録（`metrics-record-template.md`） |
| 従来構成比較 | シナリオ1で improved vs legacy を実施 |
