import { Deserializable } from '../../interface/deserializable.model';

export class BeverageModel implements Deserializable {
  id: number;
  name: string;
  caffeine: number;
  measure: string;
  servings: number;
  status: string;

  deserialize(input: any) {
    Object.assign(this, input);
    return this;
  }
    
  getCaffeine(consumed: any) {
    return this.caffeine * this.servings * consumed;
  } 

  getRemaining(remaining: any) {
    return Math.floor(remaining / this.getCaffeine(1));
  }
}