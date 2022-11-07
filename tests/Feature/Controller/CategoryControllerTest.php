<?php

namespace Tests\Feature\Controller;

class CategoryControllerTest extends AbstractApiTest
{

    /**
     * Test category creation.
     *
     * @return void
     */
    public function testPost()
    {
        $user = $this->createAdminUser();

        $response = $this->apiCallAuthorizated($user, 'POST', '/category', $this->defaultCategoryData);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertDatabaseHas('categories', $this->defaultCategoryData);
    }

    /**
     * Test validation errors of request.
     *
     * @param array $data
     * @return void
     */
    public function testPostError400()
    {
        $user = $this->createAdminUser();
        $response = $this->apiCallAuthorizated($user, 'POST', '/category', [
            'description' => 'Test Category 1 Description',
        ]);
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**
     * Test error code.
     *
     * @return void
     */
    public function testPostError403()
    {
        $user = $this->createUser();
        $response = $this->apiCallAuthorizated($user, 'POST', '/category');
        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * Test category editing.
     *
     * @return void
     */
    public function testPut()
    {
        $user = $this->createAdminUser();
        $category = $this->createCategory();

        $response = $this->apiCallAuthorizated($user, 'PUT', '/category/' . $category->id, [
            'name' => 'Test Category 1 Edited',
            'description' => 'Test Category 1 Description Edited',
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertDatabaseHas('categories', [
            'name' => 'Test Category 1 Edited',
            'description' => 'Test Category 1 Description Edited',
        ]);
    }

    /**
     * Test validation errors of request.
     *
     * @param array $data
     * @return void
     */
    public function testPutError400()
    {
        $user = $this->createAdminUser();
        $category = $this->createCategory();
        $response = $this->apiCallAuthorizated($user, 'PUT', '/category/' . $category->id, [
            'description' => 'Test Category 1 Description',
        ]);
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**
     * Test that simple users cannot use request.
     *
     * @return void
     */
    public function testPutError403()
    {
        $user = $this->createUser();
        $category = $this->createCategory();
        $response = $this->apiCallAuthorizated($user, 'PUT', '/category/' . $category->id, $this->defaultCategoryData);
        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * Test error code.
     *
     * @return void
     */
    public function testPutError404()
    {
        $user = $this->createUser();
        $response = $this->apiCallAuthorizated($user, 'PUT', '/category/123', $this->defaultCategoryData);
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * Test category delete.
     *
     * @return void
     */
    public function testDelete()
    {
        $user = $this->createAdminUser();
        $category = $this->createCategory();

        $response = $this->apiCallAuthorizated($user, 'DELETE', '/category/' . $category->id);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertDatabaseMissing('categories', $this->defaultCategoryData);
    }

    /**
     * Test that simple users cannot use request.
     *
     * @return void
     */
    public function testDeleteError403()
    {
        $user = $this->createUser();
        $category = $this->createCategory();
        $response = $this->apiCallAuthorizated($user, 'DELETE', '/category/' . $category->id);
        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * Test error code.
     *
     * @return void
     */
    public function testDeleteError404()
    {
        $user = $this->createUser();
        $response = $this->apiCallAuthorizated($user, 'DELETE', '/category/123', $this->defaultCategoryData);
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * Test category get.
     *
     * @return void
     */
    public function testGet()
    {
        $user = $this->createUser();
        $category = $this->createCategory();

        $response = $this->apiCallAuthorizated($user, 'GET', '/category/' . $category->id);

        $this->assertEquals(200, $response->getStatusCode());
        $content = json_decode($response->getContent(), 1);
        $this->assertEquals($this->defaultCategoryData['name'], $content['name']);
        $this->assertEquals($this->defaultCategoryData['description'], $content['description']);
    }

    /**
     * Test error code.
     *
     * @return void
     */
    public function testGetError404()
    {
        $user = $this->createUser();

        $response = $this->apiCallAuthorizated($user, 'GET', '/category/123', $this->defaultCategoryData);
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * Test category get.
     *
     * @return void
     */
    public function testGetAll()
    {
        $user = $this->createUser();

        // Create two categories.
        $this->createCategory();
        $this->createCategory();

        $response = $this->apiCallAuthorizated($user, 'GET', '/categories');

        $this->assertEquals(200, $response->getStatusCode());
        $content = json_decode($response->getContent(), 1);
        $this->assertEquals(2, count($content));
    }

}
