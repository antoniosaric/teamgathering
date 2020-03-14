import { Component, OnInit } from '@angular/core';
import { AlertifyService } from '../../_services/alertify.service';
import { ProfileService } from '../../_services/profile.service';
import { ActivatedRoute, Router } from '@angular/router';
import { AuthService } from 'src/app/_services/auth.service';
// import { MessagesComponent } from 'src/app/messages/messages.component';


@Component({
  selector: 'app-profile-info',
  templateUrl: './profile-info.component.html',
  styleUrls: ['./profile-info.component.css']
})
export class ProfileInfoComponent implements OnInit {
  profile_info: any;
  modalState = false;

  constructor(
    private alertify: AlertifyService, 
    private profileService: ProfileService,
    private route: ActivatedRoute,
    private router: Router,
    public authService: AuthService, 
    // private message: MessagesComponent
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

//   openChatInMessages(event){
//     console.log(event)
//     console.log(this.profile_info)
//     // this.start_chat_select_array = this.associates;
//     // this.modalState = event;
//   }

//   outputUpdateMessage(profile_id){

// console.log(profile_id)

//     // var index = this.messages.findIndex(x => x.sender_id === profile_id );
//     // index = index = -1 ? this.messages.findIndex(x => x.recipient_id === profile_id ) : index;
//     // this.selected_index = index;
//     // this.selected_id = profile_id;
//     // this.set_thread(this.messages[index], index);
//   }

//   profile_id: 2
// email: "b@b.com"
// image: "https://res.cloudinary.com/dqd4ouqyf/image/upload/v1578601905/default_user.png"
// first_name: "John"
// last_name: "Smith"
// created_date: "2019-12-10 21:18:31"
// team_name: "epic team of epicness"
// team_id: 4
// project_id: 3
// project_name: "Project Three"
  
}
