Feature: Process horoscope request

  Scenario: Process an horoscope request
    Given I process this horoscope request:
      """
      {
        "sender": "foo@bar.com",
        "recipient": "horoscopo@muchacuba.com",
        "subject": "sagitario"
      }
      """
    Then I should get this result:
      """
      {
        "responses": [
          {
            "from": "Horóscopo Muchacuba <horoscopo@muchacuba.com>",
            "to": "foo@bar.com",
            "subject": "Re: sagitario",
            "body": "@string@.contains('Amor').contains('Salud').contains('Trabajo')",
            "attachments": []
          }
        ],
        "events": []
      }
      """
