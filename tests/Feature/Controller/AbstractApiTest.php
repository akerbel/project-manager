<?php

namespace Tests\Feature\Controller;

use App\Models\Category;
use App\Models\Project;
use App\Models\Situation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

abstract class AbstractApiTest extends TestCase
{

    use RefreshDatabase;

    const USER_TEST_PASSWORD = '12345678';
    const USER_TEST_EMAIL = 'stansmith@mail.com';
    const USER_ADMIN_TEST_EMAIL = 'adminsmith@mail.com';

    protected $apiPrefix = '/api';

    /**
     * @var array|string[]
     */
    protected array $defaultCategoryData = [
        'name' => 'Test Category 1',
        'description' => 'Test Category 1 Description',
    ];

    /**
     * @var array|string[]
     */
    protected array $defaultProjectData = [
        'name' => 'Project 1',
        'description' => 'Description 1',
    ];

    protected array $defaultSituationData = [
        'name' => 'Name 1',
        'description' => 'Description 1',
        'status' => Situation::STATUS_PLANNED,
    ];

    /**
     * Api call without authorization.
     *
     * @param string $method
     * @param string $uri
     * @param array $params
     *
     * @return \Illuminate\Testing\TestResponse
     */
    protected function apiCall(string $method, string $uri, array $params = [])
    {
        return $this->json($method, $this->apiPrefix . $uri, $params);
    }

    /**
     * Api call with authorization.
     *
     * @param User $user
     * @param string $method
     * @param string $uri
     * @param array $params
     *
     * @return \Illuminate\Testing\TestResponse
     */
    protected function apiCallAuthorizated(User $user, string $method, string $uri, array $params = [])
    {
        $token = $this->createToken($user);
        return $this->json($method, $this->apiPrefix . $uri, $params, [
            'Authorization' => 'Bearer ' . $token,
        ]);
    }

    /**
     * Create default user in DB.
     *
     * @param bool $verified
     *
     * @return User
     */
    protected function createUser(bool $verified = true): User
    {
        $user = new User([
            'name' => 'Stan Smith',
        ]);
        $user->setEmail(self::USER_TEST_EMAIL);
        $user->setPassword(self::USER_TEST_PASSWORD);
        if ($verified) {
            $user->markEmailAsVerified();
        }
        $user->save();

        return $user;
    }

    /**
     * Create admin user in DB.
     *
     * @param bool $verified
     *
     * @return User
     */
    protected function createAdminUser(bool $verified = true): User
    {
        $user = new User([
            'name' => 'Admin Smith',
            'role' => User::ADMIN_ROLE,
        ]);
        $user->setEmail(self::USER_ADMIN_TEST_EMAIL);
        $user->setPassword(self::USER_TEST_PASSWORD);
        if ($verified) {
            $user->markEmailAsVerified();
        }
        $user->save();

        return $user;
    }

    /**
     * Get access token for the user.
     *
     * @param User $user
     *
     * @return string
     */
    protected function createToken(User $user): string
    {
        return $user->createToken('authToken')->plainTextToken;
    }

    /**
     * Create test category.
     *
     * @return Category
     */
    protected function createCategory(): Category
    {
        $category = new Category($this->defaultCategoryData);
        $category->save();
        return $category;
    }

    /**
     * Create test project.
     *
     * @param User $user
     *
     * @return Project
     */
    protected function createProject(User $user): Project
    {
        $category = $this->createCategory();
        $project = new Project($this->defaultProjectData);
        $project->user()->associate($user);
        $project->save();
        $project->categories()->attach($category->id);
        return $project;
    }

    /**
     * Create test situation.
     *
     * @param Project $project
     *
     * @return Situation
     */
    protected function createSituation(Project $project): Situation
    {
        $situation = new Situation($this->defaultSituationData);
        $situation->project()->associate($project);
        $situation->save();
        return $situation;
    }

    /**
     * Data provider for user creation tests.
     *
     * @return \string[][][]
     */
    public function registerValidationErrorsDataProvider(): array
    {
        return [
            [
                'data' => [
                    'name' => 'Test User',
                    'email' => 'testuser@test.com',
                    'password' => '123',
                ],
            ],
            [
                'data' => [
                    'email' => 'testuser@test.com',
                    'password' => '12345678',
                ],
            ],
            [
                'data' => [
                    'name' => 'Test User',
                    'password' => '12345678',
                ],
            ],
            [
                'data' => [
                    'name' => 'Test User',
                    'email' => 'testuser@test.com',
                ],
            ],
        ];
    }

}
