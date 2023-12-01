<template>
  <span v-for="(require, idx) in requireMessage" :key="idx">
    <div class="redtxt">
      {{ require }}
    </div>
  </span>
  <div>
    <div>
      <div class="single_control">
        <label v-if="reportPageMode == MODE.EDIT" class="center">
          <fa icon="fa-clipboard" class="clipboard" />
          <span>共有内容</span>
        </label>
        <label v-if="reportPageMode == MODE.CONFIRM" class="mb-10">
          ※入力内容をご確認ください。
        </label>
        <div>
          <button
            v-if="reportPageMode == MODE.EDIT"
            class="graybtn btn smalltxt flex_h"
            @click="$emit('getLastReport')"
          >
            <i class="fa fa-undo" />
            前回の投稿
          </button>
        </div>
      </div>
      <ul>
        <li v-if="requireNotice">
          <div class="input_box">
            <span class="article_li_date">必読投稿</span>
            <input
              v-if="reportPageMode == MODE.EDIT"
              :value="isRequireNotice"
              type="checkbox"
              :checked="isRequireNotice"
              @change="$emit('update:isRequireNotice', $event.target.checked)"
            />
            <span v-if="reportPageMode == MODE.CONFIRM">
              {{ isRequireNotice ? '：チェックあり' : '：チェックなし' }}
            </span>
          </div>
        </li>
        <li
          v-for="(dairy, index) in diary.contents"
          :key="index"
          class="input_box"
        >
          <div>
            <label>
              {{ dairy.title }}
              <span
                v-if="dairy.require == true && reportPageMode == MODE.EDIT"
                class="redtxt smalltxt"
              >
                (必須)
              </span>
            </label>
          </div>
          <div class="content_font">
            <input
              v-if="
                dairy.contentType === REPORT_CONTENT_TYPE.TEXT &&
                reportPageMode == MODE.EDIT
              "
              v-model="dairy.content"
              type="text"
              @blur="inputBlur"
            />
            <span
              v-if="
                dairy.contentType === REPORT_CONTENT_TYPE.TEXT &&
                reportPageMode == MODE.CONFIRM
              "
            >
              {{ dairy.content }}
            </span>

            <template
              v-if="
                dairy.contentType === REPORT_CONTENT_TYPE.CHOICE &&
                reportPageMode == MODE.EDIT
              "
            >
              <p
                v-for="(ch, chi) in dairy.choices"
                :key="`previewchecked${String(index)}${String(chi)}`"
              >
                <input
                  v-model="dairy.checked"
                  :value="ch.value"
                  type="checkbox"
                />
                {{ ch.title }}
              </p>
            </template>
            <div
              v-if="
                dairy.contentType === REPORT_CONTENT_TYPE.CHOICE &&
                reportPageMode == MODE.CONFIRM
              "
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
              <p
                v-for="(ch, chi) in dairy.choices"
                :key="`previewselected${String(index)}${String(chi)}`"
              >
                <input
                  v-model="dairy.content"
                  :value="String(ch.value)"
                  type="radio"
                />
                {{ ch.title }}
              </p>
            </template>
            <div
              v-if="
                dairy.contentType === REPORT_CONTENT_TYPE.SELECT &&
                reportPageMode == MODE.CONFIRM
              "
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
            />
            <div
              v-if="
                dairy.contentType === REPORT_CONTENT_TYPE.TEXTAREA &&
                reportPageMode == MODE.CONFIRM
              "
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
                  @change="$emit('selectMedia', $event, index, /image\/*/)"
                />
                <span class="filebtn">ファイルを選択</span>
              </label>
              {{ dairy.content }}
              <div v-if="dairy.content" class="mt-2">
                <img :src="getMediaUrl(dairy.path)" width="250" />
                <p
                  class="attached_delete"
                  @click="
                    $emit('deleteMedia', `imageUpload${String(index)}`, index)
                  "
                >
                  添付した画像を削除
                  <fa icon="fa-trash" class="w-5 h-5 p-1" />
                </p>
              </div>
            </template>
            <span
              v-if="
                dairy.contentType === REPORT_CONTENT_TYPE.IMAGE &&
                reportPageMode == MODE.CONFIRM
              "
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
                <p
                  class="attached_delete"
                  @click="
                    $emit('deleteMedia', `movieUpload${String(index)}`, index)
                  "
                >
                  添付した動画を削除
                  <fa icon="fa-trash" class="w-5 h-5 p-1" />
                </p>
              </div>
            </template>
            <span
              v-if="
                dairy.contentType === REPORT_CONTENT_TYPE.MOVIE &&
                reportPageMode == MODE.CONFIRM
              "
            >
              {{ dairy.content }}
            </span>
            <Datepicker
              v-if="
                dairy.contentType === REPORT_CONTENT_TYPE.DATETIME &&
                reportPageMode == MODE.EDIT
              "
              v-model="dairy.time"
              class="date_picker"
            />
            <div
              v-if="
                dairy.contentType === REPORT_CONTENT_TYPE.DATETIME &&
                reportPageMode == MODE.CONFIRM
              "
            >
              {{ dairy.content }}
            </div>
            <div
              v-if="
                dairy.contentType === REPORT_CONTENT_TYPE.TENKI &&
                reportPageMode == MODE.EDIT
              "
              class="mt5 my_weather"
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
    </div>
  </div>
  <div
    v-if="reportPageMode == MODE.EDIT"
    class="single_main add_comment ft_singlebtn_area"
  >
    <button class=" draft" @click="$emit('saveDraft')">
      <div class="">下書き保存</div>
    </button>
    <button class="bluebtn btn" @click="$emit('confirmReport')">
      <div>確認</div>
    </button>
  </div>
  <div
    v-if="reportPageMode == MODE.CONFIRM"
    class="single_main add_comment ft_btn_area"
  >
    <button class="graybtn btn" @click="$emit('editReport')">
      <div>編集</div>
    </button>
    <button
      v-if="diaryEdit"
      class="bluebtn btn"
      @click="$emit('submitPutReport')"
    >
      <div>更新</div>
    </button>
    <button v-else class="bluebtn btn" @click="$emit('submitPostReport')">
      <div>送信</div>
    </button>
  </div>
</template>

<script setup lang="ts">
import Datepicker from 'vue3-datepicker';
import { computed } from 'vue';
import { REPORT_CONTENT_TYPE } from '/@/js/contarcts/ReportContentType';
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
defineProps<Props>();

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
defineEmits<Emits>();
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
const inputBlur = (e) => {
  e.target.blur();
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
li {
  list-style: none;
}
.clipboard {
  color: #a6a6a6;
  padding-right: 4px;
}
.center {
  display: flex;
  align-items: baseline;
  white-space: nowrap;
}
.single_control span {
  font-size: 18px;
  margin-bottom: 10px;
}
.ft_singlebtn_area {
  display: flex;
  justify-content: flex-end;
}
.attached_delete {
  display: flex;
  align-items: center;
  cursor: pointer;
  margin-top: 8px;
  font-size: 14px;
  color: rgb(156 163 175);
}
.mb-10 {
  margin-bottom: 10px;
  font-size: 17px;
}
.draft{
  color: #06B6D4;
  border: 1px solid #06B6D4;
  padding: 4px 17px;
  border-radius: 30px;
  background-color: white;
  margin-right: 15px;
}
</style>
