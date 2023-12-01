import dayjs from 'dayjs';
import { User } from './User';
import { DiaryModel } from './Diary';

export interface MustReadInterface {
  diary: DiaryModel;
  user: User;
  created_at: string;
  updated_at: string;
}

export class MustReadModel implements MustReadInterface {
  public readonly diary: DiaryModel;
  public readonly user: User;
  public readonly created_at: string;
  public readonly updated_at: string;

  constructor(data: Partial<MustReadModel>) {
    this.diary = new DiaryModel(data.diary || {});
    this.user = data.user || ({} as User);
    this.created_at = data.created_at
      ? dayjs(data.created_at).format('YYYY/MM/DD HH:mm:ss')
      : '';
    this.updated_at = data.updated_at
      ? dayjs(data.updated_at).format('YYYY/MM/DD HH:mm:ss')
      : '';
  }
}

export interface MustReadListInterface {
  list: MustReadInterface[];
}

export type MustReadListType = Array<MustReadModel> | [];

export class MustReadListModel implements MustReadListInterface {
  public readonly list: MustReadInterface[];

  constructor() {
    this.list = [];
  }

  init(data: MustReadListType) {
    this.clear();
    this.list.push(
      ...data.map((d: MustReadInterface) => {
        return new MustReadModel(d);
      })
    );
  }

  clear() {
    this.list.splice(0);
  }

  get latestCount(): number {
    return this.list.filter((i) => {
      const createDate = dayjs(i.created_at);
      // // 24時間以内の必読を最新とする
      const now = dayjs().subtract(1, 'd');
      // 1時間以内の必読を最新とする
      // const now = dayjs().subtract(1, 'h');
      return createDate.isAfter(now);
    }).length;
  }
}
