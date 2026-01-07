<?php

namespace Tests\Feature;

use App\Models\InventoryItem;
use App\Models\InventoryItemVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class InventoryVariantSkuCompositionTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create and authenticate a user
        $this->user = User::factory()->create();
        $this->actingAs($this->user, 'sanctum');
    }

    /**
     * Test that backend composes and stores full SKU correctly
     *
     * @return void
     */
    public function test_backend_composes_and_stores_full_sku()
    {
        // Step 1: Create an inventory item with SKU "PILLOW4"
        $item = InventoryItem::create([
            'name' => 'Test Pillow',
            'sku' => 'PILLOW4',
            'description' => 'Test pillow item',
            'type' => 'renovation',
            'status' => 'active',
        ]);

        // Step 2: Create a variant with variant SKU part "500"
        $response = $this->postJson('/api/inventory-variants', [
            'inventory_item_id' => $item->id,
            'variant_name' => 'Red Pillow',
            'sku' => '500', // Frontend sends variant part only
            'in_stock' => 10,
            'projected_stock' => 5,
            'utilised_stock' => 2,
            'incoming_stock' => 8,
        ]);

        $response->assertStatus(200);
        
        // Step 3: Verify database stores FULL SKU "PILLOW4-500"
        $variant = InventoryItemVariant::where('inventory_item_id', $item->id)->first();
        $this->assertNotNull($variant);
        $this->assertEquals('PILLOW4-500', $variant->sku, 'Database should store full composed SKU');
        
        // Step 4: Verify API response returns FULL SKU
        $responseData = $response->json('data');
        $this->assertEquals('PILLOW4-500', $responseData['sku'], 'API response should return full composed SKU');
    }

    /**
     * Test GET endpoint returns full SKU
     *
     * @return void
     */
    public function test_get_variant_returns_full_sku()
    {
        // Create item and variant
        $item = InventoryItem::create([
            'name' => 'Test Chair',
            'sku' => 'CHAIR-001',
            'type' => 'renovation',
            'status' => 'active',
        ]);

        $variant = InventoryItemVariant::create([
            'inventory_item_id' => $item->id,
            'variant_name' => 'Blue Chair',
            'sku' => 'BLUE', // Frontend sends variant part
            'in_stock' => 5,
            'projected_stock' => 3,
            'utilised_stock' => 1,
            'incoming_stock' => 4,
        ]);

        // Verify database has full SKU
        $variant->refresh();
        $this->assertEquals('CHAIR-001-BLUE', $variant->sku);

        // Test GET endpoint
        $response = $this->getJson("/api/inventory-variants/{$variant->id}");
        $response->assertStatus(200);
        
        $responseData = $response->json('data');
        $this->assertEquals('CHAIR-001-BLUE', $responseData['sku'], 'GET endpoint should return full SKU');
    }

    /**
     * Test SKU uniqueness on full composed SKU
     *
     * @return void
     */
    public function test_sku_uniqueness_on_full_composed_sku()
    {
        // Create two items with different SKUs
        $item1 = InventoryItem::create([
            'name' => 'Item A',
            'sku' => 'ITEM-A',
            'type' => 'renovation',
            'status' => 'active',
        ]);

        $item2 = InventoryItem::create([
            'name' => 'Item B',
            'sku' => 'ITEM-B',
            'type' => 'renovation',
            'status' => 'active',
        ]);

        // Create variant under item1 with variant part "TEST"
        // Full SKU will be "ITEM-A-TEST"
        $response1 = $this->postJson('/api/inventory-variants', [
            'inventory_item_id' => $item1->id,
            'variant_name' => 'Variant 1',
            'sku' => 'TEST',
            'in_stock' => 10,
        ]);
        $response1->assertStatus(200);

        // Try to create variant under item1 again with same variant part "TEST"
        // Should FAIL because full SKU "ITEM-A-TEST" already exists
        $response2 = $this->postJson('/api/inventory-variants', [
            'inventory_item_id' => $item1->id,
            'variant_name' => 'Variant 2',
            'sku' => 'TEST',
            'in_stock' => 5,
        ]);
        $response2->assertStatus(422);
        $response2->assertJsonValidationErrors('full_sku');

        // Create variant under item2 with same variant part "TEST"
        // Should SUCCEED because full SKU "ITEM-B-TEST" is different from "ITEM-A-TEST"
        $response3 = $this->postJson('/api/inventory-variants', [
            'inventory_item_id' => $item2->id,
            'variant_name' => 'Variant 3',
            'sku' => 'TEST',
            'in_stock' => 8,
        ]);
        $response3->assertStatus(200);
        
        $variant3Data = $response3->json('data');
        $this->assertEquals('ITEM-B-TEST', $variant3Data['sku']);
    }

    /**
     * Test updating variant SKU recomposes full SKU
     *
     * @return void
     */
    public function test_update_variant_sku_recomposes_full_sku()
    {
        // Create item and variant
        $item = InventoryItem::create([
            'name' => 'Test Table',
            'sku' => 'TABLE-001',
            'type' => 'renovation',
            'status' => 'active',
        ]);

        $variant = InventoryItemVariant::create([
            'inventory_item_id' => $item->id,
            'variant_name' => 'Red Table',
            'sku' => 'RED', // Full SKU: TABLE-001-RED
            'in_stock' => 10,
        ]);

        $variant->refresh();
        $this->assertEquals('TABLE-001-RED', $variant->sku);

        // Update variant SKU part to "BLUE"
        $response = $this->putJson("/api/inventory-variants/{$variant->id}", [
            'sku' => 'BLUE', // Frontend sends new variant part
            'in_stock' => 10,
        ]);

        $response->assertStatus(200);

        // Verify database has new full SKU
        $variant->refresh();
        $this->assertEquals('TABLE-001-BLUE', $variant->sku, 'SKU should be recomposed on update');

        // Verify response has new full SKU
        $responseData = $response->json('data');
        $this->assertEquals('TABLE-001-BLUE', $responseData['sku']);
    }

    /**
     * Test SKU validation enforces correct format (uppercase, alphanumeric, hyphens only)
     * Validation happens before normalization, so invalid formats are rejected
     *
     * @return void
     */
    public function test_sku_validation_rejects_invalid_format()
    {
        $item = InventoryItem::create([
            'name' => 'Test Item',
            'sku' => 'ITEM-001',
            'type' => 'renovation',
            'status' => 'active',
        ]);

        // Test 1: Lowercase letters should be rejected by validation
        $response1 = $this->postJson('/api/inventory-variants', [
            'inventory_item_id' => $item->id,
            'variant_name' => 'Test Variant',
            'sku' => 'lowercase', // Invalid: lowercase letters
            'in_stock' => 10,
        ]);
        $response1->assertStatus(422);
        $response1->assertJsonValidationErrors('sku');

        // Test 2: Special characters should be rejected
        $response2 = $this->postJson('/api/inventory-variants', [
            'inventory_item_id' => $item->id,
            'variant_name' => 'Test Variant 2',
            'sku' => 'TEST@SPECIAL', // Invalid: @ character
            'in_stock' => 10,
        ]);
        $response2->assertStatus(422);
        $response2->assertJsonValidationErrors('sku');

        // Test 3: Spaces should be rejected
        $response3 = $this->postJson('/api/inventory-variants', [
            'inventory_item_id' => $item->id,
            'variant_name' => 'Test Variant 3',
            'sku' => 'TEST SPACE', // Invalid: space
            'in_stock' => 10,
        ]);
        $response3->assertStatus(422);
        $response3->assertJsonValidationErrors('sku');

        // Test 4: Valid format should pass
        $response4 = $this->postJson('/api/inventory-variants', [
            'inventory_item_id' => $item->id,
            'variant_name' => 'Test Variant 4',
            'sku' => 'TEST-123', // Valid: uppercase, numbers, hyphens
            'in_stock' => 10,
        ]);
        $response4->assertStatus(200);
        
        $variant = InventoryItemVariant::where('variant_name', 'Test Variant 4')->first();
        $this->assertEquals('ITEM-001-TEST-123', $variant->sku);
    }

    /**
     * Test empty SKU generates fallback
     *
     * @return void
     */
    public function test_empty_sku_generates_fallback()
    {
        $item = InventoryItem::create([
            'name' => 'Test Item',
            'sku' => 'ITEM-002',
            'type' => 'renovation',
            'status' => 'active',
        ]);

        // Frontend sends no SKU
        $response = $this->postJson('/api/inventory-variants', [
            'inventory_item_id' => $item->id,
            'variant_name' => 'Test Variant',
            // sku not provided
            'in_stock' => 10,
        ]);

        $response->assertStatus(200);

        $variant = InventoryItemVariant::where('inventory_item_id', $item->id)->first();
        // Should generate from variant_name
        $this->assertEquals('ITEM-002-TEST-VARIANT', $variant->sku);
    }

    /**
     * Test variants list includes full SKU
     *
     * @return void
     */
    public function test_variants_list_includes_full_sku()
    {
        $item = InventoryItem::create([
            'name' => 'Test Item',
            'sku' => 'ITEM-003',
            'type' => 'renovation',
            'status' => 'active',
        ]);

        $variant1 = InventoryItemVariant::create([
            'inventory_item_id' => $item->id,
            'variant_name' => 'Variant A',
            'sku' => 'A',
            'in_stock' => 10,
        ]);

        $variant2 = InventoryItemVariant::create([
            'inventory_item_id' => $item->id,
            'variant_name' => 'Variant B',
            'sku' => 'B',
            'in_stock' => 5,
        ]);

        // Get inventory item with variants
        $response = $this->getJson("/api/inventory/{$item->id}");
        $response->assertStatus(200);

        $variants = $response->json('data.variants');
        $this->assertCount(2, $variants);
        $this->assertEquals('ITEM-003-A', $variants[0]['sku']);
        $this->assertEquals('ITEM-003-B', $variants[1]['sku']);
    }
}

