import { Component, OnInit, ViewChild, HostListener } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { AlertifyService } from 'src/app/_services/alertify.service';
import { NgForm, Validators, FormBuilder, FormGroup } from '@angular/forms';
import { ProfileService } from 'src/app/_services/profile.service';
import { AuthService } from 'src/app/_services/auth.service';

@Component({
  selector: 'app-profile-edit',
  templateUrl: './profile-edit.component.html',
  styleUrls: ['./profile-edit.component.css']
})
export class ProfileEditComponent implements OnInit {
  @ViewChild('editForm', {static:true}) editForm: FormGroup;
  profile_info: any;
  state = 'profile';
  image:string = '';
  page = 'profile';
  
  @HostListener('window:beforeunload', ['$event'])
  unloadNotification($event: any) {
    if(this.editForm.dirty) {
      $event.returnValue = true;
    }
  }

  constructor(
    private route: ActivatedRoute, 
    private alertify: AlertifyService, 
    private profileService: ProfileService, 
    private authService: AuthService,
    private fb: FormBuilder,
    private router: Router
  ) { }

  ngOnInit() {
    this.route.data.subscribe(data => {
      this.profile_info = data['profile'];
      this.image = this.profile_info.image;
      console.log(this.profile_info)
    })
    this.createEditForm();
  }

  createEditForm(){
    this.editForm = this.fb.group({
      first_name: [this.profile_info.first_name, Validators.required],
      last_name: [this.profile_info.last_name, Validators.required],
      zip_code: [this.profile_info.zip_code, [ Validators.required, Validators.minLength(5), Validators.maxLength(7), Validators.pattern('[0-9]{5}') ] ],
      description: [this.profile_info.description ],
      looking_for: [this.profile_info.looking_for ]
    })
  }

  updateProfile(){
    this.authService.checkToken();
    if( this.editForm.valid ){
      this.profile_info = Object.assign( {}, {...this.editForm.value, ...{ image: this.profile_info.image} } );
      this.profileService.updateProfile({ 'token': localStorage.getItem('token') }, this.profile_info).subscribe(next => {
        this.authService.setProfileName(this.profile_info);
        this.authService.setToken(next);
        this.alertify.success('Profile update successful');
        this.editForm.reset(this.profile_info);
      }, error => {
        this.alertify.error(error);
      }, () => {
        // this.ngOnInit();
        this.router.navigate(['/profile/edit']);
      })
    }
  }

  changeState(state){
    console.log('@@@@@@@@')
    this.state = state;
  }

  setPhoto(data){
    this.profile_info.image = data.image;
    this.image = data.image;
  }

  toPresentCheck(date){
    var current_date = new Date();
    if( date == '0000-00-00 00:00:00' || current_date == date || date == undefined ){
      return true;
    }else{
      return false;
    }
  }

  statusClassCheck(data){
    if(data.profile_team_status == 'active' ){
      return 'green';
    }else{
      return 'red';
    }
  }

  deleteTag(tag){
    console.log(tag)
  }
}
