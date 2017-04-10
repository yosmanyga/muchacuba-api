Feature: Process lyrics request

  Scenario: Process an spanish lyrics request that find lyrics
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
        "events": []
      }
      """

  Scenario: Process an english lyrics request that find lyrics
    Given I process this lyrics request:
      """
      {
        "sender": "foo@bar.com",
        "receptor": "lyrics@muchacuba.com",
        "subject": "Evanescence My Immortal"
      }
      """
    Then I should get this result:
      """
      {
        "responses": [
          {
            "from": "Letras Muchacuba <letras@muchacuba.com>",
            "to": "foo@bar.com",
            "subject": "Re: Evanescence My Immortal",
            "body": "@string@.contains('Suppressed by all my childish fears')",
            "attachments": []
          }
        ],
        "events": []
      }
      """
