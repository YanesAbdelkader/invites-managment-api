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
                        'presence',
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
                    'presence',
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
        // Arrange: Test cases for validation
        $testCases = [
            'empty_data' => [],
            'missing_first_name' => array_merge($this->validInviteData, ['first_name' => '']),
            'missing_last_name' => array_merge($this->validInviteData, ['last_name' => '']),
            'missing_description' => array_merge($this->validInviteData, ['description' => '']),
            'invalid_phone' => array_merge($this->validInviteData, ['phone' => '12345678901']) // More than 10 digits
        ];

        foreach ($testCases as $case => $data) {
            // Act
            $response = $this->postJson('/api/invites', $data);

            // Assert
            $response
                ->assertStatus(422)
                ->assertJsonStructure([
                    'message',
                    'errors'
                ]);

            $this->assertEquals('فشل في التحقق من صحة البيانات', $response['message']);
        }
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
                    'presence',
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
            ->assertJson([
                'message' => 'المدعو غير موجود'
            ]);
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
                    'presence',
                    'created_at',
                    'updated_at'
                ]
            ]);

        $this->assertEquals('تم تحديث بيانات المدعو بنجاح', $response['message']);
        $this->assertDatabaseHas('invites', $updateData);
    }

    /** @test */
    public function can_update_invite_with_partial_data()
    {
        // Arrange
        $invite = Invite::factory()->create();
        $partialUpdate = ['first_name' => 'UpdatedFirstName'];

        // Act
        $response = $this->putJson("/api/invites/{$invite->id}", $partialUpdate);

        // Assert
        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'تم تحديث بيانات المدعو بنجاح',
                'Invite' => [
                    'first_name' => 'UpdatedFirstName',
                    'last_name' => $invite->last_name,
                    'description' => $invite->description,
                    'phone' => $invite->phone
                ]
            ]);
    }

    /** @test */
    public function cannot_update_invite_with_invalid_data()
    {
        // Arrange
        $invite = Invite::factory()->create();
        $invalidData = [
            'first_name' => str_repeat('a', 256), // Exceeds max length
            'phone' => '12345678901' // Exceeds max length
        ];

        // Act
        $response = $this->putJson("/api/invites/{$invite->id}", $invalidData);

        // Assert
        $response
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors'
            ]);
    }

    /** @test */
    public function can_update_presence_status()
    {
        // Arrange
        $invite = Invite::factory()->create();
        $presenceStates = ['حاضر', 'غائب', 'لم يتم التسجيل'];

        foreach ($presenceStates as $presence) {
            // Act
            $response = $this->putJson("/api/invites/{$invite->id}/presence", [
                'presence' => $presence
            ]);

            // Assert
            $response
                ->assertStatus(200)
                ->assertJson([
                    'message' => 'تم تحديث حالة المدعو بنجاح',
                    'Invite' => [
                        'presence' => $presence
                    ]
                ]);

            $this->assertDatabaseHas('invites', [
                'id' => $invite->id,
                'presence' => $presence
            ]);
        }
    }

    /** @test */
    public function cannot_update_presence_with_invalid_status()
    {
        // Arrange
        $invite = Invite::factory()->create();

        // Act
        $response = $this->putJson("/api/invites/{$invite->id}/presence", [
            'presence' => 'invalid_status'
        ]);

        // Assert
        $response->assertStatus(422);
    }

    /** @test */
    public function can_delete_invite()
    {
        // Arrange
        $invite = Invite::factory()->create();

        // Act
        $response = $this->deleteJson("/api/invites/{$invite->id}");

        // Assert
        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'تم حذف المدعو بنجاح'
            ]);

        $this->assertDatabaseMissing('invites', ['id' => $invite->id]);
    }

    /** @test */
    public function create_and_edit_endpoints_return_405()
    {
        $this->getJson('/api/invites/create')->assertStatus(405);
        $this->getJson('/api/invites/1/edit')->assertStatus(405);
    }
} 