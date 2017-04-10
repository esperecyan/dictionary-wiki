<?php

namespace Tests\Unit\Helpers;

use Tests\TestCase;
use App\{User, ExternalAccount, Dictionary, File, Revision, Tag};
use Carbon\Carbon;

class HelpersTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();
    }
    
    /**
     * @covers ::show_time
     *
     * @param  string  $time
     * @param  string  $html
     * @return void
     *
     * @dataProvider timeProvider
     */
    public function testShowTime(string $time, string $html): void
    {
        $this->assertEqualHTMLStringWithoutWhiteSpaces($html, show_time(new Carbon($time)));
    }
    
    public function timeProvider(): array
    {
        return [
            [
                '2017-01-01T12:00:00+09:00',
                '<time datetime="2017-01-01T12:00:00+09:00" title="2017-01-01 12:00:00 (+09:00)">2017-01-01</time>',
            ],
        ];
    }
    
    /**
     * @covers ::show_user
     *
     * @param  int  $id
     * @param  string  $name
     * @param  bool  $nameProviderAvailable
     * @param  string|null  $avatar
     * @param  bool  $avatarProviderAvailable
     * @param  string  $html
     *
     * @dataProvider userProvider
     */
    public function testShowUser(
        int $id,
        string $name,
        bool $nameProviderAvailable,
        ?string $avatar,
        bool $avatarProviderAvailable,
        string $html
    ): void {
        $user = factory(User::class)->make(['id' => $id]);
        $user->setRelation('externalAccounts', factory(ExternalAccount::class, 2)->make());
        
        $user->externalAccounts[0]->name = $name;
        $user->externalAccounts[0]->available = $nameProviderAvailable;
        $user->name_provider_id = $user->externalAccounts[0]->id = 1;
        
        $user->externalAccounts[1]->avatar = $avatar;
        $user->externalAccounts[1]->available = $avatarProviderAvailable;
        $user->avatar_provider_id = $user->externalAccounts[1]->id = 2;
        
        $this->assertEqualHTMLStringWithoutWhiteSpaces(
            str_replace('{{ $appURL }}', url(''), $html),
            show_user($user)
        );
    }
    
    public function userProvider(): array
    {
        return [
            [
                1,
                '100の人',
                true,
                null,
                false,
                '<a href="{{ $appURL }}/users/1" class="user">
                    <img src="{{ $appURL }}/img/no-avatar.png" alt="">
                    <bdi>100の人</bdi>
                </a>',
            ],
            [
                1,
                '100の人',
                false,
                null,
                false,
                '<a href="{{ $appURL }}/users/1" class="user">
                    <img src="{{ $appURL }}/img/no-avatar.png" alt="">
                    <bdi>100の人</bdi>
                </a>',
            ],
            [
                2,
                'エスパー・イーシア',
                true,
                'https://resource.test/avatar.png',
                true,
                '<a href="{{ $appURL }}/users/2" class="user">
                    <img src="https://resource.test/avatar.png" alt="">
                    <bdi>エスパー・イーシア</bdi>
                </a>',
            ],
            [
                2,
                'エスパー・イーシア',
                true,
                'https://resource.test/avatar.png',
                false,
                '<a href="{{ $appURL }}/users/2" class="user">
                    <img src="{{ $appURL }}/img/no-avatar.png" alt="">
                    <bdi>エスパー・イーシア</bdi>
                </a>',
            ],
        ];
    }
    
    /**
     * @covers ::parameter_name
     *
     * @param  \Illuminate\Database\Eloquent\Model|string  $model
     * @param  string  $parameterName
     * @param  string  $resouceName
     * @return void
     *
     * @dataProvider modelProvider
     */
    public function testParameterName($model, string $parameterName, string $resouceName): void
    {
        $this->assertSame($parameterName, parameter_name($model));
    }
    
    /**
     * @covers ::resource_name
     *
     * @param  \Illuminate\Database\Eloquent\Model|string  $model
     * @param  string  $parameterName
     * @param  string  $resouceName
     * @return void
     *
     * @dataProvider modelProvider
     */
    public function testResourceName($model, string $parameterName, string $resouceName): void
    {
        $this->assertSame($resouceName, resource_name($model));
    }
    
    public function modelProvider(): array
    {
        return [
            [User::class           , 'user'            , 'users'            ],
            [ExternalAccount::class, 'external-account', 'external-accounts'],
            [Dictionary::class     , 'dictionary'      , 'dictionaries'     ],
            [new File()            , 'file'            , 'files'            ],
            [new Revision()        , 'revision'        , 'revisions'        ],
            [new Tag()             , 'tag'             , 'tags'             ],
        ];
    }
}
