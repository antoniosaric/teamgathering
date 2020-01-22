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
  new_project_id: number = 0;
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
      project_name: ['', Validators.required ],
      project_status: ['private', Validators.required ],
      description: ['', Validators.required],
      short_description: ['', Validators.required]
      // looking_for: ['' ],
      // stacks: ['' ]
    })
  }

  setNewProjectId(data){
    this.new_project_id = data.new_project_id;
    console.log(this.new_project_id)
  }

  addProject(){
    this.authService.checkToken();
    if( this.addProjectForm.valid ){
      this.project_info = Object.assign( {}, this.addProjectForm.value );
      this.projectService.addProject({ 'token': localStorage.getItem('token') }, this.project_info).subscribe(next => {
        this.authService.setToken(next);
        this.setNewProjectId(next);
        this.alertify.success('Project added successful');
        this.addProjectForm.reset(this.project_info);
      }, error => {
        this.alertify.error(error);
      }, () => {
        // this.ngOnInit();
        this.router.navigate(['/project/edit/'+this.new_project_id ]);
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
