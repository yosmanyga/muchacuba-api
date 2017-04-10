Feature: Manage phones

  Scenario: Collect phones
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
      }
    ]
    """
    When I collect the phones from profile "1"
    """
    {
      "uniqueness": "1"
    }
    """
    Then I should get the phones:
    """
    [
      {
        "number": "+123",
        "name": "Phone 1"
      },
      {
        "number": "+456",
        "name": "Phone 2"
      }
    ]
    """

  Scenario: Add a phone
    Given there are the profiles with phones:
    """
    [
      {
        "uniqueness": "1",
        "phones": []
      }
    ]
    """
    When I add the phone:
    """
    {
      "uniqueness": "1",
      "number": "+123",
      "name": "Phone 1"
    }
    """
    And I collect the phones from profile "1"
    Then I should get the phones:
    """
    [
      {
        "number": "+123",
        "name": "Phone 1"
      }
    ]
    """

  Scenario: Try to add a phone that already exist in profile
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
    When I add the phone:
    """
    {
      "uniqueness": "1",
      "number": "+123",
      "name": "Phone 2"
    }
    """
    Then I should get an existent phone exception

  Scenario: Try to add a phone that already exist in another profile
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
      },
      {
        "uniqueness": "2",
        "phones": [
          {
            "number": "+456",
            "name": "Phone 1"
          }
        ]
      }
    ]
    """
    When I add the phone:
    """
    {
      "uniqueness": "1",
      "number": "+456",
      "name": "Phone 2"
    }
    """
    Then I should get an existent phone exception

  Scenario: Update a phone
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
    When I update the phone:
    """
    {
      "uniqueness": "1",
      "number": "+123",
      "name": "Phone 2"
    }
    """
    And I collect the phones from profile "1"
    Then I should get the phones:
    """
    [
      {
        "number": "+123",
        "name": "Phone 2"
      }
    ]
    """

  Scenario: Try to update a nonexistent phone
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
    When I update the phone:
    """
    {
      "uniqueness": "1",
      "number": "+456",
      "name": "Phone 2"
    }
    """
    Then I should get a nonexistent phone exception

  Scenario: Try to update a phone from another profile
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
      },
      {
        "uniqueness": "2",
        "phones": [
          {
            "number": "+456",
            "name": "Phone 1"
          }
        ]
      }
    ]
    """
    When I update the phone:
    """
    {
      "uniqueness": "1",
      "number": "+456",
      "name": "Phone 2"
    }
    """
    Then I should get a nonexistent phone exception

  Scenario: Remove a phone
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
      }
    ]
    """
    When I remove the phone:
    """
    {
      "uniqueness": "1",
      "number": "+123"
    }
    """
    And I collect the phones from profile "1"
    Then I should get the phones:
    """
    [
      {
        "number": "+456",
        "name": "Phone 2"
      }
    ]
    """

  Scenario: Try to remove a nonexistent phone
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
    When I remove the phone:
    """
    {
      "uniqueness": "1",
      "number": "+456"
    }
    """
    Then I should get a nonexistent phone exception

  Scenario: Try to remove a phone from another profile
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
      },
      {
        "uniqueness": "2",
        "phones": [
          {
            "number": "+456",
            "name": "Phone 1"
          }
        ]
      }
    ]
    """
    When I remove the phone:
    """
    {
      "uniqueness": "1",
      "number": "+456"
    }
    """
    Then I should get a nonexistent phone exception
