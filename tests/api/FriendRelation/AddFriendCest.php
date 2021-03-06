<?php
namespace FriendRelation;
use \ApiTester;

class AddFriendCest
{
    protected $endpoint = 'http://localhost/api/v1/friend_connect';
    private $email;
    private $email2;
    private $not_registered_email;
    private $not_registered_email2;

    public function _before(ApiTester $I)
    {
        $this->email = 'andy@example.com';
        $this->email2 = 'john@example.com';
        $this->not_registered_email = 'test@email.com';
        $this->not_registered_email2 = 'test2@email.com';
        $this->haveUser($I, ['email' => $this->email]);
        $this->haveUser($I, ['email' => $this->email2]);
    }

    public function addFriend(ApiTester $I)
    {
        $I->wantToTest('add friend');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST($this->endpoint,
            [
                'friends' => [$this->email, $this->email2]
            ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    private function haveUser(ApiTester $I, $data = [])
    {
        $data = array_merge([
            'name' => 'Rizky',
            'password' => 'test',
            'friend_count' => 0,
        ], $data);
        return $I->haveRecord('users', $data);
    }


    /*
     * Request Validation
     */
    public function tryToTestErrorEmptyData(ApiTester $I)
    {
        $I->wantToTest('add friend');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST($this->endpoint,
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
        $I->sendPOST($this->endpoint,
            [
                'friends' => [$this->email, $this->email]
            ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function tryToTestErrorDataNotFoundFirst(ApiTester $I)
    {
        $I->wantToTest('add friend');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST($this->endpoint,
            [
                'friends' => [$this->not_registered_email, $this->email]
            ]);
        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();
    }

    public function tryToTestErrorDataNotFoundSecond(ApiTester $I)
    {
        $I->wantToTest('add friend');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST($this->endpoint,
            [
                'friends' => [$this->email, $this->not_registered_email]
            ]);
        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();
    }

    public function tryToTestErrorDataNotFoundBoth(ApiTester $I)
    {
        $I->wantToTest('add friend');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST($this->endpoint,
            [
                'friends' => [$this->not_registered_email, $this->not_registered_email2]
            ]);
        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();
    }
}
