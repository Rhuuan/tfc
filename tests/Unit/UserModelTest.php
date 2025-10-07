<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa se o modelo User possui os traits necessários.
     */
    public function test_user_model_has_required_traits(): void
    {
        $user = new User();
        
        $this->assertContains(HasFactory::class, class_uses($user));
        $this->assertContains(Notifiable::class, class_uses($user));
    }

    /**
     * Testa se o modelo User estende a classe correta.
     */
    public function test_user_model_extends_authenticatable(): void
    {
        $user = new User();
        
        $this->assertInstanceOf(Authenticatable::class, $user);
    }

    /**
     * Testa se os fillable estão configurados corretamente.
     */
    public function test_user_model_has_correct_fillable_attributes(): void
    {
        $user = new User();
        $expectedFillable = ['name', 'email', 'password'];
        
        $this->assertEquals($expectedFillable, $user->getFillable());
    }

    /**
     * Testa se os hidden estão configurados corretamente.
     */
    public function test_user_model_has_correct_hidden_attributes(): void
    {
        $user = new User();
        $expectedHidden = ['password', 'remember_token'];
        
        $this->assertEquals($expectedHidden, $user->getHidden());
    }

    /**
     * Testa se os casts estão configurados corretamente.
     */
    public function test_user_model_has_correct_casts(): void
    {
        $user = new User();
        $expectedCasts = [
            'id' => 'int',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
        
        $this->assertEquals($expectedCasts, $user->getCasts());
    }

    /**
     * Testa se é possível criar um usuário com dados válidos.
     */
    public function test_can_create_user_with_valid_data(): void
    {
        $userData = [
            'name' => 'João Silva',
            'email' => 'joao@example.com',
            'password' => 'password123',
        ];

        $user = User::create($userData);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('João Silva', $user->name);
        $this->assertEquals('joao@example.com', $user->email);
        $this->assertTrue(Hash::check('password123', $user->password));
        $this->assertDatabaseHas('users', [
            'name' => 'João Silva',
            'email' => 'joao@example.com',
        ]);
    }

    /**
     * Testa se a senha é automaticamente hasheada.
     */
    public function test_password_is_hashed_automatically(): void
    {
        $user = User::create([
            'name' => 'Maria Santos',
            'email' => 'maria@example.com',
            'password' => 'plaintext-password',
        ]);

        $this->assertNotEquals('plaintext-password', $user->password);
        $this->assertTrue(Hash::check('plaintext-password', $user->password));
    }

    /**
     * Testa se a senha não é retornada na serialização.
     */
    public function test_password_is_hidden_in_serialization(): void
    {
        $user = User::factory()->create([
            'password' => 'secret-password',
        ]);

        $userArray = $user->toArray();

        $this->assertArrayNotHasKey('password', $userArray);
        $this->assertArrayNotHasKey('remember_token', $userArray);
    }

    /**
     * Testa se o timestamp email_verified_at é cast corretamente.
     */
    public function test_email_verified_at_is_cast_to_datetime(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => '2023-01-01 12:00:00',
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $user->email_verified_at);
    }

    /**
     * Testa se é possível usar a factory do usuário.
     */
    public function test_can_use_user_factory(): void
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);
        $this->assertNotEmpty($user->name);
        $this->assertNotEmpty($user->email);
        $this->assertNotEmpty($user->password);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => $user->email,
        ]);
    }

    /**
     * Testa se é possível criar múltiplos usuários com a factory.
     */
    public function test_can_create_multiple_users_with_factory(): void
    {
        $users = User::factory()->count(3)->create();

        $this->assertCount(3, $users);
        $this->assertEquals(3, User::count());
        
        foreach ($users as $user) {
            $this->assertInstanceOf(User::class, $user);
            $this->assertDatabaseHas('users', [
                'id' => $user->id,
                'email' => $user->email,
            ]);
        }
    }

    /**
     * Testa se o email deve ser único.
     */
    public function test_email_must_be_unique(): void
    {
        $email = 'test@example.com';
        
        User::factory()->create(['email' => $email]);

        $this->expectException(\Illuminate\Database\QueryException::class);
        
        User::factory()->create(['email' => $email]);
    }

    /**
     * Testa se os atributos obrigatórios são validados.
     */
    public function test_required_attributes_cannot_be_null(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        User::create([
            'name' => null,
            'email' => null,
            'password' => null,
        ]);
    }

    /**
     * Testa se é possível atualizar um usuário.
     */
    public function test_can_update_user(): void
    {
        $user = User::factory()->create([
            'name' => 'Nome Original',
            'email' => 'original@example.com',
        ]);

        $user->update([
            'name' => 'Nome Atualizado',
            'email' => 'atualizado@example.com',
        ]);

        $this->assertEquals('Nome Atualizado', $user->fresh()->name);
        $this->assertEquals('atualizado@example.com', $user->fresh()->email);
        
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Nome Atualizado',
            'email' => 'atualizado@example.com',
        ]);
    }

    /**
     * Testa se é possível deletar um usuário.
     */
    public function test_can_delete_user(): void
    {
        $user = User::factory()->create();
        $userId = $user->id;

        $user->delete();

        $this->assertNull(User::find($userId));
        $this->assertDatabaseMissing('users', [
            'id' => $userId,
        ]);
    }

    /**
     * Testa se o modelo tem o nome da tabela correto.
     */
    public function test_user_model_uses_correct_table(): void
    {
        $user = new User();
        
        $this->assertEquals('users', $user->getTable());
    }

    /**
     * Testa se o modelo tem a primary key correta.
     */
    public function test_user_model_has_correct_primary_key(): void
    {
        $user = new User();
        
        $this->assertEquals('id', $user->getKeyName());
        $this->assertTrue($user->getIncrementing());
    }
}
