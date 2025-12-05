<?php

namespace Tests\Unit\Services;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Enums\UserRole;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    public function test_register_hashes_password(): void
    {
        $repository = Mockery::mock(UserRepositoryInterface::class);
        $service = new UserService($repository);

        $capturedData = null;

        $repository->shouldReceive('create')
            ->once()
            ->andReturnUsing(function ($data) use (&$capturedData) {
                $capturedData = $data;
                return new User();
            });

        $service->register([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'role' => 'student',
        ]);

        $this->assertNotNull($capturedData);
        $this->assertTrue(Hash::check('password123', $capturedData['password']));
    }

    public function test_register_converts_role_to_enum(): void
    {
        $repository = Mockery::mock(UserRepositoryInterface::class);
        $service = new UserService($repository);

        $capturedData = null;

        $repository->shouldReceive('create')
            ->once()
            ->andReturnUsing(function ($data) use (&$capturedData) {
                $capturedData = $data;
                return new User();
            });

        $service->register([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'role' => 'instructor',
        ]);

        $this->assertNotNull($capturedData);
        $this->assertInstanceOf(UserRole::class, $capturedData['role']);
        $this->assertEquals(UserRole::Instructor, $capturedData['role']);
    }

    public function test_register_calls_repository_with_correct_data(): void
    {
        $repository = Mockery::mock(UserRepositoryInterface::class);
        $service = new UserService($repository);

        $user = new User();
        $user->name = 'Test User';
        $user->email = 'test@example.com';

        $repository->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($data) {
                return $data['name'] === 'Test User'
                    && $data['email'] === 'test@example.com'
                    && isset($data['password'])
                    && isset($data['role']);
            }))
            ->andReturn($user);

        $result = $service->register([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'role' => 'student',
        ]);

        $this->assertInstanceOf(User::class, $result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}

