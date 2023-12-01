import { ref, reactive } from 'vue';
import { NoticeListModel } from '../models/Notice';
import axios from 'axios';

const useNotice = () => {
  const noticeList = reactive<NoticeListModel>(new NoticeListModel());

  const getNotice = () => {
    axios
      .get('/api/comment/showNotice')
      .then((res) => {
        noticeList.init(res.data);
      })
      .catch((err) => {});
  };

  return {
    noticeList,
    getNotice,
  };
};

export default useNotice;
