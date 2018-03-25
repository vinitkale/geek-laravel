<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use App\User;
use App\Blog;
use App\Image;
use Datatables;
use App\BlogCategory;

class BlogController extends Controller {

    public function index(Request $request) {
            
         
         if($request->get('search')!=null){
            
            $search = $request->get('search');
                               
            $blog =   Blog::select(['blog_id', 'blog_title', 'blog_content', 'gm_blogs.status', 'gm_blogs.published_date as create_date', 'gm_images.name as thumb', 'users.username', 'title'])->leftJoin('users', 'gm_blogs.added_by', '=', 'users.user_id')->leftJoin('gm_images', 'gm_blogs.thumb', '=', 'gm_images.image_id')
             ->orWhere(function ($query) use ($search) {
                $query->where('blog_title','like','%'.$search.'%');
                $query->orWhere('username','like','%'.$search.'%');
                $query->orWhere('blog_content','like','%'.$search.'%');
            })
            ->paginate(2);

        }
        else{
        $search = '';    
        $blog = Blog::select(['blog_id', 'blog_title', 'blog_content', 'gm_blogs.status', 'gm_blogs.published_date as create_date', 'gm_images.name as thumb', 'users.username', 'title'])->leftJoin('users', 'gm_blogs.added_by', '=', 'users.user_id')->leftJoin('gm_images', 'gm_blogs.thumb', '=', 'gm_images.image_id')->paginate(2);
        }
        
        return view('blog.index', ['blog' => $blog,'search'=>$search]);
    }

    public function create() {
        $blog_category = BlogCategory::where('blog_category_status', 1)->orderBy('blog_category_id', 'desc')->get();
        $users = User::where('user_id', "!=", 1)->where('user_id', "!=", Auth::user()->user_id)->orderBy('user_id', 'desc')->get();
        return view('blog.create', ['user' => $users, 'category' => $blog_category]);
    }

    public function store(Request $request) {

        if ($request->isMethod('post')) {
            $inputs = $request->All();

            $rules = array(
                'blog_title' => 'required|unique:gm_blogs',
                'blog_content' => 'required',
                'published_date' => 'required',
                'status' => 'required',
            );
//            $messages = ['required' => 'this field is required'];
            $validator = app('validator')->make($inputs, $rules);
            if ($validator->fails()) {
                $request->flash();
                return redirect('blog/create')
                                ->withErrors($validator, 'blog');
            } else {

                if ($request->file('blog_image') != NULL) {

                    $imageTempName = $request->file('blog_image')->getPathname();
                    $imageName = str_random(10) . '_' . $request->file('blog_image')->getClientOriginalName();
                    $path = base_path() . '/uploads/blog/';
                    $request->file('blog_image')->move($path, $imageName);
                    $image_data = array(
                        'title' => $request->file('blog_image')->getClientOriginalName(),
                        'name' => $imageName,
                        'type' => 'blog',
                        'extension' => $request->file('blog_image')->getClientOriginalExtension(),
                        'content_type' => $request->file('blog_image')->getClientMimeType()
                    );
                    $image = Image::create($image_data);
                    $inputs['thumb'] = $image->image_id;
                }

                if (!empty($inputs['blog_category'])) {
                    foreach ($inputs['blog_category'] as $cat) {
                        $cate_res = BlogCategory::where('blog_category_name', $cat)->get();


                        if ($cate_res->isEmpty()) {
                            $category = new BlogCategory;
                            $category->blog_category_name = $cat;
                            $category->blog_category_status = 1;
                            $category->save();
                        }
                    }
                    $inputs['blog_category'] = implode(',', $inputs['blog_category']);
                }

                if ((array_key_exists('allow_comment', $inputs)) && ($inputs['allow_comment'] == 'on')) {
                    $inputs['allow_comment'] = 1;
                } else {
                    $inputs['allow_comment'] = 0;
                }



                $inputs['added_by'] = Auth::user()->user_id;


                $res = Blog::create($inputs);
                if ($res != FALSE) {
                    return redirect('blog')->with('success', 'Blog added successfully');
                } else {
                    return redirect('blog')->with('error', 'some thing went wrong');
                }
            }
        }
    }

