<template>
  <div
    v-for="(require, idx) in requireMessage"
    :key="idx"
    class="w-full items-center flex justify-center"
  >
    <div class="w-192 rounded-lg justify-center text-xs text-red-500">
      {{ require }}
    </div>
  </div>
  <div class="w-full items-center flex justify-center">
    <div
      class="overflow-y-auto w-192 border rounded-lg justify-center bg-white"
    >
      <div class="h-10 flex border-b border-solid">
        <fa
          v-if="reportPageMode == MODE.EDIT"
          icon="fa-clipboard"
          class="pl-4 self-center text-slate-300"
        />
        <div
          class="self-center mx-3 text-sm font-bold text-slate-700 flex justify-between w-full"
        >
          <span v-if="reportPageMode == MODE.EDIT" class="py-2">共有内容</span>
          <span v-if="reportPageMode == MODE.CONFIRM" class="py-2">
            入力内容をご確認ください。
          </span>
          <button
            v-if="reportPageMode == MODE.EDIT"
            class="border p-1 rounded-md text-gray-400 hover:text-gray-600 bg-white border-slate-300"
            @click="$emit('getLastReport')"
          >
            <fa icon="fa-clock-rotate-left" class="mr-1" />
            前回の投稿
          </button>
        </div>
      </div>
      <ul class="">
        <li v-if="requireNotice" class="flex py-4 border-b border-solid">
          <div class="flex mx-3.5 w-32">
            <fa icon="fa-clipboard" class="self-center text-slate-300" />
            <div class="ml-3 self-center text-slate-600 text-sm">必読投稿</div>
          </div>
          <div class="w-[calc(100%_-_230px)]">
            <input
              v-if="reportPageMode == MODE.EDIT"
              :value="isRequireNotice"
              type="checkbox"
              :checked="isRequireNotice"
              @change="$emit('update:isRequireNotice', $event.target.checked)"
            />
            <span
              v-if="reportPageMode == MODE.CONFIRM"
              class="text-slate-600 p-2 text-sm h-8 self-center w-full"
            >
              {{ isRequireNotice ? '必読投稿' : '' }}
            </span>
          </div>
        </li>
        <li
          v-for="(dairy, index) in diary.contents"
          :key="index"
          class="flex py-4 border-b border-solid"
        >
          <div class="flex mx-3.5 w-32">
            <fa icon="fa-clipboard" class="self-center text-slate-300" />
            <div class="ml-3 self-center text-slate-600 text-sm">
              {{ dairy.title }}
            </div>
          </div>
          <div class="w-[calc(100%_-_230px)]">
            <div
              v-if="dairy.require == true && reportPageMode == MODE.EDIT"
              class="text-xs text-red-500 -mt-2 pb-1 ml-1.5"
            >
              必須
            </div>
            <input
              v-if="
                dairy.contentType === REPORT_CONTENT_TYPE.TEXT &&
                reportPageMode == MODE.EDIT
              "
              v-model="dairy.content"
              type="text"
              class="border border-slate-300 text-slate-600 p-2 text-sm rounded-md bg-slate-100 h-8 self-center w-full"
            />
            <span
              v-if="
                dairy.contentType === REPORT_CONTENT_TYPE.TEXT &&
                reportPageMode == MODE.CONFIRM
              "
              class="text-slate-600 p-2 text-sm h-8 self-center w-full"
            >
              {{ dairy.content }}
            </span>

            <template
              v-if="
                dairy.contentType === REPORT_CONTENT_TYPE.CHOICE &&
                reportPageMode == MODE.EDIT
              "
            >
              <label
                v-for="(ch, chi) in dairy.choices"
                :key="`previewchecked${String(index)}${String(chi)}`"
                class="mr-2 inline-block"
              >
                <input
                  v-model="dairy.checked"
                  :value="ch.value"
                  type="checkbox"
                />
                {{ ch.title }}
              </label>
            </template>
            <div
              v-if="
                dairy.contentType === REPORT_CONTENT_TYPE.CHOICE &&
                reportPageMode == MODE.CONFIRM
              "
              class="text-slate-600 p-2 text-sm whitespace-pre-wrap break-words"
            >
              <span>
                {{ dairy.getCheckedTitle() }}
              </span>
            </div>

            <template
              v-if="
                dairy.contentType === REPORT_CONTENT_TYPE.SELECT &&
                reportPageMode == MODE.EDIT
              "
            >
              <label
                v-for="(ch, chi) in dairy.choices"
                :key="`previewselected${String(index)}${String(chi)}`"
                class="mr-2 inline-block"
              >
                <input
                  v-model="dairy.content"
                  :value="String(ch.value)"
                  type="radio"
                />
                {{ ch.title }}
              </label>
            </template>
            <div
              v-if="
                dairy.contentType === REPORT_CONTENT_TYPE.SELECT &&
                reportPageMode == MODE.CONFIRM
              "
              class="text-slate-600 p-2 text-sm whitespace-pre-wrap break-words"
            >
              <span>
                {{ dairy.getSelectTitle() }}
              </span>
            </div>

            <textarea
              v-if="
                dairy.contentType === REPORT_CONTENT_TYPE.TEXTAREA &&
                reportPageMode == MODE.EDIT
              "
              v-model="dairy.content"
              :inputFormat="dairy.content"
              rows="4"
              class="border border-slate-400 p-1 w-full rounded-md bg-slate-100"
            />
            <div
              v-if="
                dairy.contentType === REPORT_CONTENT_TYPE.TEXTAREA &&
                reportPageMode == MODE.CONFIRM
              "
              class="text-slate-600 p-2 text-sm whitespace-pre-wrap break-words"
            >
              {{ dairy.content }}
            </div>
            <template
              v-if="
                dairy.contentType === REPORT_CONTENT_TYPE.IMAGE &&
                reportPageMode == MODE.EDIT
              "
            >
              <label class="fileUploadBtn">
                <input
                  :id="`imageUpload${String(index)}`"
                  type="file"
                  name="uploadFile[]"
                  accept="image/*"
                  class="w-full h-8"
                  @change="$emit('selectMedia', $event, index, /image\/*/)"
                />
                <span class="filebtn">ファイルを選択</span>
              </label>
              {{ dairy.content }}
              <div v-if="dairy.content" class="mt-2">
                <img :src="getMediaUrl(dairy.path)" width="250" />
                <label
                  class="flex items-center cursor-pointer text-semibold text-gray-400 hover:text-gray-600 text-xs mt-2"
                  @click="
                    $emit('deleteMedia', `imageUpload${String(index)}`, index)
                  "
                >
                  添付した画像を削除
                  <fa icon="fa-trash" class="w-5 h-5 p-1" />
                </label>
              </div>
            </template>
            <span
              v-if="
                dairy.contentType === REPORT_CONTENT_TYPE.IMAGE &&
                reportPageMode == MODE.CONFIRM
              "
              class="text-slate-600 p-2 text-sm h-8 self-center w-full"
            >
              {{ dairy.content }}
            </span>
            <template
              v-if="
                dairy.contentType === REPORT_CONTENT_TYPE.MOVIE &&
                reportPageMode == MODE.EDIT
              "
            >
              <label class="fileUploadBtn">
                <input
                  :id="`movieUpload${String(index)}`"
                  type="file"
                  name="uploadMovie[]"
                  accept="video/*"
                  class="w-full h-8"
                  @click="$emit('resetMedia', $event)"
                  @change="$emit('selectMedia', $event, index, /video\/*/)"
                />
                <span class="filebtn">ファイルを選択</span>
              </label>
              {{ dairy.content }}
              <div v-if="dairy.content">
                <!-- <video
                  :src="getMediaUrl(dairy.path)"
                  controls
                  width="250"
                ></video> -->
                <label
                  class="flex items-center cursor-pointer text-semibold text-gray-400 hover:text-gray-600 text-xs mt-2"
                  @click="
                    $emit('deleteMedia', `movieUpload${String(index)}`, index)
                  "
                >
                  添付した動画を削除
                  <fa icon="fa-trash" class="w-5 h-5 p-1" />
                </label>
              </div>
            </template>
            <span
              v-if="
                dairy.contentType === REPORT_CONTENT_TYPE.MOVIE &&
                reportPageMode == MODE.CONFIRM
              "
              class="text-slate-600 p-2 text-sm h-8 self-center w-full"
            >
              {{ dairy.content }}
            </span>
            <Datepicker
              v-if="
                dairy.contentType === REPORT_CONTENT_TYPE.DATETIME &&
                reportPageMode == MODE.EDIT
              "
              id=""
              v-model="dairy.time"
              type="date"
              class="border w-full border-slate-300 text-slate-600 p-2 text-sm rounded-md bg-slate-100 h-8 self-center"
            />
            <div
              v-if="
                dairy.contentType === REPORT_CONTENT_TYPE.DATETIME &&
                reportPageMode == MODE.CONFIRM
              "
              class="text-slate-600 p-2 text-sm whitespace-pre-wrap break-words"
            >
              {{ dairy.content }}
            </div>
            <div
              v-if="
                dairy.contentType === REPORT_CONTENT_TYPE.TENKI &&
                reportPageMode == MODE.EDIT
              "
            >
              <WeatherSelect
                :diary="dairy.content"
                :report-page-mode="reportPageMode"
                @update="dairy.content = $event"
              />
            </div>
            <div
              v-if="
                dairy.contentType === REPORT_CONTENT_TYPE.TENKI &&
                reportPageMode == MODE.CONFIRM
              "
            >
              <WeatherResult :diary="dairy.content" />
            </div>
          </div>
        </li>
      </ul>
      <div
        v-if="reportPageMode == MODE.EDIT"
        class="h-20 px-10 flex justify-end"
      >
      <button class="draft_button py-1 px-8  self-center rounded-md border-0" @click="$emit('saveDraft')">
        <div class="draft">下書き保存</div>
      </button>
        <button
          class="py-1 px-8 bg-cyan-500 self-center rounded-md border-0"
          @click="$emit('confirmReport')"
        >
          <div class="text-white font-bold">確認</div>
        </button>
      </div>
      <div
        v-if="reportPageMode == MODE.CONFIRM"
        class="h-20 px-10 flex justify-between"
      >
        <button
          class="py-1 px-8 bg-gray-300 self-center rounded-md"
          @click="$emit('editReport')"
        >
          <div class="text-gray-500 font-bold">編集</div>
        </button>
        <button
          v-if="diaryEdit"
          class="py-1 px-8 bg-cyan-500 self-center rounded-md border-0"
          @click="$emit('submitPutReport')"
        >
          <div class="text-white font-bold">更新</div>
        </button>
        <button
          v-else
          class="py-1 px-8 bg-cyan-500 self-center rounded-md"
          @click="$emit('submitPostReport')"
        >
          <div class="text-white font-bold">送信</div>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import Datepicker from 'vue3-datepicker';
