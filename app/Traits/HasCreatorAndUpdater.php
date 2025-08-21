<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait HasCreatorAndUpdater
{
    // Salva autore di creazione o modifiche file
    protected static function bootHasCreatorAndUpdater(){

        static::creating(function($model){
            $model->created_by = Auth::id();
            $model->updated_by = Auth::id();
        });

        static::updating(function($model){
            $model->updated_by = Auth::id();
        });
    }
}
