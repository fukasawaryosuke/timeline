import { IUlizaPlayerFunctions } from '/@/js/models/Uliza';

declare global {
  interface Window {
    // NOTE: ULIZA Playerのオブジェクト名を変更する際はUlizaPlayerService.phpの$playerObjectNameも変更すること
    ulizaPlayer?: IUlizaPlayerFunctions; // ULIZA Player (HTML5)オブジェクト
  }
}
