import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable, of } from 'rxjs';
import { flatMap } from 'rxjs/operators';
import { Response } from '../http/response';

import { environment } from '../../environments/environment';


@Injectable({
	providedIn: 'root'
})
export class UserService {
	private registerUrl = environment.api_host + '/api/user/register';
	private registerPasswordUrl = environment.api_host + '/api/user/register/password';
	private loginUrl = environment.api_host + '/api/user/login';

    private auth = {};
	
	constructor(private http: HttpClient) {}

	register(data: object): Observable<Response> {
		var form = new FormData();
		for(var key in data)
			form.append(key, data[key]);
		return this.http.post(this.registerUrl, form).pipe(
			flatMap((res: object) => {
                var response = new Response(res);
                var token =response.getParam('token');
                if(token)
                    localStorage.setItem('token', token);
				return of(new Response(res));
			})
		);
	}

	registerPassword(data: object): Observable<Response> {
		return this.http.post(this.registerPasswordUrl, data).pipe(
			flatMap((res: object) => {
                var response = new Response(res);
                var token =response.getParam('token');
                if(token)
                    localStorage.setItem('token', token);
				return of(new Response(res));
			})
		);
	}

	login(data: object): Observable<Response> {
		var form = new FormData();
		for(var key in data)
			form.append(key, data[key]);
			
		var httpOptions = {
			withCredentials: true
		};
		
		return this.http.post(this.loginUrl, form, httpOptions).pipe(
			flatMap((res: object) => {
                var response = new Response(res);
                var token =response.getParam('token');
                if(token)
                    localStorage.setItem('token', token);
				return of(new Response(res));
			})
		);
	}
    
    isAuthenticated() {
        if(localStorage.getItem('token'))
            return true;
        else
            return false;
    }
}
