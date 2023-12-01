<script lang="ts" setup>
interface Props {
  isOpen: boolean;
  message?: string;
  subMessage?: string;
  confirmLabel: string;
}

const props = defineProps<Props>();
const emit = defineEmits(['close_modal', 'confirm']);

const closeModal = () => {
  emit('close_modal');
};

const confirm = () => {
  emit('confirm', true);
};
</script>

<template>
  <section
    v-if="props.isOpen"
    id="modalArea"
    :class="{ modalArea: true, openModal: isOpen, closeModal: !isOpen }"
  >
    <div id="modalBg" class="modalBg" @click="closeModal" />
    <div class="modalWrapper">
      <fa icon="fa-xmark" class="cursor-pointer" @click="closeModal" />
      <div class="modalContents">
        <div class="h1_wrap">
          <h1 class="preLine">{{ props.message }}</h1>
          <p class="sub_message">{{ props.subMessage }}</p>
        </div>
        <div class="modal_inner">
          <button class="search_submit modal_btn_gray" @click="closeModal">
            キャンセル
          </button>
          <button class="search_submit" @click="confirm">
            {{ confirmLabel }}
          </button>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
h1 {
  font-weight: inherit;
}
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
  z-index: 9001;
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
  width: 50%;
  padding: 40px 5%;
  height: auto;
  overflow: auto;
  box-sizing: inherit;
  background-color: rgb(236 254 255);
  border: solid;
  border-color: rgb(6 182 212);
  border-width: 4px;
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
  /* font-style: inherit; */
  border-bottom: none;
  display: inline-block;
  margin: 0 auto;
}

.modalContents .sub_message {
  color: #f00;
  font-weight: bold;
}

.modal_inner {
  margin: 30px 0 0 0;
  display: flex;
  justify-content: space-around;
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
  padding: 10px 20px;
  color: #fff;
  font-weight: bold;
  width: 150px;
  letter-spacing: 5px;
  font-size: 100%;
  background-color: rgb(6 182 212);
  border-radius: 6px;
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

.cursor-pointer {
  cursor: pointer;
  font-style: normal;
  color: rgb(156 163 175);
  position: absolute;
  top: 8px;
  right: 16px;
  text-align: center;
  font-size: 30px;
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
