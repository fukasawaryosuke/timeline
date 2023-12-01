# hat(timeline) project

## コーディング

### front

#### vue

・上から script, template, style の順で記述
・interface, type でデータを取り回さない インスタンスを作成して取り回す
・ロジックはなるべく一番上の親コンポーネント or composition function にて定義し子コンポーネントはただの入れ物にして再利用性を向上させる

## 初期設定

### docker

docker インストールしてターミナルに下記コマンドコピペ実行で OK なはず

```console
> コピー
cp .env.example .env

> Docker起動
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/var/www/html \
    -w /var/www/html \
    laravelsail/php80-composer:latest \
    composer install --ignore-platform-reqs
```

sail で起動

```console
sail up
```

### DB

.env に指定されているデータベース作成 \*初期値では"hdc_test"

マイグレート実行

```console
sail php artisan migrate
```

### composer

composer に必要モジュールをインストール \*別ターミナルで実行

```console
sail composer install
```

### npm

npm に必要モジュールをインストール \*別ターミナルで実行

```console
sail npm i
```

npm インストール後にビルドファイル生成

```console
sail npm run dev
```

ビルドファイル生成後は以下のコマンドでホットリロードしてくれる

```console
sail npm run watch
```

## vscode 設定

### 拡張機能

#### vetur をインストールしている場合は無効化しておく

・"volar"で検索し、"Vue Language Features (Volar)"をインストール後、ワークスペースのみで有効化

・"@builtin typescript"で検索し、"TypeScript と JavaScript の言語機能"をワークスペースのみで無効化

### .prettierrc 設定例

```console
{
  "htmlWhitespaceSensitivity": "ignore"
}
```

### .eslintrc 設定例

```console
{
    "root": true,
    "env": {
        "browser": true,
        "es2021": true,
        "node": true
    },
    "extends": [
        "plugin:vue/vue3-recommended",
        "eslint:recommended",
        "@vue/typescript/recommended",
        "prettier"
    ],
    "parserOptions": {
        "ecmaVersion": 2021
    },
    "plugins": [],
    "rules": {
        "vue/multi-word-component-names": "off",
        "@typescript-eslint/no-explicit-any": 0
    },
    //Eslintが反応してしまう為、グローバル変数として定義
    "globals": {
        "defineProps": "readonly",
        "defineEmits": "readonly",
        "defineExpose": "readonly",
        "withDefaults": "readonly"
    }
}

```

### settings.json 設定例

```console
{
 "[vue]": {
    "editor.formatOnSave": true,
    "editor.defaultFormatter": "esbenp.prettier-vscode"
  },
 "[markdown]": {
    "editor.formatOnSave": true,
    "editor.defaultFormatter": "esbenp.prettier-vscode"
  },
 "[typescript]": {
    "editor.formatOnSave": true,
    "editor.defaultFormatter": "esbenp.prettier-vscode"
  },
  "auto-close-tag.activationOnLanguage": [
    "xml",
    "php",
    "blade",
    "ejs",
    "jinja",
    "javascript",
    "javascriptreact",
    "typescript",
    "typescriptreact",
    "plaintext",
    "markdown",
    // "vue",
    "liquid",
    "erb",
    "lang-cfml",
    "cfml",
    "HTML (EEx)",
    "HTML (Eex)",
    "plist"
  ]
}
```

### launchjson 設定例 (ほぼ自動生成されるので参考までに)

```console
{
  // IntelliSense を使用して利用可能な属性を学べます。
  // 既存の属性の説明をホバーして表示します。
  // 詳細情報は次を確認してください: https://go.microsoft.com/fwlink/?linkid=830387
  "version": "0.2.0",
  "configurations": [
    {
      "name": "Listen for Xdebug",
      "type": "php",
      "request": "launch",
      "port": 9003,
      "pathMappings": {
          "/var/www/html": "${workspaceFolder}"
      },
    },
    {
      "name": "Launch currently open script",
      "type": "php",
      "request": "launch",
      "program": "${file}",
      "cwd": "${fileDirname}",
      "port": 0,
      "runtimeArgs": [
        "-dxdebug.start_with_request=yes"
      ],
      "env": {
        "XDEBUG_MODE": "debug,develop",
        "XDEBUG_CONFIG": "client_port=${port}"
      }
    },
    {
      "name": "Launch Built-in web server",
      "type": "php",
      "request": "launch",
      "runtimeArgs": [
        "-dxdebug.mode=debug",
        "-dxdebug.start_with_request=yes",
        "-S",
        "localhost:0"
      ],
      "program": "",
      "cwd": "${workspaceRoot}",
      "port": 9003,
      "serverReadyAction": {
        "pattern": "Development Server \\(http://localhost:([0-9]+)\\) started",
        "uriFormat": "http://localhost:%s",
        "action": "openExternally"
      }
    }
  ]
}
```

### extension.json 設定例

```console
{
  "recommendations": [
    "vue.volar",
    "esbenp.prettier-vscode",
    "dbaeumer.vscode-eslint"
  ]
}
```

## ステージングリリース

- review ブランチを最新にする

- npm run stg を実行

- sync_excludes に記述されている rsync コマンドで最新ソースをアップロード

- 動作確認
