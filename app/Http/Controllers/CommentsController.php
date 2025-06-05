<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Response;
use App\Models\Comment;
use Illuminate\Support\Facades\Validator;

class CommentsController extends Controller
{
    public function getAllUserComments(){

        return Response::push([
            'comments' => request()->user->comments
        ] , 200 , 'Success');

    }

    public function deleteComment($commentId){

        $comment = request()->user->comments()->whereId($commentId)->first();

        if ($comment){

            $comment->delete();

            return Response::push([] , 200 , 'Comment Deleted Success');

        }

        return Response::push([] , 404 , 'Comment not Found');


    }



    public function updateComment($commentId){
        
        $check = Validator::make(request()->only('comment') , [
            'comment' => ['required']
        ]);


        if ($check->fails()){
            return Response::push([
                'errors' => $check->errors()
            ]);
        }

        $comment = request()->user->comments()->whereId($commentId)->first();

        if ($comment){

            $comment->update([
                'comment' => request()->comment
            ]);

            return Response::push([
                'comment' => $comment
            ] , 200 , 'Comment Updated Success');

        }

        return Response::push([] , 404 , 'Comment not Found');

    }


}
