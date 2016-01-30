<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;
use Fish\Logger\Logger;

class Author extends BaseModel
{

    use Logger;

    protected $fillable = ['user_id','role'];

    public function user() {

      return $this->belongsTo(User::class);

    }

    public function posts() {

      return $this->hasMany(Post::class);

    }




}
