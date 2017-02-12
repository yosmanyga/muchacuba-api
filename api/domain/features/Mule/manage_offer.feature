Feature: Manage offer

  Scenario: Insert an offer
    Given I insert an offer:
      """
      {
        "name": "A name",
        "contact": "A contact.",
        "address": "An address",
        "coordinates": {"lat": 1, "lng": 1},
        "destinations": ["hab", "pri"],
        "description": "A description.",
        "trips": [1473093332, 1473179732]
      }
      """
    Then I should have 1 offer
