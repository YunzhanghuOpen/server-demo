<?php
/**
 * Created by PhpStorm.
 * User: philiptang
 * Date: 12/30/15
 * Time: 17:01
 */

namespace App\Http\Controllers;


use Illuminate\Http\Request;

class UserController
{

    public function register(Request $request)
    {
        $input = $request->all();
        return response()->json($input);
    }

    public function login(Request $request)
    {
        $input = $request->all();
        return response()->json($input);
    }

}