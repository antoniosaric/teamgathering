import { Component, OnInit, Output, EventEmitter, Input } from '@angular/core';
import { FormControl, NgForm } from '@angular/forms';
import { HttpClient } from '@angular/common/http';
  
import { debounceTime, tap, switchMap, finalize } from 'rxjs/operators';
import { TagService } from 'src/app/_services/tag.service';
import { AuthService } from 'src/app/_services/auth.service';
import { AlertifyService } from 'src/app/_services/alertify.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-tag-profile',
  templateUrl: './tag-profile.component.html',
  styleUrls: ['./tag-profile.component.css']
})
export class TagProfileComponent implements OnInit {
  @Output() returnTag = new EventEmitter<object>();
  @Input() page: string; 
  input_string: string;
  tag_string: string;
  searchTagsCtrl = new FormControl();
  filtered_tags: any;
  isLoading = false;
  errorMsg: string;
  tagForSave: any;

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

  // addTagToProfile( next, tag_name ){
  //   this.returnTag.emit( { 'tag_name': tag_name, 'tag_id': next.tag_id } );
  // }

  addTag(tag){
    if( tag != '' && tag != null && tag != undefined){

      this.returnTag.emit( { 'tag_name': tag } );
      this.searchTagsCtrl.reset()

    //   this.tagService.addTag({ 'token': localStorage.getItem('token') }, { 'tag_name': tag } ).subscribe(next => {
    //     this.authService.setToken(next);
    //     this.addTagToProfile( next, tag )
    //     this.alertify.success('skill added successfully');
    //     this.searchTagsCtrl.reset()
    //   }, error => {
    //     this.alertify.error(error);
    //   }, () => {
    //     this.router.navigate(['/profile/edit']);
    //   })
    }
  }

  onSubmit() {
      this.searchTagsCtrl.reset()
  };

  returnFalse(){
    return false;
  }

  filteredTagsReturn(input){
    // var return_array: any = [];
    // var regex = new RegExp( input, "gi");
    // // return this.all_tags.filter(   tag => tag.tag_name.match(regex)  );
    // for (var i = 0; i < this.all_tags.length; i++) { 
    //   // return this.all_tags.filter(   tag => tag.tag_name.match(regex)     );
    //   // return this.all_tags.filter(   tag => console.log(tag.tag_name.match(regex))     );
    //   var tag = this.all_tags[i];
    //   if( tag.tag_name.match(regex) ){ 
    //     return_array.push( tag );
    //   } 
    // } 
    // return return_array; 
  }

}
