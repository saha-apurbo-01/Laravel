<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthorController extends Controller
{
    function author_register(Request $request){
        Author::insert([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>bcrypt($request->password),
            'created_at'=>Carbon::now(),
        ]);
        return back()->with('reg_success', 'Registration Success!');
    }
    function author_login(Request $request){
        if(Author::where('email', $request->email)->exists()){
            if(Auth::guard('author')->attempt(['email'=> $request->email, 'password'=> $request->password])){
                if(Auth::guard('author')->user()->status != 1){
                    Auth::guard('author')->logout();
                    return back()->with('pending', 'Your account is waiting for approval!');
                }
                else{
                    return redirect('/');
                }
            }
            else{
                return back()->with('correct', 'Login attempt incorrect!');
            }
        }
        else{
            return back()->with('exist', 'Email does not exist');
        }
    }
    function author_logout(){
        Auth::guard('author')->logout();
        return redirect('/');
    }
    function author_dashboard(){
        return view('fontend.author.author');
    }

    function authors(){
        $authors = Author::all();
        return view('fontend.author.authors', compact('authors'));
    }
    function authors_status($author_id){
        $author = Author::find($author_id);
        if($author->status == 1){
            Author::find($author_id)->update([
                'status'=> 0,
            ]);
            return back();
        }
        else{
            Author::find($author_id)->update([
                'status'=> 1,
            ]);
            return back(); 
        }
    }
    function authors_edit(){
        return view('fontend.author.edit');
    }
}
