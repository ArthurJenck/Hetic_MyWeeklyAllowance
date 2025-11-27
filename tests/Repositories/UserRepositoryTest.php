<?php

namespace Tests\Repositories;

use App\Repositories\UserRepository;

class UserRepositoryTest extends RepositoryTestCase
{
    private UserRepository $userRepo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepo = new UserRepository($this->pdo);
    }

    public function test_create_parent_creates_record(): void
    {
        // Act
        $id = $this->userRepo->createParent('John Doe', 'john@test.com', 'secret');

        // Assert
        $this->assertIsInt($id);
        $this->assertGreaterThan(0, $id);

        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertEquals('John Doe', $user['name']);
        $this->assertEquals('john@test.com', $user['email']);
        $this->assertEquals('parent', $user['role']);
    }

    public function test_create_teenager_creates_record(): void
    {
        // Arrange
        $parentId = $this->userRepo->createParent('John Doe', 'john@test.com', 'secret');

        // Act
        $teenId = $this->userRepo->createTeenager('Alice', '2010-01-01', 'alice@test.com', 'secret', $parentId);

        // Assert
        $this->assertIsInt($teenId);

        $user = $this->userRepo->findById($teenId);
        $this->assertEquals('Alice', $user['name']);
        $this->assertEquals('teenager', $user['role']);
        $this->assertEquals($parentId, $user['parent_id']);
    }

    public function test_find_parent_by_email(): void
    {
        // Arrange
        $this->userRepo->createParent('John Doe', 'john@test.com', 'secret');

        // Act
        $user = $this->userRepo->findParentByEmail('john@test.com');

        // Assert
        $this->assertNotNull($user);
        $this->assertEquals('John Doe', $user['name']);

        $notFound = $this->userRepo->findParentByEmail('unknown@test.com');
        $this->assertNull($notFound);
    }

    public function test_find_teenager_by_email(): void
    {
        // Arrange
        $parentId = $this->userRepo->createParent('John', 'j@t.com', 's');
        $this->userRepo->createTeenager('Teen', '2010-01-01', 'teen@test.com', 'secret', $parentId);

        // Act
        $user = $this->userRepo->findTeenagerByEmail('teen@test.com');

        // Assert
        $this->assertNotNull($user);
        $this->assertEquals('Teen', $user['name']);

        $notFound = $this->userRepo->findTeenagerByEmail('unknown@test.com');
        $this->assertNull($notFound);
    }

    public function test_find_teenager_by_name(): void
    {
        // Arrange
        $parentId = $this->userRepo->createParent('John', 'j@t.com', 's');
        $this->userRepo->createTeenager('Alice', '2010-01-01', 'a@t.com', 's', $parentId);

        // Act & Assert
        $user = $this->userRepo->findTeenagerByName('Alice', $parentId);
        $this->assertNotNull($user);

        $notFound = $this->userRepo->findTeenagerByName('Bob', $parentId);
        $this->assertNull($notFound);
    }

    public function test_find_teenagers_by_parent_id(): void
    {
        // Arrange
        $parentId = $this->userRepo->createParent('Parent', 'p@test.com', 'pass');
        $this->userRepo->createTeenager('T1', '2010-01-01', 't1@test.com', 'pass', $parentId);
        $this->userRepo->createTeenager('T2', '2012-01-01', 't2@test.com', 'pass', $parentId);

        // Act
        $teenagers = $this->userRepo->findTeenagersByParentId($parentId);

        // Assert
        $this->assertCount(2, $teenagers);
    }

    public function test_update_user(): void
    {
        // Arrange
        $id = $this->userRepo->createParent('Old Name', 'old@test.com', 'pass');

        // Act
        $result = $this->userRepo->updateUser($id, [
            'name' => 'New Name',
            'email' => 'new@test.com'
        ]);

        // Assert
        $this->assertTrue($result);

        $user = $this->userRepo->findById($id);
        $this->assertEquals('New Name', $user['name']);
        $this->assertEquals('new@test.com', $user['email']);
    }

    public function test_soft_delete_updates_deleted_at(): void
    {
        // Arrange
        $id = $this->userRepo->createParent('To Delete', 'del@test.com', 'pass');

        // Act
        $this->userRepo->softDelete($id);

        // Assert
        $user = $this->userRepo->findById($id);
        $this->assertNull($user);

        $stmt = $this->pdo->prepare("SELECT deleted_at FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $deletedAt = $stmt->fetchColumn();

        $this->assertNotNull($deletedAt);
    }
}
