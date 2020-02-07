import React from 'react';
import classNames from "classnames";
import './App.css';
import {  EmailService } from "./email/service";


class Message extends React.Component {
    visible = true;
    errors = [];

    isLoading() {
        return this.props.loading;
    }

    hide() {
        this.props.visible = false;
    }

    show() {
        this.props.visible = true;
    }

    isError() {
        return !this.isLoading() && this.props.type === 'error';
    }

    isSuccess() {
        return !this.isLoading() && this.props.type === 'success';
    }

    isWarning() {
        return !this.isLoading() && this.props.type === 'warning';
    }

    isNotice() {
        return !this.isLoading() && this.props.type === 'notice';
    }

    reset() {
        this.props.message = '';
    }

    constructor(props) {
        super(props);
    }
    
    render() {
        console.log(this.props);
        if(this.props.message && this.visible) {
            return <div className={classNames({'text-danger': this.isError(), 'text-success': this.isSuccess()})}>
                <i className={classNames("fas",{'fa-circle-notch fa-spin': this.isLoading(), 'fa-check': this.isSuccess(), 'fa-exclamation-circle text-warning': this.isNotice(), 'fa-ban': this.isError()})}></i>&nbsp;<span>{this.props.message}</span>
                {this.errors.map((error, index) => (
                    <div key={index}>
                        <i className="fas fa-fw fa-exclamation-circle"></i>&nbsp;{ error }
                    </div>
                ))}
            </div>
        }
        return <div></div>    
    }
}

class Register extends React.Component {
    state = {
        email: '',
        password: '',
        password_confirm: '',
        first_name: '',
        last_name: '',
        validation: {
            email: {
                message: '',
                message_type: '',
                loading: false,
                exists: false,
                registered: false,
                valid: false
            }
        }
    }

    authenticated = false;
	password = {
		type: 'password'
	};

	password_confirm = {
		type: 'password'
	};

