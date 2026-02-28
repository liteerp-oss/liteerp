<?php

namespace Tests\Unit;

use App\Exceptions\BadException;
use Core\Business\Domain\Entities\Business;
use Core\Business\Domain\Repositories\BusinessRepositoryInterface;
use Core\Business\Infrastructure\Services\BusinessServiceImpl;
use Tests\TestCase;
use Mockery;

class BusinessServiceImplTest extends TestCase
{
    protected $repoMock;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repoMock = Mockery::mock(BusinessRepositoryInterface::class);
        $this->service = new BusinessServiceImpl($this->repoMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_create_returns_business_when_name_not_used()
    {
        $data = [
            'name' => 'Test Business',
            'address' => '123 Main St',
            'tax_code' => '123456789',
            'phone' => '1234567890',
            'email' => 'business@example.com',
            'bank_name' => 'Test Bank',
            'bank_account_number' => '1234567890',
            'bank_account_name' => 'Test Account',
        ];

        $business = Business::fromArray($data);
        $business->id = 1;

        $this->repoMock->shouldReceive('findByName')->with($data)->andReturn(null);
        $this->repoMock->shouldReceive('checkExists')->andReturn(false);
        $this->repoMock->shouldReceive('create')->andReturn($business);

        $result = $this->service->create($data);

        $this->assertInstanceOf(Business::class, $result);
    }

    public function test_create_throws_exception_when_name_already_used()
    {
        $data = [
            'name' => 'Test Business',
            'address' => '123 Main St',
            'tax_code' => '123456789',
            'phone' => '1234567890',
            'email' => 'business@example.com',
            'bank_name' => 'Test Bank',
            'bank_account_number' => '1234567890',
            'bank_account_name' => 'Test Account',
        ];

        $existingBusiness = Business::fromArray($data);
        $existingBusiness->id = 2;

        $this->repoMock->shouldReceive('findByName')->with($data)->andReturn($existingBusiness);

        $this->expectException(BadException::class);
        $this->expectExceptionMessage('Business name has been used');

        $this->service->create($data);
    }

    public function test_create_throws_exception_when_business_already_exists()
    {
        $data = [
            'name' => 'Test Business',
            'address' => '123 Main St',
            'tax_code' => '123456789',
            'phone' => '1234567890',
            'email' => 'business@example.com',
            'bank_name' => 'Test Bank',
            'bank_account_number' => '1234567890',
            'bank_account_name' => 'Test Account',
        ];

        $business = Business::fromArray($data);

        $this->repoMock->shouldReceive('findByName')->with($data)->andReturn(null);
        $this->repoMock->shouldReceive('checkExists')->andReturn(true);

        $this->expectException(BadException::class);
        $this->expectExceptionMessage('Name and address has been used');

        $this->service->create($data);
    }

    public function test_show_returns_business_data()
    {
        $data = [
            'id' => 1,
        ];

        $businessData = [
            'id' => 1,
            'name' => 'Test Business',
            'address' => '123 Main St',
            'tax_code' => '123456789',
            'phone' => '1234567890',
            'email' => 'business@example.com',
            'bank_name' => 'Test Bank',
            'bank_account_number' => '1234567890',
            'bank_account_name' => 'Test Account',
        ];

        $this->repoMock->shouldReceive('findByIdWithFullData')->with($data)->andReturn($businessData);

        $result = $this->service->show($data);

        $this->assertIsArray($result);
        $this->assertEquals(1, $result['id']);
        $this->assertEquals('Test Business', $result['name']);
    }

    public function test_show_throws_exception_when_business_not_found()
    {
        $data = [
            'id' => 1,
        ];

        $this->repoMock->shouldReceive('findByIdWithFullData')->with($data)->andReturn(null);

        $this->expectException(BadException::class);
        $this->expectExceptionMessage('Not found business');

        $this->service->show($data);
    }

    public function test_update_returns_updated_business()
    {
        $data = [
            'id' => 1,
            'name' => 'Updated Business',
            'address' => 'Updated Address',
            'tax_code' => '987654321',
            'phone' => '0987654321',
            'email' => 'updated@example.com',
            'logo_url' => 'updated-logo.jpg',
            'bank_name' => 'Updated Bank',
            'bank_account_number' => '0987654321',
            'bank_account_name' => 'Updated Account',
        ];

        $existingBusiness = Business::fromArray([
            'name' => 'Old Business',
            'address' => 'Old Address',
            'tax_code' => '123456789',
            'phone' => '1234567890',
            'email' => 'old@example.com',
            'bank_name' => 'Old Bank',
            'bank_account_number' => '1234567890',
            'bank_account_name' => 'Old Account',
        ]);
        $existingBusiness->id = 1;

        $updatedBusiness = Business::fromArray([
            'name' => 'Updated Business',
            'address' => 'Updated Address',
            'tax_code' => '987654321',
            'phone' => '0987654321',
            'email' => 'updated@example.com',
            'logo_url' => 'updated-logo.jpg',
            'bank_name' => 'Updated Bank',
            'bank_account_number' => '0987654321',
            'bank_account_name' => 'Updated Account',
        ]);
        $updatedBusiness->id = 1;

        $this->repoMock->shouldReceive('findByName')->with($data)->andReturn(null);
        $this->repoMock->shouldReceive('findById')->with($data)->andReturn($existingBusiness);
        $this->repoMock->shouldReceive('update')->andReturn($updatedBusiness);

        $result = $this->service->update($data);

        $this->assertInstanceOf(Business::class, $result);
        $this->assertEquals('Updated Business', $result->name);
        $this->assertEquals('Updated Address', $result->address);
        $this->assertEquals('987654321', $result->tax_code);
        $this->assertEquals('0987654321', $result->phone);
        $this->assertEquals('updated@example.com', $result->email);
        $this->assertEquals('updated-logo.jpg', $result->logo_url);
        $this->assertEquals('Updated Bank', $result->bank_name);
        $this->assertEquals('0987654321', $result->bank_account_number);
        $this->assertEquals('Updated Account', $result->bank_account_name);
    }

    public function test_update_throws_exception_when_name_already_used_by_another_business()
    {
        $data = [
            'id' => 1,
            'name' => 'Updated Business',
            'address' => 'Updated Address',
            'tax_code' => '987654321',
            'phone' => '0987654321',
            'email' => 'updated@example.com',
            'bank_name' => 'Updated Bank',
            'bank_account_number' => '0987654321',
            'bank_account_name' => 'Updated Account',
        ];

        $anotherBusiness = Business::fromArray([
            'name' => 'Updated Business',
            'address' => 'Another Address',
            'tax_code' => '111111111',
            'phone' => '1111111111',
            'email' => 'another@example.com',
            'bank_name' => 'Another Bank',
            'bank_account_number' => '1111111111',
            'bank_account_name' => 'Another Account',
        ]);
        $anotherBusiness->id = 2;

        $this->repoMock->shouldReceive('findByName')->with($data)->andReturn($anotherBusiness);

        $this->expectException(BadException::class);
        $this->expectExceptionMessage('Business name has been used');

        $this->service->update($data);
    }

    public function test_all_returns_array()
    {
        $businesses = [
            Business::fromArray([
                'name' => 'Business 1',
                'address' => 'Address 1',
                'tax_code' => '123456789',
                'phone' => '1234567890',
                'email' => 'business1@example.com',
                'bank_name' => 'Bank 1',
                'bank_account_number' => '1234567890',
                'bank_account_name' => 'Account 1',
            ]),
            Business::fromArray([
                'name' => 'Business 2',
                'address' => 'Address 2',
                'tax_code' => '987654321',
                'phone' => '0987654321',
                'email' => 'business2@example.com',
                'bank_name' => 'Bank 2',
                'bank_account_number' => '0987654321',
                'bank_account_name' => 'Account 2',
            ]),
        ];

        $this->repoMock->shouldReceive('all')->andReturn($businesses);

        $result = $this->service->all();

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
    }
}
