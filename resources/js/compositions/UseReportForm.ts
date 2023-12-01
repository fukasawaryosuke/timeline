/**
 * report form composition
 */
import { ref, reactive, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useToast } from 'vue-toastification';
import axios from 'axios';
import useAxios from '/@/js/compositions/UseAxios';
import { REPORT_CONTENT_TYPE } from '/@/js/contarcts/ReportContentType';
import ReportForm, {
  IReportForm,
  ReportContentsItem,
  IReportContentsItem,
} from '/@/js/models/admin/ReportForm';

const useReportForm = (data: IReportForm) => {
  /** composition */
  const router = useRouter();
  const toast = useToast();
  const { asyncMethodLock, axiosErrorHandling, successToast, errorToast } =
    useAxios();
  /** state */
  const REPORT_FORM_TYPE = {
    EDIT: 0,
    PREVIEW: 1,
    CONFIRM: 2,
  };
  const mode = ref<number>(0);
  // フォーム作成・更新用データ
  const formData = ref<IReportForm>(data);
  const dummyData = ref<IReportForm>(data);

  const newContents = ref<IReportContentsItem>(
    ReportContentsItem.getInstance(0)
  );
  /** method */
  // フォーム名更新
  const updateName = (event: any) => {
    formData.value.name = event.target.value;
  };
  // 必読設定更新
  const updateRequired = (event: any) => {
    formData.value.required = event.target.checked;
  };
  // コンテンツタイプ更新
  const updateContentsType = (event: any, index: number) => {
    if (
      event.target.value !== ReportForm.TYPE_CHOICE &&
      event.target.value !== ReportForm.TYPE_SELECT
    ) {
      formData.value.diary.contents[index].reset();
    }
    formData.value.diary.contents[index].contentType = event.target.value;
  };
  // コンテンツのタイトル更新
  const updateTitle = (event: any, index: number) => {
    formData.value.diary.contents[index].title = event.target.value;
  };
  // 必須項目設定更新
  const updateContentsRequired = (event: any, index: number) => {
    formData.value.diary.contents[index].require = event.target.checked;
  };
  // レポート表示項目設定更新
  const updateContentsReportVisible = (event: any, index: number) => {
    formData.value.diary.contents[index].reportVisible = event.target.checked;
  };
  // コンテンツ追加
  const addContents = () => {
    if (!newContents.value.title || !newContents.value.title.trim()) {
      toast.error('項目名が入力されていません');
      return false;
    }
    if (
      !newContents.value.contentType ||
      !newContents.value.contentType.trim()
    ) {
      toast.error('フォーム種類が選択されていません');
      return false;
    }
    if (!newContents.value.hasSelected()) {
      toast.error('選択肢は最低1個設定してください');
      return false;
    }
    formData.value.diary.addContents(newContents.value);
    newContents.value = ReportContentsItem.getInstance(0);
  };
  // コンテンツ削除
  const deleteContents = (index: number) => {
    formData.value.diary.deleteContents(index);
  };
  // 選択時追加
  const addChoice = (index: number) => {
    formData.value.diary.contents[index].addChoice();
  };
  // 選択肢更新
  const updateChoice = (index: number, choiceIndex: number, value: string) => {
    formData.value.diary.contents[index].updateChoice(choiceIndex, value);
  };
  // 選択肢更新
  const deleteChoice = (index: number, choiceIndex: number) => {
    formData.value.diary.contents[index].deleteChoice(choiceIndex);
  };
  // 確認画面へ遷移する前に入力バリデーションを行う
  const confirmForm = () => {
    const [result, message] = validationForm();
    if (result) {
      errorToast(401, message);
      return false;
    }
    mode.value = REPORT_FORM_TYPE.CONFIRM;
  };
  // プレビュー画面表示
  const previewMode = (e) => {
    mode.value = e.target.checked
      ? REPORT_FORM_TYPE.PREVIEW
      : REPORT_FORM_TYPE.EDIT;
  };
  // 作成・編集画面に戻る
  const returnEditForm = () => {
    mode.value = REPORT_FORM_TYPE.EDIT;
  };
  /** async method */
  // フォーム情報取得
  const getForm = async (form_id): Promise<[IReportForm | null, string]> => {
    let reportForm: IReportForm | null = null;
    let firstForm = '';
    await asyncMethodLock(async () => {
      await axios
        .get(`/api/reportForm/${form_id}`)
        .then((res) => {
          reportForm = new ReportForm(res.data.data.form);
          firstForm = res.data.data.first_report_form;
        })
        .catch((err) => {
          const result = axiosErrorHandling(err);
          errorToast(result.code, result.message);
          reportForm = null;
        });
    });
    return [reportForm, firstForm];
  };
  // 作成 or 更新メソッド設定
  const createOrUpdate = (edit: boolean, id?: string) => {
    const [result, message] = validationForm();
    if (result) {
      errorToast(401, message);
      return false;
    }
    return edit ? updateForm(id) : createForm();
  };
  // 報告フォーム削除
  const deleteForm = async (id?: string) => {
    await asyncMethodLock(async () => {
      await axios
        .delete(`/api/reportForm/${id}`)
        .then(() => {
          successToast(200, '報告フォームの削除に成功しました。');
          gotoReportFormList();
        })
        .catch((err) => {
          const result = axiosErrorHandling(err);
          errorToast(result.code, result.message);
        });
    });
  };
  /** computed */
  const modeStr = computed(() => (edit: boolean) => {
    return edit ? '更新' : '作成';
  });

  /** private state */
  /** private method */
  // 報告フォームのバリデーション
  const validationForm = (): [boolean, string] => {
    let hasError = false;
    const message: string[] = [];
    // 投稿フォーム名
    if (!formData.value.name || formData.value.name.trim() === '') {
      hasError = true;
      message.push('投稿フォーム名が入力されていません。');
    }
    // コンテンツを一つも登録していない
    if (!formData.value.diary.hasContents()) hasError = true;
    // コンテンツ
    formData.value.diary.contents.forEach((c, i) => {
      const index = String(i + 1);
      // タイトルがない
      if (!c.title || c.title.trim() === '') {
        hasError = true;
        message.push(`${index}番目の項目名が入力されていません。`);
      }
      // タイトル重複
      if (formData.value.diary.hasTitle(c.title)) {
        hasError = true;
        message.push(`${index}番目の項目名が重複しています。`);
      }
      // contentTypeを選んでいない
      if (c.contentType === '') {
        hasError = true;
        message.push(`${index}番目のフォーム種類が選択されていません。`);
      }
      // 単一・複数選択かつ選択肢が一つも入力されていない
      if (!c.hasSelected()) {
        hasError = true;
        message.push(`${index}番目の選択肢を1つ以上登録してください。`);
      }
    });
    const errorMessage = message.join('\n');
    return [hasError, errorMessage];
  };
  // 報告フォーム作成
  const createForm = async () => {
    await asyncMethodLock(async () => {
      await axios
        .post('/api/reportForm/create', formData.value)
        .then(() => {
          successToast(200, '報告フォームの作成に成功しました。');
          gotoReportFormList();
        })
        .catch((err) => {
          const result = axiosErrorHandling(err);
          errorToast(result.code, result.message);
        });
    });
  };
  // 報告フォーム更新
  const updateForm = async (id?: string) => {
    await asyncMethodLock(async () => {
      await axios
        .put(`/api/reportForm/${id}`, formData.value)
        .then(() => {
          successToast(200, '報告フォームの更新に成功しました。');
          gotoReportFormList();
        })
        .catch((err) => {
          const result = axiosErrorHandling(err);
          errorToast(result.code, result.message);
        });
    });
  };
  // 報告フォーム一覧へ戻る
  const gotoReportFormList = () => {
    router.push({
      name: 'ReportFormList',
    });
  };

  return {
    REPORT_FORM_TYPE,
    mode,
    formData,
    dummyData,
    updateName,
    updateRequired,
    updateContentsType,
    updateContentsRequired,
    updateContentsReportVisible,
    updateTitle,
    addContents,
    confirmForm,
    previewMode,
    returnEditForm,
    getForm,
    createOrUpdate,
    deleteContents,
    deleteForm,
    modeStr,
    addChoice,
    updateChoice,
    deleteChoice,
    newContents,
  };
};

export default useReportForm;
