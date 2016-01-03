<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * Class User
 * @package App\Models
 * @property int id
 * @property string ref
 * @property string uid
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

}