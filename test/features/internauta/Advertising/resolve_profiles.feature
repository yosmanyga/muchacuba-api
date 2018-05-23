Feature: Resolve profiles

#  Scenario: Users receive advertisement just once
#    Given there are these users:
#    """
#    [
#      {
#        "id": "1",
#        "email": "username1@server.com",
#        "mobile": "123"
#      },
#      {
#        "id": "2",
#        "email": "username2@server.com",
#        "mobile": "456"
#      },
#      {
#        "id": "3",
#        "email": "username3@server.com",
#        "mobile": "789"
#      },
#      {
#        "id": "4",
#        "email": "username4@server.com",
#        "mobile": "101112"
#      }
#    ]
#    """
#    And there are these emails in advertising:
#    """
#    [
#      {
#        "id": "1",
#        "subject": "subject 1",
#        "body": "body 1"
#      },
#      {
#        "id": "2",
#        "subject": "subject 2",
#        "body": "body 2"
#      }
#    ]
#    """
#    And there are these profiles in advertising:
#    """
#    [
#      {
#        "user": "1",
#        "email": "username1@server.com",
#        "advertisements": []
#      },
#      {
#        "user": "2",
#        "email": "username2@server.com",
#        "advertisements": [
#          {
#            "email": "1"
#          }
#        ]
#      },
#      {
#        "user": "3",
#        "email": "username3@server.com",
#        "advertisements": []
#      },
#      {
#        "user": "4",
#        "email": "username4@server.com",
#        "advertisements": []
#      }
#    ]
#    """
#    When I resolve profiles in advertising:
#    """
#    {
#      "email": "1",
#      "amount": 2
#    }
#    """
#    Then I should get these profiles:
#    """
#    [
#      {
#        "user": "1",
#        "email": "username1@server.com",
#        "advertisements": []
#      },
#      {
#        "user": "3",
#        "email": "username3@server.com",
#        "advertisements": []
#      }
#    ]
#    """