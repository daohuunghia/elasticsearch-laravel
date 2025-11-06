<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ElasticsearchService;
use App\Models\Product;
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
            $data = [
                ['name' => 'iPhone 15 Pro', 'brand' => 'Apple', 'color' => 'đen', 'price' => 32990000, 'description' => 'Điện thoại cao cấp với chip A17 Pro.'],
                ['name' => 'Samsung Galaxy S24', 'brand' => 'Samsung', 'color' => 'xanh', 'price' => 24990000, 'description' => 'Camera tốt, hiệu năng mạnh, màn hình AMOLED.'],
                ['name' => 'Xiaomi 14', 'brand' => 'Xiaomi', 'color' => 'trắng', 'price' => 15990000, 'description' => 'Hiệu năng mạnh, giá rẻ, pin trâu.'],
            ];

            DB::beginTransaction();
            foreach ($data as $product) {
                $p = Product::create($product);

                $this->es->index([
                    'index' => 'products',
                    'id' => $p->id,
                    'body' => $product
                ]);
            }
            DB::commit();

            return response()->json(['message' => 'Đã tạo và index 3 sản phẩm mẫu!']);
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
        }
    }

    public function search(Request $request)
    {
        $query = $request->get('q', '');

        $params = [
            'index' => 'products',
            'body' => [
                'query' => [
                    'multi_match' => [
                        'query' => $query,
                        'fields' => ['name', 'brand', 'description']
                    ]
                ]
            ]
        ];

        $results = $this->es->search($params);

        return response()->json($results->asArray());
    }
}
