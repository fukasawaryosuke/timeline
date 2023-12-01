# hat(timeline) project

##　フロントコーディング規約
・変数、関数ともにキャメルケース

・vueファイルは上から script, template, 必要であればstyle の順で記述

・コメントを入れる場合はなんのメソッドなのか最低限いれること
・$a = 555; $aが何故555である必要があるか主観的ではなく客観的に見て判断できない場合はコメントを入れる（例：お客様指定の数値 等）

・コンポーネントは最大３階層までで、ビジネスロジックは親コンポーネント、もしくはcomposition functionに記述

・CSSは基本styleタグ内に記述、少ない簡単なスタイルをあてる場合はTailwind.cssの使用も可（５個以上のクラスになる場合はstyleタグに記述）
[Tailwind.cssチートシート](https://nerdcave.com/tailwind-cheat-sheet)