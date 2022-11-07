<?php

namespace Tests\Feature\Controller;

class ProjectControllerTest extends AbstractApiTest
{

    /**
     * @return void
     */
    public function testPost()
    {
        $user = $this->createUser();
        $category = $this->createCategory();
        $projectData = $this->defaultProjectData;
        $projectData['categories'] = [$category->id];

        $response = $this->apiCallAuthorizated($user, 'POST', '/project', $projectData);
        $this->assertEquals(200, $response->getStatusCode());
        $content = json_decode($response->getContent(), 1);
        $this->assertArrayHasKey('id', $content);
        $this->assertDatabaseHas('projects', [
            'name' => $this->defaultProjectData['name'],
            'description' => $this->defaultProjectData['description'],
            'user_id' => $user->id,
        ]);
        $this->assertDatabaseHas('projects_categories', [
            'project_id' => $content['id'],
            'category_id' => $category->id,
        ]);
    }

    /**
     * @param array $data
     * @return void
     *
     * @dataProvider validationErrorsDataProvider
     */
    public function testPostError400(array $data)
    {
        $user = $this->createUser();
        $category = $this->createCategory();
        if (isset($data['categories']) && is_array($data['categories'])) {
            $data['categories'] = [$category->id];
        }

        $response = $this->apiCallAuthorizated($user, 'POST', '/project', $data);
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testPatch()
    {
        // User edits his project.
        $user = $this->createUser();
        $project = $this->createProject($user);

        $newProjectName = 'New project name';
        $newProjectDescription = 'New project description';
        $newCategory = $this->createCategory();
        $oldCategory = $project->categories()->first();

        $response = $this->apiCallAuthorizated($user, 'PATCH', '/project/' . $project->id, [
            'name' => $newProjectName,
            'description' => $newProjectDescription,
            'categories' => [$newCategory->id],
        ]);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertDatabaseHas('projects', [
            'name' => $newProjectName,
            'description' => $newProjectDescription,
            'user_id' => $user->id,
        ]);
        $this->assertDatabaseHas('projects_categories', [
            'project_id' => $project->id,
            'category_id' => $newCategory->id,
        ]);
        $this->assertDatabaseMissing('projects_categories', [
            'project_id' => $project->id,
            'category_id' => $oldCategory->id,
        ]);

        // Admin edits user's project
        $adminUser = $this->createAdminUser();
        $newProjectName = 'New project name by admin';
        $newProjectDescription = 'New project description by admin';
        $newCategory = $this->createCategory();
        $oldCategory = $project->categories()->first();

        $response = $this->apiCallAuthorizated($adminUser, 'PATCH', '/project/' . $project->id, [
            'name' => $newProjectName,
            'description' => $newProjectDescription,
            'categories' => [$newCategory->id],
        ]);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertDatabaseHas('projects', [
            'name' => $newProjectName,
            'description' => $newProjectDescription,
            'user_id' => $user->id,
        ]);
        $this->assertDatabaseHas('projects_categories', [
            'project_id' => $project->id,
            'category_id' => $newCategory->id,
        ]);
        $this->assertDatabaseMissing('projects_categories', [
            'project_id' => $project->id,
            'category_id' => $oldCategory->id,
        ]);
    }

    /**
     * @return void
     */
    public function testPatchError400()
    {
        $user = $this->createUser();
        $project = $this->createProject($user);

        $response = $this->apiCallAuthorizated($user, 'PATCH', '/project/' . $project->id, [
            'categories' => 'string instead of array',
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
        $project = $this->createProject($adminUser);

        $response = $this->apiCallAuthorizated($user, 'PATCH', '/project/' . $project->id, [
            'name' => 'New project name',
        ]);
        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testPatchError404()
    {
        $user = $this->createUser();

        $response = $this->apiCallAuthorizated($user, 'PATCH', '/project/123', [
            'name' => 'New project name',
        ]);
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testDelete()
    {
        // User deletes his project.
        $user = $this->createUser();
        $project = $this->createProject($user);
        $projectId = $project->id;
        $this->createSituation($project);

        $response = $this->apiCallAuthorizated($user, 'DELETE', '/project/' . $projectId);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertDatabaseMissing('projects', [
            'id' => $projectId,
        ]);
        $this->assertDatabaseMissing('projects_categories', [
            'project_id' => $projectId,
        ]);
        $this->assertDatabaseMissing('situations', [
            'project_id' => $projectId,
        ]);

        // Admin deletes user's project
        $adminUser = $this->createAdminUser();
        $project = $this->createProject($user);
        $projectId = $project->id;
        $this->createSituation($project);

        $response = $this->apiCallAuthorizated($adminUser, 'DELETE', '/project/' . $projectId);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertDatabaseMissing('projects', [
            'id' => $projectId,
        ]);
        $this->assertDatabaseMissing('projects_categories', [
            'project_id' => $projectId,
        ]);
        $this->assertDatabaseMissing('situations', [
            'project_id' => $projectId,
        ]);
    }

    /**
     * @return void
     */
    public function testDeleteError403()
    {
        $user = $this->createUser();
        $adminUser = $this->createAdminUser();
        $project = $this->createProject($adminUser);

        $response = $this->apiCallAuthorizated($user, 'DELETE', '/project/' . $project->id);
        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testDeleteError404()
    {
        $user = $this->createUser();

        $response = $this->apiCallAuthorizated($user, 'PATCH', '/project/123');
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testGet()
    {
        // User gets his project.
        $user = $this->createUser();
        $project = $this->createProject($user);

        $response = $this->apiCallAuthorizated($user, 'GET', '/project/' . $project->id);
        $this->assertEquals(200, $response->getStatusCode());
        $content = json_decode($response->getContent(), 1);
        $this->assertEquals($this->defaultProjectData['name'], $content['name']);
        $this->assertEquals($this->defaultProjectData['description'], $content['description']);
        $this->assertEquals($user->id, $content['user_id']);
        $this->assertEquals(1, count($content['categories']));

        // Admin deletes user's project
        $adminUser = $this->createAdminUser();

        $response = $this->apiCallAuthorizated($adminUser, 'GET', '/project/' . $project->id);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($this->defaultProjectData['name'], $content['name']);
        $this->assertEquals($this->defaultProjectData['description'], $content['description']);
        $this->assertEquals($user->id, $content['user_id']);
        $this->assertEquals(1, count($content['categories']));
    }

    /**
     * @return void
     */
    public function testGetError403()
    {
        $user = $this->createUser();
        $adminUser = $this->createAdminUser();
        $project = $this->createProject($adminUser);

        $response = $this->apiCallAuthorizated($user, 'GET', '/project/' . $project->id);
        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testGetError404()
    {
        $user = $this->createUser();

        $response = $this->apiCallAuthorizated($user, 'GET', '/project/123');
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testGetAll()
    {
        $user = $this->createUser();
        $adminUser = $this->createAdminUser();
        $project1 = $this->createProject($user);
        $project2 = $this->createProject($adminUser);

        $response = $this->apiCallAuthorizated($user, 'GET', '/projects');
        $this->assertEquals(200, $response->getStatusCode());
        $content = json_decode($response->getContent(), 1);
        $this->assertCount(1, $content);
        $this->assertEquals($project1->id, $content[0]['id']);
    }

    /**
     * @return void
     */
    public function testGetAllByAdmin()
    {
        $user = $this->createUser();
        $adminUser = $this->createAdminUser();
        $project1 = $this->createProject($user);
        $project2 = $this->createProject($adminUser);

        $response = $this->apiCallAuthorizated($adminUser, 'GET', '/projects');
        $this->assertEquals(200, $response->getStatusCode());
        $content = json_decode($response->getContent(), 1);
        $this->assertCount(2, $content);
        $this->assertEquals($project1->id, $content[0]['id']);
        $this->assertEquals($project2->id, $content[1]['id']);
    }

    /**
     * Data provider for validation errors.
     *
     * @return array
     */
    public function validationErrorsDataProvider(): array
    {
        return [
            [
                'data' => [
                    'name' => 'Project name',
                    'description' => 'Description',
                ],
            ],
            [
                'data' => [
                    'description' => 'Description',
                    'categories' => [1, 2, 3],
                ],
            ],
            [
                'data' => [
                    'name' => 'Project name',
                    'description' => 'Description',
                    'categories' => 'string instead of array',
                ],
            ],
        ];
    }


}
