<template>
  <section
    v-if="orgzModalOpen"
    class="modalArea"
    :class="{
      openModal: orgzModalOpen,
      closeModal: !orgzModalOpen,
    }"
  >
    <div id="modalBg" class="modalBg" @click="$emit('close')"></div>
    <div
      class="modalWrapper px-8 bg-white border rounded-lg border-gray-400 w-176"
    >
      <fa
        icon="fa-xmark"
        class="cursor-pointer font-semibold text-gray-400 hover:text-gray-600 absolute top-2 right-4 text-3xl"
        @click="$emit('close')"
      />
      <div class="modalContents">
        <div
          class="mb-1 pt-2 text-slate-600 border-b-gray-300 text-xl font-semibold"
        >
          部門を選択
        </div>
        <div
          class="modalInner py-2 max-h-[calc(100vh-20rem)] overflow-y-scroll"
        >
          <OrganizationTree
            :orgz-tree-list="orgzTreeList"
            :changed="divisionCodeChanged"
          />
        </div>
        <div class="py-2 flex justify-center">
          <div
            class="w-40 py-2 bg-assist-grad rounded-full text-cyan-50 cursor-pointer text-center"
            @click="filterWithDivisionCd"
          >
            選択
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
<script lang="ts" setup>
import { ref } from 'vue';
import { OrganizationTreeListModel } from '/@/js/models/Organization';
import OrganizationTree from '/@/js/components/home/organization/OrganizationTree.vue';
interface Props {
  orgzTreeList: OrganizationTreeListModel;
  orgzModalOpen: boolean;
  updateDivisionCode: (cds: number[]) => void;
  updateHomeMode: (flg: boolean) => void;
}
const props = defineProps<Props>();

interface Emits {
  (e: 'close'): void;
}
const emits = defineEmits<Emits>();

const selectedDivisionCode = ref<number>(0);
const divisionCodeChanged = (division_cd: number) => {
  selectedDivisionCode.value = division_cd;
};
const filterWithDivisionCd = () => {
  props.updateHomeMode(false);
  if (selectedDivisionCode.value) {
    props.updateDivisionCode([selectedDivisionCode.value]);
  } else {
    props.updateDivisionCode([]);
  }
  emits('close');
};
</script>
<style scoped>
.openModal {
  transition: 0.5s;
  opacity: 1;
}
.closeModal {
  transition: 0.5s;
  opacity: 0;
}
.modalArea {
  display: block;
  position: fixed;
  z-index: 150;
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
  min-width: 750px;
  /* max-width: 75%; */
  /* padding: 40px 5%; */
  height: auto;
  overflow: auto;
  box-sizing: inherit;
}
</style>
