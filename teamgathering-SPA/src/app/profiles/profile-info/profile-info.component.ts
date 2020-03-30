import { Component, OnInit } from '@angular/core';
import { AlertifyService } from '../../_services/alertify.service';
import { ProfileService } from '../../_services/profile.service';
import { ActivatedRoute, Router } from '@angular/router';
import { AuthService } from 'src/app/_services/auth.service';

@Component({
  selector: 'app-profile-info',
  templateUrl: './profile-info.component.html',
  styleUrls: ['./profile-info.component.css']
})
export class ProfileInfoComponent implements OnInit {
  profile_info: any;
  modalState = false;
  associate = [];
  current_project_array = [];

  constructor(
    private alertify: AlertifyService, 
    private profileService: ProfileService,
    private route: ActivatedRoute,
    private router: Router,
    public authService: AuthService, 
  ) { }

  ngOnInit() {
    this.route.data.subscribe(data => {
      this.profile_info = data['profile'];
    })
  }

  statusClassCheck(data){
    if(data.profile_team_status == 'active' ){
      return 'green';
    }else{
      return 'red';
    }
  }

  openChatInMessages(event){
    this.associate = [{
      'profile_id': this.profile_info.profile_id,
      'first_name': this.profile_info.first_name,
      'last_name': this.profile_info.last_name
    }];
    this.modalState = event;
  }

  outputUpdateMessage(event){
    this.router.navigate(['/messages']);

// console.log(profile_id)
//     this.router.navigate(['/messages']);
//     var index = this.messages.findIndex(x => x.sender_id === profile_id );
//     index = index = -1 ? this.messages.findIndex(x => x.recipient_id === profile_id ) : index;
//     this.selected_index = index;
//     this.selected_id = profile_id;
//     this.set_thread(this.messages[index], index);
  }

  projectIdCheck(project_id){
    console.log(this.current_project_array)
    if( this.current_project_array.includes( project_id ) ){
      this.current_project_array.push( project_id ) ;
      return true;
    }else{
      this.current_project_array.push( project_id ) ;
      return false;
    }
  }

  resetIdCheck(event){
    console.log(event)
    this.current_project_array = [];
  }



}
