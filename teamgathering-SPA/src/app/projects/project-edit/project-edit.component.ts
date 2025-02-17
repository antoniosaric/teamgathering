import { Component, OnInit, ViewChild, HostListener } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { AlertifyService } from 'src/app/_services/alertify.service';
import { NgForm, Validators, FormBuilder, FormGroup } from '@angular/forms';
import { ProjectService } from 'src/app/_services/project.service';
import { AuthService } from 'src/app/_services/auth.service';
import { TagService } from 'src/app/_services/tag.service';

@Component({
  selector: 'app-project-edit',
  templateUrl: './project-edit.component.html',
  styleUrls: ['./project-edit.component.css']
})
export class ProjectEditComponent implements OnInit {
  @ViewChild('editForm', {static:true}) editForm: FormGroup;
  project_info: any;
  state = 'project';
  project_id: number = 0;
  image:string = '';
  page = 'project';
  status_options: any = ['public', 'private'];
  deleteState = false;
  deleteProjectForm: FormGroup;
  deleteProjectObject: any;
  project_object: any;
  
  @HostListener('window:beforeunload', ['$event'])
  unloadNotification($event: any) {
    if(this.editForm.dirty) {
      $event.returnValue = true;
    }
  }

  constructor(
    private route: ActivatedRoute, 
    private alertify: AlertifyService, 
    private projectService: ProjectService, 
    private authService: AuthService,
    private fb: FormBuilder,
    private router: Router,
    private tagService: TagService
  ) { }

  ngOnInit() {
    this.route.data.subscribe(data => {
      this.project_info = data['project'];
      this.image = this.project_info.image;
      this.project_id = this.project_info.project_id;
    })
    this.setProjectInfoToAddTeam();
    this.createEditForm();
  }

  createEditForm(){
    this.editForm = this.fb.group({
      project_name: [this.project_info.project_name, [ Validators.required ] ],
      project_status: [this.project_info.project_status, [ Validators.required ] ],
      description: [this.project_info.description, Validators.required],
      short_description: [this.project_info.short_description, Validators.required],
      looking_for: [this.project_info.looking_for ],
      stacks: [this.project_info.stacks ]
    })

    this.deleteProjectForm = this.fb.group({
      deleteText: ['', [ Validators.required, Validators.minLength(5), Validators.maxLength(6), Validators.pattern('(?:^|\W)DELETE(?:$|\W)') ] ],
      password: ['', [Validators.required, Validators.minLength(4), Validators.maxLength(16)]]
    })

  }

  updateProject(){
    this.authService.checkToken();
    if( this.editForm.valid ){
      this.project_info = Object.assign( {}, 
        {...this.editForm.value, ...{ 'image': this.project_info.image},
         ...{'teams': this.project_info['teams'] },
         ...{'count': this.project_info['count'] },
         ...{'created_date': this.project_info['created_date']},
         ...{'project_id': this.project_info['project_id']},
         ...{ 'tags': this.project_info['tags']} 
        } 
      );
      this.projectService.updateProject({ 'token': localStorage.getItem('token') }, this.project_info).subscribe(next => {
        this.authService.setToken(next);

        this.alertify.success('Project update successful');
        this.editForm.reset(this.project_info);
      }, error => {
        this.alertify.error(error);
      }, () => {
        // this.ngOnInit();
        this.router.navigate(['/project/edit/'+this.project_info['project_id']]);
      })
    }
  }

  changeState(state){
    this.state = state;
  }

  setPhoto(data){
    console.log(data)
    this.project_info.image = data.image;
    this.image = data.image;
  }

  toPresentCheck(date){
    var current_date = new Date();
    if( date == '0000-00-00 00:00:00' || current_date == date || date == undefined ){
      return true;
    }else{
      return false;
    }
  }

  statusClassCheck(data){
    if(data.profile_team_status == 'active' ){
      return 'green';
    }else{
      return 'red';
    }
  }

  removeTagFromProjectObject(tag){
    this.project_info.tags.splice(this.project_info.tags.findIndex(v => v.tag_id === tag.tag_id), 1);
  }

  addTagToProjectObject(next, tag_name){
    this.project_info.tags.push({ 'tag_name': tag_name, 'tag_id': next.tag_id });
  }

  deleteTagProject(tag){
    this.alertify.confirm('Are you sure you want to remove this stack?', () => {
      this.authService.checkToken();
      this.tagService.deleteTagProject({ 'token': localStorage.getItem('token') }, { 'tag_id': tag.tag_id, 'project_id': this.project_id } ).subscribe(next => {
        this.authService.setToken(next);
        this.removeTagFromProjectObject(tag);
        this.alertify.success('Profile update successful');
      }, error => {
        this.alertify.error(error);
      }, () => {
      })
    })
  }

  changeDeleteState(event){
    this.deleteProjectForm.reset(this.deleteProjectObject);
    this.deleteState = event;
  }

  backToProfile(){
    this.router.navigate(['/profile/edit']);
  }

  deleteProject(){
    this.alertify.confirm('Last Chance To Not Delete This Project', () => {
      this.authService.checkToken();
      this.projectService.deleteProject( { 'token': localStorage.getItem('token')}, { 'project_id': this.project_info.project_id, 'password': this.deleteProjectForm.value.password } ).subscribe(next => {
        this.authService.setToken(next);
        this.alertify.success('project successfully deleted');
        this.backToProfile();
      }, error => {
        this.alertify.error(error);
      })
    })
    return true;
  }

  addTag(tag){
    console.log(tag)
    if( tag != '' && tag != null && tag != undefined){
      this.tagService.addTagProject({ 'token': localStorage.getItem('token') }, tag ).subscribe(next => {
        this.authService.setToken(next);
        this.addTagToProjectObject( next, tag.tag_name )
        this.alertify.success('skill added successfully');
      }, error => {
        this.alertify.error(error);
      }, () => {
        this.router.navigate(['/project/edit/'+this.project_id]);
      })
    }
  }


  setProjectInfoToAddTeam(){
    this.project_object = [{
      'project_id': this.project_info.project_id,
      'project_name': this.project_info.project_name
    }];
  }

}