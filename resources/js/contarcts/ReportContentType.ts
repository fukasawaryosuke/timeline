export const REPORT_CONTENT_TYPE = { 
    TEXT:'text',
    CHOICE:'choice',
    SELECT:'select',
    DATETIME:'datetime',
    TENKI:'tenki',
    TEXTAREA:'textarea',
    IMAGE:'dairyFixImage', // スペルミスがあるが、既に本番環境にアップ済みの為、修正はしないこと
    MOVIE:'diaryMovie',
  } as const;
  export type REPORT_CONTENT_TYPE = typeof REPORT_CONTENT_TYPE[keyof typeof REPORT_CONTENT_TYPE];
  