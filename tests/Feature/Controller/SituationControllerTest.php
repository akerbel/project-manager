<?php

namespace Tests\Feature\Controller;

use App\Models\Situation;

class SituationControllerTest extends AbstractApiTest
{

    /**
     * @return void
     */
    public function testPost()
    {
        // User creates situation in his project.
        $user = $this->createUser();
        $project = $this->createProject($user);
        $situationData = $this->defaultSituationData;
        $situationData['project_id'] = $project->id;

        $response = $this->apiCallAuthorizated($user, 'POST', '/situation', $situationData);
        $this->assertEquals(200, $response->getStatusCode());
        $content = json_decode($response->getContent(), 1);
        $this->assertArrayHasKey('id', $content);
        $situationData['id'] = $content['id'];
        $this->assertDatabaseHas('situations', $situationData);

        // Admin creates situation in user's project.
        $adminUser = $this->createAdminUser();
        $situationData = $this->defaultSituationData;
        $situationData['project_id'] = $project->id;

        $response = $this->apiCallAuthorizated($adminUser, 'POST', '/situation', $situationData);
        $this->assertEquals(200, $response->getStatusCode());
        $content = json_decode($response->getContent(), 1);
        $this->assertArrayHasKey('id', $content);
        $situationData['id'] = $content['id'];
        $this->assertDatabaseHas('situations', $situationData);
    }