    isEmailNew() {
        return this.state.validation.email.valid && (!this.isEmailRegistered() && !this.isEmailDuplicate());
    }
    isEmailDuplicate() {
        return this.state.validation.email.exists;
    }
    isEmailRegistered() {
        return this.state.validation.email.registered;
    }
    isEmailLoading() {
        return this.state.validation.email.loading;
    }
    resetEmail() {
        this.state.validation.email.registered = false;
        this.state.validation.email.exists = false;
        this.state.validation.email.valid = false;
        this.state.validation.email.message = '';
    }

    
	form = {
		loading: false,
		submitted: true,
		error: true,
		errors: ['Some error'],
		message: 'There were errors',
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

    login = () => {

    }

    onEmailKeyup = (e) => {
        let _this = this;
        _this.setState({
            email: e.target.value,
            validation: {
                email: {
                    loading: true,
                    message: "Checking...",
                    message_type: 'notice'
                }
            }
        });
        clearTimeout(this.timer);
        var emailService = new EmailService();
		_this.timer = setTimeout(function() {
            emailService.check(_this.state.email).then((response) => {
                console.log(response);

                _this.setState({
                    validation: {
                        email: {
                            loading: false,
                            message: response.getMessage(),
                            valid: response.isSuccessful()
                        }
                    }
                });

                console.log(_this.state.validation.email.message);
                if(!response.isSuccessful()) {
                    if(response.getParam('duplicate')) {
                        _this.setState({
                            validation: {
                                email: {
                                    valid: true,
                                    exists: true,
                                    registered: response.getParam('registered') ? true : false
                                }
                            }
                        })
                    }
                }
                if(_this.isEmailRegistered())
                    _this.state.validation.email.message += ' Login to proceed.';
                else if(_this.isEmailDuplicate())
                    _this.state.validation.email.message += ' You may choose a password now.';
                
                if(_this.isEmailLoading()) {
                    _this.state.validation.email.message_type = 'notice';
                } else if(!_this.state.validation.email.valid) 
                    _this.state.validation.email.message_type = 'error';
                else if(_this.isEmailNew())
                    _this.state.validation.email.message_type = 'success';
                else
                    _this.state.validation.email.message_type = 'notice';
            });

		}, 500);
    }
    

    render() {
        return <div><div className={classNames("padding-sm", 'alert', {
                        'hidden': !this.form.isSubmitted() || !this.form.message,
                        'alert-danger': !this.form.isLoading() && this.form.hasError(),
                        'alert-success': !this.form.isLoading() && !this.form.hasError()
                    })}>
                <div style={{"fontSize":"18px"}}>
                    <i className={classNames("fas fa-fw", {'fa-circle-notch fa-spin': this.form.isLoading(), 'fa-check': !this.form.hasError() && !this.form.isLoading(), 'fa-ban': this.form.hasError() && !this.form.isLoading() })}></i>&nbsp;<span>{this.form.message}</span>
                </div>
                {this.form.errors.map((error, index) => (
                    <div key={index}>
                        <i className="fas fa-fw fa-exclamation-circle"></i>&nbsp;{ error }
                    </div>
                ))}
            </div>
            <form id="user-register-form" className="margin-top-xl">
                <div className="user-account-lookup">
                    <div className="form-group">
                        <label className="control-label left">
                            Email
                        </label>
                        <div className="margin-bottom-sm">
                            <input onChange={this.onEmailKeyup} name="email" value={this.state.email} className="form-control" placeholder="Enter your email to see if you already exist..."/>
                        </div>
                        <Message message={this.state.validation.email.message} loading={this.state.validation.email.loading} type={this.state.validation.email.message_type}></Message>
                    </div>
                </div>
                <div className={classNames("user-password-container", {'hidden': !this.state.validation.email.valid})}>
                    <div className="form-group">
                        <label className="control-label left">
                            Password <small className="text-muted">(8 character min, Contains a-z, A-Z, 0-9)</small>
                        </label>
                        <div>
                            <input className="form-control" name="password" type={this.password.type} style={{'width':'auto', 'display':'inline-block'}} value={this.state.password} />&nbsp;&nbsp;<div className={classNames("btn", "btn-sm", "clickable", {'btn-primary': this.password.type === 'password', 'btn-secondary': this.password.type === 'text'})} onClick={this.togglePassword} ><i className="fa fa-eye"></i></div>
                        </div>
                        <a className="hidden password-reset-button clickable" data-action="user.forgot.password">Forgot Password?</a>
                    </div> 
                </div>
            
                <div className={classNames("user-password-confirm-container", {'hidden': !this.state.validation.email.valid || this.isEmailRegistered()})}>
                    <div className="form-group">
                        <label className="control-label left">
                            Confirm Password
                        </label>
                        <div>
                            <input className="form-control" name="password_confirm" type={this.password_confirm.type} style={{'width':'auto', 'display':'inline-block'}} value={this.state.password_confirm} />&nbsp;&nbsp;<div className={classNames("btn btn-sm clickable", {'btn-primary': this.password_confirm.type === 'password', 'btn-secondary': this.password_confirm.type === 'text'})} onClick={this.togglePasswordConfirm}><i className="fa fa-eye"></i></div>
                        </div>
                    </div> 
                </div>
            
                <div className={classNames("user-login", {'hidden': !this.isEmailRegistered()})}>
                    <div className={classNames("btn btn-md btn-primary clickable", {'disabled': this.form.isLoading()})} onClick={this.login()}>Login <i className="fa fa-play"></i></div>
                </div>
            
                <div className={classNames("user-set-password-and-login-container", {'hidden': !this.isEmailDuplicate() || this.isEmailRegistered()})}>
                    <div className={classNames("btn btn-md btn-primary clickable", {'disabled': this.form.isLoading()})} onClick={this.registerPassword}>Set Password & Login <i className="fa fa-play"></i></div>
                </div>
            
                <div className={classNames("user-registration", {'hidden': !this.isEmailNew()})}>
                    <div className="form-group">
                        <label className="control-label left">
                            First Name
                        </label>
                        <div>
                            <input name="first_name" className="form-control" value={this.state.first_name} />
                        </div>
                    </div>
                    <div className="form-group">
                        <label className="control-label left">
                            Last Name
                        </label>
                        <div>
                            <input name="last_name" className="form-control" value={this.state.last_name}/>
                        </div>
                    </div>
            
                    <div className={classNames("btn btn-md btn-primary clickable", {'disabled': this.form.isLoading()})} onClick={this.register}>Register <i className="fa fa-play"></i></div>
                </div> 
                  
            </form> </div>
    }
}


class Login extends React.Component {
    render() {
        let containerClass = {
            'boxShadow': 'none !important', 
            'background': 'none !important'
        };

        let mainContainerClass = {
            'padding': '28px;background: #fff',
            'marginTop': '50px'
        };

        return <div className="container" style={containerClass}>
            <div className="row justify-content-center">
                <div className="col-sm-10 col-sm-offset-1 col-md-6 col-md-offset-3">
                    <div className="main shadowed rounded-lg" style={mainContainerClass}>
                        <div className="row margin-bottom-xl">
                            <div className="col-sm-9">
                                <h1 className="no-margin page-title">Welcome!</h1>
                            </div>
                        </div>
                        <Register />
                    </div>
                </div>
            </div>
        </div>
    }
}

class Nav extends React.Component {
    render() {
        let navbarStyle = {
            'backgroundColor': '#d8dee4',
            'boxShadow': '0 4px 2px -2px #a0a1ab'
        }

        return <nav className="navbar navbar-expand-lg navbar-light" style={navbarStyle}>
            <a className="navbar-brand"><h4 className="no-margin"><i className="fas fa-coffee"></i>&nbsp;Caffeinator</h4></a>
            <button className="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span className="navbar-toggler-icon"></span>
            </button>
            <div className="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div className="navbar-nav">
                    <a className="nav-item nav-link">Login<span className="sr-only">(current)</span></a>
                    <a className="nav-item nav-link">Logout<span className="sr-only">(current)</span></a>
                    <a className="nav-item nav-link">Calculator<span className="sr-only">(current)</span></a>
                </div>
            </div>
        </nav>
    }
}

class App extends React.Component {
  
  render() {
    return <div className="App">
        <Nav />
        <Login />
    </div>
  }
}

export default App;
