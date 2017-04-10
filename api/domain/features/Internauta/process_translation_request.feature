Feature: Process translation request

  Scenario: Process a translation request with english text
    Given I process this translation request:
      """
      {
        "sender": "foo@bar.com",
        "receptor": "traduccion@muchacuba.com",
        "subject": "school"
      }
      """
    Then I should get this result:
      """
      {
        "responses": [
          {
            "from": "Traducción Muchacuba <traduccion@muchacuba.com>",
            "to": "foo@bar.com",
            "subject": "Re: school",
            "body": "colegio",
            "attachments": []
          }
        ],
        "events": []
      }
      """

  Scenario: Process a translation request with spanish text
    Given I process this translation request:
      """
      {
        "sender": "foo@bar.com",
        "receptor": "traduccion@muchacuba.com",
        "subject": "escuela"
      }
      """
    Then I should get this result:
      """
      {
        "responses": [
          {
            "from": "Traducción Muchacuba <traduccion@muchacuba.com>",
            "to": "foo@bar.com",
            "subject": "Re: escuela",
            "body": "school",
            "attachments": []
          }
        ],
        "events": []
      }
      """

  Scenario: Process a translation request with text in another language
    Given I process this translation request:
      """
      {
        "sender": "foo@bar.com",
        "receptor": "traduccion@muchacuba.com",
        "subject": "escola"
      }
      """
    Then I should get this result:
      """
      {
        "responses": [
          {
            "from": "Traducción Muchacuba <traduccion@muchacuba.com>",
            "to": "foo@bar.com",
            "subject": "Re: escola",
            "body": "escuela",
            "attachments": []
          }
        ],
        "events": []
      }
      """