<?php
namespace App\Http\Traits;

use Illuminate\Support\Facades\Auth;

trait ModelCompositeKey
{
        // Composite primary key handling
        public function getKey()
        {
            return $this->getKeyForSaveQuery();
        }

        protected function getKeyForSaveQuery()
        {
            $primaryKey = $this->getKeyName();
            if (!is_array($primaryKey)) {
                return $this->getAttribute($primaryKey);
            }

            $key = [];
            foreach ($primaryKey as $field) {
                $key[$field] = $this->getAttribute($field);
            }
            return $key;
        }

        protected function setKeysForSaveQuery($query)
        {
            foreach ($this->getKeyForSaveQuery() as $field => $value) {
                $query->where($field, '=', $value);
            }
            return $query;
        }


        public static function boot()
        {
            parent::boot();

            static::deleting(function($model){
                $model->deleted_by = Auth::user()->user_name;
                $model->save();
            });
        }
}
