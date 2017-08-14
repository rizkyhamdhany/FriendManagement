<?php

namespace App;


class Rest
{
    public static function insertSuccess(){
        $returnData = array(
            'status' => 'success',
        );
        return response()->json($returnData, 200);
    }

    public static function badRequest(){
        $returnData = array(
            'status' => 'error',
            'message' => 'Bad Request !'
        );
        return response()->json($returnData, 400);
    }

    public static function badRequestWithMsg($msg){
        $returnData = array(
            'status' => 'error',
            'message' => $msg
        );
        return response()->json($returnData, 400);
    }

    public static function dataNotFound($message){
        $returnData = array(
            'status' => 'error',
            'message' => $message
        );
        return response()->json($returnData, 404);
    }
}