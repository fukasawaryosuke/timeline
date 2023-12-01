## コーディング

### backend

#### 記法

* Controllersのファイル名 → アッパーキャメル（HogeData）

* モデル名 → アッパーキャメル

* migration名 → スネークケース（hoge_data）

* 変数名 → ローワーキャメルケース（hogeHoge）
※命名を長くせざるを得ない場合

* 関数名 → スネークケース（hoge_data）
※命名を長くせざるを得ない場合


## 拡張機能

#### Prettier


* 拡張機能で「PrettierPrettier」と検索し、"Prettier" をインストール


#### ESLint


* 拡張機能で「ESLint」と検索し、"ESLint" をインストール


### .prettierrc 設定例

```console
{
    "singleQuote": true,
    "htmlWhitespaceSensitivity": "ignore"
}
```
