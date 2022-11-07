<?php

namespace Tests\Feature\Command;

use Tests\Feature\Controller\AbstractApiTest;

class PromoteToAdminTest extends AbstractApiTest
{

    /**
     * @return void
     */
    public function testPromoteToAdmin()
    {
        $user = $this->createUser();
        $this->artisan('app:make-admin ' . $user->email)->assertSuccessful();
        $user->refresh();
        $this->assertTrue($user->isAdmin());
    }

}
