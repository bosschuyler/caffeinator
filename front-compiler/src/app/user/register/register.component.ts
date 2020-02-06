import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { EmailAddressService } from '../email-address.service';
import { of, Observable } from 'rxjs';
import { Response } from 'src/app/http/response';
import { UserService } from '../user.service';
import { MessageComponent } from 'src/app/utilities/message/message.component';

@Component({
	selector: 'app-register',
	templateUrl: './register.component.html',
	styleUrls: ['./register.component.scss']
})
export class RegisterComponent implements OnInit {
	authenticated = false;
	password = {
		type: 'password'
	};

	password_confirm = {
		type: 'password'
	};
	
	email = {
        message: '',
        messageType: '',
        loading: false,
		value: '',
		exists: false,
		registered: false,
		valid: false,
		isNew() {
		    return this.valid && (!this.isRegistered() && !this.isDuplicate());
		},
		isDuplicate() {
		    return this.exists;
		},
		isRegistered() {
		    return this.registered;
		},
		isLoading() {
		    return this.loading;
		},
		reset() {
			this.registered = false;
			this.exists = false;
            this.valid = false;
            this.message = '';
		}
	};
	timer;

	data = {
		first_name: '',
		last_name: '',
		password: '',
		password_confirm: '',
		confirmation_code: '',
		email: '',
		phone: ''
	}

	form = {
		loading: false,
		submitted: false,
		error: false,
		errors: [],
		message: '',
		isLoading() {
			return this.loading;
		},
		isSubmitted() {
			return this.submitted;
		},
		hasError() {
			return this.error;
		}
	}

	constructor(private router: Router, private userService: UserService, private emailAddressService: EmailAddressService ) {}  

	onEmailKeyup(event: any) {
		var _this = this;
		var emailService = this.emailAddressService;
		_this.email.loading = true;
        _this.email.message = "Checking...";
        _this.email.messageType = 'notice';
		clearTimeout(this.timer);
		this.timer = setTimeout(function() {  
		emailService.check(_this.data.email).subscribe((response: Response) => {
			_this.email.loading = false;
			_this.email.reset();
			_this.email.valid = response.isSuccessful();
            _this.email.message = response.getMessage();
            if(!response.isSuccessful()) {
				if(response.getParam('duplicate')) {
					_this.email.valid = true;
					_this.email.exists = true;
					if(response.getParam('registered'))
						_this.email.registered = true;
				}
            }
            if(_this.email.isRegistered())
                _this.email.message += ' Login to proceed.';
            else if(_this.email.isDuplicate())
                _this.email.message += ' You may choose a password now.';
            
            if(_this.email.isLoading()) {
                _this.email.messageType = 'notice';
            } else if(!_this.email.valid) 
                _this.email.messageType = 'error';
            else if(_this.email.isNew())
                _this.email.messageType = 'success';
            else
                _this.email.messageType = 'notice';
		});
		}, 500);
	}

	register(event: any) {
		var _this = this;
		_this.form.submitted = true;
		_this.form.message = "Submitting...";
		_this.form.loading = true;
		_this.form.errors = []; 
		_this.userService.register(_this.data).subscribe((response: Response) => {
			_this.form.loading = false;
			_this.form.error = response.isSuccessful() ? false : true;
			_this.form.message  = response.getMessage();
            _this.form.errors = response.getErrors();
            
            if(response.isSuccessful())
				_this.router.navigateByUrl('/beverage/calculator');
		});
	}

	registerPassword(event: any) {
		console.log(this.data);
	}

	login(event: any) {
		var _this = this;
		_this.form.submitted = true;
		_this.form.message = "Checking Auth...";
		_this.form.loading = true;
		_this.form.errors = []; 
		_this.userService.login(_this.data).subscribe((response: Response) => {
			_this.form.loading = false;
			_this.form.message = response.getMessage();
            _this.form.error = response.isSuccessful() ? false : true;

			if(response.isSuccessful())
				_this.router.navigateByUrl('/beverage/calculator');
		});
	}

	togglePassword(event: any) {
		this.password.type = (this.password.type == 'password') ? 'text' : 'password';
	}
	togglePasswordConfirm(event: any) {
		this.password_confirm.type = (this.password_confirm.type == 'password') ? 'text' : 'password';
	}

	private handleError<T> (operation = 'operation', result?: T) {
		return (error: any): Observable<T> => {
		
			// TODO: send the error to remote logging infrastructure
			console.error(error); // log to console instead
		
			// TODO: better job of transforming error for user consumption
			this.log(`${operation} failed: ${error.message}`);
		
			// Let the app keep running by returning an empty result.
			return of(result as T);
		};
	}

	private log(message: string) {
	}

	ngOnInit() {
        var _this = this;
        if(_this.userService.isAuthenticated())
            _this.router.navigateByUrl('/beverage/calculator');

	}

}
