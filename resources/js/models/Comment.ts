import dayjs from "dayjs"
import { User } from "./User"
import { DiaryModel } from "./Diary"

export interface CommentInterface {
  id: number
  comment: string
  created_at: string
  created_by: User
  deleted_at: string
  diary: DiaryModel
  diary_id: number
}

export class CommentModel implements CommentInterface {
  public readonly id: number;
  public readonly comment: string;
  public readonly created_at: string;
  public readonly created_by: User;
  public readonly deleted_at: string;
  public readonly diary: DiaryModel;
  public readonly diary_id: number;

  constructor(data: Partial<CommentModel>) {
    this.id = data.id || 0;
    this.comment = data.comment || "";
    this.created_at = data.created_at? dayjs(data.created_at).format("YYYY/MM/DD HH:mm:ss"): '';
    this.created_by = data.created_by || {} as User;
    this.deleted_at = data.deleted_at? dayjs(data.deleted_at).format("YYYY/MM/DD HH:mm:ss"): '';
    this.diary = new DiaryModel(data.diary || {});
    this.diary_id = data.diary_id || 0;
  }
}