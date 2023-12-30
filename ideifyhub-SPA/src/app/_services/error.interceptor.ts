import { Injectable } from "@angular/core";
import { HttpRequest, HttpHandler, HttpErrorResponse, HttpEvent, HTTP_INTERCEPTORS } from '@angular/common/http';
import { Observable, throwError } from "rxjs";
import { catchError } from 'rxjs/operators';

@Injectable()
export class HttpInterceptor implements HttpInterceptor {
    intercept(
        req: HttpRequest<any>, 
        next: HttpHandler
    ): Observable<HttpEvent<any>> {
        return next.handle(req).pipe(
            catchError(error => {
                if(error instanceof HttpErrorResponse){
                    console.log(error);
                    console.log(error.error);
                    if(
                        error.status === 400 ||
                        error.status === 401 || 
                        error.status === 403 ||
                        error.status === 404 ||
                        error.status === 500 ||
                        error.status === 501
                    ){
                        return throwError(error.error.message);
                    }
                    const serverError = error.error.message;
                    let modalStateErrors = '';
                    if(serverError && typeof serverError === 'object'){
                        for(const key in serverError){
                            if(serverError[key]){
                                modalStateErrors += serverError[key] + '\n';
                            }
                        }
                    }
                    return throwError(modalStateErrors || serverError || 'Server Error');
                }
            })
        );
    }
}

export const ErrorInterceptorProvider = {
    provide: HTTP_INTERCEPTORS,
    useClass: HttpInterceptor,
    multi: true
}