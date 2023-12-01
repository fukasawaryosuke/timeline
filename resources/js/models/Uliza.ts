/**
 * UlizaPlayerオブジェクト
 *
 * script読み込み時にULIZA Player (HTML5) をwindowオブジェクトに追加される
 * その追加されたオブジェクトの定義
 * 必要な機能はその都度追記する形で対応すること
 *
 * https://docs.p.uliza.jp/uliza-player/uliza-player-html5/latest/api-reference/pages/02_ulizaplayer.html#isconnectingcast
 * https://docs.p.uliza.jp/uliza-player/uliza-player-cloud/latest/api-reference/#section/Authentication
 */

// UlizaPlayerのメソッド
export interface IUlizaPlayerFunctions {
  // イベントが発生した時のコールバックを設定
  on: (event: string, callback: () => void) => void;
  // on で設定したコールバックを削除
  off: (event: string, callback: () => void) => void;
  // コンテンツの現在の再生位置を取得
  getCurrentTime: () => number;
  // コンテンツの指定の再生位置にジャンプ(設定)
  setCurrentTime: (time: number) => void;
  // ジェスチャー用パラメータを設定
  setGestureControl: (params: IGestureControl) => boolean;
  // UlizaPlayerのインスタンスを破棄
  dispose: () => void;
  // プレイヤーの幅を設定
  setWidth: (value: number | string) => boolean;
  // プレイヤーの高さを設定
  setHeight: (value: number | string) => boolean;
  // フルスクリーン状態情報
  fullscreen: IFullscreen;
}

// ジェスチャー用設定
export interface IGestureControl {
  enable: boolean;
  enablePageScroll: boolean;
  eventListener: () => string;
  flickInterval: number;
  multiPressInterval: number;
}
// フルスクリーン設定
export interface IFullscreen {
  isFullscreen_: boolean;
}
