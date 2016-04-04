<?php
namespace Fish\Logger;

use Auth;

trait Logger {

 public function logs() {
  return $this->morphMany('Fish\Logger\Log','loggable');
}

public static function bootLogger()
{
  static::created(function ($model) {
     $model->logCreated();
  });

  static::updating(function ($model) {
    $model->logUpdated();
  });

  static::deleting(function ($model) {
    $model->logDeleted($model);
  });

}

protected function insertNewLog($action, $before, $after) {

  $userId = Auth::id()?:null;

  return $this->logs()->save(new Log(['user_id'=>$userId,
    'action'=>$action,
    'before'=>$before?json_encode($before):null,
    'after'=>$after?json_encode($after):null]));
}

protected function logCreated() {
  $model = $this->stripRedundantKeys();
  return $this->insertNewLog('created',null, $model);
}

protected function logUpdated() {
  $diff = $this->getDiff();
  return $this->insertNewLog('updated',$diff['before'],$diff['after']);
}

protected function logDeleted() {
  $model = $this->stripRedundantKeys();
  return $this->insertNewLog('deleted',$model, null);
}

    /**
     * Fetch a diff for the model's current state.
     */
    protected function getDiff()
    {
      $after = $this->getDirty();
      $before = array_intersect_key($this->fresh()->toArray(), $after);

      return compact('before', 'after');
    }

    protected function stripRedundantKeys() {
      $model = $this->toArray();

      if (isset($model['created_at'])) unset($model['created_at']);
      if (isset($model['updated_at'])) unset($model['updated_at']);
      if (isset($model['id'])) unset($model['id']);

      return $model;
    }

    /**
     * get a fresh copy of the model from the database
     * a fallback for Laravel 4
     */
    public function fresh(array $with = [])
    {
      if (! $this->exists) {
        return;
      }
      $key = $this->getKeyName();
      return static::with($with)->where($key, $this->getKey())->first();
    }

  }
