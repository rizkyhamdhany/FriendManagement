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
        $user1->friend_count = $user1->friend_count + 1;
        $user1->save();
        $user2->friend_count = $user2->friend_count + 1;
        $user2->save();
        return Rest::insertSuccess();
    }

    public function friendList(Request $request){
        $email = $request->input('email');
        if (!isset($email)){
            return Rest::badRequest();
        }
        $user = User::where('email', $email)->first();
        if (!isset($user)){
            return Rest::dataNotFound('User '.$user.' not found !');
        }
        $relations = Relation::where(function ($query) use ($user) {$query
                        ->where('first_user_id', $user->id)
                        ->orWhere('second_user_id', $user->id);
                })->where('status', FR::Friend)->get();
        $emails = [];
        foreach ($relations as $relation){
            if ($relation->first_user_id == $user->id){
                $friend = User::find($relation->second_user_id);
                array_push($emails, $friend->email);
            } else {
                $friend = User::find($relation->first_user_id);
                array_push($emails, $friend->email);
            }
        }
        return Rest::successWithDataWithCount('friends', $emails, count($emails));
    }

    public function commonFriend(Request $request){
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
        $relations = Relation::where(function ($query) use ($user1) {$query
            ->where('first_user_id', $user1->id)
            ->orWhere('second_user_id', $user1->id);
        })->where('status', FR::Friend)->get();
        $emails1 = [];
        foreach ($relations as $relation){
            if ($relation->first_user_id == $user1->id){
                $friend = User::find($relation->second_user_id);
                array_push($emails1, $friend->email);
            } else {
                $friend = User::find($relation->first_user_id);
                array_push($emails1, $friend->email);
            }
        }

        $relations = Relation::where(function ($query) use ($user2) {$query
            ->where('first_user_id', $user2->id)
            ->orWhere('second_user_id', $user2->id);
        })->where('status', FR::Friend)->get();
        $emails2 = [];
        foreach ($relations as $relation){
            if ($relation->first_user_id == $user2->id){
                $friend = User::find($relation->second_user_id);
                array_push($emails2, $friend->email);
            } else {
                $friend = User::find($relation->first_user_id);
                array_push($emails2, $friend->email);
            }
        }
        $result = array_values(array_intersect($emails1, $emails2));
        return Rest::successWithDataWithCount('friends', $result, count($result));
    }

    public function subscribe(Request $request){
        $requestor = $request->input("requestor");
        $target = $request->input("target");
        if(!isset($requestor) || !isset($target)){
            return Rest::badRequest();
        }
        $user1 = User::where('email', $requestor)->first();
        if (!isset($user1)){
            return Rest::dataNotFound('User '.$requestor.' not found !');
        }
        $user2 = User::where('email', $target)->first();
        if (!isset($user2)){
            return Rest::dataNotFound('User '.$target.' not found !');
        }
        if ($user1->id == $user2->id){
            return Rest::badRequest();
        }

        $relations = Relation::where('status', FR::Subscribe)->where('first_user_id',$user1->id)->where('second_user_id',$user2->id)->first();
        if (isset($relations)){
            return Rest::badRequestWithMsg('Already Subscribed !');
        }
        $relations = new Relation();
        $relations->first_user_id = $user1->id;
        $relations->second_user_id = $user2->id;
        $relations->status = FR::Subscribe;
        $relations->save();
        return Rest::insertSuccess();
    }

    public function block(Request $request){
        $requestor = $request->input("requestor");
        $target = $request->input("target");
        if(!isset($requestor) || !isset($target)){
            return Rest::badRequest();
        }
        $user1 = User::where('email', $requestor)->first();
        if (!isset($user1)){
            return Rest::dataNotFound('User '.$requestor.' not found !');
        }
        $user2 = User::where('email', $target)->first();
        if (!isset($user2)){
            return Rest::dataNotFound('User '.$target.' not found !');
        }
        if ($user1->id == $user2->id){
            return Rest::badRequest();
        }

        $relations = Relation::where('status', FR::FriendBlocked)->where('first_user_id',$user1->id)->where('second_user_id',$user2->id)->first();
        if (isset($relations)){
            return Rest::badRequestWithMsg('Already Blocked !');
        }
        $relations = new Relation();
        $relations->first_user_id = $user1->id;
        $relations->second_user_id = $user2->id;
        $relations->status = FR::FriendBlocked;
        $relations->save();
        return Rest::insertSuccess();
    }
}
