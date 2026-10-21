<?php

namespace Database\Seeders;

use App\Enums\PurchaseOrderStatus;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use App\Services\PurchaseOrderService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin', 'password' => Hash::make('password')]
        );

        $suppliers = collect([
            ['Acme Industrial Supply', 'sales@acme.test', '+1 555 0101'],
            ['Globex Components', 'orders@globex.test', '+1 555 0102'],
            ['Initech Wholesale', 'hello@initech.test', '+1 555 0103'],
        ])->map(fn ($s) => Supplier::updateOrCreate(['email' => $s[1]], ['name' => $s[0], 'phone' => $s[2]]));

        $products = collect([
            ['SKU-1001', 'Hex Bolt M8 (box of 100)', 12.50, 120],
            ['SKU-1002', 'Steel Washer M8 (box of 200)', 8.75, 80],
            ['SKU-1003', 'Cordless Drill 18V', 89.00, 6],
            ['SKU-1004', 'Safety Gloves (pair)', 4.20, 150],
            ['SKU-1005', 'LED Work Light', 24.99, 9],
            ['SKU-1006', 'Packing Tape 48mm', 2.10, 300],
            ['SKU-1007', 'Barcode Label Roll', 6.40, 45],
            ['SKU-1008', 'Handheld Scanner Battery', 31.00, 3],
            ['SKU-1009', 'Pallet Wrap 500mm', 14.30, 25],
            ['SKU-1010', 'Steel Shelving Unit', 145.00, 12],
        ])->map(fn ($p) => Product::updateOrCreate(
            ['sku' => $p[0]],
            ['name' => $p[1], 'unit_cost' => $p[2], 'stock_quantity' => $p[3], 'is_active' => true]
        ));

        // Sample orders so the dashboard is not empty on first login.
        if (PurchaseOrder::count() === 0) {
            $service = app(PurchaseOrderService::class);

            $make = fn (Supplier $s, array $picks) => $service->create([
                'supplier_id' => $s->id,
                'items' => collect($picks)->map(fn ($p) => [
                    'product_id' => $products[$p[0]]->id,
                    'quantity' => $p[1],
                    'unit_price' => $p[2],
                ])->all(),
            ], $admin->id);

            $service->transition(
                $service->transition($make($suppliers[0], [[0, 50, 11.9], [1, 40, 8.5]]), PurchaseOrderStatus::Approved),
                PurchaseOrderStatus::Received
            );
            $service->transition($make($suppliers[1], [[2, 10, 85.0], [4, 20, 23.5]]), PurchaseOrderStatus::Approved);
            $make($suppliers[2], [[7, 30, 29.0]]);
        }
    }
}