    /**
     * @param array $data
     * @return void
     *
     * @dataProvider validationErrorDataProvider
     */
    public function testPostError400(array $data)
    {
        $user = $this->createUser();
        $project = $this->createProject($user);
        $situationData = $data;
        if (isset($situationData['project_id']) && is_int($situationData['project_id'])) {
            $situationData['project_id'] = $project->id;
        }

        $response = $this->apiCallAuthorizated($user, 'POST', '/situation', $situationData);
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testPostError403()
    {
        $user = $this->createUser();
        $adminUser = $this->createAdminUser();
        $project = $this->createProject($adminUser);
        $situationData = $this->defaultSituationData;
        $situationData['project_id'] = $project->id;

        $response = $this->apiCallAuthorizated($user, 'POST', '/situation', $situationData);
        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testPatch()
    {
        // User edits situation in his project.
        $user = $this->createUser();
        $project = $this->createProject($user);
        $situation = $this->createSituation($project);
        $newSituationName = 'New situation name';
        $newSituationDescription = 'New situation description';
        $newSituationStatus = Situation::STATUS_ONGOING;

        $response = $this->apiCallAuthorizated($user, 'PATCH', '/situation/' . $situation->id, [
            'name' => $newSituationName,
            'description' => $newSituationDescription,
            'status' => $newSituationStatus,
        ]);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertDatabaseHas('situations', [
            'id' => $situation->id,
            'name' => $newSituationName,
            'description' => $newSituationDescription,
            'status' => $newSituationStatus,
        ]);

        // Admin edits situation in user's project.
        $adminUser = $this->createAdminUser();
        $newSituationName = 'New situation name by admin';
        $newSituationDescription = 'New situation description by admin';
        $newSituationStatus = Situation::STATUS_COMPLETED;

        $response = $this->apiCallAuthorizated($adminUser, 'PATCH', '/situation/' . $situation->id, [
            'name' => $newSituationName,
            'description' => $newSituationDescription,
            'status' => $newSituationStatus,
        ]);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertDatabaseHas('situations', [
            'id' => $situation->id,
            'name' => $newSituationName,
            'description' => $newSituationDescription,
            'status' => $newSituationStatus,
        ]);
    }

    /**
     * @return void
     */
    public function testPatchError400()
    {
        $user = $this->createUser();
        $project = $this->createProject($user);
        $situation = $this->createSituation($project);

        $response = $this->apiCallAuthorizated($user, 'PATCH', '/situation/' . $situation->id, [
            'status' => 123,
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
        $situation = $this->createSituation($project);

        $response = $this->apiCallAuthorizated($user, 'PATCH', '/situation/' . $situation->id, [
            'name' => 'New name',
        ]);
        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testPatchError404()
    {
        $user = $this->createUser();

        $response = $this->apiCallAuthorizated($user, 'PATCH', '/situation/123', [
            'name' => 'New name',
        ]);
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testDelete()
    {
        // User deletes situation in his project.
        $user = $this->createUser();
        $project = $this->createProject($user);
        $situation = $this->createSituation($project);

        $response = $this->apiCallAuthorizated($user, 'DELETE', '/situation/' . $situation->id);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertDatabaseMissing('situations', [
            'id' => $situation->id,
        ]);

        // Admin deletes situation in user's project.
        $adminUser = $this->createAdminUser();
        $situation = $this->createSituation($project);

        $response = $this->apiCallAuthorizated($adminUser, 'DELETE', '/situation/' . $situation->id);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertDatabaseMissing('situations', [
            'id' => $situation->id,
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
        $situation = $this->createSituation($project);

        $response = $this->apiCallAuthorizated($user, 'DELETE', '/situation/' . $situation->id);
        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testDeleteError404()
    {
        $user = $this->createUser();

        $response = $this->apiCallAuthorizated($user, 'DELETE', '/situation/123');
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testGet()
    {
        // User gets situation in his project.
        $user = $this->createUser();
        $project = $this->createProject($user);
        $situation = $this->createSituation($project);

        $response = $this->apiCallAuthorizated($user, 'GET', '/situation/' . $situation->id);
        $this->assertEquals(200, $response->getStatusCode());
        $content = json_decode($response->getContent(), 1);
        $this->assertEquals($situation->id, $content['id']);
        $this->assertEquals($situation->name, $content['name']);
        $this->assertEquals($situation->description, $content['description']);
        $this->assertEquals($situation->project_id, $content['project']['id']);
        $this->assertEquals($situation->status, $content['status']);

        // Admin gets situation in user's project.
        $adminUser = $this->createAdminUser();

        $response = $this->apiCallAuthorizated($adminUser, 'GET', '/situation/' . $situation->id);
        $this->assertEquals(200, $response->getStatusCode());
        $content = json_decode($response->getContent(), 1);
        $this->assertEquals($situation->id, $content['id']);
        $this->assertEquals($situation->name, $content['name']);
        $this->assertEquals($situation->description, $content['description']);
        $this->assertEquals($situation->project_id, $content['project']['id']);
        $this->assertEquals($situation->status, $content['status']);
    }

    /**
     * @return void
     */
    public function testGetError403()
    {
        $user = $this->createUser();
        $adminUser = $this->createAdminUser();
        $project = $this->createProject($adminUser);
        $situation = $this->createSituation($project);

        $response = $this->apiCallAuthorizated($user, 'GET', '/situation/' . $situation->id);
        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testGetError404()
    {
        $user = $this->createUser();

        $response = $this->apiCallAuthorizated($user, 'GET', '/situation/123');
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testGetAll()
    {
        // User gets situations in his project.
        $user = $this->createUser();
        $project = $this->createProject($user);
        $situation1 = $this->createSituation($project);
        $situation2 = $this->createSituation($project);

        $response = $this->apiCallAuthorizated($user, 'GET', '/situations/' . $project->id);
        $this->assertEquals(200, $response->getStatusCode());
        $content = json_decode($response->getContent(), 1);
        $this->assertCount(2, $content);
        $this->assertEquals($situation1->id, $content[0]['id']);
        $this->assertEquals($situation2->id, $content[1]['id']);

        // Admin gets situations in user's project.
        $adminUser = $this->createAdminUser();
        $response = $this->apiCallAuthorizated($adminUser, 'GET', '/situations/' . $project->id);
        $this->assertEquals(200, $response->getStatusCode());
        $content = json_decode($response->getContent(), 1);
        $this->assertCount(2, $content);
        $this->assertEquals($situation1->id, $content[0]['id']);
        $this->assertEquals($situation2->id, $content[1]['id']);
    }

    /**
     * @return void
     */
    public function testGetAllError403()
    {
        $user = $this->createUser();
        $adminUser = $this->createAdminUser();
        $project = $this->createProject($adminUser);
        $situation = $this->createSituation($project);

        $response = $this->apiCallAuthorizated($user, 'GET', '/situations/' . $project->id);
        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testGetAllError404()
    {
        $user = $this->createUser();

        $response = $this->apiCallAuthorizated($user, 'GET', '/situations/123');
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * Data provider for situation validation errors.
     *
     * @return \array[][]
     */
    public function validationErrorDataProvider(): array
    {
        return [
            [
                'data' => [
                    'description' => 'Situation description',
                    'status' => Situation::STATUS_ONGOING,
                    'project_id' => 123,
                ],
            ],
            [
                'data' => [
                    'name' => 'Situation name',
                    'description' => 'Situation description',
                    'project_id' => 123,
                ],
            ],
            [
                'data' => [
                    'name' => 'Situation name',
                    'description' => 'Situation description',
                    'status' => Situation::STATUS_ONGOING,
                ],
            ],
            [
                'data' => [
                    'name' => 'Situation name',
                    'description' => 'Situation description',
                    'status' => 123,
                    'project_id' => 123,
                ],
            ],
            [
                'data' => [
                    'name' => 'Situation name',
                    'description' => 'Situation description',
                    'status' => Situation::STATUS_ONGOING,
                    'project_id' => 'String instead of number',
                ],
            ],
        ];
    }

}
