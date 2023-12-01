<script lang="ts" setup>
import axios from 'axios';
import { ref, onMounted, onUnmounted } from 'vue';
import { useToast } from 'vue-toastification';
import useAxios from '/@/js/compositions/UseAxios';
interface Props {
  modalOpen: boolean;
  moviePath: string;
}
const props = defineProps<Props>();
interface Emits {
  (e: 'close'): void;
}
const emits = defineEmits<Emits>();
const toast = useToast();
const { asyncMethodLock, axiosErrorHandling, errorToast } = useAxios();
const videoVisible = ref<boolean>(false);

/** uliza method */

// ulizaPlayerオブジェクトの存在確認
const hasUliza = () => {
  return window.ulizaPlayer && 'dispose' in window.ulizaPlayer;
};
// ulizaPlayerオブジェクトを破棄
const close = () => {
  if (hasUliza()) {
    window.ulizaPlayer?.dispose();
  }
  document.getElementById('moviePlayerScript')?.remove();
  emits('close');
};
// ulizaPlayerが入るDOMの現在のwidthを取得
const getWidth = () => {
  const dom = document.getElementsByClassName('modal_inner')[0];
  return dom.clientWidth;
};
// リサイズイベント発火時にブラウザのサイズに合わせてulizaPlayerのwidthを設定する
const resizePlayer = () => {
  window.ulizaPlayer?.setWidth(getWidth());
};
// 数秒待つ関数
const sleep = (ms: number) => {
  return new Promise((resolve) => setTimeout(resolve, ms));
};
// ulizaPlayer APIからjavascriptソースを取得
const playerCreate = async () => {
  await asyncMethodLock(async () => {
    await axios
      .get(`/api/media/movie/status/${props.moviePath}`)
      .then(async () => {
        let count = 0;
        let elScript = document.createElement('script');
        elScript.type = 'text/javascript';
        elScript.src = `/api/media/movie/script/${props.moviePath}`;
        elScript.id = 'moviePlayerScript';
        elScript.defer = true;
        document.getElementsByTagName('head')[0].appendChild(elScript);
        // ulizaPlayerがmountする、もしくは5秒たつまで待機
        while (!hasUliza()) {
          await sleep(1000); // 1秒待機
          count++;
          if (count >= 5) {
            // 5秒待ってもmountされない場合はエラー表示して処理終了
            toast.error(
              'プレイヤーの初期化に失敗しました。\n画面を開き直してください。'
            );
            break;
          }
        }
        videoVisible.value = true;
        resizePlayer();
      })
      .catch((err) => {
        const result = axiosErrorHandling(err);
        errorToast(result.code, result.message);
      });
  });
};

onMounted(() => {
  playerCreate();
  window.addEventListener('resize', resizePlayer);
});
onUnmounted(() => {
  window.removeEventListener('resize', resizePlayer);
});
</script>

<template>
  <section
    v-if="modalOpen"
    id="modalArea"
    :class="{ modalArea: true, openModal: modalOpen, closeModal: !modalOpen }"
  >
    <div id="modalBg" class="modalBg" @click="close" />
    <div class="modalWrapper bg-cyan-50 border-cyan-500 border-4">
      <fa
        icon="fa-xmark"
        class="cursor-pointer font-semibold text-gray-400 hover:text-gray-600 absolute top-2 right-4 text-3xl"
        @click="close"
      />
      <div class="modalContents">
        <div class="modal_inner">
          <div v-show="!videoVisible" id="spinner" class="flex justify-center">
            <div
              class="animate-spin h-10 w-10 border-4 border-gray-400 rounded-full border-t-transparent"
            ></div>
          </div>
          <div v-show="videoVisible" id="videoArea"></div>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.openModal {
  transition: 0.5s;
  opacity: 1;
}
.closeModal {
  transition: 0.5s;
  opacity: 0;
}
.preLine {
  white-space: pre-wrap;
}

.modalArea {
  display: block;
  position: fixed;
  z-index: 200;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: #00000099;
}

.modalBg {
  width: 100%;
  height: 100%;
  background-color: rgba(30, 30, 30, 0);
}

.modalWrapper {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 80%;
  padding: 40px 5%;
  height: auto;
  overflow: auto;
  box-sizing: inherit;
}

.closeModal.cancel_top {
  position: absolute;
  top: 0.5rem;
  right: 1rem;
  cursor: pointer;
}

.cancel_top .cancel {
  padding: 10px 20px;
  color: #fff;
  font-weight: bold;
  letter-spacing: 5px;
  margin: 0 0 0 0;
  font-size: 100%;
}

.modalimport {
  width: 95% !important;
  max-width: 100% !important;
}

/* モーダル内コンテンツ */

.h1_wrap i {
  display: block;
  font-size: 250%;
  margin: 0 0 15px 0;
}

.modalWrapper .modalContents .h1_wrap h1::before {
  background: transparent;
}

.fa-exclamation-triangle,
.fa-file-upload {
  color: #f05353;
}

.fa-check-circle {
  color: #80b74f;
}

.modalWrapper .fa-download {
  color: #0087ff;
}

.modalContents h1 {
  text-align: center;
  font-size: 115%;
  border-bottom: 2px dashed #9ab583;
  display: inline-block;
  margin: 0 auto;
}

.modal_inner {
  margin: 30px 0 0 0;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.update_wrap .search_submit {
  width: 100%;
}

.update_wrap .search_submit:last-child {
  margin: 20px 3% 0 0;
}

.h1_wrap {
  text-align: center;
  padding: 0 15px;
}

.search_submit {
  padding: 10px 30px;
  color: #fff;
  font-weight: bold;
  width: 150px;
  letter-spacing: 5px;
  margin: 0 3% 0 0;
  font-size: 100%;
}

.search_submit:last-child {
  margin: 0;
}

.modal_btn_gray {
  background-color: #bdbdbd;
}
.modal_btn_orange {
  background-color: #ff8902;
}
@media screen and (max-width: 959px) {
  .modalWrapper {
    width: 85%;
    max-width: 100%;
  }

  .modalWrapper {
    padding: 40px 3%;
  }

  .modal_inner button {
    width: 150px !important;
  }
}
</style>
