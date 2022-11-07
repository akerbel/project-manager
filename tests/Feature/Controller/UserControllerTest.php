<?php

namespace Tests\Feature\Controller;

use App\Models\User;

class UserControllerTest extends AbstractApiTest
{
    private array $defaultTestUserData = [
        'name' => 'Test User',
        'email' => 'testuser@mail.com',
        'password' => '12345678',
    ];

    /**
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function testPost()
    {
        $user = $this->createAdminUser();

        $response = $this->apiCallAuthorizated($user, 'POST', '/user', $this->defaultTestUserData);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertDatabaseHas('users', [
            'name' => $this->defaultTestUserData['name'],
            'email' => $this->defaultTestUserData['email'],
            'email_verified_at' => null,
            'role' => User::USER_ROLE,
        ]);

        $emails = app()->make('mailer')->getSymfonyTransport()->messages();
        $this->assertEquals(1, count($emails));
    }

    /**
     * @param array $data
     *
     * @return void
     *
     * @dataProvider registerValidationErrorsDataProvider
     */
    public function testPostError400(array $data)
    {
        $user = $this->createAdminUser();

        $response = $this->apiCallAuthorizated($user, 'POST', '/user', $data);
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testPostError403()
    {
        $user = $this->createUser();

        $response = $this->apiCallAuthorizated($user, 'POST', '/user', $this->defaultTestUserData);
        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testPatch()
    {
        // User edits self.
        $user = $this->createUser();
        $newName = $this->defaultTestUserData['name'] . ' Edited';
        $response = $this->apiCallAuthorizated($user, 'PATCH', '/user', [
            'name' => $newName,
        ]);
        $this->assertEquals(200, $response->getStatusCode());
        $user->refresh();
        $this->assertEquals($newName, $user->name);

        // Admin edits user.
        $adminUser = $this->createAdminUser();
        $newName = $this->defaultTestUserData['name'] . ' Edited by admin';
        $response = $this->apiCallAuthorizated($adminUser, 'PATCH', '/user/' . $user->id, [
            'name' => $newName,
        ]);
        $this->assertEquals(200, $response->getStatusCode());
        $user->refresh();
        $this->assertEquals($newName, $user->name);
    }

    /**
     * @return void
     */
    public function testPatchError400()
    {
        $user = $this->createUser();

        $response = $this->apiCallAuthorizated($user, 'PATCH', '/user', [
            'password' => '123',
        ]);
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testPatchError403()
    {
        $user = $this->createUser();
        $adminUser = $this->createAdminUser();

        $response = $this->apiCallAuthorizated($user, 'PATCH', '/user/' . $adminUser->id, $this->defaultTestUserData);
        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testPatchError404()
    {
        $user = $this->createAdminUser();

        $response = $this->apiCallAuthorizated($user, 'PATCH', '/user/123', $this->defaultTestUserData);
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testDelete()
    {
        $user = $this->createUser();
        $email = $user->email;
        $adminUser = $this->createAdminUser();

        $response = $this->apiCallAuthorizated($adminUser, 'DELETE', '/user/' . $user->id);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertDatabaseMissing('users', ['email' => $email]);
    }

    /**
     * @return void
     */
    public function testDeleteError403()
    {
        $user = $this->createUser();
        $adminUser = $this->createAdminUser();

        $response = $this->apiCallAuthorizated($user, 'DELETE', '/user/' . $adminUser->id);
        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testDeleteError404()
    {
        $user = $this->createAdminUser();

        $response = $this->apiCallAuthorizated($user, 'DELETE', '/user/123');
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testGet()
    {
        // User gets self.
        $user = $this->createUser();
        $response = $this->apiCallAuthorizated($user, 'GET', '/user');
        $this->assertEquals(200, $response->getStatusCode());
        $content = json_decode($response->getContent(), 1);
        $this->assertEquals($user->name, $content['name']);
        $this->assertEquals($user->email, $content['email']);

        // Admin gets user.
        $adminUser = $this->createAdminUser();
        $response = $this->apiCallAuthorizated($adminUser, 'GET', '/user/' . $user->id);
        $this->assertEquals(200, $response->getStatusCode());
        $content = json_decode($response->getContent(), 1);
        $this->assertEquals($user->name, $content['name']);
        $this->assertEquals($user->email, $content['email']);
    }

    /**
     * @return void
     */
    public function testGetError403()
    {
        $user = $this->createUser();
        $adminUser = $this->createAdminUser();

        $response = $this->apiCallAuthorizated($user, 'GET', '/user/' . $adminUser->id);
        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testGetError404()
    {
        $user = $this->createAdminUser();

        $response = $this->apiCallAuthorizated($user, 'GET', '/user/123', $this->defaultTestUserData);
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testGetAll()
    {
        // Create two users.
        $user = $this->createUser();
        $adminUser = $this->createAdminUser();

        $response = $this->apiCallAuthorizated($adminUser, 'GET', '/users');
        $this->assertEquals(200, $response->getStatusCode());
        $content = json_decode($response->getContent(), 1);
        $this->assertEquals(2, count($content));
    }

    /**
     * @return void
     */
    public function testGetAllError403()
    {
        // Create two users.
        $user = $this->createUser();
        $adminUser = $this->createAdminUser();

        $response = $this->apiCallAuthorizated($user, 'GET', '/users');
        $this->assertEquals(403, $response->getStatusCode());
    }

}
