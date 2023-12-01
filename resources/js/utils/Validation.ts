import useAxios from '/@/js/compositions/UseAxios';

const { errorToast } = useAxios();

export interface Target {
  value: string;
  message: string;
  rule: (value: string) => boolean;
}
interface ValidatorInterface {
  targets: Target[];
  validate(): boolean;
}
export class Validator implements ValidatorInterface{
  targets: Target[];

  constructor(targets: Target[]) {
    this.targets = targets;
  }

  validate(): boolean {
    for (const target of this.targets) {
      if (target.rule(target.value)) {
        errorToast(400, target.message);
        return true;
      }
    }

    return false;
  }
}
