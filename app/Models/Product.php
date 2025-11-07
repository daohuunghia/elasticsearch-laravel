<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Elasticsearch\Common\Exceptions\Missing404Exception;

class Product extends Model
{
    protected $fillable = [
        'name',
        'color',
        'brand',
        'description',
        'price'
    ];
    
    const INDEX_NAME = 'products';

    protected static function booted()
    {
        static::created(function ($model) {
            $client = app('elasticsearch');
            $client->index([
                'index' => self::INDEX_NAME,
                'id'    => $model->id,
                'body'  => $model->toArray(),
            ]);
        });

        static::updated(function ($model) {
            Log::info("model updated: {$model->title}");
        });

        static::deleted(function ($model) {
            $client = app('elasticsearch');
            
            try {
                $client->delete([
                    'index' => self::INDEX_NAME,
                    'id'    => $model->id,
                ]);
            } catch (\Elastic\Elasticsearch\Exception\ClientResponseException $e) {
                if ($e->getCode() !== 404) {
                    throw $e;
                }
                // Bỏ qua nếu không tìm thấy document
            }
        });
    }
}
