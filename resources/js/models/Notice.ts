import dayjs from 'dayjs';
import { CommentModel } from './Comment';

export const NOTICE_TYPE = {
  UNKNOWN: 0,
  MENTION: 1,
  COMMENT: 2,
};

export interface NoticeInterface {
  id: number;
  comment: CommentModel;
  comment_id: number;
  notification_type: number;
  user_id: number;
  created_at: string;
  updated_at: string;
  deleted_at: string;
  user: CommentUserInterface;
  system_name: string;
}

class CommentUser {
  public readonly id: number;
  public readonly human_id: string;
  public readonly user_name: string;
  constructor(data: Partial<CommentUserInterface>) {
    this.id = data.id || 0;
    this.human_id = data.human_id || '';
    this.user_name = data.user_name || '';
  }
}

interface CommentUserInterface {
  id: number;
  human_id: string;
  user_name: string;
}

export interface NoticeListInterface {
  list: NoticeInterface[];
}

export class NoticeModel implements NoticeInterface {
  public readonly id: number;
  public readonly comment: CommentModel;
  public readonly comment_id: number;
  public readonly notification_type: number;
  public readonly user_id: number;
  public readonly created_at: string;
  public readonly updated_at: string;
  public readonly deleted_at: string;
  public readonly user: CommentUser;
  public readonly system_name: string;

  constructor(data: Partial<NoticeModel>) {
    this.id = data.id || 0;
    this.comment = new CommentModel(data.comment || {});
    this.comment_id = data.comment_id || 0;
    this.notification_type = data.notification_type || NOTICE_TYPE.UNKNOWN;
    this.user_id = data.user_id || 0;
    this.user = data.user || new CommentUser({});
    this.created_at = data.created_at
      ? dayjs(data.created_at).format('YYYY/MM/DD HH:mm:ss')
      : '';
    this.updated_at = data.updated_at
      ? dayjs(data.updated_at).format('YYYY/MM/DD HH:mm:ss')
      : '';
    this.deleted_at = data.deleted_at
      ? dayjs(data.deleted_at).format('YYYY/MM/DD HH:mm:ss')
      : '';
    this.system_name = data.comment?.diary.in_house_system.system_name || '';
  }

  get message(): string {
    if (this.notification_type == NOTICE_TYPE.MENTION) {
      return 'あなた宛にコメントしました。';
    } else if (this.notification_type == NOTICE_TYPE.COMMENT) {
      return 'あなたの投稿にコメントしました。';
    }
    return '';
  }
}

export type NoticeListType = Array<NoticeModel> | [];

export class NoticeListModel implements NoticeListInterface {
  public readonly list: NoticeModel[];

  constructor() {
    this.list = [];
  }

  init(data: NoticeListType) {
    this.clear();
    this.list.push(
      ...data.map((d: NoticeInterface) => {
        return new NoticeModel(d);
      })
    );
  }

  clear() {
    this.list.splice(0);
  }

  noticeCount() {
    return this.list.length;
  }

  newNoticeCount() {
    const newNotice = this.list.filter((n) => {
      const createDate = dayjs(n.created_at);
      const now = dayjs().subtract(2, 'd'); // 本日の日付から2日以内を最新とする
      return createDate.isAfter(now);
    });
    return newNotice.length;
  }
}
