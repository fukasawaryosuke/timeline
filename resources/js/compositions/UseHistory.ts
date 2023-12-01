import { ref, reactive, onMounted, Ref } from 'vue';
import axios from 'axios';
import { HistoryModel, HistoryListModel } from '../models/History';

const useHistory = () => {
  const loadingFlag = ref(true);
  const infiniteId = ref<number>(0);
  const nextPage = ref<string>('/api/history/showHistory');
  const spNextPage = ref<string>('/api/history/spShowHistory');
  const historyList = reactive<HistoryListModel>(new HistoryListModel());

  const total = ref(0);

  onMounted(() => {
    infiniteId.value = infiniteId.value + 1;
  });

  const historyLoadData = (diaryId: number, page: Ref<string>) => {
    loadingFlag.value = true;
    let searchParam = {} as any;
    searchParam.diary_id = diaryId;
    axios
      .get(page.value, { params: searchParam })
      .then((res) => {
        let data = res.data.data;
        if (!data) {
          loadingFlag.value = false;
          return;
        }
        total.value = res.data.total;
        page.value = res.data.next_page_url;
        Object.keys(data).forEach((k) => {
          historyList.contents.push(new HistoryModel(data[k]));
        });

        if (res.data.next_page_url === null) {
          loadingFlag.value = false;
        }
      })
      .catch(() => {
        loadingFlag.value = false;
      });
  };
  const loadData = (diaryId: number) => {
    historyLoadData(diaryId, nextPage);
  };
  const spLoadData = (diaryId: number) => {
    historyLoadData(diaryId, spNextPage);
  };
  return {
    loadingFlag,
    infiniteId,
    nextPage,
    historyList,
    total,
    loadData,
    spLoadData,
  };
};

export default useHistory;
