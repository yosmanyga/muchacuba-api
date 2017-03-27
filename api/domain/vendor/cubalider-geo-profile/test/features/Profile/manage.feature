Feature: Manage profile

  Scenario: Create a profile
    Given I create a profile with the following data:
    """
    {
      "uniqueness": "1",
      "lat": "38.898648",
      "lng": "77.037692"
    }
    """
    When I pick a profile with the following data:
    """
    {
      "uniqueness": "1"
    }
    """
    Then I should get the following data:
    """
    {
      "uniqueness": "1",
      "lat": "38.898648",
      "lng": "77.037692"
    }
    """

  Scenario: Create an existent profile
    Given I create a profile with the following data:
    """
    {
      "uniqueness": "1",
      "lat": "38.898648",
      "lng": "77.037692"
    }
    """
    When I create a profile with the following data:
    """
    {
      "uniqueness": "1",
      "lat": "39.898648",
      "lng": "78.037692"
    }
    """
    Then I should get an existent profile exception

  Scenario: Delete a profile
    Given I create a profile with the following data:
    """
    {
      "uniqueness": "1",
      "lat": "38.898648",
      "lng": "77.037692"
    }
    """
    And I create a profile with the following data:
    """
    {
      "uniqueness": "2",
      "lat": "39.898648",
      "lng": "78.037692"
    }
    """
    When I delete a profile with the following data:
    """
    {
      "uniqueness": "1"
    }
    """
    And I collect the profiles
    Then I should get the following data:
    """
    [
      {
        "uniqueness": "2",
        "lat": "39.898648",
        "lng": "78.037692"
      }
    ]
    """

  Scenario: Delete a nonexistent profile
    When I delete a profile with the following data:
    """
    {
      "uniqueness": "1"
    }
    """
    Then I should get a nonexistent profile exception

  Scenario: Pick a profile by uniqueness
    Given I create a profile with the following data:
    """
    {
      "uniqueness": "1",
      "lat": "38.898648",
      "lng": "77.037692"
    }
    """
    When I pick a profile with the following data:
    """
    {
      "uniqueness": "1"
    }
    """
    Then I should get the following data:
    """
    {
      "uniqueness": "1",
      "lat": "38.898648",
      "lng": "77.037692"
    }
    """

  Scenario: Pick a nonexistent profile
    When I pick a profile with the following data:
    """
    {
      "uniqueness": "1"
    }
    """
    Then I should get a nonexistent profile exception

  Scenario: Collect profile
    Given I create a profile with the following data:
    """
    {
      "uniqueness": "1",
      "lat": "38.898648",
      "lng": "77.037692"
    }
    """
    And I create a profile with the following data:
    """
    {
      "uniqueness": "2",
      "lat": "39.898648",
      "lng": "78.037692"
    }
    """
    When I collect the profiles
    Then I should get the following data:
    """
    [
      {
        "uniqueness": "1",
        "lat": "38.898648",
        "lng": "77.037692"
      },
      {
        "uniqueness": "2",
        "lat": "39.898648",
        "lng": "78.037692"
      }
    ]
    """


