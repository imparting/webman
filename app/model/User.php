<?php

namespace app\model;


use support\medoo\Model;

class User extends Model
{
    public function __construct()
    {
        $this->addFilter('username', function ($username) {
            return "----" . $username . "---";
        }, function ($username) {
            return "+++++" . $username . "++++";
        });
    }

    protected $table = 'user';

    protected $columns = [
        'id',
        'username'
    ];
    // 关联模型
    protected $foreign = [
        'post' => ['-', Posts::class, 'user_id', 'id'],
        'posts' => ['*', Posts::class, 'user_id', 'id'],
    ];

    protected $soft_delete = 'is_delete';

}