    public function edit($id) {
        $blog = Blog::find($id);
        if (!empty($blog)) {
            $blog_category = BlogCategory::where('blog_category_status', 1)->orderBy('blog_category_id', 'desc')->get();
            $users = User::where('user_id', "!=", 1)->where('user_id', "!=", Auth::user()->user_id)->orderBy('user_id', 'desc')->get();
            $attch_data = array();
            $attch_info = '';
            if ($blog->blog_media != '') {
                $image_data = array();

                $images = explode(',', $blog->blog_media);

                $attch_info = array();
                foreach ($images as $image_val) {
                    $images_array = Image::find($image_val);


                    if (!empty($images_array)) {

                        $path = base_path() . '/uploads/blog/' . $images_array->name;
                        if (file_exists($path)) {

                            $type = explode('/', $images_array->content_type);
                            switch ($type[0]) {
                                case 'image':
                                    $src = asset('geekmeet/uploads/blog/' . $images_array->name);
                                    $title = $images_array->title;
                                    $alt = $images_array->title;
                                    $attch_data[] = '<img img_id = "' . $images_array->image_id . '" width="200px" featured_image="' . $images_array->featured_image . '" height="200px" src="' . $src . '" class="img-responsive" alt="' . $alt . '" title="' . $title . '">';
                                    $attch_info[] = array(
                                        'url' => url('delete_per_media'),
                                        'caption' => $images_array->title,
                                        'key' => $images_array->image_id,
                                    );
                                    break;

                                case 'video':
                                    $src = asset('geekmeet/uploads/blog/' . $images_array->name);
                                    $attch_data[] = '<video width="auto" height="160" controls> <source src="' . $src . '"></video>';
                                    $attch_info[] = array(
                                        'url' => url('delete_per_media'),
                                        'caption' => $images_array->title,
                                        'key' => $images_array->image_id,
                                    );
                                    break;
                                case 'audio':
                                    $src = asset('geekmeet/uploads/blog/' . $images_array->name);
                                    $attch_data[] = '<audio controls width="auto"> <source src="' . $src . '"></audio>';
                                    $attch_info[] = array(
                                        'url' => url('delete_per_media'),
                                        'caption' => $images_array->title,
                                        'key' => $images_array->image_id,
                                    );
                                    break;
                            }
                        }
                    }
                }

                $blog->images = $attch_data;
                $blog->attachment = json_encode($attch_info);
            }

            return view('blog.edit', ['blog' => $blog, 'user' => $users, 'category' => $blog_category]);
        }
    }

    public function update(Request $request) {
        if (method_field('PUT')) {
            $inputs = $request->All();
            $blog_id = $inputs['blog_id'];
            $rules = array(
                'blog_content' => 'required',
                'published_date' => 'required',
                'status' => 'required',
            );

            if ($request->input('blog_title') == $request->input('blog_old_title')) {
                $rules['blog_title'] = 'required';
            } else {
                $rules['blog_title'] = 'required|unique:gm_blogs';
            }
//            $messages = ['required' => 'this field is required'];
            $validator = app('validator')->make($inputs, $rules);
            if ($validator->fails()) {
                return redirect('blog/' . $blog_id . '/edit')
                                ->withErrors($validator, 'blog');
            } else {

                if ($request->file('blog_image') != NULL) {

                    $imageTempName = $request->file('blog_image')->getPathname();
                    $imageName = str_random(10) . '_' . $request->file('blog_image')->getClientOriginalName();
                    $path = base_path() . '/uploads/blog/';
                    $request->file('blog_image')->move($path, $imageName);
                    $image_data = array(
                        'title' => $request->file('blog_image')->getClientOriginalName(),
                        'name' => $imageName,
                        'type' => 'blog',
                        'extension' => $request->file('blog_image')->getClientOriginalExtension(),
                        'content_type' => $request->file('blog_image')->getClientMimeType()
                    );
                    $image = Image::create($image_data);
                    $inputs['thumb'] = $image->image_id;
                    if ($inputs['old_image'] != '') {
                        $image_del = Image::find($inputs['old_image']);
                        $path = base_path() . '/uploads/blog/';
                        if (!empty($image_del)) {
                            if (file_exists($path . $image_del->name)) {
                                unlink($path . $image_del->name);
                            }
                            $image_del->delete();
                        }
                    }
                } else {
                    $inputs['thumb'] = $inputs['old_image'];
                }


                if (!empty($inputs['blog_category'])) {
                    foreach ($inputs['blog_category'] as $cat) {
                        $cate_res = BlogCategory::where('blog_category_name', $cat)->get();


                        if ($cate_res->isEmpty()) {
                            $category = new BlogCategory;
                            $category->blog_category_name = $cat;
                            $category->blog_category_status = 1;
                            $category->save();
                        }
                    }
                    $inputs['blog_category'] = implode(',', $inputs['blog_category']);
                }

                if ((array_key_exists('allow_comment', $inputs)) && ($inputs['allow_comment'] == 'on')) {
                    $inputs['allow_comment'] = 1;
                } else {
                    $inputs['allow_comment'] = 0;
                }

                $inputs['added_by'] = Auth::user()->user_id;




                unset($inputs['old_image']);
                unset($inputs['_method']);
                unset($inputs['blog_old_title']);
                unset($inputs['_token']);
                unset($inputs['blog_image']);


                $res = Blog::where('blog_id', $blog_id)
                        ->update($inputs);
                if ($res != FALSE) {
                    return redirect('blog')->with('success', 'Blog updated successfully');
                } else {
                    return redirect('blog')->with('error', 'some thing went wrong');
                }
            }
        }
    }

