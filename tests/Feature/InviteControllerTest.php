<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Invite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class InviteControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private array $validInviteData;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Prepare valid invite data for tests
        $this->validInviteData = [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'description' => $this->faker->sentence,
            'phone' => '1234567890'
        ];
    }

    /** @test */
    public function can_get_all_invites()
    {
        // Arrange
        Invite::factory()->count(3)->create();

        // Act
        $response = $this->getJson('/api/invites');

        // Assert
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'Invites' => [
                    '*' => [
                        'id',
                        'first_name',
                        'last_name',
                        'description',
                        'phone',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]);
        
        $this->assertEquals('تم جلب قائمة المدعوين بنجاح', $response['message']);
    }

    /** @test */
    public function can_create_invite()
    {
        // Act
        $response = $this->postJson('/api/invites', $this->validInviteData);

        // Assert
        $response
            ->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'Invite' => [
                    'id',
                    'first_name',
                    'last_name',
                    'description',
                    'phone',
                    'created_at',
                    'updated_at'
                ]
            ]);

        $this->assertEquals('تم إضافة المدعو بنجاح', $response['message']);
        $this->assertDatabaseHas('invites', $this->validInviteData);
    }

    /** @test */
    public function cannot_create_invite_with_invalid_data()
    {
        // Act
        $response = $this->postJson('/api/invites', []);

        // Assert
        $response
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'first_name',
                    'last_name',
                    'description',
                    'phone'
                ]
            ]);

        $this->assertEquals('فشل في التحقق من صحة البيانات', $response['message']);
    }

    /** @test */
    public function can_get_single_invite()
    {
        // Arrange
        $invite = Invite::factory()->create();

        // Act
        $response = $this->getJson("/api/invites/{$invite->id}");

        // Assert
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'Invite' => [
                    'id',
                    'first_name',
                    'last_name',
                    'description',
                    'phone',
                    'created_at',
                    'updated_at'
                ]
            ]);

        $this->assertEquals('تم العثور على بيانات المدعو بنجاح', $response['message']);
    }

    /** @test */
    public function returns_404_when_invite_not_found()
    {
        // Act
        $response = $this->getJson("/api/invites/999");

        // Assert
        $response
            ->assertStatus(404)
            ->assertJsonStructure([
                'message',
                'error'
            ]);

        $this->assertEquals('المدعو غير موجود', $response['message']);
    }

    /** @test */
    public function can_update_invite()
    {
        // Arrange
        $invite = Invite::factory()->create();
        $updateData = [
            'first_name' => 'UpdatedFirstName',
            'last_name' => 'UpdatedLastName',
            'description' => 'Updated description',
            'phone' => '9876543210'
        ];

        // Act
        $response = $this->putJson("/api/invites/{$invite->id}", $updateData);

        // Assert
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'Invite' => [
                    'id',
                    'first_name',
                    'last_name',
                    'description',
                    'phone',
                    'created_at',
                    'updated_at'
                ]
            ]);

        $this->assertEquals('تم تحديث بيانات المدعو بنجاح', $response['message']);
        $this->assertDatabaseHas('invites', $updateData);
    }

    /** @test */
    public function cannot_update_invite_with_invalid_data()
    {
        // Arrange
        $invite = Invite::factory()->create();

        // Act
        $response = $this->putJson("/api/invites/{$invite->id}", [
            'phone' => 'invalid_phone_number'
        ]);

        // Assert
        $response
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors'
            ]);

        $this->assertEquals('فشل في التحقق من صحة البيانات', $response['message']);
    }

    /** @test */
    public function can_delete_invite()
    {
        // Arrange
        $invite = Invite::factory()->create();

        // Act
        $response = $this->deleteJson("/api/invites/{$invite->id}");

        // Assert
        $response->assertStatus(204);
        $this->assertDatabaseMissing('invites', ['id' => $invite->id]);
    }

    /** @test */
    public function cannot_delete_nonexistent_invite()
    {
        // Act
        $response = $this->deleteJson("/api/invites/999");

        // Assert
        $response
            ->assertStatus(404)
            ->assertJsonStructure([
                'message',
                'error'
            ]);

        $this->assertEquals('المدعو غير موجود', $response['message']);
    }

    /** @test */
    public function can_update_invite_with_single_field()
    {
        // Arrange
        $invite = Invite::factory()->create([
            'first_name' => 'OldName',
            'last_name' => 'OldLastName',
            'description' => 'Old description',
            'phone' => '1234567890'
        ]);

        // Act
        $response = $this->putJson("/api/invites/{$invite->id}", [
            'first_name' => 'NewName'
        ]);

        // Assert
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'Invite' => [
                    'id',
                    'first_name',
                    'last_name',
                    'description',
                    'phone',
                    'created_at',
                    'updated_at'
                ]
            ]);

        $this->assertEquals('تم تحديث بيانات المدعو بنجاح', $response['message']);
        $this->assertEquals('NewName', $response['Invite']['first_name']);
        $this->assertEquals('OldLastName', $response['Invite']['last_name']);
        $this->assertEquals('Old description', $response['Invite']['description']);
        $this->assertEquals('1234567890', $response['Invite']['phone']);
    }

    /** @test */
    public function cannot_update_invite_with_empty_data()
    {
        // Arrange
        $invite = Invite::factory()->create();

        // Act
        $response = $this->putJson("/api/invites/{$invite->id}", []);

        // Assert
        $response
            ->assertStatus(422)
            ->assertJsonStructure([
                'status',
                'message',
                'errors' => [
                    'general'
                ]
            ]);

        $this->assertEquals('لم يتم تقديم أي بيانات للتحديث', $response['message']);
    }
} 