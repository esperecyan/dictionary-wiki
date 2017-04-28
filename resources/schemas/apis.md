# esperecyan/dictionary-wiki API
辞書まとめwikiにおける辞書を取り扱うAPIです。

* [辞書](#辞書)
  * [GET dictionaries](#get-dictionaries)
  * [GET users/:user/dictionaries](#get-usersuserdictionaries)
  * [GET dictionaries/:dictionary?scope=header](#get-dictionariesdictionary)
  * [GET dictionaries/:dictionary](#get-dictionariesdictionary-1)

## 辞書


### Properties

### GET dictionaries


* type
  * 「json」のみ指定可能。
  * Example: `"json"`
  * Type: string
* scope
  * 「without-warnings」を指定することで、ピクトセンス、およびInteligenceωのしりとり辞書に変換可能なお題のみ含む辞書に結果を限定できます。
  * Example: `"without-warnings"`
  * Type: string
* sort
  * 辞書名は「title」、語数は「words」、更新日時は「updated_at」。省略された場合、更新が新しい順に並びます。
  * Example: `"updated_at"`
  * Type: string
* order
  * 「asc」で昇順、「desc」で降順に並べ替えます。
  * Example: `"desc"`
* page
  * ページ番号は1から始まります。
  * Example: `1`
  * Type: integer
* search
  * 辞書名、タグ、概要、および各お題の正しい表記の答え (textフィールド値) から検索します。
  * Example: `"ゲーム"`
  * Type: string

```
GET dictionaries?order=desc&page=1&scope=without-warnings&search=%E3%82%B2%E3%83%BC%E3%83%A0&sort=updated_at&type=json HTTP/1.1
Host: api.example.com
```

```
HTTP/1.1 200 OK
Content-Type: application/json

{
  "total": 3,
  "per_page": 50,
  "current_page": 1,
  "last_page": 1,
  "next_page_url": null,
  "prev_page_url": null,
  "from": 1,
  "to": 3,
  "data": [
    {
      "id": 1,
      "category": "generic",
      "tags": [
        "アニメ"
      ],
      "locale": "ja",
      "title": "普通名詞・初級",
      "words": 100,
      "summary": null,
      "regard": null,
      "updated_at": "2017-01-01T00:00:00Z"
    }
  ]
}
```

### GET users/:user/dictionaries


* type
  * 「json」のみ指定可能。
  * Example: `"json"`
  * Type: string
* scope
  * 「without-warnings」を指定することで、ピクトセンス、およびInteligenceωのしりとり辞書に変換可能なお題のみ含む辞書に結果を限定できます。
  * Example: `"without-warnings"`
  * Type: string
* sort
  * 辞書名は「title」、語数は「words」、更新日時は「updated_at」。省略された場合、更新が新しい順に並びます。
  * Example: `"updated_at"`
  * Type: string
* order
  * 「asc」で昇順、「desc」で降順に並べ替えます。
  * Example: `"desc"`
* page
  * ページ番号は1から始まります。
  * Example: `1`
  * Type: integer
* search
  * 辞書名、タグ、概要、および各お題の正しい表記の答え (textフィールド値) から検索します。
  * Example: `"ゲーム"`
  * Type: string

```
GET users/{user}/dictionaries?order=desc&page=1&scope=without-warnings&search=%E3%82%B2%E3%83%BC%E3%83%A0&sort=updated_at&type=json HTTP/1.1
Host: api.example.com
```

```
HTTP/1.1 200 OK
Content-Type: application/json

{
  "total": 3,
  "per_page": 50,
  "current_page": 1,
  "last_page": 1,
  "next_page_url": null,
  "prev_page_url": null,
  "from": 1,
  "to": 3,
  "data": [
    {
      "id": 1,
      "category": "generic",
      "tags": [
        "アニメ"
      ],
      "locale": "ja",
      "title": "普通名詞・初級",
      "words": 100,
      "summary": null,
      "regard": null,
      "updated_at": "2017-01-01T00:00:00Z"
    }
  ]
}
```

### GET dictionaries/:dictionary


* type
  * 「type」に「json」を指定する場合、必ず「scope」で「header」を指定する必要があります。
  * Example: `"json"`
  * Type: string
* scope
  * 「type」に「json」を指定する場合、必ず「scope」で「header」を指定する必要があります。
  * Example: `"header"`
  * Type: string

```
GET dictionaries/{dictionary}?scope=header&type=json HTTP/1.1
Host: api.example.com
```

```
HTTP/1.1 200 OK
Content-Type: application/json

{
  "id": 1,
  "category": "generic",
  "tags": [
    "アニメ"
  ],
  "locale": "ja",
  "title": "普通名詞・初級",
  "words": 100,
  "summary": null,
  "regard": null,
  "updated_at": "2017-01-01T00:00:00Z"
}
```

### GET dictionaries/:dictionary
指定された形式に変換された辞書を返します。変換に失敗した場合、エラー内容を表すJSONを返します。

* type
  * 汎用辞書は「csv」、キャッチフィーリング・DrawingCatchは「cfq」、きゃっちまは「dat」、Inteligence ω のクイズ辞書は「quiz」、同じくしりとり辞書は「siri」、ピクトセンスは「pictsense」を指定します。
  * Example: `"csv"`
  * Type: string

```
GET dictionaries/{dictionary}?type=csv HTTP/1.1
Host: api.example.com
```

```
HTTP/1.1 200 OK
Content-Type: */*
```

