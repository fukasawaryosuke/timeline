<script lang="ts" setup>
interface Props {
  modalOpen: boolean;
  imagePath: string;
}
defineProps<Props>();
interface Emits {
  (e: 'close'): void;
}
defineEmits<Emits>();
</script>

<template>
  <section
    v-if="modalOpen"
    id="modalArea"
    :class="{ modalArea: true, openModal: modalOpen, closeModal: !modalOpen }"
  >
    <div id="modalBg" class="modalBg" @click="$emit('close')" />
    <div class="modalWrapper">
      <fa class="close_modal" icon="fa-xmark" @click="$emit('close')" />
      <div class="modalContents">
        <div class="modal_inner">
          <img :src="imagePath" />
          <div class="btn_area alert_btn" />
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
  z-index: 9001;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: #00000099;
  -webkit-overflow-scrolling: auto;
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
  width: 98%;
  padding: 40px 1%;
  max-height: 80%;
  overflow: auto;
  box-sizing: inherit;
  background-color: #fff;
  border: 1px solid rgb(6 182 212);
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
.close_modal {
  display: flex;
  height: 25px;
  vertical-align: -0.125em;
  position: absolute;
  top: 7px;
  right: 13px;
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
</style>
