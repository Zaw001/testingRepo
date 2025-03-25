<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderCollection;
use App\Http\Resources\OrderResource;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Warehouse;
use App\Repositories\OrderRepository;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class OrderAPIController extends Controller
{
    /** @var orderRepository */
    private $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function index(Request $request): OrderCollection
    {
        $perPage = getPageSize($request);
        $search = $request->filter['search'] ?? '';
        $customer = (Customer::where('name', 'LIKE', "%$search%")->get()->count() != 0);
        $warehouse = (Warehouse::where('name', 'LIKE', "%$search%")->get()->count() != 0);

        $sales = $this->orderRepository;
        if ($customer || $warehouse) {
            $sales->whereHas('customer', function (Builder $q) use ($search, $customer) {
                if ($customer) {
                    $q->where('name', 'LIKE', "%$search%");
                }
            })->whereHas('warehouse', function (Builder $q) use ($search, $warehouse) {
                if ($warehouse) {
                    $q->where('name', 'LIKE', "%$search%");
                }
            });
        }

        if ($request->get('start_date') && $request->get('end_date')) {
            $sales->whereBetween('created_at', [$request->get('start_date'), $request->get('end_date')]);
        }

        if ($request->get('warehouse_id')) {
            $sales->where('warehouse_id', $request->get('warehouse_id'));
        }

        if ($request->get('customer_id')) {
            $sales->where('customer_id', $request->get('customer_id'));
        }

        if ($request->get('status') && $request->get('status') != 'null') {
            $sales->Where('status', $request->get('status'));
        }
        // if ($request->get('payment_type') && $request->get('payment_type') != 'null') {
        //     $sales->where('payment_type', $request->get('payment_type'));
        // }
        $sales = $sales->paginate($perPage);

        OrderResource::usingWithCollection();
        return new OrderCollection($sales);
    }

    public function show($id): OrderResource
    {
        $sale = $this->orderRepository->find($id);

        return new OrderResource($sale);
    }

    public function fetchTotalOrder()
    {
        $totalOrder = Order::sum('grand_total');
        return response()->json(['data' => $totalOrder]);
    }
}
