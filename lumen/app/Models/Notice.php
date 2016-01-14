<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * Class User
 * @package App\Models
 * @property int id
 * @property string ref
 * @property string uid
 * @property string type
 * @property string result
 * @property string created_at
 * @property string updated_at
 */
class Notice extends Model
{
    protected $table = 'notice';
    protected $primaryKey = 'id';

    protected $fillable = [];
    protected $hidden = [];

    const T_REAL_NAME = '实名认证';
    const T_BANKCARD = '绑定银行卡';
    const T_INVEST = '投资';

}