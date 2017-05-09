Feature: Managing businesses

  Scenario: Add a business
    When I add a business
    And I collect businesses
    Then I should get a list with that business

  Scenario: Try to add a business using a text as profit balance
    When I try to add a business using a text as profit balance
    Then I should get an invalid business data exception related to the field profit percent

  Scenario: Try to add a business using a negative number as profit balance
    When I try to add a business using a negative number as profit balance
    Then I should get an invalid business data exception related to the field profit percent

  Scenario: Collect businesses
    Given I add a business
    And I add another business
    When I collect businesses
    Then I should get a list with those businesses