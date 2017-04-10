<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\{User, ExternalAccount};

class UserTest extends TestCase
{
    /**
     * @param  string  $url
     * @param  bool  $result
     * @return void
     *
     * @dataProvider urlProvider
     */
    public function testIsAbsoluteURLWithHTTPScheme(string $url, bool $result): void
    {
        $this->assertSame($result, User::isAbsoluteURLWithHTTPScheme($url));
    }
    
    public function urlProvider(): array
    {
        return [
            ['/path/name'                             , false],
            ['//example.test/'                        , false],
            ['https://example.test/'                  , true ],
            ['http://example.test/'                   , true ],
            ['ftp://example.test/'                    , false],
            ['https://example.test/path/name#fragment', true ],
        ];
    }
    
    /**
     * @return void
     */
    public function testGetNameAttribute(): void
    {
        $user = factory(User::class)->make(['id' => 1]);
        $user->setRelation('externalAccounts', factory(ExternalAccount::class, 3)->make());
        $user->name_provider_id = $user->externalAccounts[0]->id = 1;
        $this->assertSame($user->externalAccounts[0]->name, $user->name);
    }
    
    /**
     * @return void
     */
    public function testGetEmailAttribute(): void
    {
        $user = factory(User::class)->make(['id' => 1]);
        $user->setRelation('externalAccounts', factory(ExternalAccount::class, 3)->make(['id' => PHP_INT_MAX]));
        $this->assertSame('', $user->email);
        
        $user->externalAccounts[0]->email = 'name@mail.test';
        $user->email_provider_id = $user->externalAccounts[0]->id = 1;
        $this->assertSame('name@mail.test', $user->email);
    }
    
    /**
     * @return void
     */
    public function testGetAvatarAttribute(): void
    {
        $user = factory(User::class)->make(['id' => 1]);
        $user->setRelation('externalAccounts', factory(ExternalAccount::class, 3)->make(['id' => PHP_INT_MAX]));
        $this->assertSame('', $user->avatar);
        
        $user->externalAccounts[0]->avatar = 'https://service.test/avatar.png';
        $user->externalAccounts[0]->available = true;
        $user->avatar_provider_id = $user->externalAccounts[0]->id = 1;
        $this->assertSame('https://service.test/avatar.png', $user->avatar);
        
        $user->externalAccounts[0]->available = false;
        $this->assertSame('', $user->avatar);
    }
    
    /**
     * @return void
     */
    public function testGetLinksAttribute(): void
    {
        $user = factory(User::class)->make(['id' => 1]);
        $factoryBuilder = factory(ExternalAccount::class);
        $user->setRelation('externalAccounts', collect([
            $factoryBuilder->make(
                ['id' => 1, 'provider' => 'a', 'link' => 'https://a.test/user', 'public' => true , 'available' => true]
            ),
            $factoryBuilder->make(
                ['id' => 2, 'provider' => 'b', 'link' => 'https://b.test/user', 'public' => true , 'available' => true]
            ),
            $factoryBuilder->make(
                ['id' => 3, 'provider' => 'c', 'link' => 'https://c.test/user', 'public' => false, 'available' => true]
            ),
        ]));
        
        $this->assertEquals(['a' => 'https://a.test/user', 'b' => 'https://b.test/user'], $user->links->toArray());
    }
}
