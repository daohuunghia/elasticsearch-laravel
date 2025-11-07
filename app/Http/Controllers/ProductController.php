<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\ElasticsearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    protected $es;

    public function __construct(ElasticsearchService $es)
    {
        $this->es = $es;
    }

    public function seed()
    {
        try {
            if (!$this->es->ping()) {
                return response()->json([
                    'error' => 'Elasticsearch không khả dụng',
                    'message' => 'Vui lòng đảm bảo Elasticsearch đang chạy tại '.config('elasticsearch.host'),
                    'hint' => 'Chạy: docker compose up -d',
                ], 503);
            }

           $data = Product::all();

            DB::beginTransaction();
            foreach ($data as $product) {
                $this->es->index([
                    'index' => 'products',
                    'id' => $product->id,
                    'body' => $product->toArray(),
                ]);
            }
            DB::commit();
            return response()->json(['message' => 'Đã tạo và index 3 sản phẩm mẫu!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Lỗi khi seed dữ liệu',
                'message' => $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null,
            ], 500);
        }
    }

    public function search(Request $request)
    {
        try {
            if (! $this->es->ping()) {
                return response()->json([
                    'error' => 'Elasticsearch không khả dụng',
                    'message' => 'Vui lòng đảm bảo Elasticsearch đang chạy tại '.config('elasticsearch.host'),
                ], 503);
            }

            $query = $request->get('q', '');

            $params = [
                'index' => 'products',
                'body' => [
                    'query' => [
                        'multi_match' => [
                            'query' => $query,
                            'fields' => ['name', 'brand', 'description'],
                        ],
                    ],
                ],
            ];

            $results = $this->es->search($params);

            return response()->json($results->asArray());
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Lỗi khi tìm kiếm',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
