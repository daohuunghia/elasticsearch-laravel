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

    public function destroy()
    {
            $client = app('elasticsearch');

            $client->indices()->delete(['index' => 'dev']);
            return response()->json([
                'message' => 'Đã xóa index products',
            ]);
    }


    public function search(Request $request)
    {
        try {
            if (!$this->es->ping()) {
                return response()->json([
                    'error' => 'Elasticsearch không khả dụng',
                    'message' => 'Vui lòng đảm bảo Elasticsearch đang chạy tại ' . config('elasticsearch.host'),
                ], 503);
            }

            $query = $request->get('q', '');

            $page = max(1, (int) $request->input('page', 1)); // trang hiện tại
            $size = (int) $request->input('size', 10);        // số kết quả / trang
            $from = ($page - 1) * $size;

            $params = [
                'index' => Product::INDEX_NAME,
                'body'  => [
                    'from' => $from,
                    'size' => $size,    
                    'query' => [
                    //       'multi_match' => [
                    //     'query' => $query,
                    //     'fields' => ['title', 'content']
                    // ]
                        'bool' => [
                            'should' => [
                                // Full-text search (phân tích ngữ nghĩa)
                                [
                                    'multi_match' => [
                                        'query'  => $query,
                                        'fields' => ['name', 'brand', 'color', 'description'],
                                        'fuzziness' => 'AUTO', // cho phép sai chính tả nhẹ
                                    ],
                                ],
                                // Partial search (tìm chứa từ)
                                [
                                    'query_string' => [
                                        'query'  => "*$query*",
                                        'fields' => ['name', 'brand', 'color', 'description'],
                                        'analyze_wildcard' => true,
                                    ],
                                ],
                            ],
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
