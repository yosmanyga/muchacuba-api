Feature: Preparing call

  Scenario: Prepare a call
    Given there is a business
    And there is a phone in that business
    And there are some prepared calls
    Given there is another business
    And there is a phone in that business
    When I prepare a call on that business using that phone
    And I collect prepared calls on that business
    Then I should get a list with that call
