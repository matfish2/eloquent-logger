<?php
namespace Fish\Logger;

use Auth;

trait Logger {

 public function logs() {
  return $this->morphMany('Fish\Logger\Log','loggable');
 }

 public static function boot()
 {

  parent::boot();

  static::created(function ($model) {
    return $model->logCreated();

  });

  static::updating(function ($model) {
    return $model->logUpdated();
  });

  static::deleting(function ($model) {
    return $model->logDeleted($model);
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
    return $this->insertNewLog('deleted',$this, null);
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



}
