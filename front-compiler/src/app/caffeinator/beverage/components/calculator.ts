import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { BeverageModel } from '../beverage.model';
import { BeverageService } from '../beverage.service';
import { SearchResult } from 'src/app/utilities/search-result';
import { UserService } from 'src/app/user/user.service';

@Component({
    selector: 'app-budget-dashboard',
    templateUrl: '../templates/calculator.component.html'
})
export class Calculator implements OnInit {
    beverage_search: SearchResult;
    beverage: BeverageModel;
    safe_limit = 500;
    
    drinks = {
        amount: null,
        message: '',
        message_type: ''
    };

    result = {
        consumed_caffeine: null,
        over_limit: 0,
        remaining_drinks: 0,
        possible_drinks: []
    };

    constructor(private userService: UserService, private service: BeverageService, private router: Router, private route: ActivatedRoute) { }
    
    ngOnInit() {
        var _this = this;
        _this.beverage_search = new SearchResult;

        // if(!_this.userService.isAuthenticated())
        //     _this.router.navigateByUrl('/login');

        this.service.search({}).subscribe((searchResult: SearchResult) => {
            _this.beverage_search = searchResult;
        }, (error) => {
            if(error.status == 401) {
                localStorage.removeItem('token');
                _this.router.navigateByUrl('/login');
            }
        });
    }

    resetAmount() {
        var _this = this;
        _this.drinks.amount = null;
        _this.drinks.message = '';
        _this.drinks.message_type = 'success';
    }

    resetResults() {
        var _this = this;
        _this.result.over_limit = 0;
        _this.result.consumed_caffeine = null;
        _this.result.remaining_drinks = null;
        _this.result.possible_drinks = [];
    }

    onBeverageSelected(event: any) {
        var _this = this;

        var items = _this.beverage_search.getItems();
        _this.beverage = items.find(x => x.id == event.target.value);
        _this.resetAmount();
        _this.resetResults();
    }

    validateDrinks() {
        var _this = this;
        _this.drinks.message = "";
        _this.drinks.message_type = "";

        if(_this.drinks.amount == '')
            return false;
        var check = Number(_this.drinks.amount);
        if(isNaN(check)) {
            _this.drinks.message = "You must enter an integer";
            _this.drinks.message_type = 'error';
            return false;
        } else {
            return true;
        }
    }

    onAmountKeyup(event: any) {
        var _this = this;
        _this.resetResults();
        if(_this.validateDrinks())
            _this.generateResults();     
    }

    generateResults() {
        var _this = this;

        _this.result.possible_drinks = [];
        _this.result.remaining_drinks = 0;
        _this.result.consumed_caffeine = _this.beverage.getCaffeine(_this.drinks.amount);
        
        if(_this.result.consumed_caffeine > _this.safe_limit)
            _this.result.over_limit = _this.result.consumed_caffeine - _this.safe_limit;

        var remaining_caffeine = _this.safe_limit - _this.result.consumed_caffeine;
        if(remaining_caffeine > 0) {
            _this.result.remaining_drinks = _this.beverage.getRemaining(remaining_caffeine);

            for(let beverage of _this.beverage_search.getItems()) {
                var available = beverage.getRemaining(remaining_caffeine);
                if(available) {
                    _this.result.possible_drinks.push({beverage: beverage, caffeine: beverage.getCaffeine(available), remaining: available});
                }
            }
        }
    }
}