    public function show($id) {
        $blog = Blog::select(['blog_id', 'blog_category', 'blog_media', 'blog_title', 'blog_content', 'gm_blogs.status', 'gm_blogs.published_date as create_date', 'gm_images.name as thumb', 'users.username', 'title'])->leftJoin('users', 'gm_blogs.added_by', '=', 'users.user_id')->leftJoin('gm_images', 'gm_blogs.thumb', '=', 'gm_images.image_id')->where('blog_id', $id)->get();
        if (!empty($blog)) {
            $blog = $blog[0];
            $attch_data = array();
            $attch_info = '';

            if ($blog->blog_media != '') {
                $image_data = array();

                $images = explode(',', $blog->blog_media);

                $attch_info = array();
                foreach ($images as $image_val) {
                    $images_array = Image::find($image_val);


                    if (!empty($images_array)) {

                        $path = base_path() . '/uploads/blog/' . $images_array->name;
                        if (file_exists($path)) {

                            $type = explode('/', $images_array->content_type);
                            switch ($type[0]) {
                                case 'image':
                                    $src = asset('geekmeet/uploads/blog/' . $images_array->name);
                                    $title = $images_array->title;
                                    $alt = $images_array->title;
                                    $attch_data[] = '<img img_id = "' . $images_array->image_id . '" width="200px" featured_image="' . $images_array->featured_image . '" height="200px" src="' . $src . '" class="img-responsive" alt="' . $alt . '" title="' . $title . '">';
                                    $attch_info[] = array(
                                        'caption' => $images_array->title,
                                        'key' => $images_array->image_id,
                                    );
                                    break;

                                case 'video':
                                    $src = asset('geekmeet/uploads/blog/' . $images_array->name);
                                    $attch_data[] = '<video width="auto" height="160" controls> <source src="' . $src . '"></video>';
                                    $attch_info[] = array(
                                        'caption' => $images_array->title,
                                        'key' => $images_array->image_id,
                                    );
                                    break;
                                case 'audio':
                                    $src = asset('geekmeet/uploads/blog/' . $images_array->name);
                                    $attch_data[] = '<audio controls width="auto"> <source src="' . $src . '"></audio>';
                                    $attch_info[] = array(
                                        'caption' => $images_array->title,
                                        'key' => $images_array->image_id,
                                    );
                                    break;
                            }
                        }
                    }
                }

                $blog->images = $attch_data;
                $blog->attachment = json_encode($attch_info);
            }

            return view('blog.show', ['blog' => $blog]);
        }
    }

    public function delete($id) {
        $blog = Blog::find($id);
        if ($blog->thumb != '') {
            $image_del = Image::find($blog->thumb);
            $path = base_path() . '/uploads/blog/';
            if (!empty($image_del)) {
                if (file_exists($path . $image_del->name)) {
                    unlink($path . $image_del->name);
                }
                $image_del->delete();
            }
        }
        if ($blog->delete()) {
            return redirect('blog')->with('success', 'Blog delete successfully');
        } else {
            return redirect('blog')->with('error', 'some thing went wrong');
        }
    }

