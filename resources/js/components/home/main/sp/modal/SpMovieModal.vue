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
  window.ulizaPlayer?.setHeight(window.innerHeight - 100);
};
const rotatePlayer = () => {
  setTimeout(() => {
    const target3 = document.getElementById('modal_inner') as HTMLElement;
    const target4 = document.getElementById('videoArea') as HTMLElement;
    if (window.ulizaPlayer?.fullscreen.isFullscreen_ && target3 && target4) {
      target3.style.width = '100%';
      target4.style.width = '100%';
    }
  }, 1000);
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
  window.addEventListener('orientationchange', rotatePlayer);
});
onUnmounted(() => {
  window.removeEventListener('resize', resizePlayer);
  window.removeEventListener('orientationchange', rotatePlayer);
});
</script>

<template>
  <section
    v-if="modalOpen"
    id="modalArea"
    :class="{ modalArea: true, openModal: modalOpen, closeModal: !modalOpen }"
  >
    <div id="modalBg" class="modalBg" @click="close" />
    <div id="modalWrapper" class="modalWrapper">
      <fa icon="fa-xmark" class="closeBtn" @click="close" />
      <div id="modalContents" class="modalContents">
        <div id="modal_inner" class="modal_inner">
          <div v-show="!videoVisible" id="spinner">
            <div class="spiner"></div>
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
  width: 100%;
  height: 100%;
  padding: 40px 5%;
  overflow: auto;
  box-sizing: inherit;
  background-color: rgb(236 254 255);
  /* border: 4px solid rgb(6 182 212); */
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
.modalWrapper {
  padding: 40px 3%;
}

.modal_inner button {
  width: 150px !important;
}

.closeBtn {
  color: rgb(156 163 175);
  font-weight: 600;
  position: absolute;
  /* top: 0; */
  right: 1rem;
  font-size: 1.875rem;
  line-height: 2.25rem;
}
#spinner {
  display: flex;
  justify-content: center;
}
.spiner {
  width: 2.5rem;
  height: 2.5rem;
  margin: 10px auto;
  border: 4px rgb(156 163 175) solid;
  border-top: 4px transparent solid;
  border-radius: 50%;
  -webkit-animation: sp-anime 1s linear infinite;
  animation: sp-anime 1s infinite linear;
}
@keyframes sp-anime {
  100% {
    transform: rotate(360deg);
  }
}
</style>
