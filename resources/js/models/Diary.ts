import dayjs from "dayjs"
import { InHouseSystemModel } from "./InHouseSystem"
import { User } from "./User"

export interface DiaryInterface {
  id: number
  diary: ContentListModel
  division_cd: number
  in_house_system_id: number
  in_house_system: InHouseSystemModel
  created_at: string
  created_by: User
  updated_at: string
  deleted_at: string
}

export interface ContentInterface {
  content: string
  contentType: number
  require: boolean
  title: string
}

export interface ContentListInterface {
  contents: ContentInterface[]
}

export type ContentListType = Array<ContentModel> | [];

export class ContentModel implements ContentInterface {
  public readonly content: string;
  public readonly contentType: number;
  public readonly require: boolean;
  public readonly title: string;

  constructor(data: Partial<ContentModel>) {
    this.content = data.content || "";
    this.contentType = data.contentType || 1;
    this.require = data.require || false;
    this.title = data.title || "";
  }
}


export class ContentListModel implements ContentListInterface {
  public readonly contents: ContentInterface[];

  constructor(data: ContentListType) {
    this.contents = data.map((d: ContentInterface) => {
      return new ContentModel(d);
    });
  }
}

export type DiaryType = {
  id: number
  diary: ContentListInterface
  division_cd: number
  in_house_system_id: number
  in_house_system: InHouseSystemModel
  created_at: string
  created_by: User
  updated_at: string
  deleted_at: string
  title: string
};

export class DiaryModel implements DiaryInterface {
  public readonly id: number;
  public readonly diary: ContentListModel;
  public readonly division_cd: number;
  public readonly in_house_system_id: number;
  public readonly in_house_system: InHouseSystemModel;
  public readonly created_at: string;
  public readonly created_by: User;
  public readonly updated_at: string;
  public readonly deleted_at: string;
  public readonly title: string;

  constructor(data: Partial<DiaryModel>) {
    this.created_at = data.created_at? dayjs(data.created_at).format("YYYY/MM/DD HH:mm:ss"): '';
    this.created_by = data.created_by || {} as User;
    this.deleted_at = data.deleted_at? dayjs(data.deleted_at).format("YYYY/MM/DD HH:mm:ss"): '';
    this.diary = new ContentListModel(data.diary?.contents || []);
    this.division_cd = data.division_cd || 0;
    this.id = data.id || 0;
    this.in_house_system_id = data.in_house_system_id || 0;
    this.in_house_system = new InHouseSystemModel(data.in_house_system || {});
    this.updated_at = data.updated_at? dayjs(data.updated_at).format("YYYY/MM/DD HH:mm:ss"): '';
    this.title = data.title? data.title: '';
  }
}