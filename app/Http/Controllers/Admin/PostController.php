<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Post;


class PostController extends Controller
{

  /**
   * Displays a list of all posts.
   *
   * This function is used to display the list of all posts.
   * It fetches all the posts from the database and
   * passes them to the view.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $posts = Post::orderBy('Id', 'Desc')->get();

    return view('Admin.post.postlist', compact('posts'));
  }
}
// Close
