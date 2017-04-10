Feature: Process image request

  Scenario: Process an image request
    Given I process this image request:
      """
      {
        "sender": "foo@bar.com",
        "receptor": "imagenes@muchacuba.com",
        "subject": "windows 10"
      }
      """
    Then I should get this result:
      """
      {
        "responses": [
          {
            "from": "Imágenes Muchacuba <imagenes@muchacuba.com>",
            "to": "foo@bar.com",
            "subject": "Re: windows 10 [1 de 3]",
            "body": "En los adjuntos está la imagen encontrada.",
            "attachments": ["@string@"]
          },
          {
            "from": "Imágenes Muchacuba <imagenes@muchacuba.com>",
            "to": "foo@bar.com",
            "subject": "Re: windows 10 [2 de 3]",
            "body": "En los adjuntos está la imagen encontrada.",
            "attachments": ["@string@"]
          },
          {
            "from": "Imágenes Muchacuba <imagenes@muchacuba.com>",
            "to": "foo@bar.com",
            "subject": "Re: windows 10 [3 de 3]",
            "body": "En los adjuntos está la imagen encontrada.",
            "attachments": ["@string@"]
          }
        ],
        "events": []
      }
      """

  Scenario: Process an image request with custom amount
    Given I process this image request:
      """
      {
        "sender": "foo@bar.com",
        "receptor": "imagenes@muchacuba.com",
        "subject": "windows 10 [1]"
      }
      """
    Then I should get this result:
      """
      {
        "responses": [
          {
            "from": "Imágenes Muchacuba <imagenes@muchacuba.com>",
            "to": "foo@bar.com",
            "subject": "Re: windows 10 [1 de 1]",
            "body": "En los adjuntos está la imagen encontrada.",
            "attachments": ["@string@"]
          }
        ],
        "events": []
      }
      """