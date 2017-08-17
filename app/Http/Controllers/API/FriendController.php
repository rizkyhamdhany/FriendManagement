<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\CC;
use App\Rest;
use App\Relation;
use Illuminate\Support\Facades\DB;

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
        }
        $relations = Relation::where('first_user_id',$user1->id)->where('second_user_id',$user2->id)->first();
        if (isset($relations)){
            if ($relations->is_friend){
                return Rest::badRequestWithMsg('Already become friend !');
            }
            $relations2 = Relation::where('first_user_id',$user2->id)->where('second_user_id',$user1->id)->first();
            if (isset($relations2)){
                $relations->is_friend = true;
                $relations2->is_friend = true;
            } else {
                $relations2 = new Relation();
                $relations2->first_user_id = $user2->id;
                $relations2->second_user_id = $user1->id;
                $relations2->is_friend = true;
                $relations2->subscribed = false;
                $relations2->blocked = false;
            }
            if ($relations->blocked || $relations2->blocked){
                return Rest::badRequestWithMsg('You have blocked this user !');
            }
            $relations2->save();
            $relations->save();
            $user1->friend_count = $user1->friend_count + 1;
            $user1->save();
            $user2->friend_count = $user2->friend_count + 1;
            $user2->save();
        } else {
            $relations = new Relation();
            $relations->first_user_id = $user1->id;
            $relations->second_user_id = $user2->id;
            $relations->is_friend = true;
            $relations->subscribed = false;
            $relations->blocked = false;
            $relations2 = Relation::where('first_user_id',$user2->id)->where('second_user_id',$user1->id)->first();
            if (isset($relations2)){
                $relations2->is_friend = true;
            } else {
                $relations2 = new Relation();
                $relations2->first_user_id = $user2->id;
                $relations2->second_user_id = $user1->id;
                $relations2->is_friend = true;
                $relations2->subscribed = false;
                $relations2->blocked = false;
            }
            if ($relations->blocked || $relations2->blocked){
                return Rest::badRequestWithMsg('This user have blocked this you !');
            }
            $relations->save();
            $relations2->save();
            $user1->friend_count = $user1->friend_count + 1;
            $user1->save();
            $user2->friend_count = $user2->friend_count + 1;
            $user2->save();
        }
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
        $emails =  User::whereIn('id', function($query) use ($user){
                        $query->select('second_user_id')
                            ->from(with(new Relation)->getTable())
                            ->where('is_friend', true)
                            ->where('first_user_id', $user->id);
                    })->pluck('email')->toArray();
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
        $emails =  DB::select(
            "select `users`.`email` 
              from `users`
              where `id` in (SELECT `my`.`second_user_id` 
                            FROM `relations` AS `my` 
                            JOIN `relations` AS their USING (`second_user_id`) 
                            WHERE my.`first_user_id` = ".$user1->id." AND their.`first_user_id` = ".$user2->id.")"
        );
        return Rest::successWithDataWithCount('friends', $emails, count($emails));
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

        $relations = Relation::where('first_user_id',$user1->id)->where('second_user_id',$user2->id)->first();
        if (isset($relations)){
            if ($relations->subscribed){
                return Rest::badRequestWithMsg('Already Subscribed !');
            }
            $relations->subscribed = true;
        } else {
            $relations = new Relation();
            $relations->first_user_id = $user1->id;
            $relations->second_user_id = $user2->id;
            $relations->is_friend = false;
            $relations->subscribed = true;
            $relations->blocked = false;
        }
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

        $relations = Relation::where('first_user_id',$user1->id)->where('second_user_id',$user2->id)->first();
        if (isset($relations)){
            if ($relations->subscribed){
                return Rest::badRequestWithMsg('Already Subscribed !');
            }
            $relations->blocked = true;
        } else {
            $relations = new Relation();
            $relations->first_user_id = $user1->id;
            $relations->second_user_id = $user2->id;
            $relations->is_friend = false;
            $relations->subscribed = false;
            $relations->blocked = true;
        }
        $relations->save();
        return Rest::insertSuccess();
    }

    public function receivingUpdate(Request $request){
        $sender = $request->input("sender");
        $text = $request->input("text");
        if(!isset($sender) || !isset($text)){
            return Rest::badRequest();
        }
        $user = User::where('email', $sender)->first();
        if (!isset($user)){
            return Rest::dataNotFound('User '.$sender.' not found !');
        }
        $emails =  User::whereIn('id', function($query) use ($user){
            $query->select('first_user_id')
                ->from(with(new Relation)->getTable())
                ->where(function($q) {
                    $q
                        ->where('is_friend', true)
                        ->orWhere('subscribed', true);
                })
                ->where('blocked', false)
                ->where('second_user_id', $user->id);
        })->pluck('email')->toArray();

        preg_match_all("/[\._a-zA-Z0-9-]+@[\._a-zA-Z0-9-]+/i", $text, $matches);
        $mentioned = User::where('email', $matches[0])->pluck('id')->toArray();
        $emails = array_merge($emails, $mentioned);
        return Rest::successWithDataWithCount('friends', $emails, count($emails));
    }
}
