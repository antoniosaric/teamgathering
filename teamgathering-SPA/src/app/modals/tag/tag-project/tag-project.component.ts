import { Component, OnInit, Output, EventEmitter, Input } from '@angular/core';
import { FormControl, NgForm } from '@angular/forms';
import { HttpClient } from '@angular/common/http';
  
import { debounceTime, tap, switchMap, finalize } from 'rxjs/operators';
import { TagService } from 'src/app/_services/tag.service';
import { AuthService } from 'src/app/_services/auth.service';
import { AlertifyService } from 'src/app/_services/alertify.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-tag-project',
  templateUrl: './tag-project.component.html',
  styleUrls: ['./tag-project.component.css']
})
export class TagProjectComponent implements OnInit {
  @Output() returnTag = new EventEmitter<object>();
  @Input() project_id: string; 
  @Input() page: string; 
  input_string: string;
  tag_string: string;
  searchTagsCtrl = new FormControl();
  filtered_tags: any;
  isLoading = false;
  errorMsg: string;
  tagForSave: any;
  all_tags = [];
  displayed_tags = [];

  constructor(
    private http: HttpClient, 
    private tagService: TagService,
    private authService: AuthService,
    private alertify: AlertifyService,
    private router: Router
    ) { }

  ngOnInit() {
    
    this.searchTagsCtrl.valueChanges
      .pipe(
        debounceTime(5),
        tap(() => {
          this.errorMsg = "";
          this.filtered_tags = [];
          this.isLoading = true;
        }),
        switchMap(value => {
          if(value != '' && value != null){
            if(value.hasOwnProperty('tag_name')){
              this.input_string = value.tag_name;
              return this.tagService.getTags( { 'token': localStorage.getItem('token') }, { 'tag': value.tag_name } )
              .pipe(
                finalize(() => {
                  this.isLoading = false
                }),
              )
            }else{
              this.input_string = value;
              return this.tagService.getTags( { 'token': localStorage.getItem('token') }, { 'tag': value } )
              .pipe(
                finalize(() => {
                  this.isLoading = false
                }),
              )
            }
          }else{
            return [];
          }
        })
      )
      .subscribe(data => {
        if (data['tags'] == undefined ) {
          this.errorMsg = data['message'];
          this.tagForSave = {'tag_name': this.input_string}
          this.filtered_tags = [];
          this.filtered_tags.unshift(this.tagForSave)
        } else {
          this.tagForSave = {'tag_name': this.input_string}
          this.errorMsg = "";
          this.filtered_tags = data['tags'];
          this.filtered_tags.unshift(this.tagForSave)
        }
        });
    }

  displayFn(tag) {
    if (!tag || tag == undefined || tag == null){
      this.isLoading = false;
      return ''; 
    }else{
      this.addTag(tag);
      this.searchTagsCtrl.reset();
      this.isLoading = false;
    }
  }

  addTag(tag){
    if( tag != '' && tag != null && tag != undefined){
      this.returnTag.emit( { 'tag_name': tag, 'project_id': this.project_id } );
      this.searchTagsCtrl.reset()
    }
  }

  onSubmit() {
      this.searchTagsCtrl.reset()
  };

  returnFalse(){
    return false;
  }
  

}
