import { Component, OnInit, Input, Output, EventEmitter } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { TagService } from 'src/app/_services/tag.service';
import { AuthService } from 'src/app/_services/auth.service';
import { AlertifyService } from 'src/app/_services/alertify.service';
import { Router } from '@angular/router';
import { FormControl } from '@angular/forms';
import { debounceTime, tap, switchMap, finalize } from 'rxjs/operators';

@Component({
  selector: 'app-tag-team',
  templateUrl: './tag-team.component.html',
  styleUrls: ['./tag-team.component.css']
})
export class TagTeamComponent implements OnInit {
  @Output() returnTag = new EventEmitter<object>();
  @Input() team_id: string; 
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
        debounceTime(500),
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
      this.returnTag.emit( { 'tag_name': tag, 'team_id': this.team_id } );
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