import { computed, reactive, ref } from 'vue';
import { REPORT_CONTENT_TYPE } from '/@/js/contarcts/ReportContentType';
import { IReportForm } from '/@/js/models/admin/ReportForm';
import { emit } from 'process';
import WeatherSelect from '/@/js/components/home/common/weatherSelect.vue';
import WeatherResult from '/@/js/components/home/common/WeatherResult.vue';

interface Props {
  diary: any;
  diaryEdit: boolean;
  reportPageMode: number;
  fileInfos: any;
  requireMessage: Array<string>;
  requireNotice: boolean;
  isRequireNotice: boolean;
}

const props = defineProps<Props>();

interface Emits {
  (e: 'getLastReport'): void;
  (e: 'resetMedia'): void;
  (e: 'confirmReport'): void;
  (e: 'editReport'): void;
  (e: 'submitPostReport'): void;
  (e: 'submitPutReport'): void;
  (e: 'deleteMedia', targetId: string, index: number): void;
  (e: 'selectMedia', event: any, index: number, reg: RegExp): void;
  (e: 'update:isRequireNotice', checked: boolean): void;
  (e: 'saveDraft'): void;
}

const emits = defineEmits<Emits>();
const MODE = {
  EDIT: 1,
  CONFIRM: 2,
};
// メディアファイルのパスを返却
const getMediaUrl = computed(() => (path: string | File) => {
  if (typeof path === 'string') return path;
  return URL.createObjectURL(path);
});
// const dairy = reactive(props.dairy.content);

//下書き保存
const saveDraft = () => {
  console.log('下書き保存');
};
</script>

<style scoped>
input[type='file'] {
  display: none;
}
.filebtn {
  padding: 5px 7px;
  color: #334155;
  font-weight: nomal;
  font-size: 100%;
  white-space: nowrap;
  border: 1px solid #334155;
  border-radius: 2px;
  background-color: #e1dfdf80;
}
.filebtn:hover {
  background-color: #cbc8c880;
}


.draft{
  color: #06B6D4;
  border: 1px solid #06B6D4;
  padding: 4px 17px;
  border-radius: 6px;
}
</style>
