<?php

namespace CanalTP\TyrComponent\Tests;

use CanalTP\TyrComponent\VersionChecker;

class TyrServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \CanalTP\TyrComponent\AbstractTyrService
     */
    private $tyrService;

    /**
     * Constructor
     */
    public function __construct()
    {
        $tyrServiceClass = VersionChecker::getTyrServiceClassName();

        $this->tyrService = new $tyrServiceClass('http://tyr.dev.canaltp.fr/v0/', 2);
    }

    public function testCreateUserReturnsValidStatusCode()
    {
        $user = $this->createRandomUser();

        $this->tyrService->createUser($user->email, $user->login);
        $this->assertEquals(200, $this->tyrService->getLastResponse()->getStatusCode());

        $this->tyrService->deleteUser($user->email);
    }

    public function testCreateUserAddNewUser()
    {
        $user = $this->createRandomUser();

        $resultNull = $this->tyrService->getUserByEmail($user->email);
        $this->assertNull($resultNull);

        $this->tyrService->createUser($user->email, $user->login);

        $resultNotNull = $this->tyrService->getUserByEmail($user->email);
        $this->assertNotNull($resultNotNull);

        $this->tyrService->deleteUser($user->email);
    }

    public function testDeleteUserReturnsValidStatusCode()
    {
        $user = $this->createRandomUser();

        $this->tyrService->createUser($user->email, $user->login);
        $success = $this->tyrService->deleteUser($user->email);

        $this->assertTrue($success);
        $this->assertEquals(204, $this->tyrService->getLastResponse()->getStatusCode());
    }

    public function testDeleteUserDesintegrateTheGuy()
    {
        $user = $this->createRandomUser();

        $this->tyrService->createUser($user->email, $user->login);

        $this->assertNotNull($this->tyrService->getUserByEmail($user->email));

        $success = $this->tyrService->deleteUser($user->email);

        $this->assertTrue($success);
        $this->assertNull($this->tyrService->getUserByEmail($user->email));
    }

    public function testCreateUserKey()
    {
        $user = $this->createRandomUser();
        $createdUser = $this->tyrService->createUser($user->email, $user->login);

        $key = $this->tyrService->createUserKey($createdUser->id);

        $this->assertEquals(200, $this->tyrService->getLastResponse()->getStatusCode());
        $this->assertRegExp('/^[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}$/', $key);

        $this->tyrService->deleteUser($user->email);
    }

    public function testGetUserKeysReturnsEmptyArrayIfNotKeys()
    {
        $user = $this->createRandomUser();
        $createdUser = $this->tyrService->createUser($user->email, $user->login);

        $keys = $this->tyrService->getUserKeys($createdUser->id);

        $this->assertCount(0, $keys);
        $this->assertEquals(array(), $keys);

        $this->tyrService->deleteUser($user->email);
    }

    public function testGetUserKeys()
    {
        $user = $this->createRandomUser();
        $createdUser = $this->tyrService->createUser($user->email, $user->login);

        $this->tyrService->createUserKey($createdUser->id);

        $this->assertCount(1, $this->tyrService->getUserKeys($createdUser->id));

        $this->tyrService->createUserKey($createdUser->id);
        $this->tyrService->createUserKey($createdUser->id);

        $this->assertCount(3, $this->tyrService->getUserKeys($createdUser->id));

        $this->tyrService->deleteUser($user->email);
    }

    public function testDeleteUserKey()
    {
        $user = $this->createRandomUser();
        $createdUser = $this->tyrService->createUser($user->email, $user->login);

        $this->tyrService->createUserKey($createdUser->id);
        $this->tyrService->createUserKey($createdUser->id);
        $this->tyrService->createUserKey($createdUser->id);

        $keys = $this->tyrService->getUserKeys($createdUser->id);

        $this->assertCount(3, $keys);

        $this->tyrService->deleteUserKey($createdUser->id, $keys[0]->id);

        $this->assertCount(2, $this->tyrService->getUserKeys($createdUser->id));

        $this->tyrService->deleteUser($user->email);
    }

    public function testCreateBillingPlan()
    {
        $this->markTestIncomplete('billing plans not implemented yet.');

        $plan = $this->createRandomPlan();
        $createdPlan = $this->tyrService->createBillingPlan(
            $plan->name,
            $plan->max_request_count,
            $plan->max_object_count,
            $plan->default
        );

        $this->assertEquals(201, $this->tyrService->getLastResponse()->getStatusCode());

        $this->assertObjectHasAttribute('id', $createdPlan);

        $this->assertEquals($plan->name, $createdPlan->name);
        $this->assertEquals($plan->max_request_count, $createdPlan->max_request_count);
        $this->assertEquals($plan->max_object_count, $createdPlan->max_object_count);
        $this->assertEquals($plan->default, $createdPlan->default);

        $this->tyrService->deleteBillingPlan($createdPlan->id);
    }

    public function testGetBillingPlans()
    {
        $this->markTestIncomplete('billing plans not implemented yet.');

        $plans = $this->tyrService->getBillingPlans();

        $this->assertEquals(200, $this->tyrService->getLastResponse()->getStatusCode());

        $this->assertInternalType('array', $plans);
    }

    public function testGetBillingPlan()
    {
        $this->markTestIncomplete('billing plans not implemented yet.');

        $plan = $this->createRandomPlan();
        $createdPlan = $this->tyrService->createBillingPlan(
            $plan->name,
            $plan->max_request_count,
            $plan->max_object_count,
            $plan->default
        );

        $retrievedPlan = $this->tyrService->getBillingPlan($createdPlan->id);

        $this->assertEquals($plan->name, $retrievedPlan->name);
        $this->assertEquals($plan->max_request_count, $retrievedPlan->max_request_count);
        $this->assertEquals($plan->max_object_count, $retrievedPlan->max_object_count);
        $this->assertEquals($plan->default, $retrievedPlan->default);

        $this->tyrService->deleteBillingPlan($createdPlan->id);
    }

    public function testUpdateBillingPlan()
    {
        $this->markTestIncomplete('billing plans not implemented yet.');

        $plan = $this->createRandomPlan();
        $createdPlan = $this->tyrService->createBillingPlan(
            $plan->name,
            $plan->max_request_count,
            $plan->max_object_count,
            $plan->default
        );

        $updated = $this->tyrService->updateBillingPlan($createdPlan->id, 'updated', 20, 30, false);

        $retrievedPlan = $this->tyrService->getBillingPlan($createdPlan->id);

        $this->assertTrue($updated, 'Update billing plan returns true.');

        $this->assertEquals($retrievedPlan->name, 'updated');
        $this->assertEquals($retrievedPlan->max_request_count, 20);
        $this->assertEquals($retrievedPlan->max_object_count, 30);
        $this->assertEquals($retrievedPlan->default, false);

        $this->tyrService->deleteBillingPlan($createdPlan->id);
    }

    public function testDeleteBillingPlan()
    {
        $this->markTestIncomplete('billing plans not implemented yet.');

        $plan = $this->createRandomPlan();
        $createdPlan = $this->tyrService->createBillingPlan(
            $plan->name,
            $plan->max_request_count,
            $plan->max_object_count,
            $plan->default
        );

        $this->tyrService->getBillingPlan($createdPlan->id);

        $this->assertEquals(200, $this->tyrService->getLastResponse()->isSuccessful());

        $this->tyrService->deleteBillingPlan($createdPlan->id);

        $this->assertEquals(200, $this->tyrService->getLastResponse()->isSuccessful());

        $this->tyrService->deleteBillingPlan($createdPlan->id);

        $this->assertEquals(404, $this->tyrService->getLastResponse()->isSuccessful());
    }

    /**
     * @return \stdClass
     */
    private function createRandomUser()
    {
        $rand = rand(10000000, 99999999).'';

        return (object) array(
            'email' => $rand.'@free.fr',
            'login' => $rand,
            'password' => $rand,
        );
    }

    /**
     * @return \stdClass
     */
    private function createRandomPlan()
    {
        $rand = rand(10000000, 99999999).'';

        return (object) array(
            'name' => $rand,
            'max_request_count' => 3000,
            'max_object_count' => 6000,
            'default' => false,
        );
    }
}
