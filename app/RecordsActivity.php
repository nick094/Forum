<?php

namespace App;

trait RecordsActivity {

    protected static function bootRecordsActivity() { //must start with bootTRAITname
       
        if(auth()->guest()) return;
/*
        foreach(static::getActivitiesToRecord() as $event {

            static::$event(function ($model) use $event {
                $model->recordActivity($event);
            }); */


        static::deleting(function ($model) {
            $model->activity()->delete();
        });
    }
 
    public static function getActivitiesToRecord() {

        return ['created','deleted'];
    }

    public function recordActivity($event) {
        Activity::create([
            'user_id' => auth()->id(),
            'type' => $this->getActivityType($event),
       /*    'subject_id' => $this->id,
            'subject_type' => get_class($this) */
        ]);
    }

    public function activity() {

        return $this->morphMany('App\Activity','subject');
    }

    public function getActivityType($event) {

        $type = strtolower((new \ReflectionClass($this))->getShortName());

        return "{{$event}}_{{$type}}";
    }

}