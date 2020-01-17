import { Component, OnInit } from '@angular/core';
import { AlertifyService } from 'src/app/_services/alertify.service';
import { ProjectService } from 'src/app/_services/project.service';
import { ActivatedRoute, Router } from '@angular/router';

@Component({
  selector: 'app-project-info',
  templateUrl: './project-info.component.html',
  styleUrls: ['./project-info.component.css']
})
export class ProjectInfoComponent implements OnInit {
  project_info: any;

  constructor(
    private alertify: AlertifyService, 
    private profileService: ProjectService,
    private route: ActivatedRoute,
    private router: Router
  ) { }

  ngOnInit() {
    this.route.data.subscribe(data => {
      this.project_info = data['project'];
      console.log(this.project_info)
    })
  }
}
