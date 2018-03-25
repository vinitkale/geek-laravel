<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\BlogCategory;
use Datatables;
use Auth;
class BlogCategoryController extends Controller {

  
    
    public function index() {
       
        return view('blog_category.index');
    }

    public function create() {
        return view('blog_category.create');
    }

    public function store(Request $request) {
        if ($request->isMethod('post')) {
            $category_post = array(
                'blog_category_name' => $request->input('blog_category_name'),
                'blog_category_status' => $request->input('blog_category_status')
            );

            $rules = array(
                'blog_category_name' => 'required|max:255|unique:gm_blog_category',
                'blog_category_status' => 'required'
            );
//            $messages = ['required' => 'this field is required'];
            $validator = app('validator')->make($category_post, $rules);
            if ($validator->fails()) {
                return redirect('blog_category/create')
                                ->withErrors($validator, 'category');
            } else {
                $blog_category = new BlogCategory;
                $blog_category->blog_category_name = $request->blog_category_name;
                $blog_category->blog_category_status = $request->blog_category_status;
                if ($blog_category->save()) {
                    return redirect('blog_category')->with('success', 'Category added successfully');
                } else {
                    return redirect('blog_category')->with('error', 'some thing went wrong');
                }
            }
        }
    }

    public function edit($id) {
        $category = BlogCategory::find($id);
        return view('blog_category.edit', ['category' => $category]);
    }

    public function update(Request $request) {
      
        if (method_field('PUT')) {
            $category_put = array(
                'blog_category_name' => $request->input('blog_category_name'),
                'blog_category_status' => $request->input('blog_category_status')
            );
            $category_id = $request->input('category_id');
            if ($request->input('blog_category_name') == $request->input('old_category_name')) {
                $rules['blog_category_name'] = 'required|max:255';
            } else {
                $rules['blog_category_name'] = 'required|max:255|unique:gm_blog_category';
            }
//            $messages = ['required' => 'this field is required'];
            $validator = app('validator')->make($category_put, $rules);
            if ($validator->fails()) {
                return redirect("blog_category/$category_id/edit")
                                ->withErrors($validator, 'category');
            } else {
                BlogCategory::where('blog_category_id', $category_id)
                        ->update($category_put);

                return redirect('blog_category')->with('success', 'Category update successfully');
            }
        }
    }

    public function delete($id) {
        $category = BlogCategory::find($id);
        if($category->delete()){
          return redirect('blog_category')->with('success', 'Blog category delete successfully');   
        }else{
           return redirect('blog_category')->with('error', 'some thing went wrong');  
        }
    }

    public function getBlogCategoryData() {


        $blogcategory = BlogCategory::select(['blog_category_id', 'blog_category_name', 'blog_category_status']);

        return Datatables::of($blogcategory)
                        ->addColumn('action', function ($blogcategory) {
                            return '<a href="blog_category/' . $blogcategory->blog_category_id . '/edit" class="btn btn-xs btn-info"><i class="glyphicon glyphicon-edit"></i> Edit</a>&nbsp;&nbsp; <a href="javascript:void(0)" data-href="blog_category/delete/' . $blogcategory->blog_category_id . '" class="btn btn-xs btn-danger delete" data-msg="blog category"><i class="glyphicon glyphicon-trash"></i> Delete</a>';
                        })
                        ->editColumn('blog_category_status', function ($blogcategory) {
                            if ($blogcategory->blog_category_status == 1) {
                                return "<span class='label label-success'>Active</span>";
                            } else {
                                return "<span class='label label-default'>Deactive</span>";
                            }
                        })
                        ->make(true);
    }

}
