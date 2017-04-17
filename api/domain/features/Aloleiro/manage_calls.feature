Feature: Manage calls

  Scenario: Prepare call
    Given there is the business "b1"
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
    And I collect the system calls from profile "u1"
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
    Given there is the business "b1"
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
      "cli": "+123",
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
    And I collect the system calls from profile "u1"
    Then I should get the system calls:
    """
    [
      {
        "from": "+123",
        "to": "+1011",
        "instances": [
          {
            "duration": null,
            "purchase": null,
            "sale": null
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
          "cli": "+123",
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
      "cli": "+123",
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
          "cli": "+123",
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
    Given there is the business "b1"
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
      "cli": "+123",
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
    When I collect the system calls from profile "u1"
    Then I should get the system calls:
    """
    [
      {
        "from": "+123",
        "to": "+1011",
        "instances": [
          {
            "duration": null,
            "purchase": null,
            "sale": null
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
          "cli": "+123",
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
    Given there is the business "b1"
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
      "cli": "+123",
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
      "callid": "1"
    }
    """
    Then I should send no response
    When I collect the system calls from profile "u1"
    Then I should get the system calls:
    """
    [
      {
        "from": "+123",
        "to": "+1011",
        "instances": [
          {
            "duration": null,
            "purchase": null,
            "sale": null
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
          "cli": "+123",
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
          "callid": "1"
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
    When I collect the sinch requests
    Then I should get the sinch requests:
    """
    [
      {
        "callId": "1"
      }
    ]
    """