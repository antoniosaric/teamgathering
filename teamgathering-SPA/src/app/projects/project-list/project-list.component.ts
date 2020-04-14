import { Component, OnInit } from '@angular/core';
import { ProjectService } from '../../_services/project.service';
import { AlertifyService } from '../../_services/alertify.service';
import { Project } from 'src/app/_models/project';
import { ActivatedRoute } from '@angular/router';
import { AuthService } from 'src/app/_services/auth.service';
import { StatusService } from 'src/app/_services/status.service';

@Component({
  selector: 'app-project-list',
  templateUrl: './project-list.component.html',
  styleUrls: ['./project-list.component.css']
})
export class ProjectListComponent implements OnInit {
  projects: Project[];

  constructor(
    private projectService: ProjectService, 
    private alertify: AlertifyService,
    private route: ActivatedRoute,
    private authService: AuthService,
    private statusService: StatusService
  ) { }

  ngOnInit() {
    this.route.data.subscribe(data=> {
      this.projects = data['projects'];
    })
    if(!this.authService.loggedIn){
      this.projects = this.projects.slice(0, 8);
    }
    this.statusService.searchStatus();
    // this.getProjects();
  }

  // getProjects(){
  //   this.projectService.getHomepage().subscribe(response => {
  //     this.projects = response;
  //     console.log(response)
  //   }, error => {
  //     this.alertify.error(error);
  //   })
  // }


}
