<?php

namespace Fish\Logger;

use Illuminate\Database\Eloquent\Model;
use App;
use Models\User;

class Log extends Model
{

  protected $table = 'mf_logs';
  protected $fillable = ['user_id','action','before', 'after'];
  public $timestamps = false;

  public static function boot()
  {

    parent::boot();

    static::creating(function ($model) {
      $model->setCreatedAt($model->freshTimestamp());
    });

  }

  public function loggable() {
    return $this->morphTo();
  }

  public function scopeAction($q, $action) {
    return $q->where('action',$action);
  }

  public function scopeWasDeleted($q) {
    return $q->action('deleted');
  }

  public function scopeWasUpdated($q) {
    return $q->action('updated');
  }

  public function scopeWasCreated($q) {
    return $q->action('created');
  }

  public function scopeBetween($q, $start, $end) {
    return $q->whereBetween('created_at',[$start, $end]);
  }

  public function scopeEntity($q, $loggableType, $loggableId) {
    return $q->where('loggable_type',$loggableType)
             ->where('loggable_id',$loggableId);
  }


  public function scopeStateOn($q, $datetime) {

    $query = clone($q);

    $totalLogs = $q->count();
    $log = $q->first();
    $class = $log->loggable_type;

    $logs = $q->where('created_at','<=',$datetime)->count();

    if ($logs==$totalLogs):
      return $log->loggable?:new $class($log->toArray());
    endif;

    $initial = $q->wasCreated()->first();


    $changes = $query->wasUpdated()
                     ->where('created_at','<=',$datetime)
                     ->get();

    $attrs = $initial->after;

    foreach ($changes as $change) {

      $attrs = array_merge($attrs, $change->after);

    }

    return new $class($attrs);

  }



  public function getBeforeAttribute($value) {
    return $value?(array) json_decode($value):null;
  }

  public function getAfterAttribute($value) {
    return $value?(array) json_decode($value):null;
  }
}