    public function getBlogData() {

        $blog = Blog::select(['blog_id', 'blog_title', 'blog_content', 'gm_blogs.status', 'gm_blogs.published_date as create_date', 'gm_images.name as thumb', 'users.username', 'title'])->leftJoin('users', 'gm_blogs.added_by', '=', 'users.user_id')->leftJoin('gm_images', 'gm_blogs.thumb', '=', 'gm_images.image_id')->get();



        return Datatables::of($blog)
                        ->addColumn('action', function ($blog) {
                            return '<a title="edit"  href="blog/' . $blog->blog_id . '/edit" class="btn btn-xs btn-info"><i class="glyphicon glyphicon-edit"></i> Edit</a>&nbsp;&nbsp; <a title="delete" href="javascript:void(0)" data-href="' . url('blog/delete/' . $blog->blog_id) . '" class="btn btn-xs btn-danger delete" data-msg="blog"><i class="glyphicon glyphicon-trash"></i> Delete</a>';
                        })
                        ->editColumn('status', function ($blog) {
                            if ($blog->status == 'active') {
                                return "<span class='label label-success'>Active</span>";
                            } else {
                                return "<span class='label label-default'>Deactive</span>";
                            }
                        })
                        ->editColumn('thumb', function ($blog) {
                            $src = asset('geekmeet/uploads/blog/' . $blog->thumb);
                            $title = $blog->title;
                            $alt = $blog->title;
                            return '<img width="150px" height="150px" src="' . $src . '" class="img-responsive" alt="' . $alt . '" title="' . $title . '">';
                        })
                        ->editColumn('create_date', function ($event) {
                            $date_obj = date_create($event->create_date);
                            return date_format($date_obj, 'F j, Y');
                        })
                        ->make(true);
    }

    public function upload_media(Request $request) {

        if ($request->file('image') != NULL) {

            $imageTempName = $request->file('image')->getPathname();

            $imageName = str_random(10) . '_' . $request->file('image')->getClientOriginalName();
            $path = base_path() . '/uploads/blog/';
            $request->file('image')->move($path, $imageName);
            $image_data = array(
                'title' => $request->file('image')->getClientOriginalName(),
                'name' => $imageName,
                'type' => 'blog',
                'extension' => $request->file('image')->getClientOriginalExtension(),
                'content_type' => $request->file('image')->getClientMimeType()
            );

            $image = Image::create($image_data);
            echo $image->image_id;
            die;
        }
    }

    public function delete_media(Request $request) {

        if ($request->input('id') != NULL) {
            if (strpos($request->input('id'), ',') > 0) {
                $ids = explode(',', $request->input('id'));
                foreach ($ids as $val) {
                    $image_del = Image::find($val);
                    $path = base_path() . '/uploads/blog/';
                    if (!empty($image_del)) {
                        if (file_exists($path . $image_del->name)) {
                            unlink($path . $image_del->name);
                        }
                        $image_del->delete();
                    }
                }
            } else {
                $image_del = Image::find($request->input('id'));
                $path = base_path() . '/uploads/blog/';
                if (!empty($image_del)) {
                    if (file_exists($path . $image_del->name)) {
                        unlink($path . $image_del->name);
                    }
                    $image_del->delete();
                }
            }
            echo json_encode(array("status" => 1));
        }
    }

    public function delete_per_media(Request $request) {

        if ($request->input('key') != NULL) {

            $image_del = Image::find($request->input('key'));
            $path = base_path() . '/uploads/blog/';
            if (!empty($image_del)) {
                if (file_exists($path . $image_del->name)) {
                    unlink($path . $image_del->name);
                }
                $image_del->delete();
            }
        }
        echo json_encode(array("status" => 1));
    }

    public function download_media($id) {

        if ($id != NULL) {

            $image_del = Image::find($id);
            $path = base_path() . '/uploads/blog/';
            if (!empty($image_del)) {
                if (file_exists($path . $image_del->name)) {
                    $filename = $path . $image_del->name;

                    header("Cache-Control: public");
                    header('Content-Type: ' . $image_del->content_type);
                    header("Content-Description: File Transfer");
                    header('Content-disposition: attachment;filename=' . basename($filename));
                    header("Content-Transfer-Encoding: binary");
                    header('Content-Length: ' . filesize($filename));
                    readfile("$filename");
                }
            }
        }
    }

    public function seacrh(Request $request) {
    
        if ($request->input('search') != '') {
            $blog = Blog::select(['blog_id', 'blog_title', 'blog_content', 'gm_blogs.status', 'gm_blogs.published_date as create_date', 'gm_images.name as thumb', 'users.username', 'title'])->leftJoin('users', 'gm_blogs.added_by', '=', 'users.user_id')->leftJoin('gm_images', 'gm_blogs.thumb', '=', 'gm_images.image_id')->paginate(2);
            return view('blog.index', ['blog' => $blog]);
        }else{
          $blog = Blog::select(['blog_id', 'blog_title', 'blog_content', 'gm_blogs.status', 'gm_blogs.published_date as create_date', 'gm_images.name as thumb', 'users.username', 'title'])->leftJoin('users', 'gm_blogs.added_by', '=', 'users.user_id')->leftJoin('gm_images', 'gm_blogs.thumb', '=', 'gm_images.image_id')->paginate(2);
           return view('blog.index', ['blog' => $blog]);  
        }
        
   
    }

}
