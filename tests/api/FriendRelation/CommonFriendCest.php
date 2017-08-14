<?php
namespace FriendRelation;
use \ApiTester;

class CommonFriendCest
{
    protected $endpoint = 'http://localhost/api/v1/common_friend';
    private $email;
    private $email2;
    private $email3;
    private $email4;

    public function _before(ApiTester $I)
    {
        $this->email = 'andy@example.com';
        $this->email2 = 'john@example.com';
        $this->email3 = 'test@email.com';
        $this->email4 = 'test2@email.com';
        $this->haveUser($I, ['email' => $this->email]);
        $this->haveUser($I, ['email' => $this->email2]);
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('http://localhost/api/v1/friend_connect',
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

    public function _after(ApiTester $I)
    {
    }

    // tests
    public function tryToTest(ApiTester $I)
    {
    }

    /*
     * Request Validation
     */
    public function tryToTestErrorEmptyData(ApiTester $I)
    {
        $I->wantToTest('common friend with empty data');
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
        $I->wantToTest('common friend with same data');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST($this->endpoint,
            [
                'friends' => [$this->email, $this->email]
            ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function tryToTestErrorDataNotFoundFirst(ApiTester $I)
    {
        $I->wantToTest('common friend with not registered user first');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST($this->endpoint,
            [
                'friends' => [$this->email3, $this->email]
            ]);
        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();
    }

    public function tryToTestErrorDataNotFoundSecond(ApiTester $I)
    {
        $I->wantToTest('common friend with not registered user 2nd');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST($this->endpoint,
            [
                'friends' => [$this->email, $this->email3]
            ]);
        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();
    }

    public function tryToTestErrorDataNotFoundBoth(ApiTester $I)
    {
        $I->wantToTest('common friend with not registered user both');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST($this->endpoint,
            [
                'friends' => [$this->email3, $this->email4]
            ]);
        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();
    }
}
