/**
 * axios composition
 */
import { ref } from 'vue';
import axios, { AxiosError } from 'axios';
import { useToast } from 'vue-toastification';

// axiosエラー内容を返却するレスポンスの型
export type AxiosErrorType = {
  code: number;
  message: string;
};

const useAxios = () => {
  const toast = useToast();
  /** state */
  const methodProcess = ref<boolean>(false);

  /** method */
  /**
   * axiosエラーのステータスコードとメッセージを取得
   * @param {AxiosError} err axiosのcatchで取得したエラーオブジェクト
   */
  const axiosErrorHandling = (err: AxiosError): AxiosErrorType => {
    // const logger = new Logger(); // TODO: できればフロントエラー用ロギングAPI作成
    let errMessage = '';
    let statusCode = 500;
    if (axios.isAxiosError(err) && err?.response) {
      // CSRFエラー
      if (err.response.status === 403) {
        statusCode = 403;
        errMessage = 'CSRFの値が不正です。画面を更新して再送信してください.';
      } else {
        statusCode = err.response.status;
        errMessage = err.response.data.message;
      }
    } else {
      // フロントエラー
      errMessage = err.message;
      // axios以外のエラーだったらロギング
      // logger.error(csrf, errMessage, err);
    }
    const result: AxiosErrorType = {
      code: statusCode,
      message: errMessage,
    };
    return result;
  };
  /**
   * 多重submit防止メソッド
   * @param {Function} callback 実行させたいメソッド
   */
  const asyncMethodLock = async (callback: () => void) => {
    if (methodProcess.value) return false;
    methodProcess.value = true;
    await callback();
    methodProcess.value = false;
  };
  /**
   * エラートースト表示
   * @param {Number} code ステータスコード
   * @param {String} message トーストに表示させるメッセージ
   */
  const successToast = (code: number, message: string) => {
    toast.success(`${String(code)}: ${message}`);
  };
  /**
   * エラートースト表示
   * @param {Number} code ステータスコード
   * @param {String} message トーストに表示させるメッセージ
   */
  const errorToast = (code: number, message: string) => {
    toast.error(`${String(code)}: ${message}`);
  };

  /** private state */
  /** private method */

  return {
    axiosErrorHandling,
    asyncMethodLock,
    successToast,
    errorToast,
  };
};

export default useAxios;
