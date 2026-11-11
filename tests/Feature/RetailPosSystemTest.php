<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RetailPosSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * Test centralized login redirection per role.
     */
    public function test_login_redirects_to_correct_role_dashboard(): void
    {
        // 1. Kasir
        $responseKasir = $this->post('/login', [
            'email' => 'kasir@pos.com',
            'password' => 'password123',
        ]);
        $responseKasir->assertRedirect(route('kasir.pos'));
        $this->assertAuthenticated();

        $this->post('/logout');
        $this->assertGuest();

        // 2. Gudang
        $responseGudang = $this->post('/login', [
            'email' => 'gudang@pos.com',
            'password' => 'password123',
        ]);
        $responseGudang->assertRedirect(route('gudang.index'));

        $this->post('/logout');

        // 3. Admin
        $responseAdmin = $this->post('/login', [
            'email' => 'admin@pos.com',
            'password' => 'password123',
        ]);
        $responseAdmin->assertRedirect(route('admin.dashboard'));
    }

    /**
     * Test public registration with explicit role selection.
     */
    public function test_public_registration_with_explicit_role(): void
    {
        $response = $this->post('/register', [
            'name' => 'Kasir Baru',
            'email' => 'kasirbaru@pos.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'UKasir',
        ]);

        $response->assertRedirect(route('kasir.pos'));
        $this->assertDatabaseHas('users', [
            'email' => 'kasirbaru@pos.com',
            'role' => 'UKasir',
        ]);
    }

    /**
     * Test RBAC strict middleware route protections.
     */
    public function test_role_based_access_control_restrictions(): void
    {
        $kasir = User::where('role', 'UKasir')->first();
        $gudang = User::where('role', 'UGudang')->first();
        $admin = User::where('role', 'UAdmin')->first();

        // Kasir cannot access Gudang or Admin
        $this->actingAs($kasir)->get('/gudang')->assertRedirect(route('kasir.pos'));
        $this->actingAs($kasir)->get('/admin')->assertRedirect(route('kasir.pos'));

        // Gudang cannot access Kasir POS or Admin
        $this->actingAs($gudang)->get('/kasir/pos')->assertRedirect(route('gudang.index'));
        $this->actingAs($gudang)->get('/admin')->assertRedirect(route('gudang.index'));

        // Admin cannot access Kasir POS or Gudang
        $this->actingAs($admin)->get('/kasir/pos')->assertRedirect(route('admin.dashboard'));
        $this->actingAs($admin)->get('/gudang')->assertRedirect(route('admin.dashboard'));
    }

    /**
     * Test real-time stock deduction on successful Kasir checkout via DB::transaction.
     */
    public function test_checkout_deducts_stock_in_real_time(): void
    {
        $kasir = User::where('role', 'UKasir')->first();
        $product = Product::first();
        $initialStock = $product->stock;
        $qtyToBuy = 3;

        $response = $this->actingAs($kasir)->postJson('/kasir/checkout', [
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => $qtyToBuy,
                ],
            ],
            'payment_method' => 'Cash',
            'payment_amount' => $product->price * $qtyToBuy + 10000,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verify stock in database was decremented
        $product->refresh();
        $this->assertEquals($initialStock - $qtyToBuy, $product->stock);

        // Verify transaction record was created
        $this->assertDatabaseHas('transactions', [
            'user_id' => $kasir->id,
            'total_amount' => $product->price * $qtyToBuy,
        ]);
    }

    /**
     * Test checkout rejection when quantity exceeds available stock.
     */
    public function test_checkout_fails_when_stock_insufficient(): void
    {
        $kasir = User::where('role', 'UKasir')->first();
        $product = Product::first();
        $initialStock = $product->stock;

        $response = $this->actingAs($kasir)->postJson('/kasir/checkout', [
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => $initialStock + 999, // Exceeds available stock
                ],
            ],
            'payment_method' => 'Cash',
            'payment_amount' => 10000000,
        ]);

        $response->assertStatus(422);

        // Verify stock was NOT changed
        $product->refresh();
        $this->assertEquals($initialStock, $product->stock);
    }

    /**
     * Test UGudang incoming stock recording (Stock In / Restock).
     */
    public function test_gudang_stock_incoming(): void
    {
        $gudang = User::where('role', 'UGudang')->first();
        $product = Product::first();
        $initialStock = $product->stock;
        $qtyIncoming = 25;

        $response = $this->actingAs($gudang)->post('/gudang/stock/incoming', [
            'product_id' => $product->id,
            'quantity' => $qtyIncoming,
            'supplier_name' => 'PT Sumber Distribusi',
            'notes' => 'Pengiriman faktur SJ-991',
        ]);

        $response->assertRedirect(route('gudang.index'));

        // Verify stock increment
        $product->refresh();
        $this->assertEquals($initialStock + $qtyIncoming, $product->stock);

        // Verify StockAdjustment created
        $this->assertDatabaseHas('stock_adjustments', [
            'product_id' => $product->id,
            'type' => 'INCOMING',
            'quantity_change' => $qtyIncoming,
            'quantity_after' => $initialStock + $qtyIncoming,
        ]);
    }

    /**
     * Test UGudang stock adjustment opname (correction).
     */
    public function test_gudang_stock_adjustment(): void
    {
        $gudang = User::where('role', 'UGudang')->first();
        $product = Product::first();

        $response = $this->actingAs($gudang)->post('/gudang/stock/adjustment', [
            'product_id' => $product->id,
            'adjustment_type' => 'SUBTRACT',
            'amount' => 2,
            'reason' => 'Kemasan bocor / rusak fisik',
        ]);

        $response->assertRedirect(route('gudang.index'));

        // Verify StockAdjustment created
        $this->assertDatabaseHas('stock_adjustments', [
            'product_id' => $product->id,
            'type' => 'ADJUSTMENT',
            'quantity_change' => -2,
            'reason' => 'Kemasan bocor / rusak fisik',
        ]);
    }

    /**
     * Test UAdmin Read-Only dashboard and reporting access.
     */
    public function test_admin_read_only_access(): void
    {
        $admin = User::where('role', 'UAdmin')->first();

        // Can access dashboard, sales, profits, activities
        $this->actingAs($admin)->get('/admin')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/sales')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/profits')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/activities')->assertStatus(200);
    }
}
