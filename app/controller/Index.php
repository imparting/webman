<?php

namespace app\controller;

use app\model\User;
use support\medoo\Db;
use support\Request;

class Index
{
    public function index(Request $request)
    {
        $user = DBC('default')->select('user', ['id', 'username'], ['is_delete' => 1]);
        return json($user);
//        $user = new User();
//        $users = $user->paginate(3,[]);
//        return json($users);
    }

    public function view(Request $request)
    {
        return view('index/view', ['name' => 'webman']);
    }

    public function json(Request $request)
    {
        return json(['code' => 0, 'msg' => 'ok']);
    }

    public function file(Request $request)
    {
        $file = $request->file('upload');
        if ($file && $file->isValid()) {
            $file->move(public_path() . '/files/myfile.' . $file->getUploadExtension());
            return json(['code' => 0, 'msg' => 'upload success']);
        }
        return json(['code' => 1, 'msg' => 'file not found']);
    }

}
