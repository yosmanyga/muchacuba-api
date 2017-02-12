Feature: Search offers

  Scenario: Search offers around an address
    Given there are these offers around an address:
      """
      [
        {
          "amount": 2,
          "radius": "UP_TO_25_KM"
        },
        {
          "amount": 1,
          "radius": "MORE_THAN_25_KM"
        }
      ]
      """
    When I search offers 25 km around that address
    Then I should get 2 offers