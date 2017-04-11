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
        "duration": null
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
        "duration": null
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
    Then I should get the response:
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
        "duration": null
      }
    ]
    """

  Scenario: Process incoming call event that is no prepared
    When I process the event:
    """
    {
      "event": "ice",
      "callid": "1",
      "cli": "+123",
      "to": {
        "endpoint": "+1213"
      }
    }
    """
    Then I should get the response:
    """
    {
      "action": {
        "name" : "Hangup"
      }
    }
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
    Then I should get the response:
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
        "duration": null
      }
    ]
    """

#  TODO
#  Scenario: Process disconnected call event (DICE)