import { Injectable } from '@angular/core';
import { environment } from 'src/environments/environment';
import { HttpClient } from '@angular/common/http';

@Injectable({
  providedIn: 'root'
})
export class SearchService {

constructor( private http: HttpClient ) { }

  search( token: any, parameters: any ){
    const params = { ...token, ...parameters };
    return this.http.post( environment.apiUrl + 'main/get_searchResults.php', params )
  }

  getSuggestions( token: any ){
    const params = { ...token };
    return this.http.post( environment.apiUrl + 'main/get_suggestions.php', params )
  }

}
