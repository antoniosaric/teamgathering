import { Component, OnInit } from '@angular/core';
import { ProjectService } from '../../_services/project.service';
import { AlertifyService } from '../../_services/alertify.service';
import { Project } from 'src/app/_models/project';

@Component({
  selector: 'app-project-list',
  templateUrl: './project-list.component.html',
  styleUrls: ['./project-list.component.css']
})
export class ProjectListComponent implements OnInit {
  projects: Project[];

  constructor(
    private projectService: ProjectService, 
    private alertify: AlertifyService
  ) { }

  ngOnInit() {
    this.getProjects();
  }

  getProjects(){
    this.projectService.getHomepage().subscribe(response => {
      this.projects = response;
      console.log(response)
    }, error => {
      this.alertify.error(error);
    })
  }


}
