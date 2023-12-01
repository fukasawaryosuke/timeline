import { ref } from 'vue';
import axios from 'axios';
import { User } from '../models/User';

const useReport = () => {
  const ihsLSKey = 'humanAssistTimeLineIhs';
  const indexdata = ref<never | any>([]);

  const nextPage = ref<string>('/api/diary/showDairy');

  const loadingFlag = ref<boolean>(true);

  const mentionFlags = ref<boolean[]>([]);

  const mentionUsers = ref<User[][]>([]);

  const browsingHistories = ref<boolean[]>([]);

  const createdBy = ref<number>(0);
  const ihsStorageData = JSON.parse(localStorage.getItem(ihsLSKey) || '{}');
  const selectedIhsId = ref<number>(Number(ihsStorageData.id ?? 0));
  const systemName = ref<string>(ihsStorageData.name ?? '');

  const diaryComments = ref<string[]>([]);

  const initLoading = () => {
    indexdata.value.length = 0;
    mentionFlags.value.length = 0;
    mentionUsers.value.length = 0;
    nextPage.value = '/api/diary/showDairy';
    loadingFlag.value = true;
    createdBy.value = 0;
  };

  const loadData = (
    isHome: boolean,
    userId: number,
    divisionCodes: number[],
    searchDate: string
  ) => {
    loadingFlag.value = true;

    const searchParam = {} as any;

    // 制度の指定
    if (selectedIhsId.value) {
      searchParam.ihs = selectedIhsId.value;
    }
    // 事業部の指定
    if (divisionCodes.length > 0) {
      searchParam.division_cd = divisionCodes;
    }
    // アカウントの指定
    if (isHome || createdBy.value) {
      // ホームを選択している or 日報作成者をフィルターしている
      // アカウントが投稿した日報を抽出
      searchParam.created_by = userId;
    }

    // if (selectedIhsId.value) {
    //   searchParam.ihs = selectedIhsId.value;
    //   if (divisionCodes.length > 0) {
    //     console.log(divisionCodes);
    //     // 事業部を選択した
    //     // 事業部内の日報を抽出
    //     searchParam.division_cd = divisionCodes;
    //   }
    // } else if (isHome || createdBy.value) {
    //   // ホームを選択している or 日報作成者をフィルターしている
    //   // アカウントが投稿した日報を抽出
    //   searchParam.created_by = userId;
    // } else if (divisionCodes.length > 0) {
    //   // 事業部を選択した
    //   // 事業部内の日報を抽出
    //   searchParam.division_cd = divisionCodes;
    // } else {
    //   // 検索条件が見つからない
    //   // 処理を抜ける
    //   loadingFlag.value = false;
    //   return;
    // }
    // 投稿日の指定
    searchParam.search_date = searchDate;

    axios
      .get(nextPage.value, {
        params: searchParam,
      })
      .then((res) => {
        const data = res.data.data;
        if (!data) {
          loadingFlag.value = false;
          return;
        }
        nextPage.value = res.data.next_page_url;

        // indexdata.value = data;
        const registHistoryIds = [] as number[];
        for (let i = 0; i < data.length; i++) {
          indexdata.value.push(data[i] as never);
          mentionFlags.value[data[i].id] = false;
          mentionUsers.value[data[i].id] = [] as User[];
          browsingHistories.value[data[i].id] = false;
          registHistoryIds.push(data[i].id);
        }
        historyRegist(registHistoryIds);

        if (res.data.next_page_url === null) {
          loadingFlag.value = false;
        }
      });
  };

  const toggleHistory = (id: number) => {
    browsingHistories.value[id] = !browsingHistories.value[id];
  };

  const closeHistory = (id: number) => {
    browsingHistories.value[id] = false;
  };

  const historyRegist = (ids: number[]) => {
    axios.post('/api/history/registHistory', {
      diary_ids: ids,
    });
  };

  const deleteDiary = (index: number) => {
    indexdata.value.splice(index, 1);
  };

  const selectedUserOnlyDiary = (createdUserId: number) => {
    initLoading();
    createdBy.value = createdUserId;
  };

  const updateIhs = (id: number, name: string) => {
    initLoading();
    selectedIhsId.value = id;
    systemName.value = name;
    const ihs = {
      id: id,
      name: name,
    };
    localStorage.setItem(ihsLSKey, JSON.stringify(ihs));
  };
  const resetIhs = () => {
    selectedIhsId.value = 0;
    systemName.value = '';
    resetLocalStorageIhs();
    initLoading();
  };
  const resetSystemName = () => {
    systemName.value = 'assistタイムライン';
  };
  const resetLocalStorageIhs = () => {
    localStorage.removeItem(ihsLSKey);
  };

  const commentReply = (diaryId: number, targetComment: any) => {
    mentionUsers.value[diaryId].push(targetComment.user);
    const baseCom = targetComment.comment.replace(/\r?\n/g, '\n');
    let repCom =
      '> ' +
      targetComment.user.user_name +
      ' ' +
      targetComment.created_at +
      '\n';
    repCom += baseCom
      .split('\n')
      .map((l) => {
        return '> ' + l;
      })
      .join('\n');
    repCom += '\n';

    diaryComments.value[diaryId] = repCom;
  };

  return {
    indexdata,
    loadingFlag,
    mentionFlags,
    mentionUsers,
    browsingHistories,
    createdBy,
    selectedIhsId,
    systemName,
    diaryComments,
    resetSystemName,
    loadData,
    initLoading,
    toggleHistory,
    closeHistory,
    deleteDiary,
    selectedUserOnlyDiary,
    updateIhs,
    resetIhs,
    resetLocalStorageIhs,
    commentReply,
  };
};

export default useReport;
