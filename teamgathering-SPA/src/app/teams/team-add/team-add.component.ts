import { Component, OnInit } from '@angular/core';
import { TeamService } from '../../_services/team.service';
import { AlertifyService } from '../../_services/alertify.service';
import { ProjectService } from '../../_services/project.service';
import { AuthService } from '../../_services/auth.service';
import { ActivatedRoute, Router } from '@angular/router';
import { Validators, FormBuilder, FormGroup } from '@angular/forms';

@Component({
  selector: 'app-team-add',
  templateUrl: './team-add.component.html',
  styleUrls: ['./team-add.component.css']
})
export class TeamAddComponent implements OnInit {
  team_info: any;
  new_team_id = 0;
  teamInfoForm: FormGroup;
  project_list = [];
  selectProject = false;

  constructor(
    private alertify: AlertifyService,
    private teamService: TeamService, 
    private projectService: ProjectService, 
    private authService: AuthService,
    private fb: FormBuilder,
    private router: Router
  ) { }

  ngOnInit() {
    this.getProjectsList();
    this.createForm();
  }

  createForm(){
    this.teamInfoForm = this.fb.group({
      team_name: [ '', Validators.required  ],
      team_description: [ '', Validators.required  ],
      project_id: [ 'select project' ]
    })
  }

  setNewTeamId(data){
    this.new_team_id= data.newTeamId;
  }

  addTeamInfo(){
    this.authService.checkToken();
    if( this.teamInfoForm.valid ){
      if(this.teamInfoForm.value.project_id != "select project"){
      this.team_info = Object.assign( {}, {...this.teamInfoForm.value });
      this.teamService.addTeam({ 'token': localStorage.getItem('token') }, this.team_info ).subscribe(next => {
        this.authService.setToken(next);
        this.alertify.success('team added successful');
        this.setNewTeamId(next);
        this.teamInfoForm.reset(this.team_info);
        this.selectProject = false;
      }, error => {
        this.alertify.error(error);
      }, () => {
        this.router.navigate(['/team/edit/'+this.new_team_id]);
      })
      }else{
        this.selectProject = true;
      }
    }
  }

  setProjectList(data){
    this.project_list = data.projects;
  }

  getProjectsList(){
    this.authService.checkToken();
    this.projectService.getProjectsList({ 'token': localStorage.getItem('token') } ).subscribe(next => {
      this.setProjectList(next);
    }, error => {
      this.alertify.error(error);
    }) 
  }

  goBack(){
    if(!!this.teamInfoForm.dirty){
      this.alertify.confirm('You have unsaved info, are you sure you want to go back?', () => {
        this.router.navigate(['/profile/edit']);
      })
    }else{
      this.router.navigate(['/profile/edit']);
    }
  }
}
