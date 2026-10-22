<?php

namespace Tests\Unit;

use Core\Customer\Domain\Entities\Customer;
use Tests\TestCase;

class CustomerEntityTest extends TestCase
{
    public function test_from_array_creates_customer_entity()
    {
        $data = [
            'id' => 1,
            'business_id' => 123,
            'name' => 'Test Customer',
            'contact_name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'address' => '123 Main St',
            'tax_code' => '123456789',
            'bank_name' => 'Test Bank',
            'bank_account' => '1234567890',
            'type' => 'company',
            'group' => 1,
            'website' => 'https://example.com',
            'note' => 'Test note',
            'active' => true,
            'national_id' => null,
        ];

        $customer = Customer::fromArray($data);

        $this->assertEquals(1, $customer->id);
        $this->assertEquals(123, $customer->business_id);
        $this->assertEquals('Test Customer', $customer->name);
        $this->assertEquals('John Doe', $customer->contact_name);
        $this->assertEquals('john@example.com', $customer->email);
        $this->assertEquals('1234567890', $customer->phone);
        $this->assertEquals('123 Main St', $customer->address);
        $this->assertEquals('123456789', $customer->tax_code);
        $this->assertEquals('Test Bank', $customer->bank_name);
        $this->assertEquals('1234567890', $customer->bank_account);
        $this->assertEquals('company', $customer->type);
        $this->assertEquals(1, $customer->group);
        $this->assertEquals('https://example.com', $customer->website);
        $this->assertEquals('Test note', $customer->note);
        $this->assertTrue($customer->active);
    }

    public function test_from_array_with_defaults()
    {
        $data = [
            'business_id' => 123,
            'name' => 'Test Customer',
            'group' => 1,
        ];

        $customer = Customer::fromArray($data);

        $this->assertNull($customer->id);
        $this->assertEquals(123, $customer->business_id);
        $this->assertEquals('Test Customer', $customer->name);
        $this->assertNull($customer->contact_name);
        $this->assertNull($customer->email);
        $this->assertNull($customer->phone);
        $this->assertNull($customer->address);
        $this->assertNull($customer->tax_code);
        $this->assertNull($customer->bank_name);
        $this->assertNull($customer->bank_account);
        $this->assertEquals('individual', $customer->type);
        $this->assertEquals(1, $customer->group);
        $this->assertNull($customer->website);
        $this->assertNull($customer->note);
        $this->assertTrue($customer->active);
    }

    public function test_to_array_converts_entity_to_array()
    {
        $customer = Customer::fromArray([
            'business_id' => 123,
            'name' => 'Test Customer',
            'contact_name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'address' => '123 Main St',
            'tax_code' => '123456789',
            'bank_name' => 'Test Bank',
            'bank_account' => '1234567890',
            'type' => 'company',
            'group' => 1,
            'website' => 'https://example.com',
            'note' => 'Test note',
            'active' => true,
            'national_id' => null,
        ]);
        $customer->id = 1;

        $array = $customer->toArray();

        $expected = [
            'id' => 1,
            'business_id' => 123,
            'name' => 'Test Customer',
            'contact_name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'address' => '123 Main St',
            'tax_code' => '123456789',
            'bank_name' => 'Test Bank',
            'bank_account' => '1234567890',
            'type' => 'company',
            'group' => 1,
            'website' => 'https://example.com',
            'note' => 'Test note',
            'active' => true,
            'national_id' => null,
        ];

        $this->assertEquals($expected, $array);
    }

    public function test_deactivate_sets_active_to_false()
    {
        $customer = Customer::fromArray([
            'business_id' => 123,
            'name' => 'Test Customer',
            'group' => 1,
            'active' => true
        ]);

        $customer->deactivate();

        $this->assertFalse($customer->active);
    }

    public function test_activate_sets_active_to_true()
    {
        $customer = Customer::fromArray([
            'business_id' => 123,
            'name' => 'Test Customer',
            'group' => 1,
            'active' => false
        ]);

        $customer->activate();

        $this->assertTrue($customer->active);
    }
}