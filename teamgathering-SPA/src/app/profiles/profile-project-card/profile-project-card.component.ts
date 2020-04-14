import { Component, OnInit, Input } from '@angular/core';
import { Project } from 'src/app/_models/project';
import { AuthService } from 'src/app/_services/auth.service';


@Component({
  selector: 'app-profile-project-card',
  templateUrl: './profile-project-card.component.html',
  styleUrls: ['./profile-project-card.component.css']
})
export class ProfileProjectCardComponent implements OnInit {
  @Input() project: Project;
  @Input() page: string;

  constructor( private authService: AuthService) { }

  ngOnInit() {
  }


}
