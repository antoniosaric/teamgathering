import { Component, OnInit } from '@angular/core';
import { AlertifyService } from 'src/app/_services/alertify.service';
import { ProjectService } from 'src/app/_services/project.service';
import { ActivatedRoute, Router } from '@angular/router';
import { AuthService } from 'src/app/_services/auth.service';

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
    public authService: AuthService
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
}
