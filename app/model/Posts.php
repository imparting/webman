<?php
namespace app\model;

use support\medoo\Model;

class Posts extends Model
{
    protected $table = 'posts';

    protected $columns = [
        'id',
        'title'
    ];
    // 关联模型
    protected $foreign = [
        'user' => ['-', User::class, 'id', 'user_id'],
    ];

    protected $soft_delete = 'is_delete';
}