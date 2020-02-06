import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders, HttpParams } from '@angular/common/http';
import { Observable, of } from 'rxjs';
import { flatMap } from 'rxjs/operators';
import { Response } from '../../http/response';
import { BeverageModel } from './beverage.model';
import { SearchResult } from '../../utilities/search-result';
import { environment } from '../../../environments/environment';


@Injectable({
	providedIn: 'root'
})
export class BeverageService {
    private searchPath = environment.api_host + '/api/beverage/search';
    private findPath = environment.api_host + '/api/beverage/find';
    private model = BeverageModel;
	
	constructor(private http: HttpClient) {}

    map(items) {
        return items.map((budget: BeverageModel) => new BeverageModel().deserialize(budget))
    }

    find(id): Observable<BeverageModel> {
        var _this = this;
        var data = { id: id };
        var token =localStorage.getItem('token');
        if(token)
            data['jwt'] = token;

        return this.http.get(this.findPath, { params: data }).pipe(
            flatMap((res: object) => {
                return of(new Response(res));
            }),
            flatMap((res: Response) => {
                var data = res.getData();
                return of(new BeverageModel().deserialize(data));
            })
        );
    }

	search(data: {}): Observable<SearchResult> {
        var _this = this;
        var token =localStorage.getItem('token');
        if(token)
            data['jwt'] = token;

		return this.http.get(this.searchPath, { params: data}).pipe(
			flatMap((res: object) => {
				return of(new Response(res));
            }),
            flatMap((res: Response) => {
                var searchResult = new SearchResult();
                searchResult.setPage(res.getParam('page'));
                searchResult.setItemsPerPage(res.getParam('items_per_page'));
                searchResult.setTotal(res.getParam('count'));
                var items = res.getParam('items');
                searchResult.items = _this.map(items);
                return of(searchResult);
            })
		);
	}
}
