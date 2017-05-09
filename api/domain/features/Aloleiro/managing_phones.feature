Feature: Managing phones

  Scenario: Add a phone
    Given there is a business
    When I add a phone to that business
    And I collect phones on that business
    Then I should get a list with that phone

  Scenario: Try to add a phone with a number that already exist in business
    Given there is a business
    And there is a phone in that business
    When I try to add a phone using same number in that business
    Then I should get an existent phone exception

  Scenario: Try to add a phone with a number that already exist in another business
    Given there is a business
    And there is a phone in that business
    And there is another business
    When I try to add a phone using same number in that business
    Then I should get an existent phone exception

  Scenario: Try to add a phone with empty text as name in that business
    Given there is a business
    When I try to add a phone with empty text as name in that business
    Then I should get an invalid phone data exception related to the field name

  Scenario: Collect phones from a business
    Given there is a business
    And there is a phone in that business
    And there is another business
    When I add a phone to that business
    And I add another phone to that business
    And I collect phones on that business
    Then I should get a list with those phones

  Scenario: Update a phone
    Given there is a business
    And there is a phone in that business
    When I update that phone
    And I collect phones on that business
    Then I should get a list with that phone

  Scenario: Try to update a phone using a number that does not exist
    Given there is a business
    When I try to update a phone on that business
    Then I should get a nonexistent phone exception

  Scenario: Try to update a phone using another business
    Given there are some businesses
    And there is another business
    And there is a phone in that business
    When I try to update that phone using another business
    Then I should get a nonexistent phone exception

  Scenario: Try to update a phone using an empty text as name
    Given there is a business
    And there is a phone in that business
    When I try to update that phone using an empty text as name
    Then I should get an invalid phone data exception related to the field name
