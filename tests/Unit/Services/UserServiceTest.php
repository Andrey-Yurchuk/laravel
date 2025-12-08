<?php

namespace Tests\Unit\Services;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Enums\UserRole;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    /** @var MockInterface&UserRepositoryInterface */
    private MockInterface $repository;
    private UserService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(UserRepositoryInterface::class);
        $this->service = new UserService($this->repository);
    }

    public function test_register_hashes_password(): void
    {
        $capturedData = null;

        $this->repository->shouldReceive('create')
            ->once()
            ->andReturnUsing(function ($data) use (&$capturedData) {
                $capturedData = $data;
                return new User();
            });

        $this->service->register([
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
        $capturedData = null;

        $this->repository->shouldReceive('create')
            ->once()
            ->andReturnUsing(function ($data) use (&$capturedData) {
                $capturedData = $data;
                return new User();
            });

        $this->service->register([
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
        $user = new User();
        $user->name = 'Test User';
        $user->email = 'test@example.com';

        $this->repository->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($data) {
                return $data['name'] === 'Test User'
                    && $data['email'] === 'test@example.com'
                    && isset($data['password'])
                    && isset($data['role']);
            }))
            ->andReturn($user);

        $result = $this->service->register([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'role' => 'student',
        ]);

        $this->assertInstanceOf(User::class, $result);
    }
}

