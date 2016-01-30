<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;
use Fish\Cascade\Cascade;

class BaseModel extends Model
{
   use Cascade;
}
