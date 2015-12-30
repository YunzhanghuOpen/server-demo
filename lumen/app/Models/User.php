<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * Class User
 * @package App\Models
 * @property int id
 * @property string mobile
 * @property string password
 * @property string salt
 * @property string created_at
 * @property string updated_at
 */
class User extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';

    protected $fillable = [];
    protected $hidden = [];

}