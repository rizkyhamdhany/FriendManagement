<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\CC;
use App\Rest;
use App\Relation;
use App\FR;

class FriendController extends Controller
{

    public function __construct()
    {

    }

    public function index(){
        return User::select('name', 'email')->get();
    }

    public function friendConnect(Request $request){
        $friends = $request->input("friends");
        if(!isset($friends) || count($friends) != 2){
            return Rest::badRequest();
        }
        $user1 = User::where('email', $friends[0])->first();
        if (!isset($user1)){
            return Rest::dataNotFound('User '.$friends[0].' not found !');
        }
        $user2 = User::where('email', $friends[1])->first();
        if (!isset($user2)){
            return Rest::dataNotFound('User '.$friends[1].' not found !');
        }
        if ($user1->id == $user2->id){
            return Rest::badRequest();
        } elseif ($user1->id > $user2->id){
            $user = $user1;
            $user1 = $user2;
            $user2 = $user;
        }
        $relations = Relation::where('first_user_id',$user1->id)->where('second_user_id',$user2->id)->first();
        if (isset($relations)){
            return Rest::badRequestWithMsg('Already become friend !');
        }
        $relations = new Relation();
        $relations->first_user_id = $user1->id;
        $relations->second_user_id = $user2->id;
        $relations->status = FR::Friend;
        $relations->save();
        return Rest::insertSuccess();
    }
}
