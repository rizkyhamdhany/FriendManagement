<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Relation;
use App\FR;

class FriendConnectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();
        foreach ($users as $user){
            $friends = User::where('id', '!=' , $user->id)->limit(15)->inRandomOrder()->get();
            foreach ($friends as $friend){
                $rel = Relation::where('first_user_id', $friend->id)->where('second_user_id', $user->id)->where('status', FR::Friend)->first();
                if (!isset($rel)){
                    $relations = new Relation();
                    if ($user->id > $friend->id){
                        $relations->first_user_id = $friend->id;
                        $relations->second_user_id = $user->id;
                    } else {
                        $relations->first_user_id = $user->id;
                        $relations->second_user_id = $friend->id;
                    }
                    $relations->status = FR::Friend;
                    $relations->save();
                    $user->friend_count = $user->friend_count + 1;
                    $user->save();
                    $friend->friend_count = $friend->friend_count + 1;
                    $friend->save();
                }
            }
        }
    }
}
