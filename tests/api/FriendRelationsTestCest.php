<?php


class FriendRelationsTestCest
{

    public function tryToTest(ApiTester $I)
    {
        $I->wantToTest('add friend');

        $I->haveHttpHeader('Content-Type', 'application/json');

        $I->sendPOST('http://localhost/api/v1/friend_connect',
            [
                'friends' => ['andy@example.com', 'john@example.com']
            ]);

        $I->seeResponseCodeIs(200);

        $I->seeResponseIsJson();
    }

    public function tryToTestErrorEmptyData(ApiTester $I)
    {
        $I->wantToTest('add friend');

        $I->haveHttpHeader('Content-Type', 'application/json');

        $I->sendPOST('http://localhost/api/v1/friend_connect',
            [
                'friends' => []
            ]);

        $I->seeResponseCodeIs(400);

        $I->seeResponseIsJson();
    }

    public function tryToTestErrorSameData(ApiTester $I)
    {
        $I->wantToTest('add friend');

        $I->haveHttpHeader('Content-Type', 'application/json');

        $I->sendPOST('http://localhost/api/v1/friend_connect',
            [
                'friends' => ['andy@example.com', 'andy@example.com']
            ]);

        $I->seeResponseCodeIs(400);

        $I->seeResponseIsJson();
    }

    public function tryToTestErrorDataNotFoundFirst(ApiTester $I)
    {
        $I->wantToTest('add friend');

        $I->haveHttpHeader('Content-Type', 'application/json');

        $I->sendPOST('http://localhost/api/v1/friend_connect',
            [
                'friends' => ['test@example.com', 'andy@example.com']
            ]);

        $I->seeResponseCodeIs(404);

        $I->seeResponseIsJson();
    }

    public function tryToTestErrorDataNotFoundSecond(ApiTester $I)
    {
        $I->wantToTest('add friend');

        $I->haveHttpHeader('Content-Type', 'application/json');

        $I->sendPOST('http://localhost/api/v1/friend_connect',
            [
                'friends' => ['andy@example.com', 'test@example.com']
            ]);

        $I->seeResponseCodeIs(404);

        $I->seeResponseIsJson();
    }

    public function tryToTestErrorDataNotFoundBoth(ApiTester $I)
    {
        $I->wantToTest('add friend');

        $I->haveHttpHeader('Content-Type', 'application/json');

        $I->sendPOST('http://localhost/api/v1/friend_connect',
            [
                'friends' => ['test1@example.com', 'test2@example.com']
            ]);

        $I->seeResponseCodeIs(404);

        $I->seeResponseIsJson();
    }
}
