import { Component, OnInit } from '@angular/core';
import { TeamService } from '../../_services/team.service';
import { AlertifyService } from '../../_services/alertify.service';
import { AuthService } from '../../_services/auth.service';
import { Team } from 'src/app/_models/team';
import { ActivatedRoute, Router } from '@angular/router';
import { NgForm, Validators, FormBuilder, FormGroup } from '@angular/forms';

@Component({
  selector: 'app-team-edit',
  templateUrl: './team-edit.component.html',
  styleUrls: ['./team-edit.component.css']
})
export class TeamEditComponent implements OnInit {
  team_info: any;
  team_update_info: any;
  team_profiles = [];
  team_profile_update_info: any;
  teamInfoForm: FormGroup;
  profileInfoForm: FormGroup;
  deleteTeamForm: FormGroup;
  profile_status_selectables = ['active', 'inactive'];
  deleteState = false;
  deleteTeamObject: any;
  model: any = {};

  constructor(
    private alertify: AlertifyService,
    private route: ActivatedRoute, 
    private teamService: TeamService, 
    private authService: AuthService,
    private fb: FormBuilder,
    private router: Router
  ) { }

  ngOnInit() {
    this.route.data.subscribe(data=> {
      this.team_info = data['team'];
      this.createForm();
      console.log(this.team_info)
    })
  }

  createForm(){
    this.teamInfoForm = this.fb.group({
      team_name: [this.team_info.team_name, Validators.required  ],
      team_description: [this.team_info.team_description, Validators.required  ]
    })

    this.deleteTeamForm = this.fb.group({
      deleteText: ['', [ Validators.required, Validators.minLength(5), Validators.maxLength(6), Validators.pattern('(?:^|\W)DELETE(?:$|\W)') ] ],
      password: ['', [Validators.required, Validators.minLength(4), Validators.maxLength(16)]]
    })
  }


  updateTeamInfo(){
    this.authService.checkToken();
    if( this.teamInfoForm.valid ){
      this.team_update_info = Object.assign( {}, {...this.teamInfoForm.value,
        ...{'team_id': this.team_info['team_id'] }
       });
       this.team_info = Object.assign( {}, {...this.teamInfoForm.value,
        ...{'profiles': this.team_info['profiles'] },
        ...{'team_id': this.team_info['team_id'] }
       });
      this.teamService.updateTeam({ 'token': localStorage.getItem('token') }, this.team_update_info ).subscribe(next => {
        this.authService.setToken(next);
        this.alertify.success('team update successful');
        this.teamInfoForm.reset(this.team_update_info);
        this.teamInfoForm.reset(this.team_info);
      }, error => {
        this.alertify.error(error);
      }, () => {
        this.router.navigate(['/team/edit/'+this.team_info['team_id']]);
      })
    }
  }

  // updateProfileTeamInfo(data){
  //   this.authService.checkToken();
  //   console.log(data)
  //   console.log(this.model)
  //   if( this.profileInfoForm.valid ){
  //     this.team_profile_update_info = Object.assign( {}, { ...{'team_name': this.profileInfoForm['teams'] }, ...{'team_description': this.profileInfoForm['count'] }} 
  //     );
  //     this.teamService.updateTeamProfile({ 'token': localStorage.getItem('token') }, this.team_profile_update_info ).subscribe(next => {
  //       this.authService.setToken(next);
  //       this.alertify.success('team update successful');
  //       this.teamInfoForm.reset(this.team_update_info);
  //     }, error => {
  //       this.alertify.error(error);
  //     }, () => {
  //       // this.ngOnInit();
  //       this.router.navigate(['/team/edit/'+this.team_info['team_id']]);
  //     })
  //   }
  // }

  deleteProfileFromTeam(data){
    this.alertify.confirm('Are you sure you want to delete this profile from the team?', () => {
      this.authService.checkToken();
      console.log(data)

      if(data.role == 'Owner'){
        this.alertify.error('cannot delete owner');
      }else{
        console.log(data)
        this.teamService.deleteProfileFromTeam( { 'token': localStorage.getItem('token')}, { 'profile_id': data.profile_id , 'profiles_team_id': data.profiles_team_id} ).subscribe(next => {
          this.authService.setToken(next);
          this.teamInfoForm.reset(this.team_info);
          this.alertify.success('profile successfully removed from team');
        }, error => {
          this.alertify.error(error);
        }, () => {
          this.router.navigate(['/team/edit/'+this.team_info['team_id']]);
        })
      }
    })
    return true;
  }

  deleteTeam(){
    console.log('yes')
    return true;
  }

  changeDeleteState(event){
    this.deleteTeamForm.reset(this.deleteTeamObject);
    this.deleteState = event;
  }



}
