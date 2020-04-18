import { Component, OnInit, ViewChild, HostListener } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { AlertifyService } from 'src/app/_services/alertify.service';
import { NgForm, Validators, FormBuilder, FormGroup } from '@angular/forms';
import { ProfileService } from 'src/app/_services/profile.service';
import { AuthService } from 'src/app/_services/auth.service';
import { TagService } from 'src/app/_services/tag.service';
import { FollowService } from 'src/app/_services/follow.service';
import { StatusService } from 'src/app/_services/status.service';

@Component({
  selector: 'app-profile-edit',
  templateUrl: './profile-edit.component.html',
  styleUrls: ['./profile-edit.component.css']
})
export class ProfileEditComponent implements OnInit {
  @ViewChild('editForm', {static:true}) editForm: FormGroup;
  profile_info: any;
  projects_array = [];
  projects_array_non_owner = [];
  state = 'profile';
  image: string = '';
  page = 'profile';
  project_page = 'edit';
  
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
    private router: Router,
    private tagService: TagService,
    private followService: FollowService,
    private statusService: StatusService
  ) { }

  ngOnInit() {
    this.route.data.subscribe(data => {
      this.profile_info = data['profile'];
      this.image = this.profile_info.image;
    })
    this.createEditForm();
    this.setTeamArray();


    this.projects_array.filter(function(){return true;});

    console.log('^^^^^^^')
    console.log(this.projects_array)


    this.statusService.searchStatus();
    console.log(this.profile_info)

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

  setTeamArray(){
    for(var i = 0 ; i < this.profile_info.teams.length ; i++){
      var project_object = { 'project_name': this.profile_info.teams[i].project_name, 'project_id': this.profile_info.teams[i].project_id };
      if( this.profile_info.teams[i].owner_id == this.profile_info.profile_id ){
        if ( !this.projects_array.some(e => e.project_id === project_object.project_id)) {
          this.projects_array.push( project_object ) ;
        }
      }else{
        if ( !this.projects_array_non_owner.some(e => e.project_id === project_object.project_id)) {
          this.projects_array_non_owner.push( project_object ) ;
        }
      }
    }
  }

  updateProfile(){
    this.authService.checkToken();
    if( this.editForm.valid ){
      this.profile_info = Object.assign( {}, {...this.editForm.value, ...{ image: this.profile_info.image }, ...{ tags: this.profile_info.tags }, ...{ follows: this.profile_info.follows }, ...{ projects: this.profile_info.projects }, ...{ teams: this.profile_info.teams } } );
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

  removeTagFromProfileObject(tag){
    this.profile_info.tags.splice(this.profile_info.tags.findIndex(v => v.tag_id === tag.tag_id), 1);
  }

  addTagToProfileObject(next, tag_name){
    this.profile_info.tags.push({ 'tag_name': tag_name, 'tag_id': next.tag_id });
  }

  deleteTag(tag){
    this.alertify.confirm('Are you sure you want to remove this skill?', () => {
      this.authService.checkToken();
      this.tagService.deleteTag({ 'token': localStorage.getItem('token') }, tag).subscribe(next => {
        this.authService.setToken(next);
        this.removeTagFromProfileObject(tag);
        this.alertify.success('Profile update successful');
      }, error => {
        this.alertify.error(error);
      }, () => {
        this.router.navigate(['/profile/edit']);
      })
    })
  }

  removeFollowFromProfileObject(follow){
    this.profile_info.follows.splice(this.profile_info.follows.findIndex(v => v.follow_id === follow.follow_id), 1);
  }

  deleteFollow(data){
    this.authService.checkToken();
    this.followService.deleteFollow({ 'token': localStorage.getItem('token') }, {'project_id': data.project_id}).subscribe(next => {
      this.authService.setToken(next);
      this.removeFollowFromProfileObject(data);
      this.alertify.success('unfollowed project');
    }, error => {
      this.alertify.error(error);
    }, () => {

    })
  }

  addTag(tag){
    if( tag != '' && tag != null && tag != undefined){
      this.tagService.addTag({ 'token': localStorage.getItem('token') }, tag ).subscribe(next => {
        this.authService.setToken(next);
        this.addTagToProfileObject( next, tag.tag_name )
        this.alertify.success('skill added successfully');
      }, error => {
        this.alertify.error(error);
      }, () => {
        this.router.navigate(['/profile/edit']);
      })
    }
  }

  onEnter(){
    return false;
  }

  ownerCheck( profile_id , owner_id ){
    if( parseInt(profile_id) == parseInt(owner_id) ){
      return true;
    }else{
      return false;
    } 
  }

  leaveTeam(team_id){
    console.log(team_id)
  }

}
