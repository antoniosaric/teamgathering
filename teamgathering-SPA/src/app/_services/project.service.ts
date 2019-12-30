import { Injectable } from '@angular/core';
import { environment } from '../../environments/environment';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Project } from '../_models/project';

@Injectable({
  providedIn: 'root'
})
export class ProjectService {

constructor( private http: HttpClient ) { }

  getHomepage(): Observable<Project[]> {
    return this.http.get<Project[]>( environment.apiUrl + 'main/homepage.php' )
  }

}
