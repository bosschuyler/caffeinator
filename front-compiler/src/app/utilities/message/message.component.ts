import { Component, OnInit, Input } from '@angular/core';

@Component({
  selector: 'app-message',
  templateUrl: './message.component.html',
  styleUrls: ['./message.component.scss']
})
export class MessageComponent implements OnInit {
  
  @Input() message = '';
  @Input() loading = false;
  @Input() errors = [];
  @Input() type = 'notice';
  visible = true;

  constructor() { }

  isLoading() {
    return this.loading;
  }

  hide() {
      this.visible = false;
  }

  show() {
      this.visible = true;
  }

  isError() {
      return !this.isLoading() && this.type == 'error';
  }

  isSuccess() {
      return !this.isLoading() && this.type == 'success';
  }

  isWarning() {
      return !this.isLoading() && this.type == 'warning';
  }

  isNotice() {
      return !this.isLoading() && this.type == 'notice';
  }

  reset() {
      this.message = '';
  }



  ngOnInit() {
  }

}
