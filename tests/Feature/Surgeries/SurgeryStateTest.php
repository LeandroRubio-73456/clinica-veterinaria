<?php

namespace Tests\Feature\Surgeries;

use App\Models\Surgery;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SurgeryStateTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    /*
    |--------------------------------------------------------------------------
    | scheduled -> in_progress
    |--------------------------------------------------------------------------
    */

    public function test_scheduled_surgery_can_be_started(): void
    {
        $surgery = Surgery::factory()->create([
            'state' => 'scheduled',
            'actual_start_time' => null,
            'actual_end_time' => null,
        ]);

        $response = $this
            ->actingAs($this->user)
            ->post(route('surgeries.start', $surgery));

        $response
            ->assertRedirect(route('surgeries.show', $surgery))
            ->assertSessionHas(
                'success',
                'La cirugía ha sido iniciada correctamente.'
            );

        $surgery->refresh();

        $this->assertSame('in_progress', $surgery->state);
        $this->assertNotNull($surgery->actual_start_time);
        $this->assertNull($surgery->actual_end_time);
    }

    public function test_in_progress_surgery_cannot_be_started_again(): void
    {
        $surgery = Surgery::factory()->create([
            'state' => 'in_progress',
            'actual_start_time' => '09:00:00',
            'actual_end_time' => null,
        ]);

        $originalStartTime = $surgery->actual_start_time;

        $response = $this
            ->actingAs($this->user)
            ->post(route('surgeries.start', $surgery));

        $response
            ->assertRedirect(route('surgeries.show', $surgery))
            ->assertSessionHas(
                'error',
                'Solo se puede iniciar una cirugía que esté programada.'
            );

        $surgery->refresh();

        $this->assertSame('in_progress', $surgery->state);
        $this->assertSame(
            $originalStartTime,
            $surgery->actual_start_time
        );
    }

    /*
    |--------------------------------------------------------------------------
    | in_progress -> completed
    |--------------------------------------------------------------------------
    */

    public function test_in_progress_surgery_can_be_completed(): void
    {
        $surgery = Surgery::factory()->create([
            'state' => 'in_progress',
            'actual_start_time' => '09:00:00',
            'actual_end_time' => null,
        ]);

        $response = $this
            ->actingAs($this->user)
            ->post(route('surgeries.complete', $surgery));

        $response
            ->assertRedirect(route('surgeries.show', $surgery))
            ->assertSessionHas(
                'success',
                'La cirugía ha sido finalizada correctamente.'
            );

        $surgery->refresh();

        $this->assertSame('completed', $surgery->state);
        $this->assertNotNull($surgery->actual_start_time);
        $this->assertNotNull($surgery->actual_end_time);
    }

    public function test_scheduled_surgery_cannot_be_completed_directly(): void
    {
        $surgery = Surgery::factory()->create([
            'state' => 'scheduled',
            'actual_start_time' => null,
            'actual_end_time' => null,
        ]);

        $response = $this
            ->actingAs($this->user)
            ->post(route('surgeries.complete', $surgery));

        $response
            ->assertRedirect(route('surgeries.show', $surgery))
            ->assertSessionHas(
                'error',
                'Solo se puede finalizar una cirugía que esté en curso.'
            );

        $surgery->refresh();

        $this->assertSame('scheduled', $surgery->state);
        $this->assertNull($surgery->actual_start_time);
        $this->assertNull($surgery->actual_end_time);
    }

    /*
    |--------------------------------------------------------------------------
    | scheduled -> cancelled
    |--------------------------------------------------------------------------
    */

    public function test_scheduled_surgery_can_be_cancelled(): void
    {
        $surgery = Surgery::factory()->create([
            'state' => 'scheduled',
            'actual_start_time' => null,
            'actual_end_time' => null,
        ]);

        $response = $this
            ->actingAs($this->user)
            ->post(route('surgeries.cancel', $surgery));

        $response
            ->assertRedirect(route('surgeries.show', $surgery))
            ->assertSessionHas(
                'success',
                'La cirugía ha sido cancelada.'
            );

        $surgery->refresh();

        $this->assertSame('cancelled', $surgery->state);
        $this->assertNull($surgery->actual_start_time);
        $this->assertNull($surgery->actual_end_time);
    }

    public function test_in_progress_surgery_cannot_be_cancelled(): void
    {
        $surgery = Surgery::factory()->create([
            'state' => 'in_progress',
            'actual_start_time' => '09:00:00',
            'actual_end_time' => null,
        ]);

        $response = $this
            ->actingAs($this->user)
            ->post(route('surgeries.cancel', $surgery));

        $response
            ->assertRedirect(route('surgeries.show', $surgery))
            ->assertSessionHas(
                'error',
                'Solo se puede cancelar una cirugía que esté programada.'
            );

        $surgery->refresh();

        $this->assertSame('in_progress', $surgery->state);
    }

    /*
    |--------------------------------------------------------------------------
    | scheduled -> no_show
    |--------------------------------------------------------------------------
    */

    public function test_scheduled_surgery_can_be_marked_as_no_show(): void
    {
        $surgery = Surgery::factory()->create([
            'state' => 'scheduled',
            'actual_start_time' => null,
            'actual_end_time' => null,
        ]);

        $response = $this
            ->actingAs($this->user)
            ->post(route('surgeries.no-show', $surgery));

        $response
            ->assertRedirect(route('surgeries.show', $surgery))
            ->assertSessionHas(
                'success',
                'La cirugía ha sido marcada como no presentada.'
            );

        $surgery->refresh();

        $this->assertSame('no_show', $surgery->state);
        $this->assertNull($surgery->actual_start_time);
        $this->assertNull($surgery->actual_end_time);
    }

    public function test_in_progress_surgery_cannot_be_marked_as_no_show(): void
    {
        $surgery = Surgery::factory()->create([
            'state' => 'in_progress',
            'actual_start_time' => '09:00:00',
            'actual_end_time' => null,
        ]);

        $response = $this
            ->actingAs($this->user)
            ->post(route('surgeries.no-show', $surgery));

        $response
            ->assertRedirect(route('surgeries.show', $surgery))
            ->assertSessionHas(
                'error',
                'Solo se puede marcar como no presentado una cirugía programada.'
            );

        $surgery->refresh();

        $this->assertSame('in_progress', $surgery->state);
    }

    /*
    |--------------------------------------------------------------------------
    | Estados finales
    |--------------------------------------------------------------------------
    */

    public function test_completed_surgery_cannot_change_state(): void
    {
        $surgery = Surgery::factory()->create([
            'state' => 'completed',
            'actual_start_time' => '09:00:00',
            'actual_end_time' => '10:00:00',
        ]);

        $this
            ->actingAs($this->user)
            ->post(route('surgeries.start', $surgery))
            ->assertSessionHas('error');

        $this
            ->actingAs($this->user)
            ->post(route('surgeries.complete', $surgery))
            ->assertSessionHas('error');

        $this
            ->actingAs($this->user)
            ->post(route('surgeries.cancel', $surgery))
            ->assertSessionHas('error');

        $this
            ->actingAs($this->user)
            ->post(route('surgeries.no-show', $surgery))
            ->assertSessionHas('error');

        $surgery->refresh();

        $this->assertSame('completed', $surgery->state);
        $this->assertSame('09:00:00', $surgery->actual_start_time);
        $this->assertSame('10:00:00', $surgery->actual_end_time);
    }

    public function test_cancelled_surgery_cannot_change_state(): void
    {
        $surgery = Surgery::factory()->create([
            'state' => 'cancelled',
            'actual_start_time' => null,
            'actual_end_time' => null,
        ]);

        $this
            ->actingAs($this->user)
            ->post(route('surgeries.start', $surgery))
            ->assertSessionHas('error');

        $this
            ->actingAs($this->user)
            ->post(route('surgeries.complete', $surgery))
            ->assertSessionHas('error');

        $this
            ->actingAs($this->user)
            ->post(route('surgeries.cancel', $surgery))
            ->assertSessionHas('error');

        $this
            ->actingAs($this->user)
            ->post(route('surgeries.no-show', $surgery))
            ->assertSessionHas('error');

        $surgery->refresh();

        $this->assertSame('cancelled', $surgery->state);
    }

    public function test_no_show_surgery_cannot_change_state(): void
    {
        $surgery = Surgery::factory()->create([
            'state' => 'no_show',
            'actual_start_time' => null,
            'actual_end_time' => null,
        ]);

        $this
            ->actingAs($this->user)
            ->post(route('surgeries.start', $surgery))
            ->assertSessionHas('error');

        $this
            ->actingAs($this->user)
            ->post(route('surgeries.complete', $surgery))
            ->assertSessionHas('error');

        $this
            ->actingAs($this->user)
            ->post(route('surgeries.cancel', $surgery))
            ->assertSessionHas('error');

        $this
            ->actingAs($this->user)
            ->post(route('surgeries.no-show', $surgery))
            ->assertSessionHas('error');

        $surgery->refresh();

        $this->assertSame('no_show', $surgery->state);
    }
}