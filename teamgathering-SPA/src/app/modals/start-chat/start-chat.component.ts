import { Component, OnInit, Output, EventEmitter, Input, HostListener } from '@angular/core';
import { ProfileService } from 'src/app/_services/profile.service';
import { AlertifyService } from 'src/app/_services/alertify.service';
import { ActivatedRoute, Router } from '@angular/router';
import { FormBuilder, Validators, FormGroup, NgForm } from '@angular/forms';
import { AuthService } from 'src/app/_services/auth.service';

@Component({
  selector: 'app-start-chat',
  templateUrl: './start-chat.component.html',
  styleUrls: ['./start-chat.component.css']
})
export class StartChatComponent implements OnInit {
  startChat: FormGroup
  message_info: any = {};
  @Input() start_chat_select_array = []; 
  @Output() onSetChatState = new EventEmitter<boolean>() ;
  @Output() outputUpdateMessage = new EventEmitter<number>();

  @HostListener('window:beforeunload', ['$event'])
  unloadNotification($event: any) {
    if(this.startChat.dirty) {
      $event.returnValue = true;
    }
  }

  constructor(
    private profileService: ProfileService, 
    private alertify: AlertifyService,
    private route: ActivatedRoute,
    private authService: AuthService,
    private fb: FormBuilder,
    private router: Router
  ) { }

  ngOnInit() {
    this.startChatForm();
  }

  startChatForm(){
    this.startChat = this.fb.group({
      message: [ '', [ Validators.required ] ],
      recipient: [ '', [ Validators.required ] ]
    })
  }

  startChatSend(){
    this.authService.checkToken();
    if( this.startChat.valid ){
      this.message_info = Object.assign( {}, { ...{ 'message': this.startChat.value.message}, ...{'recipient_id': this.startChat.value.recipient.profile_id } } 
      );
      this.profileService.postMessage({ 'token': localStorage.getItem('token') }, this.message_info ).subscribe(next => {
        // this.router.navigate(['/messages']);
        this.outputUpdateMessage.emit(this.startChat.value.recipient.profile_id);
        this.onSetChatState.emit(false);
        this.startChat.reset(this.message_info);
      }, error => {
        this.alertify.error(error);
      })
    }else{
      this.alertify.error('Recipient needed and message needed');
    }
  }

  closeModal(){
    this.onSetChatState.emit(false);
  }

  assignName(data){
    if(!!data.first_name){
      return data.first_name + ' ' + data.last_name;
    }else{
      return 'member';
    }
  }

  checkName(data){
    if(!!data.first_name){
      return true;
    }else{
      return false;
    }
  }

  chatArrayCountCheck(){
    if( this.start_chat_select_array.length > 0 ){
      return true;
    }else{
      return false;
    }
  }

}
