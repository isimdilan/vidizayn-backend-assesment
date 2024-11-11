<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;
use App\Models\Provider;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Mail\SendQueuedMailable;
use App\Mail\AppointmentCreated;

class UserUnitTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function login_user()
    {
        $user= User::create(
            [
                'name' => 'User2',
                'email' => 'user2@hotmail.com',
                'password' => bcrypt('user2123'),
            ]
            );
        $response = $this->post('/login', [
            'email' => 'user2@hotmail.com',
            'password' => 'user2123',
        ]);
        $response->assertRedirect('/dashboard');

        $this->assertDatabaseHas('users', ['email' => 'user2@hotmail.com']);

        $this->assertAuthenticated();
    }

    /** @test */
    public function login_user_with_invalid_credentials()
    {
        $response = $this->post('/login', [
            'email' => 'user2@hotmail.csfölşdom',
            'password' => 'user2123',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function create_appointment()
    {
        Queue::fake();
        Mail::fake();
        $user= User::create(
            [
                'name' => 'User3',
                'email' => 'user3@hotmail.com',
                'password' => bcrypt('user3123'),
            ]
            );
        
        $provideruser = User::create(
            [
                'name' => 'provideruser',
                'email' => 'provideruser@hotmail.com',
                'password' => bcrypt('provideruser3123'),
                'role'=>'provider',
            ]
            );

        $provider = Provider::create([
            'user_id' => $provideruser->id,
            'specialty' => 'text',
            'description' => 'test',
            'working_hours' => '9.00 - 17.00',
        ]);
        
        $appointment = Appointment::create([
            'user_id'=>$user->id,
            'provider_id'=>$provider->id,
            'time'=>'10:00:00',
            'date'=>'2024-12-10',
        ]);

        Mail::to($user->email)->queue(new AppointmentCreated($appointment));

        Mail::assertQueued(AppointmentCreated::class, function ($mail) use ($appointment) {
            return $mail->appointment->id === $appointment->id;
        });       

    }

    /** @test */
    public function provider_login()
    {
        $provider = User::create([
            'name' => 'Provider',
            'email' => 'provider@hotmail.com',
            'password' => bcrypt('provider123'),
            'role'=>'provider',
            
        ]);

        $response = $this->postJson('api/login', [
            'email'=>'provider@hotmail.com',
            'password'=>'provider123',
        ]);

        $response->assertStatus(200)->assertJson(['message'=>'Provider Login']);
        
    }

    
}
