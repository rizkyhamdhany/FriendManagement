<?php

namespace App;


class Rest
{
    public static function insertSuccess(){
        $returnData = array(
            'success' => true,
        );
        return response()->json($returnData, 200);
    }

    public static function successWithDataWithCount($field, $data, $count){
        $returnData = array(
            'success' => true,
            $field => $data,
            'count' => $count
        );
        return response()->json($returnData, 200);
    }

    public static function badRequest(){
        $returnData = array(
            'success' => false,
            'message' => 'Bad Request !'
        );
        return response()->json($returnData, 400);
    }

    public static function badRequestWithMsg($msg){
        $returnData = array(
            'success' => false,
            'message' => $msg
        );
        return response()->json($returnData, 400);
    }

    public static function dataNotFound($message){
        $returnData = array(
            'success' => false,
            'message' => $message
        );
        return response()->json($returnData, 404);
    }
}