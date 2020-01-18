import { Component, OnInit, HostListener } from '@angular/core';
import { FormGroup, FormBuilder, Validators } from '@angular/forms';
import { AlertifyService } from 'src/app/_services/alertify.service';
import { ProjectService } from 'src/app/_services/project.service';
import { AuthService } from 'src/app/_services/auth.service';
import { Router, ActivatedRoute } from '@angular/router';

@Component({
  selector: 'app-project-add',
  templateUrl: './project-add.component.html',
  styleUrls: ['./project-add.component.css']
})
export class ProjectAddComponent implements OnInit {
  addProjectForm: FormGroup;
  project_info: any;
  status_options: any = ['public', 'private'];
  
  @HostListener('window:beforeunload', ['$event'])
  unloadNotification($event: any) {
    if(this.addProjectForm.dirty) {
      $event.returnValue = true;
    }
  }

  constructor(
    private route: ActivatedRoute, 
    private alertify: AlertifyService, 
    private projectService: ProjectService, 
    private authService: AuthService,
    private fb: FormBuilder,
    private router: Router
  ) { }

  ngOnInit() {
    this.createaddProjectForm();
  }

  createaddProjectForm(){
    this.addProjectForm = this.fb.group({
      project_name: ['', [ Validators.required ] ],
      project_status: ['Select Project Status', [ Validators.required ] ],
      description: ['', Validators.required],
      short_description: ['', Validators.required],
      looking_for: ['' ],
      stacks: ['' ]
    })
  }

  addProject(){
    this.authService.checkToken();
    if( this.addProjectForm.valid ){
      this.project_info = Object.assign( {}, this.addProjectForm.value );
      this.projectService.addProject({ 'token': localStorage.getItem('token') }, this.project_info).subscribe(next => {
        this.authService.setToken(next);
        this.alertify.success('Project added successful');
        this.addProjectForm.reset(this.project_info);
      }, error => {
        this.alertify.error(error);
      }, () => {
        // this.ngOnInit();
        this.router.navigate(['/project/edit/'+this.project_info['project_id']]);
      })
    }
  }

  editProfileRoute(){
    this.alertify.confirm('Are you sure you want to not save this project and return to edit profile?', () => {
      this.addProjectForm.reset(this.project_info);
      console.log(this.authService.profile_id)
      this.router.navigate(['/profile/edit']);
    })
  }

}
