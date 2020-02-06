<?php
namespace App\Module\Beverage\Controller;

use Illuminate\Http\Request;

class BeverageController extends \App\Http\Controllers\Controller
{

    protected function process(Request $request) {
        $query = \App\Module\Beverage\Model\Beverage::query();

        $loggedUser = $request->loggedUser;
        
		if(!$itemsPerPage = $request->input('items_per_page')) {
			$itemsPerPage = 10;
		}
		
		$page=1;
		if($currentPage = $request->input('page')) {
			if(is_numeric($currentPage) && $currentPage > 1) {
				$page = $currentPage;
			}
		}

		if($sort = $request->input('sort')) {
			if(!$sortDirection = $request->input('sort_direction')) {
				$sortDirection = 'ASC';
			}
		} else {
			$sort = 'id';
			$sortDirection = 'DESC';
		}

		// Use the Eloquent models and query builder to retrieve data sets from the database.
		// if uncertain about how eloquent models work, see the laravel documentation found here:
		// https://laravel.com/docs/4.2/eloquent
		$query = $query->orderBy($sort, $sortDirection);

		// add additional searching requirements into this part of the logic
		if($search = $request->input('search')) {

		} else {
			$search = '';
        }

		// before applying the limiting clauses to the query we need to retrieve the total result set for 
		// handling the pagination.
		$total = $query->count();

		// Apply the final limitations on the query and retrieve the result.  Mysql is based on 0 starting indexes so we 
		// need to subtract 1 from the page number in order to start at record 0.
		$query = $query->skip( ($page-1) * $itemsPerPage)->take($itemsPerPage);
		$items = $query->get();

		// determine the total number of pages based on the total result set and the number of items
		// given per page.
		$totalPages = ceil($total / $itemsPerPage); 

		return [
			'items' => $items,
			'page' => $page,
			'total_pages' => $totalPages,
            'items_per_page'=>$itemsPerPage,
            'count'=>$total
		];
	}	

	function search(Request $request)
	{
		$data = $this->process($request);
        if($request->input('res_format', 'json') == 'json')
            return response()->json(['status'=>'success', 'data'=>$data, 'message'=>'Retrieved beverages']);
	}

    function find(Request $request) {
        $id = $request->input('id', null);
        $item = \App\Module\Beverage\Model\Beverage::find($id);
        if($request->input('res_format', 'json') == 'json')
            return response()->json(['status'=>'success', 'data'=>$item, 'message'=>'Retrieved budget']);
    }

	function details(Request $request) {
        $loggedUser = $request->loggedUser;

        $budget_id = $request->input('id', null);
        if(!$item = \App\Module\Beverage\Model\Beverage::find($budget_id))
            throw new Exception('No Campaign located for the ID: '.$budget_id);

        return view('tracking.budget.details', [
                'item'=>$item,
                'loggedUser'=>$loggedUser
            ]);
	}
	
	public function form(Request $request) {
        $loggedUser = $request->loggedUser;
				
		// this section determines if the form will be updating an existing record or inserting a new one
		$budget_id = $request->input('id', null);
        if(!$item = \App\Module\Beverage\Model\Beverage::find($budget_id))
            $item = new \App\Module\Beverage\Model\Beverage;   
        
        return view('tracking.budget.form', [
            'item'=> $item,
            'loggedUser'=>$loggedUser
		]);
	}
    
    public function save(Request $request) {
        $response = [ 'status'=>STATUS_SUCCESS, 'message'=>'Campaign successfully saved.', 'data'=>[] ];

        $loggedUser = $request->loggedUser;

        try {
            // setup the validation schema for the form to validate against
            $rules = \App\Module\Beverage\Model\Beverage::getRules();
            
            // the standard messages aren't really messages at all so I am specifying the errors directly for each
            // of the validations I want to apply for the fields.
            // $messages = \App\Module\Beverage\Model\Beverage::getMessages();

            app('validator')->make($request->all(), $rules)->validate();

            $budget_id = $request->input('id', null);
            if(!$item = \App\Module\Beverage\Model\Beverage::find($budget_id))
                $item = new \App\Module\Beverage\Model\Beverage;

            $item->fill($request->all());
            $item->save();
        }
        catch(\Illuminate\Validation\ValidationException $error) {    
            $response ['status'] = STATUS_ERROR;
            $response ['data'] = $error->validator->messages()->all();
            $response ['message'] = $error->getMessage() . "\n" . implode("\n", $response ['data']);            
        }
        catch (Exception $e) {
            $response['status'] = STATUS_ERROR;
            $response['message'] = $e->getMessage();
            $response['data'] = \App\Helpers\Exception::normalize($e);
        }
        
        return response()->json($response);
    }

    public function remove(Request $request) {
        $response = [ 'status'=>STATUS_SUCCESS, 'message'=>'Successfully deleted budget.', 'data'=>[] ];
        $loggedUser = $request->loggedUser;

        try {
            if(!$loggedUser->hasRole('Administrator'))
                throw new \Exception("Must be an administrator to manage campaigns");

            $budget_id = $request->input('id', null);
            if(!$budget = \App\Module\Beverage\Model\Beverage::find($budget_id))
                throw new \App\Exceptions\MissingRecordException("No Campaign with id `{$budget_id}`");
            
            $budget->delete();
        } 
        catch(ValidationException $error) {          
            $response ['status'] = STATUS_ERROR;
            $response ['data'] = $error->validator->messages()->all();
            $response ['message'] = $error->getMessage() . "\n" . implode("\n", $response ['data']);            
        }
        catch(\Exception $error) {
            $response ['status'] = STATUS_ERROR;
            $response ['message'] = $error->getMessage();            
        }

        return response()->json($response);
    }
    
}