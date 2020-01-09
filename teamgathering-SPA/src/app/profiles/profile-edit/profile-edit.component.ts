import { Component, OnInit, ViewChild, HostListener } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { AlertifyService } from 'src/app/_services/alertify.service';
import { NgForm } from '@angular/forms';
import { ProfileService } from 'src/app/_services/profile.service';
import { AuthService } from 'src/app/_services/auth.service';
import { Profile } from 'selenium-webdriver/firefox';

@Component({
  selector: 'app-profile-edit',
  templateUrl: './profile-edit.component.html',
  styleUrls: ['./profile-edit.component.css']
})
export class ProfileEditComponent implements OnInit {
  @ViewChild('editForm', {static:true}) editForm: NgForm;
  profile: Profile;
  profile_info: any;
  state = 'profile';
  image:string = '';
  @HostListener('window:beforeunload', ['$event'])
  unloadNotification($event: any) {
    if(this.editForm.dirty) {
      $event.returnValue = true;
    }
  }

  constructor(private route: ActivatedRoute, private alertify: AlertifyService, private profileService: ProfileService, private authService: AuthService) { }

  ngOnInit() {
    this.route.data.subscribe(data => {
      this.profile_info = data['profile'];
      this.image = this.profile_info.image;
    })
  }

  updateProfile(){
    this.profileService.updateProfile({ 'token': localStorage.getItem('token') }, this.profile_info).subscribe(next => {
      this.alertify.success('Profile update successful');
      this.editForm.reset(this.profile_info);
    }, error => {
      this.alertify.error(error);
    })

  }

  changeState(state){
    this.state = state;
  }

  setPhoto(data){
    this.profile_info.image = data.image;
    this.image = data.image;
  }

}
