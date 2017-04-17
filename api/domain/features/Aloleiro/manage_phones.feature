Feature: Manage phones

  Scenario: Add a phone
    Given there is the business "b1":
    """
    {
      "balance": "0.0",
      "profitPercent": "15",
      "currencyExchange": "4412"
    }
    """
    And there is the profile:
    """
    {
      "uniqueness": "u1",
      "business": "b1"
    }
    """
    When I add the phone:
    """
    {
      "uniqueness": "u1",
      "number": "+123",
      "name": "Phone 1"
    }
    """
    And I collect the phones using profile "u1"
    Then I should get the phones:
    """
    [
      {
        "number": "+123",
        "business": "b1",
        "name": "Phone 1"
      }
    ]
    """

  Scenario: Try to add a phone that already exist in profile
    Given there is the business "b1":
    """
    {
      "balance": "0.0",
      "profitPercent": "15",
      "currencyExchange": "4412"
    }
    """
    And there is the profile:
    """
    {
      "uniqueness": "u1",
      "business": "b1"
    }
    """
    When I add the phone:
    """
    {
      "uniqueness": "u1",
      "number": "+123",
      "name": "Phone 1"
    }
    """
    When I add the phone:
    """
    {
      "uniqueness": "u1",
      "number": "+123",
      "name": "Phone 2"
    }
    """
    Then I should get an existent phone exception

  Scenario: Try to add a phone that already exist in another profile
    Given there is the business "b1":
    """
    {
      "balance": "0.0",
      "profitPercent": "15",
      "currencyExchange": "4412"
    }
    """
    Given there is the business "b2":
    """
    {
      "balance": "0.0",
      "profitPercent": "15",
      "currencyExchange": "4412"
    }
    """
    And there is the profile:
    """
    {
      "uniqueness": "u1",
      "business": "b1"
    }
    """
    And there is the profile:
    """
    {
      "uniqueness": "u2",
      "business": "b2"
    }
    """
    And I add the phone:
    """
    {
      "uniqueness": "u1",
      "number": "+123",
      "name": "Phone 1"
    }
    """
    When I add the phone:
    """
    {
      "uniqueness": "u2",
      "number": "+123",
      "name": "Phone 1"
    }
    """
    Then I should get an existent phone exception

  Scenario: Update a phone
    Given there is the business "b1":
    """
    {
      "balance": "0.0",
      "profitPercent": "15",
      "currencyExchange": "4412"
    }
    """
    And there is the profile:
    """
    {
      "uniqueness": "u1",
      "business": "b1"
    }
    """
    When I add the phone:
    """
    {
      "uniqueness": "u1",
      "number": "+123",
      "name": "Phone 1"
    }
    """
    When I update the phone:
    """
    {
      "uniqueness": "u1",
      "number": "+123",
      "name": "Phone 2"
    }
    """
    And I collect the phones using profile "u1"
    Then I should get the phones:
    """
    [
      {
        "number": "+123",
        "business": "b1",
        "name": "Phone 2"
      }
    ]
    """

  Scenario: Try to update a nonexistent phone
    Given there is the business "b1":
    """
    {
      "balance": "0.0",
      "profitPercent": "15",
      "currencyExchange": "4412"
    }
    """
    And there is the profile:
    """
    {
      "uniqueness": "u1",
      "business": "b1"
    }
    """
    When I update the phone:
    """
    {
      "uniqueness": "u1",
      "number": "+456",
      "name": "Phone 2"
    }
    """
    Then I should get a nonexistent phone exception

  Scenario: Try to update a phone from another profile
    Given there is the business "b1":
    """
    {
      "balance": "0.0",
      "profitPercent": "15",
      "currencyExchange": "4412"
    }
    """
    Given there is the business "b2":
    """
    {
      "balance": "0.0",
      "profitPercent": "15",
      "currencyExchange": "4412"
    }
    """
    And there is the profile:
    """
    {
      "uniqueness": "u1",
      "business": "b1"
    }
    """
    And there is the profile:
    """
    {
      "uniqueness": "u2",
      "business": "b2"
    }
    """
    When I add the phone:
    """
    {
      "uniqueness": "u1",
      "number": "+123",
      "name": "Phone 1"
    }
    """
    When I update the phone:
    """
    {
      "uniqueness": "u2",
      "number": "+123",
      "name": "Phone 2"
    }
    """
    Then I should get a nonexistent phone exception

  Scenario: Remove a phone
    Given there is the business "b1":
    """
    {
      "balance": "0.0",
      "profitPercent": "15",
      "currencyExchange": "4412"
    }
    """
    And there is the profile:
    """
    {
      "uniqueness": "u1",
      "business": "b1"
    }
    """
    And I add the phone:
    """
    {
      "uniqueness": "u1",
      "number": "+123",
      "name": "Phone 1"
    }
    """
    And I add the phone:
    """
    {
      "uniqueness": "u1",
      "number": "+456",
      "name": "Phone 2"
    }
    """
    When I remove the phone:
    """
    {
      "uniqueness": "u1",
      "number": "+123"
    }
    """
    And I collect the phones using profile "u1"
    Then I should get the phones:
    """
    [
      {
        "number": "+456",
        "business": "b1",
        "name": "Phone 2"
      }
    ]
    """

  Scenario: Try to remove a nonexistent phone
    Given there is the business "b1":
    """
    {
      "balance": "0.0",
      "profitPercent": "15",
      "currencyExchange": "4412"
    }
    """
    And there is the profile:
    """
    {
      "uniqueness": "u1",
      "business": "b1"
    }
    """
    When I remove the phone:
    """
    {
      "uniqueness": "u1",
      "number": "+456"
    }
    """
    Then I should get a nonexistent phone exception

  Scenario: Try to remove a phone from another profile
    Given there is the business "b1":
    """
    {
      "balance": "0.0",
      "profitPercent": "15",
      "currencyExchange": "4412"
    }
    """
    Given there is the business "b2":
    """
    {
      "balance": "0.0",
      "profitPercent": "15",
      "currencyExchange": "4412"
    }
    """
    And there is the profile:
    """
    {
      "uniqueness": "u1",
      "business": "b1"
    }
    """
    And there is the profile:
    """
    {
      "uniqueness": "u2",
      "business": "b2"
    }
    """
    When I add the phone:
    """
    {
      "uniqueness": "u1",
      "number": "+123",
      "name": "Phone 1"
    }
    """
    When I remove the phone:
    """
    {
      "uniqueness": "u2",
      "number": "+123"
    }
    """
    Then I should get a nonexistent phone exception

  Scenario: Collect phones
    Given there is the business "b1":
    """
    {
      "balance": "0.0",
      "profitPercent": "15",
      "currencyExchange": "4412"
    }
    """
    Given there is the business "b2":
    """
    {
      "balance": "0.0",
      "profitPercent": "15",
      "currencyExchange": "4412"
    }
    """
    And there is the profile:
    """
    {
      "uniqueness": "u1",
      "business": "b1"
    }
    """
    And there is the profile:
    """
    {
      "uniqueness": "u2",
      "business": "b2"
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
    And there are the phones on business that the profile "u2" belongs to:
    """
    [
      {
        "number": "+456",
        "name": "Phone 1"
      }
    ]
    """
    And I collect the phones using profile "u1"
    Then I should get the phones:
    """
    [
      {
        "number": "+123",
        "business": "b1",
        "name": "Phone 1"
      }
    ]
    """