<?php

$argv[1] = '{
    "update_id": 345083659,
    "message": {
        "message_id": 112250,
        "from": {
            "id": 243692601,
            "is_bot": false,
            "first_name": "Ammar",
            "last_name": "Faizi",
            "username": "ammarfaizi2",
            "language_code": "en-US"
        },
        "chat": {
            "id": -1001162202776,
            "title": "Koding Teh",
            "type": "supergroup"
        },
        "date": 1542296205,
        "reply_to_message": {
            "message_id": 112249,
            "from": {
                "id": 243692601,
                "is_bot": false,
                "first_name": "Ammar",
                "last_name": "Faizi",
                "username": "ammarfaizi2",
                "language_code": "en-US"
            },
            "chat": {
                "id": -1001162202776,
                "title": "Koding Teh",
                "type": "supergroup"
            },
            "date": 1542296200,
            "text": "/start",
            "entities": [
                {
                    "offset": 0,
                    "length": 6,
                    "type": "bot_command"
                }
            ]
        },
        "text": "/debug",
        "entities": [
            {
                "offset": 0,
                "length": 6,
                "type": "bot_command"
            }
        ]
    }
}';

require __DIR__."/../connectors/telegram/webhook_worker.php";
