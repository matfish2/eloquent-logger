<?php
namespace Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{

  protected $fillable = ['author_id','title','body'];

  public function comments() {

    return $this->hasMany(Comment::class);

  }


}
