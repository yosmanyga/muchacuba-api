Feature: Manage calls

  Scenario: Prepare call
    Given there is the rate:
    """
    {
      "countryName": "Venezuela",
      "countryTranslation": "Venezuela",
      "countryCurrencyExchange": 4412
    }
    """
    And there is the business "b1":
    """
    {
      "balance": "0.0",
      "profitPercent": "15"
    }
    """
    And there is the profile:
    """
    {
      "uniqueness": "u1",
      "business": "b1"
    }
    """
    And there are the phones on business that the profile "u1" belongs to:
    """
    [
      {
        "number": "+123",
        "name": "Phone 1"
      }
    ]
    """
    When I prepare the call from the profile "u1":
    """
    {
      "from": "+123",
      "to": "+1011"
    }
    """
    And I collect the system calls
    Then I should get the system calls:
    """
    [
      {
        "from": "+123",
        "to": "+1011",
        "instances": []
      }
    ]
    """

  Scenario: Process incoming call event (ICE)
    Given there is the business "b1":
    """
    {
      "balance": "0.0",
      "profitPercent": "15"
    }
    """
    And there is the profile:
    """
    {
      "uniqueness": "u1",
      "business": "b1"
    }
    """
    And there are the phones on business that the profile "u1" belongs to:
    """
    [
      {
        "number": "+123",
        "name": "Phone 1"
      }
    ]
    """
    When I prepare the call from the profile "u1":
    """
    {
      "from": "+123",
      "to": "+1011"
    }
    """
    When I process the sinch event:
    """
    {
      "event": "ice",
      "callid": "1",
      "cli": "123",
      "to": {
        "endpoint": "+789"
      }
    }
    """
    Then I should send the response:
    """
    {
      "action": {
        "name" : "ConnectPSTN",
        "number" : "+1011",
        "maxDuration" : 3600,
        "cli" : "+789"
      }
    }
    """
    And I collect the system calls
    Then I should get the system calls:
    """
    [
      {
        "from": "+123",
        "to": "+1011",
        "instances": [
          {
            "timestamp": null,
            "duration": null,
            "purchase": null,
            "sale": null,
            "profit": null
          }
        ]
      }
    ]
    """
    When I collect the logs
    Then I should get the logs:
    """
    [
      {
        "type": "event-from-sinch",
        "payload": {
          "event": "ice",
          "callid": "1",
          "cli": "123",
          "to": {
            "endpoint": "+789"
          }
        },
        "date": "@integer@"
      },
      {
        "type": "response-to-sinch",
        "payload": {
          "action": {
            "name": "ConnectPSTN",
            "number": "+1011",
            "maxDuration": 3600,
            "cli": "+789"
          }
        },
        "date": "@integer@"
      }
    ]
    """

  Scenario: Process incoming call event that is no prepared
    When I process the sinch event:
    """
    {
      "event": "ice",
      "callid": "1",
      "cli": "123",
      "to": {
        "endpoint": "+789"
      }
    }
    """
    Then I should send the response:
    """
    {
      "action": {
        "name" : "Hangup"
      }
    }
    """
    When I collect the logs
    Then I should get the logs:
    """
    [
      {
        "type": "event-from-sinch",
        "payload": {
          "event": "ice",
          "callid": "1",
          "cli": "123",
          "to": {
            "endpoint": "+789"
          }
        },
        "date": "@integer@"
      },
      {
        "type": "response-to-sinch",
        "payload": {
          "action": {
            "name": "Hangup"
          }
        },
        "date": "@integer@"
      }
    ]
    """

  Scenario: Process answered call event (ACE)
    Given there is the business "b1":
    """
    {
      "balance": "0.0",
      "profitPercent": "15"
    }
    """
    And there is the profile:
    """
    {
      "uniqueness": "u1",
      "business": "b1"
    }
    """
    And there are the phones on business that the profile "u1" belongs to:
    """
    [
      {
        "number": "+123",
        "name": "Phone 1"
      }
    ]
    """
    When I prepare the call from the profile "u1":
    """
    {
      "from": "+123",
      "to": "+1011"
    }
    """
    When I process the sinch event:
    """
    {
      "event": "ice",
      "callid": "1",
      "cli": "123",
      "to": {
        "endpoint": "+789"
      }
    }
    """
    And I process the sinch event:
    """
    {
      "event": "ace",
      "callid": "1"
    }
    """
    Then I should send the response:
    """
    {
      "action": {
        "name" : "Continue"
      }
    }
    """
    When I collect the system calls
    Then I should get the system calls:
    """
    [
      {
        "from": "+123",
        "to": "+1011",
        "instances": [
          {
            "timestamp": null,
            "duration": null,
            "purchase": null,
            "sale": null,
            "profit": null
          }
        ]
      }
    ]
    """
    When I collect the logs
    Then I should get the logs:
    """
    [
      {
        "type": "event-from-sinch",
        "payload": {
          "event": "ice",
          "callid": "1",
          "cli": "123",
          "to": {
            "endpoint": "+789"
          }
        },
        "date": "@integer@"
      },
      {
        "type": "response-to-sinch",
        "payload": {
          "action": {
            "name" : "ConnectPSTN",
            "number" : "+1011",
            "maxDuration" : 3600,
            "cli" : "+789"
          }
        },
        "date": "@integer@"
      },
      {
        "type": "event-from-sinch",
        "payload": {
          "event": "ace",
          "callid": "1"
        },
        "date": "@integer@"
      },
      {
        "type": "response-to-sinch",
        "payload": {
          "action": {
            "name": "Continue"
          }
        },
        "date": "@integer@"
      }
    ]
    """

  Scenario: Process disconnected call event (DICE)
    Given there is the rate:
    """
    {
      "countryName": "Venezuela",
      "countryTranslation": "Venezuela",
      "countryCurrencyExchange": 4412
    }
    """
    And there is the business "b1":
    """
    {
      "balance": "0.0",
      "profitPercent": "15"
    }
    """
    And there is the profile:
    """
    {
      "uniqueness": "u1",
      "business": "b1"
    }
    """
    And there are the phones on business that the profile "u1" belongs to:
    """
    [
      {
        "number": "+123",
        "name": "Phone 1"
      }
    ]
    """
    When I prepare the call from the profile "u1":
    """
    {
      "from": "+123",
      "to": "+1011"
    }
    """
    And I process the sinch event:
    """
    {
      "event": "ice",
      "callid": "1",
      "cli": "123",
      "to": {
        "endpoint": "+789"
      }
    }
    """
    And I process the sinch event:
    """
    {
      "event": "ace",
      "callid": "1"
    }
    """
    And I process the sinch event:
    """
    {
      "event": "dice",
      "callid": "1",
      "timestamp": "2017-04-07T03:48:25Z",
      "duration": 30,
      "debit": {
        "amount": 0.1
      }
    }
    """
    Then I should send no response
    When I collect the system calls
    Then I should get the system calls:
    """
    [
      {
        "from": "+123",
        "to": "+1011",
        "instances": [
          {
            "timestamp": 1491536905,
            "duration": 30,
            "purchase": 0.1,
            "sale": 0.13,
            "profit": 0.03
          }
        ]
      }
    ]
    """
    When I collect the business calls from profile "u1"
    Then I should get the business calls:
    """
    [
      {
        "from": "+123",
        "to": "+1011",
        "instances": [
          {
            "timestamp": 1491536905,
            "duration": 30,
            "purchase": 574,
            "sale": 660,
            "profit": 86
          }
        ]
      }
    ]
    """
    When I collect the client calls from profile "u1"
    Then I should get the client calls:
    """
    [
      {
        "from": "+123",
        "to": "+1011",
        "instances": [
          {
            "timestamp": 1491536905,
            "duration": 30,
            "charge": 660
          }
        ]
      }
    ]
    """
    When I collect the logs
    Then I should get the logs:
    """
    [
      {
        "type": "event-from-sinch",
        "payload": {
          "event": "ice",
          "callid": "1",
          "cli": "123",
          "to": {
            "endpoint": "+789"
          }
        },
        "date": "@integer@"
      },
      {
        "type": "response-to-sinch",
        "payload": {
          "action": {
            "name" : "ConnectPSTN",
            "number" : "+1011",
            "maxDuration" : 3600,
            "cli" : "+789"
          }
        },
        "date": "@integer@"
      },
      {
        "type": "event-from-sinch",
        "payload": {
          "event": "ace",
          "callid": "1"
        },
        "date": "@integer@"
      },
      {
        "type": "response-to-sinch",
        "payload": {
          "action": {
            "name": "Continue"
          }
        },
        "date": "@integer@"
      },
      {
        "type": "event-from-sinch",
        "payload": {
          "event": "dice",
          "callid": "1",
          "timestamp": "2017-04-07T03:48:25Z",
          "duration": 30,
          "debit": {
            "amount": 0.1
          }
        },
        "date": "@integer@"
      },
      {
        "type": "response-to-sinch",
        "payload": null,
        "date": "@integer@"
      }
    ]
    """

  Scenario: Process two calls in same prepared call
    Given there is the rate:
    """
    {
      "countryName": "Venezuela",
      "countryTranslation": "Venezuela",
      "countryCurrencyExchange": 4412
    }
    """
    And there is the business "b1":
    """
    {
      "balance": "0.0",
      "profitPercent": "15"
    }
    """
    And there is the profile:
    """
    {
      "uniqueness": "u1",
      "business": "b1"
    }
    """
    And there are the phones on business that the profile "u1" belongs to:
    """
    [
      {
        "number": "+123",
        "name": "Phone 1"
      }
    ]
    """
    When I prepare the call from the profile "u1":
    """
    {
      "from": "+123",
      "to": "+1011"
    }
    """
    And I process the sinch event:
    """
    {
      "event": "ice",
      "callid": "1",
      "cli": "123",
      "to": {
        "endpoint": "+789"
      }
    }
    """
    And I process the sinch event:
    """
    {
      "event": "ace",
      "callid": "1"
    }
    """
    And I process the sinch event:
    """
    {
      "event": "dice",
      "callid": "1",
      "timestamp": "2017-04-07T03:48:25Z",
      "duration": 30,
      "debit": {
        "amount": 0.1
      }
    }
    """
    And I process the sinch event:
    """
    {
      "event": "ice",
      "callid": "2",
      "cli": "123",
      "to": {
        "endpoint": "+789"
      }
    }
    """
    And I process the sinch event:
    """
    {
      "event": "ace",
      "callid": "2"
    }
    """
    And I process the sinch event:
    """
    {
      "event": "dice",
      "callid": "2",
      "timestamp": "2017-04-07T03:56:25Z",
      "duration": 60,
      "debit": {
        "amount": 0.2
      }
    }
    """
    When I collect the system calls
    Then I should get the system calls:
    """
    [
      {
        "from": "+123",
        "to": "+1011",
        "instances": [
          {
            "timestamp": 1491537385,
            "duration": 60,
            "purchase": 0.2,
            "sale": 0.26,
            "profit": 0.06
          },
          {
            "timestamp": 1491536905,
            "duration": 30,
            "purchase": 0.1,
            "sale": 0.13,
            "profit": 0.03
          }
        ]
      }
    ]
    """

  Scenario: Prepare and process two consecutive calls
    Given there is the rate:
    """
    {
      "countryName": "Venezuela",
      "countryTranslation": "Venezuela",
      "countryCurrencyExchange": 4412
    }
    """
    And there is the business "b1":
    """
    {
      "balance": "0.0",
      "profitPercent": "15"
    }
    """
    And there is the profile:
    """
    {
      "uniqueness": "u1",
      "business": "b1"
    }
    """
    And there are the phones on business that the profile "u1" belongs to:
    """
    [
      {
        "number": "+123",
        "name": "Phone 1"
      }
    ]
    """
    When I prepare the call from the profile "u1":
    """
    {
      "from": "+123",
      "to": "+1011"
    }
    """
    And I process the sinch event:
    """
    {
      "event": "ice",
      "callid": "1",
      "cli": "123",
      "to": {
        "endpoint": "+789"
      }
    }
    """
    And I process the sinch event:
    """
    {
      "event": "ace",
      "callid": "1"
    }
    """
    And I process the sinch event:
    """
    {
      "event": "dice",
      "callid": "1",
      "timestamp": "2017-04-07T03:48:25Z",
      "duration": 30,
      "debit": {
        "amount": 0.1
      }
    }
    """
    When I prepare the call from the profile "u1":
    """
    {
      "from": "+123",
      "to": "+2021"
    }
    """
    And I process the sinch event:
    """
    {
      "event": "ice",
      "callid": "2",
      "cli": "123",
      "to": {
        "endpoint": "+789"
      }
    }
    """
    And I process the sinch event:
    """
    {
      "event": "ace",
      "callid": "2"
    }
    """
    And I process the sinch event:
    """
    {
      "event": "dice",
      "callid": "2",
      "timestamp": "2017-04-07T03:56:25Z",
      "duration": 60,
      "debit": {
        "amount": 0.2
      }
    }
    """
    When I collect the system calls
    Then I should get the system calls:
    """
    [
      {
        "from": "+123",
        "to": "+2021",
        "instances": [
          {
            "timestamp": 1491537385,
            "duration": 60,
            "purchase": 0.2,
            "sale": 0.26,
            "profit": 0.06
          }
        ]
      },
      {
        "from": "+123",
        "to": "+1011",
        "instances": [
          {
            "timestamp": 1491536905,
            "duration": 30,
            "purchase": 0.1,
            "sale": 0.13,
            "profit": 0.03
          }
        ]
      }
    ]
    """