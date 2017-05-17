Feature: Answering call

  Scenario: Answering a call
    Given there is a business
    And there is a phone in that business
    And there is a prepared call for that phone
    When I receive a nexmo answer call, having the specified phone in that prepared call
    Then I should get a response to nexmo, ordering to connect to the specified number in that prepared call

  @this
  Scenario: Answering a call from Venezuela
    Given there is a business
    And there is a phone from Venezuela in that business
    And there is a prepared call for that phone
    When I receive a nexmo answer call from Venezuela, having the specified phone without the country code in that prepared call
    Then I should get a response to nexmo, ordering to connect to the specified number in that prepared call

  Scenario: Answering a call from a not authorized phone
    Given there is a business
    And there is a phone in that business
    And there is a prepared call for that phone
    When I receive a nexmo answer call, having another phone than that prepared call
    Then I should get a response to nexmo, ordering to talk a no available message