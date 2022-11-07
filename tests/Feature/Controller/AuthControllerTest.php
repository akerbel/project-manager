<?php

namespace Tests\Feature\Controller;

use App\Models\User;
use Illuminate\Support\Facades\URL;

class AuthControllerTest extends AbstractApiTest
{

    /**
     * Test user registration.
     *
     * @return void
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function testRegister()
    {
        $response = $this->apiCall('POST', '/register', [
            'name' => 'Test user',
            'email' => 'testuser@test.com',
            'password' => self::USER_TEST_PASSWORD,
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertDatabaseHas('users', [
            'name' => 'Test user',
            'email' => 'testuser@test.com',
            'email_verified_at' => null,
            'role' => User::USER_ROLE,
        ]);

        $emails = app()->make('mailer')->getSymfonyTransport()->messages();
        $this->assertEquals(1, count($emails));
    }

    /**
     * Test registration validation errors.
     *
     * @param array $data
     * @return void
     *
     * @dataProvider registerValidationErrorsDataProvider
     */
    public function testRegisterValidationErrors(array $data)
    {
        $response = $this->apiCall('POST', '/register', $data);
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**
     * Test user login.
     *
     * @return void
     */
    public function testLogin() {
        $user = $this->createUser(false);

        $response = $this->apiCall('POST', '/login', [
            'email' => $user->email,
            'password' => self::USER_TEST_PASSWORD,
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $content = json_decode($response->getContent(), 1);
        $this->assertArrayHasKey('access_token', $content);
        $this->assertNotEmpty($content['access_token']);
        $this->assertEquals('Bearer', $content['token_type']);
    }

    /**
     * Test email verification.
     *
     * @return void
     */
    public function testEmailVerify()
    {
        $user = $this->createUser(false);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );
        $uri = substr($verificationUrl, strpos($verificationUrl, '/email/verify'));

        $response = $this->apiCallAuthorizated($user, 'GET', $uri);

        $this->assertEquals(200, $response->getStatusCode());
        $user->refresh();
        $this->assertTrue($user->hasVerifiedEmail());
    }

}
