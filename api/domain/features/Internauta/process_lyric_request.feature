Feature: Process lyrics request

  Scenario: Process an spanish lyrics request that find lyrics
    Given I process this spanish lyrics request:
      """
      {
        "sender": "foo@bar.com",
        "recipient": "letras@muchacuba.com",
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
        "events": []
      }
      """

  Scenario: Process an english lyrics request that find lyrics
    Given I process this english lyrics request:
      """
      {
        "sender": "foo@bar.com",
        "recipient": "lyrics@muchacuba.com",
        "subject": "Evanescence Frozen"
      }
      """
    Then I should get this result:
      """
      {
        "responses": [
          {
            "from": "Lyrics Muchacuba <lyrics@muchacuba.com>",
            "to": "foo@bar.com",
            "subject": "Re: Evanescence Frozen",
            "body": "@string@.contains('How can you see into my eyes like open doors?')",
            "attachments": []
          }
        ],
        "events": []
      }
      """
