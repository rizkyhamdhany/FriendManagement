<?php
namespace FriendRelation;
use \ApiTester;

class FriendListCest
{
    protected $endpoint = 'http://localhost/api/v1/friend_list';
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
        $this->haveUser($I, ['email' => $this->email3]);
        $this->haveUser($I, ['email' => $this->email4]);
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

    public function tryToTest(ApiTester $I)
    {
        $I->wantToTest('friend list');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST($this->endpoint,
            [
                'email' => $this->email
            ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->expect('added friend in friendlist');
        $I->seeResponseContainsJson(['friends' => [$this->email2]]);
    }

    public function tryToTestHaveNoFriend(ApiTester $I)
    {
        $I->wantToTest('friend list');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST($this->endpoint,
            [
                'email' => $this->email3
            ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->expect('added friend in friendlist');
        $I->seeResponseContainsJson(['friends' => []]);
    }

    public function tryToTestEmptyPostData(ApiTester $I)
    {
        $I->wantToTest('friend list empty');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST($this->endpoint,
            [
            ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function tryToTestWrongPostData(ApiTester $I)
    {
        $I->wantToTest('friend list wrong post data');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST($this->endpoint,
            [
                'wrong_data' => 'andy@example.com'
            ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }
}
