import { Component, OnInit } from '@angular/core';
import { AlertifyService } from 'src/app/_services/alertify.service';
import { ProjectService } from 'src/app/_services/project.service';
import { ActivatedRoute, Router } from '@angular/router';
import { AuthService } from 'src/app/_services/auth.service';
import { RequestService } from 'src/app/_services/request.service';

@Component({
  selector: 'app-project-info',
  templateUrl: './project-info.component.html',
  styleUrls: ['./project-info.component.css']
})
export class ProjectInfoComponent implements OnInit {
  project_info: any;

  constructor(
    private alertify: AlertifyService, 
    private projectService: ProjectService,
    private route: ActivatedRoute,
    private router: Router,
    public authService: AuthService,
    private requestService: RequestService
  ) { }

  ngOnInit() {
    this.route.data.subscribe(data => {
      this.project_info = data['project'];
    })
  }

  editAuthorizationCheck(){
    if( parseInt(this.project_info.owner_id) == parseInt(this.authService.profile_id) ){
      return true;
    }else{
      return false;
    }
  }

  profileOnTeamCheck(index){
    for( var i = 0; i < this.project_info.teams[index].profiles.length; i++){
      console.log(this.project_info.teams[index].profiles[i].profile_id)
      if( this.authService.profile_id == this.project_info.teams[index].profiles[i].profile_id){
        return true;
        break;
      }
    }
    return false;
  }

  requestJoinProject(project_id){
    this.requestService.addRequest({ 'token': localStorage.getItem('token') }, {'project_id': project_id}).subscribe(next => {
      this.authService.setToken(next);
      this.alertify.success('request made');
    }, error => {
      this.alertify.error(error);
    }, () => {

    })
  }



}
