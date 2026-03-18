# EC-CUBE お気に入り登録数表示プラグイン

商品詳細・一覧ページのカートボタン上に、お気に入り登録数を表示するEC-CUBE 4.2/4.3向けプラグインです。

```
♡ 12人がお気に入り登録
```

## 動作環境

- EC-CUBE: 4.2 / 4.3系
- PHP: 8.1以上

## インストール

```bash
bin/console eccube:plugin:install --code=FavoriteCount
bin/console eccube:plugin:enable --code=FavoriteCount
```

## アンインストール

```bash
bin/console eccube:plugin:disable --code=FavoriteCount
bin/console eccube:plugin:uninstall --code=FavoriteCount
```

## デザインのカスタマイズ

管理画面の **コンテンツ管理 → テンプレート管理** から以下のファイルを編集できます。

- `FavoriteCount/favorite_count.twig` — 商品詳細ページ用
- `FavoriteCount/favorite_count_list.twig` — 商品一覧ページ用

## ライセンス

MIT
