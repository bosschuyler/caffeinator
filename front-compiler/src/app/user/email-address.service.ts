import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, of } from 'rxjs';
import { flatMap } from 'rxjs/operators';
import { Response } from '../http/response';
import { environment } from '../../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class EmailAddressService {
  private checkUrl = environment.api_host + '/api/user/register/email-check';
  
  constructor(private http: HttpClient) {}

  check(email: string): Observable<Response> {
    return this.http.get(this.checkUrl+"?email="+email).pipe(
        flatMap((res: object) => {
            return of(new Response(res));
        })
    );
  }
}
