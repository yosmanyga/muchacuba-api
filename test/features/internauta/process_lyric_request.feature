Feature: Process lyrics request

  Scenario: Process a lyrics request that find lyrics
    Given I process this lyrics request:
      """
      {
        "sender": "foo@bar.com",
        "receptor": "letras@muchacuba.com",
        "subject": "Shakira Tu"
      }
      """
    Then I should get this result:
      """
      {
        "responses": [
          {
            "from": "Letras Muchacuba <letras@muchacuba.com>",
            "to": "foo@bar.com",
            "subject": "Re: Shakira Tu",
            "body": "@string@.contains('Te regalo mi cintura')",
            "attachments": []
          }
        ],
        "events": [
          {
            "type": "Muchacuba\\Internauta\\Lyrics\\ProcessRequest.Found",
            "payload": {
              "link": "@string@"
            },
            "date": "@integer@"
          }
        ]
      }
      """

