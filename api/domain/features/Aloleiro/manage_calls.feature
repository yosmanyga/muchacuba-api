Feature: Manage calls

  Scenario: Collect calls
    Given there are the profiles with phones:
    """
    [
      {
        "uniqueness": "1",
        "phones": [
          {
            "number": "+123",
            "name": "Phone 1"
          },
          {
            "number": "+456",
            "name": "Phone 2"
          }
        ]
      },
      {
        "uniqueness": "2",
        "phones": [
          {
            "number": "+789",
            "name": "Phone 1"
          }
        ]
      }
    ]
    """
    And there are the calls:
    """
    [
      {
        "uniqueness": "1",
        "from": "+123",
        "to": "+1011"
      },
      {
        "uniqueness": "2",
        "from": "+789",
        "to": "+1314"
      }
    ]
    """
    When I collect the calls from profile "1"
    """
    {
      "uniqueness": "1"
    }
    """
    Then I should get the calls:
    """
    [
      {
        "id": "@string@",
        "from": "+123",
        "to": "+1011",
        "status": "p",
        "duration": null,
        "charge": null
      }
    ]
    """

  Scenario: Prepare call
    Given there are the profiles with phones:
    """
    [
      {
        "uniqueness": "1",
        "phones": [
          {
            "number": "+123",
            "name": "Phone 1"
          }
        ]
      }
    ]
    """
    When I prepare the call:
    """
    {
      "uniqueness": "1",
      "from": "+123",
      "to": "+1011"
    }
    """
    And I collect the calls from profile "1"
    Then I should get the calls:
    """
    [
      {
        "id": "@string@",
        "from": "+123",
        "to": "+1011",
        "status": "p",
        "duration": null,
        "charge": null
      }
    ]
    """

  Scenario: Process incoming call event (ICE)
    Given there are the profiles with phones:
    """
    [
      {
        "uniqueness": "1",
        "phones": [
          {
            "number": "+123",
            "name": "Phone 1"
          }
        ]
      }
    ]
    """
    And I prepare the call:
    """
    {
      "uniqueness": "1",
      "from": "+123",
      "to": "+1011"
    }
    """
    When I process the event:
    """
    {
      "event": "ice",
      "callid": "1",
      "cli": "+123"
    }
    """
    Then I should send the response:
    """
    {
      "action": {
        "name" : "ConnectPSTN",
        "number" : "+1011",
        "maxDuration" : 3600,
        "cli" : "+123"
      }
    }
    """
    When I collect the calls from profile "1"
    Then I should get the calls:
    """
    [
      {
        "id": "@string@",
        "from": "+123",
        "to": "+1011",
        "status": "f",
        "duration": null,
        "charge": null
      }
    ]
    """
    When I collect the events
    Then I should get the events:
    """
    [
      {
        "id": "@string@",
        "type": "sinch-event",
        "payload": {
          "event": "ice",
          "callid": "1",
          "cli": "+123"
        }
      },
      {
        "id": "@string@",
        "type": "response-to-sinch",
        "payload": {
          "action": {
            "name": "ConnectPSTN",
            "number": "+1011",
            "maxDuration": 3600,
            "cli": "+123"
          }
        }
      }
    ]
    """

  Scenario: Process incoming call event that is no prepared
    When I process the event:
    """
    {
      "event": "ice",
      "callid": "1",
      "cli": "+123"
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
    When I collect the events
    Then I should get the events:
    """
    [
      {
        "id": "@string@",
        "type": "sinch-event",
        "payload": {
          "event": "ice",
          "callid": "1",
          "cli": "+123"
        }
      },
      {
        "id": "@string@",
        "type": "response-to-sinch",
        "payload": {
          "action": {
            "name": "Hangup"
          }
        }
      }
    ]
    """

  Scenario: Process answered call event (ACE)
    Given there are the profiles with phones:
    """
    [
      {
        "uniqueness": "1",
        "phones": [
          {
            "number": "+123",
            "name": "Phone 1"
          }
        ]
      }
    ]
    """
    And I prepare the call:
    """
    {
      "uniqueness": "1",
      "from": "+123",
      "to": "+1011"
    }
    """
    And I process the event:
    """
    {
      "event": "ice",
      "callid": "1",
      "cli": "+123"
    }
    """
    When I process the event:
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
    When I collect the calls from profile "1"
    Then I should get the calls:
    """
    [
      {
        "id": "@string@",
        "from": "+123",
        "to": "+1011",
        "status": "a",
        "duration": null,
        "charge": null
      }
    ]
    """
    When I collect the events
    Then I should get the events:
    """
    [
      {
        "id": "@string@",
        "type": "sinch-event",
        "payload": {
          "event": "ice",
          "callid": "1",
          "cli": "+123"
        }
      },
      {
        "id": "@string@",
        "type": "response-to-sinch",
        "payload": {
          "action": {
            "name" : "ConnectPSTN",
            "number" : "+1011",
            "maxDuration" : 3600,
            "cli" : "+123"
          }
        }
      },
      {
        "id": "@string@",
        "type": "sinch-event",
        "payload": {
          "event": "ace",
          "callid": "1"
        }
      },
      {
        "id": "@string@",
        "type": "response-to-sinch",
        "payload": {
          "action": {
            "name": "Continue"
          }
        }
      }
    ]
    """

  @this
  Scenario: Process disconnected call event (DICE)
    Given there are the profiles with phones:
    """
    [
      {
        "uniqueness": "1",
        "phones": [
          {
            "number": "+123",
            "name": "Phone 1"
          }
        ]
      }
    ]
    """
    And I prepare the call:
    """
    {
      "uniqueness": "1",
      "from": "+123",
      "to": "+1011"
    }
    """
    And I process the event:
    """
    {
      "event": "ice",
      "callid": "1",
      "cli": "+123"
    }
    """
    And I process the event:
    """
    {
      "event": "ace",
      "callid": "1"
    }
    """
    When I process the event:
    """
    {
      "event": "dice",
      "callid": "1"
    }
    """
    Then I should send no response
    When I collect the calls from profile "1"
    Then I should get the calls:
    """
    [
      {
        "id": "@string@",
        "from": "+123",
        "to": "+1011",
        "status": "a",
        "duration": null,
        "charge": null
      }
    ]
    """
    When I collect the events
    Then I should get the events:
    """
    [
      {
        "id": "@string@",
        "type": "sinch-event",
        "payload": {
          "event": "ice",
          "callid": "1",
          "cli": "+123"
        }
      },
      {
        "id": "@string@",
        "type": "response-to-sinch",
        "payload": {
          "action": {
            "name" : "ConnectPSTN",
            "number" : "+1011",
            "maxDuration" : 3600,
            "cli" : "+123"
          }
        }
      },
      {
        "id": "@string@",
        "type": "sinch-event",
        "payload": {
          "event": "ace",
          "callid": "1"
        }
      },
      {
        "id": "@string@",
        "type": "response-to-sinch",
        "payload": {
          "action": {
            "name": "Continue"
          }
        }
      },
      {
        "id": "@string@",
        "type": "sinch-event",
        "payload": {
          "event": "dice",
          "callid": "1"
        }
      }
    ]
    """
    When I collect the requests
    Then I should get the requests:
    """
    [
      {
        "callId": "1"
      }
    ]
    """