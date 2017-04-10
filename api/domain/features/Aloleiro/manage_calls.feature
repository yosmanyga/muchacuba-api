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
        "uniqueness": "1",
        "from": "+123",
        "to": "+1011"
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
        "uniqueness": "1",
        "from": "+123",
        "to": "+1011"
      }
    ]
    """

  Scenario: Process ICE event
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
      "cli": "+123",
      "to": {
        "endpoint": "+1213"
      }
    }
    """
    Then I should get the response:
    """
    {
      "name" : "ConnectPSTN",
      "number" : "+1213",
      "maxDuration" : 3600,
      "cli" : "+123"
    }
    """

  Scenario: Process ICE event and hangup because there is no prepared call
    When I process the event:
    """
    {
      "event": "ice",
      "cli": "+123",
      "to": {
        "endpoint": "+1213"
      }
    }
    """
    Then I should get the response:
    """
    {
      "name" : "Hangup"
    }
    """