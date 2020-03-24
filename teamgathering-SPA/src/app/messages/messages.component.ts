import { Component, OnInit, Input, HostListener } from '@angular/core';
import { AuthService } from '../_services/auth.service';
import { environment } from '../../environments/environment';
import { ProfileService } from '../_services/profile.service';
import { AlertifyService } from '../_services/alertify.service';
import { ActivatedRoute } from '@angular/router';
import { Profile } from '../_models/profile';
import { Validators, FormGroup, FormBuilder } from '@angular/forms';

@Component({
  selector: 'app-messages',
  templateUrl: './messages.component.html',
  styleUrls: ['./messages.component.css']
})
export class MessagesComponent implements OnInit {
  startChat: FormGroup;
  modalState = false;
  name = '';
  profile_id: any;
  selected_index: any;
  selected_id: any;
  selected_image = '';
  threads = [];
  messages = [];
  associates = [];
  model: any = {};
  profiles: Profile[];
  message_state = true;
  start_chat_select_array = [];
  interval: any;

  constructor( private authService: AuthService,
    private profileService: ProfileService, 
    private alertify: AlertifyService,
    private route: ActivatedRoute,
    private fb: FormBuilder
    ) { }

  ngOnInit() {
    this.name = this.authService.name;
    this.profile_id = this.authService.profile_id;
    this.getAssociates();
    this.getMessages();
  }

  ngOnDestroy() {
    if (this.interval) {
      clearInterval(this.interval);
    }
  }

  startCheckingThreads(profile_id){
    this.ngOnDestroy();
    this.getMessage( profile_id );
    this.interval = setInterval(() => {
      this.getMessage( profile_id );
    }, 10000);
  }

  getMessages(){
    this.profileService.getMessages({ 'token': localStorage.getItem('token') }).subscribe(next => {
      this.messages = next['messages'];
      if( next['messages'].length > 0 ){
        var reply_id = this.messages[0].sender_id == this.profile_id ? this.messages[0].recipient_id : this.messages[0].sender_id;
        this.selected_image = this.messages[0].image;
        this.selected_index = 0;
        this.selected_id = reply_id;
        this.message_state = true;
        this.startCheckingThreads(reply_id)
      }else{
        this.message_state = false;
        this.selected_index = null;
        this.selected_id = null;

      }
    }, error => {
      this.alertify.error(error);
    })
  }

  getMessage(profile_id){
    this.authService.checkToken();
    this.profileService.getMessage({ 'token': localStorage.getItem('token') }, { 'profile_id': profile_id }).subscribe(next => {
      this.threads = next['threads'];
    }, error => {
      this.alertify.error(error);
    })
  }

  assignAssociates(data){
    this.associates = data;
  }

  getAssociates(){
    this.authService.checkToken();
    this.profileService.getAssociateProfiles({ 'token': localStorage.getItem('token') }).subscribe(next => {
      this.assignAssociates(next)
    }, error => {
      this.alertify.error(error);
    })
  }

  messageCheck(state){
    if(state == 'incoming' ){
      return true;
    }else{
      return false;
    }
  }

  returnFalse(){
    return false;
  }

  postMessage(){
    this.authService.checkToken();
    var message_info = Object.assign( {}, {...{ "message": this.model.message }, ...{ "recipient_id": this.selected_id } } );
    this.profileService.postMessage({ 'token': localStorage.getItem('token') }, message_info ).subscribe(next => {
      var todayISOString : string = new Date().toISOString();
      var time_array = todayISOString.split(/[.\T]/);
      var post_time = time_array[0].concat(' ').concat(time_array[1]);
      var post = {
        "image":"https:\/\/res.cloudinary.com\/dqd4ouqyf\/image\/upload\/v1579315614\/default_profile",
        "first_name":"",
        "last_name":"",
        "created_date":post_time,
        "sender_id":this.profile_id,
        "recipient_id":this.selected_id,
        "message":this.model.message,
        "message_id":0
      }; 
      this.threads.push(post);
      this.model.message = '';
    }, error => {
      this.alertify.error(error);
    })
  }

  selectedCheck(index){
    console.log(index)
  }

  assignName(data){
    if(!!data.first_name){
      return data.first_name + ' ' + data.last_name;
    }else{
      return 'member';
    }
  }

  checkActive(index){
    if( index == this.selected_index ){
      return true;
    }else{
      return false;
    }
  }

  outputUpdateMessage(profile_id){
    var index = this.messages.findIndex(x => x.sender_id === profile_id );
    index = index = -1 ? this.messages.findIndex(x => x.recipient_id === profile_id ) : index;
    this.selected_index = index;
    this.selected_id = profile_id;
    this.set_thread(this.messages[index], index);
  }

  set_thread(data, index){
    var select_id = data.sender_id == this.profile_id ? data.recipient_id : data.sender_id;
    this.selected_image = data.image;
    this.selected_index = index;
    this.selected_id = select_id;
    this.startCheckingThreads(select_id)
  }

  onSetChatState(event){
    console.log(this.associates)
    this.start_chat_select_array = this.associates;
    this.modalState = event;
  }

  // scrollBottom(){

  //   const bottom = document.getElementById("inbox-chat")
  //   var c = 0
    
  //   setInterval(function() {
  //       // allow 1px inaccuracy by adding 1
  //       const isScrolledToBottom = bottom.scrollHeight - bottom.clientHeight <= bottom.scrollTop + 1
    
  //       const newElement = document.createElement("div")
    
  //       newElement.textContent = format(c++, 'Bottom position:', bottom.scrollHeight - bottom.clientHeight,  'Scroll position:', bottom.scrollTop)
    
  //       bottom.appendChild(newElement)
    
  //       // scroll to bottom if isScrolledToBottom is true
  //       if if(!isScrolledToBottom) {
  //         bottom.scrollTop = bottom.scrollHeight - bottom.clientHeight
  //       }
  //   }, 500)
    
  //   function format () {
  //     return Array.prototype.slice.call(arguments).join(' ')
  //   }
  // }



}
