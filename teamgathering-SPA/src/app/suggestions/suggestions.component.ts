import { Component, OnInit } from '@angular/core';
import { NgForm } from '@angular/forms';
import { environment } from '../../environments/environment';
import { HttpClient } from '@angular/common/http';
import { AlertifyService } from '../_services/alertify.service';



@Component({
  selector: 'app-suggestions',
  templateUrl: './suggestions.component.html',
  styleUrls: ['./suggestions.component.css']
})
export class SuggestionsComponent implements OnInit {

  constructor( private http: HttpClient, private alertify: AlertifyService) { }

  ngOnInit() {
  }

  sendSuggestion(parameters: any){
    return this.http.post( environment.apiUrl + 'main/do_suggestions.php', parameters )

  }

  onSubmit(form: NgForm) {
    if(!!form.valid){
      this.sendSuggestion( { ...form.value } ).subscribe(next => {
      }, error => {
        this.alertify.error(error);
      })
    }

    console.log('Your form data : ', form.value);
    console.log('Your form data : ', form.valid);
}



}
