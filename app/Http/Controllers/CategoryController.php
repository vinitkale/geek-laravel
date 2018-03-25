<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Category;
use Datatables;
use Auth;
class CategoryController extends Controller {

  
    
    public function index() {
       
        return view('category.index');
    }

    public function create() {
        return view('category.create');
    }

    public function store(Request $request) {
        if ($request->isMethod('post')) {
            $category_post = array(
                'category_name' => $request->input('category_name'),
                'category_status' => $request->input('category_status')
            );

            $rules = array(
                'category_name' => 'required|max:255|unique:gm_event_category',
                'category_status' => 'required'
            );
//            $messages = ['required' => 'this field is required'];
            $validator = app('validator')->make($category_post, $rules);
            if ($validator->fails()) {
                return redirect('category/create')
                                ->withErrors($validator, 'category');
            } else {
                $category = new Category;
                $category->category_name = $request->category_name;
                $category->category_status = $request->category_status;
                if ($category->save()) {
                    return redirect('category')->with('success', 'Category added successfully');
                } else {
                    return redirect('category')->with('error', 'some thing went wrong');
                }
            }
        }
    }

    public function edit($id) {
        $category = Category::find($id);
        return view('category.edit', ['category' => $category]);
    }

    public function update(Request $request) {
        if (method_field('PUT')) {
            $category_put = array(
                'category_name' => $request->input('category_name'),
                'category_status' => $request->input('category_status')
            );
            $category_id = $request->input('category_id');
            if ($request->input('category_name') == $request->input('old_category_name')) {
                $rules['category_name'] = 'required|max:255';
            } else {
                $rules['category_name'] = 'required|max:255|unique:gm_event_category';
            }
//            $messages = ['required' => 'this field is required'];
            $validator = app('validator')->make($category_put, $rules);
            if ($validator->fails()) {
                return redirect("category/$category_id/edit")
                                ->withErrors($validator, 'category');
            } else {
                Category::where('category_id', $category_id)
                        ->update($category_put);

                return redirect('category')->with('success', 'Category update successfully');
            }
        }
    }

    public function delete($id) {
        $category = Category::find($id);
        if($category->delete()){
          return redirect('category')->with('success', 'Category delete successfully');   
        }else{
           return redirect('category')->with('error', 'some thing went wrong');  
        }
    }

    public function getCategoryData() {


        $category = Category::select(['category_id', 'category_name', 'category_status']);

        return Datatables::of($category)
                        ->addColumn('action', function ($category) {
                            return '<a href="category/' . $category->category_id . '/edit" class="btn btn-xs btn-info"><i class="glyphicon glyphicon-edit"></i> Edit</a>&nbsp;&nbsp; <a href="javascript:void(0)" data-href="category/delete/' . $category->category_id . '" class="btn btn-xs btn-danger delete" data-msg="category"><i class="glyphicon glyphicon-trash"></i> Delete</a>';
                        })
                        ->editColumn('category_status', function ($category) {
                            if ($category->category_status == 1) {
                                return "<span class='label label-success'>Active</span>";
                            } else {
                                return "<span class='label label-default'>Deactive</span>";
                            }
                        })
                        ->make(true);
    }

}
