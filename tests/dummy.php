<?php

$argv[1] = '{
    "update_id": 345133577,
    "message": {
        "message_id": 123090,
        "from": {
            "id": 243692601,
            "is_bot": false,
            "first_name": "Ammar",
            "last_name": "Faizi",
            "username": "ammarfaizi2",
            "language_code": "en"
        },
        "chat": {
            "id": -1001162202776,
            "title": "Koding Teh",
            "username": "KodingTeh",
            "type": "supergroup"
        },
        "date": 1545553737,
        "text": "/tr en id hello world",
        "entities": [
            {
                "offset": 0,
                "length": 8,
                "type": "bot_command"
            }
        ]
    }
}';

require __DIR__."/../connectors/telegram/webhook_worker.php";
// require __DIR__."/../connectors/telegram/logger.php";
